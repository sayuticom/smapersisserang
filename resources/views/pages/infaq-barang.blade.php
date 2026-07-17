@extends('layouts.public')

@php
    $schoolName = $schoolSetting->school_name ?? 'SMA Persis Serang';
    $displayName = $schoolSetting->school_name ?? 'SMA Persis Serang';
    $heroBg = $setting?->hero_image ? asset('storage/' . $setting->hero_image) : null;
    $waNumber = preg_replace('/[^0-9]/', '', $setting?->whatsapp_number ?: '6289661234569');
@endphp

@section('title', 'Infaq Barang - ' . $schoolName)

@section('content')

<section class="bg-[#FBF7EF] py-8 sm:py-12 lg:py-16">
    <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">

        <div class="mb-6 text-center">
            <p class="text-sm font-bold uppercase tracking-[0.24em] text-[#D4A017]">INFAQ BARANG</p>
            <h2 class="mt-2 font-serif text-2xl font-bold text-[#052E1F] sm:text-3xl">Infaq Barang</h2>
            <p class="mt-2 text-sm text-gray-500 sm:text-base">Dukung kebutuhan pendidikan melalui infaq barang yang bermanfaat.</p>
        </div>

        <div x-data="barangForm()" class="space-y-4 sm:space-y-6">
            <div class="rounded-xl border border-amber-200/60 bg-white p-5 shadow-lg shadow-emerald-950/5 sm:rounded-2xl sm:p-6 lg:p-10">
                <p class="mb-4 text-sm text-gray-500">Titipkan donasi barang untuk kebutuhan santri. Konfirmasi langsung via WhatsApp.</p>
                <form @submit.prevent="submitBarang" class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700">Nama Donatur <span class="text-gray-400">(opsional)</span></label>
                        <input type="text" x-model="form.donor_name"
                               class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-[#0F6B3A] focus:ring-2 focus:ring-[#0F6B3A]/20"
                               placeholder="Hamba Allah">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700">Nomor WhatsApp <span class="text-gray-400">(opsional)</span></label>
                        <input type="tel" x-model="form.whatsapp"
                               class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-[#0F6B3A] focus:ring-2 focus:ring-[#0F6B3A]/20"
                               placeholder="08xxxxxxxxxx">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700">Jenis Barang <span class="text-red-500">*</span></label>
                        <select x-model="form.item_type" required
                                class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-[#0F6B3A] focus:ring-2 focus:ring-[#0F6B3A]/20">
                            <option value="">— Pilih —</option>
                            <option value="Beras">Beras</option>
                            <option value="Telur">Telur</option>
                            <option value="Sayuran">Sayuran</option>
                            <option value="Lauk pauk">Lauk pauk</option>
                            <option value="Sembako">Sembako</option>
                            <option value="Perlengkapan sekolah">Perlengkapan sekolah</option>
                            <option value="Perlengkapan asrama">Perlengkapan asrama</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700">Jumlah / Perkiraan <span class="text-gray-400">(opsional)</span></label>
                        <input type="text" x-model="form.quantity"
                               class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-[#0F6B3A] focus:ring-2 focus:ring-[#0F6B3A]/20"
                               placeholder="Contoh: 5 kg, 1 dus, 1 kardus">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700">Cara Penyerahan</label>
                        <select x-model="form.delivery_method"
                                class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-[#0F6B3A] focus:ring-2 focus:ring-[#0F6B3A]/20">
                            <option value="Diantar ke sekolah">Diantar ke sekolah</option>
                            <option value="Dijemput pihak sekolah">Dijemput pihak sekolah</option>
                            <option value="Konsultasi dulu">Konsultasi dulu</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700">Catatan <span class="text-gray-400">(opsional)</span></label>
                        <textarea x-model="form.note" rows="2"
                                  class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-[#0F6B3A] focus:ring-2 focus:ring-[#0F6B3A]/20"
                                  placeholder="Ada yang ingin disampaikan?"></textarea>
                    </div>
                    <button type="submit"
                            class="mt-2 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-amber-400 to-amber-600 px-6 py-3 text-sm font-bold text-emerald-950 shadow-lg shadow-amber-900/20 transition hover:from-amber-300 hover:to-amber-500">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Konfirmasi via WhatsApp
                    </button>
                </form>
                <div x-show="formSubmitted" x-cloak class="mt-3 text-center text-sm text-emerald-600">
                    ✓ Pesan WhatsApp telah terbuka. Silakan kirim.
                </div>
            </div>
        </div>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-400">
                Data dari form publik tidak langsung disimpan. Donasi dicatat setelah admin memverifikasi.
            </p>
        </div>
    </div>
</section>

@push('scripts')
<script>
    function barangForm() {
        return {
            form: {
                donor_name: '',
                whatsapp: '',
                item_type: '',
                quantity: '',
                delivery_method: 'Diantar ke sekolah',
                note: '',
            },
            formSubmitted: false,
            submitBarang() {
                const msg = 'Assalamu\'alaikum, saya ingin berinfaq barang untuk SMA Persis Serang:\n\nNama: ' + (this.form.donor_name || '(tidak diisi)') + '\nWhatsApp: ' + (this.form.whatsapp || '(tidak diisi)') + '\nJenis Barang: ' + this.form.item_type + '\nJumlah/Perkiraan: ' + (this.form.quantity || '(tidak diisi)') + '\nCara Penyerahan: ' + this.form.delivery_method + '\nCatatan: ' + (this.form.note || '(tidak diisi)');

                const waNumber = '{{ $waNumber }}';
                const waUrl = 'https://wa.me/' + waNumber + '?text=' + encodeURIComponent(msg);
                window.open(waUrl, '_blank');
                this.formSubmitted = true;
            }
        };
    }
</script>
@endpush

@endsection
