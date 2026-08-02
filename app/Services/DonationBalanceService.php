<?php

namespace App\Services;

use App\Models\DonationOutflow;
use App\Models\DonationTransaction;

class DonationBalanceService
{
    /**
     * Status donation_transactions yang dianggap sebagai dana masuk yang valid/lunas.
     *
     * Midtrans menggunakan 'settlement' (dan kadang 'capture') untuk pembayaran
     * berhasil, sedangkan penerimaan manual memakai 'paid'. Status lain seperti
     * pending/cancelled/expired/failed tidak dihitung.
     */
    public const INCOME_STATUSES = ['paid', 'settlement', 'capture'];

    public function totalIncoming(?string $from = null, ?string $to = null): int
    {
        return (int) DonationTransaction::query()
            ->whereIn('status', self::INCOME_STATUSES)
            ->when($from, fn ($query) => $query->whereDate('created_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('created_at', '<=', $to))
            ->sum('amount');
    }

    public function totalApprovedOutflow(): int
    {
        return (int) DonationOutflow::query()
            ->where('status', DonationOutflow::STATUS_APPROVED)
            ->sum('amount');
    }

    public function totalPendingOutflow(): int
    {
        return (int) DonationOutflow::query()
            ->where('status', DonationOutflow::STATUS_PENDING)
            ->sum('amount');
    }

    public function pendingCount(): int
    {
        return DonationOutflow::query()
            ->where('status', DonationOutflow::STATUS_PENDING)
            ->count();
    }

    public function recordedBalance(): int
    {
        return $this->totalIncoming() - $this->totalApprovedOutflow();
    }

    public function availableBalance(): int
    {
        return $this->recordedBalance() - $this->totalPendingOutflow();
    }

    /**
     * Data ringkasan untuk kartu saldo pada halaman Donasi.
     */
    public function summary(?string $from = null, ?string $to = null): array
    {
        return [
            'recorded_balance' => $this->recordedBalance(),
            'available_balance' => $this->availableBalance(),
            'total_incoming' => $this->totalIncoming($from, $to),
            'total_approved_outflow' => $this->totalApprovedOutflow(),
            'total_pending_outflow' => $this->totalPendingOutflow(),
            'pending_count' => $this->pendingCount(),
        ];
    }
}
