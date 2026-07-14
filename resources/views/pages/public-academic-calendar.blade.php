@extends('layouts.public')

@php
    $pageTitle = 'Kalender Pendidikan';
    $pageDescription = 'Informasi kegiatan, asesmen, hari libur, dan agenda pendidikan SMA Persis Serang.';
    $ogImage = $schoolSetting?->meta_image
        ? asset('storage/' . $schoolSetting->meta_image)
        : asset('images/og/sma-persis-serang.jpg');
@endphp

@section('title', $pageTitle . ' - ' . ($schoolSetting->school_name ?? 'SMA Persis Serang'))

@section('meta')
    <meta name="description" content="{{ $pageDescription }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ request()->fullUrl() }}">
    <meta property="og:title" content="{{ $pageTitle }} - {{ $schoolSetting->school_name ?? 'SMA Persis Serang' }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }} - {{ $schoolSetting->school_name ?? 'SMA Persis Serang' }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
@endsection

@section('content')

<style>
    .pubcal-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 300px;
        gap: 1.25rem;
        align-items: start;
    }

    .pubcal-sidebar {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .pubcal-legend-box {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 0.375rem;
        flex-shrink: 0;
    }

    .pubcal-legend-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 9999px;
        flex-shrink: 0;
    }

    .pubcal-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 22px;
        height: 20px;
        padding: 0 6px;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 1;
        background-color: #f1f5f9;
        color: #64748b;
    }

    .pubcal-legend-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.25rem 1rem;
    }

    .pubcal-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        width: 100%;
    }

    .pubcal-cell {
        min-width: 0;
        border-bottom: 1px solid #fde9cc;
        border-right: 1px solid #fde9cc;
    }

    .pubcal-cell:empty {
        border-right: none;
    }

    .pubcal-cell-today {
        box-shadow: inset 0 0 0 2px #0F6B3A;
        border-radius: 2px;
    }

    .pubcal-cell-selected {
        background-color: rgba(15, 107, 58, 0.06);
        box-shadow: inset 0 0 0 2px #0F6B3A;
        border-radius: 2px;
    }

    .pubcal-event-dot {
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 9999px;
        flex-shrink: 0;
    }

    .pubcal-day-name {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 2.5rem;
        font-size: 0.75rem;
        font-weight: 700;
        border-bottom: 1px solid #fde9cc;
    }

    @media (max-width: 1023px) {
        .pubcal-layout {
            grid-template-columns: minmax(0, 1fr);
        }
        .pubcal-legend-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 639px) {
        .pubcal-legend-grid {
            grid-template-columns: 1fr;
        }
        .pubcal-sidebar {
            display: flex;
            flex-direction: column;
        }
        .pubcal-sidebar > :nth-child(1) { order: 4; }
        .pubcal-sidebar > :nth-child(2) { order: 1; }
        .pubcal-sidebar > :nth-child(3) { order: 2; }
        .pubcal-sidebar > :nth-child(4) { order: 3; }
        .pubcal-cell {
            min-height: 54px;
            padding: 3px;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            user-select: none;
        }
        .pubcal-cell .event-content-desktop {
            display: none;
        }
        .pubcal-day-name {
            height: 26px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            border-bottom: 1px solid #fde9cc;
        }
        .pubcal-day-number {
            font-size: 12px;
            line-height: 1;
        }
    }

    @media (min-width: 640px) {
        .pubcal-cell {
            min-height: 110px;
            padding: 0.375rem;
        }
        .pubcal-cell .mobile-event-indicator {
            display: none;
        }
        .pubcal-mobile-nav,
        .pubcal-mobile-detail {
            display: none;
        }
        .pubcal-day-name {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 2.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            border-bottom: 1px solid #fde9cc;
        }
        .pubcal-day-number {
            font-size: 0.75rem;
            line-height: 1;
        }
        .pubcal-cell-today .pubcal-day-number,
        .pubcal-cell-selected .pubcal-day-number {
            background-color: #d1fae5;
            color: #065f46;
            border-radius: 9999px;
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    }

    @media (min-width: 640px) and (max-width: 1023px) {
        .pubcal-cell {
            min-height: 90px;
            padding: 0.25rem;
        }
    }
</style>

<section class="relative isolate overflow-hidden bg-[#052E1F]">
    <div class="absolute inset-0 bg-gradient-to-br from-[#052E1F] via-[#063f2a] to-[#0F6B3A]"></div>
    <div class="absolute inset-0 opacity-[0.06]"
         style="background-image: linear-gradient(135deg, rgba(255,255,255,.45) 1px, transparent 1px); background-size: 42px 42px;"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/95 via-emerald-950/75 to-emerald-900/30"></div>

    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="py-10 sm:py-16 lg:py-20">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-amber-300/70 bg-emerald-950/60 px-4 py-2 text-sm font-semibold text-amber-300 shadow-lg shadow-emerald-950/20 backdrop-blur">
                    <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                    KALENDER PENDIDIKAN
                </div>
                <h1 class="mt-4 sm:mt-6 font-serif text-2xl font-bold leading-tight text-white sm:text-4xl lg:text-5xl">
                    Kalender Pendidikan
                </h1>
                @if($academicYear)
                    <p class="mt-2 sm:mt-3 text-sm sm:text-lg font-semibold text-amber-300">
                        {{ $academicYear->name }}
                    </p>
                @endif
                <p class="mt-2 sm:mt-3 text-sm sm:text-base leading-6 sm:leading-7 text-emerald-50/90 line-clamp-3 sm:line-clamp-none">
                    {{ $pageDescription }}
                </p>
            </div>
        </div>
    </div>
</section>

@if(!$academicYear)
    <section class="bg-[#FBF7EF] py-16 lg:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
            <div class="mx-auto max-w-md">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="mt-6 font-serif text-2xl font-bold text-[#052E1F]">Kalender pendidikan belum tersedia.</h2>
                <p class="mt-3 text-base leading-7 text-gray-600">Saat ini belum ada tahun pelajaran aktif. Silakan kunjungi kembali halaman ini nanti.</p>
            </div>
        </div>
    </section>
@else
    <section class="bg-[#FBF7EF] py-8 sm:py-10 lg:py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="hidden sm:flex flex-wrap items-center justify-between gap-3 mb-6">
                <div class="flex items-center gap-2">
                    @if($canGoPrevious)
                        <a href="{{ route('public.academic-calendar', ['month' => $previousMonthDate->format('Y-m')]) }}"
                           class="inline-flex items-center gap-1 rounded-xl border border-amber-200 bg-white px-4 py-2.5 text-sm font-semibold text-[#052E1F] shadow-sm transition hover:bg-amber-50 hover:border-amber-300">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            {{ $indonesianMonths[$previousMonthDate->month] }} {{ $previousMonthDate->year }}
                        </a>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-xl border border-amber-100 bg-white/60 px-4 py-2.5 text-sm font-semibold text-slate-300 cursor-not-allowed">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            {{ $indonesianMonths[$previousMonthDate->month] }} {{ $previousMonthDate->year }}
                        </span>
                    @endif

                    <h2 class="text-xl font-bold text-[#052E1F] min-w-[170px] text-center font-serif">
                        {{ $indonesianMonths[$selectedMonthDate->month] }} {{ $selectedMonthDate->year }}
                    </h2>

                    @if($canGoNext)
                        <a href="{{ route('public.academic-calendar', ['month' => $nextMonthDate->format('Y-m')]) }}"
                           class="inline-flex items-center gap-1 rounded-xl border border-amber-200 bg-white px-4 py-2.5 text-sm font-semibold text-[#052E1F] shadow-sm transition hover:bg-amber-50 hover:border-amber-300">
                            {{ $indonesianMonths[$nextMonthDate->month] }} {{ $nextMonthDate->year }}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-xl border border-amber-100 bg-white/60 px-4 py-2.5 text-sm font-semibold text-slate-300 cursor-not-allowed">
                            {{ $indonesianMonths[$nextMonthDate->month] }} {{ $nextMonthDate->year }}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                    @endif
                </div>

                @if($showTodayButton)
                    <a href="{{ route('public.academic-calendar', ['month' => now()->format('Y-m')]) }}"
                       class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-white px-4 py-2.5 text-sm font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-50 hover:border-emerald-300">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Hari Ini
                    </a>
                @endif
            </div>

            <div class="sm:hidden flex items-center justify-between gap-1 mb-3">
                @if($canGoPrevious)
                    <a href="{{ route('public.academic-calendar', ['month' => $previousMonthDate->format('Y-m')]) }}"
                       class="flex h-9 w-9 items-center justify-center rounded-lg border border-amber-200 bg-white text-[#052E1F] shadow-sm transition hover:bg-amber-50"
                       aria-label="{{ $indonesianMonths[$previousMonthDate->month] }} {{ $previousMonthDate->year }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                @else
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-amber-100 bg-white/60 text-slate-300">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </span>
                @endif

                <h2 class="text-base font-bold text-[#052E1F] font-serif text-center shrink-0" style="min-width:120px;">
                    {{ $indonesianMonths[$selectedMonthDate->month] }} {{ $selectedMonthDate->year }}
                </h2>

                @if($canGoNext)
                    <a href="{{ route('public.academic-calendar', ['month' => $nextMonthDate->format('Y-m')]) }}"
                       class="flex h-9 w-9 items-center justify-center rounded-lg border border-amber-200 bg-white text-[#052E1F] shadow-sm transition hover:bg-amber-50"
                       aria-label="{{ $indonesianMonths[$nextMonthDate->month] }} {{ $nextMonthDate->year }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @else
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-amber-100 bg-white/60 text-slate-300">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                @endif
            </div>

            @if($showTodayButton)
                <div class="sm:hidden flex justify-center mb-3">
                    <a href="{{ route('public.academic-calendar', ['month' => now()->format('Y-m')]) }}"
                       class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-white px-3 py-1.5 text-xs font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-50">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Hari Ini
                    </a>
                </div>
            @endif

            <div x-data="{
                selectedDate: '{{ $selectedDayDate ?? '' }}',
                eventData: {!! json_encode($mobileEventData) !!},
                get events() {
                    return this.eventData[this.selectedDate] || [];
                },
                get dateLabel() {
                    if (!this.selectedDate) return '';
                    const p = this.selectedDate.split('-');
                    const months = {!! json_encode($indonesianMonths) !!};
                    const day = parseInt(p[2], 10);
                    const month = parseInt(p[1], 10);
                    return day + ' ' + months[month] + ' ' + p[0];
                },
                get eventCount() {
                    return this.events.length;
                },
                selectDate(date) {
                    this.selectedDate = date;
                }
            }" class="pubcal-layout">
                <div class="min-w-0">
                    <div class="rounded-2xl border border-amber-100 bg-white shadow-md shadow-emerald-950/5">
                        <div class="pubcal-grid">
                            @foreach($dayNames as $idx => $dn)
                                <div class="pubcal-day-name {{ $idx === 0 ? 'text-red-500' : 'text-[#052E1F]' }}">
                                    {{ $dn }}
                                </div>
                            @endforeach
                        </div>

                        <div class="pubcal-grid">
                            @foreach($calendarDays as $day)
                                @php
                                    $ds = $day['date'];
                                    $dayEvts = $dayEvents->get($ds, collect());
                                    $dayEvtCount = $dayEvts->count();
                                    $isSelected = $ds === $selectedDayDate;
                                @endphp
                                <button type="button"
                                    @click="selectDate('{{ $ds }}')"
                                    :class="{ 'pubcal-cell-selected': selectedDate === '{{ $ds }}' }"
                                    aria-label="{{ \Carbon\Carbon::parse($ds)->format('j') }} {{ $indonesianMonths[\Carbon\Carbon::parse($ds)->month] }} {{ \Carbon\Carbon::parse($ds)->year }}{{ $dayEvtCount > 0 ? ', ' . $dayEvtCount . ' agenda' : '' }}"
                                    class="pubcal-cell text-left
                                        {{ $day['isCurrentMonth'] ? '' : 'bg-amber-50/40' }}
                                        {{ $day['isToday'] ? 'pubcal-cell-today' : '' }}
                                        {{ $ds === $selectedDayDate ? 'pubcal-cell-selected' : '' }}">
                                    <div class="flex items-center justify-between">
                                        <span class="pubcal-day-number font-bold leading-none
                                            {{ $day['isSunday'] ? 'text-red-500' : ($day['isCurrentMonth'] ? 'text-[#052E1F]' : 'text-slate-300') }}">
                                            {{ $day['day'] }}
                                        </span>
                                        @if($dayEvtCount > 0 && $day['isCurrentMonth'])
                                            <span class="mobile-event-indicator text-[10px] font-medium text-slate-400">{{ $dayEvtCount }}</span>
                                        @endif
                                    </div>

                                    @if($dayEvtCount > 0 && $day['isCurrentMonth'])
                                        <div class="mobile-event-indicator mt-0.5 flex flex-wrap items-center gap-0.5">
                                            @php $shownCats = $dayEvts->pluck('category')->unique()->take(3); @endphp
                                            @foreach($shownCats as $cat)
                                                @php $dotCat = $categoryMap[$cat] ?? $categoryMap['lainnya']; @endphp
                                                <span class="pubcal-event-dot {{ $dotCat['dot_class'] }}"></span>
                                            @endforeach
                                            @if($dayEvtCount > 3)
                                                <span class="text-[9px] font-semibold text-slate-400">+{{ $dayEvtCount }}</span>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="event-content-desktop mt-0.5 space-y-0.5">
                                        @if($day['isToday'])
                                            <div class="text-[9px] font-semibold text-emerald-600 leading-tight">Hari Ini</div>
                                        @endif
                                        @php $visibleEvts = $dayEvts->take(2); @endphp
                                        @foreach($visibleEvts as $evt)
                                            @php $ecat = $categoryMap[$evt['category']] ?? $categoryMap['lainnya']; @endphp
                                            <div title="{{ $evt['title'] }} — {{ $evt['start_date'] }} s.d {{ $evt['end_date'] }}{{ $evt['start_time'] ? ' — ' . $evt['start_time'] . '–' . $evt['end_time'] : '' }}{{ $evt['location'] ? ' — ' . $evt['location'] : '' }}"
                                                 class="block rounded px-1 py-0.5 {{ $ecat['bg_class'] }} {{ $ecat['text_class'] }}">
                                                <div class="text-[11px] font-medium leading-tight truncate">{{ $evt['title'] }}</div>
                                                @if($evt['start_time'])
                                                    <div class="text-[10px] leading-tight opacity-70 truncate">{{ $evt['start_time'] }}–{{ $evt['end_time'] }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                        @php $hiddenCount = $dayEvtCount - 2; @endphp
                                        @if($hiddenCount > 0)
                                            <span class="text-[10px] text-slate-400 pl-1">+{{ $hiddenCount }} agenda lainnya</span>
                                        @endif
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="sm:hidden mt-4">
                        <template x-if="selectedDate && events.length > 0">
                            <div class="rounded-2xl border border-amber-100 bg-white p-5 shadow-md shadow-emerald-950/5">
                                <h3 class="text-sm font-bold text-[#052E1F] mb-3" x-text="'Agenda ' + dateLabel"></h3>
                                <div class="space-y-3">
                                    <template x-for="(evt, idx) in events" :key="idx">
                                        <div>
                                            <div class="flex items-start gap-2.5">
                                                <div class="pubcal-legend-box shrink-0" :class="evt.bg_class">
                                                    <span class="pubcal-legend-dot" :class="evt.dot_class"></span>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-medium text-[#052E1F] leading-tight" x-text="evt.title"></p>
                                                    <p class="text-xs text-slate-500 leading-tight mt-px" x-text="evt.date_formatted"></p>
                                                    <template x-if="evt.start_time">
                                                        <p class="text-xs text-slate-400" x-text="evt.start_time + '–' + evt.end_time"></p>
                                                    </template>
                                                    <div class="flex flex-wrap gap-1 mt-1">
                                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium" :class="evt.bg_class + ' ' + evt.text_class" x-text="evt.category_label"></span>
                                                        <template x-if="evt.location">
                                                            <span class="inline-flex items-center text-[10px] text-slate-400" x-text="evt.location"></span>
                                                        </template>
                                                    </div>
                                                    <template x-if="evt.description">
                                                        <p class="text-xs text-slate-500 mt-1 leading-relaxed" x-text="evt.description.length > 120 ? evt.description.substring(0, 120) + '...' : evt.description"></p>
                                                    </template>
                                                </div>
                                            </div>
                                            <template x-if="idx < events.length - 1">
                                                <div class="border-t border-amber-100 mt-3"></div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template x-if="selectedDate && events.length === 0">
                            <div class="rounded-2xl border border-amber-100 bg-white p-5 shadow-md shadow-emerald-950/5">
                                <h3 class="text-sm font-bold text-[#052E1F] mb-3" x-text="'Agenda ' + dateLabel"></h3>
                                <p class="text-sm text-slate-400 text-center py-4">Tidak ada agenda pada tanggal ini.</p>
                            </div>
                        </template>
                    </div>
                </div>

                <aside class="pubcal-sidebar">
                    <div class="rounded-2xl border border-amber-100 bg-white p-5 shadow-md shadow-emerald-950/5">
                        <h3 class="mb-3 text-sm font-bold text-[#052E1F]">Legenda</h3>
                        <div class="pubcal-legend-grid">
                            @foreach($categories as $cat)
                                <div class="flex items-center gap-2 text-sm">
                                    <div class="pubcal-legend-box {{ $cat['bg_class'] }}">
                                        <span class="pubcal-legend-dot {{ $cat['dot_class'] }}"></span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-slate-700 leading-tight text-[13px]">{{ $cat['label'] }}</p>
                                    </div>
                                    <span class="pubcal-badge">{{ $cat['count'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-2xl border border-amber-100 bg-white p-5 shadow-md shadow-emerald-950/5">
                        <h3 class="mb-3 text-sm font-bold text-[#052E1F]">Ringkasan {{ $indonesianMonths[$selectedMonthDate->month] }}</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Total Kegiatan</span>
                                <span class="text-sm font-bold text-[#052E1F]">{{ $monthlySummary['total_events'] }}</span>
                            </div>
                            <div class="border-t border-amber-100"></div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Hari Libur</span>
                                <span class="text-sm font-bold text-[#052E1F]">{{ $monthlySummary['hari_libur'] }} hari</span>
                            </div>
                            <div class="border-t border-amber-100"></div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Hari Asesmen</span>
                                <span class="text-sm font-bold text-[#052E1F]">{{ $monthlySummary['hari_asesmen'] }} hari</span>
                            </div>
                            <div class="border-t border-amber-100"></div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Hari Berkegiatan</span>
                                <span class="text-sm font-bold text-[#052E1F]">{{ $monthlySummary['hari_berkegiatan'] }} hari</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-amber-100 bg-white p-5 shadow-md shadow-emerald-950/5">
                        <h3 class="mb-3 text-sm font-bold text-[#052E1F]">Agenda {{ $indonesianMonths[$selectedMonthDate->month] }} {{ $selectedMonthDate->year }}</h3>
                        <div class="space-y-2.5 max-h-[400px] overflow-y-auto">
                            @forelse($monthlyRawEvents as $evt)
                                @php $ecat = $categoryMap[$evt['category']] ?? $categoryMap['lainnya']; @endphp
                                <div class="flex items-start gap-2.5">
                                    <div class="pubcal-legend-box {{ $ecat['bg_class'] }}">
                                        <span class="pubcal-legend-dot {{ $ecat['dot_class'] }}"></span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-sm font-medium text-[#052E1F] leading-tight">
                                            {{ $evt['title'] }}
                                        </div>
                                        <p class="text-xs text-slate-500 leading-tight mt-px">
                                            {{ $evt['date_formatted'] }}
                                        </p>
                                        @if($evt['start_time'] ?? null)
                                            <p class="text-xs text-slate-400">{{ $evt['start_time'] }}–{{ $evt['end_time'] }}</p>
                                        @endif
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            <span class="inline-flex items-center rounded-full {{ $ecat['bg_class'] }} {{ $ecat['text_class'] }} px-2 py-0.5 text-[10px] font-medium">{{ $ecat['label'] }}</span>
                                            @if($evt['location'] ?? null)
                                                <span class="inline-flex items-center text-[10px] text-slate-400">{{ $evt['location'] }}</span>
                                            @endif
                                        </div>
                                        @if($evt['description'] ?? null)
                                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ Str::limit($evt['description'], 120) }}</p>
                                        @endif
                                    </div>
                                </div>
                                @if(!$loop->last)
                                    <div class="border-t border-amber-100"></div>
                                @endif
                            @empty
                                <p class="text-sm text-slate-400 text-center py-4">Belum ada agenda publik pada bulan ini.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-2xl border border-amber-100 bg-white p-5 shadow-md shadow-emerald-950/5">
                        <h3 class="mb-3 text-sm font-bold text-[#052E1F]">Kegiatan Terdekat</h3>
                        <div class="space-y-2.5">
                            @forelse($upcomingEvents as $ue)
                                @php $uecat = $categoryMap[$ue['category']] ?? $categoryMap['lainnya']; @endphp
                                <div class="flex items-start gap-2.5">
                                    <div class="pubcal-legend-box {{ $uecat['bg_class'] }}">
                                        <span class="pubcal-legend-dot {{ $uecat['dot_class'] }}"></span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-[#052E1F] leading-tight">{{ $ue['title'] }}</p>
                                        <p class="text-xs text-slate-500 leading-tight mt-px">{{ $ue['date_formatted'] }}</p>
                                        @if($ue['start_time'] ?? null)
                                            <p class="text-xs text-slate-400 leading-tight">{{ $ue['start_time'] }}–{{ $ue['end_time'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                @if(!$loop->last)
                                    <div class="border-t border-amber-100"></div>
                                @endif
                            @empty
                                <p class="text-sm text-slate-400 text-center py-4">Belum ada kegiatan terdekat.</p>
                            @endforelse
                        </div>
                    </div>
                </aside>
            </div>

        </div>
    </section>
@endif

@endsection
