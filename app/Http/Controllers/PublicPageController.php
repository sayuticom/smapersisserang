<?php

namespace App\Http\Controllers;

use App\Models\DonationTransaction;
use App\Models\Faq;
use App\Models\DonationEducationSetting;
use App\Models\GalleryCategory;
use App\Models\OrganizationStructure;
use App\Models\SchoolFigure;
use App\Models\SchoolImage;
use App\Models\SchoolSetting;
use App\Models\SchoolSubject;
use App\Models\SchoolValue;
use App\Models\Teacher;
use App\Models\WebsitePage;
use App\Services\QrisDynamicService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class PublicPageController extends Controller
{
    public function profile()
    {
        try {
            $schoolSetting = SchoolSetting::current();
            $websitePage = WebsitePage::key('profile');

            $buildingImages = SchoolImage::where('is_active', true)
                ->whereHas('categories', fn($q) => $q->where('slug', 'fasilitas'))
                ->orderBy('sort_order')
                ->get();
        } catch (\Exception $e) {
            $schoolSetting = null;
            $websitePage = null;
            $buildingImages = collect();
        }

        return view('pages.profile', compact('schoolSetting', 'websitePage', 'buildingImages'));
    }

    public function program()
    {
        try {
            $schoolSetting = SchoolSetting::current();
            $websitePage = WebsitePage::key('program');
            $subjectsByCategory = SchoolSubject::with(['teachers' => function ($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                }])
                ->where('is_active', true)
                ->orderBy('category')
                ->orderBy('sort_order')
                ->get()
                ->groupBy('category');

            $schoolValues = SchoolValue::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        } catch (\Exception $e) {
            $schoolSetting = null;
            $websitePage = null;
            $subjectsByCategory = collect();
            $schoolValues = collect();
        }

        $subjectCategories = SchoolSubject::CATEGORIES;

        return view('pages.program', compact('schoolSetting', 'websitePage', 'subjectCategories', 'subjectsByCategory', 'schoolValues'));
    }

    public function boarding()
    {
        try {
            $schoolSetting = SchoolSetting::current();
            $websitePage = WebsitePage::key('boarding');
        } catch (\Exception $e) {
            $schoolSetting = null;
            $websitePage = null;
        }

        return view('pages.boarding', compact('schoolSetting', 'websitePage'));
    }

    public function gallery(Request $request)
    {
        try {
            $schoolSetting = SchoolSetting::current();
            $websitePage = WebsitePage::key('gallery');
            $galleryCategories = GalleryCategory::where('slug', '!=', 'hero')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        } catch (\Exception $e) {
            $schoolSetting = null;
            $websitePage = null;
            $galleryCategories = collect();
        }

        $category = $request->get('category');

        $query = SchoolImage::where('is_active', true)
            ->whereHas('categories', fn($q) => $q->where('slug', '!=', 'hero'))
            ->orderBy('sort_order')
            ->latest();

        if ($category && $galleryCategories->firstWhere('slug', $category)) {
            $query->whereHas('categories', fn($q) => $q->where('slug', $category));
        }

        $galleryImages = $query->with('categories')->get();

        $categories = $galleryCategories->pluck('name', 'slug');

        return view('pages.gallery', compact('schoolSetting', 'galleryImages', 'categories', 'category', 'websitePage'));
    }

    public function figures()
    {
        try {
            $schoolSetting = SchoolSetting::current();

            $figures = SchoolFigure::where('is_active', true)
                ->orderBy('sort_order')
                ->latest()
                ->get();

            $websitePage = WebsitePage::key('figures');
        } catch (\Exception $e) {
            $schoolSetting = null;
            $figures = collect();
            $websitePage = null;
        }

        return view('pages.figures', compact('schoolSetting', 'figures', 'websitePage'));
    }

    public function faq()
    {
        try {
            $schoolSetting = SchoolSetting::current();

            $faqs = Faq::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        } catch (\Exception $e) {
            $schoolSetting = null;
            $faqs = collect();
        }

        return view('pages.faq', compact('schoolSetting', 'faqs'));
    }

    /**
     * Menampilkan halaman Struktur Organisasi.
     *
     * Data struktur organisasi diambil dari database melalui model OrganizationStructure
     * dan dapat dikelola melalui menu admin: Website > Struktur Organisasi.
     *
     * -------------------------------------------------------------------
     * CARA MENGEDIT ISI ORGANIGRAM:
     * 1. Buka dashboard admin → menu "Struktur Organisasi" (sidebar kiri)
     * 2. Edit data per level:
     *    - Level 1 (pembina/pimpinan): diposisikan paling atas
     *    - Level 2 (kepala sekolah, komite): diposisikan di tengah
     *    - Level 3 (waka, TU, asrama dll.): ditambahkan sebagai anak dari
     *      struktur level 2 dengan mengisi field "Parent Key"
     * 3. Untuk setiap entri, isi:
     *    - Label: nama jabatan (contoh: "Waka Kurikulum")
     *    - Person Name: nama pejabat yang menjabat (opsional)
     *    - Person ID: pilih dari data Guru yang sudah ada (opsional)
     *    - Description: keterangan tambahan
     *    - Members: daftar anggota (dipisahkan koma atau baris baru)
     *    - Level: 1, 2, atau 3
     *    - Card Type: 'principal' untuk kepala sekolah (diberi aksen khusus)
     *    - Parent Key: diisi untuk menghubungkan level 3 ke level 2
     *      (contoh: parent_key level 3 diisi 'kepala-sekolah')
     * -------------------------------------------------------------------
     */
    private function getOrganisasiData(): array
    {
        $organizationStructures = OrganizationStructure::with('person')
            ->active()
            ->orderBy('level')
            ->orderBy('sort_order')
            ->get();

        $childrenByParent = $organizationStructures
            ->whereNotNull('parent_key')
            ->groupBy('parent_key');

        return $organizationStructures
            ->whereNull('parent_key')
            ->map(function ($structure) use ($childrenByParent) {
                $item = [
                    'key' => $structure->structure_key,
                    'level' => $structure->level,
                    'jabatan' => $structure->label,
                    'person_name' => $structure->person_name,
                    'person_display_name' => $structure->person?->name ?: $structure->person_name,
                    'photo_url' => $this->organizationPhotoUrl($structure),
                    'deskripsi' => $structure->description,
                    'anggota' => $structure->members ?? [],
                    'card_type' => $structure->card_type,
                ];

                $children = $childrenByParent->get($structure->structure_key, collect());
                if ($children->isNotEmpty()) {
                    $item['children'] = $children->map(fn($child) => [
                        'key' => $child->structure_key,
                        'level' => $child->level,
                        'jabatan' => $child->label,
                        'person_name' => $child->person_name,
                        'person_display_name' => $child->person?->name ?: $child->person_name,
                        'photo_url' => $this->organizationPhotoUrl($child),
                        'deskripsi' => $child->description,
                        'anggota' => $child->members ?? [],
                        'card_type' => $child->card_type,
                    ])->values()->all();
                }

                return $item;
            })
            ->values()
            ->all();
    }

    public function strukturOrganisasi()
    {
        try {
            $schoolSetting = SchoolSetting::current();
            $websitePage = WebsitePage::key('struktur-organisasi');
        } catch (\Exception $e) {
            $schoolSetting = null;
            $websitePage = null;
        }

        $organisasi = $this->getOrganisasiData();

        return view('pages.struktur-organisasi', compact('schoolSetting', 'websitePage', 'organisasi'));
    }

    public function donasiPendidikan()
    {
        try {
            $schoolSetting = SchoolSetting::current();
        } catch (\Exception $e) {
            $schoolSetting = null;
        }

        $setting = DonationEducationSetting::activeSetting();

        return view('pages.donasi-pendidikan', compact('schoolSetting', 'setting'));
    }

    public function formDonatur()
    {
        try {
            $schoolSetting = SchoolSetting::current();
        } catch (\Exception $e) {
            $schoolSetting = null;
        }

        $setting = DonationEducationSetting::activeSetting();

        return view('pages.form-donatur', compact('schoolSetting', 'setting'));
    }

    public function submitDonatur(Request $request)
    {
        // Honeypot: jika field website_url terisi, tolak sebagai spam
        if ($request->filled('website_url')) {
            return redirect()->route('donasi-pendidikan.form-donatur')
                ->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }

        // Timestamp: tolak jika submit kurang dari 3 detik sejak halaman dibuka
        $formStartedAt = $request->input('form_started_at');
        if ($formStartedAt) {
            $started = strtotime($formStartedAt);
            $elapsed = time() - $started;
            if ($elapsed < 3) {
                return redirect()->route('donasi-pendidikan.form-donatur')
                    ->with('error', 'Terlalu cepat. Silakan isi form dengan benar.');
            }
        }

        $data = $request->validate([
            'name' => 'nullable|string|max:100',
            'whatsapp' => 'nullable|string|max:20',
            'amount' => 'required|string|max:50',
            'custom_amount' => 'nullable|string|max:50',
            'note' => 'nullable|string|max:500',
            'allow_future_donation_contact' => 'nullable|boolean',
        ]);

        $data['name'] = trim($data['name'] ?? '') ?: null;
        $data['whatsapp'] = trim($data['whatsapp'] ?? '') ?: null;

        // Jika tidak diizinkan dihubungi, hapus nomor WA
        if (!$request->boolean('allow_future_donation_contact')) {
            $data['whatsapp'] = null;
        }

        $amount = $data['amount'] === 'lainnya' && $data['custom_amount']
            ? (int) str_replace(['.', ','], '', $data['custom_amount'])
            : (int) $data['amount'];

        if ($amount < 10000) {
            return back()->withErrors(['amount' => 'Minimal donasi Rp10.000'])->withInput();
        }

        // Generate nomor referensi: DON-YYYYMMDD-XXXX
        $orderId = 'DON-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));

        // Simpan ke database dengan status pending
        $transaction = DonationTransaction::create([
            'order_id' => $orderId,
            'donor_name' => $data['name'],
            'donor_whatsapp' => $data['whatsapp'],
            'support_type' => 'Donasi Pendidikan & Makan Santri',
            'amount' => $amount,
            'note' => $data['note'] ?? null,
            'payment_gateway' => 'manual',
            'status' => 'pending',
        ]);

        session(['donation_data' => [
            'donor_name' => $data['name'],
            'donor_whatsapp' => $data['whatsapp'],
            'support_type' => 'Donasi Pendidikan & Makan Santri',
            'amount' => $amount,
            'note' => $data['note'] ?? null,
            'allow_future_donation_contact' => $request->boolean('allow_future_donation_contact'),
            'order_id' => $orderId,
        ]]);

        return redirect()->route('donasi-pendidikan.form-donatur')
            ->with('success', 'Data donasi berhasil disimpan. Nomor referensi: ' . $orderId . '. Silakan lakukan pembayaran melalui QRIS di bawah.');
    }

    public function previewQrisInline(Request $request, QrisDynamicService $qrisService)
    {
        $data = $request->validate([
            'donor_name' => ['nullable', 'string', 'max:100'],
            'donor_whatsapp' => ['nullable', 'string', 'max:30'],
            'amount' => ['nullable', 'string', 'max:50'],
            'custom_amount' => ['nullable', 'numeric', 'min:1000', 'max:50000000'],
            'note' => ['nullable', 'string', 'max:500'],
            'allow_future_donation_contact' => ['nullable', 'boolean'],
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

        $waNumber = preg_replace('/[^0-9]/', '', $setting?->whatsapp_number ?: '6289661234569');

        $donorName = $data['donor_name'] ?: 'Hamba Allah';
        $donorWhatsapp = $data['donor_whatsapp'] ?: '-';
        $allowContact = $request->boolean('allow_future_donation_contact');
        $allowContactText = $allowContact ? 'Ya' : 'Tidak';

        $confirmMessage = "Assalamu'alaikum, saya sudah melakukan donasi untuk SMA Persis Serang.\n\n"
            . "Nama: {$donorName}\n"
            . "Nomor WA: {$donorWhatsapp}\n"
            . "Nominal: Rp" . number_format($amount, 0, ',', '.') . "\n";

        if ($data['note'] ?? null) {
            $confirmMessage .= "Catatan: {$data['note']}\n";
        }

        $confirmMessage .= "Persetujuan dihubungi lagi: {$allowContactText}\n\n"
            . "Saya lampirkan bukti pembayaran. Terima kasih.";

        $confirmWaUrl = 'https://wa.me/' . $waNumber . '?text=' . urlencode($confirmMessage);

        $response = [
            'success' => (bool) $qrisImage,
            'qris_image' => $qrisImage,
            'is_dynamic' => $isDynamic,
            'static_fallback' => $staticFallback,
            'amount_formatted' => 'Rp' . number_format($amount, 0, ',', '.'),
            'amount_raw' => $amount,
            'whatsapp_url' => $confirmWaUrl,
            'summary' => [
                'donor_name' => $donorName,
                'donor_whatsapp' => $donorWhatsapp,
                'note' => $data['note'] ?? null,
                'allow_future_donation_contact' => $allowContact,
            ],
        ];

        if (!$qrisImage) {
            $response['message'] = 'QRIS belum tersedia. Silakan hubungi admin melalui WhatsApp.';
        } elseif ($staticFallback) {
            $response['message'] = 'Nominal belum otomatis. Silakan masukkan nominal secara manual di aplikasi pembayaran.';
        }

        return response()->json($response);
    }

    public function payment($order_id)
    {
        try {
            $schoolSetting = SchoolSetting::current();
        } catch (\Exception $e) {
            $schoolSetting = null;
        }

        $setting = DonationEducationSetting::activeSetting();

        $transaction = DonationTransaction::where('order_id', $order_id)->firstOrFail();

        $waNumber = preg_replace('/[^0-9]/', '', $setting?->whatsapp_number ?: '6289661234569');

        $confirmMessage = "Assalamu'alaikum, saya sudah melakukan donasi untuk SMA Persis Serang.\n\n"
            . "Order ID: {$transaction->order_id}\n"
            . "Nama: " . ($transaction->donor_name ?: 'Hamba Allah') . "\n"
            . "Jenis Dukungan: {$transaction->support_type}\n"
            . "Nominal: Rp" . number_format($transaction->amount, 0, ',', '.') . "\n"
            . "Status: " . ucfirst($transaction->status) . "\n";

        if ($transaction->note) {
            $confirmMessage .= "Catatan: {$transaction->note}\n";
        }

        $confirmMessage .= "\nTerima kasih.";

        $confirmWaUrl = 'https://wa.me/' . $waNumber . '?text=' . urlencode($confirmMessage);

        return view('pages.payment-donasi', compact('schoolSetting', 'setting', 'transaction', 'confirmWaUrl'));
    }

    public function midtransNotification(Request $request)
    {
        $notification = $request->all();

        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');

        $orderId = $notification['order_id'] ?? null;
        $transactionStatus = $notification['transaction_status'] ?? null;
        $fraudStatus = $notification['fraud_status'] ?? null;
        $statusCode = $notification['status_code'] ?? null;
        $grossAmount = $notification['gross_amount'] ?? null;
        $signatureKey = $notification['signature_key'] ?? null;

        if (!$orderId || !$transactionStatus || !$statusCode || !$grossAmount) {
            return response()->json(['message' => 'Invalid notification'], 400);
        }

        $serverKey = config('midtrans.server_key');
        $calculatedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($signatureKey !== $calculatedSignature) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transaction = DonationTransaction::where('order_id', $orderId)->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $updateData = [
            'raw_notification' => $notification,
            'midtrans_transaction_id' => $notification['transaction_id'] ?? null,
            'midtrans_payment_type' => $notification['payment_type'] ?? null,
            'midtrans_fraud_status' => $fraudStatus,
        ];

        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'accept') {
                $updateData['status'] = 'paid';
                $updateData['paid_at'] = now();
            }
        } elseif ($transactionStatus === 'settlement') {
            $updateData['status'] = 'paid';
            $updateData['paid_at'] = now();
        } elseif ($transactionStatus === 'pending') {
            $updateData['status'] = 'pending';
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel', 'failure'])) {
            $updateData['status'] = $transactionStatus;
        }

        $transaction->update($updateData);

        return response()->json(['message' => 'OK']);
    }

    public function qris(Request $request, QrisDynamicService $qrisService)
    {
        try {
            $schoolSetting = SchoolSetting::current();
        } catch (\Exception $e) {
            $schoolSetting = null;
        }

        $setting = DonationEducationSetting::activeSetting();

        $donationData = $request->session()->get('donation_data');

        if (!$donationData) {
            return redirect()->route('donasi-pendidikan.form-donatur')
                ->with('error', 'Silakan isi form donatur terlebih dahulu.');
        }

        $dynamicQrisBase64 = null;
        $dynamicPayload = null;
        $hasQrisPayload = $setting?->donation_qris_payload && (int) $donationData['amount'] >= 1;

        if ($hasQrisPayload) {
            try {
                $dynamicPayload = $qrisService->generatePayload(
                    $setting->donation_qris_payload,
                    (int) $donationData['amount']
                );

                $dynamicQrisBase64 = $qrisService->generateBase64(
                    $setting->donation_qris_payload,
                    (int) $donationData['amount']
                );
            } catch (\Exception $e) {
                $dynamicQrisBase64 = null;
                $dynamicPayload = null;
            }
        }

        $waNumber = preg_replace('/[^0-9]/', '', $setting?->whatsapp_number ?: '6289661234569');

        $confirmMessage = "Assalamu'alaikum, saya sudah melakukan donasi untuk Program Orang Tua Asuh Santri SMA Persis Serang.\n\n"
            . "Nama: {$donationData['donor_name']}\n"
            . "Nomor WA: {$donationData['donor_whatsapp']}\n"
            . "Jenis Dukungan: {$donationData['support_type']}\n"
            . "Nominal: Rp" . number_format($donationData['amount'], 0, ',', '.') . "\n";

        if ($donationData['note'] ?? null) {
            $confirmMessage .= "Catatan: {$donationData['note']}\n";
        }

        $confirmMessage .= "\nSaya lampirkan bukti pembayaran. Terima kasih.";

        $confirmWaUrl = 'https://wa.me/' . $waNumber . '?text=' . urlencode($confirmMessage);

        return view('pages.qris', compact('schoolSetting', 'setting', 'donationData', 'confirmWaUrl', 'dynamicQrisBase64', 'dynamicPayload', 'hasQrisPayload'));
    }

    public function downloadQris(Request $request, QrisDynamicService $qrisService)
    {
        $setting = DonationEducationSetting::activeSetting();

        $amount = (int) $request->query('amount', 0);

        if ($amount < 1000) {
            $donationData = $request->session()->get('donation_data');
            $amount = $donationData ? (int) $donationData['amount'] : 0;
        }

        if ($amount < 1000) {
            return redirect()->route('donasi-pendidikan.form-donatur')
                ->with('error', 'Nominal tidak valid.');
        }

        if ($setting?->donation_qris_payload) {
            try {
                $pngBinary = $qrisService->generatePngBinary($setting->donation_qris_payload, $amount);

                $filename = 'qris-donasi-sma-persis-serang-' . $amount . '.png';

                return response($pngBinary, 200, [
                    'Content-Type' => 'image/png',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]);
            } catch (\Exception $e) {
                return redirect()->route('donasi-pendidikan.form-donatur')
                    ->with('error', 'Gagal generate QRIS. Silakan coba lagi.');
            }
        }

        if ($setting?->donation_qris_image) {
            $imageUrl = \Illuminate\Support\Facades\Storage::url($setting->donation_qris_image);

            return redirect()->away($imageUrl);
        }

        return redirect()->route('donasi-pendidikan.form-donatur')
            ->with('error', 'QRIS belum tersedia.');
    }

    private function organizationPhotoUrl(OrganizationStructure $structure): ?string
    {
        $photoPath = $structure->person?->photo_path;

        return $photoPath ? asset('storage/' . $photoPath) : null;
    }

    public function teachers(Request $request)
    {
        $tab = $request->query('tab', 'guru');

        try {
            $schoolSetting = SchoolSetting::current();

            $heroImages = SchoolImage::where('is_active', true)
                ->whereHas('categories', fn($q) => $q->where('slug', 'guru'))
                ->orderBy('sort_order')
                ->latest()
                ->get();

            if ($heroImages->isEmpty()) {
                $heroImages = SchoolImage::where('is_active', true)
                    ->whereHas('categories', fn($q) => $q->where('slug', 'fasilitas'))
                    ->orderBy('sort_order')
                    ->latest()
                    ->get();
            }

            $headmaster = Teacher::with(['subjects' => function ($query) {
                    $query->where('school_subjects.is_active', true)
                        ->orderBy('school_subjects.sort_order');
                }])
                ->where('is_active', true)
                ->where('position', 'Kepala Sekolah')
                ->orderBy('sort_order')
                ->first();

            if ($headmaster) {
                $headmaster->setAttribute('label', 'KEPALA SEKOLAH');
            }

            $teachers = Teacher::with(['subjects' => function ($query) {
                    $query->where('school_subjects.is_active', true)
                        ->orderBy('school_subjects.sort_order');
                }])
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('position')
                        ->orWhere('position', '!=', 'Kepala Sekolah');
                })
                ->orderBy('sort_order')
                ->get();

            $groupedByCategory = collect();
            $orphanTeachers = collect();

            foreach ($teachers as $teacher) {
                $teacher->setAttribute('label', 'GURU PENGAMPU');
                $activeSubjects = $teacher->subjects;

                if ($activeSubjects->isEmpty()) {
                    $orphanTeachers->push($teacher);
                    continue;
                }

                $primarySubject = $activeSubjects->first();
                $category = $primarySubject->category;

                if (!$groupedByCategory->has($category)) {
                    $groupedByCategory->put($category, collect([
                        'category_label' => $primarySubject->category_label,
                        'teachers' => collect(),
                    ]));
                }

                $categoryGroup = $groupedByCategory->get($category);
                $alreadyAdded = $categoryGroup['teachers']->first(fn($t) => $t->id === $teacher->id);
                if (!$alreadyAdded) {
                    $categoryGroup['teachers']->push($teacher);
                }
            }

            $groupedByCategory = $groupedByCategory->sortKeys();

            $websitePage = WebsitePage::key('teachers');
        } catch (\Exception $e) {
            $schoolSetting = null;
            $headmaster = null;
            $groupedByCategory = collect();
            $orphanTeachers = collect();
            $websitePage = null;
            $heroImages = collect();
        }

        $organisasi = null;
        $strukturWebsitePage = null;

        if ($tab === 'struktur') {
            $organisasi = $this->getOrganisasiData();
            try {
                $strukturWebsitePage = WebsitePage::key('struktur-organisasi');
            } catch (\Exception $e) {
                $strukturWebsitePage = null;
            }
        }

        return view('pages.teachers', compact('schoolSetting', 'websitePage', 'headmaster', 'groupedByCategory', 'orphanTeachers', 'heroImages', 'tab', 'organisasi', 'strukturWebsitePage'));
    }
}
