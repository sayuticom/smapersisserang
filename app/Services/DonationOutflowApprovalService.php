<?php

namespace App\Services;

use App\Models\DonationOutflow;
use App\Models\FinanceIncome;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DonationOutflowApprovalService
{
    public function approve(DonationOutflow $outflow, User $approver): DonationOutflow
    {
        return DB::transaction(function () use ($outflow, $approver) {
            $lockedOutflow = DonationOutflow::query()
                ->lockForUpdate()
                ->findOrFail($outflow->getKey());

            if ($lockedOutflow->status !== DonationOutflow::STATUS_PENDING) {
                throw ValidationException::withMessages([
                    'status' => 'Transaksi ini sudah diproses dan tidak dapat disetujui kembali.',
                ]);
            }

            if (FinanceIncome::where('donation_outflow_id', $lockedOutflow->id)->exists()) {
                throw ValidationException::withMessages([
                    'status' => 'Pemasukan untuk transaksi ini sudah tercatat.',
                ]);
            }

            FinanceIncome::create([
                'donation_outflow_id' => $lockedOutflow->id,
                'date' => $lockedOutflow->handover_date->format('Y-m-d'),
                'income_type' => 'Transfer dari Donasi',
                'amount' => $lockedOutflow->amount,
                'payment_method' => $lockedOutflow->handover_method === 'cash'
                    ? 'Tunai'
                    : 'Transfer Bank',
                'source_name' => $lockedOutflow->donation_source,
                'description' => $this->buildDescription($lockedOutflow),
                'proof_file' => $lockedOutflow->proof_file,
                'created_by' => $approver->id,
            ]);

            $lockedOutflow->update([
                'status' => DonationOutflow::STATUS_APPROVED,
                'approved_by' => $approver->id,
                'approved_at' => now(),
                'rejected_by' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
            ]);

            $lockedOutflow->statusHistories()->create([
                'from_status' => DonationOutflow::STATUS_PENDING,
                'to_status' => DonationOutflow::STATUS_APPROVED,
                'changed_by' => $approver->id,
            ]);

            return $lockedOutflow->fresh(['financeIncome', 'statusHistories']);
        });
    }

    private function buildDescription(DonationOutflow $outflow): string
    {
        $lines = [
            'Donasi Keluar: '.$outflow->transaction_number,
            'Keterangan/Periode: '.($outflow->description ?: '-'),
            'Kas/Rekening Tujuan: '.$outflow->destination_account,
        ];

        if ($outflow->notes) {
            $lines[] = 'Catatan: '.$outflow->notes;
        }

        return implode("\n", $lines);
    }
}
