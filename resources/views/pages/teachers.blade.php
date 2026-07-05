@extends('layouts.public')

@section('content')
<style>
.teacher-card {
    overflow: hidden;
    border: 1px solid #f3d46b;
    border-radius: 16px;
    background: #fffaf0;
    box-shadow: 0 1px 3px rgba(0,0,0,.06);
}
.teacher-card-main {
    display: flex;
    align-items: stretch;
    min-height: 190px;
}
.teacher-card-content {
    flex: 1;
    padding: 18px 24px 12px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.teacher-card-photo {
    width: 190px;
    flex: 0 0 190px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px 16px;
}
.teacher-photo-frame {
    width: 150px;
    height: 150px;
    border-radius: 9999px;
    border: 3px solid #e4b93d;
    padding: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    flex-shrink: 0;
}
.teacher-photo-ring {
    width: 100%;
    height: 100%;
    border-radius: 9999px;
    border: 1.5px dashed #d9b14a;
    padding: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fffdf7;
}
.teacher-photo-inner {
    width: 100%;
    height: 100%;
    border-radius: 9999px;
    overflow: hidden;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
}
.teacher-photo-inner img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center top;
    display: block;
}
.teacher-card-quote {
    background: #00583f;
    color: #fff;
    padding: 14px 24px;
    min-height: 78px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.teacher-card-quote p::before {
    content: "\201C";
    color: #fbbf24;
    font-size: 28px;
    font-weight: 700;
    font-style: normal;
    margin-right: 8px;
    position: relative;
    top: 2px;
}
.teacher-list-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 24px;
    align-items: stretch;
}
@media (max-width: 768px) {
    .teacher-list-grid {
        grid-template-columns: 1fr;
    }
}
.teacher-card-quote p {
    margin: 0;
    font-family: Georgia, serif;
    font-size: 19px;
    font-style: italic;
    line-height: 1.55;
    color: #fff;
}
@media (max-width: 640px) {
    .teacher-card-main {
        min-height: 170px;
    }
    .teacher-card-content {
        padding: 16px 18px 10px;
    }
    .teacher-card-photo {
        width: 135px;
        flex-basis: 135px;
        padding: 8px 10px;
    }
    .teacher-photo-frame {
        width: 110px;
        height: 110px;
        padding: 5px;
    }
    .teacher-photo-ring {
        padding: 4px;
    }
    .teacher-card-quote {
        padding: 12px 18px;
        gap: 10px;
    }
    .teacher-card-quote p {
        font-size: 17px;
    }
    .teacher-card-quote p::before {
        font-size: 24px;
    }
}
</style>
@php
    $hasHeroBg = isset($heroImages) && $heroImages->isNotEmpty();
    $heroBgUrl = $hasHeroBg ? asset('storage/' . $heroImages->first()->image_path) : null;
    $hasHeadmaster = $headmaster && $headmaster->exists;
    $hasCategoryTeachers = $groupedByCategory->isNotEmpty();
    $hasOrphanTeachers = $orphanTeachers->isNotEmpty();
@endphp

<section class="relative isolate overflow-hidden bg-[#052E1F]">
    @if($heroBgUrl)
        <div class="absolute inset-0 bg-cover bg-center"
             style="background-image: linear-gradient(rgba(6,78,59,.65), rgba(6,78,59,.65)), url('{{ $heroBgUrl }}')">
        </div>
    @else
        <div class="absolute inset-0 bg-gradient-to-br from-[#052E1F] via-[#063f2a] to-[#0F6B3A]"></div>
        <div class="absolute inset-0 opacity-[0.08]"
             style="background-image: linear-gradient(135deg, rgba(255,255,255,.45) 1px, transparent 1px); background-size: 42px 42px;"></div>
    @endif

    <div class="absolute inset-x-0 bottom-0 h-48 bg-gradient-to-t from-emerald-950/75 via-emerald-950/30 to-transparent"></div>

    <div class="relative z-10 mx-auto flex max-w-7xl flex-col items-center justify-center px-4 py-12 text-center sm:px-6 lg:py-20">
        <div class="inline-flex items-center gap-2 rounded-full border border-amber-300/70 bg-emerald-950/70 px-5 py-2 text-sm font-semibold text-amber-300">
            <span class="h-2 w-2 rounded-full bg-amber-300"></span>
            TENAGA PENDIDIK
        </div>
        <h1 class="mt-6 text-4xl font-bold leading-tight text-white sm:text-5xl lg:text-6xl">
            {{ $websitePage?->title ?? 'Profil Guru' }}
        </h1>
        <div class="mx-auto mt-4 h-1.5 w-32 rounded-full bg-gradient-to-r from-amber-400 to-yellow-300"></div>
        <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-white lg:text-xl">
            {{ $websitePage?->subtitle ?? 'Tenaga pendidik profesional yang berdedikasi tinggi' }}
        </p>
    </div>
</section>

<div class="bg-[#FBF7EF]">
    <div class="max-w-7xl mx-auto px-4 pb-20" style="padding-top: 2rem;">

        <div class="mb-8 flex flex-wrap items-center justify-center gap-2 border-b border-amber-200/60 pb-4">
            <a href="{{ route('public.teachers', ['tab' => 'guru']) }}"
               class="inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-semibold transition-colors
                      {{ ($tab ?? 'guru') === 'guru' ? 'bg-[#0F6B3A] text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200 hover:bg-emerald-50 hover:text-[#0F6B3A]' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2m8-10a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Guru &amp; Tendik
            </a>
            <a href="{{ route('public.teachers', ['tab' => 'struktur']) }}"
               class="inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-semibold transition-colors
                      {{ ($tab ?? 'guru') === 'struktur' ? 'bg-[#0F6B3A] text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200 hover:bg-emerald-50 hover:text-[#0F6B3A]' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 6h6M9 12h6m-6 6h6M5 6h.01M5 12h.01M5 18h.01M19 6h.01M19 12h.01M19 18h.01"/>
                </svg>
                Struktur Organisasi
            </a>
        </div>

        @if(($tab ?? 'guru') === 'struktur')
            @php
                $schoolName = $schoolSetting->school_name ?? 'SMA Persis Serang';
                $displayName = $schoolSetting->school_name ?? 'SMA Persis Serang';
                $heroBg = $schoolSetting?->building_image_path ? asset('storage/' . $schoolSetting->building_image_path) : null;
                $sectionTitle = $strukturWebsitePage?->subtitle ?? 'Bagan Organisasi ' . $displayName;
                $introText = $strukturWebsitePage?->content ?? 'Struktur organisasi SMA Persis Serang disusun untuk mendukung pengelolaan sekolah berbasis pendidikan, pembinaan akhlak, dan sistem boarding school.';
                $topLevelItems = collect($organisasi)->where('level', 1);
                $secondLevelItems = collect($organisasi)->where('level', 2);
                $kepalaSekolah = collect($organisasi)->firstWhere('key', 'kepala-sekolah') ?? collect($organisasi)->firstWhere('level', 2);
                $unitDiBawahKepalaSekolah = $kepalaSekolah['children'] ?? [];
                $activeLevels = collect($organisasi)->pluck('level')->unique()->count();
                $totalUnits = collect($unitDiBawahKepalaSekolah)->count();
                $totalMembers = collect($unitDiBawahKepalaSekolah)->sum(fn($unit) => count($unit['anggota'] ?? []));
            @endphp

            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-bold uppercase tracking-[0.24em] text-[#D4A017]">STRUKTUR ORGANISASI</p>
                <h2 class="mt-3 font-serif text-3xl font-bold text-[#052E1F] sm:text-4xl">{{ $sectionTitle }}</h2>
                <p class="mt-4 text-lg leading-8 text-emerald-900/70">{{ $introText }}</p>
            </div>

            <div class="mt-12">
                @if(empty($organisasi))
                    <div class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-8 text-center">
                        <h3 class="font-serif text-2xl font-bold text-[#052E1F]">Data struktur organisasi belum tersedia.</h3>
                        <p class="mt-3 text-sm leading-6 text-emerald-900/70">Silakan lengkapi data struktur organisasi melalui dashboard admin.</p>
                    </div>
                @else
                    <div class="grid gap-6 sm:grid-cols-2">
                        @foreach($topLevelItems as $item)
                            <div class="group rounded-2xl border-2 border-[#D4A017]/40 bg-gradient-to-br from-[#052E1F] to-[#0F6B3A] p-6 text-center shadow-lg shadow-emerald-950/15 transition hover:-translate-y-1 hover:shadow-xl">
                                @include('pages.partials.organization-avatar', [
                                    'photoUrl' => $item['photo_url'] ?? null,
                                    'name' => $item['person_display_name'] ?? $item['jabatan'],
                                    'tone' => 'dark',
                                ])
                                <h3 class="text-lg font-bold text-white">{{ $item['jabatan'] }}</h3>
                                @if($item['person_display_name'] ?? null)
                                    <p class="mt-1 text-sm font-semibold text-amber-300">{{ $item['person_display_name'] }}</p>
                                @endif
                                @if($item['deskripsi'] ?? null)
                                    <p class="mt-2 text-sm leading-relaxed text-emerald-100/80">{{ $item['deskripsi'] }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($topLevelItems->isNotEmpty() && $secondLevelItems->isNotEmpty())
                        <div class="flex justify-center py-4">
                            <div class="h-8 w-0.5 bg-gradient-to-b from-[#D4A017] to-[#0F6B3A]"></div>
                        </div>
                    @endif

                    <div class="grid gap-6 sm:grid-cols-2">
                        @foreach($secondLevelItems as $item)
                            @php $isKepsek = ($item['card_type'] ?? null) === 'principal' || str_contains($item['jabatan'], 'Kepala SMA'); @endphp
                            <div class="{{ $isKepsek ? 'sm:col-span-1' : '' }} rounded-2xl border-2 border-emerald-200 bg-gradient-to-br from-white to-emerald-50 p-6 text-center shadow-lg shadow-emerald-950/10 transition hover:-translate-y-1 hover:shadow-xl {{ $isKepsek ? 'ring-2 ring-[#D4A017]/40' : 'ring-1 ring-amber-200' }}">
                                @if($isKepsek)
                                    <div class="mx-auto mb-2 inline-flex items-center gap-1 rounded-full bg-[#D4A017]/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-[#D4A017]">
                                        <span class="h-1.5 w-1.5 rounded-full bg-[#D4A017]"></span>
                                        Pimpinan Sekolah
                                    </div>
                                @endif
                                @include('pages.partials.organization-avatar', [
                                    'photoUrl' => $item['photo_url'] ?? null,
                                    'name' => $item['person_display_name'] ?? $item['jabatan'],
                                ])
                                <h3 class="text-xl font-bold {{ $isKepsek ? 'text-[#052E1F]' : 'text-gray-800' }}">{{ $item['jabatan'] }}</h3>
                                @if($item['person_display_name'] ?? null)
                                    <p class="mt-1 text-sm font-semibold text-[#0F6B3A]">{{ $item['person_display_name'] }}</p>
                                @endif
                                @if($item['deskripsi'] ?? null)
                                    <p class="mt-2 text-sm leading-relaxed text-gray-500">{{ $item['deskripsi'] }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if(count($unitDiBawahKepalaSekolah))
                        <div class="relative py-4">
                            <div class="mx-auto hidden h-8 w-0.5 bg-gradient-to-b from-[#0F6B3A] to-[#0F6B3A] sm:block"></div>
                            <div class="relative hidden sm:block">
                                <div class="absolute left-[12.5%] right-[12.5%] top-0 h-0.5 bg-[#0F6B3A]/30"></div>
                            </div>
                        </div>
                    @endif

                    @if(count($unitDiBawahKepalaSekolah))
                        <div class="mt-2 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                            @foreach($unitDiBawahKepalaSekolah as $bawah)
                                <div class="group rounded-xl border border-emerald-100 bg-white p-5 shadow-md shadow-emerald-950/5 transition hover:-translate-y-1 hover:border-emerald-200 hover:shadow-lg">
                                    @include('pages.partials.organization-avatar', [
                                        'photoUrl' => $bawah['photo_url'] ?? null,
                                        'name' => $bawah['person_display_name'] ?? $bawah['jabatan'],
                                    ])
                                    <div class="mb-3 text-center">
                                        <h4 class="text-sm font-bold text-[#052E1F] leading-tight">{{ $bawah['jabatan'] }}</h4>
                                    </div>
                                    @if($bawah['person_display_name'] ?? null)
                                        <p class="mb-3 text-sm font-semibold text-[#0F6B3A]">{{ $bawah['person_display_name'] }}</p>
                                    @endif
                                    @if($bawah['anggota'] ?? null)
                                        <ul class="space-y-1.5 border-t border-emerald-50 pt-3">
                                            @foreach($bawah['anggota'] as $anggota)
                                                <li class="flex items-center gap-2 text-sm text-gray-600">
                                                    <span class="h-1.5 w-1.5 flex-shrink-0 rounded-full bg-[#D4A017]/60"></span>
                                                    {{ $anggota }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>
        @else
            @php
                $hasHeadmaster = $headmaster && $headmaster->exists;
                $hasCategoryTeachers = $groupedByCategory->isNotEmpty();
                $hasOrphanTeachers = $orphanTeachers->isNotEmpty();
            @endphp

            @if(!$hasHeadmaster && !$hasCategoryTeachers && !$hasOrphanTeachers)
                <div class="bg-white rounded-2xl border border-amber-100 p-8 text-center shadow-sm">
                    <p class="text-gray-500 font-medium">Data guru belum tersedia.</p>
                </div>
            @else
                @if($hasHeadmaster)
                    <div class="max-w-lg mx-auto mb-12">
                        @php
                            $teacher = $headmaster;
                            $primarySubject = $teacher->subjects->first();
                        @endphp
                        <article class="teacher-card">
                            <div class="teacher-card-main gap-6" style="padding-top: 2rem;">
                                <div class="teacher-card-content">
                                    <span class="inline-block w-fit bg-gradient-to-r from-amber-100 to-amber-300 text-emerald-950 rounded-lg px-3 py-1 text-xs font-bold uppercase tracking-wide mb-3">
                                        {{ $teacher->label }}
                                    </span>
                                    <p class="text-xl font-bold text-emerald-950 mt-2 leading-tight">{{ $teacher->name }}</p>
                                    @if($teacher->position)
                                        <p class="text-amber-700 font-semibold mt-1">{{ $teacher->position }}</p>
                                    @endif
                                    @if($teacher->description)
                                        <p class="text-slate-600 text-sm mt-1 leading-relaxed">{{ $teacher->description }}</p>
                                    @elseif($primarySubject?->description)
                                        <p class="text-slate-600 text-sm mt-1 leading-relaxed">{{ $primarySubject->description }}</p>
                                    @endif
                                </div>
                                @if($teacher->photo_path)
                                    <div class="teacher-card-photo">
                                        <div class="teacher-photo-frame">
                                            <div class="teacher-photo-ring">
                                                <div class="teacher-photo-inner">
                                                    <img src="{{ asset('storage/' . $teacher->photo_path) }}"
                                                         alt="{{ $teacher->name }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @if(filled($teacher->teacher_quote))
                                <div class="teacher-card-quote">
                                    <p>{{ $teacher->teacher_quote }}</p>
                                </div>
                            @endif
                        </article>
                    </div>

                    <div class="max-w-3xl mb-10">
                        <p class="text-sm font-bold tracking-[0.18em] uppercase text-[#D4A017]">STRUKTUR PENGAJAR</p>
                        <h2 class="text-3xl font-bold text-gray-900 mt-3">Guru Berdasarkan Mata Pelajaran</h2>
                        <p class="text-gray-600 mt-4 leading-relaxed">
                            Daftar mata pelajaran beserta guru pengampu di SMA Persis Serang.
                        </p>
                    </div>
                @else
                    <div class="max-w-3xl mb-10">
                        <p class="text-sm font-bold tracking-[0.18em] uppercase text-[#D4A017]">STRUKTUR PENGAJAR</p>
                        <h2 class="text-3xl font-bold text-gray-900 mt-3">Guru Berdasarkan Mata Pelajaran</h2>
                        <p class="text-gray-600 mt-4 leading-relaxed">
                            Daftar mata pelajaran beserta guru pengampu di SMA Persis Serang.
                        </p>
                    </div>
                @endif

                @if($hasCategoryTeachers)
                    @foreach($groupedByCategory as $category => $categoryGroup)
                        @if($categoryGroup['teachers']->isNotEmpty())
                            <div class="mb-10 last:mb-0">
                                <div class="flex items-center gap-4 mb-6">
                                    <h3 class="text-lg font-bold text-emerald-950 uppercase tracking-wide">{{ $categoryGroup['category_label'] }}</h3>
                                    <div class="flex-1 h-px bg-gradient-to-r from-amber-200 to-transparent"></div>
                                </div>
                                <div class="teacher-list-grid">
                                    @foreach($categoryGroup['teachers'] as $teacher)
                                        @php
                                            $primarySubject = $teacher->subjects->first();
                                        @endphp
                                        <article class="teacher-card">
                                            <div class="teacher-card-main">
                                                <div class="teacher-card-content">
                                                    <span class="inline-block w-fit bg-gradient-to-r from-amber-100 to-amber-300 text-emerald-950 rounded-lg px-3 py-1 text-xs font-bold uppercase tracking-wide">
                                                        {{ $teacher->label }}
                                                    </span>
                                                    <p class="text-xl font-bold text-emerald-950 mt-2 leading-tight">{{ $teacher->name }}</p>
                                                    @if($teacher->subjects->isNotEmpty())
                                                        <div class="mt-1 space-y-1">
                                                            @foreach($teacher->subjects as $s)
                                                                <p class="text-amber-700 font-semibold">{{ $s->name }}</p>
                                                            @endforeach
                                                        </div>
                                                    @elseif($teacher->position)
                                                        <p class="text-amber-700 font-semibold mt-1">{{ $teacher->position }}</p>
                                                    @else
                                                        <p class="text-sm text-slate-400 mt-1">Mapel belum diatur</p>
                                                    @endif
                                                    @if($teacher->description)
                                                        <p class="text-slate-600 text-sm mt-1 leading-relaxed">{{ $teacher->description }}</p>
                                                    @elseif($primarySubject?->description)
                                                        <p class="text-slate-600 text-sm mt-1 leading-relaxed">{{ $primarySubject->description }}</p>
                                                    @endif
                                                </div>
                                                @if($teacher->photo_path)
                                                    <div class="teacher-card-photo">
                                                        <div class="teacher-photo-frame">
                                                            <div class="teacher-photo-ring">
                                                                <div class="teacher-photo-inner">
                                                                    <img src="{{ asset('storage/' . $teacher->photo_path) }}"
                                                                         alt="{{ $teacher->name }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            @if(filled($teacher->teacher_quote))
                                                <div class="teacher-card-quote">
                                                    <p>{{ $teacher->teacher_quote }}</p>
                                                </div>
                                            @endif
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif

                @if($hasOrphanTeachers)
                    <div class="mb-10">
                    <div class="teacher-list-grid">
                        @foreach($orphanTeachers as $teacher)
                                @php
                                    $primarySubject = $teacher->subjects->first();
                                @endphp
                                <article class="teacher-card">
                                    <div class="teacher-card-main">
                                        <div class="teacher-card-content">
                                            <span class="inline-block w-fit bg-gradient-to-r from-amber-100 to-amber-300 text-emerald-950 rounded-lg px-3 py-1 text-xs font-bold uppercase tracking-wide">
                                                {{ $teacher->label }}
                                            </span>
                                            <p class="text-xl font-bold text-emerald-950 mt-2 leading-tight">{{ $teacher->name }}</p>
                                            @if($teacher->subjects->isNotEmpty())
                                                <div class="mt-1 space-y-1">
                                                    @foreach($teacher->subjects as $s)
                                                        <p class="text-amber-700 font-semibold">{{ $s->name }}</p>
                                                    @endforeach
                                                </div>
                                            @elseif($teacher->position)
                                                <p class="text-amber-700 font-semibold mt-1">{{ $teacher->position }}</p>
                                            @else
                                                <p class="text-sm text-slate-400 mt-1">Mapel belum diatur</p>
                                            @endif
                                            @if($teacher->description)
                                                <p class="text-slate-600 text-sm mt-1 leading-relaxed">{{ $teacher->description }}</p>
                                            @elseif($primarySubject?->description)
                                                <p class="text-slate-600 text-sm mt-1 leading-relaxed">{{ $primarySubject->description }}</p>
                                            @endif
                                        </div>
                                        @if($teacher->photo_path)
                                            <div class="teacher-card-photo">
                                                <div class="teacher-photo-frame">
                                                    <div class="teacher-photo-ring">
                                                        <div class="teacher-photo-inner">
                                                            <img src="{{ asset('storage/' . $teacher->photo_path) }}"
                                                                 alt="{{ $teacher->name }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @if(filled($teacher->teacher_quote))
                                        <div class="teacher-card-quote">
                                            <p>{{ $teacher->teacher_quote }}</p>
                                        </div>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        @endif
        </div>
    </div>
@endsection
