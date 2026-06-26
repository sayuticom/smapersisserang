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
<div class="bg-[#FBF7EF]">
    <div class="border-t border-amber-100">
        <div class="max-w-7xl mx-auto px-4 pt-14 pb-20 sm:pt-16">
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
                            <div class="teacher-card-main">
                                <div class="teacher-card-content">
                                    <span class="inline-block w-fit bg-gradient-to-r from-amber-100 to-amber-300 text-emerald-950 rounded-lg px-3 py-1 text-xs font-bold uppercase tracking-wide">
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
        </div>
    </div>
</div>
@endsection
