<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceExpense;
use App\Models\FinanceIncome;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

    public function dashboard(): View
    {
        $now = now();
        $monthStart = $now->copy()->startOfMonth();

        $totalIncome = FinanceIncome::sum('amount');
        $totalExpense = FinanceExpense::sum('amount');
        $balance = $totalIncome - $totalExpense;

        $monthIncome = FinanceIncome::where('date', '>=', $monthStart)->sum('amount');
        $monthExpense = FinanceExpense::where('date', '>=', $monthStart)->sum('amount');

        $topExpense = FinanceExpense::where('date', '>=', $monthStart)
            ->selectRaw('expense_category, sum(amount) as total')
            ->groupBy('expense_category')
            ->orderByDesc('total')
            ->first();

        return view('admin.finance.dashboard', compact(
            'balance', 'monthIncome', 'monthExpense', 'topExpense'
        ));
    }

    // ==========================================
    // PEMASUKAN
    // ==========================================

    public function incomesIndex(): View
    {
        $incomes = FinanceIncome::with('creator')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.finance.incomes.index', compact('incomes'));
    }

    public function incomesCreate(): View
    {
        $incomeTypes = self::INCOME_TYPES;
        $paymentMethods = self::PAYMENT_METHODS;
        return view('admin.finance.incomes.create', compact('incomeTypes', 'paymentMethods'));
    }

    public function incomesStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'income_type' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string'],
            'source_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

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
        $incomeTypes = self::INCOME_TYPES;
        $paymentMethods = self::PAYMENT_METHODS;
        return view('admin.finance.incomes.edit', compact('financeIncome', 'incomeTypes', 'paymentMethods'));
    }

    public function incomesUpdate(Request $request, FinanceIncome $financeIncome): RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'income_type' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string'],
            'source_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        if ($request->hasFile('proof_file')) {
            if ($financeIncome->proof_file) {
                Storage::disk('public')->delete($financeIncome->proof_file);
            }
            $validated['proof_file'] = $request->file('proof_file')
                ->store('finance/proofs', 'public');
        }

        $financeIncome->update($validated);

        return redirect()->route('admin.finance.incomes.index')
            ->with('success', 'Pemasukan berhasil diperbarui.');
    }

    public function incomesDestroy(FinanceIncome $financeIncome): RedirectResponse
    {
        if ($financeIncome->proof_file) {
            Storage::disk('public')->delete($financeIncome->proof_file);
        }
        $financeIncome->delete();

        return redirect()->route('admin.finance.incomes.index')
            ->with('success', 'Pemasukan berhasil dihapus.');
    }

    // ==========================================
    // PENGELUARAN
    // ==========================================

    public function expensesIndex(): View
    {
        $expenses = FinanceExpense::with('creator')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.finance.expenses.index', compact('expenses'));
    }

    public function expensesCreate(): View
    {
        $expenseCategories = FinanceExpense::expenseCategories();
        $paymentMethods = self::PAYMENT_METHODS;
        return view('admin.finance.expenses.create', compact('expenseCategories', 'paymentMethods'));
    }

    public function expensesStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'expense_category' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
            'paid_to' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

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
        $paymentMethods = self::PAYMENT_METHODS;
        return view('admin.finance.expenses.edit', compact('financeExpense', 'expenseCategories', 'paymentMethods'));
    }

    public function expensesUpdate(Request $request, FinanceExpense $financeExpense): RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'expense_category' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
            'paid_to' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        if ($request->hasFile('proof_file')) {
            if ($financeExpense->proof_file) {
                Storage::disk('public')->delete($financeExpense->proof_file);
            }
            $validated['proof_file'] = $request->file('proof_file')
                ->store('finance/proofs', 'public');
        }

        $financeExpense->update($validated);

        return redirect()->route('admin.finance.expenses.index')
            ->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function expensesDestroy(FinanceExpense $financeExpense): RedirectResponse
    {
        if ($financeExpense->proof_file) {
            Storage::disk('public')->delete($financeExpense->proof_file);
        }
        $financeExpense->delete();

        return redirect()->route('admin.finance.expenses.index')
            ->with('success', 'Pengeluaran berhasil dihapus.');
    }

    // ==========================================
    // LAPORAN
    // ==========================================

    public function report(Request $request): View
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $incomes = FinanceIncome::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')->get();
        $expenses = FinanceExpense::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')->get();

        $totalIncome = $incomes->sum('amount');
        $totalExpense = $expenses->sum('amount');
        $balance = $totalIncome - $totalExpense;

        $expenseByCategory = $expenses->groupBy('expense_category')
            ->map(fn($items) => $items->sum('amount'));

        $isPrint = $request->boolean('print');

        if ($isPrint) {
            return view('admin.finance.report-print', compact(
                'startDate', 'endDate', 'incomes', 'expenses',
                'totalIncome', 'totalExpense', 'balance', 'expenseByCategory'
            ));
        }

        return view('admin.finance.report', compact(
            'startDate', 'endDate', 'incomes', 'expenses',
            'totalIncome', 'totalExpense', 'balance', 'expenseByCategory'
        ));
    }
}
