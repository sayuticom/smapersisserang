@php
    $superadminRole = $roles->firstWhere('name', 'superadmin');
    $displayRoles = $roles->reject(fn($r) => $r->name === 'superadmin')->values();
@endphp
<x-admin-layout>
    <x-slot:title>Pengaturan Hak Akses</x-slot:title>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Pengaturan Hak Akses</h1>
        <p class="text-sm text-slate-500 mt-1">Atur permission setiap role untuk setiap menu di sidebar admin.</p>
        <div class="mt-2 text-xs bg-blue-50 border border-blue-100 rounded-lg p-3 text-slate-600">
            <strong>Catatan:</strong> Perubahan di sini memengaruhi permission database secara langsung.
            Superadmin selalu memiliki akses penuh ke semua menu.
            Mode <strong>Baca Saja</strong> hanya memberikan hak lihat, <strong>Akses Penuh Modul</strong> memberikan semua hak.
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.menu-access.update') }}" id="hakAksesForm">
        @csrf
        @method('PUT')

        <div class="overflow-x-auto bg-white rounded-lg shadow-sm border border-slate-200">
            <table class="w-full text-sm min-w-[700px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left py-3 px-4 font-semibold text-slate-700 w-48">Menu</th>
                        <th class="text-left py-3 px-4 font-semibold text-slate-700 w-28">Section</th>
                        @foreach ($displayRoles as $role)
                            <th class="text-center py-3 px-2 font-semibold text-slate-700 whitespace-nowrap text-xs">
                                {{ $role->display_name ?? $role->name }}
                            </th>
                        @endforeach
                        <th class="text-center py-3 px-3 font-semibold text-slate-700 text-xs w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($menuItems as $index => $item)
                        @php
                            $perm = $item['permission'];
                            $isLocked = $item['is_locked'];
                        @endphp
                        <tr class="border-b border-slate-100 hover:bg-slate-50/50 {{ $isLocked ? 'opacity-60' : '' }}"
                            data-menu-key="{{ $item['key'] }}"
                            data-index="{{ $index }}">
                            <td class="py-2.5 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-slate-800 font-medium">{{ $item['label'] }}</span>
                                    @if ($isLocked)
                                        <span class="text-xs bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded">locked</span>
                                    @endif
                                </div>
                                <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $item['key'] }}</div>
                                @if ($perm)
                                    <div class="text-xs text-slate-400 mt-0.5">perm: {{ $perm }}</div>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-slate-500 text-xs align-top">
                                {{ $item['section'] ?? '—' }}
                            </td>
                            @foreach ($displayRoles as $role)
                                @php
                                    $isChecked = $isLocked
                                        ? ($perm ? $role->permissions->contains('name', $perm) : false)
                                        : ($perm ? $role->permissions->contains('name', $perm) : false);
                                    $isDisabled = $isLocked;
                                    $tooltip = '';
                                    if ($isLocked) {
                                        $tooltip = $perm ? 'Menu ini terkunci.' : 'Menu ini tidak memiliki permission.';
                                    }
                                @endphp
                                <td class="text-center py-2.5 px-2 align-middle">
                                    <div class="relative inline-flex items-center justify-center group">
                                        <input type="checkbox"
                                               {{ $isChecked ? 'checked' : '' }}
                                               {{ $isDisabled ? 'disabled' : '' }}
                                               name="items[{{ $index }}][roles][]"
                                               value="{{ $role->name }}"
                                               data-menu-index="{{ $index }}"
                                               class="menu-checkbox rounded border-slate-300 text-green-600 focus:ring-green-500 {{ $isDisabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer' }}">
                                        @if ($tooltip)
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover:block z-20 w-max max-w-48">
                                                <div class="bg-slate-800 text-white text-xs rounded px-2 py-1 shadow-lg text-center">
                                                    {{ $tooltip }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                            <td class="text-center py-2.5 px-3 align-middle">
                                @if ($isLocked || !$perm)
                                    <span class="text-xs text-slate-400">—</span>
                                @else
                                    <div class="flex items-center justify-center gap-1" data-action-group="{{ $index }}">
                                        <button type="button"
                                                onclick="setMode({{ $index }}, 'baca_saja')"
                                                class="mode-btn px-2 py-1 text-xs rounded border transition-colors
                                                       border-slate-200 text-slate-500 hover:bg-blue-50 hover:text-blue-700 hover:border-blue-300"
                                                data-mode="baca_saja"
                                                data-idx="{{ $index }}"
                                                title="Hanya berikan hak lihat">
                                            Baca
                                        </button>
                                        <button type="button"
                                                onclick="setMode({{ $index }}, 'akses_penuh')"
                                                class="mode-btn px-2 py-1 text-xs rounded border transition-colors
                                                       border-green-300 bg-green-50 text-green-700"
                                                data-mode="akses_penuh"
                                                data-idx="{{ $index }}"
                                                title="Berikan semua hak akses modul">
                                            Penuh
                                        </button>
                                        <button type="button"
                                                onclick="setMode({{ $index }}, 'kosongkan')"
                                                class="mode-btn px-2 py-1 text-xs rounded border transition-colors
                                                       border-slate-200 text-slate-500 hover:bg-red-50 hover:text-red-700 hover:border-red-300"
                                                data-mode="kosongkan"
                                                data-idx="{{ $index }}"
                                                title="Hapus semua hak akses modul ini">
                                            Kosong
                                        </button>
                                    </div>
                                @endif
                                <input type="hidden" name="items[{{ $index }}][mode]" value="akses_penuh" data-mode-field="{{ $index }}">
                            </td>
                            <input type="hidden" name="items[{{ $index }}][key]" value="{{ $item['key'] }}">
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex items-center justify-between flex-wrap gap-4">
            <div class="text-xs text-slate-400 space-y-1">
                <p>Menu <strong>locked</strong> tidak dapat diubah.</p>
                <p><strong>Baca</strong> = hak lihat saja. <strong>Penuh</strong> = semua hak modul. <strong>Kosong</strong> = hapus semua hak.</p>
                <p>Perubahan langsung memengaruhi permission database, bukan hanya sidebar.</p>
            </div>
            <div class="flex gap-3">
                <button type="submit" name="reset" value="1"
                        onclick="return confirm('Kembalikan semua pengaturan hak akses ke default?')"
                        class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition-colors">
                    Kembalikan ke Default
                </button>
                <button type="submit"
                        class="px-6 py-2.5 bg-green-700 text-white text-sm font-medium rounded-lg hover:bg-green-800 transition-colors">
                    Simpan Pengaturan
                </button>
            </div>
        </div>
    </form>
</x-admin-layout>

<script>
function setMode(index, mode) {
    const field = document.querySelector('[data-mode-field="' + index + '"]');
    if (field) field.value = mode;

    const group = document.querySelector('[data-action-group="' + index + '"]');
    if (group) {
        group.querySelectorAll('.mode-btn').forEach(btn => {
            const btnMode = btn.getAttribute('data-mode');
            btn.className = 'mode-btn px-2 py-1 text-xs rounded border transition-colors ';
            if (btnMode === mode) {
                if (mode === 'akses_penuh') {
                    btn.className += 'border-green-300 bg-green-50 text-green-700';
                } else if (mode === 'baca_saja') {
                    btn.className += 'border-blue-300 bg-blue-50 text-blue-700';
                } else {
                    btn.className += 'border-red-300 bg-red-50 text-red-700';
                }
            } else {
                btn.className += 'border-slate-200 text-slate-500 hover:bg-slate-50';
            }
        });
    }

    if (mode === 'kosongkan') {
        document.querySelectorAll('[data-menu-index="' + index + '"]').forEach(cb => {
            if (!cb.disabled) cb.checked = false;
        });
    } else if (mode === 'akses_penuh') {
        document.querySelectorAll('[data-menu-index="' + index + '"]').forEach(cb => {
            if (!cb.disabled) cb.checked = true;
        });
    }
}
</script>
