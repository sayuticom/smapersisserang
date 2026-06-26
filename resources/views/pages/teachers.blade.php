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
    min-height: 255px;
}
.teacher-card-content {
    flex: 1;
    padding: 16px 24px 12px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.teacher-card-photo {
    width: 180px;
    flex: 0 0 180px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 18px;
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
.teacher-card-photo .no-photo {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    width: 100%;
}
.teacher-card-quote {
    background: #00583f;
    color: #fff;
    padding: 22px 28px;
    display: flex;
    gap: 16px;
}
.teacher-card-quote .quote-icon {
    font-size: 30px;
    line-height: 1;
    color: #fbbf24;
    flex-shrink: 0;
    margin-top: 2px;
    user-select: none;
}
.teacher-card-quote p {
    margin: 0;
    font-family: Georgia, serif;
    font-size: 18px;
    font-style: italic;
    line-height: 1.55;
    color: #fff;
}
@media (max-width: 640px) {
    .teacher-card-main {
        min-height: 220px;
    }
    .teacher-card-content {
        padding: 14px 20px 10px;
    }
    .teacher-card-photo {
        width: 135px;
        flex-basis: 135px;
        padding: 12px;
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
        padding: 18px 22px;
    }
    .teacher-card-quote p {
        font-size: 16px;
    }
    .teacher-card-quote .quote-icon {
        font-size: 24px;
    }
}
</style>
<div class="bg-[#FBF7EF]">
    <div class="border-t border-amber-100">
        <div class="max-w-7xl mx-auto px-4 pt-14 pb-20 sm:pt-16">
            <div class="max-w-3xl mb-10">
                <p class="text-sm font-bold tracking-[0.18em] uppercase text-[#D4A017]">STRUKTUR PENGAJAR</p>
                <h2 class="text-3xl font-bold text-gray-900 mt-3">Guru Berdasarkan Mata Pelajaran</h2>
                <p class="text-gray-600 mt-4 leading-relaxed">
                    Daftar mata pelajaran beserta guru pengampu di SMA Persis Serang.
                </p>
            </div>

            @if($teacherCards->isEmpty())
                <div class="bg-white rounded-2xl border border-amber-100 p-8 text-center shadow-sm">
                    <p class="text-gray-500 font-medium">Data guru belum tersedia.</p>
                </div>
            @else
                <div class="grid md:grid-cols-2 gap-6">
                    @foreach($teacherCards as $teacher)
                        @php
                            $words = preg_split('/\s+/', trim($teacher->name));
                            $initials = collect($words)
                                ->filter()
                                ->take(2)
                                ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
                                ->implode('');
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
                                        <div class="mt-2 space-y-1">
                                            @foreach($teacher->subjects as $subject)
                                                <p class="text-amber-700 font-semibold">{{ $subject->name }}</p>
                                            @endforeach
                                        </div>
                                    @elseif($teacher->position)
                                        <p class="text-amber-700 font-semibold mt-2">{{ $teacher->position }}</p>
                                    @else
                                        <p class="text-sm text-slate-400 mt-2">Mapel belum diatur</p>
                                    @endif

                                    @if($teacher->description)
                                        <p class="text-slate-600 text-sm mt-2 leading-relaxed">{{ $teacher->description }}</p>
                                    @elseif($primarySubject?->description)
                                        <p class="text-slate-600 text-sm mt-2 leading-relaxed">{{ $primarySubject->description }}</p>
                                    @endif
                                </div>

                                <div class="teacher-card-photo">
                                    @if($teacher->photo_path)
                                        <div class="teacher-photo-frame">
                                            <div class="teacher-photo-ring">
                                                <div class="teacher-photo-inner">
                                                    <img src="{{ asset('storage/' . $teacher->photo_path) }}"
                                                         alt="{{ $teacher->name }}">
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="no-photo">
                                            <span class="flex h-20 w-20 items-center justify-center rounded-full border border-amber-200 bg-emerald-100 text-xl font-bold text-emerald-800 shadow-sm">
                                                {{ $initials ?: 'G' }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if(filled($teacher->teacher_quote))
                                <div class="teacher-card-quote">
                                    <span class="quote-icon">&ldquo;</span>
                                    <p>{{ $teacher->teacher_quote }}</p>
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
