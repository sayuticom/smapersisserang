<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationAccount;
use App\Models\DonationTransfer;
use App\Services\DonationBalanceService;
use App\Services\DonationTransferApprovalService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DonationTransferController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date'],
            'filter_type' => ['nullable', 'in:date,month'],
            'status' => ['nullable', 'in:pending,approved,rejected'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->filled('filter_type') && ! $request->filled('date')) {
            throw ValidationException::withMessages([
                'date' => 'Tanggal harus dipilih terlebih dahulu.',
            ]);
        }

        $query = DonationTransfer::with(['fromAccount', 'toAccount', 'requester']);

        if ($request->filled('date')) {
            $date = Carbon::parse($request->date);

            if ($request->input('filter_type') === 'month') {
                $query->whereYear('transfer_date', $date->year)
                    ->whereMonth('transfer_date', $date->month);
            } else {
                $query->whereDate('transfer_date', $request->date);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transfer_number', 'like', "%{$search}%")
                    ->orWhere('note', 'like', "%{$search}%")
                    ->orWhereHas('fromAccount', fn ($sub) => $sub->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('toAccount', fn ($sub) => $sub->where('name', 'like', "%{$search}%"));
            });
        }

        $transfers = $query->latest('transfer_date')
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.donation-transfers.index', compact('transfers'));
    }

    public function create(): View
    {
        $donationAccounts = DonationAccount::donation()->active()->orderBy('name')->get();
        $financeAccounts = DonationAccount::finance()->active()->orderBy('name')->get();

        $availableBalances = $donationAccounts->mapWithKeys(
            fn (DonationAccount $account) => [
                $account->id => app(DonationBalanceService::class)->availableBalanceForAccount($account->id),
            ]
        )->all();

        return view('admin.donation-transfers.create', compact('donationAccounts', 'financeAccounts', 'availableBalances'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'transfer_date' => ['required', 'date'],
            'from_account_id' => ['required', 'integer', 'exists:donation_accounts,id'],
            'to_account_id' => ['required', 'integer', 'exists:donation_accounts,id', 'different:from_account_id'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'note' => ['nullable', 'string', 'max:2000'],
            'proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $transfer = DB::transaction(function () use ($request, $validated) {
            $from = DonationAccount::findOrFail($validated['from_account_id']);
            $to = DonationAccount::findOrFail($validated['to_account_id']);

            if (! $from->isDonation()) {
                throw ValidationException::withMessages([
                    'from_account_id' => 'Akun sumber mutasi harus berjenis Donasi.',
                ]);
            }

            if (! $to->isFinance()) {
                throw ValidationException::withMessages([
                    'to_account_id' => 'Akun tujuan mutasi harus berjenis Keuangan.',
                ]);
            }

            if (! $from->is_active) {
                throw ValidationException::withMessages([
                    'from_account_id' => 'Akun sumber mutasi sedang tidak aktif.',
                ]);
            }

            if (! $to->is_active) {
                throw ValidationException::withMessages([
                    'to_account_id' => 'Akun tujuan mutasi sedang tidak aktif.',
                ]);
            }

            $this->lockDonationBalanceRows();

            $available = app(DonationBalanceService::class)->availableBalanceForAccount((int) $from->id);
            if ((float) $validated['amount'] > (float) $available) {
                throw ValidationException::withMessages([
                    'amount' => $this->insufficientBalanceMessage($from, $available),
                ]);
            }

            if ($request->hasFile('proof_file')) {
                $validated['proof_file'] = $request->file('proof_file')
                    ->store('donation/transfer-proofs', 'public');
            }

            return DonationTransfer::create(array_merge($validated, [
                'transfer_number' => $this->generateTransferNumber(),
                'status' => DonationTransfer::STATUS_PENDING,
                'requested_by' => auth()->id(),
            ]));
        });

        return redirect()->route('admin.donation-transfers.show', $transfer)
            ->with('success', 'Mutasi Dana berhasil dibuat dan menunggu verifikasi Keuangan.');
    }

    public function show(DonationTransfer $donationTransfer): View
    {
        $donationTransfer->load([
            'fromAccount',
            'toAccount',
            'requester',
            'approver',
            'rejector',
        ]);

        return view('admin.donation-transfers.show', compact('donationTransfer'));
    }

    public function approve(
        DonationTransfer $donationTransfer,
        DonationTransferApprovalService $approvalService
    ): RedirectResponse {
        $this->assertCanVerify($donationTransfer);

        $approvalService->approve($donationTransfer, auth()->user());

        return redirect()->route('admin.donation-transfers.show', $donationTransfer)
            ->with('success', 'Mutasi Dana disetujui. Saldo akun telah diperbarui.');
    }

    public function reject(Request $request, DonationTransfer $donationTransfer): RedirectResponse
    {
        $this->assertCanVerify($donationTransfer);

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($donationTransfer, $validated) {
            $locked = DonationTransfer::query()
                ->lockForUpdate()
                ->findOrFail($donationTransfer->getKey());

            if (! $locked->isPending()) {
                throw ValidationException::withMessages([
                    'status' => 'Transaksi ini sudah diproses dan tidak dapat ditolak.',
                ]);
            }

            $locked->update([
                'status' => DonationTransfer::STATUS_REJECTED,
                'rejected_by' => auth()->id(),
                'rejected_at' => now(),
                'rejection_reason' => $validated['rejection_reason'],
                'approved_by' => null,
                'approved_at' => null,
            ]);
        });

        return redirect()->route('admin.donation-transfers.show', $donationTransfer)
            ->with('success', 'Mutasi Dana ditolak.');
    }

    private function assertCanVerify(DonationTransfer $donationTransfer): void
    {
        if ((int) $donationTransfer->requested_by === (int) auth()->id()) {
            abort(403, 'Pembuat transaksi tidak dapat memverifikasi transaksi sendiri.');
        }

        if (! auth()->user()->isFinanceOfficer()) {
            abort(403, 'Hanya petugas Keuangan yang dapat memverifikasi transaksi Mutasi Dana.');
        }
    }

    private function generateTransferNumber(): string
    {
        do {
            $number = 'MD-'.now()->format('Ymd').'-'.Str::upper(Str::random(4));
        } while (DonationTransfer::where('transfer_number', $number)->exists());

        return $number;
    }

    private function insufficientBalanceMessage(DonationAccount $account, float $available): string
    {
        return 'Saldo akun '.$account->name.' tidak mencukupi. Saldo tersedia Rp'
            .number_format($available, 0, ',', '.').'.';
    }

    private function lockDonationBalanceRows(): void
    {
        DB::table('donation_transactions')->lockForUpdate()->get(['id']);
        DB::table('donation_transfers')->lockForUpdate()->get(['id']);
    }
}
