<x-admin-layout>
    <div class="max-w-6xl mx-auto">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Pengajuan Orang Tua Asuh</h2>
                <p class="text-gray-500 mt-1">Daftar pengajuan donasi program Orang Tua Asuh.</p>
            </div>
            <a href="{{ route('admin.orang-tua-asuh.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                &larr; Kembali ke Data Murid
            </a>
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
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Anak Asuh</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Donatur</th>
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
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    {{ $submission->fosterStudent?->name ?: 'Diserahkan ke Sekolah' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    <div>{{ $submission->donor_name }}</div>
                                    @if($submission->donor_phone)
                                        <div class="text-xs text-gray-400">{{ $submission->donor_phone }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $submission->amount_formatted }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $submission->commitment_duration }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $submission->payment_status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                                        {{ $submission->payment_status === 'paid' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $submission->payment_status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                        {{ $submission->payment_status === 'pending' ? 'Menunggu' : ($submission->payment_status === 'paid' ? 'Lunas' : 'Batal') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($submission->payment_status === 'pending')
                                        <form action="{{ route('admin.orang-tua-asuh.submissions.mark-paid', $submission) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-sm font-medium text-green-600 hover:text-green-700"
                                                    onclick="return confirm('Tandai pembayaran ini sebagai lunas?')">Tandai Lunas</button>
                                        </form>
                                        <form action="{{ route('admin.orang-tua-asuh.submissions.mark-cancelled', $submission) }}" method="POST" class="inline ml-2">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700"
                                                    onclick="return confirm('Batalkan pengajuan ini?')">Batal</button>
                                        </form>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
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
