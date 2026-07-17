@php
    $menuService = app(\App\Services\AdminMenuService::class);
@endphp
<x-admin-layout>
    <x-slot:title>Pengaturan Role</x-slot:title>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Pengaturan Role</h1>
        <p class="text-sm text-slate-500 mt-1">Kelola role / peran pengguna di sistem.</p>
        <div class="mt-2 text-xs bg-blue-50 border border-blue-100 rounded-lg p-3 text-slate-600">
            <strong>Catatan:</strong> Role baru tidak otomatis mendapat akses modul. Hak akses tetap mengikuti
            <code class="bg-slate-100 px-1 rounded">role:</code> middleware pada route. Role baru hanya dapat dicentang
            di Pengaturan Menu Akses jika route middleware sudah mengizinkannya.
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left py-3 px-4 font-semibold text-slate-700 w-1">No</th>
                        <th class="text-left py-3 px-4 font-semibold text-slate-700">Role</th>
                        <th class="text-left py-3 px-4 font-semibold text-slate-700">Nama Tampilan</th>
                        <th class="text-left py-3 px-4 font-semibold text-slate-700">Deskripsi</th>
                        <th class="text-center py-3 px-4 font-semibold text-slate-700">Status</th>
                        <th class="text-center py-3 px-4 font-semibold text-slate-700">User</th>
                        <th class="text-center py-3 px-4 font-semibold text-slate-700">Urutan</th>
                        <th class="text-center py-3 px-4 font-semibold text-slate-700">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $i => $role)
                        <tr class="border-b border-slate-100 hover:bg-slate-50/50 {{ !$role->is_active ? 'opacity-60' : '' }}">
                            <td class="py-2.5 px-4 text-slate-400 text-xs">{{ $i + 1 }}</td>
                            <td class="py-2.5 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-sm {{ $role->is_system ? 'text-slate-800' : 'text-slate-600' }}">{{ $role->name }}</span>
                                    @if ($role->is_system)
                                        <span class="text-xs bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded">system</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-2.5 px-4">
                                <div class="text-slate-800">{{ $role->display_name }}</div>
                            </td>
                            <td class="py-2.5 px-4 text-slate-500 text-xs max-w-xs">
                                {{ $role->description ?: '—' }}
                                @if (!empty($routeRoles[$role->name]) || !empty($configRoles[$role->name]))
                                    <div class="mt-1 space-y-0.5">
                                        @if (!empty($routeRoles[$role->name]))
                                            <div class="text-amber-500">route: {{ implode(', ', array_slice($routeRoles[$role->name], 0, 3)) }}{{ count($routeRoles[$role->name]) > 3 ? ', ...' : '' }}</div>
                                        @endif
                                        @if (!empty($configRoles[$role->name]))
                                            <div class="text-blue-500">menu: {{ implode(', ', array_slice($configRoles[$role->name], 0, 2)) }}{{ count($configRoles[$role->name]) > 2 ? ', ...' : '' }}</div>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="text-center py-2.5 px-4">
                                @if ($role->is_system)
                                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded font-medium">Sistem</span>
                                @elseif ($role->is_active)
                                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded font-medium">Aktif</span>
                                @else
                                    <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded font-medium">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center py-2.5 px-4 text-slate-500 text-xs">{{ $role->users_count }}</td>
                            <td class="text-center py-2.5 px-4 text-slate-500 text-xs">{{ $role->sort_order }}</td>
                            <td class="text-center py-2.5 px-4">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="#edit-{{ $role->id }}"
                                       onclick="event.preventDefault(); document.getElementById('edit-form-{{ $role->id }}').classList.toggle('hidden');"
                                       class="px-2.5 py-1 text-xs font-medium rounded bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                                        Edit
                                    </a>
                                    @if (!$role->is_system)
                                        <form action="{{ route('admin.roles.toggle-active', $role) }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    onclick="return confirm('{{ $role->is_active ? 'Nonaktifkan' : 'Aktifkan' }} role \"{{ $role->display_name }}\"?')"
                                                    class="px-2.5 py-1 text-xs font-medium rounded {{ $role->is_active ? 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }} transition-colors">
                                                {{ $role->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm('Hapus role \"{{ $role->display_name }}\"?')"
                                                    class="px-2.5 py-1 text-xs font-medium rounded bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                <div id="edit-form-{{ $role->id }}" class="hidden mt-2 border-t border-slate-100 pt-2">
                                    <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="space-y-2">
                                        @csrf @method('PUT')
                                        <input type="text" name="display_name" value="{{ $role->display_name }}"
                                               placeholder="Nama tampilan"
                                               class="w-full text-xs rounded border-slate-300 px-2 py-1" required>
                                        <input type="text" name="description" value="{{ $role->description ?? '' }}"
                                               placeholder="Deskripsi (opsional)"
                                               class="w-full text-xs rounded border-slate-300 px-2 py-1">
                                        @if (!$role->is_system)
                                            <div class="flex gap-1">
                                                <input type="number" name="sort_order" value="{{ $role->sort_order }}"
                                                       placeholder="Urutan"
                                                       class="w-16 text-xs rounded border-slate-300 px-2 py-1">
                                                <label class="flex items-center gap-1 text-xs text-slate-600">
                                                    <input type="checkbox" name="is_active" value="1" {{ $role->is_active ? 'checked' : '' }}
                                                           class="rounded border-slate-300">
                                                    Aktif
                                                </label>
                                            </div>
                                        @endif
                                        <button type="submit"
                                                class="w-full px-2 py-1 text-xs font-medium rounded bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors">
                                            Simpan
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
        <h3 class="text-lg font-semibold text-slate-800 mb-4">Tambah Role Baru</h3>
        <form method="POST" action="{{ route('admin.roles.store') }}" class="max-w-xl space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Key Role <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="contoh: wali_kelas"
                           class="w-full rounded-lg border-slate-300 focus:border-green-500 focus:ring-green-500 text-sm"
                           required pattern="^[a-z]+(_[a-z]+)*$"
                           title="snake_case, huruf kecil dan underscore saja">
                    <p class="text-xs text-slate-400 mt-1">Format snake_case. Tidak dapat diubah setelah dibuat.</p>
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Tampilan <span class="text-red-500">*</span></label>
                    <input type="text" name="display_name" value="{{ old('display_name') }}"
                           placeholder="contoh: Wali Kelas"
                           class="w-full rounded-lg border-slate-300 focus:border-green-500 focus:ring-green-500 text-sm" required>
                    @error('display_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                <input type="text" name="description" value="{{ old('description') }}"
                       placeholder="Opsional"
                       class="w-full rounded-lg border-slate-300 focus:border-green-500 focus:ring-green-500 text-sm">
                @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex justify-end">
                <button type="submit"
                        class="px-6 py-2.5 bg-green-700 text-white text-sm font-medium rounded-lg hover:bg-green-800 transition-colors">
                    Tambah Role
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
