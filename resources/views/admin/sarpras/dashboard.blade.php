<x-admin-layout>
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Dashboard Sarana Prasarana</h2>
                <p class="text-gray-500 mt-1">Ringkasan data sarana dan prasarana.</p>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 md:p-5">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-emerald-700 md:text-xs">Total Aset</p>
                <p class="mt-2 text-2xl font-bold text-emerald-800 md:text-3xl">{{ $totalAssets }}</p>
            </div>
            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 md:p-5">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-blue-700 md:text-xs">Total Ruangan</p>
                <p class="mt-2 text-2xl font-bold text-blue-800 md:text-3xl">{{ $totalRooms }}</p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 md:p-5">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-amber-700 md:text-xs">Kebutuhan Menunggu</p>
                <p class="mt-2 text-2xl font-bold text-amber-800 md:text-3xl">{{ $pendingNeeds }}</p>
            </div>
            <div class="rounded-2xl border border-orange-200 bg-orange-50 p-4 md:p-5">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-orange-700 md:text-xs">Perbaikan Aktif</p>
                <p class="mt-2 text-2xl font-bold text-orange-800 md:text-3xl">{{ $pendingMaintenances }}</p>
            </div>
            <div class="rounded-2xl border border-purple-200 bg-purple-50 p-4 md:p-5">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-purple-700 md:text-xs">Pengadaan Berjalan</p>
                <p class="mt-2 text-2xl font-bold text-purple-800 md:text-3xl">{{ $procurementsInProgress }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider mb-4">Aset per Kategori</h3>
                <div class="space-y-3">
                    @forelse($assetsByCategory as $item)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600">{{ $item->category ?? 'Tanpa Kategori' }}</span>
                            <span class="text-sm font-semibold text-slate-900">{{ $item->total }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">Belum ada data.</p>
                    @endforelse
                </div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider mb-4">Kondisi Aset</h3>
                <div class="space-y-3">
                    @forelse($assetsByCondition as $item)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600">
                                @php
                                    $conditionLabels = ['baik' => 'Baik', 'rusak_ringan' => 'Rusak Ringan', 'rusak_berat' => 'Rusak Berat', 'hilang' => 'Hilang'];
                                @endphp
                                {{ $conditionLabels[$item->condition] ?? $item->condition }}
                            </span>
                            <span class="text-sm font-semibold text-slate-900">{{ $item->total }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">Belum ada data.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-emerald-50">
                <h3 class="text-sm font-semibold text-emerald-800 uppercase tracking-wider">Perbaikan Terbaru</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Aset/Ruangan</th>
                            <th class="px-4 py-3 text-left font-semibold">Judul</th>
                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                            <th class="px-4 py-3 text-left font-semibold">Pelapor</th>
                            <th class="px-4 py-3 text-left font-semibold">Tgl</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentMaintenances as $m)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-slate-700">{{ $m->asset_name ?? $m->room_name ?? '-' }}</td>
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $m->title }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $m->status === 'diajukan' ? 'bg-amber-100 text-amber-700' : '' }}
                                        {{ $m->status === 'diproses' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $m->status === 'selesai' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $m->status === 'ditolak' ? 'bg-red-100 text-red-700' : '' }}">
                                        {{ ucfirst($m->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $m->reporter ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $m->created_at?->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada perbaikan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-emerald-50">
                <h3 class="text-sm font-semibold text-emerald-800 uppercase tracking-wider">Kebutuhan Terbaru</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Judul</th>
                            <th class="px-4 py-3 text-left font-semibold">Prioritas</th>
                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentNeeds as $need)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $need->title }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $need->priority === 'tinggi' ? 'bg-red-100 text-red-700' : '' }}
                                        {{ $need->priority === 'sedang' ? 'bg-amber-100 text-amber-700' : '' }}
                                        {{ $need->priority === 'rendah' ? 'bg-green-100 text-green-700' : '' }}">
                                        {{ ucfirst($need->priority) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $need->status === 'menunggu' ? 'bg-amber-100 text-amber-700' : '' }}
                                        {{ $need->status === 'disetujui' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $need->status === 'terealisasi' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $need->status === 'ditolak' ? 'bg-red-100 text-red-700' : '' }}">
                                        {{ ucfirst($need->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada kebutuhan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
