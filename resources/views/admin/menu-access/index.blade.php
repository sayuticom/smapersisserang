@php
    $menuService = app(\App\Services\AdminMenuService::class);
@endphp
<x-admin-layout>
    <x-slot:title>Pengaturan Menu Akses</x-slot:title>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Pengaturan Menu Akses</h1>
        <p class="text-sm text-slate-500 mt-1">Atur peran yang dapat mengakses setiap menu di sidebar admin.</p>
        <div class="mt-2 text-xs bg-blue-50 border border-blue-100 rounded-lg p-3 text-slate-600">
            <strong>Catatan:</strong> Pengaturan ini hanya mengontrol visibilitas menu di sidebar. Izin akses ke halaman
            tetap ditentukan oleh <code class="bg-slate-100 px-1 rounded">role:</code> middleware pada route.
            Superadmin selalu dapat melihat semua menu. Kosongkan semua checkbox untuk menggunakan pengaturan default.
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.menu-access.update') }}">
        @csrf
        @method('PUT')

        <div class="overflow-x-auto bg-white rounded-lg shadow-sm border border-slate-200">
            <table class="w-full text-sm min-w-[640px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left py-3 px-4 font-semibold text-slate-700">Menu</th>
                        <th class="text-left py-3 px-4 font-semibold text-slate-700">Section</th>
                        @foreach ($roleNames as $roleName)
                            <th class="text-center py-3 px-2 font-semibold text-slate-700 whitespace-nowrap text-xs">
                                {{ $roleName }}
                            </th>
                        @endforeach
                        <th class="text-center py-3 px-4 font-semibold text-slate-700">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($menuItems as $index => $item)
                        @php
                            $routeAllowed = $item['route_allowed_roles'];
                            $configRoles = $item['config_roles'];
                            $overrideRoles = $item['override_roles'];
                            $isLocked = $item['is_locked'];
                            $activeRoles = $overrideRoles ?? $configRoles;
                            $allRolesEmpty = empty($configRoles);
                        @endphp
                        <tr class="border-b border-slate-100 hover:bg-slate-50/50 {{ $isLocked ? 'opacity-60' : '' }}">
                            <td class="py-2.5 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-slate-800 font-medium">{{ $item['label'] }}</span>
                                    @if ($isLocked)
                                        <span class="text-xs bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded">locked</span>
                                    @endif
                                </div>
                                <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $item['key'] }}</div>
                            </td>
                            <td class="py-2.5 px-4 text-slate-500 text-xs align-top">
                                {{ $item['section'] ?? '—' }}
                                @if (!empty($routeAllowed) && !empty(array_diff($routeAllowed, $configRoles)))
                                    <div class="text-amber-500 mt-0.5">route: {{ implode(', ', $routeAllowed) }}</div>
                                @endif
                            </td>
                            @foreach ($roleNames as $roleName)
                                @php
                                    $isSuperadmin = $roleName === 'superadmin';
                                    $routeForbids = !empty($routeAllowed) && !in_array($roleName, $routeAllowed);
                                    $isChecked = $isSuperadmin || $allRolesEmpty || in_array($roleName, $activeRoles);
                                    $isDisabled = $isLocked || $isSuperadmin || $routeForbids;
                                    $tooltip = '';
                                    if ($isLocked) {
                                        $tooltip = 'Menu ini terkunci dan tidak dapat diubah.';
                                    } elseif ($isSuperadmin) {
                                        $tooltip = 'Superadmin selalu memiliki akses ke semua menu.';
                                    } elseif ($routeForbids) {
                                        $tooltip = 'Route tidak mengizinkan role ' . $roleName . ' untuk menu ini.';
                                    }
                                @endphp
                                <td class="text-center py-2.5 px-2 align-middle">
                                    <div class="relative inline-flex items-center justify-center group">
                                        <input type="checkbox"
                                               {{ $isChecked ? 'checked' : '' }}
                                               {{ $isDisabled ? 'disabled' : '' }}
                                               name="overrides[{{ $index }}][roles][]"
                                               value="{{ $roleName }}"
                                               class="rounded border-slate-300 text-green-600 focus:ring-green-500 {{ $isDisabled ? 'cursor-not-allowed opacity-50' : '' }}">
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
                            <td class="text-center py-2.5 px-4">
                                @if ($overrideRoles !== null)
                                    <span class="text-xs text-amber-600 font-medium">Diubah</span>
                                @else
                                    <span class="text-xs text-slate-400">Default</span>
                                @endif
                            </td>
                        </tr>
                        <input type="hidden" name="overrides[{{ $index }}][key]" value="{{ $item['key'] }}">
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex items-center justify-between flex-wrap gap-4">
            <div class="text-xs text-slate-400 space-y-1">
                <p>Menu <strong>locked</strong> tidak dapat diubah.</p>
                <p>Kosongkan semua checkbox untuk menggunakan pengaturan <strong>default</strong> dari konfigurasi.</p>
                <p>Checkbox <strong>superadmin</strong> dan role yang tidak diizinkan route tidak dapat diubah.</p>
            </div>
            <div class="flex gap-3">
                <button type="submit" name="reset" value="1"
                        onclick="return confirm('Kembalikan semua pengaturan akses menu ke default?')"
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
