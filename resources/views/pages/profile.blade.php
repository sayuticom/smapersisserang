@extends('layouts.public')

@section('content')

<section class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-20">
        <div class="max-w-3xl mx-auto text-center">
            <h1 class="text-4xl lg:text-5xl font-bold text-gray-900">
                {{ $websitePage?->title ?? ($schoolSetting->school_name ?? 'SMA Persis Serang') }}
            </h1>
            @if($websitePage?->subtitle)
                <p class="text-xl text-[#D4A017] font-semibold mt-3">
                    {{ $websitePage->subtitle }}
                </p>
            @elseif($schoolSetting?->tagline)
                <p class="text-xl text-[#D4A017] font-semibold mt-3">
                    {{ $schoolSetting->tagline }}
                </p>
            @endif
        </div>
    </div>
</section>

@if($schoolSetting?->about_school)
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <div class="order-last lg:order-none">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Tentang {{ $schoolSetting->school_name ?? 'SMA Persis Serang' }}</h2>
                <div class="text-gray-600 leading-relaxed text-lg">
                    {{ $schoolSetting->about_school }}
                </div>
            </div>
            <div>
                @if($schoolSetting?->building_image_path)
                    <div class="aspect-[4/3] rounded-3xl overflow-hidden shadow-lg border border-emerald-100">
                        <img src="{{ asset('storage/' . $schoolSetting->building_image_path) }}"
                             alt="Foto {{ $schoolSetting->school_name }}"
                             class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="aspect-[4/3] rounded-3xl overflow-hidden shadow-lg border border-emerald-100 bg-gradient-to-br from-[#EAF6EE] via-white to-white flex items-center justify-center">
                        <div class="text-center p-8">
                            <div class="w-20 h-20 mx-auto bg-[#0F6B3A]/10 rounded-2xl flex items-center justify-center mb-4">
                                <svg class="w-10 h-10 text-[#0F6B3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <p class="text-gray-400 font-medium">Foto Gedung Sekolah</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endif

@if($schoolSetting?->vision || $schoolSetting?->mission)
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            @if($schoolSetting->vision)
            <div class="bg-emerald-50 rounded-2xl p-8 border border-emerald-100">
                <h2 class="text-2xl font-bold text-[#0F6B3A] mb-4">Visi</h2>
                <p class="text-gray-700 leading-relaxed text-lg">{{ $schoolSetting->vision }}</p>
            </div>
            @endif
            @if($schoolSetting->mission)
            <div class="bg-amber-50 rounded-2xl p-8 border border-amber-100">
                <h2 class="text-2xl font-bold text-[#D4A017] mb-4">Misi</h2>
                <div class="text-gray-700 leading-relaxed">
                    @foreach(explode("\n", $schoolSetting->mission) as $line)
                        @if(trim($line))
                            <p class="mb-2 flex items-start gap-2">
                                <span class="text-[#D4A017] mt-1 flex-shrink-0">•</span>
                                <span>{{ $line }}</span>
                            </p>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endif

@if($schoolSetting?->about_boarding)
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-br from-[#0F6B3A] to-[#0A4F2B] rounded-[2rem] p-8 lg:p-12 text-white">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-3xl font-bold mb-6">Boarding School</h2>
                <div class="text-emerald-100/90 leading-relaxed text-lg">
                    {{ $schoolSetting->about_boarding }}
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-8 max-w-3xl mx-auto">
            @if($schoolSetting?->address || $schoolSetting?->city || $schoolSetting?->province)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="w-12 h-12 bg-[#EAF6EE] rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-[#0F6B3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Alamat</h3>
                <p class="text-gray-600 leading-relaxed">
                    {{ $schoolSetting->address }}<br>
                    {{ $schoolSetting->city }}{{ $schoolSetting->city && $schoolSetting->province ? ', ' : '' }}{{ $schoolSetting->province }}
                </p>
            </div>
            @endif
            @if($schoolSetting?->email || $schoolSetting?->whatsapp_number)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="w-12 h-12 bg-[#EAF6EE] rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-[#0F6B3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Kontak</h3>
                <div class="space-y-2 text-gray-600">
                    @if($schoolSetting->whatsapp_number)
                        <p>WhatsApp: {{ $schoolSetting->whatsapp_number }}</p>
                    @endif
                    @if($schoolSetting->email)
                        <p>Email: {{ $schoolSetting->email }}</p>
                    @endif
                </div>
            </div>
            @endif
        </div>

        @if($schoolSetting?->google_maps_embed_url)
            <div class="mt-10">
                <h3 class="text-xl font-semibold text-gray-900 text-center mb-4">Lokasi</h3>
                <iframe src="{{ $schoolSetting->google_maps_embed_url }}"
                        class="w-full h-72 rounded-3xl border border-emerald-100 shadow-lg"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen>
                </iframe>
                @if($schoolSetting?->google_maps_link)
                    <div class="text-center mt-4">
                        <a href="{{ $schoolSetting->google_maps_link }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-[#0F6B3A] text-white font-semibold rounded-xl hover:bg-[#0A4F2B] transition-colors shadow-lg shadow-[#0F6B3A]/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Buka di Google Maps
                        </a>
                    </div>
                @endif
            </div>
        @elseif($schoolSetting?->google_maps_link)
            <div class="text-center mt-10">
                <a href="{{ $schoolSetting->google_maps_link }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-[#0F6B3A] text-white font-semibold rounded-xl hover:bg-[#0A4F2B] transition-colors shadow-lg shadow-[#0F6B3A]/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Buka di Google Maps
                </a>
            </div>
        @endif

        <div class="flex flex-wrap justify-center gap-4 mt-10">
            <a href="{{ route('ppdb.create') }}"
               class="px-8 py-4 bg-[#0F6B3A] text-white font-semibold rounded-xl hover:bg-[#0A4F2B] transition-colors shadow-lg shadow-[#0F6B3A]/20">
                Daftar SPMB
            </a>
            <a href="{{ route('ppdb.status.form') }}"
               class="px-8 py-4 border-2 border-[#0F6B3A] text-[#0F6B3A] font-semibold rounded-xl hover:bg-[#EAF6EE] transition-colors">
                Cek Status
            </a>
            @if($schoolSetting?->whatsapp_number)
                <a href="{{ $schoolSetting->whatsappLink('Assalamu\'alaikum, saya ingin bertanya tentang SPMB.') }}"
                   class="px-8 py-4 border-2 border-[#0F6B3A] text-[#0F6B3A] font-semibold rounded-xl hover:bg-[#EAF6EE] transition-colors"
                   target="_blank" rel="noopener">
                    Hubungi WhatsApp
                </a>
            @endif
        </div>
    </div>
</section>

@endsection
