@extends('layouts.public')

@php
    $schoolName = $schoolSetting->school_name ?? 'SMA Persis Serang';
    $heroBg = $schoolSetting?->building_image_path ? asset('storage/' . $schoolSetting->building_image_path) : null;
    $waUrl = 'https://wa.me/6289661234569?text=Assalamu%27alaikum%2C%20saya%20ingin%20berdonasi%20untuk%20program%20pendidikan%20dan%20makan%20santri%20SMA%20Persis%20Serang';
    $shareText = 'Assalamu’alaikum. Mari ikut mendukung program pendidikan gratis dan makan santri SMA Persis Serang. Donasi bisa berupa beras, telur, sayur, sembako, atau uang. Hubungi WA 6289661234569.';
    $shareUrl = 'https://wa.me/?text=' . urlencode($shareText . ' ' . route('donasi-pendidikan'));
    $donationItems = [
        ['title' => 'Beras', 'description' => 'Mendukung kebutuhan pokok makan harian santri.'],
        ['title' => 'Telur', 'description' => 'Sumber protein untuk menu makan santri.'],
        ['title' => 'Sayuran', 'description' => 'Membantu pemenuhan gizi dan menu sehat.'],
        ['title' => 'Lauk pauk', 'description' => 'Untuk melengkapi kebutuhan makan harian.'],
        ['title' => 'Sembako', 'description' => 'Bahan pokok dapur dan kebutuhan asrama.'],
        ['title' => 'Donasi uang', 'description' => 'Disalurkan untuk pendidikan dan makan santri.'],
        ['title' => 'Kebutuhan dapur/asrama lainnya', 'description' => 'Dapat dikonsultasikan langsung dengan pihak sekolah.'],
    ];
@endphp

@section('title', 'Donasi Pendidikan & Makan Santri - ' . $schoolName)

@section('content')

<section class="relative isolate overflow-hidden bg-[#052E1F]">
    @if($heroBg)
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $heroBg }}')"></div>
    @else
        <div class="absolute inset-0 bg-gradient-to-br from-[#052E1F] via-[#063f2a] to-[#0F6B3A]"></div>
        <div class="absolute inset-0 opacity-[0.08]"
             style="background-image: linear-gradient(135deg, rgba(255,255,255,.45) 1px, transparent 1px); background-size: 42px 42px;"></div>
    @endif
    <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/95 via-emerald-950/80 to-emerald-900/50"></div>

    <div class="relative z-10 mx-auto max-w-7xl px-4 pb-16 pt-28 sm:px-6 lg:px-8 lg:pb-20 lg:pt-32">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 rounded-full border border-amber-300/70 bg-emerald-950/60 px-4 py-2 text-sm font-semibold text-amber-300 shadow-lg shadow-emerald-950/20 backdrop-blur">
                <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                LADANG AMAL JARIYAH
            </div>
            <h1 class="mt-6 font-serif text-4xl font-bold leading-tight text-white sm:text-5xl lg:text-6xl">
                Donasi Pendidikan &amp; Makan Santri
            </h1>
            <p class="mt-5 max-w-2xl text-lg font-semibold leading-8 text-amber-300 sm:text-xl">
                Bersama mendukung pendidikan gratis dan kebutuhan makan santri SMA Persis Serang.
            </p>

            <div class="mt-8 rounded-2xl border border-amber-300/30 bg-white/10 p-5 text-emerald-50 shadow-lg shadow-emerald-950/20 backdrop-blur">
                <p class="text-lg font-semibold leading-8">
                    “Barangsiapa menempuh jalan untuk mencari ilmu, Allah akan mudahkan baginya jalan menuju surga.”
                </p>
                <p class="mt-2 text-sm font-medium text-amber-300">HR. Muslim</p>
            </div>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ $waUrl }}" target="_blank" rel="noopener"
                   class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-amber-400 to-amber-600 px-7 py-4 text-sm font-bold text-emerald-950 shadow-lg shadow-amber-900/20 transition hover:from-amber-300 hover:to-amber-500">
                    Hubungi WA SMA Persis Serang
                </a>
                <a href="{{ $shareUrl }}" target="_blank" rel="noopener"
                   class="inline-flex items-center justify-center rounded-xl border border-amber-300/80 px-7 py-4 text-sm font-bold text-white transition hover:bg-white/10">
                    Sebarkan Informasi Kebaikan Ini
                </a>
            </div>
        </div>
    </div>
</section>

<section class="bg-[#FBF7EF] py-16 lg:py-20">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[0.95fr_1.05fr] lg:items-center lg:px-8">
        <div>
            <p class="text-sm font-bold uppercase tracking-[0.24em] text-[#D4A017]">Tentang Program</p>
            <h2 class="mt-3 font-serif text-3xl font-bold text-[#052E1F] sm:text-4xl">
                Menopang Pendidikan dan Kebutuhan Harian Santri
            </h2>
        </div>
        <p class="text-lg leading-8 text-emerald-900/75">
            SMA Persis Serang berikhtiar menghadirkan pendidikan yang terjangkau, bahkan menggratiskan biaya pendidikan dan biaya makan asrama bagi anak-anak yang membutuhkan. Program ini menjadi kesempatan bagi kaum muslimin untuk ikut menyuburkan ladang pahala melalui sedekah dan infak pendidikan.
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
                Yang memiliki beras, bisa menitipkan berasnya. Yang memiliki telur, bisa menitipkan telurnya. Yang memiliki sayuran, bisa menitipkan sayurannya. Apabila diperlukan, insyaAllah kami siap menjemput donasi ke tempat Bapak/Ibu/Saudara/i.
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
                Hubungi WA SMA Persis Serang
            </a>
            <a href="{{ $shareUrl }}" target="_blank" rel="noopener"
               class="inline-flex items-center justify-center rounded-xl border border-amber-300/80 px-7 py-4 text-sm font-bold text-white transition hover:bg-white/10">
                Sebarkan Informasi Kebaikan Ini
            </a>
        </div>
    </div>
</section>

@endsection
