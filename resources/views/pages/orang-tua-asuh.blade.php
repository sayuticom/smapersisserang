@extends('layouts.public')

@php
    $schoolName = $schoolSetting->school_name ?? 'SMA Persis Serang';
    $heroBg = $schoolSetting?->hero_image_path
        ? asset('storage/' . $schoolSetting->hero_image_path)
        : 'https://images.unsplash.com/photo-1523050854058-8df90110c7f1?w=1920&q=80';
    $heroTitle = 'Jadi Orang Tua Asuh untuk Santri Kurang Mampu';
    $heroSubtitle = 'Bantu kebutuhan pendidikan, makan, asrama, dan pembinaan santri SMA Persis Serang.';
@endphp

@section('title', 'Orang Tua Asuh - ' . $schoolName)

@section('meta')
    <meta name="description" content="{{ $heroSubtitle }}">
    <meta property="og:title" content="Orang Tua Asuh Santri - SMA Persis Serang">
    <meta property="og:description" content="{{ $heroSubtitle }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $heroBg }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Orang Tua Asuh Santri - SMA Persis Serang">
    <meta name="twitter:description" content="{{ $heroSubtitle }}">
    <meta name="twitter:image" content="{{ $heroBg }}">
@endsection

@section('content')

<!-- Hero Section -->
<section class="relative isolate overflow-hidden bg-gradient-to-br from-[#052E1F] via-[#0A4F2B] to-[#0F6B3A]">
    <div class="absolute inset-0 opacity-[0.06]"
         style="background-image: linear-gradient(135deg, rgba(255,255,255,.5) 1px, transparent 1px); background-size: 42px 42px;"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/30 via-transparent to-emerald-950/20"></div>
    <div class="absolute inset-0">
        <img src="{{ $heroBg }}" alt="" class="h-full w-full object-cover opacity-20">
    </div>
    <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/80 via-emerald-950/60 to-emerald-900/40"></div>
    <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-emerald-950/80 via-emerald-950/35 to-transparent"></div>

    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-amber-300/70 bg-emerald-950/60 px-4 py-1.5 text-sm font-semibold text-amber-300 shadow-lg shadow-emerald-950/20 backdrop-blur">
                    <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                    PROGRAM SOSIAL
                </div>
                <h1 class="mt-6 font-serif text-4xl font-bold leading-tight text-white sm:text-5xl lg:text-6xl">
                    Jadi <span class="text-amber-300">Orang Tua Asuh</span>
                    <br>untuk Santri Kurang Mampu
                </h1>
                <p class="mt-4 max-w-xl text-base sm:text-lg leading-relaxed text-white/80">
                    {{ $heroSubtitle }}
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-4">
                    <a href="#form-ota"
                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-amber-400 to-amber-600 px-7 py-3.5 text-sm font-bold text-emerald-950 shadow-lg shadow-amber-900/20 transition hover:from-amber-300 hover:to-amber-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        Menjadi Orang Tua Asuh
                    </a>
                </div>
            </div>
            <div class="hidden lg:flex justify-center">
                <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur p-8 max-w-md w-full text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-amber-400/20 text-amber-300 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <p class="text-white/90 font-semibold text-lg">Bergabunglah bersama puluhan orang tua asuh lainnya</p>
                    <p class="text-white/60 text-sm mt-2">Setiap bulan, santri kami membutuhkan biaya pendidikan, makan, dan asrama.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="bg-white py-20 lg:py-24">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-14">Bagaimana Cara Kerjanya?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl font-bold text-[#0F6B3A]">1</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Isi Form Pendaftaran</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Lengkapi data diri dan pilih calon anak asuh yang ingin Anda dukung.</p>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl font-bold text-[#0F6B3A]">2</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Konfirmasi via WhatsApp</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Tim kami akan menghubungi Anda untuk konfirmasi dan informasi lebih lanjut.</p>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl font-bold text-[#0F6B3A]">3</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Mulai Program Asuh</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Salurkan donasi Anda secara berkala dan pantau perkembangan anak asuh.</p>
            </div>
        </div>
    </div>
</section>

<!-- Form Section -->
<section id="form-ota" class="bg-slate-50 py-20 lg:py-24">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-gray-900 mb-3">Daftar Menjadi Orang Tua Asuh</h2>
            <p class="text-gray-500">Isi form di bawah untuk memulai program Orang Tua Asuh.</p>
        </div>

        @if(session('success'))
            <div class="mb-6 px-5 py-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-center">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 px-5 py-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-center">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('orang-tua-asuh.submit') }}"
              class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 md:p-8 space-y-5">
            @csrf

            <input type="text" name="website_url" class="hidden" tabindex="-1" autocomplete="off">
            <input type="hidden" name="form_started_at" x-ref="formStartedAt" value="">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-gray-400 text-xs">(opsional)</span></label>
                <input type="text" name="donor_name" value="{{ old('donor_name') }}"
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#0F6B3A] focus:border-[#0F6B3A] transition-shadow"
                       placeholder="cth: Ahmad Fauzi">
                @error('donor_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor WhatsApp <span class="text-red-500">*</span></label>
                <input type="tel" name="donor_phone" value="{{ old('donor_phone') }}" required
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#0F6B3A] focus:border-[#0F6B3A] transition-shadow"
                       placeholder="cth: 08123456789">
                @error('donor_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Pilih Anak Asuh <span class="text-gray-400 text-xs">(opsional)</span></label>
                <select name="student_id"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#0F6B3A] focus:border-[#0F6B3A] transition-shadow">
                    <option value="">— Serahkan ke Sekolah —</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->student_name }} — {{ $student->gender }} — {{ $student->diterima_di_kelas }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1.5">Kosongkan jika ingin sekolah yang menentukan.</p>
                @error('student_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nominal Donasi per Bulan <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-2" x-data="{ amount: '{{ old('amount', '100000') }}' }">
                    @php
                        $presets = [
                            '100000'  => 'Rp100.000',
                            '250000'  => 'Rp250.000',
                            '500000'  => 'Rp500.000',
                            '1000000' => 'Rp1.000.000',
                        ];
                    @endphp
                    @foreach($presets as $val => $label)
                        <label class="flex items-center gap-2 px-4 py-3 rounded-xl border-2 text-sm font-medium cursor-pointer transition-all duration-200"
                               :class="amount === '{{ $val }}' ? 'border-[#0F6B3A] bg-emerald-50 text-[#0F6B3A]' : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300'">
                            <input type="radio" name="amount" value="{{ $val }}"
                                   x-model="amount"
                                   class="sr-only">
                            {{ $label }}
                        </label>
                    @endforeach
                    <label class="flex items-center gap-2 px-4 py-3 rounded-xl border-2 text-sm font-medium cursor-pointer transition-all duration-200"
                           :class="amount === 'lainnya' ? 'border-[#0F6B3A] bg-emerald-50 text-[#0F6B3A]' : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300'">
                        <input type="radio" name="amount" value="lainnya"
                               x-model="amount"
                               class="sr-only">
                        Lainnya
                    </label>
                </div>
                @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div x-show="$el.querySelector('[name=amount]:checked')?.value === 'lainnya'">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nominal Lainnya (Rp)</label>
                <input type="text" name="custom_amount" value="{{ old('custom_amount') }}"
                       inputmode="numeric"
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#0F6B3A] focus:border-[#0F6B3A] transition-shadow"
                       placeholder="cth: 750000">
                @error('custom_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Komitmen Donasi <span class="text-red-500">*</span></label>
                <select name="commitment_duration" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#0F6B3A] focus:border-[#0F6B3A] transition-shadow">
                    <option value="">— Pilih Durasi —</option>
                    <option value="1 bulan" {{ old('commitment_duration') === '1 bulan' ? 'selected' : '' }}>1 Bulan</option>
                    <option value="3 bulan" {{ old('commitment_duration') === '3 bulan' ? 'selected' : '' }}>3 Bulan</option>
                    <option value="6 bulan" {{ old('commitment_duration') === '6 bulan' ? 'selected' : '' }}>6 Bulan</option>
                    <option value="12 bulan" {{ old('commitment_duration') === '12 bulan' ? 'selected' : '' }}>12 Bulan (1 Tahun)</option>
                    <option value="Belum ditentukan" {{ old('commitment_duration') === 'Belum ditentukan' ? 'selected' : '' }}>Belum Ditentukan</option>
                </select>
                @error('commitment_duration') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan <span class="text-gray-400 text-xs">(opsional)</span></label>
                <textarea name="note" rows="3"
                          class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#0F6B3A] focus:border-[#0F6B3A] transition-shadow"
                          placeholder="Ada yang ingin disampaikan?">{{ old('note') }}</textarea>
                @error('note') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                    class="w-full py-3.5 bg-[#0F6B3A] text-white font-semibold rounded-xl hover:bg-[#0A4F2B] transition-colors text-base">
                Kirim Pengajuan
            </button>
        </form>
    </div>
</section>

<script>
    document.querySelector('[name=form_started_at]').value = new Date().toISOString();
</script>

@endsection
