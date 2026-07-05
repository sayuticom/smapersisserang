<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonationTransaction extends Model
{
    protected $fillable = [
        'order_id',
        'donor_name',
        'donor_whatsapp',
        'support_type',
        'amount',
        'note',
        'payment_gateway',
        'snap_token',
        'snap_redirect_url',
        'midtrans_transaction_id',
        'midtrans_payment_type',
        'midtrans_fraud_status',
        'status',
        'paid_at',
        'raw_notification',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'paid_at' => 'datetime',
            'raw_notification' => 'array',
        ];
    }

    public function isPaid(): bool
    {
        return in_array($this->status, ['paid', 'settlement', 'capture']);
    }
}
