<x-admin-layout>
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
                                <th class="px-4 py-2.5 text-gray-500 font-medium hidden sm:table-cell">Metode</th>
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
                                    @if($income->donationOutflow)
                                        <span class="mt-1 block w-fit rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">Dari Donasi Keluar</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-blue-600 font-medium whitespace-nowrap">Rp {{ number_format($income->amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-2.5 text-gray-600 hidden sm:table-cell">{{ $income->payment_method }}</td>
                                <td class="px-4 py-2.5 text-gray-600 hidden md:table-cell max-w-[180px]">
                                    <span class="block truncate">{{ $income->source_name ?: '-' }}</span>
                                    @if($income->donationOutflow)
                                        <a href="{{ route('admin.donation-outflows.show', $income->donationOutflow) }}" class="block text-xs font-medium text-emerald-700 hover:underline">{{ $income->donationOutflow->transaction_number }}</a>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-gray-400 text-xs hidden lg:table-cell">{{ $income->creator?->name }}</td>
                                <td class="px-4 py-2.5">
                                    @if(!$income->donation_outflow_id)
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.finance.incomes.edit', $income) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Edit</a>
                                            <form method="POST" action="{{ route('admin.finance.incomes.destroy', $income) }}" onsubmit="return confirm('Hapus pemasukan ini?')" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Hapus</button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">Terkunci</span>
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
