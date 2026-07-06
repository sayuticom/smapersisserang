<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            SchoolSettingSeeder::class,
            SchoolValueSeeder::class,
            WebsitePageSeeder::class,
            NavigationMenuSeeder::class,
            OrganizationStructureSeeder::class,
            DonationEducationSettingSeeder::class,
            SchoolSubjectSeeder::class,
            AdmissionYearsTableSeeder::class,
            AdmissionProgramsTableSeeder::class,
            FaqSeeder::class,
            AiFaqSeeder::class,
            DonationShareTemplateSeeder::class,
        ]);

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ]
        );
    }
}
