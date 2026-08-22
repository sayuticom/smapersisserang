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
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Kas Masuk Keuangan</p>
                <p class="text-xl font-bold text-blue-600 mt-2">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                <div class="mt-2 text-xs text-gray-500 space-y-0.5">
                    <p>• Pemasukan Eksternal: <span class="font-semibold text-gray-700">Rp {{ number_format($externalIncomesTotal, 0, ',', '.') }}</span></p>
                    <p>• Transfer Internal Donasi: <span class="font-semibold text-blue-700">Rp {{ number_format($internalTransferIncomesTotal, 0, ',', '.') }}</span></p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Pengeluaran</p>
                <p class="text-xl font-bold text-red-600 mt-2">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-2">Realisasi belanja operasional</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Saldo Kas Periode</p>
                <p class="text-xl font-bold {{ $balance >= 0 ? 'text-emerald-600' : 'text-red-600' }} mt-2">Rp {{ number_format($balance, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-2">Kas masuk - pengeluaran periode ini</p>
            </div>
        </div>

        {{-- KONSOLIDASI PENDAPATAN ORGANISASI (NON-DOUBLE COUNTING) --}}
        <div class="bg-white rounded-xl shadow-sm border border-purple-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-purple-100 bg-purple-50 flex items-center justify-between gap-3 flex-wrap">
                <div>
                    <h3 class="text-sm font-bold text-purple-900 uppercase tracking-wider">Laporan Konsolidasi Pendapatan Sekolah (Periode Ini)</h3>
                    <p class="text-xs text-purple-700 mt-0.5">Membedakan pendapatan eksternal baru dari perpindahan dana internal agar tidak tercatat ganda</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-200 text-purple-800">
                    Konsolidasi Akuntansi
                </span>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                <div class="bg-gray-50 p-3.5 rounded-lg border border-gray-100">
                    <span class="text-gray-500 font-medium block">1. Donasi Masuk Valid</span>
                    <span class="text-sm font-bold text-indigo-700 mt-1 block">Rp {{ number_format($consolidated['total_donation'], 0, ',', '.') }}</span>
                    <span class="text-[11px] text-gray-400 mt-0.5 block">{{ $consolidated['donations_count'] }} transaksi donasi</span>
                </div>
                <div class="bg-gray-50 p-3.5 rounded-lg border border-gray-100">
                    <span class="text-gray-500 font-medium block">2. Pemasukan Eksternal Keuangan</span>
                    <span class="text-sm font-bold text-emerald-700 mt-1 block">Rp {{ number_format($consolidated['total_external_income'], 0, ',', '.') }}</span>
                    <span class="text-[11px] text-gray-400 mt-0.5 block">{{ $consolidated['external_incomes_count'] }} transaksi eksternal</span>
                </div>
                <div class="bg-purple-50 p-3.5 rounded-lg border border-purple-100">
                    <span class="text-purple-700 font-bold block">Total Pendapatan Murni (1 + 2)</span>
                    <span class="text-base font-extrabold text-purple-900 mt-1 block">Rp {{ number_format($consolidated['total_organization_revenue'], 0, ',', '.') }}</span>
                    <span class="text-[11px] text-purple-600 mt-0.5 block">Pendapatan riil organisasi</span>
                </div>
                <div class="bg-blue-50 p-3.5 rounded-lg border border-blue-100">
                    <span class="text-blue-700 font-medium block">Transfer Internal (Donasi &rarr; Keuangan)</span>
                    <span class="text-sm font-bold text-blue-900 mt-1 block">Rp {{ number_format($consolidated['total_internal_transfer'], 0, ',', '.') }}</span>
                    <span class="text-[11px] text-blue-600 mt-0.5 block">Perpindahan saldo (bukan pendapatan baru)</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Rincian Pemasukan --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 bg-blue-50">
                    <h3 class="text-sm font-semibold text-blue-800 uppercase tracking-wider">Rincian Pemasukan Keuangan</h3>
                </div>
                @if($incomes->isEmpty())
                    <div class="p-5 text-center text-gray-400 text-sm">Tidak ada pemasukan.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-left">
                                <tr>
                                    <th class="px-3 py-2 text-gray-500 font-medium">Tanggal</th>
                                    <th class="px-3 py-2 text-gray-500 font-medium">Jenis / Tipe</th>
                                    <th class="px-3 py-2 text-gray-500 font-medium">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($incomes as $income)
                                <tr>
                                    <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $income->date->format('d/m/Y') }}</td>
                                    <td class="px-3 py-2 text-gray-900">
                                        <div class="font-medium">{{ $income->income_type }}</div>
                                        @if($income->isInternalTransfer())
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-100 text-blue-700 mt-0.5">
                                                Transfer Internal
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-700 mt-0.5">
                                                Eksternal
                                            </span>
                                        @endif
                                    </td>
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
