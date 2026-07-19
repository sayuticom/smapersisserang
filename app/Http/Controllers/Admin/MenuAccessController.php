<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MenuAccessController extends Controller
{
    private array $lockedKeys = ['dashboard', 'account.profile'];

    public function index(): View
    {
        $roles = Role::active()->ordered()->get()->load('permissions');
        $menuItems = $this->buildMenuItems();

        return view('admin.menu-access.index', compact('roles', 'menuItems'));
    }

    public function update(Request $request): RedirectResponse
    {
        if ($request->has('reset')) {
            return $this->resetToDefaults();
        }

        $data = $request->validate([
            'items' => 'required|array',
            'items.*.key' => 'required|string',
            'items.*.mode' => 'required|string|in:baca_saja,akses_penuh,kosongkan',
            'items.*.roles' => 'array',
            'items.*.roles.*' => 'string',
        ]);

        $menuItemMap = collect($this->buildMenuItems())->keyBy('key');
        $allRoles = Role::active()->get();
        $validRoleNames = $allRoles->pluck('name')->toArray();
        $systemPermIds = Permission::where('is_system', true)->pluck('id')->toArray();
        $grantedBy = $request->user()->id;

        DB::transaction(function () use ($data, $menuItemMap, $allRoles, $validRoleNames, $systemPermIds, $grantedBy) {
            foreach ($data['items'] as $item) {
                $menuKey = $item['key'];
                $mode = $item['mode'];
                $checkedRoles = array_intersect($item['roles'] ?? [], $validRoleNames);

                if (in_array($menuKey, $this->lockedKeys, true)) {
                    continue;
                }

                $menuItem = $menuItemMap->get($menuKey);
                if (!$menuItem) {
                    continue;
                }

                $relatedPermNames = $menuItem['all_permissions'];
                if (empty($relatedPermNames)) {
                    continue;
                }

                $relatedPermIds = Permission::whereIn('name', $relatedPermNames)
                    ->pluck('id')
                    ->toArray();

                $relatedPermIds = array_values(array_diff($relatedPermIds, $systemPermIds));

                if (empty($relatedPermIds)) {
                    continue;
                }

                foreach ($allRoles as $role) {
                    if ($role->name === 'superadmin') {
                        continue;
                    }

                    $isChecked = in_array($role->name, $checkedRoles, true);

                    if ($mode === 'kosongkan' || !$isChecked) {
                        DB::table('permission_role')
                            ->where('role_id', $role->id)
                            ->whereIn('permission_id', $relatedPermIds)
                            ->delete();
                        continue;
                    }

                    $grantIds = $relatedPermIds;
                    if ($mode === 'baca_saja') {
                        $grantIds = Permission::whereIn('name', $relatedPermNames)
                            ->where('action', 'view')
                            ->pluck('id')
                            ->toArray();
                        $grantIds = array_values(array_diff($grantIds, $systemPermIds));
                        $revokeIds = array_diff($relatedPermIds, $grantIds);
                        DB::table('permission_role')
                            ->where('role_id', $role->id)
                            ->whereIn('permission_id', $revokeIds)
                            ->delete();
                    }

                    foreach ($grantIds as $permId) {
                        DB::table('permission_role')->updateOrInsert(
                            ['permission_id' => $permId, 'role_id' => $role->id],
                            ['granted_by' => $grantedBy, 'created_at' => now(), 'updated_at' => now()]
                        );
                    }
                }
            }
        });

        return redirect()->route('admin.menu-access.index')
            ->with('success', 'Pengaturan hak akses berhasil disimpan.');
    }

    private function resetToDefaults(): RedirectResponse
    {
        $manifest = config('permissions', []);
        $systemRoleNames = ['admin', 'kepala_sekolah', 'guru', 'staf_tata_usaha', 'staf_keuangan', 'staf_kesiswaan', 'staf_sarpras'];
        $systemRoleIds = Role::whereIn('name', $systemRoleNames)->pluck('id')->toArray();

        DB::transaction(function () use ($manifest, $systemRoleIds) {
            DB::table('permission_role')
                ->whereIn('role_id', $systemRoleIds)
                ->delete();

            foreach ($manifest as $permData) {
                if ($permData['is_system'] ?? false) {
                    continue;
                }

                $perm = Permission::where('name', $permData['name'])->first();
                if (!$perm) {
                    continue;
                }

                $defaultRoles = $permData['default_roles'] ?? [];
                $roleIds = Role::whereIn('name', $defaultRoles)->pluck('id')->toArray();

                foreach ($roleIds as $roleId) {
                    DB::table('permission_role')->insertOrIgnore([
                        'permission_id' => $perm->id,
                        'role_id' => $roleId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $adminRole = Role::where('name', 'admin')->first();
                if ($adminRole) {
                    DB::table('permission_role')->insertOrIgnore([
                        'permission_id' => $perm->id,
                        'role_id' => $adminRole->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });

        return redirect()->route('admin.menu-access.index')
            ->with('success', 'Semua pengaturan hak akses dikembalikan ke default.');
    }

    private function buildMenuItems(): array
    {
        $config = config('admin-menu');
        $manifest = config('permissions', []);

        $permIndex = [];
        foreach ($manifest as $perm) {
            $key = $perm['menu_key'] ?? null;
            if ($key) {
                $permIndex[$key][] = $perm['name'];
            }
        }

        $relatedIndex = [];
        foreach ($manifest as $perm) {
            $parts = explode('.', $perm['name']);
            if (count($parts) >= 2) {
                $base = $parts[0] . '.' . $parts[1];
                $relatedIndex[$base][] = $perm['name'];
            }
        }

        $items = [];
        $processItem = function (array $item, ?string $section) use ($permIndex, $relatedIndex) {
            $key = $item['key'] ?? null;
            if (!$key) {
                return null;
            }

            $primaryPerm = $item['permission'] ?? null;
            $allPerms = $permIndex[$key] ?? [];

            if ($primaryPerm) {
                $parts = explode('.', $primaryPerm);
                $base = $parts[0] . '.' . $parts[1];
                $allPerms = array_merge($allPerms, $relatedIndex[$base] ?? []);
            }

            $allPerms = array_values(array_unique($allPerms));

            return [
                'key' => $key,
                'label' => $item['label'],
                'section' => $section,
                'permission' => $primaryPerm,
                'all_permissions' => $allPerms,
                'is_locked' => in_array($key, $this->lockedKeys),
            ];
        };

        if (isset($config['dashboard'])) {
            $result = $processItem($config['dashboard'], null);
            if ($result) {
                $items[] = $result;
            }
        }

        foreach ($config['sections'] ?? [] as $section) {
            foreach ($section['items'] as $item) {
                if (!empty($item['children'])) {
                    foreach ($item['children'] as $child) {
                        $result = $processItem($child, $section['label']);
                        if ($result) {
                            $items[] = $result;
                        }
                    }
                } else {
                    $result = $processItem($item, $section['label']);
                    if ($result) {
                        $items[] = $result;
                    }
                }
            }
        }

        foreach ($config['account']['items'] ?? [] as $item) {
            $result = $processItem($item, $config['account']['label'] ?? 'AKUN');
            if ($result) {
                $items[] = $result;
            }
        }

        return $items;
    }
}
