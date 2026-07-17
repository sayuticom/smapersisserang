<?php

namespace App\Services;

use App\Models\MenuRoleOverride;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AdminMenuService
{
    protected ?Collection $overrides = null;

    protected bool $overridesFailed = false;

    protected array $routeRolesCache = [];

    protected array $lockedKeys = ['dashboard', 'account.profile', 'system.users'];

    protected array $validRoleNames = [
        'superadmin', 'admin', 'kepala_sekolah', 'guru',
        'staf_tata_usaha', 'staf_keuangan', 'staf_kesiswaan', 'staf_sarpras',
    ];

    public function getValidRoleNames(): array
    {
        return $this->validRoleNames;
    }

    public function getValidMenuKeys(): array
    {
        $cacheKey = 'admin_menu_valid_keys';
        return Cache::rememberForever($cacheKey, function () {
            $config = config('admin-menu');
            $keys = [];

            if (isset($config['key'])) {
                $keys[] = $config['key'];
            }

            if (isset($config['sections'])) {
                foreach ($config['sections'] as $section) {
                    foreach ($section['items'] as $item) {
                        if (!empty($item['children'])) {
                            foreach ($item['children'] as $child) {
                                if (isset($child['key'])) {
                                    $keys[] = $child['key'];
                                }
                            }
                        } elseif (isset($item['key'])) {
                            $keys[] = $item['key'];
                        }
                    }
                }
            }

            if (isset($config['account'])) {
                foreach ($config['account']['items'] as $item) {
                    if (isset($item['key'])) {
                        $keys[] = $item['key'];
                    }
                }
            }

            return $keys;
        });
    }

    public function flushCache(): void
    {
        $this->overrides = null;
        $this->routeRolesCache = [];
    }

    public function getOverrides(): Collection
    {
        if ($this->overrides === null && !$this->overridesFailed) {
            try {
                $this->overrides = MenuRoleOverride::all()->keyBy('menu_key');
            } catch (QueryException $e) {
                $this->overridesFailed = true;
                $this->overrides = new Collection();
            }
        }
        return $this->overrides ?? new Collection();
    }

    public function getOverrideFor(string $menuKey): ?MenuRoleOverride
    {
        return $this->getOverrides()->get($menuKey);
    }

    public function getRouteRoles(string $routeName): array
    {
        if (isset($this->routeRolesCache[$routeName])) {
            return $this->routeRolesCache[$routeName];
        }

        $route = app('router')->getRoutes()->getByName($routeName);
        if (!$route) {
            return $this->routeRolesCache[$routeName] = [];
        }

        $middleware = $route->gatherMiddleware();
        $roles = [];
        foreach ($middleware as $m) {
            if (is_string($m) && str_starts_with($m, 'role:')) {
                $parsed = explode(',', substr($m, 5));
                $roles = array_merge($roles, $parsed);
            }
        }

        return $this->routeRolesCache[$routeName] = array_values(array_unique($roles));
    }

    protected function userRoleNames(?User $user): array
    {
        if (!$user) return [];
        return $user->relationLoaded('roles')
            ? $user->roles->pluck('name')->toArray()
            : $user->roles()->pluck('name')->toArray();
    }

    public function isLocked(string $menuKey): bool
    {
        return in_array($menuKey, $this->lockedKeys, true);
    }

    public function getEffectiveRoles(array $item): array
    {
        $menuKey = $item['key'] ?? null;
        if (!$menuKey) {
            return [];
        }

        if ($this->isLocked($menuKey)) {
            return $item['roles'] ?? [];
        }

        $configRoles = $item['roles'] ?? [];

        $override = $this->getOverrideFor($menuKey);
        $overrideRoles = $override ? $override->roles : null;

        $candidateRoles = $overrideRoles ?? $configRoles;

        $route = $item['route'] ?? null;
        if ($route && $route !== '#') {
            $routeRoles = $this->getRouteRoles($route);
            if (!empty($routeRoles)) {
                $candidateRoles = array_values(array_intersect($candidateRoles, $routeRoles));
            }
        }

        return array_values($candidateRoles);
    }

    public function isVisibleToUser(array $item, ?User $user): bool
    {
        if (!empty($item['children'])) {
            foreach ($item['children'] as $child) {
                if ($this->isVisibleToUser($child, $user)) {
                    return true;
                }
            }
            return false;
        }

        if (!$user) {
            return false;
        }

        $userRoles = $this->userRoleNames($user);

        if (in_array('superadmin', $userRoles, true)) {
            return true;
        }

        $effectiveRoles = $this->getEffectiveRoles($item);

        if (empty($effectiveRoles)) {
            return true;
        }

        return !empty(array_intersect($effectiveRoles, $userRoles));
    }

    public function getSidebar(?User $user): array
    {
        $config = config('admin-menu');
        $result = [];

        if ($user && in_array('superadmin', $this->userRoleNames($user), true)) {
            return $config;
        }

        if (isset($config['dashboard'])) {
            $result['dashboard'] = $config['dashboard'];
        }

        if (isset($config['sections'])) {
            $visibleSections = [];
            foreach ($config['sections'] as $section) {
                $visibleItems = [];
                foreach ($section['items'] as $item) {
                    if ($this->isVisibleToUser($item, $user)) {
                        $visibleItems[] = $item;
                    }
                }
                if (!empty($visibleItems)) {
                    $visibleSections[] = [
                        'label' => $section['label'],
                        'items' => $visibleItems,
                    ];
                }
            }
            $result['sections'] = $visibleSections;
        }

        if (isset($config['account'])) {
            $visibleAccountItems = [];
            foreach ($config['account']['items'] as $item) {
                if ($this->isVisibleToUser($item, $user)) {
                    $visibleAccountItems[] = $item;
                }
            }
            if (!empty($visibleAccountItems)) {
                $result['account'] = [
                    'label' => $config['account']['label'],
                    'items' => $visibleAccountItems,
                ];
            }
        }

        return $result;
    }

    public function getAllMenuItems(): array
    {
        $config = config('admin-menu');
        $items = [];

        if (isset($config['key'])) {
            $items[] = $this->buildManagementItem($config);
        }

        if (isset($config['sections'])) {
            foreach ($config['sections'] as $section) {
                foreach ($section['items'] as $item) {
                    if (!empty($item['children'])) {
                        foreach ($item['children'] as $child) {
                            $items[] = $this->buildManagementItem($child, $section['label']);
                        }
                    } elseif (isset($item['key'])) {
                        $items[] = $this->buildManagementItem($item, $section['label']);
                    }
                }
            }
        }

        if (isset($config['account'])) {
            foreach ($config['account']['items'] as $item) {
                if (isset($item['key'])) {
                    $items[] = $this->buildManagementItem($item, $config['account']['label']);
                }
            }
        }

        return $items;
    }

    public function buildManagementItem(array $item, ?string $sectionLabel = null): array
    {
        $menuKey = $item['key'] ?? '';
        $override = $this->getOverrideFor($menuKey);
        $configRoles = $item['roles'] ?? [];

        return [
            'key' => $menuKey,
            'label' => $item['label'],
            'section' => $sectionLabel,
            'config_roles' => $configRoles,
            'override_roles' => $override ? $override->roles : null,
            'route_allowed_roles' => $this->getRouteRoles($item['route'] ?? ''),
            'is_locked' => $this->isLocked($menuKey),
        ];
    }

    public function updateOverride(string $menuKey, array $roles, ?User $user = null): MenuRoleOverride
    {
        $data = ['roles' => $roles];
        if ($user) {
            $data['updated_by'] = $user->id;
        }

        $existing = MenuRoleOverride::where('menu_key', $menuKey)->first();

        if ($existing) {
            $existing->update($data);
            return $existing->fresh();
        }

        if ($user) {
            $data['created_by'] = $user->id;
        }

        return MenuRoleOverride::create(array_merge(
            ['menu_key' => $menuKey],
            $data
        ));
    }

    public function deleteOverride(string $menuKey): bool
    {
        return MenuRoleOverride::where('menu_key', $menuKey)->delete() > 0;
    }

    public function getRouteAllowedRolesForItem(string $menuKey): array
    {
        $config = config('admin-menu');
        $items = [];

        if (isset($config['key']) && $config['key'] === $menuKey) {
            $items[] = $config;
        }

        if (isset($config['sections'])) {
            foreach ($config['sections'] as $section) {
                foreach ($section['items'] as $item) {
                    if (!empty($item['children'])) {
                        foreach ($item['children'] as $child) {
                            if (($child['key'] ?? null) === $menuKey) {
                                $items[] = $child;
                            }
                        }
                    } elseif (($item['key'] ?? null) === $menuKey) {
                        $items[] = $item;
                    }
                }
            }
        }

        if (isset($config['account'])) {
            foreach ($config['account']['items'] as $item) {
                if (($item['key'] ?? null) === $menuKey) {
                    $items[] = $item;
                }
            }
        }

        $routeRoles = [];
        foreach ($items as $item) {
            $route = $item['route'] ?? null;
            if ($route && $route !== '#') {
                $routeRoles = array_merge($routeRoles, $this->getRouteRoles($route));
            }
        }

        return array_values(array_unique($routeRoles));
    }
}
