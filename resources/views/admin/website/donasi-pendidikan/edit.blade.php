<x-admin-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Donasi Pendidikan</h2>
                <p class="text-gray-500 mt-1">Kelola konten halaman publik Donasi Pendidikan.</p>
            </div>
            <a href="{{ route('donasi-pendidikan') }}" target="_blank"
               class="inline-flex items-center justify-center rounded-lg bg-[#EAF6EE] px-4 py-2.5 text-sm font-medium text-[#0F6B3A] transition-colors hover:bg-[#D5EDDE]">
                Lihat Halaman Donasi
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.website.donasi-pendidikan.update') }}" method="POST" enctype="multipart/form-data"
              class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5">
            @csrf
            @method('PUT')

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', $setting->is_active) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-[#0F6B3A] focus:ring-[#0F6B3A]">
                <label for="is_active" class="text-sm font-medium text-gray-700">Aktif</label>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Hero</label>
                    <input type="text" name="hero_title" value="{{ old('hero_title', $setting->hero_title) }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
                    @error('hero_title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sumber Hadits</label>
                    <input type="text" name="hadith_source" value="{{ old('hadith_source', $setting->hadith_source) }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
                    @error('hadith_source') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Subjudul Hero</label>
                <textarea name="hero_subtitle" rows="2"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">{{ old('hero_subtitle', $setting->hero_subtitle) }}</textarea>
                @error('hero_subtitle') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Hero / Banner Utama</label>
                @if($setting->hero_image)
                    <div class="mb-3 overflow-hidden rounded-xl border border-gray-200 bg-gray-50">
                        <img src="{{ asset('storage/' . $setting->hero_image) }}" alt="Gambar hero donasi" class="h-48 w-full object-cover">
                    </div>
                    <label class="mb-3 inline-flex items-center gap-2 text-sm text-red-600">
                        <input type="checkbox" name="remove_hero_image" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        Hapus gambar hero
                    </label>
                @else
                    <div class="mb-3 rounded-xl border border-dashed border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                        Belum ada gambar hero. Silakan upload banner Donasi Pendidikan.
                    </div>
                @endif
                <input type="file" name="hero_image" accept="image/jpeg,image/png,image/jpg,image/webp"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
                <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, WebP. Maksimal 2MB.</p>
                @error('hero_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teks Hadits</label>
                <textarea name="hadith_text" rows="3"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">{{ old('hadith_text', $setting->hadith_text) }}</textarea>
                @error('hadith_text') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Pengantar</label>
                <input type="text" name="intro_title" value="{{ old('intro_title', $setting->intro_title) }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
                @error('intro_title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Isi Pengantar</label>
                <textarea name="intro_text" rows="5"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">{{ old('intro_text', $setting->intro_text) }}</textarea>
                @error('intro_text') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Pendukung / Ilustrasi Section</label>
                @if($setting->section_image)
                    <div class="mb-3 overflow-hidden rounded-xl border border-gray-200 bg-gray-50">
                        <img src="{{ asset('storage/' . $setting->section_image) }}" alt="Gambar pendukung donasi" class="h-48 w-full object-cover">
                    </div>
                    <label class="mb-3 inline-flex items-center gap-2 text-sm text-red-600">
                        <input type="checkbox" name="remove_section_image" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        Hapus gambar pendukung
                    </label>
                @endif
                <input type="file" name="section_image" accept="image/jpeg,image/png,image/jpg,image/webp"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
                <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, WebP. Maksimal 2MB.</p>
                @error('section_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bentuk Donasi</label>
                <textarea name="donation_items_text" rows="7"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]"
                          placeholder="Satu item per baris">{{ old('donation_items_text', $donationItemsText) }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Isi satu bentuk donasi per baris.</p>
                @error('donation_items_text') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">QRIS Donasi / Orang Tua Asuh</label>
                @if($setting->donation_qris_image)
                    <div class="mb-3 overflow-hidden rounded-xl border border-gray-200 bg-gray-50">
                        <img src="{{ asset('storage/' . $setting->donation_qris_image) }}" alt="QRIS Donasi" class="h-48 w-full object-contain">
                    </div>
                    <label class="mb-3 inline-flex items-center gap-2 text-sm text-red-600">
                        <input type="checkbox" name="remove_donation_qris_image" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        Hapus QRIS
                    </label>
                @else
                    <div class="mb-3 rounded-xl border border-dashed border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                        QRIS belum diupload. Upload gambar QRIS Merchant resmi.
                    </div>
                @endif
                <input type="file" name="donation_qris_image" accept="image/jpeg,image/png,image/jpg,image/webp"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
                <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, WebP. Maksimal 4MB.</p>
                @error('donation_qris_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teks Ajakan</label>
                <textarea name="invitation_text" rows="4"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">{{ old('invitation_text', $setting->invitation_text) }}</textarea>
                @error('invitation_text') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
                    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $setting->whatsapp_number) }}"
                           placeholder="6289661234569"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
                    <p class="text-xs text-gray-400 mt-1">Boleh ditulis dengan spasi, +, atau strip. Sistem akan menyimpan angka saja.</p>
                    @error('whatsapp_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teks Tombol WhatsApp</label>
                    <input type="text" name="whatsapp_button_text" value="{{ old('whatsapp_button_text', $setting->whatsapp_button_text) }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
                    @error('whatsapp_button_text') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pesan Otomatis WhatsApp</label>
                <textarea name="whatsapp_message" rows="3"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">{{ old('whatsapp_message', $setting->whatsapp_message) }}</textarea>
                @error('whatsapp_message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teks Tombol Share</label>
                <input type="text" name="share_button_text" value="{{ old('share_button_text', $setting->share_button_text) }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
                @error('share_button_text') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pesan Share WhatsApp</label>
                <textarea name="share_message" rows="4"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">{{ old('share_message', $setting->share_message) }}</textarea>
                @error('share_message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-[#0F6B3A] text-white font-medium rounded-lg hover:bg-[#0A4F2B] transition-colors">
                    Simpan
                </button>
                <a href="{{ route('donasi-pendidikan') }}" target="_blank"
                   class="px-6 py-2.5 text-sm font-medium text-[#0F6B3A] hover:text-[#0A4F2B] transition-colors">
                    Lihat Halaman Donasi
                </a>
            </div>
        </form>
    </div>
</x-admin-layout>
