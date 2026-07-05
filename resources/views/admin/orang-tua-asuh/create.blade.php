<x-admin-layout>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.orang-tua-asuh.index') }}" class="text-sm text-[#0F6B3A] hover:text-[#0A4F2B]">&larr; Kembali</a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">Tambah Murid</h2>
        </div>

        <form action="{{ route('admin.orang-tua-asuh.store') }}" method="POST" enctype="multipart/form-data"
              class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Murid <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <select name="gender" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
                        <option value="Laki-laki" {{ old('gender') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('gender') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas <span class="text-red-500">*</span></label>
                    <input type="text" name="class_name" value="{{ old('class_name') }}" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
                    @error('class_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Asal Daerah</label>
                    <input type="text" name="origin" value="{{ old('origin') }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
                    @error('origin') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Anak Asuh <span class="text-red-500">*</span></label>
                    <select name="foster_status" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
                        <option value="available" {{ old('foster_status') === 'available' ? 'selected' : '' }}>Belum Ada Orang Tua Asuh</option>
                        <option value="assigned" {{ old('foster_status') === 'assigned' ? 'selected' : '' }}>Sudah Ada Orang Tua Asuh</option>
                        <option value="inactive" {{ old('foster_status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                    @error('foster_status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Murid</label>
                <input type="file" name="photo" accept="image/jpeg,image/png,image/jpg,image/webp"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
                <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, WebP. Maksimal 2MB.</p>
                @error('photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ringkasan Kebutuhan</label>
                <textarea name="need_description" rows="3"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">{{ old('need_description') }}</textarea>
                @error('need_description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-[#0F6B3A] focus:ring-[#0F6B3A]">
                    <label for="is_active" class="text-sm font-medium text-gray-700">Aktif</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_priority" id="is_priority" value="1"
                           {{ old('is_priority') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                    <label for="is_priority" class="text-sm font-medium text-gray-700">Prioritas</label>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-[#0F6B3A] text-white font-medium rounded-lg hover:bg-[#0A4F2B] transition-colors">
                    Simpan
                </button>
                <a href="{{ route('admin.orang-tua-asuh.index') }}"
                   class="px-6 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-admin-layout>
