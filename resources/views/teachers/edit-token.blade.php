@extends('layouts.public')

@section('content')
<div class="bg-[#FBF7EF] min-h-screen">
    <div class="max-w-2xl mx-auto px-4 py-12">
        <div class="bg-white rounded-2xl shadow-sm border border-amber-200/70 p-6 sm:p-8">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Edit Data Guru</h1>
                <p class="text-gray-500 mt-1">Perbarui data diri Anda</p>
            </div>

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm mb-6">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('public.teachers.update-token', $teacher->public_edit_token) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $teacher->name) }}"
                           class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
                    <input type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number', $teacher->whatsapp_number) }}"
                           placeholder="Contoh: 081234567890"
                           class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @error('whatsapp_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat</label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('description', $teacher->description) }}</textarea>
                </div>

                <div>
                    <label for="teacher_quote" class="block text-sm font-medium text-gray-700 mb-1">Moto / Kutipan Guru</label>
                    <textarea name="teacher_quote" id="teacher_quote" rows="3"
                              placeholder="Contoh: Pendidikan adalah jalan terbaik untuk menyiapkan masa depan yang berakar pada nilai dan akhlak."
                              class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('teacher_quote', $teacher->teacher_quote) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Profil</label>
                    @if($teacher->photo_path)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $teacher->photo_path) }}" alt="{{ $teacher->name }}"
                                 class="h-28 w-auto rounded-xl border border-gray-200 object-contain">
                        </div>
                    @endif
                    <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/jpg,image/webp"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    <p class="text-xs text-gray-400 mt-1">Maksimal 2MB. Format: JPG, PNG, WebP.</p>
                    @error('photo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit"
                            class="px-6 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-xl hover:bg-emerald-700 transition-colors shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
