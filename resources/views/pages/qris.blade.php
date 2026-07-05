@extends('layouts.public')

@php
    $schoolName = $schoolSetting->school_name ?? 'SMA Persis Serang';
    $merchantName = $schoolSetting->school_name ? strtoupper($schoolSetting->school_name) . ', CURUG' : 'SMA PERSIS SERANG, CURUG';
    $merchantCity = 'SERANG';
    $qrisImageUrl = $setting?->donation_qris_image
        ? \Illuminate\Support\Facades\Storage::url($setting->donation_qris_image)
        : null;
    $nominalFormatted = 'Rp' . number_format($donationData['amount'], 0, ',', '.');
@endphp

@section('title', 'Pembayaran via QRIS - ' . $schoolName)

@section('content')

<section class="bg-[#FBF7EF] py-16 lg:py-20">
    <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">

        <div class="rounded-2xl border border-amber-200/60 bg-white p-6 shadow-lg shadow-emerald-950/5 lg:p-10">
            <div class="text-center">
                <p class="text-sm font-bold uppercase tracking-[0.24em] text-[#D4A017]">PEMBAYARAN</p>
                <h1 class="mt-3 font-serif text-2xl font-bold text-[#052E1F] lg:text-3xl">Pembayaran Donasi via QRIS</h1>
            </div>

            <div class="mt-6 rounded-xl border border-emerald-100 bg-emerald-50/50 p-5">
                <table class="w-full text-sm">
                    <tr>
                        <td class="py-1.5 pr-4 font-medium text-gray-500">Nama</td>
                        <td class="py-1.5 font-semibold text-[#052E1F]">{{ $donationData['donor_name'] ?: 'Hamba Allah' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 pr-4 font-medium text-gray-500">Nomor WhatsApp</td>
                        <td class="py-1.5 font-semibold text-[#052E1F]">{{ $donationData['donor_whatsapp'] }}</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 pr-4 font-medium text-gray-500">Jenis Dukungan</td>
                        <td class="py-1.5 font-semibold text-[#052E1F]">{{ $donationData['support_type'] }}</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 pr-4 font-medium text-gray-500">Nominal</td>
                        <td class="py-1.5 font-semibold text-[#0F6B3A]">{{ $nominalFormatted }}</td>
                    </tr>
                    @if($donationData['note'] ?? null)
                    <tr>
                        <td class="py-1.5 pr-4 font-medium text-gray-500">Catatan</td>
                        <td class="py-1.5 text-gray-600">{{ $donationData['note'] }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            <div class="mt-8 text-center">
                @if($hasQrisPayload && $dynamicQrisBase64)
                    <div class="mx-auto max-w-xs">
                        <div class="mb-3 text-center leading-snug">
                            <div class="text-sm font-bold text-gray-900">{{ $merchantName }}</div>
                            <div class="text-xs text-gray-400">{{ $merchantCity }}</div>
                        </div>
                        <img src="{{ $dynamicQrisBase64 }}"
                             alt="QRIS Donasi {{ $schoolName }} - {{ $nominalFormatted }}"
                             class="w-full rounded-2xl border bg-white p-3 shadow-lg">
                    </div>

                    <div class="mt-4 flex flex-wrap justify-center gap-3">
                        <a href="{{ route('donasi-pendidikan.qris.download') }}"
                           class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-yellow-400 to-amber-500 px-6 py-3 text-sm font-bold text-emerald-950 shadow-lg shadow-amber-900/20 transition hover:from-yellow-300 hover:to-amber-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download QRIS
                        </a>
                    </div>
                @elseif($qrisImageUrl)
                    <div class="mx-auto max-w-xs">
                        <div class="mb-3 text-center leading-snug">
                            <div class="text-sm font-bold text-gray-900">{{ $merchantName }}</div>
                            <div class="text-xs text-gray-400">{{ $merchantCity }}</div>
                        </div>
                        <img src="{{ $qrisImageUrl }}" alt="QRIS Donasi {{ $schoolName }}"
                             class="w-full rounded-2xl border bg-white p-3 shadow-lg">
                    </div>

                    <div class="mt-4 flex flex-wrap justify-center gap-3">
                        <a href="{{ $qrisImageUrl }}" download
                           class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-yellow-400 to-amber-500 px-6 py-3 text-sm font-bold text-emerald-950 shadow-lg shadow-amber-900/20 transition hover:from-yellow-300 hover:to-amber-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download QRIS
                        </a>
                    </div>

                    <div class="mt-4 rounded-xl border border-amber-100 bg-amber-50/50 p-4 text-sm text-gray-600">
                        Nominal belum otomatis. Silakan isi nominal secara manual pada aplikasi pembayaran.
                    </div>
                @else
                    <div class="mx-auto max-w-sm rounded-2xl border border-dashed border-amber-300 bg-amber-50 p-8 text-center">
                        <p class="text-sm font-medium text-amber-700">
                            QRIS belum tersedia. Silakan hubungi admin melalui WhatsApp.
                        </p>
                    </div>
                @endif
            </div>

            @if($hasQrisPayload)
            <div class="mt-6 rounded-xl border border-amber-100 bg-amber-50/50 p-5 text-sm leading-6 text-gray-700">
                <p>
                    Jika Bapak/Ibu membuka halaman ini melalui HP yang sama dengan aplikasi pembayaran, silakan tekan <strong>Download QRIS</strong> terlebih dahulu, lalu gunakan fitur upload QRIS di mobile banking atau e-wallet. Atau tekan lama gambar QRIS, lalu pilih simpan.
                </p>
            </div>
            @endif

            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                <a href="{{ $confirmWaUrl }}" target="_blank" rel="noopener"
                   class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-amber-400 to-amber-600 px-7 py-4 text-sm font-bold text-emerald-950 shadow-lg shadow-amber-900/20 transition hover:from-amber-300 hover:to-amber-500 sm:w-auto">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Konfirmasi via WhatsApp
                </a>
                <a href="{{ route('donasi-pendidikan.form-donatur') }}"
                   class="inline-flex w-full items-center justify-center rounded-xl border border-gray-300 bg-white px-7 py-4 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 sm:w-auto">
                    Kembali ke Form
                </a>
            </div>
        </div>

    </div>
</section>

@endsection
