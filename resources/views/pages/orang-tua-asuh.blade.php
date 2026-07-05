@extends('layouts.public')

@php
    $schoolName = $schoolSetting->school_name ?? 'SMA Persis Serang';
    $whatsappNumber = preg_replace('/[^0-9]/', '', $setting?->whatsapp_number ?: '6289661234569');
@endphp

@section('title', 'Program Orang Tua Asuh Santri - ' . $schoolName)

@section('content')

<x-slot:header>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
</x-slot:header>

<section class="relative isolate overflow-hidden bg-gradient-to-br from-[#052E1F] via-[#0A4F2B] to-[#0F6B3A]">
    <div class="absolute inset-0 opacity-[0.06]"
         style="background-image: linear-gradient(135deg, rgba(255,255,255,.5) 1px, transparent 1px); background-size: 42px 42px;"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/30 via-transparent to-emerald-950/20"></div>

    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-24 text-center">
        <div class="inline-flex items-center gap-2 rounded-full border border-amber-300/70 bg-emerald-950/60 px-4 py-2 text-sm font-semibold text-amber-300 shadow-lg shadow-emerald-950/20 backdrop-blur-sm">
            <span class="h-2 w-2 rounded-full bg-amber-300"></span>
            PROGRAM ORANG TUA ASUH
        </div>
        <h1 class="mx-auto mt-6 max-w-4xl text-4xl font-bold leading-tight text-white sm:text-5xl lg:text-6xl">
            Program Orang Tua Asuh Santri
        </h1>
        <p class="mx-auto mt-5 max-w-3xl text-lg leading-8 text-emerald-100/90">
            Program Orang Tua Asuh Santri adalah gerakan kepedulian untuk membantu kebutuhan pendidikan, makan, asrama, dan pembinaan murid SMA Persis Serang yang sedang menempuh pendidikan secara gratis.
        </p>
        <div class="mt-8 flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
            <a href="{{ $waUrl }}" target="_blank" rel="noopener"
               class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-amber-400 to-amber-600 px-7 py-4 text-sm font-bold text-emerald-950 shadow-lg shadow-amber-900/20 transition hover:from-amber-300 hover:to-amber-500">
                Hubungi WA SMA Persis Serang
            </a>
        </div>
    </div>
</section>

<section class="bg-white py-16 lg:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl">
            <h2 class="font-serif text-3xl font-bold text-[#052E1F] sm:text-4xl text-center">Tentang Program</h2>
            <div class="mt-8 space-y-5 text-lg leading-8 text-emerald-900/75">
                <p>
                    <strong class="text-[#0F6B3A]">Program Orang Tua Asuh Santri</strong> adalah gerakan kepedulian untuk membantu kebutuhan pendidikan, makan, asrama, dan pembinaan murid SMA Persis Serang yang sedang menempuh pendidikan secara gratis.
                </p>
                <p>
                    Donatur dapat menjadi <strong class="text-[#0F6B3A]">orang tua asuh</strong> bagi satu atau beberapa murid. Bantuan yang diberikan digunakan untuk makan harian, perlengkapan sekolah, perlengkapan asrama, kesehatan ringan, dan pembinaan akhlak.
                </p>
                <p>
                    Donatur boleh memilih murid tertentu, atau menyerahkan kepada pihak sekolah untuk menentukan murid yang paling membutuhkan.
                </p>
            </div>
        </div>
    </div>
</section>

<section id="daftar-murid" class="bg-[#FBF7EF] py-16 lg:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-bold uppercase tracking-[0.24em] text-[#D4A017]">CALON ANAK ASUH</p>
            <h2 class="mt-3 font-serif text-3xl font-bold text-[#052E1F] sm:text-4xl">Pilih Anak Asuh</h2>
            <p class="mt-3 text-lg leading-8 text-emerald-900/75">
                Berikut adalah murid-murid yang siap mendapatkan orang tua asuh.
            </p>
        </div>

        @if($students->isEmpty())
            <div class="mt-10 mx-auto max-w-2xl text-center">
                <div class="rounded-2xl border border-amber-200 bg-white p-8 shadow-lg shadow-emerald-950/5">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-amber-100">
                        <svg class="h-8 w-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <p class="mt-4 text-lg font-semibold text-emerald-900">
                        Data anak asuh sedang disiapkan
                    </p>
                    <p class="mt-2 text-sm text-gray-600">
                        Bapak/Ibu tetap dapat mendaftar sebagai Orang Tua Asuh dan pilihan anak asuh akan dibantu oleh pihak sekolah.
                    </p>
                </div>
            </div>
        @else
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach($students as $student)
                    <div class="group rounded-2xl border border-emerald-100 bg-white p-5 shadow-md shadow-emerald-950/5 transition hover:-translate-y-1 hover:border-amber-200 hover:shadow-lg flex flex-col">
                        <div class="flex-shrink-0">
                            @if($student->photo_url)
                                <img src="{{ $student->photo_url }}" alt="{{ $student->name }}"
                                     class="w-full aspect-[4/5] object-cover rounded-xl">
                            @else
                                <div class="w-full aspect-[4/5] rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-50 flex items-center justify-center">
                                    <svg class="h-16 w-16 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h18a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="mt-4 flex-1 flex flex-col">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="text-base font-bold text-[#052E1F]">{{ $student->name }}</h3>
                                @if($student->is_priority)
                                    <span class="shrink-0 inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700">Prioritas</span>
                                @endif
                            </div>
                            <div class="mt-2 space-y-1 text-sm text-gray-600">
                                <p>{{ $student->gender }} &middot; {{ $student->class_name }}</p>
                                @if($student->origin)
                                    <p>Asal: {{ $student->origin }}</p>
                                @endif
                            </div>
                            @if($student->need_description)
                                <p class="mt-2 text-sm text-gray-500 line-clamp-2">{{ $student->need_description }}</p>
                            @endif
                            <div class="mt-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    {{ $student->foster_status === 'available' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $student->foster_status === 'assigned' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $student->foster_status === 'inactive' ? 'bg-gray-100 text-gray-600' : '' }}">
                                    {{ $student->foster_status_label }}
                                </span>
                            </div>
                            <div class="mt-auto pt-4">
                                @if($student->foster_status === 'available')
                                    <a href="#form-pendaftaran"
                                       @click.prevent="selectStudent({{ $student->id }}, '{{ $student->name }}')"
                                       class="inline-flex w-full items-center justify-center rounded-lg bg-[#0F6B3A] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#0A4F2B]">
                                        Pilih sebagai Anak Asuh
                                    </a>
                                @elseif($student->foster_status === 'assigned')
                                    <span class="inline-flex w-full items-center justify-center rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-400 cursor-not-allowed">
                                        Sudah Ada Orang Tua Asuh
                                    </span>
                                @else
                                    <span class="inline-flex w-full items-center justify-center rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-400 cursor-not-allowed">
                                        Tidak Tersedia
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-10 text-center">
            <button @click="serahkanKeSekolah()"
                    class="inline-flex items-center gap-2 rounded-xl border-2 border-dashed border-[#0F6B3A]/40 px-6 py-4 text-sm font-semibold text-[#0F6B3A] transition hover:border-[#0F6B3A] hover:bg-[#0F6B3A]/5">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Saya serahkan pilihan anak asuh kepada pihak sekolah
            </button>
        </div>
    </div>
</section>

<section id="form-pendaftaran" class="bg-white py-16 lg:py-20"
         x-data="fosterForm()"
         x-show="formOpen"
         x-cloak>
    <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-2xl border border-amber-200/60 bg-white p-6 shadow-lg shadow-emerald-950/5 lg:p-10">
            <div class="text-center">
                <p class="text-sm font-bold uppercase tracking-[0.24em] text-[#D4A017]">FORMULIR PENDAFTARAN</p>
                <h2 class="mt-3 font-serif text-2xl font-bold text-[#052E1F] lg:text-3xl">Daftar sebagai Orang Tua Asuh</h2>
            </div>

            <template x-if="selectedStudentName">
                <div class="mt-6 rounded-xl border border-emerald-100 bg-emerald-50/50 p-4 text-center">
                    <p class="text-sm text-gray-500">Anak Asuh yang Dipilih</p>
                    <p class="mt-1 text-lg font-bold text-[#052E1F]" x-text="selectedStudentName"></p>
                </div>
            </template>

            <template x-if="!selectedStudentName && formOpen">
                <div class="mt-6 rounded-xl border border-amber-100 bg-amber-50/50 p-4 text-center">
                    <p class="text-sm font-medium text-amber-700">
                        Pilihan anak asuh akan ditentukan oleh pihak sekolah.
                    </p>
                </div>
            </template>

            <form class="mt-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Donatur <span class="text-gray-400">(opsional)</span></label>
                    <input type="text" x-model="form.donor_name" maxlength="100"
                           class="mt-1.5 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-[#0F6B3A] focus:ring-[#0F6B3A]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nomor WhatsApp <span class="text-gray-400">(opsional)</span></label>
                    <input type="text" x-model="form.donor_phone" maxlength="30"
                           class="mt-1.5 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-[#0F6B3A] focus:ring-[#0F6B3A]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nominal Bantuan per Bulan <span class="text-red-500">*</span></label>
                    <div class="mt-1.5 grid grid-cols-3 gap-2 sm:grid-cols-4">
                        <template x-for="nom in nominalPresets" :key="nom.value">
                            <button type="button" @click="setNominal(nom.value)"
                                    :class="form.amount === nom.value ? 'bg-[#0F6B3A] text-white border-[#0F6B3A]' : 'bg-white text-gray-700 border-gray-300 hover:border-[#0F6B3A]'"
                                    class="rounded-xl border px-3 py-2.5 text-sm font-semibold transition">
                                <span x-text="nom.label"></span>
                            </button>
                        </template>
                        <button type="button" @click="setNominal('lainnya')"
                                :class="form.amount === 'lainnya' ? 'bg-[#0F6B3A] text-white border-[#0F6B3A]' : 'bg-white text-gray-700 border-gray-300 hover:border-[#0F6B3A]'"
                                class="rounded-xl border px-3 py-2.5 text-sm font-semibold transition">
                            Lainnya
                        </button>
                    </div>
                    <div x-show="form.amount === 'lainnya'" x-cloak class="mt-2">
                        <input type="text" x-model="form.customAmount" x-on:input.debounce="previewQris()" maxlength="12"
                               placeholder="Masukkan nominal"
                               class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-[#0F6B3A] focus:ring-[#0F6B3A]">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Durasi Komitmen <span class="text-red-500">*</span></label>
                    <div class="mt-1.5 grid grid-cols-3 gap-2 sm:grid-cols-5">
                        <template x-for="dur in durationOptions" :key="dur.value">
                            <button type="button" @click="form.commitment_duration = dur.value"
                                    :class="form.commitment_duration === dur.value ? 'bg-[#0F6B3A] text-white border-[#0F6B3A]' : 'bg-white text-gray-700 border-gray-300 hover:border-[#0F6B3A]'"
                                    class="rounded-xl border px-3 py-2.5 text-sm font-semibold transition">
                                <span x-text="dur.label"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Catatan <span class="text-gray-400">(opsional)</span></label>
                    <textarea x-model="form.note" rows="3" maxlength="500"
                              class="mt-1.5 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-[#0F6B3A] focus:ring-[#0F6B3A]"></textarea>
                </div>
            </form>

            <div class="mt-6">
                <template x-if="isLoading">
                    <div class="flex items-center justify-center py-8">
                        <svg class="h-8 w-8 animate-spin text-[#0F6B3A]" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </template>

                <template x-if="qrisError && !isLoading">
                    <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-center">
                        <p class="text-sm text-red-600" x-text="qrisError"></p>
                    </div>
                </template>

                <template x-if="previewData && !isLoading && !qrisError">
                    <div>
                        <div class="rounded-xl border border-emerald-100 bg-emerald-50/50 p-4">
                            <table class="w-full text-sm">
                                <tr>
                                    <td class="py-1 pr-4 font-medium text-gray-500">Nama</td>
                                    <td class="py-1 font-semibold text-[#052E1F]" x-text="previewData.summary.donor_name"></td>
                                </tr>
                                <tr>
                                    <td class="py-1 pr-4 font-medium text-gray-500">Nomor WhatsApp</td>
                                    <td class="py-1 font-semibold text-[#052E1F]" x-text="previewData.summary.donor_phone"></td>
                                </tr>
                                <tr x-show="previewData.summary.student_name">
                                    <td class="py-1 pr-4 font-medium text-gray-500">Anak Asuh</td>
                                    <td class="py-1 font-semibold text-[#052E1F]" x-text="previewData.summary.student_name"></td>
                                </tr>
                                <tr>
                                    <td class="py-1 pr-4 font-medium text-gray-500">Nominal</td>
                                    <td class="py-1 font-semibold text-[#0F6B3A]" x-text="previewData.amount_formatted"></td>
                                </tr>
                                <tr>
                                    <td class="py-1 pr-4 font-medium text-gray-500">Durasi Komitmen</td>
                                    <td class="py-1 font-semibold text-[#052E1F]" x-text="previewData.summary.commitment_duration"></td>
                                </tr>
                                <tr x-show="previewData.summary.note">
                                    <td class="py-1 pr-4 font-medium text-gray-500">Catatan</td>
                                    <td class="py-1 font-semibold text-[#052E1F]" x-text="previewData.summary.note"></td>
                                </tr>
                            </table>
                        </div>

                        <div class="mt-6 flex justify-center">
                            <div class="rounded-2xl border border-emerald-100 bg-white p-4 shadow-lg shadow-emerald-950/5">
                                <img :src="previewData.qris_image" alt="QRIS Donasi" class="h-64 w-64 object-contain">
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <p class="text-xs text-gray-500">
                                <template x-if="previewData.is_dynamic">Scan QRIS di atas menggunakan aplikasi pembayaran.</template>
                                <template x-if="previewData.static_fallback">Silakan masukkan nominal <strong x-text="previewData.amount_formatted"></strong> secara manual di aplikasi pembayaran.</template>
                            </p>
                        </div>

                        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-center">
                            <a :href="`/donasi-pendidikan/qris/download?amount=${previewData.amount_raw}`"
                               target="_blank"
                               class="inline-flex items-center justify-center rounded-xl border border-[#0F6B3A] px-6 py-3 text-sm font-bold text-[#0F6B3A] transition hover:bg-[#0F6B3A] hover:text-white">
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Download QRIS
                            </a>
                        </div>
                    </div>
                </template>

                <template x-if="!previewData && !isLoading && !qrisError && hasValidAmount()">
                    <div class="flex items-center justify-center py-8">
                        <p class="text-sm text-gray-500">Menampilkan QRIS...</p>
                    </div>
                </template>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
function fosterForm() {
    return {
        formOpen: false,
        fosterStudentId: null,
        selectedStudentName: null,
        form: {
            donor_name: '',
            donor_phone: '',
            amount: '',
            customAmount: '',
            commitment_duration: '',
            note: '',
        },
        isLoading: false,
        qrisError: null,
        previewData: null,
        nominalPresets: [
            { value: '50000', label: 'Rp50.000' },
            { value: '100000', label: 'Rp100.000' },
            { value: '150000', label: 'Rp150.000' },
            { value: '200000', label: 'Rp200.000' },
            { value: '300000', label: 'Rp300.000' },
            { value: '500000', label: 'Rp500.000' },
            { value: '750000', label: 'Rp750.000' },
            { value: '1000000', label: 'Rp1.000.000' },
        ],
        durationOptions: [
            { value: '1 bulan', label: '1 Bulan' },
            { value: '3 bulan', label: '3 Bulan' },
            { value: '6 bulan', label: '6 Bulan' },
            { value: '1 tahun', label: '1 Tahun' },
            { value: 'lainnya', label: 'Lainnya' },
        ],

        init() {
            this.$watch('form.amount', () => {
                if (this.form.amount && this.form.amount !== 'lainnya') {
                    this.previewQris();
                }
            });
            this.$watch('form.customAmount', () => {
                if (this.form.amount === 'lainnya') {
                    this.previewQris();
                }
            });
        },

        selectStudent(id, name) {
            this.fosterStudentId = id;
            this.selectedStudentName = name;
            this.formOpen = true;
            this.previewData = null;
            this.qrisError = null;
            if (this.form.amount) {
                this.previewQris();
            }
            setTimeout(() => {
                document.getElementById('form-pendaftaran')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        },

        serahkanKeSekolah() {
            this.fosterStudentId = null;
            this.selectedStudentName = null;
            this.formOpen = true;
            this.previewData = null;
            this.qrisError = null;
            if (this.form.amount) {
                this.previewQris();
            }
            setTimeout(() => {
                document.getElementById('form-pendaftaran')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        },

        setNominal(value) {
            this.form.amount = value;
            if (value !== 'lainnya') {
                this.form.customAmount = '';
            }
        },

        hasValidAmount() {
            if (this.form.amount && this.form.amount !== 'lainnya') return true;
            if (this.form.amount === 'lainnya' && this.form.customAmount) return true;
            return false;
        },

        previewQris() {
            const amount = this.form.amount === 'lainnya' ? this.form.customAmount : this.form.amount;
            if (!amount) return;

            this.isLoading = true;
            this.qrisError = null;
            this.previewData = null;

            const payload = new URLSearchParams();
            if (this.fosterStudentId) payload.append('foster_student_id', this.fosterStudentId);
            payload.append('donor_name', this.form.donor_name);
            payload.append('donor_phone', this.form.donor_phone);
            payload.append('amount', this.form.amount);
            if (this.form.amount === 'lainnya' && this.form.customAmount) {
                payload.append('custom_amount', this.form.customAmount);
            }
            payload.append('commitment_duration', this.form.commitment_duration);
            payload.append('note', this.form.note);

            fetch('{{ route("orang-tua-asuh.qris.preview") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: payload,
            })
            .then(r => r.json())
            .then(data => {
                this.isLoading = false;
                if (data.success) {
                    this.previewData = data;
                } else {
                    this.qrisError = data.message || 'Gagal membuat QRIS';
                }
            })
            .catch(err => {
                this.isLoading = false;
                this.qrisError = 'Terjadi kesalahan. Silakan coba lagi.';
            });
        },
    };
}
</script>
@endpush

@endsection
