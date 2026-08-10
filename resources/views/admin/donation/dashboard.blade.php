<x-admin-layout>
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Dashboard Donasi</h2>
                <p class="mt-1 text-gray-500">Ringkasan saldo donasi dan transaksi terbaru.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @php($rows = $balance['by_payment_method'] ?? [])
        @if(count($rows))
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Metode Pembayaran</th>
                                <th class="px-4 py-3 text-right font-semibold">Donasi Masuk</th>
                                <th class="px-4 py-3 text-right font-semibold">Donasi Keluar</th>
                                <th class="px-4 py-3 text-right font-semibold">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($rows as $row)
                                <tr class="{{ $row['key'] === 'unclassified' ? 'bg-amber-50/50' : 'hover:bg-slate-50' }}">
                                    <td class="px-4 py-3 text-slate-800">
                                        {{ $row['label'] }}
                                        @if($row['key'] === 'unclassified')
                                            <span class="ml-1 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700">Belum Ditentukan</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium text-slate-900">Rp{{ number_format($row['incoming'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-medium text-slate-900">Rp{{ number_format($row['approved_outflow'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-emerald-700">Rp{{ number_format($row['recorded_balance'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4 md:gap-4">
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 md:p-5">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-emerald-700 md:text-xs">Saldo Donasi</p>
                <p class="mt-2 text-xl font-bold text-emerald-800 md:text-2xl">Rp{{ number_format($balance['recorded_balance'], 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-emerald-600">Masuk valid - Keluar approved</p>
            </div>
            <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4 md:p-5">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-sky-700 md:text-xs">Donasi Masuk</p>
                <p class="mt-2 text-xl font-bold text-sky-800 md:text-2xl">Rp{{ number_format($balance['total_incoming'], 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-sky-600">Transaksi uang yang telah diterima</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 md:p-5">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-700 md:text-xs">Donasi Keluar</p>
                <p class="mt-2 text-xl font-bold text-slate-800 md:text-2xl">Rp{{ number_format($balance['total_approved_outflow'], 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-slate-600">Penyerahan dana disetujui</p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 md:p-5">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-amber-700 md:text-xs">Menunggu Verifikasi</p>
                <p class="mt-2 text-xl font-bold text-amber-800 md:text-2xl">Rp{{ number_format($balance['total_pending_outflow'], 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-amber-600">{{ $balance['pending_count'] }} transaksi pending</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h3 class="text-base font-bold text-gray-900">Donasi Keluar Menunggu Verifikasi</h3>
                <a href="{{ route('admin.donation-outflows.index') }}"
                   class="text-sm font-semibold text-emerald-700 hover:text-emerald-800">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Nomor</th>
                            <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                            <th class="px-4 py-3 text-left font-semibold">Sumber Donasi</th>
                            <th class="px-4 py-3 text-left font-semibold">Nominal</th>
                            <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pendingOutflows as $outflow)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $outflow->transaction_number }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $outflow->handover_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $outflow->donation_source }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">Rp{{ number_format($outflow->amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.donation-outflows.show', $outflow) }}"
                                       class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">
                                    Tidak ada donasi keluar yang menunggu verifikasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h3 class="text-base font-bold text-gray-900">Donasi Masuk Terbaru</h3>
                <a href="{{ route('admin.donasi-transactions.index') }}"
                   class="text-sm font-semibold text-emerald-700 hover:text-emerald-800">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
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
                        @forelse ($latestIncome as $transaction)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-mono text-xs text-slate-600">
                                    {{ $transaction->order_id ?? $transaction->reference ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ optional($transaction->created_at)->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3 font-semibold text-gray-900">
                                    {{ $transaction->donor_name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 font-semibold text-gray-900">
                                    Rp{{ number_format($transaction->amount ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.donasi-transactions.show', $transaction) }}"
                                       class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">
                                    Belum ada donasi masuk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
