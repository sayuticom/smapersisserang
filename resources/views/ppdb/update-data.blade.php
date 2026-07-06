@extends('layouts.public')

@php
    $app = $application;
    $activeStep = (int) request('step', $app->current_step ?: 1);
    $activeStep = max(1, min(5, $activeStep));
    $steps = [
        1 => ['title' => 'Data Siswa', 'completed' => filled($app->student_data_completed_at)],
        2 => ['title' => 'Data Orang Tua', 'completed' => filled($app->parent_data_completed_at)],
        3 => ['title' => 'Wali & Kesehatan', 'completed' => filled($app->guardian_boarding_completed_at)],
        4 => ['title' => 'Upload Dokumen', 'completed' => filled($app->documents_completed_at)],
        5 => ['title' => 'Review Final', 'completed' => (bool) $app->is_final_submitted],
    ];
@endphp

@section('title', 'Pembaruan Data Siswa - SPMB SMA Persis Serang')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-emerald-950 via-emerald-900 to-emerald-800 py-6 sm:py-12">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="mb-5 text-center sm:mb-8">
            <h1 class="text-2xl font-bold text-white sm:text-3xl">Pembaruan Data Siswa</h1>
            <p class="mt-2 text-sm font-medium text-[#F5D36B] sm:mt-3 md:text-base">Simpan data bertahap agar progres tidak hilang.</p>
            <p class="mt-2 text-xs text-white/90 sm:text-sm">Nomor Pendaftaran: <strong>{{ $app->registration_number }}</strong></p>
        </div>

        @if($app->is_final_submitted)
            <div class="rounded-2xl border border-emerald-100 bg-white p-5 text-center shadow-lg sm:p-8">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="mt-4 text-xl font-bold text-slate-900">Data Sudah Dikirim</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Pembaruan data sudah dikirim final dan sedang menunggu verifikasi admin SPMB.</p>
                <a href="{{ route('spmb.info') }}" class="mt-5 inline-flex rounded-xl bg-emerald-700 px-5 py-3 text-sm font-bold text-white hover:bg-emerald-800">
                    Kembali ke Info SPMB
                </a>
            </div>
        @else
            @include('ppdb.update-data.partials.stepper')

            @if(session('success'))
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="font-bold">Ada data yang perlu diperbaiki.</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="overflow-hidden rounded-2xl bg-white shadow-lg">
                @if($activeStep === 1)
                    @include('ppdb.update-data.partials.student')
                @elseif($activeStep === 2)
                    @include('ppdb.update-data.partials.parents')
                @elseif($activeStep === 3)
                    @include('ppdb.update-data.partials.guardian-boarding')
                @elseif($activeStep === 4)
                    @include('ppdb.update-data.partials.documents')
                @else
                    @include('ppdb.update-data.partials.review')
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
