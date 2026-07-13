<x-admin-layout>
    <div class="mx-auto max-w-7xl space-y-6" x-data="{
        tab: '{{ request('view', 'annual') === 'monthly' ? 'bulanan' : (request('view', 'annual') === 'list' ? 'daftar' : (request('view', 'annual') === 'effective' ? 'rekap' : 'tahunan')) }}',
        selectedYear: {{ $academicYearId ?? 'null' }}
    }">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Kalender Pendidikan</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $academicYear?->name ?? '-' }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <form method="GET" action="{{ url()->current() }}">
                    <select name="academic_year_id" onchange="this.form.submit()"
                            class="rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ ($academicYearId ?? null) == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('admin.akademik.tahun-pelajaran.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6h12M6 12h12M6 18h12"/>
                    </svg>
                    Kelola Tahun Pelajaran
                </a>
                <button type="button" disabled
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-500 opacity-60 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Impor Kalender
                </button>
                <a href="{{ route('admin.akademik.kalender.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Kegiatan
                </a>
                <button type="button" disabled
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-500 opacity-60 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak PDF
                </button>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm h-full">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-100">
                        <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-bold text-gray-900 leading-tight">{{ $summary['hari_efektif'] }}</p>
                        <p class="text-xs text-slate-500 leading-tight">Hari Efektif <span class="text-[10px] text-amber-500 font-medium">(Estimasi)</span></p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm h-full">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-100">
                        <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-bold text-gray-900 leading-tight">{{ $summary['hari_libur'] }}</p>
                        <p class="text-xs text-slate-500 leading-tight">Hari Libur</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm h-full">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-bold text-gray-900 leading-tight">{{ $summary['asesmen'] }}</p>
                        <p class="text-xs text-slate-500 leading-tight">Asesmen</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm h-full">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-100">
                        <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-amber-600 font-medium leading-tight mb-0.5">Kegiatan Terdekat</p>
                        @if($summary['kegiatan_terdekat'])
                            <p class="text-sm font-semibold text-gray-900 truncate leading-tight">{{ $summary['kegiatan_terdekat']['title'] }}</p>
                            <p class="text-xs text-slate-500 leading-tight mt-0.5">{{ $summary['kegiatan_terdekat']['date'] }}</p>
                            @if($summary['kegiatan_terdekat']['time'] ?? null)
                                <p class="text-xs text-slate-400 leading-tight">{{ $summary['kegiatan_terdekat']['time'] }}</p>
                            @endif
                        @else
                            <p class="text-sm text-slate-400 leading-tight">Tidak ada</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="border-b border-slate-200">
            <nav class="-mb-px flex gap-6 overflow-x-auto">
                <button type="button" @click="tab = 'tahunan'"
                        :class="tab === 'tahunan' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700'"
                        class="whitespace-nowrap border-b-2 px-1 py-3 text-sm font-semibold transition">
                    Kalender Tahunan
                </button>
                <button type="button" @click="tab = 'bulanan'"
                        :class="tab === 'bulanan' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700'"
                        class="whitespace-nowrap border-b-2 px-1 py-3 text-sm font-semibold transition">
                    Kalender Bulanan
                </button>
                <button type="button" @click="tab = 'daftar'"
                        :class="tab === 'daftar' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700'"
                        class="whitespace-nowrap border-b-2 px-1 py-3 text-sm font-semibold transition">
                    Daftar Kegiatan
                </button>
                <button type="button" @click="tab = 'rekap'"
                        :class="tab === 'rekap' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700'"
                        class="whitespace-nowrap border-b-2 px-1 py-3 text-sm font-semibold transition">
                    Rekap Hari Efektif
                </button>
            </nav>
        </div>

@push('styles')
<style>
    .academic-calendar-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 300px;
        gap: 1.25rem;
        align-items: start;
    }

    .academic-calendar-months {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
        min-width: 0;
    }

    .academic-calendar-sidebar {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .academic-calendar-month {
        min-width: 0;
        overflow: hidden;
    }

    .academic-calendar-month-header {
        padding: 0.625rem 0.75rem;
    }

    .academic-calendar-month-body {
        padding: 0.5rem 0.625rem;
    }

    .academic-legend-box {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 0.375rem;
        flex-shrink: 0;
    }

    .academic-legend-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 9999px;
        flex-shrink: 0;
    }

    .academic-badge {
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

    .academic-legend-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.25rem 1rem;
    }

    @media (max-width: 1023px) {
        .academic-legend-grid {
            grid-template-columns: 1fr;
        }
    }

    .academic-date-cell {
        min-width: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 32px;
        position: relative;
        border-radius: 0.25rem;
        font-size: 0.8125rem;
        line-height: 1;
    }

    .academic-date-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 9999px;
    }

    @media (max-width: 1279px) {
        .academic-calendar-layout {
            grid-template-columns: minmax(0, 1fr) 270px;
        }

        .academic-calendar-months {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 1023px) {
        .academic-calendar-layout {
            grid-template-columns: minmax(0, 1fr);
        }

        .academic-calendar-months {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 639px) {
        .academic-calendar-months {
            grid-template-columns: minmax(0, 1fr);
        }
    }
</style>
@endpush

        <div x-show="tab === 'tahunan'" x-cloak>
            @php
                $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            @endphp
            <div class="academic-calendar-layout">
                {{-- BEGIN: academic-calendar-months --}}
                <div class="academic-calendar-months">
                    @foreach($months as $m)
                        @php
                            $daysInMonth = \Carbon\Carbon::createFromDate($m['year'], $m['num'], 1)->daysInMonth;
                            $firstDayOfWeek = \Carbon\Carbon::createFromDate($m['year'], $m['num'], 1)->dayOfWeek;
                        @endphp
                        <div class="rounded-xl border border-slate-200 bg-white shadow-sm academic-calendar-month">
                            <div class="border-b border-slate-100 academic-calendar-month-header">
                                <h3 class="text-sm font-bold text-slate-900">{{ $m['name'] }} {{ $m['year'] }}</h3>
                            </div>
                            <div class="academic-calendar-month-body">
                                <div style="display:grid;grid-template-columns:repeat(7,minmax(0,1fr));">
                                    @foreach($dayNames as $dn)
                                        <div style="min-width:0;" class="flex items-center justify-center h-6 text-[11px] font-semibold text-slate-500">{{ $dn }}</div>
                                    @endforeach
                                </div>
                                <div style="display:grid;grid-template-columns:repeat(7,minmax(0,1fr));">
                                    @for($i = 0; $i < $firstDayOfWeek; $i++)
                                        <div style="min-width:0;"></div>
                                    @endfor
                                    @for($d = 1; $d <= $daysInMonth; $d++)
                                        @php
                                            $dateStr = sprintf('%04d-%02d-%02d', $m['year'], $m['num'], $d);
                                            $dateObj = \Carbon\Carbon::createFromDate($m['year'], $m['num'], $d);
                                            $isSunday = $dateObj->dayOfWeek === 0;
                                            $dayEvents = $events->where('date', $dateStr);
                                            $hasEvent = $dayEvents->count() > 0;
                                            $eventCellBg = $dayEvents->pluck('cell_bg')->unique()->first();
                                            $isToday = $dateObj->isToday();
                                            $tooltipText = $dayEvents->pluck('title')->unique()->join('; ');
                                        @endphp
                                        <div style="min-width:0"
                                             class="academic-date-cell
                                                    {{ $isSunday ? 'text-red-500' : 'text-slate-700' }}
                                                    {{ $isToday ? 'font-bold' : '' }}
                                                    {{ $hasEvent ? $eventCellBg : '' }}"
                                             @if($hasEvent) title="{{ $tooltipText }}"@endif>
                                            <span class="academic-date-number
                                                {{ $isToday ? 'bg-emerald-100 text-emerald-700 font-bold' : '' }}
                                                {{ $hasEvent && !$isToday ? 'font-semibold' : '' }}">
                                                {{ $d }}
                                            </span>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                {{-- END: academic-calendar-months --}}

                {{-- BEGIN: academic-calendar-sidebar --}}
                <aside class="academic-calendar-sidebar">
                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <h3 class="mb-3 text-sm font-semibold text-slate-900">Legenda</h3>
                        <div class="academic-legend-grid">
                            @foreach($categories as $cat)
                                <div class="flex items-center gap-2 text-sm">
                                    <div class="academic-legend-box {{ $cat['bg_class'] }}">
                                        <span class="academic-legend-dot {{ $cat['dot_class'] }}"></span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-slate-700 leading-tight text-[13px]">{{ $cat['label'] }}</p>
                                    </div>
                                    <span class="academic-badge">{{ $cat['count'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <h3 class="mb-3 text-sm font-semibold text-slate-900">Ringkasan</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Hari Efektif <span class="text-[10px] text-amber-500 font-medium">(Estimasi)</span></span>
                                <span class="text-sm font-semibold text-slate-900">{{ $summary['hari_efektif'] }} hari</span>
                            </div>
                            <div class="border-t border-slate-100"></div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Hari Libur</span>
                                <span class="text-sm font-semibold text-slate-900">{{ $summary['hari_libur'] }} hari</span>
                            </div>
                            <div class="border-t border-slate-100"></div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Asesmen</span>
                                <span class="text-sm font-semibold text-slate-900">{{ $summary['asesmen'] }} hari</span>
                            </div>
                            <div class="border-t border-slate-100"></div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Total Kegiatan</span>
                                <span class="text-sm font-semibold text-slate-900">{{ $rawEvents->count() }} kegiatan</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <h3 class="mb-2.5 text-sm font-semibold text-slate-900">Kegiatan Terdekat</h3>
                        <div class="space-y-2.5">
                            @foreach($upcomingEvents as $ue)
                                <div class="flex items-start gap-2.5">
                                    <div class="academic-legend-box {{ $ue['bg_class'] }}">
                                        <span class="academic-legend-dot {{ $ue['dot_class'] }}"></span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-slate-900 leading-tight">{{ $ue['title'] }}</p>
                                        <p class="text-xs text-slate-500 leading-tight mt-px">{{ $ue['date'] }}</p>
                                        @if($ue['time'] ?? null)
                                            <p class="text-xs text-slate-400 leading-tight">{{ $ue['time'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                @if(!$loop->last)
                                    <div class="border-t border-slate-100"></div>
                                @endif
                            @endforeach
                            @if($upcomingEvents->isEmpty())
                                <p class="text-sm text-slate-400">Tidak ada kegiatan terdekat.</p>
                            @endif
                        </div>
                    </div>
                </aside>
                {{-- END: academic-calendar-sidebar --}}
            </div>
        </div>

        <div x-show="tab === 'bulanan'" x-cloak>
            @php
                $selMonth = $selectedMonthDate->month;
                $selYear = $selectedMonthDate->year;
                $monthlyDayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
                $queryPrefix = 'academic_year_id=' . ($academicYearId ?? '') . '&view=monthly&month=';
                $monthlyBaseUrl = route('admin.akademik.kalender.index');
            @endphp
            <div class="space-y-4">
                {{-- Navigation --}}
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        @if($canGoPrevious)
                            <a href="{{ $monthlyBaseUrl }}?{{ $queryPrefix }}{{ $previousMonthDate->format('Y-m') }}"
                               class="inline-flex items-center gap-1 rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                <span class="hidden sm:inline">Bulan Sebelumnya</span>
                                <span class="inline sm:hidden">{{ $indonesianMonths[$previousMonthDate->month] }}</span>
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-300 cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                <span class="hidden sm:inline">Bulan Sebelumnya</span>
                                <span class="inline sm:hidden">{{ $indonesianMonths[$previousMonthDate->month] }}</span>
                            </span>
                        @endif
                        <h3 class="text-lg font-bold text-slate-900 min-w-[160px] text-center">
                            {{ $indonesianMonths[$selMonth] }} {{ $selYear }}
                        </h3>
                        @if($canGoNext)
                            <a href="{{ $monthlyBaseUrl }}?{{ $queryPrefix }}{{ $nextMonthDate->format('Y-m') }}"
                               class="inline-flex items-center gap-1 rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                                <span class="hidden sm:inline">Bulan Berikutnya</span>
                                <span class="inline sm:hidden">{{ $indonesianMonths[$nextMonthDate->month] }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-300 cursor-not-allowed">
                                <span class="hidden sm:inline">Bulan Berikutnya</span>
                                <span class="inline sm:hidden">{{ $indonesianMonths[$nextMonthDate->month] }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        @endif
                    </div>
                    <a href="{{ $monthlyBaseUrl }}?{{ $queryPrefix }}{{ now()->format('Y-m') }}"
                       class="rounded-lg border border-emerald-300 px-3 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-50 transition">
                        Hari Ini
                    </a>
                </div>

                {{-- Calendar grid + Agenda side-by-side on desktop --}}
                <div class="academic-calendar-layout">
                    <div class="min-w-0">
                        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-x-auto">
                            <div style="min-width:700px;">
                                {{-- Day header --}}
                                <div style="display:grid;grid-template-columns:repeat(7,minmax(0,1fr));">
                                    @foreach($monthlyDayNames as $idx => $dn)
                                        <div style="min-width:0;"
                                             class="flex items-center justify-center h-9 text-xs font-semibold border-b border-slate-100 {{ $idx === 0 ? 'text-red-500' : 'text-slate-500' }}">
                                            {{ $dn }}
                                        </div>
                                    @endforeach
                                </div>
                                {{-- Day cells --}}
                                <div style="display:grid;grid-template-columns:repeat(7,minmax(0,1fr));">
                                    @foreach($monthlyCalendarDays as $day)
                                        @php
                                            $ds = $day['date'];
                                            $dayEvts = $monthlyDayEvents[$ds] ?? collect();
                                            $dayEvtCount = $dayEvts->count();
                                            $visibleEvts = $dayEvts->take(3);
                                            $hiddenCount = $dayEvtCount - 3;
                                        @endphp
                                        <div style="min-width:0;min-height:110px;"
                                             class="border-b border-r border-slate-100 p-1.5
                                                    {{ $day['isCurrentMonth'] ? '' : 'bg-slate-50' }}
                                                    {{ $day['isToday'] ? 'ring-2 ring-emerald-400 ring-inset' : '' }}">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs font-semibold leading-none
                                                    {{ $day['isSunday'] ? 'text-red-500' : ($day['isCurrentMonth'] ? 'text-slate-900' : 'text-slate-300') }}
                                                    {{ $day['isToday'] ? 'bg-emerald-100 text-emerald-700 rounded-full w-5 h-5 flex items-center justify-center' : '' }}">
                                                    {{ $day['day'] }}
                                                </span>
                                                @if($dayEvtCount > 0 && $day['isCurrentMonth'])
                                                    <span class="text-[10px] font-medium text-slate-400">{{ $dayEvtCount }}</span>
                                                @endif
                                            </div>
                                            <div class="space-y-0.5">
                                                @foreach($visibleEvts as $evt)
                                                    @php $ecat = $categoryMap[$evt['category']] ?? $categoryMap['lainnya']; @endphp
                                                    <a href="{{ route('admin.akademik.kalender.edit', $evt['id']) }}"
                                                       title="{{ $evt['title'] }} — {{ $evt['start_date'] }} s.d {{ $evt['end_date'] }}{{ $evt['start_time'] ? ' — ' . $evt['start_time'] . '–' . $evt['end_time'] : '' }}{{ $evt['location'] ? ' — ' . $evt['location'] : '' }}"
                                                       class="block rounded px-1 py-0.5 {{ $ecat['bg_class'] }} {{ $ecat['text_class'] }} hover:opacity-80 transition">
                                                        <div class="text-[11px] font-medium leading-tight truncate">{{ $evt['title'] }}</div>
                                                        @if($evt['start_time'])
                                                            <div class="text-[10px] leading-tight opacity-70 truncate">{{ $evt['start_time'] }}–{{ $evt['end_time'] }}</div>
                                                        @endif
                                                    </a>
                                                @endforeach
                                                @if($hiddenCount > 0)
                                                    <span class="block text-[10px] text-slate-400 pl-1">+{{ $hiddenCount }} kegiatan lainnya</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sidebar: Agenda + Summary --}}
                    <aside class="academic-calendar-sidebar">
                        {{-- Agenda Bulan Ini --}}
                        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                            <h3 class="mb-3 text-sm font-semibold text-slate-900">Agenda {{ $indonesianMonths[$selMonth] }} {{ $selYear }}</h3>
                            <div class="space-y-2.5 max-h-[400px] overflow-y-auto">
                                @forelse($monthlyRawEvents as $evt)
                                    @php
                                        $ecat = $categoryMap[$evt['category']] ?? $categoryMap['lainnya'];
                                        $pic = $evt['person_in_charge'] ?? '';
                                        if (!$pic && ($evt['teacher'] ?? null)) {
                                            $pic = $evt['teacher']['name'] ?? $evt['teacher']['full_name'] ?? '';
                                        }
                                    @endphp
                                    <a href="{{ route('admin.akademik.kalender.edit', $evt['id']) }}"
                                       class="flex items-start gap-2.5 group">
                                        <div class="academic-legend-box {{ $ecat['bg_class'] }}">
                                            <span class="academic-legend-dot {{ $ecat['dot_class'] }}"></span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm font-medium text-slate-900 group-hover:text-emerald-700 transition leading-tight">
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
                                                @if($pic)
                                                    <span class="inline-flex items-center text-[10px] text-slate-400">{{ $pic }}</span>
                                                @endif
                                                @if($evt['location'] ?? null)
                                                    <span class="inline-flex items-center text-[10px] text-slate-400">{{ $evt['location'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                    @if(!$loop->last)
                                        <div class="border-t border-slate-100"></div>
                                    @endif
                                @empty
                                    <p class="text-sm text-slate-400 text-center py-4">Belum ada kegiatan pada bulan ini.</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Ringkasan Bulan --}}
                        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                            <h3 class="mb-3 text-sm font-semibold text-slate-900">Ringkasan Bulan</h3>
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-slate-600">Total Kegiatan</span>
                                    <span class="text-sm font-semibold text-slate-900">{{ $monthlySummary['total_events'] }}</span>
                                </div>
                                <div class="border-t border-slate-100"></div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-slate-600">Hari Libur</span>
                                    <span class="text-sm font-semibold text-slate-900">{{ $monthlySummary['hari_libur'] }} hari</span>
                                </div>
                                <div class="border-t border-slate-100"></div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-slate-600">Hari Asesmen</span>
                                    <span class="text-sm font-semibold text-slate-900">{{ $monthlySummary['hari_asesmen'] }} hari</span>
                                </div>
                                <div class="border-t border-slate-100"></div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-slate-600">Hari Berkegiatan</span>
                                    <span class="text-sm font-semibold text-slate-900">{{ $monthlySummary['hari_berkegiatan'] }} hari</span>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>

        <div x-show="tab === 'daftar'" x-cloak>
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50">
                                <th class="px-5 py-3 font-semibold text-slate-700">Tanggal</th>
                                <th class="px-5 py-3 font-semibold text-slate-700">Kegiatan</th>
                                <th class="px-5 py-3 font-semibold text-slate-700">Kategori</th>
                                <th class="px-5 py-3 font-semibold text-slate-700">Penanggung Jawab</th>
                                <th class="px-5 py-3 font-semibold text-slate-700">Sumber</th>
                                <th class="px-5 py-3 font-semibold text-slate-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($rawEvents as $evt)
                                @php
                                    $pic = $evt['person_in_charge'] ?? '';
                                    if (!$pic && ($evt['teacher'] ?? null)) {
                                        $pic = $evt['teacher']['name'] ?? $evt['teacher']['full_name'] ?? '';
                                    }
                                    $startDate = \Carbon\Carbon::parse($evt['start_date']);
                                    $endDate = \Carbon\Carbon::parse($evt['end_date']);
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-5 py-3 text-slate-600">
                                        {{ $startDate->format('d') }} {{ $indonesianMonths[$startDate->month] }}
                                        @if($evt['start_date'] !== $evt['end_date'])
                                            – {{ $endDate->format('d') }} {{ $indonesianMonths[$endDate->month] }} {{ $endDate->format('Y') }}
                                        @else
                                            {{ $startDate->format('Y') }}
                                        @endif
                                        @if($evt['start_time'] ?? null)
                                            <br><span class="text-xs text-slate-400">{{ $evt['start_time'] }}–{{ $evt['end_time'] ?? '—' }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 font-medium text-slate-900">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-block h-2 w-2 rounded-full {{ ($categoryMap[$evt['category']] ?? $categoryMap['lainnya'])['dot_class'] }}"></span>
                                            <span>{{ $evt['title'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex rounded-full {{ ($categoryMap[$evt['category']] ?? $categoryMap['lainnya'])['bg_class'] }} {{ ($categoryMap[$evt['category']] ?? $categoryMap['lainnya'])['text_class'] }} px-2.5 py-0.5 text-xs font-medium">
                                            {{ ($categoryMap[$evt['category']] ?? $categoryMap['lainnya'])['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-sm text-slate-600">
                                        {{ $pic ?: '-' }}
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-500">
                                            @if(($evt['source'] ?? 'sekolah') === 'pemerintah')
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M3 10h18M3 7l9-4 9 4M3 10v11m18-11v11"/>
                                                </svg>
                                                Pemerintah
                                            @else
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                </svg>
                                                Sekolah
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-1.5">
                                            <a href="{{ route('admin.akademik.kalender.edit', $evt['id']) }}"
                                               class="inline-flex items-center gap-1 rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('admin.akademik.kalender.destroy', $evt['id']) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1 rounded-lg border border-red-200 px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition"
                                                        onclick="return confirm('Hapus kegiatan kalender ini?')">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-12 text-center text-sm text-slate-400">
                                        Belum ada kegiatan untuk tahun ajaran ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-100 px-5 py-3 text-xs text-slate-400">
                    Menampilkan {{ $rawEvents->count() }} kegiatan
                </div>
            </div>
        </div>

        <div x-show="tab === 'rekap'" x-cloak>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-sm font-semibold text-slate-900">Rekap Hari Efektif Semester Ganjil</h3>
                    <div class="space-y-3">
                        @php
                            $rekapGanjil = [
                                ['label' => 'Total Hari', 'value' => 120],
                                ['label' => 'Hari Efektif', 'value' => 95],
                                ['label' => 'Hari Libur', 'value' => 25],
                                ['label' => 'Asesmen', 'value' => 7],
                            ];
                            $rekapGenap = [
                                ['label' => 'Total Hari', 'value' => 115],
                                ['label' => 'Hari Efektif', 'value' => 90],
                                ['label' => 'Hari Libur', 'value' => 25],
                                ['label' => 'Asesmen', 'value' => 6],
                            ];
                        @endphp
                        @foreach($rekapGanjil as $item)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">{{ $item['label'] }}</span>
                                <span class="text-sm font-semibold text-slate-900">{{ $item['value'] }}</span>
                            </div>
                            @if(!$loop->last)
                                <div class="border-t border-slate-100"></div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-sm font-semibold text-slate-900">Rekap Hari Efektif Semester Genap</h3>
                    <div class="space-y-3">
                        @foreach($rekapGenap as $item)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">{{ $item['label'] }}</span>
                                <span class="text-sm font-semibold text-slate-900">{{ $item['value'] }}</span>
                            </div>
                            @if(!$loop->last)
                                <div class="border-t border-slate-100"></div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="mt-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-sm font-semibold text-slate-900">Rekap Hari Efektif Tahunan</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50">
                                <th class="px-4 py-3 font-semibold text-slate-700">Bulan</th>
                                <th class="px-4 py-3 font-semibold text-slate-700">Total Hari</th>
                                <th class="px-4 py-3 font-semibold text-slate-700">Hari Efektif</th>
                                <th class="px-4 py-3 font-semibold text-slate-700">Hari Libur</th>
                                <th class="px-4 py-3 font-semibold text-slate-700">Asesmen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $rekapBulanan = [
                                    ['Juli', 22, 18, 4, 0],
                                    ['Agustus', 21, 17, 4, 0],
                                    ['September', 22, 18, 4, 0],
                                    ['Oktober', 21, 15, 4, 3],
                                    ['November', 21, 18, 3, 0],
                                    ['Desember', 21, 9, 10, 4],
                                    ['Januari', 21, 18, 3, 0],
                                    ['Februari', 20, 16, 4, 0],
                                    ['Maret', 23, 11, 12, 3],
                                    ['April', 20, 14, 6, 0],
                                    ['Mei', 21, 12, 9, 3],
                                    ['Juni', 22, 18, 4, 0],
                                ];
                            @endphp
                            @foreach($rekapBulanan as $rb)
                                <tr class="hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-900">{{ $rb[0] }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $rb[1] }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $rb[2] }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $rb[3] }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $rb[4] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
