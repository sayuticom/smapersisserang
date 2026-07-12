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

    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-24">

        <div class="grid lg:grid-cols-2 gap-10 lg:gap-14 items-center">

            @if($heroBg)
                <div class="flex justify-center">
                    <img src="{{ $heroBg }}" alt="{{ $heroTitle }}" class="w-full max-w-md lg:max-w-lg aspect-square object-cover rounded-3xl shadow-2xl border border-amber-300/30">
                </div>
            @endif

            <div class="text-center lg:text-left">
                <div class="inline-flex items-center gap-2 rounded-full border border-amber-300/70 bg-emerald-950/60 px-4 py-2 text-sm font-semibold text-amber-300 shadow-lg shadow-emerald-950/20 backdrop-blur-sm">
                    <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                    LADANG AMAL JARIYAH
                </div>
                <h1 class="mx-auto mt-6 max-w-4xl text-4xl font-bold leading-tight text-white sm:text-5xl lg:text-6xl lg:mx-0">
                    {{ $heroTitle }}
                </h1>
                <p class="mx-auto mt-5 max-w-2xl text-lg font-semibold leading-8 text-amber-300 sm:text-xl lg:mx-0">
                    {{ $heroSubtitle }}
                </p>

                <div class="mx-auto mt-8 max-w-3xl rounded-2xl border border-amber-300/30 bg-white/10 p-5 text-emerald-50 shadow-lg shadow-emerald-950/20 backdrop-blur lg:mx-0">
                    <p class="text-lg font-semibold leading-8">
                        “{{ $hadithText }}”
                    </p>
                    <p class="mt-2 text-sm font-medium text-amber-300">{{ $hadithSource }}</p>
                </div>

                <p class="mx-auto mt-8 max-w-2xl text-base leading-7 text-emerald-100/90 lg:mx-0">
                    Bantuan Bapak/Ibu akan digunakan untuk kebutuhan makan harian, perlengkapan sekolah, perlengkapan asrama, kesehatan ringan, dan pembinaan akhlak para santri.
                </p>

                <div class="mt-6 flex flex-col items-center gap-3 sm:flex-row lg:justify-start">
                    <a href="{{ route('donasi-pendidikan.form-donatur') }}"
                       class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-yellow-300 bg-gradient-to-r from-amber-400 to-yellow-500 px-7 py-4 text-sm font-bold text-white shadow-lg shadow-yellow-500/30 ring-1 ring-yellow-200/20 transition-all duration-200 hover:-translate-y-0.5 hover:from-amber-300 hover:to-yellow-400 hover:shadow-xl hover:shadow-yellow-500/40 active:translate-y-0 active:shadow-lg sm:w-auto">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                        </svg>
                        Donasi Sekarang
                    </a>
                    <a href="{{ route('donasi-pendidikan.sebarkan') }}"
                       class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-amber-300/80 px-7 py-4 text-sm font-bold text-white transition hover:bg-white/10 sm:w-auto">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z"/>
                        </svg>
                        {{ $shareButtonText }}
                    </a>
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

@endsection
