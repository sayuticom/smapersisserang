@extends('layouts.public')

@section('content')
<div class="bg-[#FBF7EF]">
    <div class="border-t border-amber-100">
        <div class="max-w-7xl mx-auto px-4 pt-14 pb-20 sm:pt-16">
            <div class="flex flex-col md:flex-row md:items-start gap-6 mb-10">
                <div class="max-w-3xl flex-1">
                    <p class="text-sm font-bold tracking-[0.18em] uppercase text-[#D4A017]">STRUKTUR PENGAJAR</p>
                    <h2 class="text-3xl font-bold text-gray-900 mt-3">Guru Berdasarkan Mata Pelajaran</h2>
                    <p class="text-gray-600 mt-4 leading-relaxed">
                        Daftar mata pelajaran beserta guru pengampu di SMA Persis Serang.
                    </p>
                </div>
                @if($headmaster)
                    <div class="bg-[#FFFBF2] rounded-xl border border-amber-200/70 shadow-sm overflow-hidden shrink-0 md:w-[360px] w-full">
                        <div class="p-5 md:p-6">
                            <span class="inline-block bg-gradient-to-r from-amber-100 to-amber-300 text-emerald-950 rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide">Kepala Sekolah</span>
                            <div class="flex flex-col sm:flex-row md:flex-col items-start gap-4 mt-3">
                                @if($headmaster->photo_path)
                                    <img src="{{ asset('storage/' . $headmaster->photo_path) }}"
                                         alt="{{ $headmaster->name }}"
                                         class="w-28 h-28 md:w-36 md:h-36 rounded-xl object-cover border border-amber-200/50 shrink-0">
                                @endif
                                <div class="min-w-0">
                                    <p class="text-base md:text-lg font-bold text-emerald-950 leading-tight">{{ $headmaster->name }}</p>
                                    <p class="text-sm text-amber-700 mt-1.5 font-medium">Kepala Sekolah</p>
                                </div>
                            </div>
                        </div>
                        @if(filled($headmaster->teacher_quote))
                            <div class="bg-gradient-to-br from-emerald-950 to-emerald-800 px-5 md:px-6 py-4">
                                <div class="flex gap-3">
                                    <span class="text-2xl text-amber-400 leading-none shrink-0 mt-0.5 select-none">&ldquo;</span>
                                    <p class="font-serif text-sm md:text-base italic leading-relaxed text-white/90">
                                        {{ $headmaster->teacher_quote }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            @if($subjectsByCategory->flatten(1)->isEmpty())
                <div class="bg-white rounded-2xl border border-amber-100 p-8 text-center shadow-sm">
                    <p class="text-gray-500 font-medium">Data mata pelajaran belum tersedia.</p>
                </div>
            @else
                <div class="space-y-12">
                    @foreach($subjectCategories as $categoryKey => $categoryLabel)
                        @php
                            $subjects = $subjectsByCategory->get($categoryKey, collect());
                        @endphp
                        @continue($subjects->isEmpty())

                        <section>
                            <div class="mb-6">
                                <h3 class="text-2xl font-bold text-gray-900">{{ $categoryLabel }}</h3>
                                <div class="mt-2 h-1 w-16 rounded-full bg-[#D4A017]"></div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-6">
                                @foreach($subjects as $subject)
                                    @php
                                        $mainTeacher = $subject->teachers->first();
                                        $otherTeachers = $subject->teachers->slice(1);
                                        $hasPhoto = $mainTeacher?->photo_path;
                                    @endphp

                                    <div class="bg-[#FFFBF2] rounded-2xl border border-amber-200/70 shadow-sm overflow-hidden">
                                        <div class="{{ $hasPhoto ? 'grid grid-cols-1 md:grid-cols-[1fr_auto]' : '' }}">
                                            <div class="p-6">
                                                <span class="inline-block bg-gradient-to-r from-amber-100 to-amber-300 text-emerald-950 rounded-lg px-3 py-1 text-xs font-bold uppercase tracking-wide">Guru Pengampu</span>

                                                @if($mainTeacher)
                                                    <p class="text-xl font-bold text-emerald-950 mt-4 leading-tight">{{ $mainTeacher->name }}</p>
                                                @endif

                                                <p class="text-amber-700 font-semibold mt-4">{{ $subject->name }}</p>

                                                @if($subject->description)
                                                    <p class="text-slate-600 text-sm mt-3 leading-relaxed">{{ $subject->description }}</p>
                                                @endif

                                                @if($otherTeachers->isNotEmpty())
                                                    <div class="mt-4 border-t border-amber-200/60 pt-3">
                                                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-600/70">Guru Lainnya</p>
                                                        <div class="mt-2 space-y-1">
                                                            @foreach($otherTeachers as $t)
                                                                <p class="text-sm text-slate-600">{{ $t->name }}</p>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                @if($subject->teachers->isEmpty())
                                                    <p class="text-sm text-slate-400 mt-3">Guru pengampu belum diatur.</p>
                                                @endif
                                            </div>

                                            @if($hasPhoto)
                                                <div class="flex items-end justify-end">
                                                    <img src="{{ asset('storage/' . $mainTeacher->photo_path) }}"
                                                         alt="{{ $mainTeacher->name }}"
                                                         class="h-64 w-auto object-contain">
                                                </div>
                                            @endif
                                        </div>

                                        @php
                                            $quoteTeacher = $subject->teachers->first(fn($t) => filled($t->teacher_quote));
                                        @endphp
                                        @if($quoteTeacher)
                                            <div class="bg-gradient-to-br from-emerald-950 to-emerald-800 px-7 py-5">
                                                <div class="flex gap-4">
                                                    <span class="text-3xl text-amber-400 leading-none shrink-0 mt-1 select-none">&ldquo;</span>
                                                    <p class="font-serif text-base md:text-lg italic leading-relaxed text-white/90">
                                                        {{ $quoteTeacher->teacher_quote }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
