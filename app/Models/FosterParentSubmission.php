<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FosterParentSubmission extends Model
{
    protected $fillable = [
        'foster_student_id',
        'donor_name',
        'donor_phone',
        'amount',
        'commitment_duration',
        'note',
        'qris_payload',
        'payment_status',
    ];

    public function fosterStudent()
    {
        return $this->belongsTo(FosterStudent::class);
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function getDonorNameAttribute($value): string
    {
        return $value ?: 'Hamba Allah';
    }

    public function getAmountFormattedAttribute(): string
    {
        return 'Rp' . number_format($this->amount, 0, ',', '.');
    }
}
