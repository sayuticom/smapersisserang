<x-admin-layout>
    <div class="max-w-6xl mx-auto">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Perbaikan & Pemeliharaan</h2>
                <p class="text-gray-500 mt-1">Laporan kerusakan dan perbaikan.</p>
            </div>
            <a href="{{ route('admin.sarpras.maintenances.create') }}"
               class="inline-flex items-center justify-center rounded-lg bg-green-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-green-800">
                Tambah Laporan
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" action="{{ route('admin.sarpras.maintenances.index') }}"
              class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex flex-col gap-3 md:flex-row md:items-end">
                <div class="flex-1">
                    <label class="mb-1 block text-xs font-medium text-slate-600">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari judul laporan..."
                           class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div class="w-full md:w-48">
                    <label class="mb-1 block text-xs font-medium text-slate-600">Status</label>
                    <select name="status"
                            class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">Semua Status</option>
                        @foreach(config('sarpras.maintenance_statuses') as $val => $label)
                            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                        class="h-11 rounded-xl bg-green-700 px-5 text-sm font-semibold text-white hover:bg-green-800">
                    Filter
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.sarpras.maintenances.index') }}"
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
                        <th class="px-4 py-3 text-left font-semibold">Aset Terkait</th>
                        <th class="px-4 py-3 text-left font-semibold">Ruangan</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-right font-semibold">Biaya Aktual</th>
                        <th class="px-4 py-3 text-left font-semibold">Tgl Lapor</th>
                        <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($maintenances as $maintenance)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-4 font-semibold text-slate-900">{{ $maintenance->title }}</td>
                            <td class="px-4 py-4 text-slate-600">{{ $maintenance->asset?->name ?? '-' }}</td>
                            <td class="px-4 py-4 text-slate-600">{{ $maintenance->room?->name ?? '-' }}</td>
                            <td class="px-4 py-4">
                                @php
                                    $badgeColors = [
                                        'dilaporkan' => 'bg-gray-100 text-gray-700',
                                        'dicek' => 'bg-blue-100 text-blue-700',
                                        'proses_perbaikan' => 'bg-orange-100 text-orange-700',
                                        'selesai' => 'bg-green-100 text-green-700',
                                        'tidak_bisa_diperbaiki' => 'bg-red-100 text-red-700',
                                    ];
                                    $label = config('sarpras.maintenance_statuses')[$maintenance->status] ?? $maintenance->status;
                                @endphp
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeColors[$maintenance->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right font-semibold text-slate-900">
                                Rp{{ number_format($maintenance->actual_cost ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4 text-slate-600">{{ $maintenance->reported_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-4 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.sarpras.maintenances.edit', $maintenance) }}"
                                       class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.sarpras.maintenances.destroy', $maintenance) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus laporan ini?');">
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
                                Belum ada data perbaikan & pemeliharaan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($maintenances->hasPages())
                <div class="px-4 py-3 border-t border-slate-100">
                    {{ $maintenances->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
