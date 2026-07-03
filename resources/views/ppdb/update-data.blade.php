@extends('layouts.public')

@php
    $app = $application;
@endphp

@section('title', 'Pembaruan Data Siswa - SPMB SMA Persis Serang')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-emerald-950 via-emerald-900 to-emerald-800 py-12">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-white">Pembaruan Data Siswa</h1>
            <p class="mt-3 text-sm md:text-base text-[#F5D36B] font-medium">Lengkapi data diri putra/putri Anda untuk kelengkapan administrasi SPMB</p>
            <p class="mt-2 text-sm text-white/90">Nomor Pendaftaran: <strong>{{ $app->registration_number }}</strong></p>
        </div>

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('spmb.update-data.store', $app->update_token) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-emerald-50 border-b border-emerald-100">
                    <h2 class="text-lg font-bold text-emerald-800">Data Siswa</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="student_name" value="{{ old('student_name', $app->student_name) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Panggilan</label>
                            <input type="text" name="nama_panggilan" value="{{ old('nama_panggilan', $app->nama_panggilan) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Induk Asal</label>
                            <input type="text" name="nomor_induk_asal" value="{{ old('nomor_induk_asal', $app->nomor_induk_asal) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">NISN</label>
                            <input type="text" name="nisn" value="{{ old('nisn', $app->nisn) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select name="gender" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                                <option value="laki_laki" {{ old('gender', $app->gender) === 'laki_laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="perempuan" {{ old('gender', $app->gender) === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Agama <span class="text-red-500">*</span></label>
                            <select name="agama" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                                @foreach($agamaOptions as $opt)
                                    <option value="{{ $opt }}" {{ old('agama', $app->agama) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tempat Lahir <span class="text-red-500">*</span></label>
                            <input type="text" name="birth_place" value="{{ old('birth_place', $app->birth_place) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" name="birth_date" value="{{ old('birth_date', $app->birth_date?->format('Y-m-d')) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Anak Ke</label>
                            <input type="number" name="anak_ke" value="{{ old('anak_ke', $app->anak_ke) }}" min="1"
                                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Status Anak dalam Keluarga</label>
                            <select name="status_anak_dalam_keluarga" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                <option value="">-- Pilih --</option>
                                @foreach($statusKeluargaOptions as $opt)
                                    <option value="{{ $opt }}" {{ old('status_anak_dalam_keluarga', $app->status_anak_dalam_keluarga) === $opt ? 'selected' : '' }}>
                                        {{ ucwords(str_replace('_', ' ', $opt)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Siswa <span class="text-red-500">*</span></label>
                        <textarea name="address" rows="3" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>{{ old('address', $app->address) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Telepon Siswa</label>
                        <input type="text" name="telepon_siswa" value="{{ old('telepon_siswa', $app->telepon_siswa) }}"
                               class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Sekolah Sebelumnya <span class="text-red-500">*</span></label>
                        <input type="text" name="previous_school" value="{{ old('previous_school', $app->previous_school) }}"
                               class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Sekolah Asal</label>
                        <textarea name="alamat_sekolah_asal" rows="2" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('alamat_sekolah_asal', $app->alamat_sekolah_asal) }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Siap Boarding <span class="text-red-500">*</span></label>
                            <select name="boarding_ready" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                                <option value="1" {{ old('boarding_ready', $app->boarding_ready) == 1 ? 'selected' : '' }}>Ya, Siap</option>
                                <option value="0" {{ old('boarding_ready', $app->boarding_ready) == 0 ? 'selected' : '' }}>Tidak</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Kemampuan Baca Al-Quran <span class="text-red-500">*</span></label>
                            <select name="quran_reading_ability" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                                <option value="belum_bisa" {{ old('quran_reading_ability', $app->quran_reading_ability) === 'belum_bisa' ? 'selected' : '' }}>Belum Bisa</option>
                                <option value="terbata_bata" {{ old('quran_reading_ability', $app->quran_reading_ability) === 'terbata_bata' ? 'selected' : '' }}>Terbata-bata</option>
                                <option value="lancar" {{ old('quran_reading_ability', $app->quran_reading_ability) === 'lancar' ? 'selected' : '' }}>Lancar</option>
                                <option value="baik" {{ old('quran_reading_ability', $app->quran_reading_ability) === 'baik' ? 'selected' : '' }}>Baik</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Motivasi <span class="text-red-500">*</span></label>
                        <textarea name="motivation" rows="3" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>{{ old('motivation', $app->motivation) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan Kesehatan</label>
                        <textarea name="health_notes" rows="2" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('health_notes', $app->health_notes) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Foto 3x4</label>
                        <input type="file" name="foto_3x4" accept="image/jpeg,image/png"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        <p class="mt-1 text-xs text-gray-400">Format: JPG/PNG, maksimal 2MB. Ukuran 3x4.</p>
                        @if($app->foto_3x4)
                            <div class="mt-3">
                                <img src="{{ asset('storage/' . $app->foto_3x4) }}" alt="Foto 3x4"
                                     class="max-w-[150px] rounded-lg border border-gray-200 shadow-sm">
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-emerald-50 border-b border-emerald-100">
                    <h2 class="text-lg font-bold text-emerald-800">Data Orang Tua</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Ayah <span class="text-red-500">*</span></label>
                            <input type="text" name="father_name" value="{{ old('father_name', $app->father_name) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Ibu <span class="text-red-500">*</span></label>
                            <input type="text" name="mother_name" value="{{ old('mother_name', $app->mother_name) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Ayah</label>
                            <textarea name="alamat_ayah" rows="2" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('alamat_ayah', $app->alamat_ayah) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Ibu</label>
                            <textarea name="alamat_ibu" rows="2" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('alamat_ibu', $app->alamat_ibu) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">No. WhatsApp Orang Tua <span class="text-red-500">*</span></label>
                            <input type="text" name="parent_whatsapp" value="{{ old('parent_whatsapp', $app->parent_whatsapp) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Pekerjaan Ayah</label>
                            <input type="text" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $app->pekerjaan_ayah) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Pekerjaan Ibu</label>
                            <input type="text" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $app->pekerjaan_ibu) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Pekerjaan Orang Tua (dulu)</label>
                            <input type="text" name="parent_job" value="{{ old('parent_job', $app->parent_job) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Pendidikan Ayah</label>
                            <input type="text" name="pendidikan_ayah" value="{{ old('pendidikan_ayah', $app->pendidikan_ayah) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Pendidikan Ibu</label>
                            <input type="text" name="pendidikan_ibu" value="{{ old('pendidikan_ibu', $app->pendidikan_ibu) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Penghasilan Ayah</label>
                            <select name="penghasilan_ayah" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                <option value="">-- Pilih --</option>
                                <option value="< 1 juta" {{ old('penghasilan_ayah', $app->penghasilan_ayah) === '< 1 juta' ? 'selected' : '' }}>&lt; Rp 1.000.000</option>
                                <option value="1-2 juta" {{ old('penghasilan_ayah', $app->penghasilan_ayah) === '1-2 juta' ? 'selected' : '' }}>Rp 1.000.000 - Rp 2.000.000</option>
                                <option value="2-5 juta" {{ old('penghasilan_ayah', $app->penghasilan_ayah) === '2-5 juta' ? 'selected' : '' }}>Rp 2.000.000 - Rp 5.000.000</option>
                                <option value="5-10 juta" {{ old('penghasilan_ayah', $app->penghasilan_ayah) === '5-10 juta' ? 'selected' : '' }}>Rp 5.000.000 - Rp 10.000.000</option>
                                <option value="> 10 juta" {{ old('penghasilan_ayah', $app->penghasilan_ayah) === '> 10 juta' ? 'selected' : '' }}>&gt; Rp 10.000.000</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Penghasilan Ibu</label>
                            <select name="penghasilan_ibu" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                <option value="">-- Pilih --</option>
                                <option value="< 1 juta" {{ old('penghasilan_ibu', $app->penghasilan_ibu) === '< 1 juta' ? 'selected' : '' }}>&lt; Rp 1.000.000</option>
                                <option value="1-2 juta" {{ old('penghasilan_ibu', $app->penghasilan_ibu) === '1-2 juta' ? 'selected' : '' }}>Rp 1.000.000 - Rp 2.000.000</option>
                                <option value="2-5 juta" {{ old('penghasilan_ibu', $app->penghasilan_ibu) === '2-5 juta' ? 'selected' : '' }}>Rp 2.000.000 - Rp 5.000.000</option>
                                <option value="5-10 juta" {{ old('penghasilan_ibu', $app->penghasilan_ibu) === '5-10 juta' ? 'selected' : '' }}>Rp 5.000.000 - Rp 10.000.000</option>
                                <option value="> 10 juta" {{ old('penghasilan_ibu', $app->penghasilan_ibu) === '> 10 juta' ? 'selected' : '' }}>&gt; Rp 10.000.000</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-emerald-50 border-b border-emerald-100">
                    <h2 class="text-lg font-bold text-emerald-800">Data Orang Tua Wali <span class="text-sm font-normal text-emerald-500">(jika ada)</span></h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Ayah Wali</label>
                            <input type="text" name="nama_ayah_wali" value="{{ old('nama_ayah_wali', $app->nama_ayah_wali) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Ibu Wali</label>
                            <input type="text" name="nama_ibu_wali" value="{{ old('nama_ibu_wali', $app->nama_ibu_wali) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Ayah Wali</label>
                            <textarea name="alamat_ayah_wali" rows="2" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('alamat_ayah_wali', $app->alamat_ayah_wali) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Ibu Wali</label>
                            <textarea name="alamat_ibu_wali" rows="2" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('alamat_ibu_wali', $app->alamat_ibu_wali) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Telepon Wali</label>
                            <input type="text" name="telepon_wali" value="{{ old('telepon_wali', $app->telepon_wali) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Pekerjaan Ayah Wali</label>
                            <input type="text" name="pekerjaan_ayah_wali" value="{{ old('pekerjaan_ayah_wali', $app->pekerjaan_ayah_wali) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Pekerjaan Ibu Wali</label>
                            <input type="text" name="pekerjaan_ibu_wali" value="{{ old('pekerjaan_ibu_wali', $app->pekerjaan_ibu_wali) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Pendidikan Ayah Wali</label>
                            <input type="text" name="pendidikan_ayah_wali" value="{{ old('pendidikan_ayah_wali', $app->pendidikan_ayah_wali) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Pendidikan Ibu Wali</label>
                            <input type="text" name="pendidikan_ibu_wali" value="{{ old('pendidikan_ibu_wali', $app->pendidikan_ibu_wali) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Penghasilan Ayah Wali</label>
                            <select name="penghasilan_ayah_wali" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                <option value="">-- Pilih --</option>
                                <option value="< 1 juta" {{ old('penghasilan_ayah_wali', $app->penghasilan_ayah_wali) === '< 1 juta' ? 'selected' : '' }}>&lt; Rp 1.000.000</option>
                                <option value="1-2 juta" {{ old('penghasilan_ayah_wali', $app->penghasilan_ayah_wali) === '1-2 juta' ? 'selected' : '' }}>Rp 1.000.000 - Rp 2.000.000</option>
                                <option value="2-5 juta" {{ old('penghasilan_ayah_wali', $app->penghasilan_ayah_wali) === '2-5 juta' ? 'selected' : '' }}>Rp 2.000.000 - Rp 5.000.000</option>
                                <option value="5-10 juta" {{ old('penghasilan_ayah_wali', $app->penghasilan_ayah_wali) === '5-10 juta' ? 'selected' : '' }}>Rp 5.000.000 - Rp 10.000.000</option>
                                <option value="> 10 juta" {{ old('penghasilan_ayah_wali', $app->penghasilan_ayah_wali) === '> 10 juta' ? 'selected' : '' }}>&gt; Rp 10.000.000</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Penghasilan Ibu Wali</label>
                            <select name="penghasilan_ibu_wali" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                <option value="">-- Pilih --</option>
                                <option value="< 1 juta" {{ old('penghasilan_ibu_wali', $app->penghasilan_ibu_wali) === '< 1 juta' ? 'selected' : '' }}>&lt; Rp 1.000.000</option>
                                <option value="1-2 juta" {{ old('penghasilan_ibu_wali', $app->penghasilan_ibu_wali) === '1-2 juta' ? 'selected' : '' }}>Rp 1.000.000 - Rp 2.000.000</option>
                                <option value="2-5 juta" {{ old('penghasilan_ibu_wali', $app->penghasilan_ibu_wali) === '2-5 juta' ? 'selected' : '' }}>Rp 2.000.000 - Rp 5.000.000</option>
                                <option value="5-10 juta" {{ old('penghasilan_ibu_wali', $app->penghasilan_ibu_wali) === '5-10 juta' ? 'selected' : '' }}>Rp 5.000.000 - Rp 10.000.000</option>
                                <option value="> 10 juta" {{ old('penghasilan_ibu_wali', $app->penghasilan_ibu_wali) === '> 10 juta' ? 'selected' : '' }}>&gt; Rp 10.000.000</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-amber-50 border-b border-amber-100">
                    <h2 class="text-lg font-bold text-amber-800">Syarat Pendaftaran</h2>
                    <p class="text-sm text-amber-600 mt-1">Silakan unggah dokumen berikut agar data pendaftaran dapat diverifikasi oleh panitia.</p>
                </div>
                <div class="p-6 space-y-5">
                    @foreach($requirements as $key => $req)
                        @php
                            $uploaded = $uploadedFiles[$key] ?? null;
                            $hasFile = !is_null($uploaded);
                            $isRequired = $req['required'] && !$hasFile;
                        @endphp
                        <div class="flex flex-col sm:flex-row sm:items-start gap-3 p-4 rounded-xl border {{ $hasFile ? 'border-emerald-200 bg-emerald-50/30' : 'border-gray-200 bg-gray-50/30' }}">
                            <div class="flex-shrink-0 mt-1">
                                @if($hasFile)
                                    <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">
                                            {{ $req['label'] }}
                                            @if($isRequired)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </p>
                                        @if($hasFile)
                                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                                                <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">
                                                    Sudah diunggah
                                                </span>
                                                <span class="text-xs text-gray-500">{{ $uploaded->original_filename }}</span>
                                                @if($uploaded->fileSizeDisplay())
                                                    <span class="text-xs text-gray-400">{{ $uploaded->fileSizeDisplay() }}</span>
                                                @endif
                                                @if($uploaded->fileUrl())
                                                    <a href="{{ $uploaded->fileUrl() }}" target="_blank"
                                                       class="text-xs text-emerald-600 hover:text-emerald-700 underline font-medium">
                                                        Lihat File
                                                    </a>
                                                @endif
                                            </div>
                                        @else
                                            <p class="text-xs text-amber-600 mt-1">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 font-medium">
                                                    Belum diunggah
                                                </span>
                                            </p>
                                        @endif
                                    </div>
                                    <div class="flex-shrink-0">
                                        <input type="file" name="{{ $key }}"
                                               accept=".pdf,.jpg,.jpeg,.png"
                                               {{ $isRequired ? 'required' : '' }}
                                               class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                                    </div>
                                </div>
                                @error($key)
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endforeach
                    <div class="text-xs text-gray-400 mt-2 space-y-1">
                        <p>Format file: PDF, JPG, JPEG, PNG.</p>
                        <p>PDF maksimal 2MB. File gambar (JPG/PNG) maksimal 8MB sebelum dikompres.</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="px-8 py-3 bg-amber-400 text-emerald-950 font-bold rounded-xl hover:bg-amber-300 transition-colors shadow-lg">
                    Simpan Data
                </button>
                <a href="{{ route('spmb.info') }}" class="px-8 py-3 bg-white/10 text-white font-semibold rounded-xl hover:bg-white/20 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
