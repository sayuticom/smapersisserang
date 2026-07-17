<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roleNames = [
            'superadmin' => 'Superadmin',
            'admin' => 'Admin',
            'kepala_sekolah' => 'Kepala Sekolah',
            'guru' => 'Guru',
            'staf_tata_usaha' => 'Staf Tata Usaha',
            'staf_keuangan' => 'Staf Keuangan',
            'staf_kesiswaan' => 'Staf Kesiswaan',
            'staf_sarpras' => 'Staf Sarpras',
        ];

        $inserted = [];
        foreach ($roleNames as $name => $displayName) {
            $role = Role::firstOrCreate(
                ['name' => $name],
                ['display_name' => $displayName, 'guard_name' => 'web']
            );
            $inserted[$name] = $role->id;
        }

        User::query()
            ->whereNotNull('role')
            ->where('role', '!=', '')
            ->chunkById(100, function ($users) use ($inserted) {
                foreach ($users as $user) {
                    $roleName = $user->role;
                    if (isset($inserted[$roleName])) {
                        $user->roles()->syncWithoutDetaching([$inserted[$roleName]]);
                    }
                }
            });
    }
}
