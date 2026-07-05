<x-admin-layout>
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Laporan Sarpras</h2>
                <p class="text-gray-500 mt-1">Rekapitulasi data sarana dan prasarana.</p>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 md:p-5">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-emerald-700 md:text-xs">Total Aset</p>
                <p class="mt-2 text-2xl font-bold text-emerald-800 md:text-3xl">{{ $totalAssets }}</p>
            </div>
            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 md:p-5">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-blue-700 md:text-xs">Total Ruangan</p>
                <p class="mt-2 text-2xl font-bold text-blue-800 md:text-3xl">{{ $totalRooms }}</p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 md:p-5">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-amber-700 md:text-xs">Kebutuhan</p>
                <p class="mt-2 text-2xl font-bold text-amber-800 md:text-3xl">{{ $totalNeeds }}</p>
            </div>
            <div class="rounded-2xl border border-orange-200 bg-orange-50 p-4 md:p-5">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-orange-700 md:text-xs">Perbaikan</p>
                <p class="mt-2 text-2xl font-bold text-orange-800 md:text-3xl">{{ $totalMaintenances }}</p>
            </div>
            <div class="rounded-2xl border border-purple-200 bg-purple-50 p-4 md:p-5">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-purple-700 md:text-xs">Pengadaan</p>
                <p class="mt-2 text-2xl font-bold text-purple-800 md:text-3xl">{{ $totalProcurements }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <h3 class="text-sm font-semibold text-slate-800 mb-4">Aset per Kondisi</h3>
                <div class="space-y-2">
                    @forelse($assetsByCondition as $condition => $total)
                        <div class="flex items-center justify-between py-1.5">
                            <span class="text-sm text-slate-600">{{ config('sarpras.asset_conditions')[$condition] ?? $condition }}</span>
                            <span class="text-sm font-semibold text-slate-800">{{ $total }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">Belum ada data.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <h3 class="text-sm font-semibold text-slate-800 mb-4">Aset per Kategori</h3>
                <div class="space-y-2">
                    @forelse($assetsByCategory as $category => $total)
                        <div class="flex items-center justify-between py-1.5">
                            <span class="text-sm text-slate-600">{{ $category }}</span>
                            <span class="text-sm font-semibold text-slate-800">{{ $total }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">Belum ada data.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <h3 class="text-sm font-semibold text-slate-800 mb-4">Ruangan per Kondisi</h3>
                <div class="space-y-2">
                    @forelse($roomsByCondition as $condition => $total)
                        <div class="flex items-center justify-between py-1.5">
                            <span class="text-sm text-slate-600">{{ config('sarpras.room_conditions')[$condition] ?? $condition }}</span>
                            <span class="text-sm font-semibold text-slate-800">{{ $total }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">Belum ada data.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <h3 class="text-sm font-semibold text-slate-800 mb-4">Kebutuhan per Status</h3>
                <div class="space-y-2">
                    @forelse($needsByStatus as $status => $total)
                        <div class="flex items-center justify-between py-1.5">
                            <span class="text-sm text-slate-600">{{ config('sarpras.need_statuses')[$status] ?? $status }}</span>
                            <span class="text-sm font-semibold text-slate-800">{{ $total }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">Belum ada data.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <h3 class="text-sm font-semibold text-slate-800 mb-4">Perbaikan per Status</h3>
                <div class="space-y-2">
                    @forelse($maintenancesByStatus as $status => $total)
                        <div class="flex items-center justify-between py-1.5">
                            <span class="text-sm text-slate-600">{{ config('sarpras.maintenance_statuses')[$status] ?? $status }}</span>
                            <span class="text-sm font-semibold text-slate-800">{{ $total }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">Belum ada data.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <h3 class="text-sm font-semibold text-slate-800 mb-4">Pengadaan per Status</h3>
                <div class="space-y-2">
                    @forelse($procurementsByStatus as $status => $total)
                        <div class="flex items-center justify-between py-1.5">
                            <span class="text-sm text-slate-600">{{ config('sarpras.procurement_statuses')[$status] ?? $status }}</span>
                            <span class="text-sm font-semibold text-slate-800">{{ $total }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">Belum ada data.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <h3 class="text-sm font-semibold text-slate-800 mb-4">Pengadaan Terbaru</h3>
                <div class="space-y-2">
                    @forelse($recentProcurements as $p)
                        <div class="flex items-center justify-between py-1.5 border-b border-slate-100 last:border-0">
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ $p->title }}</p>
                                <p class="text-xs text-slate-400">{{ $p->procurement_date ? $p->procurement_date->format('d M Y') : '-' }}</p>
                            </div>
                            <span class="text-xs font-medium px-2 py-1 rounded-full
                                @if($p->status === 'selesai') bg-green-100 text-green-700
                                @elseif($p->status === 'proses') bg-blue-100 text-blue-700
                                @elseif($p->status === 'dibatalkan') bg-red-100 text-red-700
                                @else bg-slate-100 text-slate-600 @endif">
                                {{ config('sarpras.procurement_statuses')[$p->status] ?? $p->status }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">Belum ada pengadaan.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <h3 class="text-sm font-semibold text-slate-800 mb-4">Perbaikan Terbaru</h3>
                <div class="space-y-2">
                    @forelse($recentMaintenances as $m)
                        <div class="flex items-center justify-between py-1.5 border-b border-slate-100 last:border-0">
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ $m->title }}</p>
                                <p class="text-xs text-slate-400">{{ $m->asset?->name ?? '-' }}</p>
                            </div>
                            <span class="text-xs font-medium px-2 py-1 rounded-full
                                @if($m->status === 'selesai') bg-green-100 text-green-700
                                @elseif($m->status === 'dilaporkan') bg-yellow-100 text-yellow-700
                                @elseif($m->status === 'proses_perbaikan') bg-blue-100 text-blue-700
                                @elseif($m->status === 'tidak_bisa_diperbaiki') bg-red-100 text-red-700
                                @else bg-slate-100 text-slate-600 @endif">
                                {{ config('sarpras.maintenance_statuses')[$m->status] ?? $m->status }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">Belum ada perbaikan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>