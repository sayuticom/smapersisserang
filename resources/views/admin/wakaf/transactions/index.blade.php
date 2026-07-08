@php
    $title = 'Wakaf Masuk';
@endphp
<x-admin-layout>
<div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Wakaf Masuk</h1>
            <p class="mt-1 text-sm text-slate-500">Data transaksi wakaf uang.</p>
        </div>
        <a href="{{ route('admin.wakaf.transactions.create-receipt') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/>
            </svg>
            Buat Bukti Penerimaan
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Wakaf</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">Rp{{ number_format($totalAmount, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Jumlah Transaksi</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">{{ $totalCount }}</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.wakaf.transactions.index') }}" class="mb-6 flex flex-wrap gap-3">
        <input type="date" name="date" value="{{ request('date') }}"
               class="rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
        <input type="month" name="month" value="{{ request('month') }}"
               class="rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
        <button type="submit" class="rounded-xl bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Filter</button>
        <a href="{{ route('admin.wakaf.transactions.index') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Reset</a>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Order ID</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Wakif</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">WhatsApp</th>
                        <th class="text-right px-4 py-3 font-semibold text-slate-600">Nominal</th>
                        <th class="text-right px-4 py-3 font-semibold text-slate-600">Admin</th>
                        <th class="text-right px-4 py-3 font-semibold text-slate-600">Total</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">Status</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Tanggal</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono text-xs">{{ $tx->order_id }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $tx->wakif_name ?: 'Hamba Allah' }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $tx->wakif_whatsapp ?: '-' }}</td>
                            <td class="px-4 py-3 text-right">Rp{{ number_format($tx->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-amber-600">Rp{{ number_format($tx->admin_fee, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-semibold">Rp{{ number_format($tx->total_transfer, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $statusClasses = [
                                        'paid' => 'bg-emerald-100 text-emerald-700',
                                        'pending' => 'bg-amber-100 text-amber-700',
                                        'cancelled' => 'bg-red-100 text-red-700',
                                        'expire' => 'bg-gray-100 text-gray-600',
                                    ];
                                    $statusLabel = [
                                        'paid' => 'Lunas',
                                        'pending' => 'Pending',
                                        'cancelled' => 'Batal',
                                        'expire' => 'Kadaluarsa',
                                    ];
                                @endphp
                                <span class="inline-block rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses[$tx->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $statusLabel[$tx->status] ?? $tx->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-500 text-xs">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.wakaf.transactions.show', $tx) }}"
                                   class="text-emerald-600 hover:text-emerald-500 text-xs font-semibold">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-sm text-slate-400">Belum ada transaksi wakaf.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
            <div class="border-t border-slate-200 px-4 py-3">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
</x-admin-layout>
