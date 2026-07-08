<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use App\Models\WaqfSetting;
use App\Models\WaqfTransaction;
use App\Services\QrisDynamicService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WaqfController extends Controller
{
    public function index()
    {
        try {
            $schoolSetting = SchoolSetting::current();
        } catch (\Exception $e) {
            $schoolSetting = null;
        }

        $setting = WaqfSetting::activeSetting();

        return view('pages.wakaf-uang.index', compact('schoolSetting', 'setting'));
    }

    public function form()
    {
        try {
            $schoolSetting = SchoolSetting::current();
        } catch (\Exception $e) {
            $schoolSetting = null;
        }

        $setting = WaqfSetting::activeSetting();

        return view('pages.wakaf-uang.form', compact('schoolSetting', 'setting'));
    }

    public function previewQris(Request $request, QrisDynamicService $qrisService)
    {
        $data = $request->validate([
            'wakif_name' => ['nullable', 'string', 'max:100'],
            'wakif_whatsapp' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s]*$/'],
            'amount' => ['nullable', 'string', 'max:50'],
            'custom_amount' => ['nullable', 'string', 'max:50'],
            'unique_code' => ['nullable', 'integer', 'min:1', 'max:999'],
            'note' => ['nullable', 'string', 'max:500'],
            'ikrar_checked' => ['nullable', 'boolean'],
        ]);

        $amount = $this->resolveAmount($data['amount'] ?? null, $data['custom_amount'] ?? null);

        if ($amount < 10000 || $amount > 50000000) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih nominal wakaf minimal Rp10.000 terlebih dahulu.',
            ]);
        }

        $uniqueCode = (int) ($data['unique_code'] ?? random_int(1, 299));
        $uniqueCode = max(1, min(299, $uniqueCode));
        $uniqueCodeFormatted = str_pad((string) $uniqueCode, 3, '0', STR_PAD_LEFT);
        $adminFee = (int) ceil($amount * 0.006);
        $totalTransfer = $amount + $adminFee + $uniqueCode;

        $setting = WaqfSetting::activeSetting();

        $qrisImage = null;
        $isDynamic = false;
        $staticFallback = false;

        if ($setting?->qris_payload) {
            try {
                $qrisImage = $qrisService->generateBase64($setting->qris_payload, $totalTransfer);
                $isDynamic = true;
            } catch (\Exception $e) {
                $qrisImage = null;
            }
        }

        if (!$qrisImage && $setting?->qris_image) {
            $qrisImage = Storage::url($setting->qris_image);
            $staticFallback = true;
        }

        $waNumber = preg_replace('/[^0-9]/', '', $setting?->whatsapp_number ?: '6289661234569');

        $wakifName = trim($data['wakif_name'] ?? '') ?: 'Hamba Allah';
        $ikrarChecked = $request->boolean('ikrar_checked');
        $ikrarText = $ikrarChecked ? 'Ya, Saya Berikrar' : 'Tidak';
        $note = trim($data['note'] ?? '') ?: '-';
        $transferDate = now()->timezone(config('app.timezone'))->format('d/m/Y');

        $confirmMessage = "Assalamu'alaikum Admin SMA Persis Serang.\n\n"
            . "Saya sudah melakukan wakaf uang.\n\n"
            . "Nama Wakif: {$wakifName}\n"
            . "Ikrar Wakaf: {$ikrarText}\n"
            . "Nomor WhatsApp: " . (trim($data['wakif_whatsapp'] ?? '') ?: '-') . "\n"
            . "Nominal Wakaf: Rp" . number_format($amount, 0, ',', '.') . "\n"
            . "Biaya Admin: Rp" . number_format($adminFee, 0, ',', '.') . "\n"
            . "Kode Unik: {$uniqueCodeFormatted}\n"
            . "Total Transfer: Rp" . number_format($totalTransfer, 0, ',', '.') . "\n"
            . "Tanggal Transfer: {$transferDate}\n"
            . "Catatan: {$note}\n\n"
            . "Mohon dicek dan dibuatkan bukti penerimaan wakaf.\n\n"
            . "Terima kasih.";

        $confirmWaUrl = 'https://wa.me/' . $waNumber . '?text=' . urlencode($confirmMessage);

        try {
            $schoolSetting = SchoolSetting::current();
            $merchantName = $schoolSetting?->school_name ? strtoupper($schoolSetting->school_name) . ', CURUG' : 'SMA PERSIS SERANG, CURUG';
            $merchantCity = 'SERANG';
        } catch (\Exception $e) {
            $merchantName = 'SMA PERSIS SERANG, CURUG';
            $merchantCity = 'SERANG';
        }

        $response = [
            'success' => (bool) $qrisImage,
            'qris_image' => $qrisImage,
            'is_dynamic' => $isDynamic,
            'static_fallback' => $staticFallback,
            'amount_formatted' => 'Rp' . number_format($amount, 0, ',', '.'),
            'amount_raw' => $totalTransfer,
            'nominal_raw' => $amount,
            'admin_fee' => $adminFee,
            'admin_fee_formatted' => 'Rp' . number_format($adminFee, 0, ',', '.'),
            'unique_code' => $uniqueCodeFormatted,
            'unique_code_raw' => $uniqueCode,
            'total_transfer_formatted' => 'Rp' . number_format($totalTransfer, 0, ',', '.'),
            'total_transfer_raw' => $totalTransfer,
            'transfer_date' => $transferDate,
            'whatsapp_url' => $confirmWaUrl,
            'merchant_name' => $merchantName,
            'merchant_city' => $merchantCity,
            'summary' => [
                'wakif_name' => $wakifName,
                'ikrar_checked' => $ikrarChecked,
                'note' => $note,
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
        if ($request->filled('website_url')) {
            return redirect()->route('wakaf-uang.form')
                ->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }

        $formStartedAt = $request->input('form_started_at');
        if ($formStartedAt) {
            $started = strtotime($formStartedAt);
            $elapsed = time() - $started;
            if ($elapsed < 3) {
                return redirect()->route('wakaf-uang.form')
                    ->with('error', 'Terlalu cepat. Silakan isi form dengan benar.');
            }
        }

        $data = $request->validate([
            'wakif_name' => 'nullable|string|max:100',
            'wakif_whatsapp' => 'nullable|string|max:20',
            'amount' => 'required|string|max:50',
            'custom_amount' => 'nullable|string|max:50',
            'note' => 'nullable|string|max:500',
            'ikrar_checked' => 'accepted',
        ]);

        $amount = $this->resolveAmount($data['amount'] ?? null, $data['custom_amount'] ?? null);

        if ($amount < 10000) {
            return back()->withErrors(['amount' => 'Minimal wakaf Rp10.000'])->withInput();
        }

        return redirect()->route('wakaf-uang.form')
            ->with('success', 'Silakan lanjutkan pembayaran melalui QRIS, lalu konfirmasi via WhatsApp. Data wakaf belum disimpan sampai admin melakukan verifikasi.');
    }

    public function downloadQris(Request $request, QrisDynamicService $qrisService)
    {
        $amount = (int) preg_replace('/[^0-9]/', '', $request->query('amount', '0'));

        if ($amount < 10000) {
            $amount = 10000;
        }

        $setting = WaqfSetting::activeSetting();

        if ($setting?->qris_payload && $amount >= 1) {
            try {
                $pngBinary = $qrisService->generatePngBinary($setting->qris_payload, $amount);
                $filename = 'qris-wakaf-sma-persis-serang.png';

                return response($pngBinary, 200, [
                    'Content-Type' => 'image/png',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                ]);
            } catch (\Exception $e) {
            }
        }

        if ($setting?->qris_image) {
            $path = Storage::path($setting->qris_image);
            if (file_exists($path)) {
                $pngBinary = $this->optimizedQrisImageBinary($setting->qris_image);
                if ($pngBinary) {
                    return response($pngBinary, 200, [
                        'Content-Type' => 'image/png',
                        'Content-Disposition' => 'attachment; filename="qris-wakaf-sma-persis-serang.png"',
                        'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    ]);
                }
            }
        }

        return redirect()->route('wakaf-uang.form')
            ->with('error', 'QRIS belum tersedia. Silakan hubungi admin.');
    }

    private function resolveAmount(?string $amount, ?string $customAmount): int
    {
        if ($amount && $amount !== 'lainnya') {
            return (int) preg_replace('/[^0-9]/', '', $amount);
        }

        if ($amount === 'lainnya' && $customAmount) {
            return (int) preg_replace('/[^0-9]/', '', $customAmount);
        }

        return 0;
    }

    private function optimizedQrisImageBinary(string $path): ?string
    {
        try {
            if (!Storage::exists($path)) {
                return null;
            }

            $originalBinary = Storage::get($path);
            $source = @imagecreatefromstring($originalBinary);

            if (!$source) {
                return $originalBinary ?: null;
            }

            $sourceWidth = imagesx($source);
            $sourceHeight = imagesy($source);
            $targetWidths = [800, 700, 600, 500];
            $lastBinary = null;

            foreach ($targetWidths as $maxWidth) {
                $scale = min(1, $maxWidth / max(1, $sourceWidth));
                $targetWidth = max(1, (int) round($sourceWidth * $scale));
                $targetHeight = max(1, (int) round($sourceHeight * $scale));

                $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
                $white = imagecolorallocate($canvas, 255, 255, 255);
                imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $white);
                imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

                ob_start();
                imagepng($canvas, null, 9);
                $binary = (string) ob_get_clean();
                imagedestroy($canvas);

                $lastBinary = $binary;

                if (strlen($binary) <= 500 * 1024) {
                    imagedestroy($source);
                    return $binary;
                }
            }

            imagedestroy($source);

            return $lastBinary;
        } catch (\Exception $e) {
            return null;
        }
    }
}
