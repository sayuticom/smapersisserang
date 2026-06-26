@extends('layouts.public')

@section('content')

<section class="bg-gradient-to-br from-[#0F6B3A] to-[#0A4F2B]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 text-center">
        <h1 class="text-4xl lg:text-5xl font-bold text-white">{{ $websitePage?->title ?? 'Islamic Boarding School' }}</h1>
        <p class="text-emerald-100/80 text-lg lg:text-xl mt-4 max-w-2xl mx-auto leading-relaxed">
            {{ $websitePage?->subtitle ?? 'Lingkungan pendidikan berasrama untuk membentuk akhlak, kemandirian, ibadah, dan kedisiplinan siswa.' }}
        </p>
    </div>
</section>

<section class="py-16 lg:py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">Mengapa Boarding School?</h2>
            <p class="text-gray-500 mt-4 text-lg">Keunggulan sistem pendidikan berasrama di {{ $schoolSetting->school_name ?? 'SMA Persis Serang' }}</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 hover:shadow-md hover:border-[#0F6B3A]/20 transition-all">
                <div class="w-14 h-14 bg-[#EAF6EE] rounded-2xl flex items-center justify-center mb-5">
                    <svg class="w-7 h-7 text-[#0F6B3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Pembinaan Akhlak Harian</h3>
                <p class="text-gray-500 text-sm mt-3 leading-relaxed">Adab dan akhlak Islami dibiasakan dalam setiap aktivitas sehari-hari siswa.</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 hover:shadow-md hover:border-[#0F6B3A]/20 transition-all">
                <div class="w-14 h-14 bg-[#D4A017]/10 rounded-2xl flex items-center justify-center mb-5">
                    <svg class="w-7 h-7 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Kemandirian dan Disiplin</h3>
                <p class="text-gray-500 text-sm mt-3 leading-relaxed">Siswa dilatih mandiri mengatur waktu, tanggung jawab, dan kedisiplinan pribadi.</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 hover:shadow-md hover:border-[#0F6B3A]/20 transition-all">
                <div class="w-14 h-14 bg-[#EAF6EE] rounded-2xl flex items-center justify-center mb-5">
                    <svg class="w-7 h-7 text-[#0F6B3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Lingkungan Belajar Terarah</h3>
                <p class="text-gray-500 text-sm mt-3 leading-relaxed">Suasana kondusif dengan bimbingan intensif untuk hasil belajar yang optimal.</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 hover:shadow-md hover:border-[#0F6B3A]/20 transition-all">
                <div class="w-14 h-14 bg-[#D4A017]/10 rounded-2xl flex items-center justify-center mb-5">
                    <svg class="w-7 h-7 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Pengawasan &amp; Pendampingan</h3>
                <p class="text-gray-500 text-sm mt-3 leading-relaxed">Didampingi pembina asrama 24 jam untuk memantau perkembangan dan kebutuhan siswa.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-16 lg:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">Kegiatan Harian Siswa</h2>
            <p class="text-gray-500 mt-4 text-lg">Rutinitas harian yang terstruktur untuk membentuk kebiasaan positif</p>
        </div>
        <div class="max-w-3xl mx-auto">
            <div class="space-y-0">
                <div class="flex items-start gap-5 group">
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-2xl bg-[#EAF6EE] flex items-center justify-center text-[#0F6B3A] font-bold text-sm shadow-sm group-hover:shadow-md transition-shadow flex-shrink-0">03.00</div>
                        <div class="w-0.5 h-16 bg-emerald-100 group-last:hidden"></div>
                    </div>
                    <div class="pb-8 pt-1.5">
                        <h3 class="font-semibold text-gray-900">Subuh &amp; Pembinaan Ibadah</h3>
                        <p class="text-gray-500 text-sm mt-1 leading-relaxed">Shalat Subuh berjamaah, dzikir pagi, dan pembinaan ibadah harian.</p>
                    </div>
                </div>
                <div class="flex items-start gap-5 group">
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-2xl bg-[#D4A017]/10 flex items-center justify-center text-[#D4A017] font-bold text-sm shadow-sm group-hover:shadow-md transition-shadow flex-shrink-0">07.00</div>
                        <div class="w-0.5 h-16 bg-emerald-100 group-last:hidden"></div>
                    </div>
                    <div class="pb-8 pt-1.5">
                        <h3 class="font-semibold text-gray-900">Pembelajaran Sekolah</h3>
                        <p class="text-gray-500 text-sm mt-1 leading-relaxed">Belajar di kelas sesuai kurikulum nasional dengan pendekatan integratif.</p>
                    </div>
                </div>
                <div class="flex items-start gap-5 group">
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-2xl bg-[#EAF6EE] flex items-center justify-center text-[#0F6B3A] font-bold text-sm shadow-sm group-hover:shadow-md transition-shadow flex-shrink-0">12.00</div>
                        <div class="w-0.5 h-16 bg-emerald-100 group-last:hidden"></div>
                    </div>
                    <div class="pb-8 pt-1.5">
                        <h3 class="font-semibold text-gray-900">Istirahat &amp; Kegiatan Mandiri</h3>
                        <p class="text-gray-500 text-sm mt-1 leading-relaxed">Shalat Dzuhur, makan siang, istirahat, dan kegiatan mandiri siswa.</p>
                    </div>
                </div>
                <div class="flex items-start gap-5 group">
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-2xl bg-[#D4A017]/10 flex items-center justify-center text-[#D4A017] font-bold text-sm shadow-sm group-hover:shadow-md transition-shadow flex-shrink-0">15.30</div>
                        <div class="w-0.5 h-16 bg-emerald-100 group-last:hidden"></div>
                    </div>
                    <div class="pb-8 pt-1.5">
                        <h3 class="font-semibold text-gray-900">Kajian / Tahsin / Pembinaan</h3>
                        <p class="text-gray-500 text-sm mt-1 leading-relaxed">Kajian Islam, tahsin Al-Qur&rsquo;an, dan pembinaan karakter setelah Ashar.</p>
                    </div>
                </div>
                <div class="flex items-start gap-5 group">
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-2xl bg-[#EAF6EE] flex items-center justify-center text-[#0F6B3A] font-bold text-sm shadow-sm group-hover:shadow-md transition-shadow flex-shrink-0">19.00</div>
                        <div class="w-0.5 h-16 bg-emerald-100"></div>
                    </div>
                    <div class="pb-8 pt-1.5">
                        <h3 class="font-semibold text-gray-900">Belajar Malam</h3>
                        <p class="text-gray-500 text-sm mt-1 leading-relaxed">Belajar mandiri dan bimbingan belajar malam setelah Isya.</p>
                    </div>
                </div>
                <div class="flex items-start gap-5 group">
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-sm shadow-sm flex-shrink-0">21.00</div>
                    </div>
                    <div class="pt-1.5">
                        <h3 class="font-semibold text-gray-900">Istirahat</h3>
                        <p class="text-gray-500 text-sm mt-1 leading-relaxed">Persiapan tidur dan istirahat malam untuk memulihkan energi.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-16 lg:py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">Fokus Pembinaan</h2>
            <p class="text-gray-500 mt-4 text-lg">Enam aspek utama yang menjadi perhatian dalam pembinaan siswa</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 hover:shadow-md hover:border-[#0F6B3A]/20 transition-all text-center">
                <div class="w-14 h-14 mx-auto bg-[#EAF6EE] rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-[#0F6B3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Ibadah</h3>
                <p class="text-gray-500 text-sm mt-2 leading-relaxed">Pembiasan shalat berjamaah, puasa sunnah, dan amalan ibadah harian.</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 hover:shadow-md hover:border-[#D4A017]/20 transition-all text-center">
                <div class="w-14 h-14 mx-auto bg-[#D4A017]/10 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Adab</h3>
                <p class="text-gray-500 text-sm mt-2 leading-relaxed">Pembentukan adab Islami terhadap Allah, sesama, dan lingkungan.</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 hover:shadow-md hover:border-[#0F6B3A]/20 transition-all text-center">
                <div class="w-14 h-14 mx-auto bg-[#EAF6EE] rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-[#0F6B3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Al-Qur&rsquo;an</h3>
                <p class="text-gray-500 text-sm mt-2 leading-relaxed">Tahsin dan tahfidz Al-Qur&rsquo;an dengan target sesuai kemampuan siswa.</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 hover:shadow-md hover:border-[#D4A017]/20 transition-all text-center">
                <div class="w-14 h-14 mx-auto bg-[#D4A017]/10 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Kemandirian</h3>
                <p class="text-gray-500 text-sm mt-2 leading-relaxed">Latihan mengurus diri sendiri, mengatur waktu, dan bertanggung jawab.</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 hover:shadow-md hover:border-[#0F6B3A]/20 transition-all text-center">
                <div class="w-14 h-14 mx-auto bg-[#EAF6EE] rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-[#0F6B3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Teknologi</h3>
                <p class="text-gray-500 text-sm mt-2 leading-relaxed">Literasi digital dan keterampilan teknologi untuk bekal masa depan.</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 hover:shadow-md hover:border-[#D4A017]/20 transition-all text-center">
                <div class="w-14 h-14 mx-auto bg-[#D4A017]/10 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-[#D4A017]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Kepemimpinan</h3>
                <p class="text-gray-500 text-sm mt-2 leading-relaxed">Pengembangan jiwa kepemimpinan melalui organisasi dan kegiatan sosial.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-16 lg:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8 lg:p-12">
            <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center">
                <div>
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">Lingkungan Asrama</h2>
                    <p class="text-[#D4A017] font-semibold text-lg mt-2">Tempat Tinggal yang Nyaman dan Kondusif</p>
                    <p class="text-gray-600 mt-6 leading-relaxed text-lg">
                        @if($schoolSetting?->about_boarding)
                            {{ $schoolSetting->about_boarding }}
                        @else
                            {{ $schoolSetting->school_name ?? 'SMA Persis Serang' }} menyediakan fasilitas asrama yang nyaman,
                            aman, dan kondusif untuk mendukung proses belajar dan pembinaan karakter siswa
                            selama 24 jam dalam lingkungan Islami.
                        @endif
                    </p>
                </div>
                <div>
                    @if($schoolSetting?->building_image_path)
                        <div class="aspect-[4/3] rounded-3xl overflow-hidden shadow-lg border border-emerald-100">
                            <img src="{{ asset('storage/' . $schoolSetting->building_image_path) }}"
                                 alt="Foto {{ $schoolSetting->school_name }}"
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

<section class="py-16 lg:py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">Ingin tahu lebih lanjut tentang boarding school?</h2>
        <p class="text-gray-500 mt-4 text-lg max-w-xl mx-auto">Daftarkan putra-putri Anda dan jadilah bagian dari keluarga besar {{ $schoolSetting->school_name ?? 'SMA Persis Serang' }}.</p>
        <div class="flex flex-wrap justify-center gap-4 mt-8">
            <a href="{{ route('spmb.create') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                Daftar SPMB
            </a>
            <a href="{{ route('spmb.status.form') }}"
               class="px-8 py-4 border-2 border-[#0F6B3A] text-[#0F6B3A] font-semibold rounded-xl hover:bg-[#EAF6EE] transition-colors">
                Cek Status
            </a>
            @if($schoolSetting?->whatsappLink('Assalamu\'alaikum, saya ingin bertanya tentang boarding school.'))
                <a href="{{ $schoolSetting->whatsappLink('Assalamu\'alaikum, saya ingin bertanya tentang boarding school.') }}"
                   class="px-8 py-4 border-2 border-[#D4A017] text-[#D4A017] font-semibold rounded-xl hover:bg-[#D4A017]/5 transition-colors">
                    Konsultasi WhatsApp
                </a>
            @endif
        </div>
    </div>
</section>

@endsection