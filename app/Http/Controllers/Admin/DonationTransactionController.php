<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationRegularDonor;
use App\Models\DonationTransaction;
use App\Services\DonationBalanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DonationTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = DonationTransaction::query();

        if ($request->filled('date')) {
            $date = \Carbon\Carbon::parse($request->date);

            if ($request->filter_type === 'month') {
                $query->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month);
            } else {
                $query->whereDate('created_at', $date->toDateString());
            }
        }

        $summaryQuery = clone $query;

        $transactions = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $totalDonations = (clone $summaryQuery)->sum('amount');
        $totalTransactions = (clone $summaryQuery)->count();

        $balance = auth()->user()->hasPermissionTo('donation.balance.view')
            ? app(DonationBalanceService::class)->summary()
            : [];

        return view('admin.donasi-transactions.index', compact('transactions', 'totalDonations', 'totalTransactions', 'balance'));
    }

    public function createReceipt()
    {
        return view('admin.donasi-transactions.create', [
            'parsed' => session('parsed_donation_confirmation', []),
            'rawMessage' => session('raw_donation_confirmation', ''),
        ]);
    }

    public function parseReceipt(Request $request)
    {
        $data = $request->validate([
            'confirmation_message' => ['required', 'string', 'max:5000'],
        ]);

        $parsed = $this->parseConfirmationMessage($data['confirmation_message']);

        return redirect()->route('admin.donasi-transactions.create-receipt')
            ->with('parsed_donation_confirmation', $parsed)
            ->with('raw_donation_confirmation', $data['confirmation_message'])
            ->with('success', 'Data pesan WhatsApp berhasil dibaca. Silakan cek dan sesuaikan jika perlu.');
    }

    public function storeReceipt(Request $request)
    {
        $useUniqueCode = $this->parseUseUniqueCode($request->input('use_unique_code'));

        $data = $request->validate([
            'donor_name' => ['required', 'string', 'max:100'],
            'allow_future_donation_contact' => ['nullable', 'string', 'max:10'],
            'donor_whatsapp' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s]*$/'],
            'nominal_amount' => ['required', 'string', 'max:50'],
            'admin_fee' => ['nullable', 'string', 'max:50'],
            'unique_code' => $useUniqueCode
                ? ['required', 'integer', 'min:1', 'max:299']
                : ['nullable', 'string', 'max:50'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'transfer_date' => ['nullable', 'string', 'max:50'],
            'note' => ['nullable', 'string', 'max:1000'],
            'confirmation_message' => ['nullable', 'string', 'max:5000'],
        ]);

        $nominalAmount = $this->moneyToInteger($data['nominal_amount']);
        $adminFee = $this->moneyToInteger($data['admin_fee'] ?? '0');
        $uniqueCode = $useUniqueCode ? (int) $data['unique_code'] : 0;
        $totalTransfer = $nominalAmount + $adminFee + $uniqueCode;
        $allowContact = strtolower(trim($data['allow_future_donation_contact'] ?? 'Tidak')) === 'ya';
        $donorWhatsapp = $allowContact ? trim($data['donor_whatsapp'] ?? '') : '';
        $donorWhatsapp = $donorWhatsapp !== '' ? $donorWhatsapp : '-';
        $normalizedWhatsapp = $this->normalizeWhatsappNumber($donorWhatsapp);
        $paymentMethod = trim($data['payment_method'] ?? '') !== '' ? $data['payment_method'] : 'Transfer Bank';

        if ($nominalAmount <= 0) {
            return back()->withErrors(['nominal_amount' => 'Nominal donasi harus lebih dari 0.'])->withInput();
        }

        $noteLines = [
            'Bersedia Dihubungi: ' . ($allowContact ? 'Ya' : 'Tidak'),
            'Nomor WhatsApp: ' . $donorWhatsapp,
            'Metode Pembayaran: ' . $paymentMethod,
            'Biaya Admin: Rp' . number_format($adminFee, 0, ',', '.'),
            'Kode Unik: ' . ($useUniqueCode ? $uniqueCode : 'Tidak digunakan'),
            'Total Transfer: Rp' . number_format($totalTransfer, 0, ',', '.'),
            'Tanggal Transfer: ' . ($data['transfer_date'] ?: '-'),
        ];

        if (trim($data['note'] ?? '') && trim($data['note']) !== '-') {
            $noteLines[] = 'Catatan Donatur: ' . trim($data['note']);
        }

        if (trim($data['confirmation_message'] ?? '')) {
            $noteLines[] = 'Pesan WA: ' . trim($data['confirmation_message']);
        }

        $transaction = DonationTransaction::create([
            'order_id' => $this->generateOrderId(),
            'donor_name' => trim($data['donor_name']) ?: 'Hamba Allah',
            'donor_whatsapp' => $donorWhatsapp,
            'support_type' => 'Donasi Pendidikan & Makan Santri',
            'amount' => $nominalAmount,
            'note' => implode("\n", $noteLines),
            'payment_gateway' => 'manual-qris',
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $regularDonorMessage = null;

        if ($allowContact && $normalizedWhatsapp) {
            $regularDonorMessage = $this->upsertRegularDonor($transaction, $normalizedWhatsapp);
        } elseif ($allowContact) {
            $regularDonorMessage = 'Donatur bersedia dihubungi, tetapi nomor WhatsApp kosong sehingga tidak disimpan sebagai donatur tetap.';
        }

        return redirect()->route('admin.donasi-transactions.show', $transaction)
            ->with('success', 'Bukti penerimaan donasi berhasil diterbitkan. Referensi: ' . $transaction->order_id)
            ->with('regular_donor_message', $regularDonorMessage);
    }

    public function show(DonationTransaction $transaction)
    {
        $receipt = $this->receiptData($transaction);
        $whatsappUrl = $this->donorReceiptWhatsappUrl($transaction, $receipt);
        $schoolSetting = \App\Models\SchoolSetting::first();

        return view('admin.donasi-transactions.show', compact('transaction', 'receipt', 'whatsappUrl', 'schoolSetting'));
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

    private function parseConfirmationMessage(string $message): array
    {
        return [
            'donor_name' => $this->extractLabel($message, 'Nama Donatur') ?: 'Hamba Allah',
            'allow_future_donation_contact' => $this->extractLabel($message, 'Bersedia Dihubungi') ?: 'Tidak',
            'donor_whatsapp' => $this->extractLabel($message, 'Nomor WhatsApp') ?: '-',
            'nominal_amount' => $this->extractLabel($message, 'Nominal Donasi') ?: '',
            'admin_fee' => $this->extractLabel($message, 'Biaya Admin') ?: '',
            'unique_code' => $this->extractLabel($message, 'Kode Unik') ?: '',
            'total_transfer' => $this->extractLabel($message, 'Total Transfer') ?: '',
            'transfer_date' => $this->extractLabel($message, 'Tanggal Transfer') ?: now()->format('d/m/Y'),
            'note' => $this->extractLabel($message, 'Catatan') ?: '-',
        ];
    }

    private function extractLabel(string $message, string $label): ?string
    {
        if (preg_match('/^' . preg_quote($label, '/') . '\s*:\s*(.+)$/mi', $message, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    private function moneyToInteger(string $value): int
    {
        return (int) preg_replace('/[^0-9]/', '', $value);
    }

    private function parseUseUniqueCode($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return in_array(strtolower(trim((string) $value)), ['1', 'ya', 'yes', 'true', 'on'], true);
    }

    private function receiptUniqueCode(?string $raw): string
    {
        if ($raw === null || trim($raw) === '' || trim($raw) === '-') {
            return 'Tidak digunakan';
        }

        if (in_array(strtolower(trim($raw)), ['tidak digunakan', '0', '000', 'tidak ada', 'none'], true)) {
            return 'Tidak digunakan';
        }

        $code = (int) preg_replace('/[^0-9]/', '', $raw);

        return $code > 0 ? str_pad((string) $code, 3, '0', STR_PAD_LEFT) : 'Tidak digunakan';
    }

    private function receiptData(DonationTransaction $transaction): array
    {
        $noteData = $this->parseNoteLines($transaction->note ?? '');

        return [
            'receipt_number' => $transaction->order_id,
            'received_at' => $transaction->paid_at ?? $transaction->created_at,
            'donor_name' => $transaction->donor_name ?: 'Hamba Allah',
            'donor_whatsapp' => $transaction->donor_whatsapp ?: '-',
            'nominal_amount' => (int) $transaction->amount,
            'admin_fee' => $this->moneyToInteger($noteData['Biaya Admin'] ?? '0'),
            'unique_code' => $this->receiptUniqueCode($noteData['Kode Unik'] ?? null),
            'total_transfer' => $this->moneyToInteger($noteData['Total Transfer'] ?? (string) $transaction->amount),
            'transfer_date' => $noteData['Tanggal Transfer'] ?? optional($transaction->paid_at ?? $transaction->created_at)->format('d/m/Y'),
            'note' => $noteData['Catatan Donatur'] ?? '-',
            'allow_contact' => $noteData['Bersedia Dihubungi'] ?? '-',
            'payment_method' => $noteData['Metode Pembayaran'] ?? '-',
            'status' => $transaction->status,
        ];
    }

    private function parseNoteLines(string $note): array
    {
        $data = [];

        foreach (preg_split('/\r\n|\r|\n/', $note) as $line) {
            if (!str_contains($line, ':')) {
                continue;
            }

            [$key, $value] = explode(':', $line, 2);
            $data[trim($key)] = trim($value);
        }

        return $data;
    }

    private function donorReceiptWhatsappUrl(DonationTransaction $transaction, array $receipt): ?string
    {
        $number = $this->normalizeWhatsappNumber($receipt['donor_whatsapp'] ?? $transaction->donor_whatsapp);

        if (!$number) {
            return null;
        }

        $message = "Assalamu'alaikum {$receipt['donor_name']}.\n\n"
            . "Terima kasih, donasi pendidikan untuk SMA Persis Serang sudah kami terima.\n\n"
            . "Nomor Bukti: {$receipt['receipt_number']}\n"
            . "Nama Donatur: {$receipt['donor_name']}\n"
            . "Nominal Donasi: Rp" . number_format($receipt['nominal_amount'], 0, ',', '.') . "\n"
            . "Total Transfer: Rp" . number_format($receipt['total_transfer'], 0, ',', '.') . "\n"
            . "Tanggal: " . $receipt['received_at']->format('d/m/Y') . "\n\n"
            . "Semoga Allah membalas dengan pahala terbaik dan menjadikan donasi ini sebagai amal jariyah.\n\n"
            . "Aamiin.\n\n"
            . "SMA Persis Serang";

        return 'https://wa.me/' . $number . '?text=' . urlencode($message);
    }

    private function normalizeWhatsappNumber(?string $number): ?string
    {
        $clean = preg_replace('/[^0-9]/', '', (string) $number);

        if (!$clean || $clean === '-') {
            return null;
        }

        if (str_starts_with($clean, '0')) {
            return '62' . substr($clean, 1);
        }

        if (str_starts_with($clean, '8')) {
            return '62' . $clean;
        }

        return $clean;
    }

    private function upsertRegularDonor(DonationTransaction $transaction, string $normalizedWhatsapp): ?string
    {
        if (!Schema::hasTable('donation_regular_donors')) {
            return 'Tabel donatur tetap belum tersedia. Jalankan migration sebelum data donatur tetap dapat disimpan.';
        }

        $donor = DonationRegularDonor::firstOrNew(['whatsapp_number' => $normalizedWhatsapp]);
        $isNew = !$donor->exists;

        $donor->fill([
            'name' => $transaction->donor_name ?: 'Hamba Allah',
            'is_active' => true,
            'source' => 'donasi_pendidikan',
            'last_donation_at' => $transaction->paid_at ?? now(),
        ]);

        if ($isNew || !$donor->first_donation_at) {
            $donor->first_donation_at = $transaction->paid_at ?? now();
        }

        $donor->total_donations_count = (int) $donor->total_donations_count + 1;
        $donor->total_donations_amount = (int) $donor->total_donations_amount + (int) $transaction->amount;
        $donor->save();

        return $isNew
            ? 'Donatur tetap baru berhasil disimpan.'
            : 'Data donatur tetap berhasil diperbarui.';
    }

    private function generateOrderId(): string
    {
        do {
            $orderId = 'DON-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
        } while (DonationTransaction::where('order_id', $orderId)->exists());

        return $orderId;
    }
}
