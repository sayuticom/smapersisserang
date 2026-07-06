@extends('layouts.public')

@php
    $schoolName = $schoolSetting->school_name ?? 'SMA Persis Serang';
    $displayName = $schoolSetting->school_name ?? 'SMA Persis Serang';
    $heroBg = $setting?->hero_image ? asset('storage/' . $setting->hero_image) : null;
@endphp

@section('title', 'Sebarkan Informasi - ' . $schoolName)

@section('content')

<section class="relative isolate min-h-[170px] overflow-hidden bg-gradient-to-br from-[#052E1F] via-[#0A4F2B] to-[#0F6B3A] sm:min-h-[300px] lg:min-h-[400px]">
    <div class="absolute inset-0 opacity-[0.06]"
         style="background-image: linear-gradient(135deg, rgba(255,255,255,.5) 1px, transparent 1px); background-size: 42px 42px;"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/30 via-transparent to-emerald-950/20"></div>
    @if($heroBg)
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $heroBg }}')"></div>
    @endif
    <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/80 via-emerald-950/60 to-emerald-900/40"></div>
    <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-emerald-950/80 via-emerald-950/35 to-transparent"></div>

    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-[170px] flex-col justify-center pb-7 pt-20 sm:min-h-[300px] sm:pb-12 sm:pt-28 lg:min-h-[400px] lg:pb-16 lg:pt-32">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-1.5 rounded-full border border-amber-300/70 bg-emerald-950/60 px-3 py-1.5 text-[11px] font-semibold text-amber-300 shadow-lg shadow-emerald-950/20 backdrop-blur sm:gap-2 sm:px-4 sm:py-2 sm:text-sm">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-300 sm:h-2 sm:w-2"></span>
                    SEBARKAN INFORMASI
                </div>
                <h1 class="mt-4 text-2xl font-extrabold leading-tight text-white sm:mt-6 sm:text-4xl lg:text-5xl">
                    Sebarkan Kebaikan <br class="hidden sm:inline">ini ke Sesama
                </h1>
                <p class="mt-2 max-w-2xl text-[13px] leading-relaxed text-emerald-100/80 sm:mt-4 sm:text-base lg:text-lg">
                    Bagikan informasi donasi pendidikan ini kepada keluarga, tetangga, atau rekan
                    dengan pesan WhatsApp yang personal.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="bg-gradient-to-b from-emerald-50/50 to-white py-10 sm:py-16 lg:py-20">
    <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-2xl border border-emerald-200/80 bg-white p-6 shadow-lg shadow-emerald-900/5 sm:p-8 lg:p-10">
            <h2 class="text-lg font-bold text-gray-900 sm:text-xl">Buat Pesan WhatsApp</h2>
            <p class="mt-1 text-sm text-gray-500">Pesan akan otomatis menyebut nama calon donatur.</p>

            @if(session('error'))
                <div class="mt-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('donasi-pendidikan.sebarkan.submit') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <label for="sapaan" class="block text-sm font-semibold text-gray-700">Sapaan <span class="text-red-500">*</span></label>
                    <select name="sapaan" id="sapaan" required
                            class="mt-1.5 block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                        <option value="">Pilih sapaan</option>
                        @foreach($sapaanOptions as $opt)
                            <option value="{{ $opt }}" {{ old('sapaan') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="nama_tujuan" class="block text-sm font-semibold text-gray-700">Nama Tujuan <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_tujuan" id="nama_tujuan" required
                           value="{{ old('nama_tujuan') }}"
                           placeholder="Haji Ajiz"
                           class="mt-1.5 block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                </div>

                <div>
                    <label for="nomor_whatsapp" class="block text-sm font-semibold text-gray-700">Nomor WhatsApp Tujuan <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <input type="text" name="nomor_whatsapp" id="nomor_whatsapp"
                           value="{{ old('nomor_whatsapp') }}"
                           placeholder="08xxxx atau 62xxxx"
                           class="mt-1.5 block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    <p class="mt-1 text-xs text-gray-400">Diisi jika ingin langsung dikirim ke nomor tujuan. Kosongi jika ingin mendapat link untuk disalin.</p>
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-emerald-900/20 transition hover:from-emerald-500 hover:to-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Buat Pesan WhatsApp
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('donasi-pendidikan') }}"
               class="inline-flex items-center gap-1.5 text-sm font-medium text-emerald-700 hover:text-emerald-800 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke halaman donasi
            </a>
        </div>
    </div>
</section>

@endsection
