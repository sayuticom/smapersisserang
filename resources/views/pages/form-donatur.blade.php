@extends('layouts.public')

@php
    $schoolName = $schoolSetting->school_name ?? 'SMA Persis Serang';
    $displayName = $schoolSetting->school_name ?? 'SMA Persis Serang';
    $heroBg = $schoolSetting?->building_image_path ? asset('storage/' . $schoolSetting->building_image_path) : null;
    $waNumber = preg_replace('/[^0-9]/', '', $setting?->whatsapp_number ?: '6289661234569');
@endphp

@section('title', 'Form Donatur - ' . $schoolName)

@section('content')

<section class="relative isolate min-h-[300px] overflow-hidden bg-[#052E1F] lg:min-h-[400px]">
    @if($heroBg)
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $heroBg }}')"></div>
    @else
        <div class="absolute inset-0 bg-gradient-to-br from-[#052E1F] via-[#063f2a] to-[#0F6B3A]"></div>
        <div class="absolute inset-0 opacity-[0.08]"
             style="background-image: linear-gradient(135deg, rgba(255,255,255,.45) 1px, transparent 1px); background-size: 42px 42px;"></div>
    @endif
    <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/95 via-emerald-950/75 to-emerald-900/30"></div>
    <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-emerald-950/80 via-emerald-950/35 to-transparent"></div>

    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-[300px] flex-col justify-center pb-12 pt-28 lg:min-h-[400px] lg:pb-16 lg:pt-32">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-amber-300/70 bg-emerald-950/60 px-4 py-2 text-sm font-semibold text-amber-300 shadow-lg shadow-emerald-950/20 backdrop-blur">
                    <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                    FORM DONATUR
                </div>
                <h1 class="mt-6 font-serif text-4xl font-bold leading-tight text-white sm:text-5xl lg:text-6xl">
                    Menjadi Donatur / Orang Tua Asuh
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
        <div class="rounded-2xl border border-amber-200/60 bg-white p-6 shadow-lg shadow-emerald-950/5 lg:p-10">
            <div class="text-center">
                <p class="text-sm font-bold uppercase tracking-[0.24em] text-[#D4A017]">DONASI</p>
                <h2 class="mt-3 font-serif text-2xl font-bold text-[#052E1F] lg:text-3xl">Isi Data Donatur / Orang Tua Asuh</h2>
                <p class="mt-2 text-sm leading-relaxed text-gray-500">Setelah dikirim, Anda akan diarahkan ke halaman pembayaran untuk memilih metode pembayaran.</p>
            </div>

            <form method="POST" action="{{ route('donasi-pendidikan.form-donatur.submit') }}" class="mt-8 space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700">Nama Donatur <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="mt-1.5 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-[#0F6B3A] focus:ring-2 focus:ring-[#0F6B3A]/20">
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="whatsapp" class="block text-sm font-semibold text-gray-700">Nomor WhatsApp <span class="text-red-500">*</span></label>
                    <input type="tel" id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}" required
                           placeholder="08xxxxxxxxxx"
                           class="mt-1.5 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-[#0F6B3A] focus:ring-2 focus:ring-[#0F6B3A]/20">
                    @error('whatsapp') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <span class="block text-sm font-semibold text-gray-700">Jenis Dukungan <span class="text-red-500">*</span></span>
                    <div class="mt-2 grid gap-3 sm:grid-cols-3">
                        <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border-2 p-4 text-sm font-semibold transition has-[:checked]:border-[#0F6B3A] has-[:checked]:bg-[#0F6B3A]/5 has-[:checked]:text-[#0F6B3A] border-gray-200 bg-white text-gray-700 hover:border-[#0F6B3A]/40">
                            <input type="radio" name="donation_type" value="orang_tua_asuh" {{ old('donation_type') === 'orang_tua_asuh' ? 'checked' : '' }} required class="sr-only">
                            Orang Tua Asuh Santri
                        </label>
                        <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border-2 p-4 text-sm font-semibold transition has-[:checked]:border-[#0F6B3A] has-[:checked]:bg-[#0F6B3A]/5 has-[:checked]:text-[#0F6B3A] border-gray-200 bg-white text-gray-700 hover:border-[#0F6B3A]/40">
                            <input type="radio" name="donation_type" value="makan_santri" {{ old('donation_type') === 'makan_santri' ? 'checked' : '' }} required class="sr-only">
                            Makan Santri
                        </label>
                        <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border-2 p-4 text-sm font-semibold transition has-[:checked]:border-[#0F6B3A] has-[:checked]:bg-[#0F6B3A]/5 has-[:checked]:text-[#0F6B3A] border-gray-200 bg-white text-gray-700 hover:border-[#0F6B3A]/40">
                            <input type="radio" name="donation_type" value="pendidikan_gratis" {{ old('donation_type') === 'pendidikan_gratis' ? 'checked' : '' }} required class="sr-only">
                            Pendidikan Gratis
                        </label>
                        <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border-2 p-4 text-sm font-semibold transition has-[:checked]:border-[#0F6B3A] has-[:checked]:bg-[#0F6B3A]/5 has-[:checked]:text-[#0F6B3A] border-gray-200 bg-white text-gray-700 hover:border-[#0F6B3A]/40">
                            <input type="radio" name="donation_type" value="asrama_perlengkapan" {{ old('donation_type') === 'asrama_perlengkapan' ? 'checked' : '' }} required class="sr-only">
                            Asrama & Perlengkapan
                        </label>
                        <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border-2 p-4 text-sm font-semibold transition has-[:checked]:border-[#0F6B3A] has-[:checked]:bg-[#0F6B3A]/5 has-[:checked]:text-[#0F6B3A] border-gray-200 bg-white text-gray-700 hover:border-[#0F6B3A]/40">
                            <input type="radio" name="donation_type" value="keduanya" {{ old('donation_type') === 'keduanya' ? 'checked' : '' }} required class="sr-only">
                            Keduanya / Umum
                        </label>
                    </div>
                    @error('donation_type') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div x-data="{
                    selected: '{{ old('amount', '') }}',
                    customAmount: '{{ old('custom_amount', '') }}',
                    get showCustom() { return this.selected === 'lainnya'; }
                }">
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
                                   :class="selected === opt.value ? 'border-[#0F6B3A] bg-[#0F6B3A]/10 text-[#0F6B3A]' : 'border-gray-200 bg-white text-gray-700 hover:border-[#0F6B3A]/40'">
                                <input type="radio" name="amount" :value="opt.value" x-model="selected" class="sr-only">
                                <span x-text="opt.label"></span>
                            </label>
                        </template>
                        <label class="flex cursor-pointer items-center justify-center rounded-xl border p-3 text-sm font-semibold transition"
                               :class="selected === 'lainnya' ? 'border-[#0F6B3A] bg-[#0F6B3A]/10 text-[#0F6B3A]' : 'border-gray-200 bg-white text-gray-700 hover:border-[#0F6B3A]/40'">
                            <input type="radio" name="amount" value="lainnya" x-model="selected" class="sr-only">
                            Lainnya
                        </label>
                    </div>
                    <div x-show="showCustom" x-transition class="mt-3">
                        <label for="custom_amount" class="block text-sm font-medium text-gray-600">Nominal lainnya</label>
                        <div class="relative mt-1">
                            <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-400">Rp</span>
                            <input type="text" id="custom_amount" name="custom_amount" x-model="customAmount"
                                   inputmode="numeric"
                                   class="w-full rounded-xl border border-gray-300 py-3 pl-10 pr-4 text-sm focus:border-[#0F6B3A] focus:ring-2 focus:ring-[#0F6B3A]/20"
                                   placeholder="0">
                        </div>
                    </div>
                    @error('amount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    @error('custom_amount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="note" class="block text-sm font-semibold text-gray-700">Catatan <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <textarea id="note" name="note" rows="3"
                              class="mt-1.5 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-[#0F6B3A] focus:ring-2 focus:ring-[#0F6B3A]/20"
                              placeholder="Contoh: Donasi atas nama keluarga, atau pesan tambahan...">{{ old('note') }}</textarea>
                    @error('note') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="flex flex-col gap-3 pt-2 sm:flex-row">
                    <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-amber-400 to-amber-600 px-7 py-4 text-sm font-bold text-emerald-950 shadow-lg shadow-amber-900/20 transition hover:from-amber-300 hover:to-amber-500 sm:w-auto">
                        Kirim via WhatsApp
                    </button>
                    <a href="{{ route('donasi-pendidikan') }}"
                       class="inline-flex w-full items-center justify-center rounded-xl border border-gray-300 bg-white px-7 py-4 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 sm:w-auto">
                        Kembali
                    </a>
                </div>
            </form>
        </div>

        <div class="mt-8 text-center">
            <p class="text-sm text-gray-400">
                Data Anda hanya digunakan untuk keperluan donasi dan tidak akan disebarluaskan.
            </p>
        </div>
    </div>
</section>

@endsection
