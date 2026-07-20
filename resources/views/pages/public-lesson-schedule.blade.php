@extends('layouts.public')

@php
    $pageTitle = 'Jadwal Mata Pelajaran';
    $pageDescription = 'Lihat jadwal pelajaran SMA Persis Serang berdasarkan tahun ajaran, semester, dan kelas.';
    $ogImage = $schoolSetting?->meta_image
        ? asset('storage/' . $schoolSetting->meta_image)
        : asset('images/og-sma-persis-serang.jpg');
    $schedulableTypes = ['pelajaran', 'kegiatan_khusus'];
    $slotsByOrder = $timeSlotsByDay->flatten()->groupBy('sort_order')->sortKeys();
@endphp

@section('title', 'Jadwal Mata Pelajaran | SMA Persis Serang')

@section('meta')
    <meta name="description" content="{{ $pageDescription }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ request()->fullUrl() }}">
    <meta property="og:title" content="Jadwal Mata Pelajaran | SMA Persis Serang">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Jadwal Mata Pelajaran | SMA Persis Serang">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
@endsection

@section('content')
    <section class="bg-gradient-to-br from-emerald-950 via-emerald-900 to-emerald-800 text-white">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-12 lg:px-8">
            <span class="inline-flex rounded-full border border-amber-300/30 bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-widest text-amber-300">Akademik</span>
            <h1 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">{{ $pageTitle }}</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-emerald-100 sm:text-base">Lihat jadwal pelajaran SMA Persis Serang berdasarkan kelas.</p>
        </div>
    </section>

    <section class="bg-slate-50 py-8 sm:py-12">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if($academicYears->isEmpty())
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-8 text-center text-sm font-semibold text-amber-900">
                    Jadwal belum tersedia karena tahun ajaran aktif belum ditentukan.
                </div>
            @elseif($classes->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-8 text-center text-sm font-semibold text-slate-700">Belum ada kelas aktif.</div>
            @elseif(!$selectedClass)
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-10 text-center">
                    <h2 class="text-base font-bold text-slate-800">Pilih kelas untuk melihat jadwal</h2>
                    <p class="mt-1 text-sm text-slate-600">Gunakan pilihan kelas di atas untuk menampilkan jadwal pelajaran.</p>
                </div>
            @else
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">Jadwal Mingguan</p>
                    <form method="GET" action="{{ route('public.lesson-schedule') }}" class="mt-1 w-full sm:max-w-xs">
                        <select name="class" onchange="this.form.submit()"
                                class="w-full rounded-lg border-slate-300 bg-white text-sm font-semibold text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @foreach($classes as $class)
                                <option value="{{ $class->name }}" @selected($selectedClassValue === $class->name)>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </form>
                    <p class="mt-1 text-sm text-slate-600">Tahun Ajaran {{ $selectedAcademicYear->academic_year }} · Semester {{ ucfirst($semester) }}</p>
                </div>

                @if(!$hasSchedules)
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-10 text-center text-sm font-semibold text-slate-700">
                    Jadwal untuk kelas ini belum tersedia.
                </div>
                @else

                {{-- Mobile --}}
                <div class="sm:hidden" x-data="{ day: 'Senin' }">
                    <div class="mb-4 overflow-x-auto pb-1">
                        <div class="inline-flex min-w-max gap-1 rounded-xl border border-slate-200 bg-slate-100 p-1">
                            @foreach($days as $day)
                                <button type="button" @click="day = '{{ $day }}'"
                                        :class="day === '{{ $day }}' ? 'bg-white text-emerald-800 shadow-sm' : 'text-slate-600'"
                                        class="min-w-[76px] rounded-lg px-3 py-2 text-sm font-bold transition">{{ $day }}</button>
                            @endforeach
                        </div>
                    </div>

                    @foreach($days as $day)
                        <div x-show="day === '{{ $day }}'" x-cloak class="space-y-2">
                            @forelse($timeSlotsByDay->get($day, collect()) as $slot)
                                @php
                                    $entry = $schedules->get($day . '|' . $slot->id);
                                    $isSchedulable = in_array($slot->type, $schedulableTypes, true);
                                    $specialStyle = match($slot->type) {
                                        'istirahat' => 'border-amber-200 bg-amber-50 text-amber-900',
                                        'ishoma' => 'border-sky-200 bg-sky-50 text-sky-900',
                                        'upacara' => 'border-rose-200 bg-rose-50 text-rose-900',
                                        'pembiasaan' => 'border-violet-200 bg-violet-50 text-violet-900',
                                        default => 'border-slate-200 bg-slate-100 text-slate-800',
                                    };
                                    $dayStyle = match($day) {
                                        'Senin' => 'border-emerald-200 bg-emerald-50/70',
                                        'Selasa' => 'border-sky-200 bg-sky-50/70',
                                        'Rabu' => 'border-amber-100 bg-amber-50/70',
                                        'Kamis' => 'border-violet-200 bg-violet-50/70',
                                        'Jumat' => 'border-yellow-200 bg-yellow-50/70',
                                        'Sabtu' => 'border-pink-200 bg-pink-50/70',
                                    };
                                @endphp
                                <article class="rounded-xl border p-3.5 transition-all duration-150 {{ $isSchedulable ? ($entry ? $dayStyle.' shadow-sm' : 'border-slate-200 bg-white/70') : $specialStyle }}">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            @if($entry)
                                                <p class="text-xs font-semibold text-slate-400">{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</p>
                                                <p class="mt-1 font-extrabold leading-snug text-slate-950">{{ $entry->schoolSubject->name }}</p>
                                                @if($entry->teacher)
                                                    <p class="mt-0.5 text-sm font-medium text-slate-500">{{ $entry->teacher->name }}</p>
                                                @else
                                                    <p class="mt-0.5 text-sm font-medium text-slate-400">Guru belum ditentukan</p>
                                                @endif
                                                @if($entry->room)
                                                    <p class="mt-0.5 text-xs font-medium text-slate-400">Ruang {{ $entry->room }}</p>
                                                @endif
                                            @else
                                                <h3 class="text-sm font-bold">{{ $slot->name }}</h3>
                                                <p class="mt-0.5 text-xs {{ $isSchedulable ? 'text-slate-500' : 'opacity-80' }}">{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</p>
                                            @endif
                                        </div>
                                        @unless($isSchedulable)
                                            <span class="shrink-0 rounded-full bg-white/70 px-2 py-0.5 text-[10px] font-bold uppercase">Kegiatan umum</span>
                                        @endunless
                                    </div>
                                </article>
                            @empty
                                <p class="rounded-xl border border-dashed border-slate-300 bg-white px-4 py-8 text-center text-sm text-slate-500">Belum ada slot aktif pada hari {{ $day }}.</p>
                            @endforelse
                        </div>
                    @endforeach
                </div>

                {{-- Desktop --}}
                <style>
                    @media (min-width: 1024px) {
                        .public-lesson-schedule-wrapper {
                            overflow: visible !important;
                        }
                        .public-lesson-schedule-table thead th {
                            position: sticky !important;
                            top: 5rem !important;
                            z-index: 40 !important;
                            background: #042f2e !important;
                        }
                    }
                </style>
                <div class="hidden rounded-2xl border border-slate-300 bg-white shadow-sm sm:block">
                    <div class="public-lesson-schedule-wrapper relative overflow-x-auto lg:overflow-visible">
                        <table class="public-lesson-schedule-table w-full min-w-[1120px] border-separate border-spacing-0 text-left">
                            <thead>
                                <tr>
                                    <th scope="col" class="sticky top-0 z-40 w-36 border-r border-emerald-800 bg-emerald-950 px-4 py-3.5 text-xs font-bold uppercase tracking-wider text-white">Jam</th>
                                    @foreach($days as $day)
                                        <th scope="col" class="sticky top-0 z-40 border-r border-emerald-800 bg-emerald-950 px-3 py-3.5 text-center text-sm font-bold text-white last:border-r-0">{{ $day }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach($slotsByOrder as $sortOrder => $rowSlots)
                                    @php $referenceSlot = $rowSlots->first(); @endphp
                                    <tr class="align-top">
                                        <th scope="row" class="border-r border-slate-200 bg-slate-50 px-3 py-3">
                                            <span class="block text-xs font-bold text-slate-900">{{ $referenceSlot->name }}</span>
                                        </th>
                                        @foreach($days as $day)
                                            @php
                                                $slot = $timeSlotsByDay->get($day, collect())->firstWhere('sort_order', $sortOrder);
                                                $entry = $slot ? $schedules->get($day . '|' . $slot->id) : null;
                                                $isSchedulable = $slot && in_array($slot->type, $schedulableTypes, true);
                                                $specialStyle = $slot ? match($slot->type) {
                                                    'istirahat' => 'border-amber-200 bg-amber-50 text-amber-900',
                                                    'ishoma' => 'border-sky-200 bg-sky-50 text-sky-900',
                                                    'upacara' => 'border-rose-200 bg-rose-50 text-rose-900',
                                                    'pembiasaan' => 'border-violet-200 bg-violet-50 text-violet-900',
                                                    default => 'border-slate-200 bg-slate-100 text-slate-800',
                                                } : '';
                                                $dayStyle = match($day) {
                                                    'Senin' => 'border-emerald-200 bg-emerald-50/70',
                                                    'Selasa' => 'border-sky-200 bg-sky-50/70',
                                                    'Rabu' => 'border-amber-100 bg-amber-50/70',
                                                    'Kamis' => 'border-violet-200 bg-violet-50/70',
                                                    'Jumat' => 'border-yellow-200 bg-yellow-50/70',
                                                    'Sabtu' => 'border-pink-200 bg-pink-50/70',
                                                };
                                            @endphp
                                            <td class="h-28 border-r border-slate-200 p-2 last:border-r-0">
                                                @if(!$slot)
                                                    <div class="h-full min-h-24 rounded-lg bg-slate-50"></div>
                                                @elseif(!$isSchedulable)
                                                    <div class="flex h-full min-h-24 flex-col items-center justify-center rounded-lg border px-2 py-3 text-center {{ $specialStyle }}">
                                                        <span class="text-xs font-bold">{{ $slot->name }}</span>
                                                        <span class="mt-1 text-[10px] font-semibold opacity-75">{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</span>
                                                    </div>
                                                @else
                                                    <div class="flex h-full min-h-24 flex-col rounded-lg p-2.5 transition-all duration-150 {{ $entry ? $dayStyle.' shadow-sm hover:shadow-md' : 'border border-dashed border-slate-200 bg-white/60' }}">
                                                        @if($entry)
                                                            <span class="text-[10px] font-semibold text-slate-400">{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</span>
                                                            <p class="mt-1 text-sm font-extrabold leading-snug text-slate-950">{{ $entry->schoolSubject->name }}</p>
                                                            @if($entry->teacher)
                                                                <p class="mt-0.5 text-xs font-medium leading-snug text-slate-500">{{ $entry->teacher->name }}</p>
                                                            @else
                                                                <p class="mt-0.5 text-xs font-medium text-slate-400">Guru belum ditentukan</p>
                                                            @endif
                                                            @if($entry->room)
                                                                <p class="mt-0.5 text-[11px] font-medium text-slate-400">Ruang {{ $entry->room }}</p>
                                                            @endif
                                                        @else
                                                            <div class="flex flex-1 flex-col items-center justify-center">
                                                                <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">{{ $slot->name }}</span>
                                                                <span class="mt-0.5 text-[10px] font-medium text-slate-300">{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            @endif
        </div>
    </section>
@endsection
