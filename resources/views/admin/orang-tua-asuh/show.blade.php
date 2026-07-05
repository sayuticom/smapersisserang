<x-admin-layout>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.orang-tua-asuh.index') }}" class="text-sm text-[#0F6B3A] hover:text-[#0A4F2B]">&larr; Kembali</a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">Detail Pengajuan</h2>
        </div>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Nama Calon OTA</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $submission->donor_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Nomor WhatsApp</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">
                            {{ $submission->donor_phone }}
                            @if($submission->donor_phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $submission->donor_phone) }}"
                                   target="_blank"
                                   class="ml-2 inline-flex items-center gap-1 text-xs font-medium text-green-600 hover:text-green-700">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                    Hubungi
                                </a>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Anak Asuh</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">
                            {{ $submission->student?->student_name ?: 'Diserahkan ke Sekolah' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Tanggal Pengajuan</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $submission->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Nominal per Bulan</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $submission->amount_formatted }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Komitmen Durasi</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $submission->commitment_duration ?: 'Belum ditentukan' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Status</p>
                        <div class="mt-2 flex items-center gap-3">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium
                                {{ $submission->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                                {{ $submission->status === 'dihubungi' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $submission->status === 'aktif' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $submission->status === 'batal' ? 'bg-red-100 text-red-700' : '' }}">
                                {{ $submission->status_label }}
                            </span>

                            <form method="POST" action="{{ route('admin.orang-tua-asuh.update-status', $submission) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="text-sm rounded-lg border border-gray-300 px-2 py-1.5 focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
                                    <option value="pending" {{ $submission->status === 'pending' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="dihubungi" {{ $submission->status === 'dihubungi' ? 'selected' : '' }}>Perlu Dihubungi</option>
                                    <option value="aktif" {{ $submission->status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="batal" {{ $submission->status === 'batal' ? 'selected' : '' }}>Batal</option>
                                </select>
                                <button type="submit"
                                        class="px-3 py-1.5 bg-[#0F6B3A] text-white text-xs font-medium rounded-lg hover:bg-[#0A4F2B] transition-colors">
                                    Ubah
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                @if($submission->note)
                    <div class="pt-4 border-t border-slate-100">
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Catatan</p>
                        <p class="text-sm text-gray-700 bg-slate-50 rounded-lg p-3">{{ $submission->note }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <a href="{{ route('admin.orang-tua-asuh.index') }}"
               class="px-6 py-2.5 text-sm font-medium text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Kembali
            </a>
            <form action="{{ route('admin.orang-tua-asuh.destroy', $submission) }}" method="POST"
                  onsubmit="return confirm('Hapus pengajuan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-6 py-2.5 text-sm font-medium text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                    Hapus Pengajuan
                </button>
            </form>
        </div>
    </div>
</x-admin-layout>
