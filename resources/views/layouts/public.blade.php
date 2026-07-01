@php
    try {
        $schoolSetting ??= \App\Models\SchoolSetting::current();
    } catch (\Exception $e) {
        $schoolSetting = null;
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', ($schoolSetting->school_name ?? 'SMA Persis Serang') . ' - ' . ($title ?? 'Website Sekolah'))</title>

        @hasSection('meta')
            @yield('meta')
        @else
            <meta name="description" content="SMA Persis Serang - Sekolah berbasis akhlak, ilmu, teknologi, dan pembinaan islami. Gratis biaya pendidikan dan asrama untuk satu rombongan belajar.">

            <meta property="og:title" content="SMA Persis Serang">
            <meta property="og:description" content="Sekolah berbasis akhlak, ilmu, teknologi, dan pembinaan islami. Gratis biaya pendidikan dan asrama untuk satu rombongan belajar.">
            <meta property="og:type" content="website">
            <meta property="og:url" content="{{ url('/') }}">
            <meta property="og:image" content="{{ asset('images/og-sma-persis-serang.jpg') }}">
            <meta property="og:image:width" content="1200">
            <meta property="og:image:height" content="630">

            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:title" content="SMA Persis Serang">
            <meta name="twitter:description" content="Sekolah berbasis akhlak, ilmu, teknologi, dan pembinaan islami.">
            <meta name="twitter:image" content="{{ asset('images/og-sma-persis-serang.jpg') }}">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white text-gray-900">
        @include('layouts.partials.public-header', [
            'variant' => request()->is('/') ? 'transparent' : 'solid',
        ])

        <main class="min-h-screen">
            @yield('content')
        </main>

        @include('layouts.partials.public-footer')
    </body>
</html>
