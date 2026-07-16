@extends('layouts.public')

@php
    $schoolName = $schoolSetting->school_name ?? 'SMA Persis Serang';
    $donationHeroImage = $setting?->hero_image
        ? asset('storage/' . $setting->hero_image)
        : ($schoolSetting?->meta_image ? asset('storage/' . $schoolSetting->meta_image) : asset('images/og-sma-persis-serang.jpg'));
    $heroBg = $setting?->hero_image
        ? asset('storage/' . $setting->hero_image)
        : null;
    $sectionImage = $setting?->section_image ? asset('storage/' . $setting->section_image) : null;
    $heroTitle = $setting?->hero_title ?: 'Donasi Pendidikan & Makan Santri';
    $heroSubtitle = $setting?->hero_subtitle ?: 'Bersama mendukung pendidikan gratis, kebutuhan makan, asrama, dan pembinaan santri SMA Persis Serang.';
    $hadithText = $setting?->hadith_text ?: 'Barangsiapa menempuh jalan untuk mencari ilmu, Allah akan mudahkan baginya jalan menuju surga.';
    $hadithSource = $setting?->hadith_source ?: 'HR. Muslim';
    $introTitle = $setting?->intro_title ?: 'Menopang Pendidikan dan Kebutuhan Harian Santri';
    $introText = $setting?->intro_text ?: 'SMA Persis Serang berikhtiar menghadirkan pendidikan yang terjangkau, bahkan menggratiskan biaya pendidikan dan biaya makan asrama bagi anak-anak yang membutuhkan. Program ini menjadi kesempatan bagi kaum muslimin untuk ikut menyuburkan ladang pahala melalui sedekah dan infak pendidikan.';
    $invitationText = $setting?->invitation_text ?: 'Yang memiliki beras, bisa menitipkan berasnya. Yang memiliki telur, bisa menitipkan telurnya. Yang memiliki sayuran, bisa menitipkan sayurannya. Apabila diperlukan, insyaAllah kami siap menjemput donasi ke tempat Bapak/Ibu/Saudara/i.';
    $whatsappNumber = preg_replace('/[^0-9]/', '', $setting?->whatsapp_number ?: '6289661234569');
    $whatsappButtonText = $setting?->whatsapp_button_text ?: 'Hubungi WA SMA Persis Serang';
    $whatsappMessage = $setting?->whatsapp_message ?: 'Assalamu\'alaikum, saya ingin berdonasi untuk program pendidikan dan makan santri SMA Persis Serang';
    $shareButtonText = $setting?->share_button_text ?: 'Sebarkan Informasi Kebaikan Ini';
    $waUrl = 'https://wa.me/' . $whatsappNumber . '?text=' . urlencode($whatsappMessage);
    $heroQrisUrl = null;      // Uploaded hero image file (for display)
    $downloadQrisUrl = null;  // Generated QR from payload for download (lightweight, without amount)

    if ($setting?->donation_qris_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($setting->donation_qris_image)) {
        $heroQrisUrl = asset('storage/' . $setting->donation_qris_image);
    }

    if ($setting?->donation_qris_payload) {
        try {
            $qrCode = new \Endroid\QrCode\QrCode(
                data: $setting->donation_qris_payload,
                encoding: new \Endroid\QrCode\Encoding\Encoding('UTF-8'),
                errorCorrectionLevel: \Endroid\QrCode\ErrorCorrectionLevel::Medium,
                size: 500,
                margin: 2,
                roundBlockSizeMode: \Endroid\QrCode\RoundBlockSizeMode::Margin,
                foregroundColor: new \Endroid\QrCode\Color\Color(0, 0, 0),
                backgroundColor: new \Endroid\QrCode\Color\Color(255, 255, 255),
            );
            $writer = new \Endroid\QrCode\Writer\PngWriter();
            $result = $writer->write($qrCode);
            $downloadQrisUrl = 'data:image/png;base64,' . base64_encode($result->getString());
        } catch (\Exception $e) {
            $downloadQrisUrl = null;
        }
    }

    if (!$downloadQrisUrl) {
        $downloadQrisUrl = $heroQrisUrl;
    }

    $hasQris = (bool) $heroQrisUrl || (bool) $downloadQrisUrl || (bool) ($setting?->donation_qris_payload);
    $defaultDonationItems = [
        ['title' => 'Beras', 'description' => 'Mendukung kebutuhan pokok makan harian santri.'],
        ['title' => 'Telur', 'description' => 'Sumber protein untuk menu makan santri.'],
        ['title' => 'Sayuran', 'description' => 'Membantu pemenuhan gizi dan menu sehat.'],
        ['title' => 'Lauk pauk', 'description' => 'Untuk melengkapi kebutuhan makan harian.'],
        ['title' => 'Sembako', 'description' => 'Bahan pokok dapur dan kebutuhan asrama.'],
        ['title' => 'Donasi uang', 'description' => 'Disalurkan untuk pendidikan dan makan santri.'],
        ['title' => 'Kebutuhan dapur/asrama lainnya', 'description' => 'Dapat dikonsultasikan langsung dengan pihak sekolah.'],
    ];
    $donationItems = collect($setting?->donation_items ?: [])
        ->map(function ($item) {
            if (is_array($item)) {
                return [
                    'title' => $item['title'] ?? '',
                    'description' => $item['description'] ?? 'Dapat disalurkan untuk mendukung pendidikan dan makan santri.',
                ];
            }

            return [
                'title' => $item,
                'description' => 'Dapat disalurkan untuk mendukung pendidikan dan makan santri.',
            ];
        })
        ->filter(fn($item) => trim($item['title']) !== '')
        ->values()
        ->all();

    if (empty($donationItems)) {
        $donationItems = $defaultDonationItems;
    }
@endphp

@section('title', $heroTitle . ' - ' . $schoolName)

@section('meta')
    <meta name="description" content="Bersama mendukung pendidikan gratis, makan, asrama, dan pembinaan santri SMA Persis Serang.">

    <meta property="og:title" content="Donasi Pendidikan &amp; Orang Tua Asuh Santri - SMA Persis Serang">
    <meta property="og:description" content="Bersama mendukung pendidikan gratis, makan, asrama, dan pembinaan santri SMA Persis Serang.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $donationHeroImage }}?v={{ date('Ymd') }}">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $heroTitle }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Donasi Pendidikan &amp; Orang Tua Asuh Santri - SMA Persis Serang">
    <meta name="twitter:description" content="Bersama mendukung pendidikan gratis, makan, asrama, dan pembinaan santri SMA Persis Serang.">
    <meta name="twitter:image" content="{{ $donationHeroImage }}?v={{ date('Ymd') }}">
@endsection

@section('content')

<section class="relative isolate overflow-hidden bg-gradient-to-br from-[#052E1F] via-[#0A4F2B] to-[#0F6B3A]">
    <div class="absolute inset-0 opacity-[0.06]"
         style="background-image: linear-gradient(135deg, rgba(255,255,255,.5) 1px, transparent 1px); background-size: 42px 42px;"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/30 via-transparent to-emerald-950/20"></div>

    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-4 pb-6 lg:pt-6 lg:pb-10">

        <div class="grid md:grid-cols-[2fr_3fr] gap-8 lg:gap-10 items-start">

            @if($hasQris)
            <div x-data="qrisExpress()" class="flex flex-col items-center md:items-stretch">
                <div class="w-full max-w-[320px] sm:max-w-[400px] md:max-w-[480px] mx-auto relative">
                    <template x-if="!previewData && !isLoading && qrisImageUrl">
                        <div class="qris-static-wrap">
                            <img :src="qrisImageUrl" alt="QRIS Donasi Pendidikan"
                                 class="w-full h-auto object-contain rounded-2xl border-2 border-white/20 bg-white p-2 shadow-xl">
                        </div>
                    </template>

                    <template x-if="previewData">
                        <div class="text-center">
                            <div class="text-sm font-bold text-white/80 mb-1" x-text="previewData?.merchant_name || ''"></div>
                            <img :src="previewData?.qris_image" alt="QRIS Donasi Pendidikan"
                                 class="w-full h-auto object-contain rounded-2xl border-2 border-white/20 bg-white p-2 shadow-xl">
                        </div>
                    </template>

                    <template x-if="isLoading">
                        <div class="absolute inset-0 flex items-center justify-center bg-[#052E1F]/80 rounded-2xl z-10">
                            <div class="flex items-center gap-2 text-sm text-amber-300/70">
                                <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span>Memproses QRIS...</span>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-2 w-full max-w-[320px] sm:max-w-[400px] md:max-w-[480px] mx-auto">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-1.5">
                        <template x-for="(opt, i) in nominalOptions" :key="i">
                            <button @click="selectNominal(opt.value)"
                                    class="rounded-lg border px-2 py-1.5 text-sm font-semibold transition"
                                    :class="selectedNominal === opt.value ? 'border-amber-400 bg-amber-400/20 text-amber-300' : 'border-white/20 text-white/80 hover:border-white/40 hover:text-white'"
                                    x-text="opt.label">
                            </button>
                        </template>
                        <button @click="selectCustom()"
                                class="rounded-lg border px-2 py-1.5 text-sm font-semibold transition"
                                :class="selectedNominal === 'lainnya' ? 'border-amber-400 bg-amber-400/20 text-amber-300' : 'border-white/20 text-white/80 hover:border-white/40 hover:text-white'">
                            Lainnya
                        </button>
                    </div>

                    <div x-show="selectedNominal === 'lainnya'" x-transition class="mt-1">
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-white/50">Rp</span>
                            <input type="text" inputmode="numeric" x-model="customAmount"
                                   x-on:input.debounce.400ms="generateQris()"
                                   class="w-full rounded-lg border border-white/20 bg-white/10 py-2 pl-8 pr-3 text-sm text-white placeholder-white/40 focus:border-amber-300 focus:ring-1 focus:ring-amber-300/30"
                                   placeholder="0">
                        </div>
                        <p x-show="customAmountError" class="mt-1 text-sm text-red-400" x-text="customAmountError"></p>
                    </div>

                    <div x-show="previewData" class="mt-2">
                        <div class="rounded-lg bg-white/10 backdrop-blur px-4 py-2.5 text-center border border-white/10">
                            <p class="text-xs font-bold uppercase tracking-wide text-amber-300">Total Pembayaran</p>
                            <p class="text-xl font-bold text-white mt-0.5" x-text="previewData.total_transfer_formatted"></p>
                            <div x-show="previewData.admin_fee !== undefined" class="mt-1.5 flex items-center justify-center gap-1 text-[11px] text-white/75 leading-tight">
                                <span>Biaya Admin: <span x-text="'Rp' + previewData.admin_fee?.toLocaleString('id-ID')"></span></span>
                                <span class="text-white/30">·</span>
                                <span>Kode Unik: <span x-text="'Rp' + previewData.unique_code_raw"></span></span>
                            </div>
                        </div>
                    </div>

                    <div x-show="error && !isLoading" class="mt-2">
                        <div class="rounded-lg bg-red-500/20 px-4 py-3 text-sm text-red-300 border border-red-500/30">
                            <p x-text="error"></p>
                        </div>
                    </div>

                    <div class="mt-2 flex gap-2">
                        <a x-show="downloadUrl" :href="downloadUrl"
                           class="flex-1 rounded-lg border border-amber-400/50 bg-amber-400/10 px-4 py-2.5 text-center text-sm font-semibold text-amber-300 transition hover:bg-amber-400/20">
                            Download QRIS
                        </a>
                        <button x-show="!downloadUrl && downloadQrisUrl" @click="downloadBase64()"
                                class="flex-1 rounded-lg border border-amber-400/50 bg-amber-400/10 px-4 py-2.5 text-center text-sm font-semibold text-amber-300 transition hover:bg-amber-400/20">
                            Download QRIS
                        </button>
                        <button x-show="previewData" @click="resetToStatic()"
                                class="flex-1 rounded-lg border border-white/20 bg-white/5 px-4 py-2.5 text-center text-sm font-semibold text-white/70 transition hover:bg-white/10">
                            Reset
                        </button>
                    </div>
                </div>
            </div>
            @elseif($heroBg)
            <div class="flex flex-col items-center">
                <div class="w-full max-w-md lg:max-w-lg">
                    <img src="{{ $heroBg }}" alt="{{ $heroTitle }}" class="w-full h-auto object-contain rounded-3xl shadow-2xl border border-amber-300/30">
                </div>
                @if($setting?->donation_qris_image)
                <a href="{{ route('donasi-pendidikan.qris.download-hero') }}"
                   class="mt-4 inline-flex w-full max-w-md lg:max-w-lg items-center justify-center gap-2 rounded-xl border border-white/30 px-7 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    Download QRIS
                </a>
                @endif
            </div>
            @else
            <div></div>
            @endif

            <div>
                <div class="text-center md:text-left">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full border border-amber-300/70 bg-emerald-950/60 px-3 py-1.5 text-xs font-semibold text-amber-300 shadow-lg shadow-emerald-950/20 backdrop-blur-sm">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-300"></span>
                            LADANG AMAL JARIYAH
                        </div>
                        <h1 class="mx-auto mt-4 max-w-4xl text-3xl font-bold leading-tight text-white sm:text-4xl lg:text-5xl md:mx-0">
                            {{ $heroTitle }}
                        </h1>
                        <p class="mx-auto mt-3 max-w-2xl text-base font-semibold leading-7 text-amber-300 sm:text-lg lg:text-xl md:mx-0">
                            {{ $heroSubtitle }}
                        </p>
                    </div>

                    <div class="mx-auto mt-4 max-w-3xl rounded-2xl border border-amber-300/30 bg-white/10 p-4 text-emerald-50 shadow-lg shadow-emerald-950/20 backdrop-blur md:mx-0">
                        <p class="text-sm leading-7">
                            &ldquo;{{ $hadithText }}&rdquo;
                        </p>
                        <p class="mt-1.5 text-xs font-medium text-amber-300">{{ $hadithSource }}</p>
                    </div>

                    <p class="mx-auto mt-4 max-w-2xl text-sm leading-6 text-emerald-100/90 md:mx-0">
                        Bantuan Bapak/Ibu akan digunakan untuk kebutuhan makan harian, perlengkapan sekolah, perlengkapan asrama, kesehatan ringan, dan pembinaan akhlak para santri.
                    </p>

                    <div class="mx-auto mt-4 max-w-2xl md:mx-0">
                        <p class="text-sm leading-6 text-emerald-100/80">
                            Jika Anda membutuhkan tanda bukti penerimaan donasi, silakan gunakan layanan berikut.
                        </p>
                        <div class="mt-3 flex flex-col sm:flex-row items-center justify-center md:justify-start gap-3 sm:gap-3">
                            <a href="{{ route('public.infaq-money') }}"
                               class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-amber-400 to-amber-600 px-5 py-2.5 text-sm font-bold text-emerald-950 shadow-lg shadow-amber-900/20 transition hover:from-amber-300 hover:to-amber-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Infaq Uang
                            </a>
                            <a href="{{ route('public.infaq-goods') }}"
                               class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-900/20 transition hover:from-emerald-400 hover:to-emerald-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                Infaq Barang
                            </a>
                            <a href="{{ route('donasi-pendidikan.sebarkan') }}"
                               class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-xl border border-amber-400/50 bg-amber-400/10 px-5 py-2.5 text-sm font-semibold text-amber-300 shadow-lg shadow-amber-900/20 transition hover:bg-amber-400/20">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z"/>
                                </svg>
                                Sebar Kebaikan
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>

<section class="bg-[#FBF7EF] py-16 lg:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-bold uppercase tracking-[0.24em] text-[#D4A017]">BENTUK DUKUNGAN</p>
            <h2 class="mt-3 font-serif text-3xl font-bold text-[#052E1F] sm:text-4xl">Apa Saja yang Bisa Didukung?</h2>
        </div>
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div class="group rounded-2xl border border-emerald-100 bg-white p-6 shadow-md shadow-emerald-950/5 transition hover:-translate-y-1 hover:border-amber-200 hover:shadow-lg">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-[#052E1F]">Makan Harian Santri</h3>
                <p class="mt-2 text-sm leading-6 text-gray-600">Mendukung kebutuhan gizi dan asupan makan sehari-hari santri di asrama.</p>
            </div>
            <div class="group rounded-2xl border border-emerald-100 bg-white p-6 shadow-md shadow-emerald-950/5 transition hover:-translate-y-1 hover:border-amber-200 hover:shadow-lg">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-[#052E1F]">Pendidikan Gratis</h3>
                <p class="mt-2 text-sm leading-6 text-gray-600">Menanggung biaya SPP dan operasional belajar santri yang tidak mampu.</p>
            </div>
            <div class="group rounded-2xl border border-emerald-100 bg-white p-6 shadow-md shadow-emerald-950/5 transition hover:-translate-y-1 hover:border-amber-200 hover:shadow-lg">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-sky-100 text-sky-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-[#052E1F]">Perlengkapan Sekolah</h3>
                <p class="mt-2 text-sm leading-6 text-gray-600">Menyediakan buku, alat tulis, seragam, dan perlengkapan belajar lainnya.</p>
            </div>
            <div class="group rounded-2xl border border-emerald-100 bg-white p-6 shadow-md shadow-emerald-950/5 transition hover:-translate-y-1 hover:border-amber-200 hover:shadow-lg">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 text-purple-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-[#052E1F]">Perlengkapan Asrama</h3>
                <p class="mt-2 text-sm leading-6 text-gray-600">Membantu kebutuhan kasur, lemari, perlengkapan mandi, dan kebutuhan asrama.</p>
            </div>
            <div class="group rounded-2xl border border-emerald-100 bg-white p-6 shadow-md shadow-emerald-950/5 transition hover:-translate-y-1 hover:border-amber-200 hover:shadow-lg sm:col-span-2 lg:col-span-1">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-rose-100 text-rose-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-[#052E1F]">Pembinaan Akhlak & Karakter</h3>
                <p class="mt-2 text-sm leading-6 text-gray-600">Mendukung kegiatan kajian, mentoring, dan pembinaan karakter santri.</p>
            </div>
        </div>
    </div>
</section>

<section class="bg-[#FBF7EF] py-16 lg:py-20">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[0.95fr_1.05fr] lg:items-center lg:px-8">
        <div>
            <p class="text-sm font-bold uppercase tracking-[0.24em] text-[#D4A017]">Tentang Program</p>
            <h2 class="mt-3 font-serif text-3xl font-bold text-[#052E1F] sm:text-4xl">
                {{ $introTitle }}
            </h2>
            @if($sectionImage)
                <div class="mt-8 overflow-hidden rounded-2xl border border-amber-100 bg-white shadow-xl shadow-emerald-950/10">
                    <img src="{{ $sectionImage }}" alt="{{ $introTitle }}" class="aspect-[4/3] w-full object-cover">
                </div>
            @endif
        </div>
        <p class="text-lg leading-8 text-emerald-900/75">
            {{ $introText }}
        </p>
    </div>
</section>

<section class="bg-white py-16 lg:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-bold uppercase tracking-[0.24em] text-[#D4A017]">Bentuk Donasi</p>
            <h2 class="mt-3 font-serif text-3xl font-bold text-[#052E1F] sm:text-4xl">Titipkan Kebaikan Terbaik</h2>
        </div>

        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($donationItems as $item)
                <div class="rounded-2xl border border-emerald-100 bg-white p-6 shadow-md shadow-emerald-950/5 transition hover:-translate-y-1 hover:border-amber-200 hover:shadow-lg">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-[#0F6B3A]/10 text-[#0F6B3A]">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#052E1F]">{{ $item['title'] }}</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600">{{ $item['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-[#FBF7EF] py-16 lg:py-20">
    <div class="mx-auto max-w-5xl px-4 text-center sm:px-6 lg:px-8">
        <div class="rounded-2xl border border-amber-200 bg-white p-8 shadow-lg shadow-emerald-950/5 lg:p-12">
            <p class="text-lg leading-9 text-emerald-900/75">
                {{ $invitationText }}
            </p>
        </div>
    </div>
</section>

<section class="bg-[#052E1F] py-16 lg:py-20">
    <div class="mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
        <p class="text-sm font-bold uppercase tracking-[0.24em] text-amber-300">Kontak Donasi</p>
        <h2 class="mt-3 font-serif text-3xl font-bold text-white sm:text-4xl">Siap Berdonasi atau Bertanya?</h2>
        <p class="mt-4 text-base leading-7 text-emerald-100/80">
            Silakan hubungi kami untuk koordinasi penyerahan donasi atau penjemputan bantuan.
        </p>
        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
            <a href="{{ $waUrl }}" target="_blank" rel="noopener"
               class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-amber-400 to-amber-600 px-7 py-4 text-sm font-bold text-emerald-950 shadow-lg shadow-amber-900/20 transition hover:from-amber-300 hover:to-amber-500">
                {{ $whatsappButtonText }}
            </a>
            <a href="{{ route('donasi-pendidikan.sebarkan') }}"
               class="inline-flex items-center justify-center rounded-xl border border-amber-300/80 px-7 py-4 text-sm font-bold text-white transition hover:bg-white/10">
                {{ $shareButtonText }}
            </a>
        </div>
    </div>
</section>

@push('scripts')
<script>
    function qrisExpress() {
        return {
            selectedNominal: null,
            customAmount: '',
            isLoading: false,
            previewData: null,
            error: null,
            customAmountError: '',
            heroQrisUrl: @json($heroQrisUrl),
            heroBgUrl: @json($heroBg),
            downloadQrisUrl: @json($downloadQrisUrl),

            nominalOptions: [
                { value: '10000', label: 'Rp10.000' },
                { value: '20000', label: 'Rp20.000' },
                { value: '30000', label: 'Rp30.000' },
                { value: '50000', label: 'Rp50.000' },
                { value: '100000', label: 'Rp100.000' },
                { value: '200000', label: 'Rp200.000' },
                { value: '300000', label: 'Rp300.000' },
                { value: '500000', label: 'Rp500.000' },
            ],

            get qrisImageUrl() {
                if (this.previewData && this.previewData.qris_image) {
                    return this.previewData.qris_image;
                }
                return this.heroQrisUrl || this.heroBgUrl || this.downloadQrisUrl;
            },

            get downloadUrl() {
                if (this.previewData && this.previewData.is_dynamic) {
                    return '/donasi-pendidikan/qris/download?amount=' + this.previewData.amount_raw;
                }
                if (this.downloadQrisUrl && this.downloadQrisUrl.startsWith('data:')) {
                    return null;
                }
                return this.downloadQrisUrl || null;
            },

            selectNominal(value) {
                this.selectedNominal = value;
                this.customAmount = '';
                this.error = null;
                this.customAmountError = '';
                this.generateQris();
            },

            selectCustom() {
                this.selectedNominal = 'lainnya';
                this.previewData = null;
                this.error = null;
                this.customAmountError = '';
            },

            generateQris() {
                const amount = this.getNumericAmount();

                if (amount === 0) {
                    this.error = null;
                    this.previewData = null;
                    return;
                }

                if (amount < 1000) {
                    this.customAmountError = 'Minimal donasi Rp1.000';
                    this.previewData = null;
                    return;
                }

                this.customAmountError = '';
                this.isLoading = true;
                this.error = null;
                this.previewData = null;

                const payload = new FormData();
                payload.append('amount', this.selectedNominal);
                payload.append('custom_amount', this.customAmount);

                fetch('{{ route('donasi-pendidikan.qris.preview') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: payload,
                })
                .then(res => res.json())
                .then(data => {
                    this.isLoading = false;
                    if (data.success) {
                        this.previewData = data;
                    } else {
                        this.error = data.message || 'QRIS belum tersedia. Silakan hubungi admin.';
                    }
                })
                .catch(() => {
                    this.isLoading = false;
                    this.error = 'Terjadi kesalahan. Silakan coba lagi.';
                });
            },

            getNumericAmount() {
                if (this.selectedNominal === 'lainnya') {
                    return parseInt((this.customAmount || '').replace(/[^\d]/g, '') || 0);
                }
                if (this.selectedNominal) {
                    return parseInt(this.selectedNominal);
                }
                return 0;
            },

            downloadBase64() {
                const link = document.createElement('a');
                link.href = this.downloadQrisUrl;
                link.download = 'QRIS-Donasi-Pendidikan.png';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            },

            resetToStatic() {
                this.selectedNominal = null;
                this.customAmount = '';
                this.isLoading = false;
                this.previewData = null;
                this.error = null;
                this.customAmountError = '';
            },
        };
    }
</script>
@endpush

@endsection
