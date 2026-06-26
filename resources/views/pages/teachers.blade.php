@extends('layouts.public')

@section('content')
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

                        <article class="overflow-hidden rounded-2xl border border-yellow-200 bg-[#fffaf0] shadow-sm">
                            <div class="grid min-h-[255px] grid-cols-[1fr_140px] md:grid-cols-[1fr_180px]">
                                <div class="p-6 flex flex-col">
                                    <span class="inline-block w-fit bg-gradient-to-r from-amber-100 to-amber-300 text-emerald-950 rounded-lg px-3 py-1 text-xs font-bold uppercase tracking-wide">
                                        {{ $teacher->label }}
                                    </span>

                                    <p class="text-xl font-bold text-emerald-950 mt-4 leading-tight">{{ $teacher->name }}</p>

                                    @if($teacher->subjects->isNotEmpty())
                                        <div class="mt-4 space-y-1.5">
                                            @foreach($teacher->subjects as $subject)
                                                <p class="text-amber-700 font-semibold">{{ $subject->name }}</p>
                                            @endforeach
                                        </div>
                                    @elseif($teacher->position)
                                        <p class="text-amber-700 font-semibold mt-4">{{ $teacher->position }}</p>
                                    @else
                                        <p class="text-sm text-slate-400 mt-4">Mapel belum diatur</p>
                                    @endif

                                    @if($teacher->description)
                                        <p class="text-slate-600 text-sm mt-3 leading-relaxed">{{ $teacher->description }}</p>
                                    @elseif($primarySubject?->description)
                                        <p class="text-slate-600 text-sm mt-3 leading-relaxed">{{ $primarySubject->description }}</p>
                                    @endif
                                </div>

                                <div class="flex h-full items-end justify-center border-l border-yellow-100 bg-white/50">
                                    @if($teacher->photo_path)
                                        <img src="{{ asset('storage/' . $teacher->photo_path) }}"
                                             alt="{{ $teacher->name }}"
                                             class="max-h-[220px] md:max-h-[255px] w-auto object-contain object-bottom">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center">
                                            <span class="flex h-20 w-20 items-center justify-center rounded-full border border-amber-200 bg-emerald-100 text-xl font-bold text-emerald-800 shadow-sm">
                                                {{ $initials ?: 'G' }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if(filled($teacher->teacher_quote))
                                <div class="bg-[#00583f] px-7 py-5">
                                    <div class="flex gap-4">
                                        <span class="text-3xl text-amber-400 leading-none shrink-0 mt-1 select-none">&ldquo;</span>
                                        <p class="font-serif text-base md:text-lg italic leading-relaxed text-white">
                                            {{ $teacher->teacher_quote }}
                                        </p>
                                    </div>
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
