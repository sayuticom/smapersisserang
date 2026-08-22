<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationAccount;
use App\Models\DonationOutflow;
use App\Models\DonationTransfer;
use App\Models\FinanceExpense;
use App\Models\FinanceIncome;
use App\Services\DonationBalanceService;
use App\Services\DonationFinanceReconciliationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FinanceController extends Controller
{
    const INCOME_TYPES = [
        'Bantuan Sekolah',
        'Dana Operasional',
        'Kas Masuk Lain',
        'Pengembalian Belanja',
        'Pemasukan Usaha Sekolah',
        'Transfer dari Donasi',
    ];

    const EXPENSE_CATEGORIES = [
        'Makan Santri',
        'Dapur',
        'ATK Sekolah',
        'Listrik & Air',
        'Internet / WiFi',
        'Kebersihan',
        'Perlengkapan Asrama',
        'Kegiatan Sekolah',
        'Sarpras',
        'Transport',
        'Lainnya',
    ];

    const PAYMENT_METHODS = ['Tunai', 'Transfer Bank', 'QRIS', 'Lainnya'];

    public function dashboard(?DonationFinanceReconciliationService $reconciliationService = null): View
    {
        $reconciliationService = $reconciliationService ?? app(DonationFinanceReconciliationService::class);
        $now = now();
        $monthStart = $now->copy()->startOfMonth();

        $reconciliation = $reconciliationService->reconciliationSummary();

        $totalIncome = FinanceIncome::sum('amount');
        $totalExpense = FinanceExpense::sum('amount');
        $balance = $totalIncome - $totalExpense;

        $monthIncome = FinanceIncome::where('date', '>=', $monthStart)->sum('amount');
        $monthExternalIncome = FinanceIncome::where('date', '>=', $monthStart)
            ->whereNull('donation_transfer_id')
            ->whereNull('donation_outflow_id')
            ->sum('amount');
        $monthInternalTransfer = FinanceIncome::where('date', '>=', $monthStart)
            ->where(fn ($q) => $q->whereNotNull('donation_transfer_id')->orWhereNotNull('donation_outflow_id'))
            ->sum('amount');
        $monthExpense = FinanceExpense::where('date', '>=', $monthStart)->sum('amount');

        $topExpense = FinanceExpense::where('date', '>=', $monthStart)
            ->selectRaw('expense_category, sum(amount) as total')
            ->groupBy('expense_category')
            ->orderByDesc('total')
            ->first();

        $pendingTransfers = DonationTransfer::query()
            ->with(['fromAccount', 'toAccount', 'requester'])
            ->where('status', DonationTransfer::STATUS_PENDING)
            ->latest('transfer_date')
            ->latest('created_at')
            ->limit(5)
            ->get();

        $pendingTransferCount = DonationTransfer::query()
            ->where('status', DonationTransfer::STATUS_PENDING)
            ->count();

        $pendingTransferTotal = (int) DonationTransfer::query()
            ->where('status', DonationTransfer::STATUS_PENDING)
            ->sum('amount');

        $accountSummaries = [];

        foreach (DonationAccount::finance()->orderBy('name')->get() as $account) {
            $income = FinanceIncome::where('finance_account_id', $account->id)->sum('amount');
            $expense = FinanceExpense::where('finance_account_id', $account->id)->sum('amount');

            $accountSummaries[] = [
                'account' => $account,
                'income' => $income,
                'expense' => $expense,
                'balance' => $income - $expense,
            ];
        }

        return view('admin.finance.dashboard', compact(
            'balance',
            'monthIncome',
            'monthExternalIncome',
            'monthInternalTransfer',
            'monthExpense',
            'topExpense',
            'pendingTransfers',
            'pendingTransferCount',
            'pendingTransferTotal',
            'accountSummaries',
            'reconciliation'
        ));
    }

    // ==========================================
    // PEMASUKAN
    // ==========================================

    public function incomesIndex(): View
    {
        $incomes = FinanceIncome::with(['creator', 'donationOutflow.creator', 'donationTransfer.requester', 'financeAccount'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.finance.incomes.index', compact('incomes'));
    }

    public function incomesCreate(): View
    {
        $incomeTypes = self::INCOME_TYPES;
        $financeAccounts = DonationAccount::finance()->active()->orderBy('name')->get();

        return view('admin.finance.incomes.create', compact('incomeTypes', 'financeAccounts'));
    }

    public function incomesStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'income_type' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
            'finance_account_id' => [
                'required',
                'integer',
                Rule::exists('donation_accounts', 'id')
                    ->where(fn ($query) => $query
                        ->where('category', DonationAccount::CATEGORY_FINANCE)
                        ->where('is_active', true)),
            ],
            'source_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $financeAccount = DonationAccount::findOrFail($validated['finance_account_id']);

        $validated['payment_method'] = $financeAccount->financePaymentMethodLabel();
        $validated['created_by'] = auth()->id();

        if ($request->hasFile('proof_file')) {
            $validated['proof_file'] = $request->file('proof_file')
                ->store('finance/proofs', 'public');
        }

        FinanceIncome::create($validated);

        return redirect()->route('admin.finance.incomes.index')
            ->with('success', 'Pemasukan berhasil dicatat.');
    }

    public function incomesEdit(FinanceIncome $financeIncome): View
    {
        $this->assertCanEditIncome($financeIncome);

        $isIntegrated = $financeIncome->donation_outflow_id !== null;
        $incomeTypes = self::INCOME_TYPES;

        $financeAccounts = DonationAccount::finance()->active()->orderBy('name')->get();
        $currentAccount = $financeIncome->financeAccount;

        if (! $isIntegrated && $currentAccount && $financeAccounts->where('id', $currentAccount->id)->isEmpty()) {
            $financeAccounts->push($currentAccount);
        }

        $paymentMethods = $isIntegrated ? ['Tunai', 'Transfer Bank'] : [];

        return view('admin.finance.incomes.edit', compact(
            'financeIncome',
            'incomeTypes',
            'financeAccounts',
            'paymentMethods',
            'isIntegrated'
        ));
    }

    public function incomesUpdate(Request $request, FinanceIncome $financeIncome): RedirectResponse
    {
        $this->assertCanEditIncome($financeIncome);

        $isIntegrated = $financeIncome->donation_outflow_id !== null;

        $rules = [
            'date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:1'],
            'source_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];

        if ($isIntegrated) {
            $rules['payment_method'] = ['required', Rule::in(['Tunai', 'Transfer Bank'])];
        } else {
            $rules['income_type'] = ['required', 'string'];
            $rules['finance_account_id'] = [
                'required',
                'integer',
                Rule::exists('donation_accounts', 'id')
                    ->where(fn ($query) => $query
                        ->where('category', DonationAccount::CATEGORY_FINANCE)
                        ->where('is_active', true)),
            ];
        }

        $validated = $request->validate($rules);

        if ($isIntegrated) {
            $this->assertEditDoesNotExceedBalance($financeIncome, (float) $validated['amount']);
        }

        $oldProofFile = $financeIncome->proof_file;
        $newProofFile = null;

        if ($request->hasFile('proof_file')) {
            $newProofFile = $request->file('proof_file')->store('finance/proofs', 'public');
        }

        DB::transaction(function () use ($financeIncome, $isIntegrated, $validated, $newProofFile) {
            $lockedIncome = FinanceIncome::query()
                ->lockForUpdate()
                ->findOrFail($financeIncome->getKey());

            if ($isIntegrated) {
                $outflow = DonationOutflow::query()
                    ->lockForUpdate()
                    ->findOrFail($lockedIncome->donation_outflow_id);

                $outflowData = [
                    'handover_date' => $validated['date'],
                    'amount' => $validated['amount'],
                    'payment_method' => $validated['payment_method'] === 'Tunai' ? 'cash' : 'bank_transfer',
                    'donation_source' => $validated['source_name'] ?? $outflow->donation_source,
                    'description' => $validated['description'] ?? $outflow->description,
                ];

                if ($newProofFile) {
                    $outflowData['proof_file'] = $newProofFile;
                }

                $outflow->update($outflowData);

                $incomeData = [
                    'date' => $validated['date'],
                    'amount' => $validated['amount'],
                    'payment_method' => $validated['payment_method'],
                    'source_name' => $validated['source_name'],
                    'description' => $this->buildIntegratedDescription($outflow, $validated['description']),
                ];

                if ($newProofFile) {
                    $incomeData['proof_file'] = $newProofFile;
                }

                $lockedIncome->update($incomeData);
            } else {
                $financeAccount = DonationAccount::findOrFail($validated['finance_account_id']);

                $incomeData = [
                    'date' => $validated['date'],
                    'amount' => $validated['amount'],
                    'finance_account_id' => $validated['finance_account_id'],
                    'payment_method' => $financeAccount->financePaymentMethodLabel(),
                    'source_name' => $validated['source_name'],
                    'income_type' => $validated['income_type'],
                    'description' => $validated['description'],
                ];

                if ($newProofFile) {
                    $incomeData['proof_file'] = $newProofFile;
                }

                $lockedIncome->update($incomeData);
            }
        });

        if ($newProofFile && $oldProofFile && $oldProofFile !== $newProofFile) {
            $this->deleteProofFileIfUnused($oldProofFile);
        }

        Log::warning($isIntegrated ? 'Pemasukan terintegrasi diperbarui' : 'Pemasukan diperbarui', [
            'updated_by' => auth()->id(),
            'income_id' => $financeIncome->id,
            'donation_outflow_id' => $financeIncome->donation_outflow_id,
            'transaction_number' => $financeIncome->donationOutflow?->transaction_number,
            'old_amount' => $financeIncome->amount,
            'new_amount' => $validated['amount'],
            'action' => $isIntegrated ? 'edit terintegrasi (sinkron Donasi Keluar)' : 'edit manual',
            'updated_at' => now()->toDateTimeString(),
        ]);

        return redirect()->route('admin.finance.incomes.index')
            ->with('success', $isIntegrated
                ? 'Pemasukan dan Donasi Keluar terkait berhasil diperbarui.'
                : 'Pemasukan berhasil diperbarui.');
    }

    public function incomesDestroy(FinanceIncome $financeIncome): RedirectResponse
    {
        $this->assertCanDelete();

        if ($financeIncome->donation_transfer_id !== null) {
            abort(403, 'Pemasukan dari Mutasi Dana bersifat read-only dan tidak dapat dihapus.');
        }

        $isIntegrated = $financeIncome->donation_outflow_id !== null;
        $proofFile = $financeIncome->proof_file;

        Log::warning('Pemasukan dihapus', [
            'deleted_by' => auth()->id(),
            'income_id' => $financeIncome->id,
            'donation_outflow_id' => $financeIncome->donation_outflow_id,
            'transaction_number' => $financeIncome->donationOutflow?->transaction_number,
            'nominal' => $financeIncome->amount,
            'action' => $isIntegrated ? 'hapus terintegrasi (termasuk Donasi Keluar)' : 'hapus manual',
            'deleted_at' => now()->toDateTimeString(),
        ]);

        DB::transaction(function () use ($financeIncome, $isIntegrated) {
            $lockedIncome = FinanceIncome::query()
                ->lockForUpdate()
                ->findOrFail($financeIncome->getKey());

            if ($isIntegrated) {
                $outflow = DonationOutflow::query()
                    ->lockForUpdate()
                    ->findOrFail($lockedIncome->donation_outflow_id);

                $lockedIncome->delete();
                $outflow->statusHistories()->delete();
                $outflow->delete();
            } else {
                $lockedIncome->delete();
            }
        });

        if ($proofFile) {
            $this->deleteProofFileIfUnused($proofFile);
        }

        return redirect()->route('admin.finance.incomes.index')
            ->with('success', $isIntegrated
                ? 'Pemasukan beserta Donasi Keluar terkait berhasil dihapus.'
                : 'Pemasukan berhasil dihapus.');
    }

    // ==========================================
    // PENGELUARAN
    // ==========================================

    public function expensesIndex(): View
    {
        $expenses = FinanceExpense::with(['creator', 'financeAccount'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.finance.expenses.index', compact('expenses'));
    }

    public function expensesCreate(): View
    {
        $expenseCategories = FinanceExpense::expenseCategories();
        $financeAccounts = DonationAccount::finance()->active()->orderBy('name')->get();

        return view('admin.finance.expenses.create', compact('expenseCategories', 'financeAccounts'));
    }

    public function expensesStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'expense_category' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
            'paid_to' => ['nullable', 'string', 'max:255'],
            'finance_account_id' => [
                'required',
                'integer',
                Rule::exists('donation_accounts', 'id')
                    ->where(fn ($query) => $query
                        ->where('category', DonationAccount::CATEGORY_FINANCE)
                        ->where('is_active', true)),
            ],
            'description' => ['nullable', 'string'],
            'proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $financeAccount = DonationAccount::findOrFail($validated['finance_account_id']);

        $available = app(DonationBalanceService::class)->financeBalanceByAccount((int) $financeAccount->id);

        if ((float) $validated['amount'] > $available) {
            throw ValidationException::withMessages([
                'amount' => "Saldo {$financeAccount->name} tidak mencukupi. Saldo tersedia Rp "
                    .number_format($available, 0, ',', '.').'.',
            ]);
        }

        $validated['payment_method'] = $financeAccount->financePaymentMethodLabel();
        $validated['created_by'] = auth()->id();

        if ($request->hasFile('proof_file')) {
            $validated['proof_file'] = $request->file('proof_file')
                ->store('finance/proofs', 'public');
        }

        FinanceExpense::create($validated);

        return redirect()->route('admin.finance.expenses.index')
            ->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function expensesEdit(FinanceExpense $financeExpense): View
    {
        $expenseCategories = FinanceExpense::expenseCategories();

        $financeAccounts = DonationAccount::finance()->active()->orderBy('name')->get();
        $currentAccount = $financeExpense->financeAccount;

        if ($currentAccount && $financeAccounts->where('id', $currentAccount->id)->isEmpty()) {
            $financeAccounts->push($currentAccount);
        }

        return view('admin.finance.expenses.edit', compact('financeExpense', 'expenseCategories', 'financeAccounts'));
    }

    public function expensesUpdate(Request $request, FinanceExpense $financeExpense): RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'expense_category' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
            'paid_to' => ['nullable', 'string', 'max:255'],
            'finance_account_id' => [
                'required',
                'integer',
                Rule::exists('donation_accounts', 'id')
                    ->where(fn ($query) => $query
                        ->where('category', DonationAccount::CATEGORY_FINANCE)
                        ->where('is_active', true)),
            ],
            'description' => ['nullable', 'string'],
            'proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $financeAccount = DonationAccount::findOrFail($validated['finance_account_id']);

        $service = app(DonationBalanceService::class);
        $available = $service->financeBalanceByAccountExcludingExpense(
            (int) $financeAccount->id,
            (int) $financeExpense->getKey()
        );

        if ((float) $validated['amount'] > $available) {
            throw ValidationException::withMessages([
                'amount' => "Saldo {$financeAccount->name} tidak mencukupi. Saldo tersedia Rp "
                    .number_format($available, 0, ',', '.').'.',
            ]);
        }

        $validated['payment_method'] = $financeAccount->financePaymentMethodLabel();

        if ($request->hasFile('proof_file')) {
            if ($financeExpense->proof_file) {
                Storage::disk('public')->delete($financeExpense->proof_file);
            }
            $validated['proof_file'] = $request->file('proof_file')
                ->store('finance/proofs', 'public');
        }

        DB::transaction(function () use ($financeExpense, $validated) {
            FinanceExpense::query()
                ->lockForUpdate()
                ->findOrFail($financeExpense->getKey())
                ->update($validated);
        });

        return redirect()->route('admin.finance.expenses.index')
            ->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function expensesDestroy(FinanceExpense $financeExpense): RedirectResponse
    {
        $this->assertCanDelete();

        Log::warning('Pengeluaran dihapus', [
            'deleted_by' => auth()->id(),
            'expense_id' => $financeExpense->id,
            'nominal' => $financeExpense->amount,
            'expense_category' => $financeExpense->expense_category,
            'deleted_at' => now()->toDateTimeString(),
        ]);

        $proofFile = $financeExpense->proof_file;

        DB::transaction(function () use ($financeExpense) {
            $financeExpense->delete();
        });

        if ($proofFile) {
            $this->deleteProofFileIfUnused($proofFile);
        }

        return redirect()->route('admin.finance.expenses.index')
            ->with('success', 'Pengeluaran berhasil dihapus.');
    }

    // ==========================================
    // LAPORAN
    // ==========================================

    public function report(Request $request, ?DonationFinanceReconciliationService $reconciliationService = null): View
    {
        $reconciliationService = $reconciliationService ?? app(DonationFinanceReconciliationService::class);
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $incomes = FinanceIncome::with(['financeAccount', 'donationTransfer', 'donationOutflow'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')->get();
        $expenses = FinanceExpense::with(['financeAccount'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')->get();

        $totalIncome = (int) $incomes->sum('amount');
        $totalExpense = (int) $expenses->sum('amount');
        $balance = $totalIncome - $totalExpense;

        $externalIncomesTotal = (int) $incomes->filter(fn ($i) => $i->isExternal())->sum('amount');
        $internalTransferIncomesTotal = (int) $incomes->filter(fn ($i) => $i->isInternalTransfer())->sum('amount');

        $consolidated = $reconciliationService->consolidatedReport($startDate, $endDate);

        $expenseByCategory = $expenses->groupBy('expense_category')
            ->map(fn ($items) => $items->sum('amount'));

        $isPrint = $request->boolean('print');

        if ($isPrint) {
            return view('admin.finance.report-print', compact(
                'startDate', 'endDate', 'incomes', 'expenses',
                'totalIncome', 'totalExpense', 'balance', 'expenseByCategory',
                'externalIncomesTotal', 'internalTransferIncomesTotal', 'consolidated'
            ));
        }

        return view('admin.finance.report', compact(
            'startDate', 'endDate', 'incomes', 'expenses',
            'totalIncome', 'totalExpense', 'balance', 'expenseByCategory',
            'externalIncomesTotal', 'internalTransferIncomesTotal', 'consolidated'
        ));
    }

    private function assertCanDelete(): void
    {
        if (!auth()->user()->isSuperadmin()) {
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

    private function mapPaymentToHandoverMethod(string $paymentMethod): string
    {
        return match ($paymentMethod) {
            'Tunai' => 'cash',
            'Transfer Bank' => 'transfer',
        };
    }

    private function buildIntegratedDescription(DonationOutflow $outflow, ?string $latestKeterangan): string
    {
        $lines = [
            'Donasi Keluar: '.$outflow->transaction_number,
            'Keterangan/Periode: '.($latestKeterangan ?: $outflow->description ?: '-'),
        ];

        if ($outflow->destination_account) {
            $lines[] = 'Kas/Rekening Tujuan: '.$outflow->destination_account;
        }

        if ($outflow->notes) {
            $lines[] = 'Catatan: '.$outflow->notes;
        }

        return implode("\n", $lines);
    }

    private function assertCanEditIncome(FinanceIncome $financeIncome): void
    {
        $user = auth()->user();

        if ($financeIncome->donation_transfer_id !== null) {
            abort(403, 'Pemasukan dari Mutasi Dana bersifat read-only dan tidak dapat diubah.');
        }

        if ($financeIncome->donation_outflow_id !== null) {
            abort_unless(
                $user->isSuperadmin(),
                403,
                'Hanya superadmin yang dapat mengelola Pemasukan yang berasal dari Donasi Keluar.'
            );

            return;
        }

        if ($user->isSuperadmin()) {
            return;
        }

        if (! $user->isAdmin() && ! $user->hasRole('staf_keuangan')) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola Pemasukan ini.');
        }

        if (! $user->hasPermissionTo('finance.transactions.manage')) {
            abort(403, 'Anda tidak memiliki izin untuk tindakan ini.');
        }
    }

    private function assertEditDoesNotExceedBalance(FinanceIncome $financeIncome, float $newAmount): void
    {
        $service = app(DonationBalanceService::class);

        $otherApprovedOutflow = DonationOutflow::query()
            ->where('status', DonationOutflow::STATUS_APPROVED)
            ->whereKeyNot($financeIncome->donation_outflow_id)
            ->sum('amount');

        $approvedTransfer = $service->approvedDonationToFinanceTransferAmount();

        $availableBefore = $service->totalIncoming() - (float) $otherApprovedOutflow - (float) $approvedTransfer;

        if ($newAmount > $availableBefore) {
            throw ValidationException::withMessages([
                'amount' => 'Nominal perubahan melebihi saldo dana donasi yang tersedia.',
            ]);
        }
    }
}
