<x-admin-layout>
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('admin.website.subjects.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition-colors">&larr; Kembali</a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">Tambah Mata Pelajaran</h2>
        </div>

        <form method="POST" action="{{ route('admin.website.subjects.store') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Mata Pelajaran <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                <select name="category" id="category" required
                        class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    <option value="">Pilih Kategori</option>
                    @foreach(\App\Models\SchoolSubject::CATEGORIES as $categoryKey => $categoryLabel)
                        <option value="{{ $categoryKey }}" @selected(old('category') === $categoryKey)>{{ $categoryLabel }}</option>
                    @endforeach
                </select>
                @error('category')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" id="description" rows="3"
                          class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('description') }}</textarea>
                @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="teacher_ids" class="block text-sm font-medium text-gray-700 mb-1">Guru Pengampu</label>
                <select name="teacher_ids[]" id="teacher_ids" multiple
                        class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" @selected(in_array($teacher->id, old('teacher_ids', [])))>
                            {{ $teacher->name }}{{ $teacher->subject ? ' - ' . $teacher->subject : '' }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">Gunakan Ctrl+Click atau Cmd+Click untuk memilih lebih dari satu guru.</p>
                @error('teacher_ids')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                           class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('sort_order')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="is_active" id="is_active"
                            class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        <option value="1" @selected(old('is_active', '1') === '1')>Aktif</option>
                        <option value="0" @selected(old('is_active', '1') === '0')>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
