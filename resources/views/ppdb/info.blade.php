@extends('layouts.public')

@section('content')
@php
    $academicYear = $admissionYear?->academic_year ?? '2026/2027';
    $programQuota = 36;
    $registrationFee = 0;
    $programName = 'Program Gratis Angkatan Pertama';
    $whatsappUrl = $schoolSetting?->whatsappLink('Assalamu\'alaikum, saya ingin konsultasi tentang SPMB SMA Persis Serang.') ?? '#kontak';
@endphp

<div class="bg-white">
    <section class="relative isolate overflow-hidden bg-gradient-to-br from-[#052E1F] via-[#0A4F2B] to-[#0F6B3A]">
        <div class="absolute inset-0 opacity-[0.06]"
             style="background-image: linear-gradient(135deg, rgba(255,255,255,.5) 1px, transparent 1px); background-size: 42px 42px;"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/30 via-transparent to-emerald-950/20"></div>
        <div class="relative z-10 mx-auto max-w-7xl px-4 py-12 text-center sm:px-6 lg:py-20">
            <div class="inline-flex items-center gap-2 rounded-full border border-amber-300/70 bg-emerald-950/60 px-4 py-2 text-sm font-semibold text-amber-300 shadow-lg shadow-emerald-950/20 backdrop-blur-sm">
                <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                SPMB Tahun Ajaran {{ $academicYear }}
            </div>

            <h1 class="mx-auto mt-6 max-w-4xl text-4xl font-bold leading-tight text-white sm:text-5xl lg:text-6xl">
                Penerimaan Murid Baru SMA Persis Serang
            </h1>
            <div class="mx-auto mt-4 h-1.5 w-32 rounded-full bg-gradient-to-r from-amber-400 to-yellow-300"></div>
            <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-emerald-50 sm:text-xl">
                Islamic Boarding School berbasis Akhlak dan Teknologi
            </p>
            <p class="mx-auto mt-4 max-w-3xl rounded-2xl border border-amber-300/40 bg-white/10 px-5 py-3 text-sm font-semibold leading-relaxed text-amber-100 shadow-lg shadow-emerald-950/10 backdrop-blur sm:text-base">
                Gratis biaya sekolah dan asrama khusus angkatan pertama 36 murid
            </p>
            <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('spmb.create') }}"
                   class="inline-flex w-full items-center justify-center rounded-xl bg-amber-400 px-7 py-3 text-sm font-bold text-emerald-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-300 sm:w-auto">
                    Daftar SPMB
                </a>
                <a href="{{ $whatsappUrl }}"
                   target="_blank"
                   class="inline-flex w-full items-center justify-center rounded-xl border border-white/40 bg-white/10 px-7 py-3 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/15 sm:w-auto">
                    Konsultasi WhatsApp
                </a>
            </div>
        </div>
    </section>

    <main class="bg-[#FBF7EF]">
        <section class="py-12 lg:py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                @if($admissionYear && $admissionStats)
                    <div class="grid gap-5 md:grid-cols-3">
                        <div class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-white to-emerald-50 p-6 text-center shadow-sm shadow-emerald-950/5">
                            <p class="text-sm font-semibold text-emerald-800">Total Pendaftar</p>
                            <div class="mt-3 text-4xl font-bold text-[#0F6B3A]">{{ $admissionStats['totalApplicants'] }}</div>
                        </div>
                        <div class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-white to-emerald-50 p-6 text-center shadow-sm shadow-emerald-950/5">
                            <p class="text-sm font-semibold text-emerald-800">Diterima</p>
                            <div class="mt-3 text-4xl font-bold text-[#0F6B3A]">{{ $admissionStats['totalAccepted'] }}</div>
                        </div>
                        <div class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-white to-emerald-50 p-6 text-center shadow-sm shadow-emerald-950/5">
                            <p class="text-sm font-semibold text-emerald-800">Sisa Kuota</p>
                            <div class="mt-3 text-4xl font-bold text-[#0F6B3A]">{{ $admissionStats['remainingQuota'] }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <section class="pb-12 lg:pb-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="text-sm font-bold uppercase tracking-[0.24em] text-[#D4A017]">SPMB SMA Persis Serang</p>
                    <h2 class="mt-3 text-3xl font-bold text-gray-900 lg:text-4xl">Program Pendaftaran</h2>
                    <p class="mt-4 text-lg leading-relaxed text-gray-600">
                        Kesempatan bergabung di angkatan pertama dengan dukungan pendidikan berasrama yang terarah.
                    </p>
                </div>

                @if($programs->isNotEmpty())
                    <div class="mt-10 grid items-stretch gap-6 lg:grid-cols-2">
                        <article class="rounded-2xl border border-emerald-100 bg-white p-6 shadow-md shadow-emerald-950/5 lg:p-8">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-emerald-700">
                                        Program Khusus
                                    </span>
                                    <h3 class="mt-4 text-2xl font-bold text-gray-900">{{ $programName }}</h3>
                                    <p class="mt-3 text-sm leading-relaxed text-gray-600">
                                        Gratis pendidikan, asrama, dan makan untuk murid angkatan pertama yang mengikuti proses SPMB.
                                    </p>
                                </div>
                                <div class="w-full rounded-2xl bg-[#EAF6EE] p-4 text-center sm:w-32">
                                    <div class="text-3xl font-bold text-[#0F6B3A]">{{ $programQuota }}</div>
                                    <p class="text-xs font-semibold text-emerald-800">Kuota Murid</p>
                                </div>
                            </div>

                            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                                <div class="rounded-2xl border border-emerald-100 bg-emerald-50/70 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Biaya</p>
                                    <p class="mt-1 text-2xl font-bold text-gray-900">Rp{{ number_format($registrationFee, 0, ',', '.') }}</p>
                                </div>
                                <div class="rounded-2xl border border-emerald-100 bg-white p-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Tahun Ajaran</p>
                                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $academicYear }}</p>
                                </div>
                            </div>
                        </article>

                        <aside class="rounded-2xl border border-amber-100 bg-white p-6 shadow-md shadow-emerald-950/5 lg:p-8">
                            <h3 class="text-xl font-bold text-gray-900">Benefit Angkatan Pertama</h3>
                            <div class="mt-6 space-y-4">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-[#0F6B3A] text-white">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </span>
                                    <p class="text-sm leading-relaxed text-gray-600">Gratis biaya pendidikan selama program berjalan.</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-[#0F6B3A] text-white">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </span>
                                    <p class="text-sm leading-relaxed text-gray-600">Gratis fasilitas asrama dalam lingkungan Islamic Boarding School.</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-[#0F6B3A] text-white">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </span>
                                    <p class="text-sm leading-relaxed text-gray-600">Gratis makan untuk mendukung kegiatan belajar dan pembinaan harian.</p>
                                </div>
                            </div>
                        </aside>
                    </div>
                @else
                    <div class="mt-10 rounded-2xl border border-emerald-100 bg-white p-8 text-center shadow-sm">
                        <p class="font-semibold text-gray-700">Belum ada program SPMB yang tersedia.</p>
                        <p class="mt-2 text-sm text-gray-500">Silakan hubungi panitia SPMB untuk informasi lebih lanjut.</p>
                    </div>
                @endif
            </div>
        </section>

        <section class="bg-white py-12 lg:py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 via-white to-amber-50 p-6 text-center shadow-md shadow-emerald-950/5 sm:p-8 lg:p-12">
                    <p class="text-sm font-bold uppercase tracking-[0.2em] text-[#D4A017]">SPMB {{ $academicYear }}</p>
                    <h2 class="mt-3 text-3xl font-bold text-gray-900 lg:text-4xl">Siap Bergabung?</h2>
                    <p class="mx-auto mt-4 max-w-2xl text-lg leading-relaxed text-gray-600">
                        @if($admissionYear && in_array($admissionYear->status, ['open', 'almost_full']))
                            Daftarkan putra-putri Anda sekarang dan lengkapi data melalui form SPMB online.
                        @else
                            Hubungi tim SPMB kami untuk informasi jadwal, kuota, dan proses seleksi berikutnya.
                        @endif
                    </p>
                    <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                        @if(!$admissionYear || in_array($admissionYear->status, ['open', 'almost_full']))
                            <a href="{{ route('spmb.create') }}"
                               class="inline-flex w-full items-center justify-center rounded-xl bg-[#0F6B3A] px-8 py-4 text-sm font-bold text-white shadow-lg shadow-emerald-700/20 transition hover:bg-[#0A4F2B] sm:w-auto">
                                Daftar SPMB
                            </a>
                        @endif
                        <a href="{{ route('spmb.status.form') }}"
                           class="inline-flex w-full items-center justify-center rounded-xl border-2 border-[#0F6B3A] bg-white px-8 py-4 text-sm font-semibold text-[#0F6B3A] transition hover:bg-[#EAF6EE] sm:w-auto">
                            Cek Status
                        </a>
                        <a href="{{ $whatsappUrl }}"
                           target="_blank"
                           class="inline-flex w-full items-center justify-center rounded-xl border-2 border-[#D4A017] bg-white px-8 py-4 text-sm font-semibold text-[#D4A017] transition hover:bg-[#D4A017]/5 sm:w-auto">
                            Konsultasi WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>
@endsection
