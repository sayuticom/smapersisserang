<x-admin-layout>
    <div class="max-w-7xl mx-auto">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Struktur Organisasi</h2>
                <p class="text-gray-500 mt-1">Kelola data organigram yang tampil di halaman publik.</p>
            </div>
            <a href="{{ route('admin.organization-structures.create') }}"
               class="inline-flex items-center justify-center rounded-lg bg-[#0F6B3A] px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-[#0A4F2B]">
                Tambah
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.organization-structures.page.update') }}" method="POST"
              class="mb-6 bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <h3 class="text-base font-semibold text-gray-900">Judul dan Pengantar Halaman</h3>
                <p class="text-sm text-gray-500 mt-1">Teks ini tampil di halaman publik Struktur Organisasi.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Halaman <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $websitePage->title) }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subjudul</label>
                    <input type="text" name="subtitle" value="{{ old('subtitle', $websitePage->subtitle) }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
                    @error('subtitle') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teks Pengantar</label>
                <textarea name="content" rows="4"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">{{ old('content', $websitePage->content) }}</textarea>
                @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                    class="inline-flex items-center rounded-lg bg-[#0F6B3A] px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-[#0A4F2B]">
                Simpan Pengantar
            </button>
        </form>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="text-center px-4 py-3 font-semibold text-slate-600">Urutan</th>
                            <th class="text-center px-4 py-3 font-semibold text-slate-600">Level</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Label</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Key</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Parent</th>
                            <th class="text-center px-4 py-3 font-semibold text-slate-600">Status</th>
                            <th class="text-right px-4 py-3 font-semibold text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($structures as $structure)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 text-center text-gray-500">{{ $structure->sort_order }}</td>
                                <td class="px-4 py-3 text-center text-gray-500">{{ $structure->level }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $structure->label }}</div>
                                    @if($structure->person_name)
                                        <div class="mt-1 text-xs font-medium text-[#0F6B3A]">{{ $structure->person_name }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-500">{{ $structure->structure_key }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $structure->parent_key ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($structure->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.organization-structures.edit', $structure) }}"
                                           class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-[#0F6B3A] bg-[#EAF6EE] rounded-lg hover:bg-[#D5EDDE] transition-colors">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.organization-structures.destroy', $structure) }}" method="POST"
                                              onsubmit="return confirm('Hapus data struktur organisasi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    Belum ada data struktur organisasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
