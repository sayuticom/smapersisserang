<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationItemCommitment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonationItemCommitmentController extends Controller
{
    public function index(Request $request)
    {
        $query = DonationItemCommitment::with('receipt');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('received_at', $request->date);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('donor_name', 'like', "%{$search}%")
                    ->orWhere('donor_phone', 'like', "%{$search}%")
                    ->orWhere('item_type', 'like', "%{$search}%")
                    ->orWhere('item_name', 'like', "%{$search}%");
            });
        }

        $commitments = $query->latest('received_at')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.infaq-barang-wa.index', [
            'commitments' => $commitments,
            'statuses' => DonationItemCommitment::STATUSES,
        ]);
    }

    public function create()
    {
        return view('admin.infaq-barang-wa.create', [
            'statuses' => DonationItemCommitment::STATUSES,
            'parsed' => session('parsed_item_commitment', []),
            'rawMessage' => session('raw_item_commitment', ''),
        ]);
    }

    public function parse(Request $request)
    {
        $data = $request->validate([
            'raw_whatsapp_message' => ['required', 'string', 'max:5000'],
        ]);

        return redirect()->route('admin.infaq-barang-wa.create')
            ->with('parsed_item_commitment', $this->parseWhatsappMessage($data['raw_whatsapp_message']))
            ->with('raw_item_commitment', $data['raw_whatsapp_message'])
            ->with('success', 'Data pesan WhatsApp berhasil dibaca. Lengkapi bagian yang masih kosong.');
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $receivedAt = $data['received_at'];

        $data['reference_number'] = $this->generateReferenceNumber((int) date('Y', strtotime($receivedAt)));
        $data['created_by'] = $request->user()?->id;
        $data['confirmed_at'] = in_array($data['status'], ['confirmed', 'pickup', 'waiting_delivery', 'received'], true) ? now() : null;

        $commitment = DonationItemCommitment::create($data);

        return redirect()->route('admin.infaq-barang-wa.show', $commitment)
            ->with('success', 'Data WA Infaq Barang berhasil disimpan.');
    }

    public function show(DonationItemCommitment $infaqBarangWa)
    {
        return view('admin.infaq-barang-wa.show', [
            'commitment' => $infaqBarangWa->load('receipt'),
            'statuses' => DonationItemCommitment::STATUSES,
        ]);
    }

    public function updateStatus(Request $request, DonationItemCommitment $infaqBarangWa)
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', array_keys(DonationItemCommitment::STATUSES))],
        ]);

        $updates = ['status' => $data['status']];

        if (in_array($data['status'], ['confirmed', 'pickup', 'waiting_delivery', 'received'], true) && !$infaqBarangWa->confirmed_at) {
            $updates['confirmed_at'] = now();
        }

        $infaqBarangWa->update($updates);

        return back()->with('success', 'Status Data WA Infaq Barang berhasil diperbarui.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'received_at' => ['required', 'date'],
            'donor_name' => ['nullable', 'string', 'max:100'],
            'donor_phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s]*$/'],
            'item_type' => ['required', 'string', 'max:100'],
            'item_name' => ['nullable', 'string', 'max:150'],
            'quantity_estimate' => ['nullable', 'string', 'max:100'],
            'delivery_method' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:1000'],
            'raw_whatsapp_message' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'string', 'in:' . implode(',', array_keys(DonationItemCommitment::STATUSES))],
        ]);
    }

    private function parseWhatsappMessage(string $message): array
    {
        return [
            'donor_name' => $this->extractLabel($message, ['Nama Donatur', 'Nama']) ?: '',
            'donor_phone' => $this->extractLabel($message, ['Nomor WhatsApp', 'WhatsApp', 'Nomor WA']) ?: '',
            'item_type' => $this->extractLabel($message, ['Jenis Barang']) ?: '',
            'quantity_estimate' => $this->extractLabel($message, ['Jumlah / Perkiraan', 'Jumlah/Perkiraan', 'Jumlah']) ?: '',
            'delivery_method' => $this->extractLabel($message, ['Cara Penyerahan']) ?: '',
            'note' => $this->extractLabel($message, ['Catatan']) ?: '',
        ];
    }

    private function extractLabel(string $message, array $labels): ?string
    {
        foreach ($labels as $label) {
            if (preg_match('/^' . preg_quote($label, '/') . '\s*:\s*(.+)$/mi', $message, $matches)) {
                $value = trim($matches[1]);
                return $value === '(tidak diisi)' ? '' : $value;
            }
        }

        return null;
    }

    private function generateReferenceNumber(int $year): string
    {
        return DB::transaction(function () use ($year) {
            $lastCommitment = DonationItemCommitment::where('reference_number', 'like', "WIB-{$year}-%")
                ->lockForUpdate()
                ->orderByDesc('reference_number')
                ->first();

            $nextNumber = 1;

            if ($lastCommitment && preg_match('/WIB-\d{4}-(\d{4})$/', $lastCommitment->reference_number, $matches)) {
                $nextNumber = (int) $matches[1] + 1;
            }

            return sprintf('WIB-%d-%04d', $year, $nextNumber);
        });
    }
}
