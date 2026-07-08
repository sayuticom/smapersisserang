@php
    $title = 'Pengaturan Wakaf Uang';
@endphp
<x-admin-layout>
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pengaturan Wakaf Uang</h1>
            <p class="mt-1 text-sm text-slate-500">Atur konten halaman wakaf uang publik.</p>
        </div>
        <a href="{{ route('wakaf-uang.index') }}" target="_blank"
           class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            Lihat Halaman Publik
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.wakaf.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">Hero Section</h2>

            <div class="mt-5 grid gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Judul Hero</label>
                    <input type="text" name="hero_title" value="{{ old('hero_title', $setting->hero_title) }}"
                           class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Subtitle Hero</label>
                    <textarea name="hero_subtitle" rows="2" class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">{{ old('hero_subtitle', $setting->hero_subtitle) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Gambar Hero</label>
                    @if($setting->hero_image)
                        <div class="mt-2 mb-2">
                            <img src="{{ asset('storage/' . $setting->hero_image) }}" class="h-32 rounded-lg border border-slate-200 object-cover">
                            <label class="mt-2 inline-flex items-center gap-2 text-xs text-red-600 cursor-pointer">
                                <input type="checkbox" name="remove_hero_image" value="1">
                                Hapus gambar
                            </label>
                        </div>
                    @endif
                    <input type="file" name="hero_image" accept="image/*"
                           class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">Konten Wakaf</h2>

            <div class="mt-5 grid gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Judul Intro</label>
                    <input type="text" name="intro_title" value="{{ old('intro_title', $setting->intro_title) }}"
                           class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Teks Intro</label>
                    <textarea name="intro_text" rows="4" class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">{{ old('intro_text', $setting->intro_text) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Teks Tujuan Wakaf</label>
                    <textarea name="waqf_purpose_text" rows="4" class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">{{ old('waqf_purpose_text', $setting->waqf_purpose_text) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Teks Ikrar Wakaf</label>
                    <textarea name="ikrar_text" rows="4" class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">{{ old('ikrar_text', $setting->ikrar_text) }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">QRIS</h2>

            <div class="mt-5 grid gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Gambar QRIS <span class="text-xs font-normal text-slate-400">(upload gambar QRIS statis)</span></label>
                    @if($setting->qris_image)
                        <div class="mt-2 mb-2">
                            <img src="{{ asset('storage/' . $setting->qris_image) }}" class="h-32 rounded-lg border border-slate-200">
                            <label class="mt-2 inline-flex items-center gap-2 text-xs text-red-600 cursor-pointer">
                                <input type="checkbox" name="remove_qris_image" value="1">
                                Hapus gambar
                            </label>
                        </div>
                    @endif
                    <input type="file" name="qris_image" accept="image/*"
                           class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700">QRIS Payload <span class="text-xs font-normal text-slate-400">(untuk QRIS dinamis)</span></label>
                    <textarea name="qris_payload" rows="3" class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">{{ old('qris_payload', $setting->qris_payload) }}</textarea>
                    <p class="mt-1 text-xs text-slate-400">Tempelkan raw string QRIS merchant untuk mengaktifkan nominal otomatis.</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">WhatsApp</h2>

            <div class="mt-5 grid gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Nomor WhatsApp Admin</label>
                    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $setting->whatsapp_number) }}"
                           placeholder="628xxxxxxxxx"
                           class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Template Pesan WhatsApp <span class="text-xs font-normal text-slate-400">(pesan awal saat klik tombol WA)</span></label>
                    <textarea name="whatsapp_message_template" rows="3" class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">{{ old('whatsapp_message_template', $setting->whatsapp_message_template) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                    class="rounded-xl bg-emerald-600 px-6 py-3 text-sm font-bold text-white shadow-sm hover:bg-emerald-500">
                Simpan Pengaturan
            </button>
            <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ $setting->is_active ? 'checked' : '' }}>
                Aktifkan halaman wakaf
            </label>
        </div>
    </form>

    @if($setting->qris_payload)
    <div class="mt-8 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900">Preview QRIS Dinamis</h2>
        <p class="mt-1 text-xs text-slate-400">Hasil generate QRIS dengan nominal Rp25.000 akan tampil seperti ini:</p>
        <div class="mt-4">
            <img src="{{ route('wakaf-uang.qris.download', ['amount' => 25000]) }}" alt="Preview QRIS" class="w-48 h-48 rounded-xl border border-slate-200 shadow-sm">
        </div>
    </div>
    @endif
</div>
</x-admin-layout>
