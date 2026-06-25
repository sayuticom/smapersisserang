<x-admin-layout>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.website.menus.index') }}" class="text-sm text-[#0F6B3A] hover:underline">&larr; Kembali</a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">Edit Menu: {{ $navigationMenu->label }}</h2>
        </div>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.website.menus.update', $navigationMenu) }}" method="POST" class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Label <span class="text-red-500">*</span></label>
                <input type="text" name="label" value="{{ old('label', $navigationMenu->label) }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
                @error('label') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Parent Menu</label>
                <select name="parent_key"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
                    <option value="">Tidak ada (menu utama)</option>
                    @foreach($parentOptions as $parent)
                        <option value="{{ $parent->menu_key }}" {{ old('parent_key', $navigationMenu->parent_key) === $parent->menu_key ? 'selected' : '' }}>
                            {{ $parent->label }}
                        </option>
                    @endforeach
                </select>
                @error('parent_key') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $navigationMenu->sort_order) }}" min="0"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
                @error('sort_order') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                <select name="location"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
                    <option value="public_header" {{ old('location', $navigationMenu->location) === 'public_header' ? 'selected' : '' }}>Public Header</option>
                </select>
                @error('location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', $navigationMenu->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-[#0F6B3A] focus:ring-[#0F6B3A]">
                    <label for="is_active" class="text-sm font-medium text-gray-700">Aktif</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_external" id="is_external" value="1"
                           {{ old('is_external', $navigationMenu->is_external) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-[#0F6B3A] focus:ring-[#0F6B3A]">
                    <label for="is_external" class="text-sm font-medium text-gray-700">Link Eksternal</label>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-[#0F6B3A] text-white font-medium rounded-lg hover:bg-[#0A4F2B] transition-colors">
                    Simpan
                </button>
                <a href="{{ route('admin.website.menus.index') }}"
                   class="px-6 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-admin-layout>
