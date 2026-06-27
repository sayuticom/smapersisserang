<?php

namespace Database\Seeders;

use App\Models\NavigationMenu;
use Illuminate\Database\Seeder;

class NavigationMenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            [
                'menu_key' => 'home',
                'label' => 'Beranda',
                'route_name' => null,
                'url' => '/',
                'parent_key' => null,
                'sort_order' => 1,
                'location' => 'public_header',
                'is_active' => true,
            ],
            [
                'menu_key' => 'profile',
                'label' => 'Profil',
                'route_name' => 'public.profile',
                'url' => null,
                'parent_key' => null,
                'sort_order' => 2,
                'location' => 'public_header',
                'is_active' => true,
            ],
            [
                'menu_key' => 'program',
                'label' => 'Program',
                'route_name' => 'public.program',
                'url' => null,
                'parent_key' => null,
                'sort_order' => 3,
                'location' => 'public_header',
                'is_active' => true,
            ],
            [
                'menu_key' => 'boarding',
                'label' => 'Boarding',
                'route_name' => 'public.boarding',
                'url' => null,
                'parent_key' => null,
                'sort_order' => 4,
                'location' => 'public_header',
                'is_active' => true,
            ],
            [
                'menu_key' => 'gallery',
                'label' => 'Galeri',
                'route_name' => 'public.gallery',
                'url' => null,
                'parent_key' => null,
                'sort_order' => 5,
                'location' => 'public_header',
                'is_active' => true,
            ],
            [
                'menu_key' => 'figures',
                'label' => 'Tokoh',
                'route_name' => 'public.figures',
                'url' => null,
                'parent_key' => null,
                'sort_order' => 6,
                'location' => 'public_header',
                'is_active' => true,
            ],
            [
                'menu_key' => 'teachers',
                'label' => 'Guru',
                'route_name' => 'public.teachers',
                'url' => null,
                'parent_key' => null,
                'sort_order' => 7,
                'location' => 'public_header',
                'is_active' => true,
            ],
            [
                'menu_key' => 'ppdb',
                'label' => 'SPMB',
                'route_name' => 'ppdb.info',
                'url' => null,
                'parent_key' => null,
                'sort_order' => 8,
                'location' => 'public_header',
                'is_active' => true,
            ],
            [
                'menu_key' => 'ppdb_info',
                'label' => 'Informasi SPMB',
                'route_name' => 'ppdb.info',
                'url' => null,
                'parent_key' => 'ppdb',
                'sort_order' => 1,
                'location' => 'public_header',
                'is_active' => true,
            ],
            [
                'menu_key' => 'ppdb_register',
                'label' => 'Daftar SPMB',
                'route_name' => 'ppdb.create',
                'url' => null,
                'parent_key' => 'ppdb',
                'sort_order' => 2,
                'location' => 'public_header',
                'is_active' => true,
            ],
            [
                'menu_key' => 'ppdb_status',
                'label' => 'Cek Status',
                'route_name' => 'ppdb.status.form',
                'url' => null,
                'parent_key' => 'ppdb',
                'sort_order' => 3,
                'location' => 'public_header',
                'is_active' => true,
            ],
            [
                'menu_key' => 'contact',
                'label' => 'Kontak',
                'route_name' => null,
                'url' => '/#kontak',
                'parent_key' => null,
                'sort_order' => 9,
                'location' => 'public_header',
                'is_active' => true,
            ],
        ];

        foreach ($menus as $menu) {
            NavigationMenu::updateOrCreate(
                ['menu_key' => $menu['menu_key']],
                $menu
            );
        }
    }
}
