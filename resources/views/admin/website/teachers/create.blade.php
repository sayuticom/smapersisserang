<x-admin-layout>
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('admin.website.teachers.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">&larr; Kembali</a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">Tambah Guru</h2>
        </div>
        <form method="POST" action="{{ route('admin.website.teachers.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran</label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" placeholder="Matematika, Bahasa Inggris, ...">
                </div>
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                    <input type="text" name="position" id="position" value="{{ old('position') }}" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" placeholder="Wali Kelas, ...">
                </div>
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" id="description" rows="4" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('description') }}</textarea>
            </div>
            <div>
                <label for="teacher_quote" class="block text-sm font-medium text-gray-700 mb-1">Moto / Kutipan Guru</label>
                <textarea name="teacher_quote" id="teacher_quote" rows="3" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" placeholder="Contoh: Pendidikan adalah jalan terbaik untuk menyiapkan masa depan yang berakar pada nilai dan akhlak.">{{ old('teacher_quote') }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Teks ini akan tampil di card guru pada halaman publik.</p>
            </div>
            <div>
                <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
                <input type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number') }}" placeholder="Contoh: 081234567890" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="w-32 rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">Foto</label>
                <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/jpg,image/webp" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                <p class="text-xs text-gray-400 mt-1">Maksimal 2MB. Format: JPG, PNG, WebP.</p>
                @error('photo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">Simpan</button>
            </div>
        </form>
    </div>
</x-admin-layout>