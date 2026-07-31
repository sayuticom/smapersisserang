<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceIncome extends Model
{
    protected $fillable = [
        'donation_outflow_id',
        'date',
        'income_type',
        'amount',
        'payment_method',
        'source_name',
        'description',
        'proof_file',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
            'amount' => 'decimal:2',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function donationOutflow(): BelongsTo
    {
        return $this->belongsTo(DonationOutflow::class);
    }

    public static function incomeTypes(): array
    {
        return [
            'Bantuan Sekolah',
            'Dana Operasional',
            'Kas Masuk Lain',
            'Pengembalian Belanja',
            'Pemasukan Usaha Sekolah',
            'Transfer dari Donasi',
        ];
    }

    public static function paymentMethods(): array
    {
        return [
            'Tunai',
            'Transfer Bank',
            'QRIS',
            'Lainnya',
        ];
    }
}
