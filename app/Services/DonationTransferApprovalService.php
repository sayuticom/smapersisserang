<?php

namespace App\Services;

use App\Models\DonationTransfer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DonationTransferApprovalService
{
    public function approve(DonationTransfer $transfer, User $approver): DonationTransfer
    {
        return DB::transaction(function () use ($transfer, $approver) {
            $locked = DonationTransfer::query()
                ->lockForUpdate()
                ->with('fromAccount')
                ->findOrFail($transfer->getKey());

            if ((int) $locked->requested_by === (int) $approver->id) {
                abort(403, 'Pembuat transaksi tidak dapat memverifikasi transaksi sendiri.');
            }

            if (! $locked->isPending()) {
                throw ValidationException::withMessages([
                    'status' => 'Transaksi ini sudah diproses dan tidak dapat disetujui kembali.',
                ]);
            }

            $from = $locked->fromAccount;
            $balanceService = app(DonationBalanceService::class);
            $available = $balanceService->availableBalanceForApproval((int) $from->id, (int) $locked->getKey());

            if ((float) $available < (float) $locked->amount) {
                throw ValidationException::withMessages([
                    'status' => 'Saldo akun '.$from->name
                        .' tidak mencukupi untuk menyetujui mutasi ini (saldo tersedia Rp'
                        .number_format($available, 0, ',', '.').').',
                ]);
            }

            $locked->update([
                'status' => DonationTransfer::STATUS_APPROVED,
                'approved_by' => $approver->id,
                'approved_at' => now(),
                'rejected_by' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
            ]);

            return $locked->fresh(['fromAccount', 'toAccount', 'approver', 'rejector']);
        });
    }
}
