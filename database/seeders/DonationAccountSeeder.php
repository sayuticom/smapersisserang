<?php

namespace Database\Seeders;

use App\Models\DonationAccount;
use Illuminate\Database\Seeder;

class DonationAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['name' => 'Tunai Donasi', 'category' => 'donation', 'description' => 'Kas tunai dari donasi.'],
            ['name' => 'QRIS Donasi', 'category' => 'donation', 'description' => 'Penerimaan donasi via QRIS.'],
            ['name' => 'Transfer Donasi', 'category' => 'donation', 'description' => 'Penerimaan donasi via transfer bank.'],
            ['name' => 'Tunai Keuangan', 'category' => 'finance', 'description' => 'Kas tunai bagian keuangan.'],
            ['name' => 'Rekening Keuangan', 'category' => 'finance', 'description' => 'Rekening bank bagian keuangan.'],
        ];

        foreach ($accounts as $account) {
            DonationAccount::updateOrCreate(
                ['name' => $account['name'], 'category' => $account['category']],
                ['description' => $account['description']]
            );
        }
    }
}
