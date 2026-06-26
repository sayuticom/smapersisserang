@php
    $headerVariant = $variant ?? (request()->is('/') ? 'transparent' : 'solid');
    $isTransparentHeader = $headerVariant === 'transparent';
    $desktopHeaderClass = $isTransparentHeader
        ? 'absolute inset-x-0 top-0 z-40 border-b border-white/10 bg-emerald-950/20 backdrop-blur-sm'
        : 'sticky top-0 z-40 border-b border-amber-300/20 bg-emerald-950/95 shadow-lg shadow-emerald-950/10 backdrop-blur';
    $desktopTextClass = 'text-emerald-50 hover:text-amber-300 hover:bg-white/10';
    $desktopActiveClass = 'text-amber-300 bg-white/10';
    $desktopBrandTitleClass = 'text-white';
    $desktopBrandSubtitleClass = 'text-emerald-100';
@endphp

<header x-data="{ mobileMenuOpen: false }" class="{{ $desktopHeaderClass }}" data-public-header data-header-variant="{{ $headerVariant }}">
    <div class="hidden h-20 max-w-7xl items-center justify-between px-4 mx-auto lg:flex">
        <a href="{{ url('/') }}" class="flex items-center space-x-3">
            @if($schoolSetting?->logo_path)
                <img src="{{ asset('storage/' . $schoolSetting->logo_path) }}"
                     alt="{{ $schoolSetting->school_name }}"
                     class="h-12 w-12 rounded-xl border border-white/20 bg-white object-contain p-1 shadow-sm">
            @else
                <div class="flex h-12 w-12 items-center justify-center rounded-xl border border-amber-300/40 bg-white/10 text-sm font-bold text-amber-300 shadow-sm">
                    {{ substr($schoolSetting->short_name ?? $schoolSetting->school_name ?? 'SMA Persis Serang', 0, 2) }}
                </div>
            @endif
            <div>
                <h1 class="text-lg font-bold leading-tight {{ $desktopBrandTitleClass }}">{{ $schoolSetting->school_name ?? 'SMA Persis Serang' }}</h1>
                <p class="text-xs leading-tight {{ $desktopBrandSubtitleClass }}">{{ $schoolSetting->tagline ?? 'Islamic Boarding School' }}</p>
            </div>
        </a>

        <nav class="flex items-center space-x-1">
            @foreach($publicMenuItems as $menu)
                @php $children = $menu->children()->active()->orderBy('sort_order')->get(); @endphp
                @if($children->isNotEmpty())
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.outside="open = false" type="button"
                                class="flex items-center gap-1 rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ $desktopTextClass }}"
                                :aria-expanded="open">
                            {{ $menu->label }}
                            <svg class="h-3.5 w-3.5 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-transition @click.outside="open = false"
                             class="absolute right-0 z-50 mt-3 w-56 rounded-xl border border-amber-100 bg-white py-2 shadow-xl shadow-emerald-950/10">
                            @foreach($children as $child)
                                <a href="{{ $child->url() }}"
                                   class="block px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-[#0F6B3A]">
                                    {{ $child->label }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ $menu->url() }}"
                       class="rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ url()->current() === $menu->url() ? $desktopActiveClass : $desktopTextClass }}">
                        {{ $menu->label }}
                    </a>
                @endif
            @endforeach
            <div class="mx-2 h-6 w-px bg-white/20"></div>
            <a href="{{ route('login') }}" class="rounded-lg border border-amber-300/80 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-white/10">
                Login
            </a>
        </nav>
    </div>

    <div class="bg-emerald-950 text-white lg:hidden">
        <div class="flex items-center justify-between px-4 py-3">
            <button x-on:click="mobileMenuOpen = !mobileMenuOpen" class="rounded-lg border border-white/10 bg-white/10 p-2 text-white">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <a href="{{ url('/') }}" class="flex min-w-0 items-center space-x-2">
                @if($schoolSetting?->logo_path)
                    <img src="{{ asset('storage/' . $schoolSetting->logo_path) }}"
                         alt="{{ $schoolSetting->school_name }}"
                         class="h-9 w-9 rounded-lg border border-white/20 bg-white object-contain p-1">
                @else
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg border border-amber-300/40 bg-white/10 text-xs font-bold text-amber-300">
                        {{ substr($schoolSetting->short_name ?? $schoolSetting->school_name ?? 'SMA Persis Serang', 0, 2) }}
                    </div>
                @endif
                <span class="truncate text-base font-bold">{{ $schoolSetting->school_name ?? 'SMA Persis Serang' }}</span>
            </a>

            <a href="{{ route('spmb.create') }}" class="rounded-lg bg-amber-400 px-3 py-2 text-xs font-bold text-emerald-950 shadow-sm">
                Daftar
            </a>
        </div>

        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="border-t border-white/10 bg-emerald-950 shadow-lg">
            <div class="space-y-1 px-4 py-2">
                @foreach($publicMenuItems as $menu)
                    @php $children = $menu->children()->active()->orderBy('sort_order')->get(); @endphp
                    @if($children->isNotEmpty())
                        <div x-data="{ open: false }">
                            <button @click="open = !open" type="button"
                                    class="flex w-full items-center justify-between rounded-lg px-4 py-3 text-sm font-medium text-emerald-50 hover:bg-white/10">
                                <span>{{ $menu->label }}</span>
                                <svg class="h-4 w-4 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" class="ml-4 space-y-1 pb-2">
                                @foreach($children as $child)
                                    <a href="{{ $child->url() }}"
                                       class="block rounded-lg px-4 py-2 text-sm text-emerald-100 hover:bg-white/10">
                                        {{ $child->label }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $menu->url() }}"
                           class="block rounded-lg px-4 py-3 text-sm font-medium text-emerald-50 hover:bg-white/10">
                            {{ $menu->label }}
                        </a>
                    @endif
                @endforeach
                <hr class="my-2 border-white/10">
                <a href="{{ route('login') }}" class="block rounded-lg px-4 py-3 text-sm font-medium text-amber-300 hover:bg-white/10">Login</a>
            </div>
        </div>
    </div>
</header>
