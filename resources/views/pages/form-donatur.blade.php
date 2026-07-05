@extends('layouts.public')

@php
    $schoolName = $schoolSetting->school_name ?? 'SMA Persis Serang';
    $displayName = $schoolSetting->school_name ?? 'SMA Persis Serang';
    $heroBg = $setting?->hero_image ? asset('storage/' . $setting->hero_image) : null;
    $waNumber = preg_replace('/[^0-9]/', '', $setting?->whatsapp_number ?: '6289661234569');
@endphp

@section('title', 'Form Donatur - ' . $schoolName)

@section('content')

<section class="relative isolate min-h-[300px] overflow-hidden bg-gradient-to-br from-[#052E1F] via-[#0A4F2B] to-[#0F6B3A] lg:min-h-[400px]">
    <div class="absolute inset-0 opacity-[0.06]"
         style="background-image: linear-gradient(135deg, rgba(255,255,255,.5) 1px, transparent 1px); background-size: 42px 42px;"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/30 via-transparent to-emerald-950/20"></div>
    @if($heroBg)
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $heroBg }}')"></div>
    @endif
    <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/80 via-emerald-950/60 to-emerald-900/40"></div>
    <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-emerald-950/80 via-emerald-950/35 to-transparent"></div>

    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-[300px] flex-col justify-center pb-12 pt-28 lg:min-h-[400px] lg:pb-16 lg:pt-32">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-amber-300/70 bg-emerald-950/60 px-4 py-2 text-sm font-semibold text-amber-300 shadow-lg shadow-emerald-950/20 backdrop-blur">
                    <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                    FORM DONATUR
                </div>
                <h1 class="mt-6 font-serif text-4xl font-bold leading-tight text-white sm:text-5xl lg:text-6xl">
                    Form Donasi
                </h1>
                <p class="mt-4 max-w-2xl text-lg font-semibold text-amber-300 sm:text-xl">
                    {{ $displayName }}
                </p>
        </div>
    </div>
    </div>
</section>

<section class="bg-[#FBF7EF] py-16 lg:py-20">
    <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div x-data="qrisPreview()" x-init="init()" class="space-y-6">

        <div class="rounded-2xl border border-amber-200/60 bg-white p-6 shadow-lg shadow-emerald-950/5 lg:p-10">
            <div class="text-center">
                <p class="text-sm font-bold uppercase tracking-[0.24em] text-[#D4A017]">DONASI</p>
                <h2 class="mt-3 font-serif text-2xl font-bold text-[#052E1F] lg:text-3xl">Isi Data Donatur</h2>
                <p class="mt-2 text-sm leading-relaxed text-gray-500">Pilih nominal donasi, QRIS akan muncul otomatis.</p>
            </div>

            <form method="POST" action="{{ route('donasi-pendidikan.form-donatur.submit') }}" class="mt-8 space-y-6" x-on:submit.prevent="handleSubmit">
                @csrf

                <input type="text" name="website_url" class="hidden" tabindex="-1" autocomplete="off">
                <input type="hidden" name="form_started_at" x-ref="formStartedAt" value="">

                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700">Nama Donatur <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                           x-model="form.name"
                           placeholder="Hamba Allah"
                           class="mt-1.5 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-[#0F6B3A] focus:ring-2 focus:ring-[#0F6B3A]/20">
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>



                <div>
                    <span class="block text-sm font-semibold text-gray-700">Nominal Dukungan <span class="text-red-500">*</span></span>
                    <div class="mt-2 grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <template x-for="(opt, i) in [
                            { value: '25000', label: 'Rp25.000' },
                            { value: '50000', label: 'Rp50.000' },
                            { value: '100000', label: 'Rp100.000' },
                            { value: '250000', label: 'Rp250.000' },
                            { value: '500000', label: 'Rp500.000' },
                        ]" :key="i">
                            <label class="flex cursor-pointer items-center justify-center rounded-xl border p-3 text-sm font-semibold transition"
                                   :class="form.amount === opt.value ? 'border-[#0F6B3A] bg-[#0F6B3A]/10 text-[#0F6B3A]' : 'border-gray-200 bg-white text-gray-700 hover:border-[#0F6B3A]/40'">
                                <input type="radio" name="amount" :value="opt.value" x-model="form.amount" class="sr-only">
                                <span x-text="opt.label"></span>
                            </label>
                        </template>
                        <label class="flex cursor-pointer items-center justify-center rounded-xl border p-3 text-sm font-semibold transition"
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
                                   class="w-full rounded-xl border border-gray-300 py-3 pl-10 pr-4 text-sm focus:border-[#0F6B3A] focus:ring-2 focus:ring-[#0F6B3A]/20"
                                   placeholder="0">
                        </div>
                    </div>
                    @error('amount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    @error('custom_amount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div x-show="isLoading" class="flex items-center justify-center gap-3 rounded-xl border border-amber-200 bg-amber-50/50 p-6 text-sm text-amber-700">
                    <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span>Menampilkan QRIS...</span>
                </div>

                <div x-show="!isLoading && !previewData && !qrisError && !hasValidAmount()" x-transition class="rounded-xl border border-dashed border-amber-300 bg-amber-50/50 p-6 text-center text-sm text-amber-700">
                    Pilih nominal donasi untuk menampilkan QRIS.
                </div>

                <div x-show="qrisError" class="rounded-xl border border-red-100 bg-red-50 p-4 text-sm text-red-700">
                    <p x-text="qrisError"></p>
                </div>

                <div x-show="!qrisError && previewData" class="rounded-xl border border-emerald-100 bg-emerald-50/50 p-5 space-y-5">
                    <table class="w-full text-sm">
                        <tr>
                            <td class="py-1 pr-4 font-medium text-gray-500">Nama</td>
                            <td class="py-1 font-semibold text-[#052E1F]" x-text="previewData.summary.donor_name"></td>
                        </tr>
                        <tr>
                            <td class="py-1 pr-4 font-medium text-gray-500">Nominal</td>
                            <td class="py-1 font-semibold text-[#0F6B3A]" x-text="previewData.amount_formatted"></td>
                        </tr>
                    </table>

                    <div class="text-center">
                        <template x-if="previewData.qris_image">
                            <div class="mx-auto max-w-xs">
                                <div class="mb-3 text-center leading-snug">
                                    <div class="text-sm font-bold text-gray-900" x-text="previewData.merchant_name || 'SMA PERSIS SERANG, CURUG'"></div>
                                    <div class="text-xs text-gray-400" x-text="previewData.merchant_city || 'SERANG'"></div>
                                </div>
                                <img :src="previewData.qris_image"
                                     alt="QRIS Donasi"
                                     class="w-full rounded-2xl border bg-white p-3 shadow-lg">
                            </div>
                        </template>

                        <template x-if="previewData.qris_image && previewData.static_fallback">
                            <div>
                                <div class="mx-auto max-w-xs">
                                    <div class="mb-3 text-center leading-snug">
                                        <div class="text-sm font-bold text-gray-900" x-text="previewData.merchant_name || 'SMA PERSIS SERANG, CURUG'"></div>
                                        <div class="text-xs text-gray-400" x-text="previewData.merchant_city || 'SERANG'"></div>
                                    </div>
                                    <img :src="previewData.qris_image"
                                         alt="QRIS Donasi"
                                         class="w-full rounded-2xl border bg-white p-3 shadow-lg">
                                </div>
                                <div class="mt-3 rounded-xl border border-amber-100 bg-amber-50/50 p-3 text-sm text-gray-600">
                                    Nominal belum otomatis. Silakan masukkan nominal secara manual di aplikasi pembayaran.
                                </div>
                            </div>
                        </template>

                        <div class="mt-4 flex flex-wrap justify-center gap-3">
                            <template x-if="previewData.is_dynamic">
                                <a :href="'/donasi-pendidikan/qris/download?amount=' + previewData.amount_raw"
                                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-yellow-400 to-amber-500 px-5 py-2.5 text-sm font-bold text-emerald-950 shadow-lg shadow-amber-900/20 transition hover:from-yellow-300 hover:to-amber-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Download QRIS
                                </a>
                            </template>
                            <template x-if="!previewData.is_dynamic && previewData.qris_image">
                                <a :href="previewData.qris_image" download
                                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-yellow-400 to-amber-500 px-5 py-2.5 text-sm font-bold text-emerald-950 shadow-lg shadow-amber-900/20 transition hover:from-yellow-300 hover:to-amber-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Download QRIS
                                </a>
                            </template>
                            <a :href="previewData.whatsapp_url" target="_blank" rel="noopener"
                               class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-amber-400 to-amber-600 px-5 py-2.5 text-sm font-bold text-emerald-950 shadow-lg shadow-amber-900/20 transition hover:from-amber-300 hover:to-amber-500">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                Konfirmasi via WhatsApp
                            </a>
                        </div>
                    </div>

                    <div x-show="previewData.is_dynamic" class="rounded-xl border border-amber-100 bg-amber-50/50 p-3 text-sm leading-6 text-gray-600">
                        <p>Jika membuka halaman ini dari HP yang sama dengan aplikasi pembayaran, <strong>Download QRIS</strong> lalu upload di mobile banking / e-wallet.</p>
                    </div>
                </div>

                <div>
                    <label for="note" class="block text-sm font-semibold text-gray-700">Catatan <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <textarea id="note" name="note" rows="3"
                              x-model="form.note"
                              class="mt-1.5 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-[#0F6B3A] focus:ring-2 focus:ring-[#0F6B3A]/20"
                              placeholder="Contoh: Donasi atas nama keluarga, atau pesan tambahan...">{{ old('note') }}</textarea>
                    @error('note') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="allow_future_donation_contact" value="1"
                           x-model="form.allowWA"
                           class="mt-0.5 rounded border-gray-300 text-[#0F6B3A] focus:ring-[#0F6B3A]">
                    <span class="text-sm text-gray-600 leading-relaxed">
                        Saya bersedia dihubungi melalui WhatsApp untuk informasi donasi berikutnya.
                    </span>
                </label>

                <div x-show="form.allowWA" x-cloak>
                    <label for="whatsapp" class="block text-sm font-semibold text-gray-700">Nomor WhatsApp</label>
                    <input type="tel" id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}"
                           x-model="form.whatsapp"
                           placeholder="08xxxxxxxxxx"
                           class="mt-1.5 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-[#0F6B3A] focus:ring-2 focus:ring-[#0F6B3A]/20">
                    @error('whatsapp') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div x-show="formError" x-cloak class="rounded-xl border border-red-100 bg-red-50 p-4 text-sm text-red-700">
                    <p x-text="formError"></p>
                </div>

                <div class="pt-2 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('donasi-pendidikan') }}"
                       class="inline-flex w-full items-center justify-center rounded-xl border border-gray-300 bg-white px-7 py-3.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                        Kembali
                    </a>
                    <button type="submit"
                            :disabled="submitting"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-amber-400 to-amber-600 px-7 py-3.5 text-sm font-bold text-emerald-950 shadow-lg shadow-amber-900/20 transition hover:from-amber-300 hover:to-amber-500 disabled:opacity-60 disabled:cursor-not-allowed">
                        <span x-show="!submitting">Simpan</span>
                        <span x-show="submitting" class="flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-400">
                Data Anda hanya digunakan untuk keperluan donasi dan tidak akan disebarluaskan.
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
                allowWA: false,
            },
            isLoading: false,
            qrisError: null,
            previewData: null,
            customDebounce: null,
            submitting: false,
            formError: '',

            init() {
                this.$refs.formStartedAt.value = new Date().toISOString();

                this.$watch('form.amount', () => {
                    this.previewData = null;
                    this.formError = '';
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
            },

            handleSubmit() {
                this.formError = '';

                if (!this.hasValidAmount()) {
                    this.formError = 'Silakan pilih nominal donasi terlebih dahulu.';
                    return;
                }

                this.submitting = true;
                this.$nextTick(() => {
                    this.$el.querySelector('form').submit();
                });
            },

            get computedAmount() {
                if (this.form.amount === 'lainnya') {
                    return this.form.customAmount.replace(/[\.\,]/g, '');
                }
                return this.form.amount;
            },

            hasValidAmount() {
                if (this.form.amount && this.form.amount !== 'lainnya') {
                    return parseInt(this.form.amount) >= 1000;
                }

                if (this.form.amount === 'lainnya') {
                    return parseInt(this.form.customAmount || 0) >= 1000;
                }

                return false;
            },

            previewQris() {
                if (!this.hasValidAmount()) {
                    this.isLoading = false;
                    this.previewData = null;
                    this.qrisError = null;
                    return;
                }

                this.isLoading = true;
                this.qrisError = null;
                this.previewData = null;

                const payload = new FormData();
                payload.append('donor_name', this.form.name);
                payload.append('donor_whatsapp', this.form.whatsapp);
                payload.append('amount', this.computedAmount);
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
