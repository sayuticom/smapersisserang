<?php

namespace App\Http\Controllers\Admin;

use App\Models\NavigationMenu;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class NavigationMenuController extends Controller
{
    public function index()
    {
        $menus = NavigationMenu::orderBy('sort_order')->orderBy('menu_key')->get();

        return view('admin.website.menus.index', compact('menus'));
    }

    public function edit(NavigationMenu $navigationMenu)
    {
        $parentOptions = NavigationMenu::whereNull('parent_key')
            ->where('menu_key', '!=', $navigationMenu->menu_key)
            ->orderBy('sort_order')
            ->get();

        return view('admin.website.menus.edit', compact('navigationMenu', 'parentOptions'));
    }

    public function update(Request $request, NavigationMenu $navigationMenu)
    {
        $data = $request->validate([
            'label' => 'required|string|max:100',
            'parent_key' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'location' => 'required|string|max:100',
            'is_active' => 'nullable|boolean',
            'is_external' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['is_external'] = $request->boolean('is_external');

        $navigationMenu->update($data);

        return redirect()->route('admin.website.menus.index')
            ->with('success', 'Menu navigasi berhasil diperbarui.');
    }

    public function toggle(NavigationMenu $navigationMenu)
    {
        $navigationMenu->update([
            'is_active' => !$navigationMenu->is_active,
        ]);

        return redirect()->route('admin.website.menus.index')
            ->with('success', 'Status menu berhasil diubah.');
    }
}
