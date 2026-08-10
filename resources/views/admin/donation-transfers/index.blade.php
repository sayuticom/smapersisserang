<x-admin-layout>
    <div class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Mutasi Dana Donasi</h2>
                <p class="text-sm text-gray-500 mt-1">Daftar pemindahan dana dari akun Donasi ke akun Keuangan dan status verifikasi</p>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row lg:shrink-0">
                @if(auth()->user()->hasPermissionTo('donation.balance.view'))
                    <a href="{{ route('admin.donation.dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 whitespace-nowrap border border-emerald-200 text-emerald-700 text-sm font-medium rounded-lg hover:bg-emerald-50 shadow-sm">
                        Lihat Dashboard Donasi
                    </a>
                @endif
                @if(auth()->user()->hasPermissionTo('donation.transfers.create'))
                    <a href="{{ route('admin.donation-transfers.create') }}" class="inline-flex items-center justify-center px-4 py-2 whitespace-nowrap bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 shadow-sm">
                        Tambah Mutasi Dana
                    </a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif

        @php
            $bulanNama = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $filteredDate = request('date');
            $filterType = request('filter_type');
            $statusFilter = request('status');
            $statusLabels = ['pending' => 'Menunggu Verifikasi', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
            $activeFilters = [];
            if ($filteredDate) {
                $d = \Carbon\Carbon::parse($filteredDate);
                if ($filterType === 'month') {
                    $activeFilters[] = 'Menampilkan data bulan ' . $bulanNama[$d->month] . ' ' . $d->year;
                } else {
                    $activeFilters[] = 'Menampilkan data tanggal ' . $d->day . ' ' . $bulanNama[$d->month] . ' ' . $d->year;
                }
            }
            if ($statusFilter) {
                $activeFilters[] = 'Status: ' . ($statusLabels[$statusFilter] ?? $statusFilter);
            }
            if (request('search')) {
                $activeFilters[] = 'Pencarian: "' . request('search') . '"';
            }
        @endphp

        <form method="GET" action="{{ route('admin.donation-transfers.index') }}"
              class="mb-4 space-y-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm xl:p-5">
            @if($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:flex lg:items-end">
                <div class="w-full lg:w-72 lg:shrink-0">
                    <label class="mb-1 block text-xs font-medium text-slate-600">Tanggal</label>
                    <input type="date" name="date" value="{{ old('date', request('date')) }}"
                           class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" name="filter_type" value="date"
                        class="h-11 w-full whitespace-nowrap rounded-xl bg-emerald-700 px-5 text-sm font-semibold text-white hover:bg-emerald-800 sm:w-auto lg:w-auto lg:shrink-0">
                    Filter Tanggal
                </button>
                <button type="submit" name="filter_type" value="month"
                        class="h-11 w-full whitespace-nowrap rounded-xl bg-emerald-700 px-5 text-sm font-semibold text-white hover:bg-emerald-800 sm:w-auto lg:w-auto lg:shrink-0">
                    Filter Bulan
                </button>
                <a href="{{ route('admin.donation-transfers.index') }}"
                   class="flex h-11 w-full items-center justify-center whitespace-nowrap rounded-xl border border-slate-300 px-5 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:w-auto lg:w-auto lg:shrink-0">
                    Reset
                </a>
            </div>

            <div class="grid grid-cols-1 items-end gap-3 md:grid-cols-2 lg:grid-cols-[minmax(0,1fr)_200px_auto]">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Cari</label>
                    <input type="text" name="search" value="{{ old('search', request('search')) }}"
                           placeholder="Nomor, akun asal, akun tujuan, keterangan"
                           class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Status</label>
                    <select name="status"
                            class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Semua Status</option>
                        <option value="pending" @selected(old('status', request('status')) === 'pending')>Menunggu Verifikasi</option>
                        <option value="approved" @selected(old('status', request('status')) === 'approved')>Disetujui</option>
                        <option value="rejected" @selected(old('status', request('status')) === 'rejected')>Ditolak</option>
                    </select>
                </div>
                <button type="submit"
                        class="h-11 w-full whitespace-nowrap rounded-xl bg-emerald-700 px-6 text-sm font-semibold text-white hover:bg-emerald-800 md:w-auto lg:w-auto lg:shrink-0">
                    Terapkan
                </button>
            </div>
        </form>

        @if($activeFilters)
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm text-emerald-700">
                {{ implode(' &bull; ', $activeFilters) }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            @if($transfers->isEmpty())
                <div class="p-8 text-center text-sm text-gray-400">Belum ada transaksi Mutasi Dana.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left">
                            <tr>
                                <th class="px-4 py-3 text-gray-500 font-medium">Nomor</th>
                                <th class="px-4 py-3 text-gray-500 font-medium">Tanggal</th>
                                <th class="px-4 py-3 text-gray-500 font-medium">Dari Akun Donasi</th>
                                <th class="px-4 py-3 text-gray-500 font-medium">Ke Akun Keuangan</th>
                                <th class="px-4 py-3 text-gray-500 font-medium">Nominal</th>
                                <th class="px-4 py-3 text-gray-500 font-medium">Status</th>
                                <th class="px-4 py-3 text-gray-500 font-medium">Diinput oleh</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($transfers as $transfer)
                                @php
                                    $statusClass = match($transfer->status) {
                                        'approved' => 'bg-emerald-100 text-emerald-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        default => 'bg-amber-100 text-amber-700',
                                    };
                                    $statusLabel = match($transfer->status) {
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                        default => 'Menunggu Verifikasi',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">{{ $transfer->transfer_number }}</td>
                                    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $transfer->transfer_date?->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $transfer->fromAccount?->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $transfer->toAccount?->name ?? '-' }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">Rp {{ number_format($transfer->amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                    <td class="px-4 py-3 text-gray-500">{{ $transfer->requester?->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.donation-transfers.show', $transfer) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">Detail</a>
                                            @if($transfer->status === 'pending'
                                                && auth()->user()->isFinanceOfficer()
                                                && (int) $transfer->requested_by !== (int) auth()->id()
                                                && auth()->user()->hasPermissionTo('donation.transfers.approve'))
                                                <a href="{{ route('admin.donation-transfers.show', $transfer) }}" class="inline-flex items-center rounded-lg bg-emerald-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm ring-2 ring-emerald-300 hover:bg-emerald-700">Verifikasi / ACC</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t border-gray-100">{{ $transfers->links() }}</div>
            @endif
        </div>
    </div>
</x-admin-layout>
