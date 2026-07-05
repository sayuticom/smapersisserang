<x-admin-layout>
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Data Aset</h2>
                <p class="text-gray-500 mt-1">Kelola inventaris aset sekolah.</p>
            </div>
            <a href="{{ route('admin.sarpras.assets.create') }}"
               class="inline-flex items-center justify-center rounded-lg bg-green-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-green-800">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Aset
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" action="{{ route('admin.sarpras.assets.index') }}"
              class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid grid-cols-2 gap-3 lg:grid-cols-[1fr_auto_auto_auto] lg:items-end">
                <div class="col-span-2 lg:col-span-1">
                    <label class="mb-1 block text-xs font-medium text-slate-600">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama atau kode inventaris..."
                           class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Kategori</label>
                    <select name="category" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Semua</option>
                        @foreach(config('sarpras.asset_categories') as $cat)
                            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Kondisi</label>
                    <select name="condition" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Semua</option>
                        @foreach(config('sarpras.asset_conditions') as $key => $label)
                            <option value="{{ $key }}" {{ request('condition') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="h-11 rounded-xl bg-green-700 px-5 text-sm font-semibold text-white hover:bg-green-800">
                        Filter
                    </button>
                    <a href="{{ route('admin.sarpras.assets.index') }}"
                       class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 px-5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Kode Inventaris</th>
                            <th class="px-4 py-3 text-left font-semibold">Nama Aset</th>
                            <th class="px-4 py-3 text-left font-semibold">Kategori</th>
                            <th class="px-4 py-3 text-right font-semibold">Jumlah</th>
                            <th class="px-4 py-3 text-left font-semibold">Lokasi</th>
                            <th class="px-4 py-3 text-left font-semibold">Kondisi</th>
                            <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($assets as $asset)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $asset->inventory_code ?? '-' }}</td>
                                <td class="px-4 py-3 font-medium text-slate-900">
                                    <a href="{{ route('admin.sarpras.assets.show', $asset) }}" class="hover:text-green-700">{{ $asset->name }}</a>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $asset->category ?? '-' }}</td>
                                <td class="px-4 py-3 text-right text-slate-900">{{ $asset->quantity }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $asset->location ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $conditionColors = ['baik' => 'bg-green-100 text-green-700', 'rusak_ringan' => 'bg-amber-100 text-amber-700', 'rusak_berat' => 'bg-red-100 text-red-700', 'hilang' => 'bg-gray-100 text-gray-700'];
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $conditionColors[$asset->condition] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ config('sarpras.asset_conditions')[$asset->condition] ?? $asset->condition }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.sarpras.assets.edit', $asset) }}"
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.sarpras.assets.destroy', $asset) }}"
                                              class="inline" onsubmit="return confirm('Yakin ingin menghapus aset ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-50 text-red-700 hover:bg-red-100">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-sm text-slate-500">
                                    Belum ada data aset.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($assets->hasPages())
                <div class="px-4 py-3 border-t border-slate-100">
                    {{ $assets->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
