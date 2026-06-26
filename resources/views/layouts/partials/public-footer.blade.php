<footer class="bg-emerald-950 text-emerald-100">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-[1.3fr_1fr_1fr_1fr]">
            <div>
                <div class="flex items-center space-x-3">
                    @if($schoolSetting?->logo_path)
                        <img src="{{ asset('storage/' . $schoolSetting->logo_path) }}"
                             alt="{{ $schoolSetting->school_name }}"
                             class="h-12 w-12 rounded-xl border border-amber-300/30 bg-white object-contain p-1">
                    @else
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl border border-amber-300/30 bg-white/10 text-sm font-bold text-amber-300">
                            {{ substr($schoolSetting->short_name ?? $schoolSetting->school_name ?? config('school.name', 'SMA Persis Serang'), 0, 2) }}
                        </div>
                    @endif
                    <div>
                        <p class="font-bold text-white">{{ $schoolSetting->school_name ?? config('school.name', 'SMA Persis Serang') }}</p>
                        <p class="text-xs text-emerald-100/70">{{ $schoolSetting->tagline ?? 'Islamic Boarding School' }}</p>
                    </div>
                </div>
                @if($schoolSetting?->description)
                    <p class="mt-5 max-w-sm text-sm leading-6 text-emerald-100/70">
                        {{ Str::limit($schoolSetting->description, 150) }}
                    </p>
                @endif
            </div>

            <div>
                <h4 class="mb-4 text-sm font-bold uppercase tracking-[0.2em] text-amber-300">Alamat</h4>
                <div class="space-y-2 text-sm leading-6 text-emerald-100/75">
                    @if($schoolSetting?->address)
                        <p>{{ $schoolSetting->address }}</p>
                    @endif
                    @if($schoolSetting?->city || $schoolSetting?->province)
                        <p>{{ $schoolSetting->city }}{{ $schoolSetting->city && $schoolSetting->province ? ', ' : '' }}{{ $schoolSetting->province }}</p>
                    @endif
                    @unless($schoolSetting?->address || $schoolSetting?->city || $schoolSetting?->province)
                        <p>Serang, Banten</p>
                    @endunless
                    @if($schoolSetting?->google_maps_link)
                        <a href="{{ $schoolSetting->google_maps_link }}" target="_blank" rel="noopener"
                           class="inline-block mt-2 text-amber-300 text-xs hover:text-amber-200 transition-colors">
                            Lihat Lokasi
                        </a>
                    @endif
                </div>
            </div>

            <div>
                <h4 class="mb-4 text-sm font-bold uppercase tracking-[0.2em] text-amber-300">Kontak</h4>
                <div class="space-y-2 text-sm text-emerald-100/75">
                    @if($schoolSetting?->whatsapp_number)
                        <p>WhatsApp: {{ $schoolSetting->whatsapp_number }}</p>
                    @endif
                    @if($schoolSetting?->email)
                        <p>Email: {{ $schoolSetting->email }}</p>
                    @endif
                    @unless($schoolSetting?->whatsapp_number || $schoolSetting?->email)
                        <p>Email: info@smapersisserang.sch.id</p>
                    @endunless
                    <a href="{{ route('spmb.status.form') }}" class="inline-block pt-2 text-amber-300 transition hover:text-amber-200">Cek Status SPMB</a>
                </div>
            </div>

            <div>
                <h4 class="mb-4 text-sm font-bold uppercase tracking-[0.2em] text-amber-300">Menu</h4>
                <div class="space-y-2 text-sm">
                    @forelse($footerMenuItems as $menu)
                        <a href="{{ $menu->url() }}"
                           class="block text-emerald-100/75 transition hover:text-amber-300">
                            {{ $menu->label }}
                        </a>
                    @empty
                        <a href="{{ route('public.profile') }}" class="block text-emerald-100/75 transition hover:text-amber-300">Profil</a>
                        <a href="{{ route('public.teachers') }}" class="block text-emerald-100/75 transition hover:text-amber-300">Guru</a>
                    @endforelse
                    <a href="{{ route('login') }}" class="block pt-2 text-emerald-100/75 transition hover:text-amber-300">Login Admin</a>
                </div>
            </div>
        </div>

        <div class="mt-10 flex flex-col gap-3 border-t border-white/10 pt-6 text-sm text-emerald-100/60 md:flex-row md:items-center md:justify-between">
            <p>&copy; {{ date('Y') }} {{ $schoolSetting->school_name ?? config('school.name', 'SMA Persis Serang') }}. All rights reserved.</p>
            <div class="flex gap-4">
                @foreach($footerMenuItems->take(3) as $menu)
                    <a href="{{ $menu->url() }}" class="transition hover:text-amber-300">{{ $menu->label }}</a>
                @endforeach
            </div>
        </div>
    </div>
</footer>
