<x-admin-layout>
    <div class="max-w-6xl mx-auto">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Pengadaan Barang</h2>
                <p class="text-gray-500 mt-1">Daftar pengadaan barang.</p>
            </div>
            <a href="{{ route('admin.sarpras.procurements.create') }}"
               class="inline-flex items-center justify-center rounded-lg bg-green-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-green-800">
                Tambah Pengadaan
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" action="{{ route('admin.sarpras.procurements.index') }}"
              class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex flex-col gap-3 md:flex-row md:items-end">
                <div class="flex-1">
                    <label class="mb-1 block text-xs font-medium text-slate-600">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari judul pengadaan..."
                           class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div class="w-full md:w-48">
                    <label class="mb-1 block text-xs font-medium text-slate-600">Status</label>
                    <select name="status"
                            class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">Semua Status</option>
                        @foreach(config('sarpras.procurement_statuses') as $val => $label)
                            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                        class="h-11 rounded-xl bg-green-700 px-5 text-sm font-semibold text-white hover:bg-green-800">
                    Filter
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.sarpras.procurements.index') }}"
                       class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 px-5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Judul</th>
                        <th class="px-4 py-3 text-left font-semibold">Tgl Pengadaan</th>
                        <th class="px-4 py-3 text-left font-semibold">Sumber Dana</th>
                        <th class="px-4 py-3 text-right font-semibold">Total Biaya</th>
                        <th class="px-4 py-3 text-left font-semibold">Vendor</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($procurements as $procurement)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-4 font-semibold text-slate-900">{{ $procurement->title }}</td>
                            <td class="px-4 py-4 text-slate-600">{{ $procurement->procurement_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-4 py-4 text-slate-600">{{ $procurement->source_fund ?? '-' }}</td>
                            <td class="px-4 py-4 text-right font-semibold text-slate-900">
                                Rp{{ number_format($procurement->total_cost ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4 text-slate-600">{{ $procurement->vendor_name ?? '-' }}</td>
                            <td class="px-4 py-4">
                                @php
                                    $badgeColors = [
                                        'rencana' => 'bg-gray-100 text-gray-700',
                                        'proses' => 'bg-blue-100 text-blue-700',
                                        'selesai' => 'bg-green-100 text-green-700',
                                        'dibatalkan' => 'bg-red-100 text-red-700',
                                    ];
                                    $label = config('sarpras.procurement_statuses')[$procurement->status] ?? $procurement->status;
                                @endphp
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeColors[$procurement->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.sarpras.procurements.edit', $procurement) }}"
                                       class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.sarpras.procurements.destroy', $procurement) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus pengadaan ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition-colors">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-slate-500">
                                Belum ada data pengadaan barang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($procurements->hasPages())
                <div class="px-4 py-3 border-t border-slate-100">
                    {{ $procurements->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
