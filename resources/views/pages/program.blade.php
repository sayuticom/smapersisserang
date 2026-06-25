@extends('layouts.public')

@section('content')

<section class="bg-gradient-to-br from-[#0F6B3A] to-[#0A4F2B]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 text-center">
        <h1 class="text-4xl lg:text-5xl font-bold text-white">{{ $websitePage?->title ?? 'Program Pendidikan' }}</h1>
        <p class="text-emerald-100/80 text-lg lg:text-xl mt-4 max-w-2xl mx-auto leading-relaxed">
            {{ $websitePage?->subtitle ?? 'Kurikulum dan pembinaan yang memadukan akhlak, keislaman, kemandirian, dan teknologi.' }}
        </p>
    </div>
</section>

<section class="py-16 lg:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">Program Unggulan</h2>
            <p class="text-gray-500 mt-4 text-lg">Enam pilar utama yang membentuk karakter dan kompetensi siswa</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 hover:shadow-md hover:border-[#0F6B3A]/20 transition-all">
                <div class="w-14 h-14 bg-[#EAF6EE] rounded-2xl flex items-center justify-center text-2xl mb-5">
                    <svg class="w-7 h-7 text-[#0F6B3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Akhlak &amp; Adab</h3>
                <p class="text-gray-500 text-sm mt-3 leading-relaxed">Membiasakan adab Islami dalam kehidupan sehari-hari di lingkungan sekolah dan asrama.</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 hover:shadow-md hover:border-[#0F6B3A]/20 transition-all">
                <div class="w-14 h-14 bg-[#D4A017]/10 rounded-2xl flex items-center justify-center text-2xl mb-5">
                    <svg class="w-7 h-7 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Keislaman</h3>
                <p class="text-gray-500 text-sm mt-3 leading-relaxed">Penguatan ibadah, aqidah, akhlak, dan pemahaman agama yang istiqomah.</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 hover:shadow-md hover:border-[#0F6B3A]/20 transition-all">
                <div class="w-14 h-14 bg-[#EAF6EE] rounded-2xl flex items-center justify-center text-2xl mb-5">
                    <svg class="w-7 h-7 text-[#0F6B3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Al-Qur&rsquo;an</h3>
                <p class="text-gray-500 text-sm mt-3 leading-relaxed">Pembinaan baca Al-Qur&rsquo;an dan tahsin sesuai kemampuan siswa secara berkelanjutan.</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 hover:shadow-md hover:border-[#0F6B3A]/20 transition-all">
                <div class="w-14 h-14 bg-[#D4A017]/10 rounded-2xl flex items-center justify-center text-2xl mb-5">
                    <svg class="w-7 h-7 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Informatika &amp; Teknologi</h3>
                <p class="text-gray-500 text-sm mt-3 leading-relaxed">Pengenalan teknologi, literasi digital, dan keterampilan informatika modern.</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 hover:shadow-md hover:border-[#0F6B3A]/20 transition-all">
                <div class="w-14 h-14 bg-[#EAF6EE] rounded-2xl flex items-center justify-center text-2xl mb-5">
                    <svg class="w-7 h-7 text-[#0F6B3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Kemandirian</h3>
                <p class="text-gray-500 text-sm mt-3 leading-relaxed">Pembinaan kedisiplinan dan tanggung jawab melalui lingkungan berasrama.</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 hover:shadow-md hover:border-[#0F6B3A]/20 transition-all">
                <div class="w-14 h-14 bg-[#D4A017]/10 rounded-2xl flex items-center justify-center text-2xl mb-5">
                    <svg class="w-7 h-7 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Kepemimpinan</h3>
                <p class="text-gray-500 text-sm mt-3 leading-relaxed">Melatih keberanian, komunikasi, dan tanggung jawab sosial melalui organisasi dan proyek.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-16 lg:py-20 bg-[#FBF7EF]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <p class="text-sm font-bold tracking-[0.18em] uppercase text-[#D4A017]">Kurikulum &amp; Mata Pelajaran</p>
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mt-3">Kurikulum &amp; Mata Pelajaran</h2>
            <p class="text-gray-600 mt-5 leading-relaxed text-lg">
                Perpaduan pelajaran nasional, keislaman, teknologi, dan pembinaan karakter boarding school.
            </p>
            <p class="mt-4 rounded-2xl border border-amber-200 bg-white/80 px-5 py-4 text-sm font-semibold text-emerald-950 shadow-sm">
                Yang membedakan kami: siswa tidak hanya belajar akademik, tetapi juga dibina dalam akhlak, keislaman, kemandirian, dan teknologi.
            </p>
        </div>

        <div class="mt-10 space-y-8">
            @if($subjectsByCategory->flatten(1)->isEmpty())
                <div class="rounded-2xl border border-amber-100 bg-white p-8 text-center shadow-sm">
                    <p class="text-gray-500 font-medium">Data mata pelajaran belum tersedia.</p>
                </div>
            @else
                @foreach($subjectCategories as $categoryKey => $categoryLabel)
                    @php($categorySubjects = $subjectsByCategory->get($categoryKey, collect()))
                    @continue($categorySubjects->isEmpty())

                    <div class="rounded-[1.5rem] bg-white border border-amber-100 shadow-sm overflow-hidden" data-category="{{ $categoryKey }}">
                    <div class="bg-emerald-950 px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <p class="text-sm font-semibold text-[#D4A017]">{{ $categoryLabel }}</p>
                            <h3 class="text-xl font-bold text-white mt-1">{{ $categoryLabel }}</h3>
                        </div>
                        <span class="w-fit rounded-full border border-amber-300/40 px-3 py-1 text-xs font-semibold text-amber-200">
                            {{ $categorySubjects->count() }} Mapel
                        </span>
                    </div>
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-px bg-amber-100">
                        @foreach($categorySubjects as $subject)
                            <article class="bg-white p-6 hover:bg-amber-50/50 transition-colors">
                                <div class="flex items-start justify-between gap-3">
                                    <h4 class="text-lg font-bold text-gray-900">{{ $subject->name }}</h4>
                                    <span class="inline-flex w-fit shrink-0 rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                        {{ $subject->category_label }}
                                    </span>
                                </div>
                                @if($subject->description)
                                    <p class="text-gray-600 text-sm mt-3 leading-relaxed">{{ $subject->description }}</p>
                                @endif
                                @if($subject->teachers->isNotEmpty())
                                    <div class="mt-5">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Guru Pengampu</p>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            @foreach($subject->teachers as $teacher)
                                                <span class="rounded-full bg-emerald-950 px-3 py-1 text-xs font-semibold text-white">{{ $teacher->name }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</section>

<section class="py-16 lg:py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8 lg:p-12">
            <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center">
                <div>
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">Boarding School</h2>
                    <p class="text-[#D4A017] font-semibold text-lg mt-2">Lingkungan Pendidikan Berasrama</p>
                    <p class="text-gray-600 mt-6 leading-relaxed text-lg">
                        {{ $schoolSetting->school_name ?? 'SMA Persis Serang' }} menerapkan sistem boarding school
                        yang mengintegrasikan pendidikan formal, pembinaan karakter, dan pengembangan diri
                        dalam lingkungan asrama yang Islami.
                    </p>
                    <div class="mt-3 space-y-2 text-gray-600">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-[#0F6B3A] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Bimbingan intensif 24 jam oleh pembina asrama</span>
                        </div>
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-[#0F6B3A] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Program tahsin dan tahfidz Al-Qur&rsquo;an</span>
                        </div>
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-[#0F6B3A] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Pembiasaan ibadah dan adab Islami sehari-hari</span>
                        </div>
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-[#0F6B3A] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Fasilitas asrama yang nyaman dan kondusif</span>
                        </div>
                    </div>
                    <div class="mt-8">
                        <a href="{{ route('public.profile') }}"
                           class="inline-flex items-center px-6 py-3 bg-[#0F6B3A] text-white font-semibold rounded-xl hover:bg-[#0A4F2B] transition-colors shadow-lg shadow-[#0F6B3A]/20">
                            Lihat Konsep Boarding School
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="aspect-[4/3] rounded-3xl overflow-hidden bg-gradient-to-br from-[#EAF6EE] via-white to-white flex items-center justify-center border border-emerald-100">
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
            </div>
        </div>
    </div>
</section>

<section class="py-16 lg:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">Siap bergabung bersama {{ $schoolSetting->school_name ?? 'SMA Persis Serang' }}?</h2>
        <p class="text-gray-500 mt-4 text-lg max-w-xl mx-auto">Daftarkan putra-putri Anda dan jadilah bagian dari generasi berilmu dan beradab.</p>
        <div class="flex flex-wrap justify-center gap-4 mt-8">
            <a href="{{ route('ppdb.create') }}"
               class="px-8 py-4 bg-[#0F6B3A] text-white font-semibold rounded-xl hover:bg-[#0A4F2B] transition-colors shadow-lg shadow-[#0F6B3A]/20">
                Daftar SPMB
            </a>
            <a href="{{ route('ppdb.status.form') }}"
               class="px-8 py-4 border-2 border-[#0F6B3A] text-[#0F6B3A] font-semibold rounded-xl hover:bg-[#EAF6EE] transition-colors">
                Cek Status Pendaftaran
            </a>
            @if($schoolSetting?->whatsappLink('Assalamu\'alaikum, saya ingin bertanya tentang program pendidikan.'))
                <a href="{{ $schoolSetting->whatsappLink('Assalamu\'alaikum, saya ingin bertanya tentang program pendidikan.') }}"
                   class="px-8 py-4 border-2 border-[#D4A017] text-[#D4A017] font-semibold rounded-xl hover:bg-[#D4A017]/5 transition-colors">
                    Konsultasi WhatsApp
                </a>
            @endif
        </div>
    </div>
</section>

@endsection
