<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonationRegularDonor extends Model
{
    protected $fillable = [
        'name',
        'whatsapp_number',
        'is_active',
        'source',
        'first_donation_at',
        'last_donation_at',
        'total_donations_count',
        'total_donations_amount',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'first_donation_at' => 'datetime',
            'last_donation_at' => 'datetime',
            'total_donations_count' => 'integer',
            'total_donations_amount' => 'integer',
        ];
    }
}
