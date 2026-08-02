<x-admin-layout>
    <div class="max-w-6xl mx-auto">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Donasi Masuk</h2>
                <p class="text-gray-500 mt-1">Daftar transaksi donasi pendidikan & makan santri.</p>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                @if(auth()->user()->hasPermissionTo('donation.balance.view'))
                    <a href="{{ route('admin.donation.dashboard') }}"
                       class="inline-flex items-center justify-center rounded-lg border border-emerald-200 px-4 py-2.5 text-sm font-semibold text-emerald-700 transition-colors hover:bg-emerald-50">
                        Lihat Dashboard Donasi
                    </a>
                @endif
                <a href="{{ route('admin.donasi-transactions.create-receipt') }}"
                   class="inline-flex items-center justify-center rounded-lg bg-green-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-green-800">
                    Buat Bukti Penerimaan
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.donasi-transactions.index') }}"
              class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid grid-cols-2 gap-3 lg:grid-cols-[minmax(260px,1fr)_auto_auto_auto] lg:items-end">
                <div class="col-span-1">
                    <label class="mb-1 block text-xs font-medium text-slate-600">Tanggal</label>
                    <input type="date" name="date" value="{{ request('date') }}"
                           class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <button type="submit" name="filter_type" value="date"
                        class="mt-5 h-11 rounded-xl bg-emerald-700 px-5 text-sm font-semibold text-white hover:bg-emerald-800 md:mt-0">
                    Filter Tanggal
                </button>
                <button type="submit" name="filter_type" value="month"
                        class="h-11 rounded-xl bg-emerald-700 px-5 text-sm font-semibold text-white hover:bg-emerald-800">
                    Filter Bulan
                </button>
                <a href="{{ route('admin.donasi-transactions.index') }}"
                   class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 px-5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Reset
                </a>
            </div>
        </form>

        <div class="mb-4 grid grid-cols-2 gap-3 md:gap-4">
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 md:p-5">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-emerald-700 md:text-xs">Total Donasi Masuk</p>
                <p class="mt-2 text-xl font-bold text-emerald-800 md:text-2xl">Rp{{ number_format($totalDonations, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-2xl border border-yellow-200 bg-yellow-50 p-4 md:p-5">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-yellow-700 md:text-xs">Jumlah Transaksi</p>
                <p class="mt-2 text-xl font-bold text-yellow-800 md:text-2xl">{{ $totalTransactions }} transaksi</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-4 hidden md:block overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Referensi</th>
                        <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                        <th class="px-4 py-3 text-left font-semibold">Donatur</th>
                        <th class="px-4 py-3 text-left font-semibold">Nominal</th>
                        <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transactions as $transaction)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-4 font-mono text-xs text-slate-600">
                                {{ $transaction->order_id ?? $transaction->reference ?? '-' }}
                            </td>
                            <td class="px-4 py-4 text-slate-700">
                                {{ optional($transaction->created_at)->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-semibold text-slate-900">
                                    {{ $transaction->donor_name ?? '-' }}
                                </div>
                                @if($transaction->donor_whatsapp)
                                    <div class="text-xs text-slate-500">
                                        {{ $transaction->donor_whatsapp }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4 font-semibold text-slate-900">
                                Rp{{ number_format($transaction->amount ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.donasi-transactions.show', $transaction) }}"
                                       title="Tampilkan Tanda Terima"
                                       class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-emerald-200 text-emerald-700 hover:bg-emerald-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15a3 3 0 100-6 3 3 0 000 6z" />
                                        </svg>
                                    </a>
                                    @if(auth()->user()->isSuperadmin())
                                        <form method="POST" action="{{ route('admin.donasi-transactions.destroy', $transaction) }}"
                                              onsubmit="return confirm('Yakin ingin menghapus Donasi Masuk ini? Data yang sudah dihapus tidak dapat dikembalikan.')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex h-9 items-center rounded-lg border border-red-200 px-3 text-xs font-semibold text-red-700 hover:bg-red-50">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">
                                Belum ada data donasi masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($transactions->hasPages())
                <div class="px-4 py-3 border-t border-slate-100">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>

        <div class="mt-4 block md:hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-xs">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-2 py-2 text-left font-semibold">Tgl</th>
                        <th class="px-2 py-2 text-left font-semibold">Donatur</th>
                        <th class="px-2 py-2 text-right font-semibold">Nominal</th>
                        <th class="px-2 py-2 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td class="px-2 py-3 whitespace-nowrap text-slate-600">
                                {{ optional($transaction->created_at)->format('d/m') }}
                            </td>
                            <td class="px-2 py-3">
                                <div class="font-semibold text-slate-900">
                                    {{ $transaction->donor_name ?? '-' }}
                                </div>
                            </td>
                            <td class="px-2 py-3 text-right font-semibold text-emerald-700 whitespace-nowrap">
                                Rp{{ number_format($transaction->amount ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-2 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.donasi-transactions.show', $transaction) }}"
                                       title="Tampilkan Tanda Terima"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-emerald-200 text-emerald-700 hover:bg-emerald-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15a3 3 0 100-6 3 3 0 000 6z" />
                                        </svg>
                                    </a>
                                    @if(auth()->user()->isSuperadmin())
                                        <form method="POST" action="{{ route('admin.donasi-transactions.destroy', $transaction) }}"
                                              onsubmit="return confirm('Yakin ingin menghapus Donasi Masuk ini? Data yang sudah dihapus tidak dapat dikembalikan.')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex h-8 items-center rounded-lg border border-red-200 px-2 text-xs font-semibold text-red-700 hover:bg-red-50">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-2 py-6 text-center text-xs text-slate-500">
                                Belum ada data donasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($transactions->hasPages())
                <div class="px-4 py-3 border-t border-slate-100">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
