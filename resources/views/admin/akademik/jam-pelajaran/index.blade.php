<x-admin-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Pengaturan Jam Pelajaran</h2>
                <p class="text-sm text-gray-500 mt-1">Atur jam pelajaran, istirahat, dan kegiatan rutin</p>
            </div>
            @if(auth()->user()?->isAdmin())
            <a href="{{ route('admin.akademik.jam-pelajaran.create') }}"
               class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Jam
            </a>
            @endif
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @php $dayOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']; @endphp

        @forelse($dayOrder as $dayLabel)
            @if(isset($settings[$dayLabel]) && $settings[$dayLabel]->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700">{{ $dayLabel }}</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($settings[$dayLabel] as $setting)
                            <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-gray-900">{{ $setting->name }}</span>
                                        @if(!$setting->is_active)
                                            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-500">Nonaktif</span>
                                        @endif
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                            {{ $setting->type === 'pelajaran' ? 'bg-blue-100 text-blue-700' : '' }}
                                            {{ $setting->type === 'istirahat' ? 'bg-green-100 text-green-700' : '' }}
                                            {{ $setting->type === 'ishoma' ? 'bg-amber-100 text-amber-700' : '' }}
                                            {{ $setting->type === 'upacara' ? 'bg-red-100 text-red-700' : '' }}
                                            {{ $setting->type === 'pembiasaan' ? 'bg-purple-100 text-purple-700' : '' }}
                                            {{ $setting->type === 'kegiatan_khusus' ? 'bg-slate-100 text-slate-700' : '' }}">
                                            {{ str_replace('_', ' ', ucfirst($setting->type)) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ \Carbon\Carbon::parse($setting->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($setting->end_time)->format('H:i') }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <span class="text-xs text-gray-400">Urutan: {{ $setting->sort_order }}</span>
                                    @if(auth()->user()?->isAdmin())
                                    <a href="{{ route('admin.akademik.jam-pelajaran.edit', $setting) }}"
                                       class="p-1.5 text-gray-400 hover:text-emerald-600 transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.akademik.jam-pelajaran.toggle', $setting) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="p-1.5 {{ $setting->is_active ? 'text-gray-400 hover:text-amber-500' : 'text-gray-400 hover:text-emerald-600' }} transition-colors"
                                                title="{{ $setting->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            @if($setting->is_active)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.akademik.jam-pelajaran.destroy', $setting) }}"
                                          onsubmit="return confirm('Hapus jam {{ $setting->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="mt-4"></div>
            @endif
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <p class="text-gray-500 font-medium">Belum ada jam pelajaran.</p>
                @if(auth()->user()?->isAdmin())
                <a href="{{ route('admin.akademik.jam-pelajaran.create') }}"
                   class="mt-4 inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                    Tambah Jam Pertama
                </a>
                @endif
            </div>
        @endforelse
    </div>
</x-admin-layout>
