@php
$followUpLabels = [
    'belum_dihubungi' => 'Belum Dihubungi', 'sudah_dihubungi' => 'Sudah Dihubungi',
    'perlu_dilengkapi' => 'Perlu Dilengkapi', 'siap_wawancara' => 'Siap Wawancara',
    'tidak_aktif' => 'Tidak Aktif', 'selesai' => 'Selesai',
];
$followUpColors = [
    'belum_dihubungi' => 'bg-slate-100 text-slate-700',
    'sudah_dihubungi' => 'bg-blue-100 text-blue-700',
    'perlu_dilengkapi' => 'bg-yellow-100 text-yellow-700',
    'siap_wawancara' => 'bg-green-100 text-green-700',
    'tidak_aktif' => 'bg-red-100 text-red-700',
    'selesai' => 'bg-emerald-100 text-emerald-700',
];

$statusLabels = [
    'baru_daftar' => 'Baru Daftar',
    'menunggu_verifikasi' => 'Menunggu Verifikasi',
    'data_kurang' => 'Data Kurang',
    'terverifikasi' => 'Terverifikasi',
    'wawancara' => 'Wawancara',
    'lulus' => 'Lulus',
    'cadangan' => 'Cadangan',
    'tidak_lulus' => 'Tidak Lulus',
    'diterima' => 'Diterima',
    'mengundurkan_diri' => 'Mengundurkan Diri',
];

$statusColors = [
    'baru_daftar' => 'bg-blue-100 text-blue-800',
    'menunggu_verifikasi' => 'bg-yellow-100 text-yellow-800',
    'data_kurang' => 'bg-orange-100 text-orange-800',
    'terverifikasi' => 'bg-green-100 text-green-800',
    'wawancara' => 'bg-purple-100 text-purple-800',
    'lulus' => 'bg-emerald-100 text-emerald-800',
    'cadangan' => 'bg-gray-100 text-gray-800',
    'tidak_lulus' => 'bg-red-100 text-red-800',
    'diterima' => 'bg-indigo-100 text-indigo-800',
    'mengundurkan_diri' => 'bg-pink-100 text-pink-800',
];

$app = $studentApplication;
@endphp

<x-admin-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('admin.ppdb.applications.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-2">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali
                </a>
                <h2 class="text-2xl font-bold text-gray-900">Detail Pendaftar</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $app->registration_number }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.ppdb.applications.print', $app) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak Data Siswa
                </a>
                <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$app->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $statusLabels[$app->status] ?? $app->status }}
                </span>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Data Siswa</h3>
            </div>
            <div class="p-4">
                @php
                    $genderLabel = $app->gender === 'laki_laki' ? 'Laki-laki' : 'Perempuan';
                @endphp
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Nama Lengkap</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $app->student_name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Nama Panggilan</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->nama_panggilan ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">No. Pendaftaran</dt>
                        <dd class="mt-1 text-sm font-mono text-gray-900">{{ $app->registration_number ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">NISN</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->nisn ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Nomor Induk Asal</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->nomor_induk_asal ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Jenis Kelamin</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $genderLabel }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Tempat Lahir</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->birth_place ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Tanggal Lahir</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->birth_date?->format('d F Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Agama</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->agama ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Anak Ke</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->anak_ke ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Status Anak dalam Keluarga</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->status_anak_dalam_keluarga ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Telepon Siswa</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->telepon_siswa ?? '-' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-400 uppercase">Alamat Siswa</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->address ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Kemampuan Baca Quran</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->quran_reading_ability ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Boarding Ready</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->boarding_ready ? 'Ya, siap boarding' : 'Tidak' }}</dd>
                    </div>
                    @if($app->health_notes)
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-400 uppercase">Catatan Kesehatan</dt>
                        <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $app->health_notes ?? '-' }}</dd>
                    </div>
                    @endif
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-400 uppercase">Motivasi</dt>
                        <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $app->motivation ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Data Sekolah Asal</h3>
            </div>
            <div class="p-4">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Nama SMP/MTs Asal</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->previous_school ?? '-' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-400 uppercase">Alamat Sekolah Asal</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->alamat_sekolah_asal ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Diterima di Kelas</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->diterima_di_kelas ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Tanggal Diterima</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->tanggal_diterima?->format('d F Y') ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Data Orang Tua Kandung</h3>
            </div>
            <div class="p-4">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Nama Ayah</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->father_name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Nama Ibu</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->mother_name ?? '-' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-400 uppercase">Alamat Ayah</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->alamat_ayah ?? '-' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-400 uppercase">Alamat Ibu</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->alamat_ibu ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">No. WhatsApp Orang Tua</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($app->parent_whatsapp)
                                <span>{{ $app->parent_whatsapp }}</span>
                                <x-whatsapp-status-button :application="$app" class="mt-1.5 px-3 py-1.5 text-xs font-medium rounded-lg gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    WA Cek Status
                                </x-whatsapp-status-button>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Pekerjaan Ayah</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->pekerjaan_ayah ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Pekerjaan Ibu</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->pekerjaan_ibu ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Pendidikan Ayah</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->pendidikan_ayah ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Pendidikan Ibu</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->pendidikan_ibu ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Penghasilan Ayah</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->penghasilan_ayah ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Penghasilan Ibu</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->penghasilan_ibu ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Data Orang Tua Wali</h3>
            </div>
            <div class="p-4">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Nama Ayah Wali</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->nama_ayah_wali ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Nama Ibu Wali</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->nama_ibu_wali ?? '-' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-400 uppercase">Alamat Ayah Wali</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->alamat_ayah_wali ?? '-' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-400 uppercase">Alamat Ibu Wali</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->alamat_ibu_wali ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Telepon Wali</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->telepon_wali ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Pekerjaan Ayah Wali</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->pekerjaan_ayah_wali ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Pekerjaan Ibu Wali</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->pekerjaan_ibu_wali ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Pendidikan Ayah Wali</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->pendidikan_ayah_wali ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Pendidikan Ibu Wali</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->pendidikan_ibu_wali ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Penghasilan Ayah Wali</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->penghasilan_ayah_wali ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Penghasilan Ibu Wali</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->penghasilan_ibu_wali ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Foto 3x4</h3>
            </div>
            <div class="p-4">
                @if($app->foto_3x4)
                    <img src="{{ asset('storage/' . $app->foto_3x4) }}" alt="Foto 3x4 {{ $app->student_name }}"
                         class="max-w-[160px] rounded-lg border border-gray-200 shadow-sm">
                @else
                    <p class="text-sm text-gray-400">-</p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Informasi Pendaftaran</h3>
            </div>
            <div class="p-4">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">No. Pendaftaran</dt>
                        <dd class="mt-1 text-sm font-mono text-gray-900">{{ $app->registration_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Boarding School</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->boarding_ready ? 'Ya, siap boarding' : 'Tidak' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Tahun Ajaran</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->admissionYear?->academic_year }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Program</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->admissionProgram?->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$app->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$app->status] ?? $app->status }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Tanggal Daftar</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->submitted_at?->format('d F Y H:i') ?: $app->created_at->format('d F Y H:i') }}</dd>
                    </div>
                    @if($app->verified_at)
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Diverifikasi Oleh</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $app->verifier?->name }} ({{ $app->verified_at->format('d F Y H:i') }})</dd>
                    </div>
                    @endif
                    @if($app->admin_notes)
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-400 uppercase">Catatan Admin</dt>
                        <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $app->admin_notes }}</dd>
                    </div>
                    @endif
                    @if($app->follow_up_status)
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Status Follow-up</dt>
                        <dd class="mt-1">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $followUpColors[$app->follow_up_status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $followUpLabels[$app->follow_up_status] ?? $app->follow_up_status }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-400 uppercase">Follow-up Terakhir</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($app->follow_up_at)
                                {{ $app->follow_up_at->format('d M Y H:i') }}
                                @if($app->followUpBy)
                                    oleh {{ $app->followUpBy->name }}
                                @endif
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Persyaratan Pendaftaran</h3>
            </div>
            <div class="p-4 space-y-3">
                @php $uploadedFiles = $app->requirementFiles->keyBy('requirement_key'); @endphp
                @foreach(\App\Models\StudentRequirementFile::$requirements as $key => $req)
                    @php $file = $uploadedFiles[$key] ?? null; @endphp
                    <div class="flex items-center justify-between gap-3 p-3 rounded-lg border {{ $file ? 'border-emerald-200 bg-emerald-50/30' : 'border-gray-200 bg-gray-50/50' }}">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            @if($file)
                                <div class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="w-5 h-5 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $req['label'] }}</p>
                                @if($file)
                                    <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 mt-0.5">
                                        <span class="text-xs text-emerald-700 font-medium">Sudah diunggah</span>
                                        @if($file->fileSizeDisplay())
                                            <span class="text-xs text-gray-400">{{ $file->fileSizeDisplay() }}</span>
                                        @endif
                                        @if($file->fileUrl())
                                            <a href="{{ $file->fileUrl() }}" target="_blank"
                                               class="text-xs text-blue-600 hover:text-blue-700 underline">Lihat File</a>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 font-medium">Belum diunggah</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Status Data Siswa</h3>
            </div>
            <div class="p-4">
                @php
                    $statusDataLabels = ['belum_lengkap' => 'Belum Lengkap', 'sudah_lengkap' => 'Sudah Lengkap', 'perlu_perbaikan' => 'Perlu Perbaikan'];
                    $statusDataColors = ['belum_lengkap' => 'bg-yellow-100 text-yellow-800', 'sudah_lengkap' => 'bg-green-100 text-green-800', 'perlu_perbaikan' => 'bg-red-100 text-red-800'];
                @endphp
                <div class="flex flex-wrap items-center gap-4 mb-4">
                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium {{ $statusDataColors[$app->status_data] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $statusDataLabels[$app->status_data] ?? $app->status_data }}
                    </span>
                    @if($app->updated_by_parent_at)
                        <span class="text-xs text-gray-400">Terakhir diperbarui: {{ $app->updated_by_parent_at->format('d M Y H:i') }}</span>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <form method="POST" action="{{ route('admin.ppdb.applications.mark-data-complete', $app) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status_data" value="{{ $app->status_data === 'sudah_lengkap' ? 'belum_lengkap' : 'sudah_lengkap' }}">
                        <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg {{ $app->status_data === 'sudah_lengkap' ? 'bg-yellow-50 text-yellow-700 border border-yellow-200 hover:bg-yellow-100' : 'bg-green-600 text-white hover:bg-green-700' }} transition-colors">
                            {{ $app->status_data === 'sudah_lengkap' ? 'Tandai Belum Lengkap' : 'Tandai Data Lengkap' }}
                        </button>
                    </form>

                    @if($app->status_data !== 'perlu_perbaikan')
                    <form method="POST" action="{{ route('admin.ppdb.applications.mark-data-complete', $app) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status_data" value="perlu_perbaikan">
                        <button type="submit" class="px-4 py-2 bg-red-50 text-red-700 border border-red-200 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors">
                            Tandai Perlu Perbaikan
                        </button>
                    </form>
                    @endif

                    <form method="POST" action="{{ route('admin.ppdb.applications.generate-update-link', $app) }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            {{ $app->update_token ? 'Buat Ulang Link' : 'Buat Link Pembaruan' }}
                        </button>
                    </form>
                </div>

                @php
                    $waPhone = $app->normalizedParentWhatsapp();
                    $waUpdateUrl = $app->updateDataUrl();
                @endphp

                @if($waPhone && $waUpdateUrl)
                    @php
                        $waMessage = "Assalamu'alaikum Bapak/Ibu.\n\n"
                            . "Kami dari Panitia SPMB SMA Persis Serang memohon bantuan Bapak/Ibu untuk melengkapi atau memperbarui data calon siswa:\n\n"
                            . "Nama: {$app->student_name}\n"
                            . "Nomor Pendaftaran: {$app->registration_number}\n\n"
                            . "Silakan klik link berikut:\n{$waUpdateUrl}\n\n"
                            . "Mohon data diisi dengan benar dan lengkap.\n\n"
                            . "Terima kasih.\nPanitia SPMB SMA Persis Serang";
                        $waUrl = 'https://wa.me/' . $waPhone . '?text=' . urlencode($waMessage);
                    @endphp
                    <div class="mt-4">
                        <a href="{{ $waUrl }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Kirim WA Perbaikan Data
                        </a>
                    </div>
                @elseif(!$waPhone)
                    <p class="mt-4 text-xs text-gray-400">Nomor WA orang tua belum tersedia.</p>
                @elseif(!$waUpdateUrl)
                    <p class="mt-4 text-xs text-gray-400">Buat link pembaruan terlebih dahulu.</p>
                @endif

                @if(session('update_link'))
                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm font-medium text-blue-800 mb-2">Link Pembaruan Data:</p>
                        <div class="flex items-center gap-2">
                            <input type="text" value="{{ session('update_link') }}" readonly
                                   class="w-full text-sm font-mono bg-white border border-blue-300 rounded-lg px-3 py-2" id="update-link-input">
                            <button onclick="copyUpdateLink()" class="px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors flex-shrink-0">
                                Salin
                            </button>
                        </div>
                    </div>
                    <script>
                        function copyUpdateLink() {
                            const input = document.getElementById('update-link-input');
                            input.select();
                            document.execCommand('copy');
                            alert('Link berhasil disalin!');
                        }
                    </script>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Catatan Follow-up</h3>
            </div>
            <div class="p-4">
                <form method="POST" action="{{ route('admin.ppdb.applications.update-follow-up', $app) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label for="follow_up_status" class="block text-sm font-medium text-gray-700 mb-1">Status Follow-up</label>
                        <select name="follow_up_status" id="follow_up_status" required
                                class="w-full sm:w-64 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                            @foreach($followUpLabels as $key => $label)
                                <option value="{{ $key }}" {{ $app->follow_up_status === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="follow_up_notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan Follow-up</label>
                        <textarea name="follow_up_notes" id="follow_up_notes" rows="3"
                                  class="w-full sm:w-96 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                                  placeholder="Catatan hasil follow-up...">{{ old('follow_up_notes', $app->follow_up_notes) }}</textarea>
                    </div>
                    @if($app->follow_up_at)
                    <div class="text-xs text-gray-400 space-y-1">
                        <p>Terakhir dihubungi: {{ $app->follow_up_at->format('d M Y H:i') }}</p>
                        @if($app->followUpBy)
                            <p>Petugas: {{ $app->followUpBy->name }}</p>
                        @endif
                    </div>
                    @endif
                    <div>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                            Simpan Follow-up
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Ubah Status</h3>
            </div>
            <div class="p-4">
                <form method="POST" action="{{ route('admin.ppdb.applications.update-status', $app) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status" required
                                class="w-full sm:w-64 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">-- Pilih Status --</option>
                            @foreach($statusLabels as $key => $label)
                                <option value="{{ $key }}" {{ $app->status === $key ? 'disabled' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Status saat ini: {{ $statusLabels[$app->status] ?? $app->status }}</p>
                    </div>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="w-full sm:w-96 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                                  placeholder="Catatan perubahan status (opsional)"></textarea>
                    </div>
                    <div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            Simpan Perubahan Status
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Riwayat Status</h3>
            </div>
            <div class="p-4">
                @if($app->statusHistories->count() > 0)
                    <div class="space-y-3">
                        @foreach($app->statusHistories->sortByDesc('created_at') as $history)
                            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0 mt-0.5">
                                    <div class="w-2 h-2 rounded-full {{ $history->to_status === $app->status ? 'bg-blue-500' : 'bg-gray-400' }}"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2">
                                        <p class="text-sm font-medium text-gray-900">
                                            @if($history->from_status)
                                                {{ $statusLabels[$history->from_status] ?? $history->from_status }}
                                                <span class="text-gray-400">&rarr;</span>
                                            @endif
                                            {{ $statusLabels[$history->to_status] ?? $history->to_status }}
                                        </p>
                                        <span class="text-xs text-gray-400">{{ $history->created_at->format('d M Y H:i') }}</span>
                                    </div>
                                    @if($history->notes)
                                        <p class="text-sm text-gray-600 mt-1">{{ $history->notes }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-1">Oleh: {{ $history->changedBy?->name ?? 'Sistem' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">Belum ada riwayat status.</p>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>