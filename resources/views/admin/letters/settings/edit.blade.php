<x-admin-layout>
    <div class="mx-auto max-w-3xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Pengaturan Surat</h2>
                <p class="mt-1 text-sm text-gray-500">Atur default yang akan digunakan saat membuat surat baru.</p>
            </div>
            <a href="{{ route('admin.letters.outgoings.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                Kembali
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.letters.settings.update') }}" enctype="multipart/form-data" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="default_letter_classification_code" class="block text-sm font-semibold text-gray-700">Kode Klasifikasi Surat Default</label>
                    <input type="text" name="default_letter_classification_code" id="default_letter_classification_code"
                           value="{{ old('default_letter_classification_code', $settings?->default_letter_classification_code ?? '421.3') }}"
                           placeholder="421.3"
                           class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div>
                    <label for="default_letter_school_code" class="block text-sm font-semibold text-gray-700">Kode Sekolah Default</label>
                    <input type="text" name="default_letter_school_code" id="default_letter_school_code"
                           value="{{ old('default_letter_school_code', $settings?->default_letter_school_code ?? 'SMA-PERSIS-SRG') }}"
                           placeholder="SMA-PERSIS-SRG"
                           class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div>
                    <label for="default_letter_pdf_font_size" class="block text-sm font-semibold text-gray-700">Ukuran Huruf PDF Default</label>
                    <select name="default_letter_pdf_font_size" id="default_letter_pdf_font_size" class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="9" {{ (string) old('default_letter_pdf_font_size', $settings?->default_letter_pdf_font_size ?? 11) === '9' ? 'selected' : '' }}>9 &ndash; Ekstra Kecil</option>
                        <option value="10" {{ (string) old('default_letter_pdf_font_size', $settings?->default_letter_pdf_font_size ?? 11) === '10' ? 'selected' : '' }}>10 &ndash; Kecil</option>
                        <option value="11" {{ (string) old('default_letter_pdf_font_size', $settings?->default_letter_pdf_font_size ?? 11) === '11' ? 'selected' : '' }}>11 &ndash; Normal</option>
                        <option value="12" {{ (string) old('default_letter_pdf_font_size', $settings?->default_letter_pdf_font_size ?? 11) === '12' ? 'selected' : '' }}>12 &ndash; Besar</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 border-t border-slate-100 pt-5">
                <h3 class="mb-3 font-semibold text-slate-900">Basmallah & Doa Penutup</h3>
                <div class="space-y-6">
                    <div x-data="{ showBasmallah: {{ old('default_letter_show_basmallah', $settings?->default_letter_show_basmallah ?? true) ? 'true' : 'false' }} }">
                        <label class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700">
                            <input type="checkbox" name="default_letter_show_basmallah" value="1" x-model="showBasmallah"
                                   class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            Tampilkan Basmallah Default
                        </label>
                        <div x-show="showBasmallah" x-cloak x-transition class="pl-6 mt-1 space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">Teks Basmallah Default</label>
                                <input type="text" name="default_letter_basmallah_text"
                                       value="{{ old('default_letter_basmallah_text', $settings?->default_letter_basmallah_text ?? 'بسم الله الرحمن الرحيم') }}"
                                       class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Gambar Basmallah Default</label>
                                @if($settings?->basmallah_image_path)
                                    <div class="mb-2">
                                        <img src="{{ Storage::disk('public')->url($settings->basmallah_image_path) }}"
                                             alt="Basmallah"
                                             class="max-h-10 object-contain">
                                    </div>
                                @endif
                                <input type="file" name="basmallah_image" accept="image/png,image/jpeg"
                                       class="block w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100">
                                <p class="mt-1 text-xs text-slate-400">PNG/JPG transparan. Maks 1 MB.</p>
                                @if($settings?->basmallah_image_path)
                                    <label class="inline-flex items-center gap-2 mt-2 text-xs text-red-600">
                                        <input type="checkbox" name="delete_basmallah_image" value="1"
                                               class="rounded border-slate-300 text-red-500 focus:ring-red-400">
                                        Hapus gambar Basmallah
                                    </label>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div x-data="{ showClosingDua: {{ old('default_letter_show_closing_dua', $settings?->default_letter_show_closing_dua ?? true) ? 'true' : 'false' }} }">
                        <label class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700">
                            <input type="checkbox" name="default_letter_show_closing_dua" value="1" x-model="showClosingDua"
                                   class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            Tampilkan Doa Penutup Default
                        </label>
                        <div x-show="showClosingDua" x-cloak x-transition class="pl-6 mt-1 space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">Teks Doa Penutup Default</label>
                                <input type="text" name="default_letter_closing_dua_text"
                                       value="{{ old('default_letter_closing_dua_text', $settings?->default_letter_closing_dua_text ?? 'اَللَّهُمَّ يَأْخُذُ بِأَيْدِيْنَا إِلَى مَا فِيْهِ خَيْرٌ لِلْإِسْلَامِ وَالْمُسْلِمِيْنَ') }}"
                                       class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Gambar Doa Penutup Default</label>
                                @if($settings?->closing_dua_image_path)
                                    <div class="mb-2">
                                        <img src="{{ Storage::disk('public')->url($settings->closing_dua_image_path) }}"
                                             alt="Doa Penutup"
                                             class="max-h-12 object-contain">
                                    </div>
                                @endif
                                <input type="file" name="closing_dua_image" accept="image/png,image/jpeg"
                                       class="block w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100">
                                <p class="mt-1 text-xs text-slate-400">PNG/JPG transparan. Maks 1 MB.</p>
                                @if($settings?->closing_dua_image_path)
                                    <label class="inline-flex items-center gap-2 mt-2 text-xs text-red-600">
                                        <input type="checkbox" name="delete_closing_dua_image" value="1"
                                               class="rounded border-slate-300 text-red-500 focus:ring-red-400">
                                        Hapus gambar Doa Penutup
                                    </label>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end border-t border-slate-100 pt-5">
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
