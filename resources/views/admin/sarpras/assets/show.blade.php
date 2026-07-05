<x-admin-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Detail Aset</h2>
                <p class="text-gray-500 mt-1">Informasi lengkap aset inventaris sekolah.</p>
            </div>
            <a href="{{ route('admin.sarpras.assets.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                &larr; Kembali
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                @if($asset->photo_path)
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <img src="{{ asset('storage/' . $asset->photo_path) }}"
                             alt="{{ $asset->name }}"
                             class="w-full rounded-xl object-cover">
                    </div>
                @else
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-8 shadow-sm flex items-center justify-center">
                        <div class="text-center">
                            <svg class="w-16 h-16 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm text-slate-400 mt-2">Tidak ada foto</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="lg:col-span-2">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Nama Aset</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ $asset->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Kode Inventaris</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1 font-mono">{{ $asset->inventory_code ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Kategori</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ $asset->category ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Jumlah</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ $asset->quantity }} {{ $asset->unit ?? 'unit' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Lokasi</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ $asset->location ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Kondisi</p>
                            <div class="mt-1">
                                @php
                                    $conditionColors = ['baik' => 'bg-green-100 text-green-700', 'rusak_ringan' => 'bg-amber-100 text-amber-700', 'rusak_berat' => 'bg-red-100 text-red-700', 'hilang' => 'bg-gray-100 text-gray-700'];
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $conditionColors[$asset->condition] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ config('sarpras.asset_conditions')[$asset->condition] ?? $asset->condition }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Tahun Pengadaan</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ $asset->procurement_year ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Sumber Dana</p>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ $asset->source_fund ?? '-' }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Deskripsi</p>
                            <p class="text-sm text-gray-700 mt-1 bg-slate-50 rounded-lg p-3">{{ $asset->description ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex items-center gap-3">
                    <a href="{{ route('admin.sarpras.assets.edit', $asset) }}"
                       class="inline-flex items-center justify-center rounded-lg bg-green-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800">
                        Edit Aset
                    </a>
                    <a href="{{ route('admin.sarpras.assets.index') }}"
                       class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-emerald-50">
                <h3 class="text-sm font-semibold text-emerald-800 uppercase tracking-wider">Riwayat Perbaikan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Judul</th>
                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                            <th class="px-4 py-3 text-right font-semibold">Biaya</th>
                            <th class="px-4 py-3 text-left font-semibold">Tgl Lapor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($asset->maintenances as $maintenance)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $maintenance->title }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $maintenance->status === 'diajukan' ? 'bg-amber-100 text-amber-700' : '' }}
                                        {{ $maintenance->status === 'diproses' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $maintenance->status === 'selesai' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $maintenance->status === 'ditolak' ? 'bg-red-100 text-red-700' : '' }}">
                                        {{ $maintenance->status_label ?? ucfirst($maintenance->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-900">
                                    {{ $maintenance->cost ? 'Rp' . number_format($maintenance->cost, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $maintenance->created_at?->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">
                                    Belum ada riwayat perbaikan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
