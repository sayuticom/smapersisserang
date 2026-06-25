<x-admin-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Mata Pelajaran</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola mata pelajaran dan guru pengampu</p>
            </div>
            <a href="{{ route('admin.website.subjects.create') }}"
               class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Mapel
            </a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @php
            $categoryLabels = \App\Models\SchoolSubject::CATEGORIES;
            $categoryColors = [
                'nasional' => 'bg-blue-100 text-blue-700',
                'keislaman' => 'bg-emerald-100 text-emerald-700',
                'teknologi' => 'bg-purple-100 text-purple-700',
                'boarding' => 'bg-amber-100 text-amber-700',
            ];
        @endphp

        @forelse($subjects as $category => $items)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 {{ $category === 'nasional' ? 'bg-blue-50' : ($category === 'keislaman' ? 'bg-emerald-50' : ($category === 'teknologi' ? 'bg-purple-50' : 'bg-amber-50')) }}">
                    <h3 class="text-sm font-semibold uppercase tracking-wider {{ $category === 'nasional' ? 'text-blue-800' : ($category === 'keislaman' ? 'text-emerald-800' : ($category === 'teknologi' ? 'text-purple-800' : 'text-amber-800')) }}">
                        {{ $categoryLabels[$category] ?? $category }}
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($items as $subject)
                        <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-gray-900">{{ $subject->name }}</span>
                                    @if(!$subject->is_active)
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-500">Nonaktif</span>
                                    @endif
                                </div>
                                @if($subject->description)
                                    <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $subject->description }}</p>
                                @endif
                                @if($subject->teachers->isNotEmpty())
                                    <p class="text-xs text-gray-400 mt-1">
                                        Guru: {{ $subject->teachers->pluck('name')->join(', ') }}
                                    </p>
                                @else
                                    <p class="text-xs text-gray-400 mt-1 italic">Belum ada guru</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="text-xs text-gray-400">Urutan: {{ $subject->sort_order }}</span>
                                <a href="{{ route('admin.website.subjects.edit', $subject) }}"
                                   class="p-1.5 text-gray-400 hover:text-emerald-600 transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.website.subjects.toggle', $subject) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="p-1.5 {{ $subject->is_active ? 'text-gray-400 hover:text-amber-500' : 'text-gray-400 hover:text-emerald-600' }} transition-colors"
                                            title="{{ $subject->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        @if($subject->is_active)
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
                                <form method="POST" action="{{ route('admin.website.subjects.destroy', $subject) }}"
                                      onsubmit="return confirm('Hapus mata pelajaran ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="p-1.5 text-gray-400 hover:text-red-500 transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 mx-auto bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                </div>
                <p class="text-gray-500 font-medium">Belum ada mata pelajaran.</p>
                <a href="{{ route('admin.website.subjects.create') }}"
                   class="mt-4 inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                    Tambah Mapel Pertama
                </a>
            </div>
        @endforelse
    </div>
</x-admin-layout>
