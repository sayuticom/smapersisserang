<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationTransaction;
use App\Models\DonationTransfer;
use App\Services\DonationBalanceService;
use Illuminate\View\View;

class DonationDashboardController extends Controller
{
    public function dashboard(): View
    {
        $balance = app(DonationBalanceService::class)->summary();

        $pendingTransfers = DonationTransfer::query()
            ->with(['fromAccount', 'toAccount', 'requester'])
            ->where('status', DonationTransfer::STATUS_PENDING)
            ->latest('transfer_date')
            ->latest('created_at')
            ->limit(5)
            ->get();

        $pendingTransferCount = DonationTransfer::query()
            ->where('status', DonationTransfer::STATUS_PENDING)
            ->count();

        $pendingTransferTotal = (int) DonationTransfer::query()
            ->where('status', DonationTransfer::STATUS_PENDING)
            ->sum('amount');

        $outflowTotal = $balance['total_approved_outflow']
            + app(DonationBalanceService::class)->approvedDonationToFinanceTransferAmount();

        $latestIncome = DonationTransaction::query()
            ->whereIn('status', DonationBalanceService::INCOME_STATUSES)
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('admin.donation.dashboard', compact(
            'balance',
            'pendingTransfers',
            'pendingTransferCount',
            'pendingTransferTotal',
            'outflowTotal',
            'latestIncome'
        ));
    }
}
