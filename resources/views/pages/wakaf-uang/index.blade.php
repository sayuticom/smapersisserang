@extends('layouts.public')

@php
    $schoolName = $schoolSetting->school_name ?? 'SMA Persis Serang';
    $heroTitle = $setting?->hero_title ?: 'Wakaf Uang';
    $heroSubtitle = $setting?->hero_subtitle ?: 'Salurkan wakaf uang Anda untuk pengembangan pendidikan, sarana prasarana, dan kemaslahatan SMA Persis Serang.';
    $introTitle = $setting?->intro_title ?: 'Apa Itu Wakaf Uang?';
    $introText = $setting?->intro_text ?: 'Wakaf uang adalah wakaf yang dilakukan seseorang atau sekelompok orang dalam bentuk uang tunai. Wakaf uang hukumnya boleh berdasarkan fatwa MUI dan merupakan salah satu instrumen pemberdayaan ekonomi umat yang fleksibel.';
    $purposeText = $setting?->waqf_purpose_text ?: 'Wakaf uang ini dihimpun untuk mendukung pengembangan, pembangunan, operasional pendidikan, sarana prasarana, dan kemaslahatan SMA Persis Serang sesuai kebutuhan prioritas sekolah.';
    $ikrarText = $setting?->ikrar_text ?: 'Dengan penuh kesadaran dan keikhlasan, saya berikrar mewakafkan uang ini untuk SMA Persis Serang dan menyerahkan pengelolaannya kepada pihak sekolah/nazhir sesuai kebutuhan prioritas.';
    $whatsappNumber = preg_replace('/[^0-9]/', '', $setting?->whatsapp_number ?: '6289661234569');
    $whatsappButtonText = 'Hubungi WA SMA Persis Serang';
    $whatsappMessage = $setting?->whatsapp_message_template ?: 'Assalamu\'alaikum, saya ingin menanyakan program wakaf uang SMA Persis Serang';
    $waUrl = 'https://wa.me/' . $whatsappNumber . '?text=' . urlencode($whatsappMessage);
@endphp

@section('title', 'Wakaf Uang Pendidikan - ' . $schoolName)

@php
    $waqfOgTitle = 'Wakaf Uang Pendidikan - ' . $schoolName;
    $waqfOgDesc = $setting->hero_subtitle ?: 'Salurkan wakaf uang terbaik untuk mendukung pendidikan, sarana prasarana, pembinaan santri, dan kemaslahatan SMA Persis Serang.';
    $waqfOgImage = $setting?->hero_image
        ? asset('storage/' . $setting->hero_image)
        : ($schoolSetting?->meta_image
            ? asset('storage/' . $schoolSetting->meta_image)
            : asset('images/og-sma-persis-serang.jpg'));
@endphp

@section('meta')
<meta name="description" content="{{ $waqfOgDesc }}">
<meta property="og:title" content="{{ $waqfOgTitle }}">
<meta property="og:description" content="{{ $waqfOgDesc }}">
<meta property="og:image" content="{{ $waqfOgImage }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $waqfOgTitle }}">
<meta name="twitter:description" content="{{ $waqfOgDesc }}">
<meta name="twitter:image" content="{{ $waqfOgImage }}">
@endsection

@section('content')
<section class="relative overflow-hidden bg-gradient-to-br from-emerald-950 via-emerald-900 to-emerald-800">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:py-20">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-1.5 rounded-full border border-amber-300/70 bg-emerald-950/60 px-3 py-1.5 text-[11px] font-semibold text-amber-300 backdrop-blur sm:gap-2 sm:px-4 sm:py-2 sm:text-sm">
                <span class="h-1.5 w-1.5 rounded-full bg-amber-300 sm:h-2 sm:w-2"></span>
                WAKAF UANG
            </div>
            <h1 class="mt-4 font-serif text-3xl font-bold leading-tight text-white sm:mt-5 sm:text-5xl lg:text-6xl">
                {{ $heroTitle }}
            </h1>
            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-white/80 sm:mt-4 sm:text-lg">
                {{ $heroSubtitle }}
            </p>
            <a href="{{ route('wakaf-uang.form') }}"
               class="mt-6 inline-flex items-center gap-2 rounded-xl bg-amber-500 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-amber-500/30 transition-all hover:bg-amber-400 hover:shadow-amber-400/30 sm:mt-8 sm:text-base">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/>
                </svg>
                Wakaf Sekarang
            </a>
        </div>
    </div>
</section>

<section class="bg-[#FBF7EF] py-12 sm:py-20 lg:py-28">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-2 lg:gap-16">
            <div>
                <h2 class="font-serif text-2xl font-bold text-[#052E1F] sm:text-3xl lg:text-4xl">{{ $introTitle }}</h2>
                <p class="mt-4 text-sm leading-relaxed text-gray-600 sm:text-base">{{ $introText }}</p>

                <div class="mt-6 rounded-2xl border border-emerald-200/60 bg-white p-5 shadow-lg shadow-emerald-950/5 sm:p-8">
                    <h3 class="font-serif text-lg font-bold text-[#052E1F] sm:text-xl">Tujuan Wakaf</h3>
                    <div class="mt-3 text-sm leading-relaxed text-gray-600 sm:text-base">
                        {{ $purposeText }}
                    </div>
                </div>

                <div class="mt-6 rounded-2xl border border-amber-200/60 bg-amber-50/80 p-5 shadow-lg shadow-emerald-950/5 sm:p-8">
                    <h3 class="font-serif text-lg font-bold text-[#052E1F] sm:text-xl">Ikrar Wakaf</h3>
                    <div class="mt-3 text-sm leading-relaxed text-gray-700 sm:text-base">
                        {{ $ikrarText }}
                    </div>
                </div>

                <a href="{{ route('wakaf-uang.form') }}"
                   class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-amber-500 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-amber-500/30 transition-all hover:bg-amber-400 hover:shadow-amber-400/30 sm:text-base">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/>
                    </svg>
                    Wakaf Sekarang
                </a>
            </div>

            <div class="space-y-6">
                <div class="rounded-2xl border border-emerald-200/60 bg-white p-5 shadow-lg shadow-emerald-950/5 sm:p-8">
                    <h3 class="font-serif text-lg font-bold text-[#052E1F] sm:text-xl">Mengapa Wakaf Uang?</h3>
                    <ul class="mt-4 space-y-3">
                        <li class="flex gap-3 text-sm text-gray-600 sm:text-base">
                            <span class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700">1</span>
                            <span>Pahala terus mengalir meskipun telah meninggal dunia (wakaf termasuk amal jariyah).</span>
                        </li>
                        <li class="flex gap-3 text-sm text-gray-600 sm:text-base">
                            <span class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700">2</span>
                            <span>Modal sosial untuk pembangunan dan pengembangan pendidikan Islam.</span>
                        </li>
                        <li class="flex gap-3 text-sm text-gray-600 sm:text-base">
                            <span class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700">3</span>
                            <span>Nilai wakaf tetap terjaga dan dikelola secara produktif oleh nazhir.</span>
                        </li>
                        <li class="flex gap-3 text-sm text-gray-600 sm:text-base">
                            <span class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700">4</span>
                            <span>Fleksibel — dapat diwakafkan kapan saja dengan nominal berapa pun.</span>
                        </li>
                    </ul>
                </div>

                <div class="rounded-2xl border border-emerald-200/60 bg-white p-5 shadow-lg shadow-emerald-950/5 sm:p-8">
                    <h3 class="font-serif text-lg font-bold text-[#052E1F] sm:text-xl">Hubungi Kami</h3>
                    <p class="mt-2 text-sm text-gray-500">Ada pertanyaan seputar wakaf uang? Silakan hubungi kami melalui WhatsApp.</p>
                    <a href="{{ $waUrl }}" target="_blank"
                       class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition-all hover:bg-emerald-500 sm:text-base">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Hubungi via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
