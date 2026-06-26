@extends('layouts.public')

@section('content')
<div class="bg-white">
    <div class="relative bg-gradient-to-br from-emerald-700 via-emerald-600 to-emerald-800">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PGNpcmNsZSBjeD0iMzAiIGN5PSIzMCIgcj0iMiIvPjwvZz48L2c+PC9zdmc+')] opacity-40"></div>
        <div class="relative max-w-7xl mx-auto px-4 py-16 sm:py-24 text-center">
            @if($admissionYear)
                @php
                    $badgeLabels = [
                        'open' => 'SPMB ' . $admissionYear->academic_year . ' Dibuka',
                        'almost_full' => 'Kuota Hampir Penuh',
                        'quota_full' => 'Kuota Penuh',
                        'closed' => 'Pendaftaran Ditutup',
                        'draft' => 'SPMB Belum Dibuka',
                        'announcement' => 'Masa Pengumuman',
                        'archived' => 'SPMB Tidak Aktif',
                    ];
                    $badgeLabel = $badgeLabels[$admissionYear->status] ?? 'SPMB';
                @endphp
                <div class="inline-flex items-center gap-2 rounded-full border border-amber-300/70 bg-emerald-950/60 px-4 py-2 text-sm font-semibold text-amber-300 shadow-lg shadow-emerald-950/20 backdrop-blur mb-6">
                    <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                    {{ $badgeLabel }}
                </div>
            @endif
            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">Informasi SPMB</h1>
            <p class="text-emerald-100 text-sm sm:text-base max-w-xl mx-auto">
                @if($admissionYear)
                    {{ $admissionYear->name ?? 'Seleksi Penerimaan Murid Baru ' . $admissionYear->academic_year }}
                @else
                    Sistem Penerimaan Murid Baru SMA Persis Serang
                @endif
            </p>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-12 sm:py-16 space-y-10">
        @if($admissionYear && $admissionStats)
            <div class="grid gap-5 sm:grid-cols-3">
                <div class="rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-white p-6 text-center shadow-sm">
                    <div class="text-3xl font-bold text-emerald-700">{{ $admissionStats['totalApplicants'] }}</div>
                    <p class="mt-1 text-sm font-medium text-gray-600">Total Pendaftar</p>
                </div>
                <div class="rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-white p-6 text-center shadow-sm">
                    <div class="text-3xl font-bold text-emerald-700">{{ $admissionStats['totalAccepted'] }}</div>
                    <p class="mt-1 text-sm font-medium text-gray-600">Diterima</p>
                </div>
                <div class="rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-white p-6 text-center shadow-sm">
                    <div class="text-3xl font-bold text-emerald-700">{{ $admissionStats['remainingQuota'] }}</div>
                    <p class="mt-1 text-sm font-medium text-gray-600">Sisa Kuota</p>
                </div>
            </div>
        @endif

        @if($programs->isNotEmpty())
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-5">Program Pendaftaran</h2>
                <div class="grid gap-5 sm:grid-cols-2">
                    @foreach($programs as $program)
                        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $program->name }}</h3>
                                    <span class="inline-flex items-center mt-1 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-0.5 text-xs font-medium text-emerald-700">
                                        @if($program->is_free_program)
                                            Program Khusus
                                        @else
                                            SPMB Reguler
                                        @endif
                                    </span>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-emerald-700">{{ $program->quota }}</div>
                                    <p class="text-xs text-gray-500">Kuota</p>
                                </div>
                            </div>
                            <div class="mt-4 space-y-1 text-sm text-gray-600">
                                @if($program->tuition_fee)
                                    <p>Biaya Pendidikan: Rp{{ number_format($program->tuition_fee, 0, ',', '.') }}</p>
                                @endif
                                @if($program->boarding_fee)
                                    <p>Biaya Asrama: Rp{{ number_format($program->boarding_fee, 0, ',', '.') }}</p>
                                @endif
                                @if($program->meal_fee)
                                    <p>Biaya Makan: Rp{{ number_format($program->meal_fee, 0, ',', '.') }}</p>
                                @endif
                                @if($program->registration_fee)
                                    <p>Biaya Pendaftaran: Rp{{ number_format($program->registration_fee, 0, ',', '.') }}</p>
                                @endif
                                @if($program->is_free_program)
                                    <p class="text-emerald-600 font-medium">Gratis biaya pendidikan, asrama, dan makan</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="rounded-2xl bg-gray-50 border border-gray-200 p-10 text-center">
                <p class="text-gray-500 font-medium">Belum ada program pendaftaran yang tersedia.</p>
                <p class="text-gray-400 text-sm mt-1">Silakan hubungi panitia SPMB untuk informasi lebih lanjut.</p>
            </div>
        @endif

        @if($admissionYear)
            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-2xl p-8 text-center border border-emerald-200">
                <h3 class="text-lg font-bold text-emerald-800 mb-2">Siap Bergabung?</h3>
                <p class="text-sm text-emerald-600 mb-6">
                    @if(in_array($admissionYear->status, ['open', 'almost_full']))
                        Daftarkan diri Anda sekarang juga melalui form pendaftaran online.
                    @else
                        Silakan hubungi tim SPMB kami untuk informasi lebih lanjut.
                    @endif
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    @if(in_array($admissionYear->status, ['open', 'almost_full']))
                        <a href="{{ route('spmb.create') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                            Daftar SPMB
                        </a>
                    @endif
                    <a href="{{ route('spmb.status.form') }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-white text-emerald-700 text-sm font-medium rounded-lg hover:bg-emerald-50 transition-colors border border-emerald-200">
                        Cek Status
                    </a>
                    <a href="https://wa.me/62{{ preg_replace('/^0/', '', $schoolSetting->phone ?? '') }}"
                       target="_blank"
                       class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-emerald-100 text-emerald-700 text-sm font-medium rounded-lg hover:bg-emerald-200 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Konsultasi
                    </a>
                </div>
            </div>
        @else
            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-2xl p-8 text-center border border-emerald-200">
                <h3 class="text-lg font-bold text-emerald-800 mb-2">Informasi SPMB Belum Tersedia</h3>
                <p class="text-sm text-emerald-600">Saat ini belum ada informasi mengenai SPMB. Silakan hubungi sekolah untuk informasi lebih lanjut.</p>
            </div>
        @endif
    </div>
</div>
@endsection