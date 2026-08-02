<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationOutflow;
use App\Services\DonationBalanceService;
use App\Services\DonationOutflowApprovalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DonationOutflowController extends Controller
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

        $query = DonationOutflow::with('creator');

        if ($request->filled('date')) {
            $date = \Carbon\Carbon::parse($request->date);

            if ($request->input('filter_type') === 'month') {
                $query->whereYear('handover_date', $date->year)
                    ->whereMonth('handover_date', $date->month);
            } else {
                $query->whereDate('handover_date', $request->date);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                    ->orWhere('donation_source', 'like', "%{$search}%")
                    ->orWhere('destination_account', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $outflows = $query->latest('handover_date')
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        $balance = auth()->user()->hasPermissionTo('donation.balance.view')
            ? app(DonationBalanceService::class)->summary()
            : [];

        return view('admin.donation-outflows.index', compact('outflows', 'balance'));
    }

    public function create(): View
    {
        $balance = auth()->user()->hasPermissionTo('donation.balance.view')
            ? app(DonationBalanceService::class)->summary()
            : [];

        return view('admin.donation-outflows.create', [
            'balance' => $balance,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'handover_date' => ['required', 'date'],
            'donation_source' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'handover_method' => ['required', 'in:cash,transfer'],
            'destination_account' => ['required', 'string', 'max:255'],
            'proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'notes' => ['nullable', 'string'],
        ]);

        $balanceService = app(DonationBalanceService::class);
        $availableBalance = $balanceService->availableBalance();

        if ((float) $validated['amount'] > (float) $availableBalance) {
            throw ValidationException::withMessages([
                'amount' => 'Nominal Donasi Keluar melebihi saldo dana donasi yang tersedia.',
            ]);
        }

        if ($request->hasFile('proof_file')) {
            $validated['proof_file'] = $request->file('proof_file')
                ->store('donation/outflow-proofs', 'public');
        }

        $outflow = DB::transaction(function () use ($validated) {
            $outflow = DonationOutflow::create(array_merge($validated, [
                'transaction_number' => $this->generateTransactionNumber(),
                'status' => DonationOutflow::STATUS_PENDING,
                'created_by' => auth()->id(),
            ]));

            $outflow->statusHistories()->create([
                'from_status' => null,
                'to_status' => DonationOutflow::STATUS_PENDING,
                'changed_by' => auth()->id(),
            ]);

            return $outflow;
        });

        return redirect()->route('admin.donation-outflows.show', $outflow)
            ->with('success', 'Donasi Keluar berhasil dibuat dan menunggu verifikasi Keuangan.');
    }

    public function show(DonationOutflow $donationOutflow): View
    {
        $donationOutflow->load([
            'creator',
            'approver',
            'rejector',
            'financeIncome',
            'statusHistories.changedBy',
        ]);

        return view('admin.donation-outflows.show', compact('donationOutflow'));
    }

    public function approve(
        DonationOutflow $donationOutflow,
        DonationOutflowApprovalService $approvalService
    ): RedirectResponse {
        $this->assertCanVerify($donationOutflow);

        $approvalService->approve($donationOutflow, auth()->user());

        return redirect()->route('admin.donation-outflows.show', $donationOutflow)
            ->with('success', 'Donasi Keluar disetujui dan Pemasukan Keuangan berhasil dibuat.');
    }

    public function reject(Request $request, DonationOutflow $donationOutflow): RedirectResponse
    {
        $this->assertCanVerify($donationOutflow);

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($donationOutflow, $validated) {
            $lockedOutflow = DonationOutflow::query()
                ->lockForUpdate()
                ->findOrFail($donationOutflow->getKey());

            if ($lockedOutflow->status !== DonationOutflow::STATUS_PENDING) {
                throw ValidationException::withMessages([
                    'status' => 'Transaksi ini sudah diproses dan tidak dapat ditolak.',
                ]);
            }

            $lockedOutflow->update([
                'status' => DonationOutflow::STATUS_REJECTED,
                'rejected_by' => auth()->id(),
                'rejected_at' => now(),
                'rejection_reason' => $validated['rejection_reason'],
                'approved_by' => null,
                'approved_at' => null,
            ]);

            $lockedOutflow->statusHistories()->create([
                'from_status' => DonationOutflow::STATUS_PENDING,
                'to_status' => DonationOutflow::STATUS_REJECTED,
                'reason' => $validated['rejection_reason'],
                'changed_by' => auth()->id(),
            ]);
        });

        return redirect()->route('admin.donation-outflows.show', $donationOutflow)
            ->with('success', 'Donasi Keluar ditolak.');
    }

    private function assertCanVerify(DonationOutflow $donationOutflow): void
    {
        if ((int) $donationOutflow->created_by === (int) auth()->id()) {
            abort(403, 'Pembuat transaksi tidak dapat memverifikasi transaksi sendiri.');
        }

        if (! auth()->user()->isFinanceOfficer()) {
            abort(403, 'Hanya petugas Keuangan yang dapat memverifikasi transaksi Donasi Keluar.');
        }
    }

    private function generateTransactionNumber(): string
    {
        do {
            $number = 'DK-'.now()->format('Ymd').'-'.Str::upper(Str::random(4));
        } while (DonationOutflow::where('transaction_number', $number)->exists());

        return $number;
    }
}
