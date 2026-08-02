<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationOutflow;
use App\Models\DonationTransaction;
use App\Services\DonationBalanceService;
use Illuminate\View\View;

class DonationDashboardController extends Controller
{
    public function dashboard(): View
    {
        $balance = app(DonationBalanceService::class)->summary();

        $pendingOutflows = DonationOutflow::query()
            ->with('creator')
            ->where('status', DonationOutflow::STATUS_PENDING)
            ->latest('handover_date')
            ->latest('created_at')
            ->limit(5)
            ->get();

        $latestIncome = DonationTransaction::query()
            ->whereIn('status', DonationBalanceService::INCOME_STATUSES)
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('admin.donation.dashboard', compact('balance', 'pendingOutflows', 'latestIncome'));
    }
}
