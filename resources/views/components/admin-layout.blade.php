<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SMA Persis Serang') }} - Admin</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                <div class="space-y-0.5">
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-green-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h2a1 1 0 001-1v-7m-6 0h6"></path>
                        </svg>
                        Beranda
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
                        Pendaftar SPMB
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.75a6 6 0 016-3v13.5a6 6 0 00-6 3m0-13.5a6 6 0 00-6-3v13.5a6 6 0 016 3m0-13.5v13.5"/>
                        </svg>
                        Mata Pelajaran
                    </a>

                    <div class="pt-3 pb-1">
                        <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">SISTEM</p>
                    </div>

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
