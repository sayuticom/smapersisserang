<x-admin-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Laporan Keuangan</h2>
                <p class="text-sm text-gray-500 mt-1">Filter dan lihat laporan pemasukan & pengeluaran</p>
            </div>
            <a href="{{ route('admin.finance.report', ['start_date' => $startDate, 'end_date' => $endDate, 'print' => 1]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Laporan
            </a>
        </div>

        <form method="GET" action="{{ route('admin.finance.report') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex flex-col sm:flex-row items-end gap-4">
            <div class="flex-1 w-full">
                <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div class="flex-1 w-full">
                <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">Tampilkan</button>
        </form>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Pemasukan</p>
                <p class="text-xl font-bold text-blue-600 mt-2">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Pengeluaran</p>
                <p class="text-xl font-bold text-red-600 mt-2">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Saldo Periode</p>
                <p class="text-xl font-bold {{ $balance >= 0 ? 'text-emerald-600' : 'text-red-600' }} mt-2">Rp {{ number_format($balance, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Rincian Pemasukan --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 bg-blue-50">
                    <h3 class="text-sm font-semibold text-blue-800 uppercase tracking-wider">Rincian Pemasukan</h3>
                </div>
                @if($incomes->isEmpty())
                    <div class="p-5 text-center text-gray-400 text-sm">Tidak ada pemasukan.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-left">
                                <tr>
                                    <th class="px-3 py-2 text-gray-500 font-medium">Tanggal</th>
                                    <th class="px-3 py-2 text-gray-500 font-medium">Jenis</th>
                                    <th class="px-3 py-2 text-gray-500 font-medium">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($incomes as $income)
                                <tr>
                                    <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $income->date->format('d/m/Y') }}</td>
                                    <td class="px-3 py-2 text-gray-900">{{ $income->income_type }}</td>
                                    <td class="px-3 py-2 text-blue-600 font-medium whitespace-nowrap">Rp {{ number_format($income->amount, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Rincian Pengeluaran --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 bg-red-50">
                    <h3 class="text-sm font-semibold text-red-800 uppercase tracking-wider">Rincian Pengeluaran</h3>
                </div>
                @if($expenses->isEmpty())
                    <div class="p-5 text-center text-gray-400 text-sm">Tidak ada pengeluaran.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-left">
                                <tr>
                                    <th class="px-3 py-2 text-gray-500 font-medium">Tanggal</th>
                                    <th class="px-3 py-2 text-gray-500 font-medium">Kategori</th>
                                    <th class="px-3 py-2 text-gray-500 font-medium">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($expenses as $expense)
                                <tr>
                                    <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $expense->date->format('d/m/Y') }}</td>
                                    <td class="px-3 py-2 text-gray-900">{{ $expense->expense_category }}</td>
                                    <td class="px-3 py-2 text-red-600 font-medium whitespace-nowrap">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Rekap per Kategori --}}
        @if($expenseByCategory->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider">Rekap Pengeluaran per Kategori</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="px-4 py-2.5 text-gray-500 font-medium">Kategori</th>
                            <th class="px-4 py-2.5 text-gray-500 font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($expenseByCategory as $category => $total)
                        <tr>
                            <td class="px-4 py-2.5 text-gray-900">{{ $category }}</td>
                            <td class="px-4 py-2.5 text-red-600 font-medium">Rp {{ number_format($total, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</x-admin-layout>
