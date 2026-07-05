@php
    $heroBg = $schoolSetting?->hero_image_path
        ? asset('storage/' . $schoolSetting->hero_image_path)
        : 'https://images.unsplash.com/photo-1523050854058-8df90110c7f1?w=1920&q=80';
@endphp

<x-guest-layout>
    <x-slot name="title">Orang Tua Asuh - SMA Persis Serang</x-slot>

    <!-- Hero Section -->
    <section class="relative min-h-[70vh] flex items-center" style="background: linear-gradient(135deg, #0F6B3A 0%, #0A4F2B 50%, #0F6B3A 100%);">
        <div class="absolute inset-0 overflow-hidden">
            <img src="{{ $heroBg }}" alt="Background" class="w-full h-full object-cover opacity-20">
        </div>
        <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(15,107,58,0.95) 0%, rgba(10,79,43,0.9) 100%);"></div>

        <div class="relative z-10 max-w-4xl mx-auto px-4 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 text-white/80 text-sm mb-6">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                </svg>
                Program Sosial
            </div>

            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6">
                Jadi <span class="text-emerald-300">Orang Tua Asuh</span>
                <br>untuk Santri Kurang Mampu
            </h1>

            <p class="text-lg md:text-xl text-white/80 max-w-2xl mx-auto mb-10 leading-relaxed">
                Bantu biaya pendidikan dan kebutuhan sehari-hari santri kami yang kurang mampu.
                Satu langkah kecil Anda bisa mengubah masa depan mereka.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="#form-ota"
                   class="inline-flex items-center gap-2 px-8 py-4 bg-white text-[#0F6B3A] font-semibold rounded-xl shadow-xl hover:bg-emerald-50 transition-all duration-300 text-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    Menjadi Orang Tua Asuh
                </a>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-20 bg-white">
        <div class="max-w-5xl mx-auto px-4">
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
    <section id="form-ota" class="py-20 bg-slate-50">
        <div class="max-w-2xl mx-auto px-4">
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
</x-guest-layout>
