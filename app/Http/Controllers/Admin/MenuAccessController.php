<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuRoleOverride;
use App\Models\Role;
use App\Services\AdminMenuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class MenuAccessController extends Controller
{
    public function __construct(
        protected AdminMenuService $menuService
    ) {}

    public function index(): View
    {
        $roles = Role::active()->ordered()->get();
        $menuItems = $this->menuService->getAllMenuItems();
        $roleNames = $roles->pluck('name')->toArray();
        $roleNames = array_unique(array_merge(['superadmin'], $roleNames));

        return view('admin.menu-access.index', compact('roles', 'menuItems', 'roleNames'));
    }

    public function update(Request $request): RedirectResponse
    {
        if ($request->has('reset')) {
            MenuRoleOverride::query()->delete();
            $this->menuService->flushCache();

            return redirect()->route('admin.menu-access.index')
                ->with('success', 'Semua pengaturan akses menu dikembalikan ke default.');
        }

        $data = $request->validate([
            'overrides' => 'array',
            'overrides.*.key' => 'required|string',
            'overrides.*.roles' => 'array',
            'overrides.*.roles.*' => 'string',
        ]);

        $validKeys = $this->menuService->getValidMenuKeys();
        $validRoles = $this->menuService->getValidRoleNames();
        $user = $request->user();

        DB::transaction(function () use ($data, $validKeys, $validRoles, $user) {
            $processedKeys = [];

            foreach ($data['overrides'] ?? [] as $override) {
                $menuKey = $override['key'];

                if (!in_array($menuKey, $validKeys, true)) {
                    continue;
                }

                if ($this->menuService->isLocked($menuKey)) {
                    continue;
                }

                $submittedRoles = array_values(array_intersect(
                    $override['roles'] ?? [],
                    $validRoles
                ));

                $submittedRoles = array_values(array_intersect(
                    $submittedRoles,
                    $this->menuService->getRouteAllowedRolesForItem($menuKey)
                ));

                if (!in_array('superadmin', $submittedRoles, true)) {
                    $submittedRoles[] = 'superadmin';
                }

                $submittedRoles = array_values(array_unique($submittedRoles));

                if (count($submittedRoles) <= 1 && in_array('superadmin', $submittedRoles, true)) {
                    $this->menuService->deleteOverride($menuKey);
                } elseif (empty($submittedRoles)) {
                    $this->menuService->deleteOverride($menuKey);
                } else {
                    $this->menuService->updateOverride($menuKey, $submittedRoles, $user);
                }

                $processedKeys[] = $menuKey;
            }

            MenuRoleOverride::whereNotIn('menu_key', $processedKeys)->delete();
        });

        $this->menuService->flushCache();

        return redirect()->route('admin.menu-access.index')
            ->with('success', 'Pengaturan akses menu berhasil disimpan.');
    }
}
