<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonationAccount extends Model
{
    public const CATEGORY_DONATION = 'donation';

    public const CATEGORY_FINANCE = 'finance';

    protected $fillable = [
        'name',
        'category',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeDonation(Builder $query): Builder
    {
        return $query->where('category', self::CATEGORY_DONATION);
    }

    public function scopeFinance(Builder $query): Builder
    {
        return $query->where('category', self::CATEGORY_FINANCE);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function isDonation(): bool
    {
        return $this->category === self::CATEGORY_DONATION;
    }

    public function isFinance(): bool
    {
        return $this->category === self::CATEGORY_FINANCE;
    }

    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(DonationTransfer::class, 'from_account_id');
    }

    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(DonationTransfer::class, 'to_account_id');
    }

    public function getCategoryLabelAttribute(): string
    {
        return $this->category === self::CATEGORY_DONATION ? 'Donasi' : 'Keuangan';
    }
}
