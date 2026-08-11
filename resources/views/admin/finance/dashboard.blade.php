<x-admin-layout>
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Dashboard Keuangan</h2>
            <p class="text-sm text-gray-500 mt-1">Ringkasan keuangan SMA Persis Serang</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="min-w-0 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Saldo Saat Ini</p>
                <p class="text-xl sm:text-2xl font-bold text-emerald-600 mt-2 break-words">Rp {{ number_format($balance, 0, ',', '.') }}</p>
            </div>
            <div class="min-w-0 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pemasukan Bulan Ini</p>
                <p class="text-xl sm:text-2xl font-bold text-blue-600 mt-2 break-words">Rp {{ number_format($monthIncome, 0, ',', '.') }}</p>
            </div>
            <div class="min-w-0 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengeluaran Bulan Ini</p>
                <p class="text-xl sm:text-2xl font-bold text-red-600 mt-2 break-words">Rp {{ number_format($monthExpense, 0, ',', '.') }}</p>
            </div>
            <div class="min-w-0 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengeluaran Terbesar</p>
                <p class="text-lg font-bold text-gray-900 mt-2 break-words">{{ $topExpense?->expense_category ?? '-' }}</p>
                @if($topExpense)
                <p class="text-sm text-red-600 font-medium break-words">Rp {{ number_format($topExpense->total, 0, ',', '.') }}</p>
                @endif
            </div>
        </div>

        @if($accountSummaries)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Saldo per Akun Keuangan</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left">
                            <tr>
                                <th class="px-5 py-2.5 text-gray-500 font-medium">Akun</th>
                                <th class="px-5 py-2.5 text-gray-500 font-medium text-right">Masuk</th>
                                <th class="px-5 py-2.5 text-gray-500 font-medium text-right">Keluar</th>
                                <th class="px-5 py-2.5 text-gray-500 font-medium text-right">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($accountSummaries as $summary)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-2.5 font-medium text-gray-900">{{ $summary['account']->name }}</td>
                                <td class="px-5 py-2.5 text-blue-600 font-medium text-right whitespace-nowrap">Rp {{ number_format($summary['income'], 0, ',', '.') }}</td>
                                <td class="px-5 py-2.5 text-red-600 font-medium text-right whitespace-nowrap">Rp {{ number_format($summary['expense'], 0, ',', '.') }}</td>
                                <td class="px-5 py-2.5 text-emerald-600 font-bold text-right whitespace-nowrap">Rp {{ number_format($summary['balance'], 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3 flex-wrap">
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Mutasi Dana Menunggu Verifikasi</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $pendingTransferCount }} transaksi · Rp {{ number_format($pendingTransferTotal, 0, ',', '.') }}</p>
                </div>
                <a href="{{ route('admin.donation-transfers.index') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-800">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="px-5 py-2.5 text-gray-500 font-medium">Nomor Mutasi</th>
                            <th class="px-5 py-2.5 text-gray-500 font-medium">Tanggal</th>
                            <th class="px-5 py-2.5 text-gray-500 font-medium">Dari Akun Donasi</th>
                            <th class="px-5 py-2.5 text-gray-500 font-medium">Ke Akun Keuangan</th>
                            <th class="px-5 py-2.5 text-gray-500 font-medium text-right">Nominal</th>
                            <th class="px-5 py-2.5 text-gray-500 font-medium">Pengaju</th>
                            <th class="px-5 py-2.5 text-gray-500 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pendingTransfers as $transfer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-2.5 font-mono text-xs text-gray-700">{{ $transfer->transfer_number }}</td>
                            <td class="px-5 py-2.5 text-gray-600 whitespace-nowrap">{{ $transfer->transfer_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-5 py-2.5 text-gray-700">{{ $transfer->fromAccount?->name ?? '-' }}</td>
                            <td class="px-5 py-2.5 text-gray-700">{{ $transfer->toAccount?->name ?? '-' }}</td>
                            <td class="px-5 py-2.5 font-semibold text-gray-900 text-right whitespace-nowrap">Rp {{ number_format($transfer->amount, 0, ',', '.') }}</td>
                            <td class="px-5 py-2.5 text-gray-600">{{ $transfer->requester?->name ?? '-' }}</td>
                            <td class="px-5 py-2.5 text-right whitespace-nowrap">
                                <a href="{{ route('admin.donation-transfers.show', $transfer) }}"
                                   class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">Periksa</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-sm text-gray-500">
                                Tidak ada mutasi dana yang menunggu verifikasi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Akses Cepat</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                <a href="{{ route('admin.finance.incomes.create') }}" class="flex items-center gap-3 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/></svg>
                    <span class="font-medium text-sm">Catat Pemasukan</span>
                </a>
                <a href="{{ route('admin.finance.expenses.create') }}" class="flex items-center gap-3 px-4 py-3 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                    <span class="font-medium text-sm">Catat Pengeluaran</span>
                </a>
                <a href="{{ route('admin.finance.report') }}" class="flex items-center gap-3 px-4 py-3 bg-emerald-50 text-emerald-700 rounded-lg hover:bg-emerald-100 transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="font-medium text-sm">Laporan Keuangan</span>
                </a>
            </div>
        </div>
    </div>
</x-admin-layout>
