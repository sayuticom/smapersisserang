<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DonationPaymentMethod;
use App\Http\Controllers\Controller;
use App\Models\DonationOutflow;
use App\Models\FinanceExpense;
use App\Models\FinanceIncome;
use App\Services\DonationBalanceService;
use App\Services\DonationOutflowApprovalService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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
            'payment_method' => ['nullable', 'in:unclassified,cash,bank_transfer,qris,other'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->filled('filter_type') && ! $request->filled('date')) {
            throw ValidationException::withMessages([
                'date' => 'Tanggal harus dipilih terlebih dahulu.',
            ]);
        }

        $query = DonationOutflow::with('creator');

        if ($request->filled('date')) {
            $date = Carbon::parse($request->date);

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

        if ($request->filled('payment_method')) {
            $filter = $request->payment_method;
            if ($filter === 'unclassified') {
                $query->whereNull('payment_method');
            } elseif (in_array($filter, DonationPaymentMethod::values(), true)) {
                $query->where('payment_method', $filter);
            }
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

        $balance = app(DonationBalanceService::class)->summary();

        return view('admin.donation-outflows.index', compact('outflows', 'balance'));
    }

    public function create(): View
    {
        $balance = app(DonationBalanceService::class)->summary();

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
            'payment_method' => ['required', Rule::in(DonationPaymentMethod::values())],
            'destination_account' => ['required', 'string', 'max:255'],
            'proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'notes' => ['nullable', 'string'],
        ]);

        $outflow = DB::transaction(function () use ($request, $validated) {
            $this->lockDonationBalanceRows();
            $this->assertSufficientBalance($validated['payment_method'], (float) $validated['amount']);

            if ($request->hasFile('proof_file')) {
                $validated['proof_file'] = $request->file('proof_file')
                    ->store('donation/outflow-proofs', 'public');
            }

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

    public function edit(DonationOutflow $donationOutflow): View
    {
        $this->assertCanEdit();

        $balance = app(DonationBalanceService::class)->summary();
        $availableByMethod = collect($balance['by_payment_method'])
            ->mapWithKeys(fn (array $row, string $method) => [
                $method => $row['available_balance']
                    + ($donationOutflow->payment_method === $method ? (float) $donationOutflow->amount : 0),
            ])
            ->all();

        return view('admin.donation-outflows.edit', compact('donationOutflow', 'balance', 'availableByMethod'));
    }

    public function update(Request $request, DonationOutflow $donationOutflow): RedirectResponse
    {
        $this->assertCanEdit();

        $validated = $request->validate([
            'handover_date' => ['required', 'date'],
            'donation_source' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_method' => ['required', Rule::in(DonationPaymentMethod::values())],
            'proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $oldProofFile = null;
        $newProofFile = null;

        DB::transaction(function () use ($request, $donationOutflow, $validated, &$oldProofFile, &$newProofFile) {
            $lockedOutflow = DonationOutflow::query()
                ->lockForUpdate()
                ->findOrFail($donationOutflow->getKey());

            $this->lockDonationBalanceRows();

            $newAmount = (float) $validated['amount'];
            if ($lockedOutflow->status !== DonationOutflow::STATUS_PENDING
                && $newAmount !== (float) $lockedOutflow->amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Nominal hanya dapat diubah ketika Donasi Keluar masih berstatus pending.',
                ]);
            }

            $newMethod = $validated['payment_method'];
            $availableForMethod = (float) app(DonationBalanceService::class)
                ->availableBalanceForMethod($newMethod);

            if ($lockedOutflow->payment_method === $newMethod) {
                $availableForMethod += (float) $lockedOutflow->amount;
            }

            if ($newAmount > $availableForMethod) {
                throw ValidationException::withMessages([
                    'payment_method' => $this->insufficientBalanceMessage($newMethod, $availableForMethod),
                ]);
            }

            $updates = [
                'handover_date' => $validated['handover_date'],
                'donation_source' => $validated['donation_source'],
                'description' => $validated['description'] ?? null,
                'amount' => $newAmount,
                'payment_method' => $newMethod,
            ];

            if ($request->hasFile('proof_file')) {
                $newProofFile = $request->file('proof_file')
                    ->store('donation/outflow-proofs', 'public');
                $oldProofFile = $lockedOutflow->proof_file;
                $updates['proof_file'] = $newProofFile;
            }

            $lockedOutflow->update($updates);
        });

        if ($oldProofFile && $oldProofFile !== $newProofFile) {
            $this->deleteProofFileIfUnused($oldProofFile);
        }

        return redirect()->route('admin.donation-outflows.show', $donationOutflow)
            ->with('success', 'Donasi Keluar berhasil diperbarui.');
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

    private function assertCanEdit(): void
    {
        if (! auth()->user()->isSuperadmin() && ! auth()->user()->isAdmin()) {
            abort(403, 'Hanya superadmin atau admin yang dapat mengubah Donasi Keluar.');
        }
    }

    public function destroy(DonationOutflow $donationOutflow): RedirectResponse
    {
        $this->assertCanDelete();

        $proofFile = null;

        DB::transaction(function () use ($donationOutflow, &$proofFile) {
            $lockedOutflow = DonationOutflow::query()
                ->lockForUpdate()
                ->findOrFail($donationOutflow->getKey());

            $income = $lockedOutflow->financeIncome;

            if ($income) {
                $income->delete();
            }

            $proofFile = $lockedOutflow->proof_file;

            $lockedOutflow->statusHistories()->delete();
            $lockedOutflow->delete();
        });

        Log::warning('Donasi Keluar dihapus', [
            'deleted_by' => auth()->id(),
            'transaction_number' => $donationOutflow->transaction_number,
            'nominal' => $donationOutflow->amount,
            'status' => $donationOutflow->status,
            'deleted_at' => now()->toDateTimeString(),
        ]);

        if ($proofFile) {
            $this->deleteProofFileIfUnused($proofFile);
        }

        return redirect()->route('admin.donation-outflows.index')
            ->with('success', 'Donasi Keluar beserta data terkait berhasil dihapus.');
    }

    private function assertCanDelete(): void
    {
        if (! auth()->user()->isSuperadmin()) {
            abort(403, 'Hanya superadmin yang dapat menghapus data ini.');
        }
    }

    private function deleteProofFileIfUnused(string $path): void
    {
        $stillUsed = FinanceIncome::where('proof_file', $path)->exists()
            || FinanceExpense::where('proof_file', $path)->exists()
            || DonationOutflow::where('proof_file', $path)->exists();

        if (! $stillUsed) {
            Storage::disk('public')->delete($path);
        }
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

    private function assertSufficientBalance(string $method, float $amount): void
    {
        $available = app(DonationBalanceService::class)->availableBalanceForMethod($method);

        if ($amount > $available) {
            throw ValidationException::withMessages([
                'payment_method' => $this->insufficientBalanceMessage($method, $available),
            ]);
        }
    }

    private function insufficientBalanceMessage(string $method, float $available): string
    {
        return 'Saldo '.DonationPaymentMethod::labelOf($method)
            .' tidak mencukupi. Saldo tersedia Rp'.number_format($available, 0, ',', '.').'.';
    }

    private function lockDonationBalanceRows(): void
    {
        DB::table('donation_transactions')->lockForUpdate()->get(['id']);
        DB::table('donation_outflows')->lockForUpdate()->get(['id']);
    }
}
