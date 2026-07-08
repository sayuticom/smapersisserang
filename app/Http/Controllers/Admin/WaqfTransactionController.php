<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WaqfTransaction;
use Illuminate\Http\Request;

class WaqfTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = WaqfTransaction::latest();

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('month')) {
            $query->whereMonth('created_at', date('m', strtotime($request->month)))
                  ->whereYear('created_at', date('Y', strtotime($request->month)));
        }

        $totalAmount = (clone $query)->sum('total_transfer');
        $totalCount = (clone $query)->count();

        $transactions = $query->paginate(20);

        return view('admin.wakaf.transactions.index', compact('transactions', 'totalAmount', 'totalCount'));
    }

    public function createReceipt()
    {
        return view('admin.wakaf.transactions.create');
    }

    public function parseReceipt(Request $request)
    {
        $data = $request->validate([
            'wa_message' => 'required|string',
        ]);

        $waMessage = $data['wa_message'];

        $patterns = [
            'wakif_name' => '/Nama Wakif:\s*(.+)/',
            'ikrar_text' => '/Ikrar Wakaf:\s*(.+)/',
            'wakif_whatsapp' => '/Nomor WhatsApp:\s*(.+)/',
            'amount' => '/Nominal Wakaf:\s*Rp?\.?\s*([\d.]+)/',
            'admin_fee' => '/Biaya Admin:\s*Rp?\.?\s*([\d.]+)/',
            'unique_code' => '/Kode Unik:\s*(\d{1,3})/',
            'total_transfer' => '/Total Transfer:\s*Rp?\.?\s*([\d.]+)/',
            'transfer_date' => '/Tanggal Transfer:\s*(.+)/',
            'note' => '/Catatan:\s*(.+)/',
        ];

        $parsed = [];
        foreach ($patterns as $key => $pattern) {
            if (preg_match($pattern, $waMessage, $matches)) {
                $parsed[$key] = trim($matches[1]);
            } else {
                $parsed[$key] = '';
            }
        }

        $parsed['amount'] = $parsed['amount'] ? (int) str_replace('.', '', $parsed['amount']) : 0;
        $parsed['admin_fee'] = $parsed['admin_fee'] ? (int) str_replace('.', '', $parsed['admin_fee']) : 0;
        $parsed['total_transfer'] = $parsed['total_transfer'] ? (int) str_replace('.', '', $parsed['total_transfer']) : 0;
        $parsed['unique_code'] = $parsed['unique_code'] ?: 0;

        if ($parsed['total_transfer'] > 0 && $parsed['amount'] === 0) {
            $parsed['amount'] = $parsed['total_transfer'] - $parsed['unique_code'];
        }

        return back()->with('parsed', $parsed)->withInput();
    }

    public function storeReceipt(Request $request)
    {
        $data = $request->validate([
            'wakif_name' => 'nullable|string|max:100',
            'wakif_whatsapp' => 'nullable|string|max:20',
            'amount' => 'required|numeric|min:10000',
            'admin_fee' => 'nullable|numeric|min:0',
            'unique_code' => 'required|numeric|min:0|max:299',
            'total_transfer' => 'required|numeric|min:10000',
            'note' => 'nullable|string|max:500',
            'transfer_date' => 'nullable|string|max:20',
        ]);

        $amount = (int) $data['amount'];
        $adminFee = (int) ($data['admin_fee'] ?? 0);
        $uniqueCode = (int) $data['unique_code'];
        $totalTransfer = (int) $data['total_transfer'];

        if ($totalTransfer !== $amount + $adminFee + $uniqueCode) {
            return back()->withErrors(['total_transfer' => 'Total transfer harus sama dengan nominal + biaya admin + kode unik.'])->withInput();
        }

        $note = $data['note'] ?? '';
        if (!empty($data['transfer_date'])) {
            $note = 'Tanggal Transfer: ' . $data['transfer_date'] . "\n" . $note;
        }

        $transaction = WaqfTransaction::create([
            'order_id' => 'WF-' . now()->format('Ymd') . '-' . strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4)),
            'wakif_name' => $data['wakif_name'] ?? null,
            'wakif_whatsapp' => $data['wakif_whatsapp'] ?? null,
            'amount' => $amount,
            'admin_fee' => $adminFee,
            'unique_code' => $uniqueCode,
            'total_transfer' => $totalTransfer,
            'note' => $note,
            'ikrar_checked' => true,
            'payment_gateway' => 'manual-qris',
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return redirect()->route('admin.wakaf.transactions.show', $transaction)
            ->with('success', 'Bukti penerimaan wakaf berhasil dibuat.');
    }

    public function show(WaqfTransaction $transaction)
    {
        return view('admin.wakaf.transactions.show', compact('transaction'));
    }

    public function markPaid(WaqfTransaction $transaction)
    {
        $transaction->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Status wakaf berhasil diubah menjadi Lunas.');
    }

    public function markCancelled(WaqfTransaction $transaction)
    {
        $transaction->update(['status' => 'cancelled']);

        return back()->with('success', 'Status wakaf berhasil diubah menjadi Batal.');
    }
}
