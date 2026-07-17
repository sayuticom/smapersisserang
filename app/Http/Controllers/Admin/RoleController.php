<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuRoleOverride;
use App\Models\Role;
use App\Services\AdminMenuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct(
        protected AdminMenuService $menuService
    ) {}

    public function index(): View
    {
        $roles = Role::ordered()->withCount('users')->get();

        $routeRoles = $this->collectRouteRoles();

        $configRoles = $this->collectConfigRoles();

        return view('admin.roles.index', compact('roles', 'routeRoles', 'configRoles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:50|unique:roles,name|regex:/^[a-z]+(_[a-z]+)*$/',
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'display_name' => $data['display_name'],
            'description' => $data['description'] ?? null,
            'guard_name' => 'web',
            'sort_order' => Role::max('sort_order') + 1,
        ]);

        $this->menuService->flushCache();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$role->display_name}\" berhasil ditambahkan.");
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        if ($role->isSystem()) {
            $data = $request->validate([
                'display_name' => 'required|string|max:100',
                'description' => 'nullable|string|max:500',
            ]);

            $role->update([
                'display_name' => $data['display_name'],
                'description' => $data['description'] ?? null,
            ]);
        } else {
            $data = $request->validate([
                'display_name' => 'required|string|max:100',
                'description' => 'nullable|string|max:500',
                'is_active' => 'boolean',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            $updates = [
                'display_name' => $data['display_name'],
                'description' => $data['description'] ?? null,
            ];

            if (array_key_exists('is_active', $data)) {
                $updates['is_active'] = $data['is_active'];
            }
            if (array_key_exists('sort_order', $data) && $data['sort_order'] !== null) {
                $updates['sort_order'] = $data['sort_order'];
            }

            $role->update($updates);
        }

        $this->menuService->flushCache();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$role->display_name}\" berhasil diperbarui.");
    }

    public function toggleActive(Role $role): RedirectResponse
    {
        if ($role->isSystem()) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'System role tidak dapat dinonaktifkan.');
        }

        $role->update(['is_active' => !$role->is_active]);

        $this->menuService->flushCache();

        $status = $role->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$role->display_name}\" berhasil {$status}.");
    }

    public function destroy(Request $request, Role $role): RedirectResponse
    {
        if ($role->isSystem()) {
            return redirect()->route('admin.roles.index')
                ->with('error', "Role \"{$role->display_name}\" adalah system role dan tidak dapat dihapus.");
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', "Role \"{$role->display_name}\" masih dipakai oleh {$role->users()->count()} user. Alihkan user ke role lain terlebih dahulu.");
        }

        $usedInRoutes = $this->findRoleInRouteMiddleware($role->name);
        if (!empty($usedInRoutes)) {
            $routes = implode(', ', array_slice($usedInRoutes, 0, 5));
            if (count($usedInRoutes) > 5) {
                $routes .= ", dan " . (count($usedInRoutes) - 5) . " lainnya";
            }
            return redirect()->route('admin.roles.index')
                ->with('error', "Role \"{$role->display_name}\" masih digunakan pada middleware route: {$routes}. Hapus dari route terlebih dahulu.");
        }

        $usedInConfig = $this->findRoleInConfig($role->name);
        if (!empty($usedInConfig)) {
            $menus = implode(', ', array_slice($usedInConfig, 0, 5));
            if (count($usedInConfig) > 5) {
                $menus .= ", dan " . (count($usedInConfig) - 5) . " lainnya";
            }
            return redirect()->route('admin.roles.index')
                ->with('error', "Role \"{$role->display_name}\" masih tercantum pada konfigurasi menu: {$menus}. Hapus dari config/admin-menu.php terlebih dahulu.");
        }

        $referencedInOverrides = MenuRoleOverride::where('roles', 'like', '%"' . $role->name . '"%')->get();
        $overrideCount = $referencedInOverrides->count();

        DB::transaction(function () use ($role, $referencedInOverrides) {
            foreach ($referencedInOverrides as $override) {
                $filteredRoles = array_values(array_filter($override->roles, fn($r) => $r !== $role->name));
                if (empty($filteredRoles)) {
                    $override->delete();
                } else {
                    $override->update(['roles' => $filteredRoles]);
                }
            }

            $role->delete();
        });

        $this->menuService->flushCache();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$role->display_name}\" berhasil dihapus" . ($overrideCount > 0 ? ". {$overrideCount} pengaturan menu akses telah dibersihkan." : "."));
    }

    protected function collectRouteRoles(): array
    {
        $routeRoles = [];
        $routes = app('router')->getRoutes();
        foreach ($routes as $route) {
            $middleware = $route->gatherMiddleware();
            foreach ($middleware as $m) {
                if (is_string($m) && str_starts_with($m, 'role:')) {
                    $parsed = explode(',', substr($m, 5));
                    $name = $route->getName() ?? 'unnamed';
                    foreach ($parsed as $roleName) {
                        $roleName = trim($roleName);
                        if (!isset($routeRoles[$roleName])) {
                            $routeRoles[$roleName] = [];
                        }
                        $routeRoles[$roleName][] = $name;
                    }
                }
            }
        }
        return $routeRoles;
    }

    protected function collectConfigRoles(): array
    {
        $configRoles = [];
        $config = config('admin-menu');

        $walk = function (array $items, string $label = '') use (&$configRoles, &$walk) {
            foreach ($items as $item) {
                if (isset($item['roles'])) {
                    $itemLabel = ($label ? $label . ' > ' : '') . ($item['label'] ?? 'unnamed');
                    foreach ($item['roles'] as $roleName) {
                        if (!isset($configRoles[$roleName])) {
                            $configRoles[$roleName] = [];
                        }
                        $configRoles[$roleName][] = $itemLabel;
                    }
                }
                if (!empty($item['children'])) {
                    $childLabel = ($label ? $label . ' > ' : '') . ($item['label'] ?? 'unnamed');
                    $walk($item['children'], $childLabel);
                }
                if (!empty($item['items'])) {
                    $walk($item['items'], $item['label'] ?? 'unnamed');
                }
            }
        };

        if (isset($config['dashboard'])) {
            $walk([$config['dashboard']]);
        }
        if (isset($config['sections'])) {
            $walk($config['sections']);
        }
        if (isset($config['account'])) {
            $walk([$config['account']]);
        }

        return $configRoles;
    }

    protected function findRoleInRouteMiddleware(string $roleName): array
    {
        return $this->collectRouteRoles()[$roleName] ?? [];
    }

    protected function findRoleInConfig(string $roleName): array
    {
        return $this->collectConfigRoles()[$roleName] ?? [];
    }
}
