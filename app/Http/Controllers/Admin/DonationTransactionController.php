<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationTransaction;
use Illuminate\Http\Request;

class DonationTransactionController extends Controller
{
    public function index()
    {
        $transactions = DonationTransaction::orderBy('created_at', 'desc')->paginate(20);

        $totalPaid = DonationTransaction::where('status', 'paid')->sum('amount');
        $totalPending = DonationTransaction::where('status', 'pending')->count();
        $totalCancelled = DonationTransaction::where('status', 'cancelled')->count();

        return view('admin.donasi-transactions.index', compact('transactions', 'totalPaid', 'totalPending', 'totalCancelled'));
    }

    public function markPaid(DonationTransaction $transaction)
    {
        $transaction->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Donasi ditandai sebagai Lunas.');
    }

    public function markCancelled(DonationTransaction $transaction)
    {
        $transaction->update(['status' => 'cancelled']);

        return back()->with('success', 'Donasi ditandai sebagai Dibatalkan.');
    }
}
