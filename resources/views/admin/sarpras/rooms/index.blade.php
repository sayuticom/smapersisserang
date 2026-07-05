<x-admin-layout>
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Data Ruangan</h2>
                <p class="text-gray-500 mt-1">Kelola data ruangan sekolah.</p>
            </div>
            <a href="{{ route('admin.sarpras.rooms.create') }}"
               class="inline-flex items-center justify-center rounded-lg bg-green-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-green-800">
                Tambah Ruangan
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" action="{{ route('admin.sarpras.rooms.index') }}"
              class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex flex-col gap-3 md:flex-row md:items-end">
                <div class="flex-1">
                    <label class="mb-1 block text-xs font-medium text-slate-600">Cari Ruangan</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nama ruangan..."
                           class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div class="w-full md:w-48">
                    <label class="mb-1 block text-xs font-medium text-slate-600">Tipe Ruangan</label>
                    <select name="room_type"
                            class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Semua Tipe</option>
                        @foreach(config('sarpras.room_types') as $type)
                            <option value="{{ $type }}" {{ request('room_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full md:w-44">
                    <label class="mb-1 block text-xs font-medium text-slate-600">Kondisi</label>
                    <select name="condition"
                            class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Semua Kondisi</option>
                        @foreach(config('sarpras.room_conditions') as $val => $label)
                            <option value="{{ $val }}" {{ request('condition') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                        class="h-11 rounded-xl bg-green-700 px-5 text-sm font-semibold text-white hover:bg-green-800">
                    Filter
                </button>
                @if(request('search') || request('room_type') || request('condition'))
                    <a href="{{ route('admin.sarpras.rooms.index') }}"
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
                        <th class="px-4 py-3 text-left font-semibold">Nama Ruangan</th>
                        <th class="px-4 py-3 text-left font-semibold">Tipe Ruangan</th>
                        <th class="px-4 py-3 text-left font-semibold">Kapasitas</th>
                        <th class="px-4 py-3 text-left font-semibold">Penanggung Jawab</th>
                        <th class="px-4 py-3 text-left font-semibold">Kondisi</th>
                        <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rooms as $room)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-4 font-semibold text-slate-900">{{ $room->name }}</td>
                            <td class="px-4 py-4 text-slate-700">{{ $room->room_type }}</td>
                            <td class="px-4 py-4 text-slate-700">{{ $room->capacity ? $room->capacity . ' orang' : '-' }}</td>
                            <td class="px-4 py-4 text-slate-700">{{ $room->person_in_charge ?? '-' }}</td>
                            <td class="px-4 py-4">
                                @php
                                    $conditionColors = [
                                        'baik' => 'bg-green-100 text-green-700',
                                        'rusak_ringan' => 'bg-yellow-100 text-yellow-700',
                                        'rusak_berat' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $conditionColors[$room->condition] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ config('sarpras.room_conditions')[$room->condition] ?? $room->condition }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.sarpras.rooms.edit', $room) }}"
                                       class="inline-flex items-center rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.sarpras.rooms.destroy', $room) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus ruangan ini?');">
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
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">
                                Belum ada data ruangan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($rooms->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">
                    {{ $rooms->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
