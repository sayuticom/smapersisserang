<x-admin-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Profil Guru</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola data tenaga pendidik</p>
            </div>
            <a href="{{ route('admin.website.teachers.create') }}"
               class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Guru
            </a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('admin.website.teachers.index') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 md:p-5">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="flex-1 min-w-0">
                    <label for="search" class="sr-only">Cari guru</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama guru atau mata pelajaran..."
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="flex flex-col sm:flex-row gap-3 shrink-0">
                    <select name="status"
                            class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <button type="submit"
                            class="px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm whitespace-nowrap">
                        Cari
                    </button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.website.teachers.index') }}"
                           class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors whitespace-nowrap text-center">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>

        @if($teachers->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($teachers as $teacher)
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-start gap-4">
                        <div class="w-14 h-14 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                            @if($teacher->photo_path)
                                <img src="{{ asset('storage/' . $teacher->photo_path) }}" alt="{{ $teacher->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-[#0F6B3A]/10 flex items-center justify-center">
                                    <span class="text-[#0F6B3A] font-bold text-lg">{{ substr($teacher->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $teacher->name }}</h3>
                            <p class="text-xs text-gray-500 truncate">{{ $teacher->subject ?? $teacher->position ?? '(tanpa data)' }}</p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-medium rounded {{ $teacher->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $teacher->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                                <span class="text-[10px] text-gray-400">Urutan: {{ $teacher->sort_order }}</span>
                            </div>
                            <div class="flex items-center gap-2 mt-1">
                                @if($teacher->whatsapp_number)
                                    <span class="text-[10px] text-gray-400">WA: {{ $teacher->whatsapp_number }}</span>
                                @endif
                                <span class="text-[10px] {{ $teacher->public_edit_token ? 'text-emerald-600' : 'text-gray-400' }}">
                                    Token: {{ $teacher->public_edit_token ? 'Ada' : 'Belum' }}
                                </span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1.5 flex-shrink-0">
                            <a href="{{ route('admin.website.teachers.edit', $teacher) }}" class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors text-center">Edit</a>
                            <form method="POST" action="{{ route('admin.website.teachers.toggle', $teacher) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="px-2.5 py-1.5 text-xs font-medium rounded-lg w-full transition-colors {{ $teacher->is_active ? 'bg-amber-50 text-amber-600 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' }}">
                                    {{ $teacher->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.website.teachers.destroy', $teacher) }}" onsubmit="return confirm('Hapus guru ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-2.5 py-1.5 text-xs font-medium rounded-lg w-full bg-red-50 text-red-600 hover:bg-red-100 transition-colors">Hapus</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8 text-center">
                @if(request()->hasAny(['search', 'status']))
                    <p class="text-gray-400 font-medium">Tidak ada guru yang cocok dengan filter.</p>
                    <a href="{{ route('admin.website.teachers.index') }}" class="inline-flex items-center px-4 py-2 mt-4 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">Reset Filter</a>
                @else
                    <p class="text-gray-400 font-medium">Belum ada data guru.</p>
                    <a href="{{ route('admin.website.teachers.create') }}" class="inline-flex items-center px-4 py-2 mt-4 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">Tambah Guru</a>
                @endif
            </div>
        @endif
    </div>
</x-admin-layout>