<x-admin-layout>
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Kebutuhan Sarpras</h2>
                <p class="text-gray-500 mt-1">Daftar kebutuhan sarana dan prasarana.</p>
            </div>
            <a href="{{ route('admin.sarpras.needs.create') }}"
               class="inline-flex items-center justify-center rounded-lg bg-green-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-green-800">
                Tambah Kebutuhan
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" action="{{ route('admin.sarpras.needs.index') }}"
              class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex flex-col gap-3 md:flex-row md:items-end">
                <div class="flex-1">
                    <label class="mb-1 block text-xs font-medium text-slate-600">Cari Kebutuhan</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Judul kebutuhan..."
                           class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div class="w-full md:w-44">
                    <label class="mb-1 block text-xs font-medium text-slate-600">Prioritas</label>
                    <select name="priority"
                            class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Semua Prioritas</option>
                        @foreach(config('sarpras.need_priorities') as $val => $label)
                            <option value="{{ $val }}" {{ request('priority') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full md:w-44">
                    <label class="mb-1 block text-xs font-medium text-slate-600">Status</label>
                    <select name="status"
                            class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Semua Status</option>
                        @foreach(config('sarpras.need_statuses') as $val => $label)
                            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                        class="h-11 rounded-xl bg-green-700 px-5 text-sm font-semibold text-white hover:bg-green-800">
                    Filter
                </button>
                @if(request('search') || request('priority') || request('status'))
                    <a href="{{ route('admin.sarpras.needs.index') }}"
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
                        <th class="px-4 py-3 text-left font-semibold">Kategori</th>
                        <th class="px-4 py-3 text-left font-semibold">Jumlah</th>
                        <th class="px-4 py-3 text-left font-semibold">Estimasi Biaya</th>
                        <th class="px-4 py-3 text-left font-semibold">Prioritas</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($needs as $need)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-4 font-semibold text-slate-900">{{ $need->title }}</td>
                            <td class="px-4 py-4 text-slate-700">{{ $need->category ?? '-' }}</td>
                            <td class="px-4 py-4 text-slate-700">{{ $need->quantity_needed }} {{ $need->unit }}</td>
                            <td class="px-4 py-4 text-slate-700">Rp{{ number_format($need->estimated_cost ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-4">
                                @php
                                    $priorityColors = [
                                        'mendesak' => 'bg-red-100 text-red-700',
                                        'penting' => 'bg-orange-100 text-orange-700',
                                        'sedang' => 'bg-yellow-100 text-yellow-700',
                                        'rendah' => 'bg-slate-100 text-slate-700',
                                    ];
                                @endphp
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $priorityColors[$need->priority] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ config('sarpras.need_priorities')[$need->priority] ?? $need->priority }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                @php
                                    $statusColors = [
                                        'diajukan' => 'bg-gray-100 text-gray-700',
                                        'disetujui' => 'bg-blue-100 text-blue-700',
                                        'proses_pengadaan' => 'bg-purple-100 text-purple-700',
                                        'terpenuhi' => 'bg-green-100 text-green-700',
                                        'ditunda' => 'bg-amber-100 text-amber-700',
                                    ];
                                @endphp
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColors[$need->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ config('sarpras.need_statuses')[$need->status] ?? $need->status }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.sarpras.needs.edit', $need) }}"
                                       class="inline-flex items-center rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.sarpras.needs.destroy', $need) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus kebutuhan ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-slate-500">
                                Belum ada data kebutuhan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($needs->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">
                    {{ $needs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
