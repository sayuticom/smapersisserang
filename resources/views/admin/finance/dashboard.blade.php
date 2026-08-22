<x-admin-layout>
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Dashboard Keuangan</h2>
            <p class="text-sm text-gray-500 mt-1">Ringkasan keuangan SMA Persis Serang</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="min-w-0 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Saldo Kas & Bank Keuangan</p>
                <p class="text-xl sm:text-2xl font-bold text-emerald-600 mt-2 break-words">Rp {{ number_format($balance, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-1">Total kas masuk - pengeluaran</p>
            </div>
            <div class="min-w-0 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Kas Masuk Bulan Ini</p>
                <p class="text-xl sm:text-2xl font-bold text-blue-600 mt-2 break-words">Rp {{ number_format($monthIncome, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-1">Eksternal: Rp {{ number_format($monthExternalIncome, 0, ',', '.') }} · Mutasi: Rp {{ number_format($monthInternalTransfer, 0, ',', '.') }}</p>
            </div>
            <div class="min-w-0 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengeluaran Bulan Ini</p>
                <p class="text-xl sm:text-2xl font-bold text-red-600 mt-2 break-words">Rp {{ number_format($monthExpense, 0, ',', '.') }}</p>
            </div>
            <div class="min-w-0 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Pendapatan Konsolidasi</p>
                <p class="text-xl sm:text-2xl font-bold text-purple-700 mt-2 break-words">Rp {{ number_format($reconciliation['organization_total_revenue'], 0, ',', '.') }}</p>
                <p class="text-xs text-purple-600 mt-1 font-medium">Non-Double Counting</p>
            </div>
        </div>

        {{-- KARTU REKONSILIASI DONASI <-> KEUANGAN --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3 flex-wrap bg-slate-50">
                <div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Rekonsiliasi Donasi &harr; Keuangan (Transfer Internal)
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">Memastikan mutasi dari donasi ke keuangan sinkron tanpa pencatatan ganda pada pendapatan organisasi</p>
                </div>
                <div>
                    @if($reconciliation['is_synchronized'])
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            SINKRON (Selisih Rp 0)
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-300">
                            <span class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></span>
                            BERMASALAH (Selisih Rp {{ number_format(abs($reconciliation['difference']), 0, ',', '.') }})
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-5 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 text-xs">
                <div class="bg-gray-50 p-3.5 rounded-lg border border-gray-100">
                    <span class="text-gray-500 font-medium block">Total Donasi Masuk</span>
                    <span class="text-sm font-bold text-gray-900 mt-1 block">Rp {{ number_format($reconciliation['total_incoming_donation'], 0, ',', '.') }}</span>
                </div>
                <div class="bg-gray-50 p-3.5 rounded-lg border border-gray-100">
                    <span class="text-gray-500 font-medium block">Saldo Donasi</span>
                    <span class="text-sm font-bold text-indigo-700 mt-1 block">Rp {{ number_format($reconciliation['donation_recorded_balance'], 0, ',', '.') }}</span>
                </div>
                <div class="bg-blue-50 p-3.5 rounded-lg border border-blue-100">
                    <span class="text-blue-700 font-medium block">Mutasi Approved</span>
                    <span class="text-sm font-bold text-blue-900 mt-1 block">Rp {{ number_format($reconciliation['approved_transfers_total'], 0, ',', '.') }}</span>
                </div>
                <div class="bg-blue-50 p-3.5 rounded-lg border border-blue-100">
                    <span class="text-blue-700 font-medium block">Penerimaan dari Donasi</span>
                    <span class="text-sm font-bold text-blue-900 mt-1 block">Rp {{ number_format($reconciliation['finance_donation_income_total'], 0, ',', '.') }}</span>
                </div>
                <div class="bg-emerald-50 p-3.5 rounded-lg border border-emerald-100">
                    <span class="text-emerald-700 font-medium block">Pemasukan Non-Donasi</span>
                    <span class="text-sm font-bold text-emerald-900 mt-1 block">Rp {{ number_format($reconciliation['finance_external_income_total'], 0, ',', '.') }}</span>
                </div>
                <div class="bg-purple-50 p-3.5 rounded-lg border border-purple-100">
                    <span class="text-purple-700 font-medium block">Pendapatan Organisasi</span>
                    <span class="text-sm font-bold text-purple-900 mt-1 block">Rp {{ number_format($reconciliation['organization_total_revenue'], 0, ',', '.') }}</span>
                </div>
            </div>

            @if($reconciliation['anomalies_count'] > 0)
                <div class="px-5 pb-5">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-xs text-red-800">
                        <p class="font-bold flex items-center gap-1.5 mb-2 text-red-900">
                            <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Ditemukan {{ $reconciliation['anomalies_count'] }} Anomali Transaksi Rekonsiliasi:
                        </p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($reconciliation['anomalies'] as $anomaly)
                                <li>{{ $anomaly['description'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
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
