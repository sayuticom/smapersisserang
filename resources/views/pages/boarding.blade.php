@extends('layouts.public')

@php
$schoolName = $schoolSetting?->school_name ?? 'SMA Persis Serang';
$btnPrimaryText = $websitePage?->button_primary_text ?? 'Daftar SPMB';
$btnPrimaryUrl = $websitePage?->button_primary_url ?? '/spmb/daftar';
$btnSecondaryText = $websitePage?->button_secondary_text ?? 'Hubungi Kami';
$btnSecondaryUrl = $websitePage?->button_secondary_url ?? '/#kontak';

$boardingImage = null;
if ($schoolSetting?->boarding_image_path) {
    $boardingImage = 'storage/' . $schoolSetting->boarding_image_path;
} elseif ($schoolSetting?->building_image_path) {
    $boardingImage = 'storage/' . $schoolSetting->building_image_path;
}

$iconMap = [
    'building' => 'M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z',
    'bolt' => 'M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z',
    'academic' => 'M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5',
    'users' => 'M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z',
    'shield' => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z',
    'cog' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
    'map' => 'M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z',
    'heart' => 'M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z',
];
@endphp

@section('content')
<section class="relative overflow-hidden bg-gradient-to-br from-emerald-950 via-emerald-900 to-emerald-800">
    <div class="mx-auto flex max-w-7xl flex-col items-center justify-center px-4 py-12 text-center sm:px-6 lg:py-20">
        <div class="inline-flex items-center gap-2 rounded-full border border-amber-300/70 bg-emerald-950/60 px-6 py-2 text-sm font-semibold text-amber-300">
            <span class="h-2 w-2 rounded-full bg-amber-300"></span>
            BOARDING SCHOOL
        </div>

        <h1 class="mt-6 text-4xl font-bold tracking-tight text-white sm:text-5xl">
            {{ $websitePage?->title ?? 'Islamic Boarding School' }}
        </h1>

        <div class="mx-auto mt-4 h-1.5 w-28 rounded-full bg-gradient-to-r from-amber-400 to-yellow-300"></div>

        <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-emerald-50 sm:text-xl">
            {{ $websitePage?->subtitle ?? 'Lingkungan pendidikan berasrama untuk membentuk akhlak, kemandirian, ibadah, dan kedisiplinan siswa.' }}
        </p>
    </div>
</section>

<section class="bg-[#FBF7EF] py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <div class="inline-flex items-center gap-2 rounded-full border border-amber-300/40 bg-amber-50 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-amber-700 mb-4">
                {{ $content['section_label'] }}
            </div>
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">{{ $content['section_heading'] }}</h2>
            <p class="text-gray-500 mt-4 text-lg leading-relaxed">{{ $content['section_subtitle'] }}</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($content['why_boarding_cards'] as $card)
            <div class="bg-white rounded-2xl border border-amber-100/60 shadow-sm p-6 lg:p-8 hover:shadow-md hover:border-emerald-300/40 transition-all">
                <div class="w-14 h-14 {{ $card['color'] === 'amber' ? 'bg-[#D4A017]/10' : 'bg-[#EAF6EE]' }} rounded-2xl flex items-center justify-center mb-5">
                    <svg class="w-7 h-7 {{ $card['color'] === 'amber' ? 'text-[#D4A017]' : 'text-[#0F6B3A]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $iconMap[$card['icon']] ?? $iconMap['building'] }}"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">{{ $card['title'] }}</h3>
                <p class="text-gray-500 text-sm mt-3 leading-relaxed">{{ $card['description'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-16 lg:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">{{ $content['schedule_heading'] }}</h2>
            <p class="text-gray-500 mt-4 text-lg leading-relaxed">{{ $content['schedule_subtitle'] }}</p>
        </div>
        <div class="max-w-3xl mx-auto">
            <div class="space-y-0">
                @foreach($content['daily_schedule'] as $i => $item)
                <div class="flex items-start gap-5 group">
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-2xl {{ $item['color'] === 'amber' ? 'bg-[#D4A017]/10 text-[#D4A017]' : ($item['color'] === 'gray' ? 'bg-gray-100 text-gray-500' : 'bg-[#EAF6EE] text-[#0F6B3A]') }} flex items-center justify-center font-bold text-sm shadow-sm group-hover:shadow-md transition-shadow flex-shrink-0">{{ $item['time'] }}</div>
                        @if(!$loop->last)
                        <div class="w-0.5 h-16 bg-emerald-100"></div>
                        @endif
                    </div>
                    <div class="{{ $loop->last ? 'pt-1.5' : 'pb-8 pt-1.5' }}">
                        <h3 class="font-semibold text-gray-900">{{ $item['title'] }}</h3>
                        <p class="text-gray-500 text-sm mt-1 leading-relaxed">{{ $item['description'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-8 rounded-xl border border-amber-200/60 bg-amber-50/50 px-5 py-4 text-center">
                <p class="text-sm text-amber-700">
                    <span class="font-medium">Catatan:</span> {{ $content['schedule_note'] }}
                </p>
            </div>
        </div>
    </div>
</section>

@if(!empty($content['holiday_schedule']))
<section class="bg-[#FBF7EF] py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <div class="inline-flex items-center gap-2 rounded-full border border-amber-300/40 bg-amber-50 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-amber-700 mb-4">
                JADWAL KHUSUS
            </div>
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">Jadwal Hari Libur</h2>
            <p class="text-gray-500 mt-4 text-lg leading-relaxed">Kegiatan santri pada hari libur atau akhir pekan tetap terarah dan terpantau.</p>
        </div>
        <div class="max-w-3xl mx-auto">
            <div class="space-y-0">
                @foreach($content['holiday_schedule'] as $i => $item)
                <div class="flex items-start gap-5 group">
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-2xl {{ $item['color'] === 'amber' ? 'bg-[#D4A017]/10 text-[#D4A017]' : ($item['color'] === 'gray' ? 'bg-gray-100 text-gray-500' : 'bg-[#EAF6EE] text-[#0F6B3A]') }} flex items-center justify-center font-bold text-sm shadow-sm group-hover:shadow-md transition-shadow flex-shrink-0">{{ $item['time'] }}</div>
                        @if(!$loop->last)
                        <div class="w-0.5 h-16 bg-amber-100"></div>
                        @endif
                    </div>
                    <div class="{{ $loop->last ? 'pt-1.5' : 'pb-8 pt-1.5' }}">
                        <h3 class="font-semibold text-gray-900">{{ $item['title'] }}</h3>
                        <p class="text-gray-500 text-sm mt-1 leading-relaxed">{{ $item['description'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

@if(!empty($content['info_cards']))
<section class="py-16 lg:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <div class="inline-flex items-center gap-2 rounded-full border border-amber-300/40 bg-amber-50 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-amber-700 mb-4">
                INFORMASI
            </div>
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">Informasi Boarding</h2>
            <p class="text-gray-500 mt-4 text-lg leading-relaxed">Informasi tambahan terkait kegiatan dan pembinaan asrama.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($content['info_cards'] as $card)
            <div class="bg-[#FBF7EF] rounded-2xl border border-amber-100/60 shadow-sm p-6 lg:p-8 hover:shadow-md hover:border-emerald-300/40 transition-all">
                <div class="w-14 h-14 {{ $card['color'] === 'amber' ? 'bg-[#D4A017]/10' : 'bg-[#EAF6EE]' }} rounded-2xl flex items-center justify-center mb-5">
                    <svg class="w-7 h-7 {{ $card['color'] === 'amber' ? 'text-[#D4A017]' : 'text-[#0F6B3A]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $iconMap[$card['icon']] ?? $iconMap['building'] }}"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">{{ $card['title'] }}</h3>
                <p class="text-gray-500 text-sm mt-3 leading-relaxed">{{ $card['description'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="bg-[#FBF7EF] py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">{{ $content['focus_heading'] }}</h2>
            <p class="text-gray-500 mt-4 text-lg leading-relaxed">{{ $content['focus_subtitle'] }}</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($content['focus_cards'] as $card)
            <div class="bg-white rounded-2xl border border-amber-100/60 shadow-sm p-6 lg:p-8 hover:shadow-md hover:border-emerald-300/40 transition-all text-center">
                <div class="w-14 h-14 mx-auto {{ $card['color'] === 'amber' ? 'bg-[#D4A017]/10' : 'bg-[#EAF6EE]' }} rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 {{ $card['color'] === 'amber' ? 'text-[#D4A017]' : 'text-[#0F6B3A]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $iconMap[$card['icon']] ?? $iconMap['building'] }}"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">{{ $card['title'] }}</h3>
                <p class="text-gray-500 text-sm mt-2 leading-relaxed">{{ $card['description'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-16 lg:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-[#FBF7EF] rounded-[2rem] border border-amber-100/60 shadow-sm p-8 lg:p-12">
            <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center">
                <div>
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">{{ $content['section_heading'] }}</h2>
                    <p class="text-[#D4A017] font-semibold text-lg mt-2">{{ $content['section_label'] }}</p>
                    <p class="text-gray-600 mt-6 leading-relaxed text-lg">
                        @if($schoolSetting?->about_boarding)
                            {{ $schoolSetting->about_boarding }}
                        @else
                            {{ $schoolName }} menyediakan fasilitas asrama yang nyaman,
                            aman, dan kondusif untuk mendukung proses belajar dan pembinaan karakter siswa
                            selama 24 jam dalam lingkungan Islami.
                        @endif
                    </p>
                </div>
                <div>
                    @if($boardingImage)
                        <div class="aspect-[4/3] rounded-3xl overflow-hidden shadow-lg border border-emerald-100">
                            <img src="{{ asset($boardingImage) }}"
                                 alt="Foto {{ $schoolName }}"
                                 class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="aspect-[4/3] rounded-3xl overflow-hidden shadow-lg border border-emerald-100 bg-gradient-to-br from-[#EAF6EE] via-white to-white flex items-center justify-center">
                            <div class="text-center p-8">
                                <div class="w-24 h-24 mx-auto bg-[#0F6B3A]/10 rounded-2xl flex items-center justify-center mb-5">
                                    <svg class="w-12 h-12 text-[#0F6B3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-400 font-medium">{{ $schoolSetting->short_name ?? 'SMA Persis Serang' }}</p>
                                <p class="text-gray-300 text-sm mt-1">Islamic Boarding School</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-[#FBF7EF] py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">Ingin tahu lebih lanjut?</h2>
        <p class="text-gray-500 mt-4 text-lg max-w-xl mx-auto leading-relaxed">Daftarkan putra-putri Anda dan jadilah bagian dari keluarga besar {{ $schoolName }}.</p>
        <div class="flex flex-wrap justify-center gap-4 mt-8">
            @if($btnPrimaryUrl)
                <a href="{{ $btnPrimaryUrl }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 bg-emerald-600 text-white font-semibold rounded-xl hover:bg-emerald-700 transition-colors shadow-sm">
                    {{ $btnPrimaryText }}
                </a>
            @endif
            @if($btnSecondaryUrl)
                <a href="{{ $btnSecondaryUrl }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 border-2 border-[#0F6B3A] text-[#0F6B3A] font-semibold rounded-xl hover:bg-[#EAF6EE] transition-colors">
                    {{ $btnSecondaryText }}
                </a>
            @endif
            <button type="button" onclick="toggleAiChatPanel()"
               class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 border-2 border-[#D4A017] text-[#D4A017] font-semibold rounded-xl hover:bg-[#D4A017]/5 transition-colors">
                Konsultasi SPMB
            </button>
        </div>
    </div>
</section>
@endsection