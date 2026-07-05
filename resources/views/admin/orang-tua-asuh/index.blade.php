<x-admin-layout>
    <div class="max-w-6xl mx-auto">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Pengajuan Orang Tua Asuh</h2>
                <p class="text-gray-500 mt-1">Daftar pengajuan program Orang Tua Asuh.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Tanggal</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Calon OTA</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Anak Asuh</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Nominal</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Durasi</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Status</th>
                            <th class="text-right px-4 py-3 font-semibold text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($submissions as $submission)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $submission->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-gray-900">
                                    <div class="font-medium">{{ $submission->donor_name }}</div>
                                    @if($submission->donor_phone)
                                        <div class="text-xs text-gray-400">{{ $submission->donor_phone }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $submission->student?->student_name ?: 'Diserahkan ke Sekolah' }}
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $submission->amount_formatted }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $submission->commitment_duration ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $submission->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                                        {{ $submission->status === 'dihubungi' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $submission->status === 'aktif' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $submission->status === 'batal' ? 'bg-red-100 text-red-700' : '' }}">
                                        {{ $submission->status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.orang-tua-asuh.show', $submission) }}"
                                       class="text-sm font-medium text-[#0F6B3A] hover:text-[#0A4F2B]">Detail</a>
                                    <form action="{{ route('admin.orang-tua-asuh.destroy', $submission) }}" method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Hapus pengajuan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ml-3 text-sm font-medium text-red-600 hover:text-red-700">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @if($submission->note)
                                <tr class="bg-slate-50">
                                    <td colspan="7" class="px-4 py-2 text-xs text-gray-500">
                                        Catatan: {{ $submission->note }}
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                                    Belum ada pengajuan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($submissions->hasPages())
                <div class="px-4 py-3 border-t border-slate-100">
                    {{ $submissions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
