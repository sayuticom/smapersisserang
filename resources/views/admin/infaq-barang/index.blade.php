<x-admin-layout>
    <div class="mx-auto max-w-6xl">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Penerimaan Infaq Barang</h2>
                <p class="mt-1 text-gray-500">Bukti penerimaan yang diterbitkan dari Data WA Infaq Barang.</p>
            </div>
            <a href="{{ route('admin.infaq-barang-wa.index') }}"
               class="inline-flex items-center justify-center rounded-lg bg-green-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800">
                Buka Data WA Infaq Barang
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" action="{{ route('admin.infaq-barang.index') }}"
              class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid gap-3 md:grid-cols-[1fr_220px_auto_auto] md:items-end">
                <div>
                    <label for="search" class="mb-1 block text-xs font-medium text-slate-600">Cari</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                           placeholder="Nomor bukti, donatur, jenis/nama barang"
                           class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div>
                    <label for="date" class="mb-1 block text-xs font-medium text-slate-600">Tanggal Terima</label>
                    <input type="date" id="date" name="date" value="{{ request('date') }}"
                           class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <button type="submit"
                        class="h-11 rounded-xl bg-emerald-700 px-5 text-sm font-semibold text-white hover:bg-emerald-800">
                    Filter
                </button>
                <a href="{{ route('admin.infaq-barang.index') }}"
                   class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 px-5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Reset
                </a>
            </div>
        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Nomor Bukti</th>
                        <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                        <th class="px-4 py-3 text-left font-semibold">Donatur</th>
                        <th class="px-4 py-3 text-left font-semibold">Barang</th>
                        <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($receipts as $receipt)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-4 font-mono text-xs text-slate-600">{{ $receipt->receipt_number }}</td>
                            <td class="px-4 py-4 text-slate-700">{{ $receipt->received_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-4">
                                <div class="font-semibold text-slate-900">{{ $receipt->donorNameLabel() }}</div>
                                @if($receipt->donor_phone)
                                    <div class="text-xs text-slate-500">{{ $receipt->donor_phone }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-semibold text-slate-900">{{ $receipt->itemLabel() }}</div>
                                <div class="text-xs text-slate-500">{{ $receipt->quantityLabel() }}</div>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <a href="{{ route('admin.infaq-barang.show', $receipt) }}"
                                   class="inline-flex h-9 items-center justify-center rounded-lg border border-emerald-200 px-3 text-xs font-semibold text-emerald-700 hover:bg-emerald-50">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">
                                Belum ada penerimaan infaq barang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($receipts->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">
                    {{ $receipts->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
