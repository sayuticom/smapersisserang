<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationOutflow;
use App\Services\DonationOutflowApprovalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DonationOutflowController extends Controller
{
    public function index(): View
    {
        $outflows = DonationOutflow::with('creator')
            ->latest('handover_date')
            ->latest('created_at')
            ->paginate(20);

        return view('admin.donation-outflows.index', compact('outflows'));
    }

    public function create(): View
    {
        return view('admin.donation-outflows.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'handover_date' => ['required', 'date'],
            'donation_source' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'handover_method' => ['required', 'in:cash,transfer'],
            'destination_account' => ['required', 'string', 'max:255'],
            'proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('proof_file')) {
            $validated['proof_file'] = $request->file('proof_file')
                ->store('donation/outflow-proofs', 'public');
        }

        $outflow = DB::transaction(function () use ($validated) {
            $outflow = DonationOutflow::create(array_merge($validated, [
                'transaction_number' => $this->generateTransactionNumber(),
                'status' => DonationOutflow::STATUS_PENDING,
                'created_by' => auth()->id(),
            ]));

            $outflow->statusHistories()->create([
                'from_status' => null,
                'to_status' => DonationOutflow::STATUS_PENDING,
                'changed_by' => auth()->id(),
            ]);

            return $outflow;
        });

        return redirect()->route('admin.donation-outflows.show', $outflow)
            ->with('success', 'Donasi Keluar berhasil dibuat dan menunggu verifikasi Keuangan.');
    }

    public function show(DonationOutflow $donationOutflow): View
    {
        $donationOutflow->load([
            'creator',
            'approver',
            'rejector',
            'financeIncome',
            'statusHistories.changedBy',
        ]);

        return view('admin.donation-outflows.show', compact('donationOutflow'));
    }

    public function approve(
        DonationOutflow $donationOutflow,
        DonationOutflowApprovalService $approvalService
    ): RedirectResponse {
        $approvalService->approve($donationOutflow, auth()->user());

        return redirect()->route('admin.donation-outflows.show', $donationOutflow)
            ->with('success', 'Donasi Keluar disetujui dan Pemasukan Keuangan berhasil dibuat.');
    }

    public function reject(Request $request, DonationOutflow $donationOutflow): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($donationOutflow, $validated) {
            $lockedOutflow = DonationOutflow::query()
                ->lockForUpdate()
                ->findOrFail($donationOutflow->getKey());

            if ($lockedOutflow->status !== DonationOutflow::STATUS_PENDING) {
                throw ValidationException::withMessages([
                    'status' => 'Transaksi ini sudah diproses dan tidak dapat ditolak.',
                ]);
            }

            $lockedOutflow->update([
                'status' => DonationOutflow::STATUS_REJECTED,
                'rejected_by' => auth()->id(),
                'rejected_at' => now(),
                'rejection_reason' => $validated['rejection_reason'],
                'approved_by' => null,
                'approved_at' => null,
            ]);

            $lockedOutflow->statusHistories()->create([
                'from_status' => DonationOutflow::STATUS_PENDING,
                'to_status' => DonationOutflow::STATUS_REJECTED,
                'reason' => $validated['rejection_reason'],
                'changed_by' => auth()->id(),
            ]);
        });

        return redirect()->route('admin.donation-outflows.show', $donationOutflow)
            ->with('success', 'Donasi Keluar ditolak.');
    }

    private function generateTransactionNumber(): string
    {
        do {
            $number = 'DK-'.now()->format('Ymd').'-'.Str::upper(Str::random(4));
        } while (DonationOutflow::where('transaction_number', $number)->exists());

        return $number;
    }
}
