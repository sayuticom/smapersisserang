<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationItemCommitment;
use App\Models\DonationItemReceipt;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonationItemReceiptController extends Controller
{
    public function index(Request $request)
    {
        $query = DonationItemReceipt::query();

        if ($request->filled('date')) {
            $query->whereDate('received_date', $request->date);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                    ->orWhere('donor_name', 'like', "%{$search}%")
                    ->orWhere('item_type', 'like', "%{$search}%")
                    ->orWhere('item_name', 'like', "%{$search}%");
            });
        }

        $receipts = $query->latest('received_date')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.infaq-barang.index', compact('receipts'));
    }

    public function create(Request $request)
    {
        $sourceCommitment = null;
        $prefill = [];

        if (!$request->filled('source_wa')) {
            return redirect()->route('admin.infaq-barang-wa.index')
                ->with('success', 'Penerimaan Infaq Barang harus dibuat dari Data WA Infaq Barang.');
        }

        if ($request->filled('source_wa')) {
            $sourceCommitment = DonationItemCommitment::findOrFail($request->integer('source_wa'));

            if ($sourceCommitment->received_receipt_id) {
                return redirect()->route('admin.infaq-barang-wa.show', $sourceCommitment)
                    ->with('success', 'Data WA ini sudah memiliki bukti penerimaan.');
            }

            $prefill = [
                'donor_name' => $sourceCommitment->donor_name,
                'donor_phone' => $sourceCommitment->donor_phone,
                'item_type' => $sourceCommitment->item_type,
                'item_name' => $sourceCommitment->item_name,
                'quantity' => $sourceCommitment->quantity_estimate,
                'delivery_method' => $sourceCommitment->delivery_method,
                'note' => $sourceCommitment->note,
            ];
        }

        return view('admin.infaq-barang.create', compact('sourceCommitment', 'prefill'));
    }

    public function store(Request $request)
    {
        if (!$request->filled('source_wa_id')) {
            return redirect()->route('admin.infaq-barang-wa.index')
                ->with('success', 'Penerimaan Infaq Barang harus dibuat dari Data WA Infaq Barang.');
        }

        $data = $request->validate([
            'received_date' => ['required', 'date'],
            'donor_name' => ['nullable', 'string', 'max:100'],
            'donor_phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s]*$/'],
            'item_type' => ['required', 'string', 'max:100'],
            'item_name' => ['nullable', 'string', 'max:150'],
            'quantity' => ['nullable', 'string', 'max:50'],
            'unit' => ['nullable', 'string', 'max:50'],
            'item_condition' => ['nullable', 'string', 'max:100'],
            'delivery_method' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:1000'],
            'received_by' => ['nullable', 'string', 'max:100'],
            'proof_photo' => ['nullable', 'image', 'max:2048'],
            'source_wa_id' => ['required', 'integer', 'exists:donation_item_commitments,id'],
        ]);

        $sourceCommitment = DonationItemCommitment::findOrFail($data['source_wa_id']);

        if ($sourceCommitment->received_receipt_id) {
            return redirect()->route('admin.infaq-barang-wa.show', $sourceCommitment)
                ->with('success', 'Data WA ini sudah memiliki bukti penerimaan.');
        }

        if ($request->hasFile('proof_photo')) {
            $data['proof_photo'] = $request->file('proof_photo')->store('donasi/infaq-barang', 'public');
        }

        unset($data['source_wa_id']);

        $data['receipt_number'] = $this->generateReceiptNumber((int) date('Y', strtotime($data['received_date'])));
        $data['user_id'] = $request->user()?->id;
        $data['received_by'] = trim($data['received_by'] ?? '') ?: $request->user()?->name;
        $data['status'] = 'received';

        $receipt = DonationItemReceipt::create($data);

        if ($sourceCommitment) {
            $sourceCommitment->update([
                'status' => 'received',
                'confirmed_at' => $sourceCommitment->confirmed_at ?: now(),
                'received_receipt_id' => $receipt->id,
            ]);
        }

        return redirect()->route('admin.infaq-barang.show', $receipt)
            ->with('success', 'Bukti penerimaan infaq barang berhasil dibuat.');
    }

    public function show(DonationItemReceipt $infaqBarang)
    {
        $schoolSetting = SchoolSetting::first();
        $whatsappUrl = $this->receiptWhatsappUrl($infaqBarang);
        $infaqBarang->load('commitment');

        return view('admin.infaq-barang.show', [
            'receipt' => $infaqBarang,
            'schoolSetting' => $schoolSetting,
            'whatsappUrl' => $whatsappUrl,
        ]);
    }

    private function generateReceiptNumber(int $year): string
    {
        return DB::transaction(function () use ($year) {
            $lastReceipt = DonationItemReceipt::where('receipt_number', 'like', "IB-{$year}-%")
                ->lockForUpdate()
                ->orderByDesc('receipt_number')
                ->first();

            $nextNumber = 1;

            if ($lastReceipt && preg_match('/IB-\d{4}-(\d{4})$/', $lastReceipt->receipt_number, $matches)) {
                $nextNumber = (int) $matches[1] + 1;
            }

            return sprintf('IB-%d-%04d', $year, $nextNumber);
        });
    }

    private function receiptWhatsappUrl(DonationItemReceipt $receipt): ?string
    {
        $number = $this->normalizeWhatsappNumber($receipt->donor_phone);

        if (!$number) {
            return null;
        }

        $message = "Assalamu'alaikum.\n\n"
            . "Terima kasih, infaq barang dari Bapak/Ibu telah kami terima.\n\n"
            . "Nomor Bukti: {$receipt->receipt_number}\n"
            . "Nama Donatur: {$receipt->donorNameLabel()}\n"
            . "Barang: {$receipt->itemLabel()}\n"
            . "Jumlah: {$receipt->quantityLabel()}\n"
            . "Tanggal Terima: {$receipt->received_date->format('d/m/Y')}\n\n"
            . "Semoga Allah membalas dengan pahala terbaik dan menjadikannya amal jariyah.\n\n"
            . "Bukti penerimaan:\n"
            . route('admin.infaq-barang.show', $receipt);

        return 'https://wa.me/' . $number . '?text=' . urlencode($message);
    }

    private function normalizeWhatsappNumber(?string $number): ?string
    {
        $clean = preg_replace('/[^0-9]/', '', (string) $number);

        if (!$clean) {
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
}
