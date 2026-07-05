<?php

namespace App\Http\Controllers;

use App\Models\DonationEducationSetting;
use App\Models\FosterParentSubmission;
use App\Models\FosterStudent;
use App\Models\SchoolSetting;
use App\Services\QrisDynamicService;
use Illuminate\Http\Request;

class FosterParentController extends Controller
{
    public function index()
    {
        try {
            $schoolSetting = SchoolSetting::current();
        } catch (\Exception $e) {
            $schoolSetting = null;
        }

        $setting = DonationEducationSetting::activeSetting();

        $students = FosterStudent::active()
            ->orderBy('is_priority', 'desc')
            ->orderBy('name')
            ->get();

        $waNumber = preg_replace('/[^0-9]/', '', $setting?->whatsapp_number ?: '6289661234569');
        $waMessage = $setting?->whatsapp_message ?: 'Assalamu\'alaikum, saya ingin menjadi Orang Tua Asuh Santri SMA Persis Serang';
        $waUrl = 'https://wa.me/' . $waNumber . '?text=' . urlencode($waMessage);

        return view('pages.orang-tua-asuh', compact(
            'schoolSetting', 'setting', 'students', 'waNumber', 'waUrl'
        ));
    }

    public function qrisPreview(Request $request, QrisDynamicService $qrisService)
    {
        $data = $request->validate([
            'foster_student_id' => ['nullable', 'exists:foster_students,id'],
            'donor_name' => ['nullable', 'string', 'max:100'],
            'donor_phone' => ['nullable', 'string', 'max:30'],
            'amount' => ['nullable', 'string', 'max:50'],
            'custom_amount' => ['nullable', 'numeric', 'min:1000', 'max:50000000'],
            'commitment_duration' => ['nullable', 'string', 'max:50'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $amount = 0;

        if (($data['amount'] ?? null) && $data['amount'] !== 'lainnya') {
            $amount = (int) $data['amount'];
        } elseif (($data['amount'] ?? null) === 'lainnya' && ($data['custom_amount'] ?? null)) {
            $amount = (int) $data['custom_amount'];
        }

        if ($amount < 1000 || $amount > 50000000) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih nominal donasi terlebih dahulu.',
            ]);
        }

        $setting = DonationEducationSetting::first();

        $qrisImage = null;
        $isDynamic = false;
        $staticFallback = false;

        if ($setting?->donation_qris_payload) {
            try {
                $qrisImage = $qrisService->generateBase64($setting->donation_qris_payload, $amount);
                $isDynamic = true;
            } catch (\Exception $e) {
                $qrisImage = null;
            }
        }

        if (!$qrisImage && $setting?->donation_qris_image) {
            $qrisImage = \Illuminate\Support\Facades\Storage::url($setting->donation_qris_image);
            $staticFallback = true;
        }

        $studentName = null;
        if (($data['foster_student_id'] ?? null)) {
            $student = FosterStudent::find($data['foster_student_id']);
            $studentName = $student?->name;
        }

        $donorName = $data['donor_name'] ?: 'Hamba Allah';
        $donorPhone = $data['donor_phone'] ?: '-';
        $commitmentDuration = $data['commitment_duration'] ?: '-';

        $response = [
            'success' => (bool) $qrisImage,
            'qris_image' => $qrisImage,
            'is_dynamic' => $isDynamic,
            'static_fallback' => $staticFallback,
            'amount_formatted' => 'Rp' . number_format($amount, 0, ',', '.'),
            'amount_raw' => $amount,
            'summary' => [
                'student_name' => $studentName,
                'donor_name' => $donorName,
                'donor_phone' => $donorPhone,
                'commitment_duration' => $commitmentDuration,
                'note' => $data['note'] ?? null,
            ],
        ];

        if (!$qrisImage) {
            $response['message'] = 'QRIS belum tersedia. Silakan hubungi admin melalui WhatsApp.';
        } elseif ($staticFallback) {
            $response['message'] = 'Nominal belum otomatis. Silakan masukkan nominal secara manual di aplikasi pembayaran.';
        }

        return response()->json($response);
    }

    public function submit(Request $request)
    {
        $data = $request->validate([
            'foster_student_id' => ['nullable', 'exists:foster_students,id'],
            'donor_name' => ['nullable', 'string', 'max:100'],
            'donor_phone' => ['nullable', 'string', 'max:30'],
            'amount' => ['required', 'string', 'max:50'],
            'custom_amount' => ['nullable', 'numeric', 'min:1000', 'max:50000000'],
            'commitment_duration' => ['required', 'string', 'max:50'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $amount = $data['amount'] === 'lainnya' && $data['custom_amount']
            ? (int) $data['custom_amount']
            : (int) $data['amount'];

        if ($amount < 1000) {
            return back()->withErrors(['amount' => 'Minimal donasi Rp1.000'])->withInput();
        }

        $submission = FosterParentSubmission::create([
            'foster_student_id' => $data['foster_student_id'] ?? null,
            'donor_name' => trim($data['donor_name'] ?? '') ?: null,
            'donor_phone' => trim($data['donor_phone'] ?? '') ?: null,
            'amount' => $amount,
            'commitment_duration' => $data['commitment_duration'],
            'note' => $data['note'] ?? null,
            'payment_status' => 'pending',
        ]);

        return redirect()->route('orang-tua-asuh')
            ->with('success', 'Data pengajuan berhasil disimpan. Silakan scan QRIS di bawah untuk melakukan pembayaran.');
    }
}
