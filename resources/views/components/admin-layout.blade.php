<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Admin SMA PERSIS SERANG</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        .admin-sidebar-menu > a:not(:first-child) {
            margin-left: 0.75rem;
        }

        @media (max-width: 420px) {
            .admin-sidebar-menu > a:not(:first-child) {
                margin-left: 0.5rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased bg-slate-50">
    <div x-data="{ sidebarOpen: false }">
        <!-- Mobile hamburger -->
        <div class="lg:hidden fixed top-4 right-4 z-50">
            <button @click="sidebarOpen = !sidebarOpen"
                    class="p-2.5 rounded-lg bg-green-700 text-white shadow-lg hover:bg-green-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

        <!-- Mobile overlay -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity duration-200"
             x-transition:leave="transition-opacity duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/50 z-30 lg:hidden"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-slate-200 transform transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col h-screen">
            <div class="flex-shrink-0 p-4 border-b border-slate-100">
                @php
                    $adminSetting = \App\Models\SchoolSetting::current();
                @endphp
                <div class="flex items-center gap-3">
                    @if($adminSetting?->logo_path)
                        <img src="{{ asset('storage/' . $adminSetting->logo_path) }}"
                             alt="{{ $adminSetting->school_name }}"
                             class="w-9 h-9 rounded-lg object-contain bg-white border border-emerald-100 flex-shrink-0">
                    @else
                        <div class="w-9 h-9 bg-green-700 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-bold text-sm">PS</span>
                        </div>
                    @endif
                    <div class="min-w-0">
                        <h2 class="font-semibold text-gray-900 text-sm truncate">{{ $adminSetting->school_name ?? 'SMA Persis Serang' }}</h2>
                        <p class="text-xs text-slate-500">{{ $adminSetting->tagline ?? 'Islamic Boarding School' }}</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto py-2 px-3">
                <div class="admin-sidebar-menu space-y-0.5">
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h2a1 1 0 001-1v-7m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>

                    <div class="pt-3 pb-1">
                        <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">SPMB</p>
                    </div>

                    <a href="{{ route('admin.ppdb.dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.ppdb.dashboard') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.ppdb.dashboard') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Dashboard SPMB
                    </a>

                    <a href="{{ route('admin.ppdb.applications.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.ppdb.applications.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.ppdb.applications.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Data Pendaftaran
                    </a>

                    <a href="{{ route('admin.ppdb.settings.edit') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.ppdb.settings.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.ppdb.settings.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Pengaturan SPMB
                    </a>

                    <div class="pt-3 pb-1">
                        <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">KEUANGAN</p>
                    </div>

                    <a href="{{ route('admin.finance.dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.finance.dashboard') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.finance.dashboard') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                        </svg>
                        Dashboard Keuangan
                    </a>

                    <a href="{{ route('admin.finance.incomes.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors ml-6 {{ request()->routeIs('admin.finance.incomes.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('admin.finance.incomes.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3 3m0 0l-3-3m3 3V8m0 13a9 9 0 110-18 9 9 0 010 18z" />
                        </svg>
                        Pemasukan
                    </a>

                    <a href="{{ route('admin.finance.expenses.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors ml-6 {{ request()->routeIs('admin.finance.expenses.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('admin.finance.expenses.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11l-3-3m0 0l-3 3m3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z" />
                        </svg>
                        Pengeluaran
                    </a>

                    <a href="{{ route('admin.finance.report') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors ml-6 {{ request()->routeIs('admin.finance.report') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('admin.finance.report') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Laporan
                    </a>

                    <div class="pt-3 pb-1">
                        <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">SURAT MENYURAT</p>
                    </div>

                    <a href="{{ route('admin.letters.outgoings.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.letters.outgoings.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.letters.outgoings.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Surat Keluar
                    </a>

                    <a href="{{ route('admin.letters.incomings.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.letters.incomings.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.letters.incomings.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-18 8h18a2 2 0 002-2V8a2 2 0 00-2-2H3a2 2 0 00-2 2v6a2 2 0 002 2z" />
                        </svg>
                        Surat Masuk
                    </a>

                    <a href="{{ route('admin.letters.templates.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.letters.templates.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.letters.templates.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Template Surat
                    </a>

                    <a href="{{ route('admin.letters.signers.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.letters.signers.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.letters.signers.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 20h4.586a1 1 0 00.707-.293L19.5 9.5a2.121 2.121 0 00-3-3L6.293 16.707A1 1 0 006 17.414V20z" />
                        </svg>
                        Penandatangan
                    </a>

                    <a href="{{ route('admin.letters.types.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.letters.types.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.letters.types.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Jenis Surat
                    </a>

                    <a href="{{ route('admin.letters.settings.edit') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.letters.settings.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.letters.settings.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Pengaturan Surat
                    </a>

                    <div class="pt-3 pb-1">
                        <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">DONASI</p>
                    </div>

                    <a href="{{ route('admin.website.donasi-pendidikan.edit') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.website.donasi-pendidikan.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.website.donasi-pendidikan.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.761 0-5-1.12-5-2.5S9.239 3 12 3s5 1.12 5 2.5S14.761 8 12 8zm-5 2v5.5C7 16.88 9.239 18 12 18s5-1.12 5-2.5V10m-10 3c0 1.38 2.239 2.5 5 2.5s5-1.12 5-2.5"/>
                        </svg>
                        Donasi Pendidikan
                    </a>

                    <a href="{{ route('admin.donasi-transactions.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.donasi-transactions.index') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.donasi-transactions.index') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Donasi Masuk
                    </a>

                    <a href="{{ route('admin.donasi-transactions.create-receipt') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.donasi-transactions.create-receipt') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.donasi-transactions.create-receipt') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6m12 8H6a2 2 0 01-2-2V6a2 2 0 012-2h7l5 5v9a2 2 0 01-2 2z"/>
                        </svg>
                        Bukti Penerimaan Donasi
                    </a>

                    <a href="{{ route('admin.infaq-barang-wa.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.infaq-barang-wa.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.infaq-barang-wa.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h8M8 14h5m8-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Data WA Infaq Barang
                    </a>

                    <a href="{{ route('admin.donasi-pendidikan.share-template') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.donasi-pendidikan.share-template') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.donasi-pendidikan.share-template') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Template Share WA
                    </a>

                    <div class="pt-3 pb-1">
                        <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">ORANG TUA ASUH</p>
                    </div>

                    <a href="{{ route('admin.orang-tua-asuh.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.orang-tua-asuh.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.orang-tua-asuh.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Pengajuan OTA
                    </a>

                    <div class="pt-3 pb-1">
                        <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">WAKAF</p>
                    </div>

                    <a href="{{ route('admin.wakaf.settings.edit') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.wakaf.settings.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.wakaf.settings.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Pengaturan Wakaf
                    </a>

                    <a href="{{ route('admin.wakaf.transactions.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.wakaf.transactions.index') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.wakaf.transactions.index') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Wakaf Masuk
                    </a>

                    <a href="{{ route('admin.wakaf.transactions.create-receipt') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.wakaf.transactions.create-receipt') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.wakaf.transactions.create-receipt') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6m12 8H6a2 2 0 01-2-2V6a2 2 0 012-2h7l5 5v9a2 2 0 01-2 2z"/>
                        </svg>
                        Buat Bukti Penerimaan
                    </a>

                    <div class="pt-3 pb-1">
                        <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">SARPRAS</p>
                    </div>

                    <a href="{{ route('admin.sarpras.dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.sarpras.dashboard') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.sarpras.dashboard') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h2a1 1 0 001-1v-7m-6 0h6"></path>
                        </svg>
                        Dashboard Sarpras
                    </a>

                    <a href="{{ route('admin.sarpras.assets.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.sarpras.assets.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.sarpras.assets.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Data Aset
                    </a>

                    <a href="{{ route('admin.sarpras.rooms.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.sarpras.rooms.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.sarpras.rooms.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                        Data Ruangan
                    </a>

                    <a href="{{ route('admin.sarpras.needs.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.sarpras.needs.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.sarpras.needs.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Kebutuhan Sarpras
                    </a>

                    <a href="{{ route('admin.sarpras.maintenances.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.sarpras.maintenances.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.sarpras.maintenances.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Perbaikan
                    </a>

                    <a href="{{ route('admin.sarpras.procurements.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.sarpras.procurements.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.sarpras.procurements.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        Pengadaan Barang
                    </a>

                    <a href="{{ route('admin.sarpras.laporan') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.sarpras.laporan') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.sarpras.laporan') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Laporan Sarpras
                    </a>

                    <div class="pt-3 pb-1">
                        <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">WEBSITE</p>
                    </div>

                    <a href="{{ route('admin.website.settings.edit') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.website.settings.edit') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.website.settings.edit') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Pengaturan Website
                    </a>

                    <a href="{{ route('admin.website.boarding.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors ml-6 {{ request()->routeIs('admin.website.boarding.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('admin.website.boarding.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Konten Boarding
                    </a>

                    <a href="{{ route('admin.website.media.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.website.media.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.website.media.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Galeri Sekolah
                    </a>

                    <a href="{{ route('admin.website.categories.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.website.categories.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.website.categories.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Kategori Galeri
                    </a>

                    <a href="{{ route('admin.website.pages.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.website.pages.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.website.pages.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        Konten Halaman
                    </a>

                    <a href="{{ route('admin.website.menus.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.website.menus.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.website.menus.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Menu Navigasi
                    </a>

                    <a href="{{ route('admin.organization-structures.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.organization-structures.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.organization-structures.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 6h6M9 12h6m-6 6h6M5 6h.01M5 12h.01M5 18h.01M19 6h.01M19 12h.01M19 18h.01"/>
                        </svg>
                        Struktur Organisasi
                    </a>

                    <a href="{{ route('admin.website.values.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.website.values.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.website.values.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118L3.077 10.1c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        Nilai Utama
                    </a>

                    <a href="{{ route('admin.website.figures.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.website.figures.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.website.figures.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h18a7 7 0 00-7-7z"></path>
                        </svg>
                        Tokoh &amp; Pembina
                    </a>

                    <div class="pt-3 pb-1">
                        <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">AKADEMIK</p>
                    </div>

                    <a href="{{ route('admin.website.teachers.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.website.teachers.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.website.teachers.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2m8-10a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Profil Guru
                    </a>

                    <a href="{{ route('admin.website.subjects.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.website.subjects.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.website.subjects.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.75a6 6 0 016-3v13.5a6 6 0 00-6 3m0-13.5a6 6 0 00-6-3v13.5a6 6 0 006 3m0-13.5v13.5"/>
                        </svg>
                        Mata Pelajaran
                    </a>

                    <div x-data="{ open: {{ request()->routeIs('admin.akademik.kalender.*') || request()->routeIs('admin.akademik.jadwal-pelajaran.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" type="button"
                                class="flex w-full items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.akademik.kalender.*') || request()->routeIs('admin.akademik.jadwal-pelajaran.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.akademik.kalender.*') || request()->routeIs('admin.akademik.jadwal-pelajaran.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="flex-1 text-left">Kalender dan Jadwal</span>
                            <svg class="h-4 w-4 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" class="ml-6 space-y-0.5 mt-0.5">
                            <a href="{{ route('admin.akademik.kalender.index') }}"
                               class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.akademik.kalender.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('admin.akademik.kalender.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Kalender Pendidikan
                            </a>
                            <a href="{{ route('admin.akademik.jadwal-pelajaran.index') }}"
                               class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.akademik.jadwal-pelajaran.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('admin.akademik.jadwal-pelajaran.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                Jadwal Pelajaran
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('admin.akademik.tahun-pelajaran.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.akademik.tahun-pelajaran.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.akademik.tahun-pelajaran.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6h12M6 12h12M6 18h12"/>
                        </svg>
                        Tahun Pelajaran
                    </a>

                    @if(auth()->user()?->isAdmin())
                    <a href="{{ route('admin.akademik.kelas.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.akademik.kelas.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.akademik.kelas.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Kelas / Rombel
                    </a>
                    @endif

                    @if(auth()->user()?->isAdmin())
                    <a href="{{ route('admin.akademik.jam-pelajaran.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.akademik.jam-pelajaran.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.akademik.jam-pelajaran.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Jam Pelajaran
                    </a>
                    @endif

                    <div class="pt-3 pb-1">
                        <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">SISTEM</p>
                    </div>

                    <a href="{{ route('admin.ai-faqs.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.ai-faqs.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.ai-faqs.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        FAQ AI
                    </a>

                    @auth
                        @if(Auth::user()->isSuperadmin())
                            <a href="{{ route('admin.users.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                Kelola User
                            </a>
                        @endif
                    @endauth

                    <div class="pt-3 pb-1">
                        <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">AKUN</p>
                    </div>

                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('profile.*') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('profile.*') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h18a7 7 0 00-7-7z"></path>
                        </svg>
                        Profil
                    </a>
                </div>
            </nav>

            <!-- User section pinned to bottom -->
            <div class="flex-shrink-0 p-3 border-t border-slate-100">
                <div class="flex items-center gap-3 px-3 py-2">
                    <div class="w-8 h-8 bg-slate-200 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h18a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-900 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-500 transition-colors" title="Logout">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <div class="lg:pl-64 min-h-screen">
            <main class="px-4 py-6 sm:px-6 lg:px-8 max-w-7xl mx-auto">
                {{ $slot }}
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
