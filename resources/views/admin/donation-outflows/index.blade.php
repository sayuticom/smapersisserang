<x-admin-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Penyerahan Dana Donasi ke Keuangan</h2>
                <p class="text-sm text-gray-500 mt-1">Daftar Donasi Keluar dan status verifikasi Keuangan</p>
            </div>
            @if(auth()->user()->hasPermissionTo('donation.outflows.create'))
                <a href="{{ route('admin.donation-outflows.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 shadow-sm">
                    Tambah Donasi Keluar
                </a>
            @endif
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            @if($outflows->isEmpty())
                <div class="p-8 text-center text-sm text-gray-400">Belum ada transaksi Donasi Keluar.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left">
                            <tr>
                                <th class="px-4 py-3 text-gray-500 font-medium">Nomor</th>
                                <th class="px-4 py-3 text-gray-500 font-medium">Tanggal</th>
                                <th class="px-4 py-3 text-gray-500 font-medium">Sumber Donasi</th>
                                <th class="px-4 py-3 text-gray-500 font-medium">Nominal</th>
                                <th class="px-4 py-3 text-gray-500 font-medium">Metode</th>
                                <th class="px-4 py-3 text-gray-500 font-medium">Status</th>
                                <th class="px-4 py-3 text-gray-500 font-medium">Diinput oleh</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($outflows as $outflow)
                                @php
                                    $statusClass = match($outflow->status) {
                                        'approved' => 'bg-emerald-100 text-emerald-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        default => 'bg-amber-100 text-amber-700',
                                    };
                                    $statusLabel = match($outflow->status) {
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                        default => 'Menunggu Verifikasi',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">{{ $outflow->transaction_number }}</td>
                                    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $outflow->handover_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $outflow->donation_source }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">Rp {{ number_format($outflow->amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $outflow->handover_method === 'cash' ? 'Tunai' : 'Transfer' }}</td>
                                    <td class="px-4 py-3"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                    <td class="px-4 py-3 text-gray-500">{{ $outflow->creator?->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.donation-outflows.show', $outflow) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">Detail</a>
                                            @if($outflow->status === 'pending'
                                                && auth()->user()->isFinanceOfficer()
                                                && (int) $outflow->created_by !== (int) auth()->id()
                                                && auth()->user()->hasPermissionTo('donation.outflows.approve'))
                                                <a href="{{ route('admin.donation-outflows.show', $outflow) }}" class="inline-flex items-center rounded-lg bg-emerald-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm ring-2 ring-emerald-300 hover:bg-emerald-700">Verifikasi / ACC</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t border-gray-100">{{ $outflows->links() }}</div>
            @endif
        </div>
    </div>
</x-admin-layout>
