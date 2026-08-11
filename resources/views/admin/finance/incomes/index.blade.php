<x-admin-layout>
    @php
        $currentUser = auth()->user();
        $canManageManualIncome = $currentUser->isAdmin() || $currentUser->hasRole('staf_keuangan');
        $canEditManualIncome = $canManageManualIncome && $currentUser->hasPermissionTo('finance.transactions.manage');
        $canDeleteManualIncome = $currentUser->isSuperadmin();
        $canManageIntegratedIncome = $currentUser->isSuperadmin();
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Pemasukan</h2>
                <p class="text-sm text-gray-500 mt-1">Catat pemasukan keuangan sekolah</p>
            </div>
            <a href="{{ route('admin.finance.incomes.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Pemasukan
            </a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            @if($incomes->isEmpty())
                <div class="p-6 text-center text-gray-400 text-sm">Belum ada pemasukan. Klik "Tambah Pemasukan" untuk mencatat.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left">
                            <tr>
                                <th class="px-4 py-2.5 text-gray-500 font-medium">Tanggal</th>
                                <th class="px-4 py-2.5 text-gray-500 font-medium">Jenis</th>
                                <th class="px-4 py-2.5 text-gray-500 font-medium">Nominal</th>
                                <th class="px-4 py-2.5 text-gray-500 font-medium hidden sm:table-cell">Akun Keuangan</th>
                                <th class="px-4 py-2.5 text-gray-500 font-medium hidden md:table-cell">Sumber</th>
                                <th class="px-4 py-2.5 text-gray-500 font-medium hidden lg:table-cell">Dicatat oleh</th>
                                <th class="px-4 py-2.5 text-gray-500 font-medium"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($incomes as $income)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2.5 text-gray-900 whitespace-nowrap">{{ $income->date->format('d/m/Y') }}</td>
                                <td class="px-4 py-2.5">
                                    <span class="font-medium text-gray-900">{{ $income->income_type }}</span>
                                    @if($income->donationTransfer)
                                        <span class="mt-1 block w-fit rounded-full bg-blue-100 px-2 py-0.5 text-[11px] font-semibold text-blue-700">Mutasi Donasi</span>
                                    @elseif($income->donationOutflow)
                                        <span class="mt-1 block w-fit rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">Dari Donasi Keluar</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-blue-600 font-medium whitespace-nowrap">Rp {{ number_format($income->amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-2.5 text-gray-600 hidden sm:table-cell">
                                    <span class="block">{{ $income->financeAccount?->name ?? $income->payment_method }}</span>
                                    @if($income->payment_method && $income->financeAccount)
                                        <span class="text-[11px] text-gray-400">{{ $income->payment_method }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-gray-600 hidden md:table-cell max-w-[180px]">
                                    <span class="block truncate">{{ $income->source_name ?: '-' }}</span>
                                    @if($income->donationOutflow)
                                        <a href="{{ route('admin.donation-outflows.show', $income->donationOutflow) }}" class="block text-xs font-medium text-emerald-700 hover:underline">{{ $income->donationOutflow->transaction_number }}</a>
                                    @elseif($income->donationTransfer)
                                        <a href="{{ route('admin.donation-transfers.show', $income->donationTransfer) }}" class="block text-xs font-medium text-blue-700 hover:underline">{{ $income->donationTransfer->transfer_number }}</a>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-xs hidden lg:table-cell">
                                    @if($income->donationOutflow)
                                        <div class="text-gray-600">
                                            <span class="font-medium">Diinput oleh:</span> {{ $income->donationOutflow->creator?->name ?? 'Pengguna tidak tersedia' }}
                                        </div>
                                        <div class="text-gray-600 mt-0.5">
                                            <span class="font-medium">Diverifikasi oleh:</span> {{ $income->creator?->name ?? '-' }}
                                        </div>
                                    @elseif($income->donationTransfer)
                                        <div class="text-gray-600">
                                            <span class="font-medium">Diajukan oleh:</span> {{ $income->donationTransfer->requester?->name ?? '-' }}
                                        </div>
                                        <div class="text-gray-600 mt-0.5">
                                            <span class="font-medium">Diverifikasi oleh:</span> {{ $income->creator?->name ?? '-' }}
                                        </div>
                                    @else
                                        <span class="text-gray-400">{{ $income->creator?->name ?? '-' }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5">
                                    @if($income->donationTransfer)
                                        <span class="text-xs font-medium text-gray-400">Read-only</span>
                                        <a href="{{ route('admin.donation-transfers.show', $income->donationTransfer) }}" class="text-xs font-medium text-blue-700 hover:underline">Lihat Mutasi</a>
                                    @elseif(!$income->donation_outflow_id)
                                        <div class="flex items-center gap-2">
                                            @if($canEditManualIncome)
                                                <a href="{{ route('admin.finance.incomes.edit', $income) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Edit</a>
                                            @endif
                                            @if($canDeleteManualIncome)
                                                <form method="POST" action="{{ route('admin.finance.incomes.destroy', $income) }}" onsubmit="return confirm('Yakin ingin menghapus Pemasukan ini? Saldo dan laporan Keuangan akan berubah.')" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Hapus</button>
                                                </form>
                                            @endif
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2">
                                            @if($canManageIntegratedIncome)
                                                <a href="{{ route('admin.finance.incomes.edit', $income) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Edit</a>
                                                <form method="POST" action="{{ route('admin.finance.incomes.destroy', $income) }}" onsubmit="return confirm('Pemasukan ini berasal dari Donasi Keluar. Menghapusnya juga akan menghapus Donasi Keluar dan riwayat status terkait. Saldo Donasi dan laporan Keuangan akan berubah. Lanjutkan?')" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Hapus</button>
                                                </form>
                                            @endif
                                            <a href="{{ route('admin.donation-outflows.show', $income->donationOutflow) }}" class="text-xs font-medium text-emerald-700 hover:underline">Lihat Donasi Keluar</a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $incomes->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
