@extends('layouts.public')

@php
    $app = $application;
    $activeStep = (int) request('step', $app->current_step ?: 1);
    $activeStep = max(1, min(6, $activeStep));
    $steps = [
        1 => ['title' => 'Data Siswa', 'completed' => filled($app->student_data_completed_at)],
        2 => ['title' => 'Data Orang Tua', 'completed' => filled($app->parent_data_completed_at)],
        3 => ['title' => 'Data Wali', 'completed' => (int) ($app->current_step ?: 1) > 3],
        4 => ['title' => 'Boarding & Kesehatan', 'completed' => filled($app->guardian_boarding_completed_at)],
        5 => ['title' => 'Upload Dokumen', 'completed' => filled($app->documents_completed_at)],
        6 => ['title' => 'Review Final', 'completed' => (bool) $app->is_final_submitted],
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
            @include('ppdb.update-data.partials.submitted')
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
                    @include('ppdb.update-data.partials.boarding-health')
                @elseif($activeStep === 5)
                    @include('ppdb.update-data.partials.documents')
                @else
                    @include('ppdb.update-data.partials.review')
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
