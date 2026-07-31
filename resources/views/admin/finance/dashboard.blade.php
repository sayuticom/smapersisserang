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

        @if(auth()->user()->hasPermissionTo('donation.outflows.view') && $pendingDonationOutflowCount > 0)
            <div class="flex w-full min-w-0 flex-col gap-4 rounded-xl border border-amber-200 bg-amber-50 p-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <h3 class="font-semibold text-amber-900">Dana Donasi Menunggu Verifikasi</h3>
                    <p class="mt-1 text-sm text-amber-800">
                        {{ $pendingDonationOutflowCount }} transaksi · Rp {{ number_format($pendingDonationOutflowTotal, 0, ',', '.') }}
                    </p>
                    <p class="mt-1 text-xs text-amber-700">Dana belum dicatat sebagai Pemasukan sebelum disetujui.</p>
                    <p class="mt-1 text-xs font-medium text-amber-800">Buka transaksi untuk memeriksa dan menyetujui penyerahan dana.</p>
                </div>
                <a href="{{ route('admin.donation-outflows.index') }}" class="inline-flex w-full shrink-0 items-center justify-center rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-700 sm:w-auto">
                    Lihat Transaksi
                </a>
            </div>
        @endif

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
