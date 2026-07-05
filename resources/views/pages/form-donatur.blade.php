@extends('layouts.public')

@php
    $schoolName = $schoolSetting->school_name ?? 'SMA Persis Serang';
    $displayName = $schoolSetting->school_name ?? 'SMA Persis Serang';
    $heroBg = $setting?->hero_image ? asset('storage/' . $setting->hero_image) : null;
    $waNumber = preg_replace('/[^0-9]/', '', $setting?->whatsapp_number ?: '6289661234569');
@endphp

@section('title', 'Form Donatur - ' . $schoolName)

@section('content')

<section class="relative isolate min-h-[170px] overflow-hidden bg-gradient-to-br from-[#052E1F] via-[#0A4F2B] to-[#0F6B3A] sm:min-h-[300px] lg:min-h-[400px]">
    <div class="absolute inset-0 opacity-[0.06]"
         style="background-image: linear-gradient(135deg, rgba(255,255,255,.5) 1px, transparent 1px); background-size: 42px 42px;"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/30 via-transparent to-emerald-950/20"></div>
    @if($heroBg)
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $heroBg }}')"></div>
    @endif
    <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/80 via-emerald-950/60 to-emerald-900/40"></div>
    <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-emerald-950/80 via-emerald-950/35 to-transparent"></div>

    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-[170px] flex-col justify-center pb-7 pt-20 sm:min-h-[300px] sm:pb-12 sm:pt-28 lg:min-h-[400px] lg:pb-16 lg:pt-32">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-1.5 rounded-full border border-amber-300/70 bg-emerald-950/60 px-3 py-1.5 text-[11px] font-semibold text-amber-300 shadow-lg shadow-emerald-950/20 backdrop-blur sm:gap-2 sm:px-4 sm:py-2 sm:text-sm">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-300 sm:h-2 sm:w-2"></span>
                    FORM DONATUR
                </div>
                <h1 class="mt-3 font-serif text-3xl font-bold leading-tight text-white sm:mt-6 sm:text-5xl lg:text-6xl">
                    Form Donasi
                </h1>
                <p class="mt-2 hidden max-w-2xl text-lg font-semibold text-amber-300 sm:mt-4 sm:block sm:text-xl">
                    {{ $displayName }}
                </p>
        </div>
    </div>
    </div>
</section>

<section class="bg-[#FBF7EF] py-5 sm:py-16 lg:py-20">
    <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div x-data="qrisPreview()" x-init="init()" class="space-y-4 sm:space-y-6">

        <div class="rounded-xl border border-amber-200/60 bg-white p-5 shadow-lg shadow-emerald-950/5 sm:rounded-2xl sm:p-6 lg:p-10">
            <div class="text-center">
                <p class="hidden text-sm font-bold uppercase tracking-[0.24em] text-[#D4A017] sm:block">DONASI</p>
                <h2 class="font-serif text-xl font-bold text-[#052E1F] sm:mt-3 sm:text-2xl lg:text-3xl">Isi Data Donatur</h2>
                <p class="mt-1 text-xs leading-relaxed text-gray-500 sm:mt-2 sm:text-sm">Pilih nominal, QRIS muncul otomatis, kemudian Download, setelah itu scan menggunakan aplikasi mobile bank anda.</p>
            </div>

            <form method="POST" action="{{ route('donasi-pendidikan.form-donatur.submit') }}" class="mt-5 space-y-4 sm:mt-8 sm:space-y-6" x-on:submit.prevent="openWhatsapp">
                @csrf

                <input type="text" name="website_url" class="hidden" tabindex="-1" autocomplete="off">
                <input type="hidden" name="form_started_at" x-ref="formStartedAt" value="">

                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700">Nama Donatur <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                           x-model="form.name"
                           placeholder="Hamba Allah"
                           class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-[#0F6B3A] focus:ring-2 focus:ring-[#0F6B3A]/20 sm:mt-1.5 sm:py-3">
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="rounded-xl border border-gray-100 bg-gray-50/60 p-3 sm:bg-transparent sm:p-0 sm:border-0">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="allow_future_donation_contact" value="1"
                               x-model="form.allowWA"
                               class="mt-0.5 rounded border-gray-300 text-[#0F6B3A] focus:ring-[#0F6B3A]">
                        <span class="text-xs text-gray-600 leading-relaxed sm:text-sm">
                            Saya bersedia dihubungi melalui WhatsApp untuk informasi donasi berikutnya.
                        </span>
                    </label>

                    <div x-show="form.allowWA" x-cloak class="mt-3">
                        <label for="whatsapp" class="block text-sm font-semibold text-gray-700">Nomor WhatsApp</label>
                        <input type="tel" id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}"
                               x-model="form.whatsapp"
                               x-on:input="form.whatsapp = form.whatsapp.replace(/[^0-9+\-\s]/g, '').slice(0, 30)"
                               placeholder="Contoh: 0877712621100"
                               class="mt-1.5 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-[#0F6B3A] focus:ring-2 focus:ring-[#0F6B3A]/20 sm:py-3">
                        @error('whatsapp') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <span class="block text-sm font-semibold text-gray-700">Nominal Dukungan <span class="text-red-500">*</span></span>
                    <div class="mt-2 grid grid-cols-2 gap-2 sm:gap-3 sm:grid-cols-4">
                        <template x-for="(opt, i) in [
                            { value: '25000', label: 'Rp25.000' },
                            { value: '50000', label: 'Rp50.000' },
                            { value: '100000', label: 'Rp100.000' },
                            { value: '250000', label: 'Rp250.000' },
                            { value: '500000', label: 'Rp500.000' },
                        ]" :key="i">
                            <label class="flex min-h-11 cursor-pointer items-center justify-center rounded-xl border px-3 py-2 text-xs font-semibold transition sm:min-h-0 sm:p-3 sm:text-sm"
                                   :class="form.amount === opt.value ? 'border-[#0F6B3A] bg-[#0F6B3A]/10 text-[#0F6B3A]' : 'border-gray-200 bg-white text-gray-700 hover:border-[#0F6B3A]/40'">
                                <input type="radio" name="amount" :value="opt.value" x-model="form.amount" class="sr-only">
                                <span x-text="opt.label"></span>
                            </label>
                        </template>
                        <label class="flex min-h-11 cursor-pointer items-center justify-center rounded-xl border px-3 py-2 text-xs font-semibold transition sm:min-h-0 sm:p-3 sm:text-sm"
                               :class="form.amount === 'lainnya' ? 'border-[#0F6B3A] bg-[#0F6B3A]/10 text-[#0F6B3A]' : 'border-gray-200 bg-white text-gray-700 hover:border-[#0F6B3A]/40'">
                            <input type="radio" name="amount" value="lainnya" x-model="form.amount" class="sr-only">
                            Lainnya
                        </label>
                    </div>
                    <div x-show="form.amount === 'lainnya'" x-transition class="mt-3">
                        <label for="custom_amount" class="block text-sm font-medium text-gray-600">Nominal lainnya (Rp)</label>
                        <div class="relative mt-1">
                            <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-400">Rp</span>
                            <input type="text" id="custom_amount" name="custom_amount"
                                   x-model="form.customAmount"
                                   inputmode="numeric"
                                   class="w-full rounded-xl border border-gray-300 py-2.5 pl-10 pr-4 text-sm focus:border-[#0F6B3A] focus:ring-2 focus:ring-[#0F6B3A]/20 sm:py-3"
                                   placeholder="0">
                        </div>
                    </div>
                    @error('amount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    @error('custom_amount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div x-show="isLoading" class="flex items-center justify-center gap-3 rounded-xl border border-amber-200 bg-amber-50/50 p-4 text-sm text-amber-700 sm:p-6">
                    <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span>Menampilkan QRIS...</span>
                </div>

                <div x-show="!isLoading && !previewData && !qrisError && !hasValidAmount()" x-transition class="rounded-xl border border-dashed border-amber-300 bg-amber-50/50 p-4 text-center text-sm text-amber-700 sm:p-6">
                    <span class="sm:hidden">Pilih nominal untuk menampilkan QRIS.</span>
                    <span class="hidden sm:inline">Pilih nominal donasi untuk menampilkan QRIS dan total pembayaran.</span>
                </div>

                <div x-show="qrisError" class="rounded-xl border border-red-100 bg-red-50 p-4 text-sm text-red-700">
                    <p x-text="qrisError"></p>
                </div>

                <div x-show="!qrisError && previewData" class="rounded-xl border border-emerald-100 bg-emerald-50/50 p-4 space-y-4 sm:p-5 sm:space-y-5">
                    <div class="rounded-xl border border-amber-200 bg-white p-3 text-center shadow-sm sm:p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-amber-600">Total yang harus dibayar</p>
                        <p class="mt-1 text-3xl font-black text-[#052E1F] sm:mt-2 sm:text-4xl" x-text="previewData.total_transfer_formatted"></p>
                    </div>

                    <div class="grid grid-cols-3 gap-2 text-xs sm:gap-3 sm:text-sm">
                        <div class="rounded-lg bg-white p-2 sm:p-3">
                            <p class="text-xs font-medium text-gray-500">Nominal Donasi</p>
                            <p class="mt-1 font-bold text-[#0F6B3A]" x-text="previewData.amount_formatted"></p>
                        </div>
                        <div class="rounded-lg bg-white p-2 sm:p-3">
                            <p class="text-xs font-medium text-gray-500">Kode Unik</p>
                            <p class="mt-1 font-bold text-[#052E1F]" x-text="previewData.unique_code"></p>
                        </div>
                        <div class="rounded-lg bg-white p-2 sm:p-3">
                            <p class="text-xs font-medium text-gray-500">Nama Donatur</p>
                            <p class="mt-1 font-bold text-[#052E1F]" x-text="previewData.summary.donor_name"></p>
                        </div>
                    </div>

                    <div class="text-center">
                        <template x-if="previewData.qris_image">
                            <div class="mx-auto max-w-[240px] sm:max-w-xs">
                                <div class="mb-2 text-center leading-snug sm:mb-3">
                                    <div class="text-sm font-bold text-gray-900" x-text="previewData.merchant_name || 'SMA PERSIS SERANG, CURUG'"></div>
                                    <div class="text-xs text-gray-400" x-text="previewData.merchant_city || 'SERANG'"></div>
                                </div>
                                <img :src="previewData.qris_image"
                                     alt="QRIS Donasi"
                                     class="w-full rounded-2xl border bg-white p-2.5 shadow-lg sm:p-3">
                            </div>
                        </template>

                        <div x-show="previewData.static_fallback" class="mt-3 rounded-xl border border-amber-100 bg-amber-50/50 p-3 text-xs text-gray-600 sm:text-sm">
                            QRIS ini bersifat manual. Mohon masukkan total pembayaran sesuai angka di atas.
                        </div>

                        <div class="mt-3 flex justify-center sm:mt-4">
                            <a :href="'/donasi-pendidikan/qris/download?amount=' + previewData.amount_raw"
                               class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-amber-300 bg-white px-3 py-2 text-xs font-semibold text-emerald-900 transition hover:bg-amber-50">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Download QRIS
                            </a>
                        </div>
                    </div>

                    <div class="rounded-xl border border-amber-100 bg-amber-50/50 p-3 text-xs leading-5 text-gray-700 sm:p-4 sm:text-sm sm:leading-6">
                        <p class="font-bold text-[#052E1F]">Penting:</p>
                        <p class="sm:hidden">Bayar sesuai total sampai 3 digit terakhir, lalu konfirmasi via WhatsApp.</p>
                        <p class="hidden sm:block">Mohon bayar sesuai nominal sampai 3 digit terakhir agar donasi mudah diverifikasi.</p>
                        <p class="hidden sm:block">Mohon masukkan nominal pembayaran sesuai angka di atas, termasuk 3 digit kode unik di belakang. Contoh: bukan Rp50.000, tetapi Rp50.127.</p>
                        <p class="hidden sm:block">Setelah transfer, klik tombol <strong>Konfirmasi via WhatsApp</strong>.</p>
                        <p x-show="previewData.is_dynamic" class="hidden sm:block">Jika membuka halaman ini dari HP yang sama dengan aplikasi pembayaran, <strong>Download QRIS</strong> lalu upload di mobile banking / e-wallet.</p>
                    </div>
                </div>

                <div>
                    <button type="button"
                            class="text-sm font-semibold text-[#0F6B3A] sm:hidden"
                            x-on:click="showNote = !showNote"
                            x-text="showNote ? 'Sembunyikan catatan' : 'Tambah catatan'"></button>
                    <div x-show="showNote" x-cloak class="mt-2 sm:hidden">
                        <label for="note" class="block text-sm font-semibold text-gray-700">Catatan <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <textarea id="note" name="note" rows="3"
                                  x-model="form.note"
                                  class="mt-1.5 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-[#0F6B3A] focus:ring-2 focus:ring-[#0F6B3A]/20 sm:py-3"
                                  placeholder="Contoh: Donasi atas nama keluarga, atau pesan tambahan...">{{ old('note') }}</textarea>
                    </div>
                    <div class="hidden sm:block">
                        <label for="note_desktop" class="block text-sm font-semibold text-gray-700">Catatan <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <textarea id="note_desktop" rows="3"
                                  x-model="form.note"
                                  class="mt-1.5 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-[#0F6B3A] focus:ring-2 focus:ring-[#0F6B3A]/20"
                                  placeholder="Contoh: Donasi atas nama keluarga, atau pesan tambahan..."></textarea>
                    </div>
                    @error('note') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div x-show="formError" x-cloak class="rounded-xl border border-red-100 bg-red-50 p-4 text-sm text-red-700">
                    <p x-text="formError"></p>
                </div>

                <div class="pt-1 flex flex-col gap-2 sm:pt-2 sm:flex-row sm:gap-3">
                    <a href="{{ route('donasi-pendidikan') }}"
                       class="inline-flex w-full items-center justify-center rounded-xl border border-gray-300 bg-white px-7 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 sm:py-3.5">
                        Kembali
                    </a>
                    <button type="submit"
                            :disabled="!previewData"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-amber-400 to-amber-600 px-7 py-3 text-sm font-bold text-emerald-950 shadow-lg shadow-amber-900/20 transition hover:from-amber-300 hover:to-amber-500 disabled:cursor-not-allowed disabled:opacity-60 sm:py-3.5">
                        Konfirmasi via WhatsApp
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-400">
                Data dari form publik tidak langsung disimpan. Donasi dicatat setelah admin memverifikasi pembayaran.
            </p>
        </div>
    </div>
    </div>
</section>

@push('scripts')
<script>
    function qrisPreview() {
        return {
            form: {
                name: '{{ old('name') }}',
                whatsapp: '{{ old('whatsapp') }}',
                amount: '{{ old('amount', '') }}',
                customAmount: '{{ old('custom_amount', '') }}',
                note: '{{ old('note') }}',
                allowWA: {{ old('allow_future_donation_contact', '1') ? 'true' : 'false' }},
            },
            isLoading: false,
            qrisError: null,
            previewData: null,
            customDebounce: null,
            formError: '',
            uniqueCode: null,
            showNote: @json(filled(old('note'))),

            init() {
                this.$refs.formStartedAt.value = new Date().toISOString();

                this.$watch('form.amount', () => {
                    this.previewData = null;
                    this.formError = '';
                    this.uniqueCode = this.generateUniqueCode();
                    if (this.form.amount && this.form.amount !== 'lainnya') {
                        this.previewQris();
                    }
                });

                this.$watch('form.customAmount', () => {
                    this.previewData = null;
                    this.formError = '';
                    if (this.form.amount === 'lainnya') {
                        if (this.customDebounce) clearTimeout(this.customDebounce);
                        this.customDebounce = setTimeout(() => this.previewQris(), 400);
                    }
                });

                this.$watch('form.name', () => this.refreshMessagePreview());
                this.$watch('form.note', () => this.refreshMessagePreview());
                this.$watch('form.whatsapp', () => this.refreshMessagePreview());
                this.$watch('form.allowWA', () => this.refreshMessagePreview());
            },

            openWhatsapp() {
                this.formError = '';

                if (!this.hasValidAmount()) {
                    this.formError = 'Silakan pilih nominal donasi terlebih dahulu.';
                    return;
                }

                if (!this.previewData) {
                    this.previewQris();
                    this.formError = 'Tunggu sampai QRIS dan total pembayaran tampil.';
                    return;
                }

                window.open(this.previewData.whatsapp_url, '_blank', 'noopener');
            },

            get computedAmount() {
                if (this.form.amount === 'lainnya') {
                    return this.form.customAmount.replace(/[\.\,]/g, '');
                }
                return this.form.amount;
            },

            hasValidAmount() {
                if (this.form.amount && this.form.amount !== 'lainnya') {
                    return parseInt(this.form.amount) >= 10000;
                }

                if (this.form.amount === 'lainnya') {
                    return parseInt((this.form.customAmount || '').replace(/[\.\,]/g, '') || 0) >= 10000;
                }

                return false;
            },

            generateUniqueCode() {
                return Math.floor(Math.random() * 999) + 1;
            },

            refreshMessagePreview() {
                if (!this.previewData || !this.hasValidAmount()) return;
                if (this.customDebounce) clearTimeout(this.customDebounce);
                this.customDebounce = setTimeout(() => this.previewQris(false), 250);
            },

            previewQris(showLoading = true) {
                if (!this.hasValidAmount()) {
                    this.isLoading = false;
                    this.previewData = null;
                    this.qrisError = null;
                    return;
                }

                if (!this.uniqueCode) {
                    this.uniqueCode = this.generateUniqueCode();
                }

                this.isLoading = showLoading;
                this.qrisError = null;
                if (showLoading) {
                    this.previewData = null;
                }

                const payload = new FormData();
                payload.append('donor_name', this.form.name);
                payload.append('donor_whatsapp', this.form.allowWA ? this.form.whatsapp : '');
                payload.append('amount', this.form.amount);
                payload.append('custom_amount', this.form.customAmount);
                payload.append('unique_code', this.uniqueCode);
                payload.append('note', this.form.note);
                payload.append('allow_future_donation_contact', this.form.allowWA ? '1' : '0');

                fetch('{{ route('donasi-pendidikan.qris.preview') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: payload,
                })
                .then(res => res.json())
                .then(data => {
                    this.isLoading = false;

                    if (data.success) {
                        this.previewData = data;
                        this.qrisError = null;
                    } else {
                        this.previewData = null;
                        this.qrisError = data.message || 'QRIS belum tersedia.';
                    }
                })
                .catch(err => {
                    this.isLoading = false;
                    this.previewData = null;
                    this.qrisError = 'Terjadi kesalahan. Silakan coba lagi.';
                });
            }
        };
    }
</script>
@endpush

@endsection
