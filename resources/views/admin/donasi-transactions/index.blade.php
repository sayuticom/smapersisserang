<x-admin-layout>
    <div class="max-w-6xl mx-auto">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Donasi Masuk</h2>
                <p class="text-gray-500 mt-1">Daftar transaksi donasi pendidikan & makan santri.</p>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                <a href="{{ route('admin.donasi-transactions.create-receipt') }}"
                   class="inline-flex items-center justify-center rounded-lg bg-green-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-green-800">
                    Buat Bukti Penerimaan
                </a>
                <a href="{{ route('admin.website.donasi-pendidikan.edit') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                    &larr; Pengaturan Donasi
                </a>
            </div>
        </div>

        <div class="mb-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                <p class="text-xs font-medium text-emerald-600 uppercase tracking-wider">Terkonfirmasi</p>
                <p class="mt-1 text-2xl font-bold text-emerald-700">Rp{{ number_format($totalPaid, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-xs font-medium text-amber-600 uppercase tracking-wider">Menunggu</p>
                <p class="mt-1 text-2xl font-bold text-amber-700">{{ $totalPending }} transaksi</p>
            </div>
            <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                <p class="text-xs font-medium text-red-600 uppercase tracking-wider">Dibatalkan</p>
                <p class="mt-1 text-2xl font-bold text-red-700">{{ $totalCancelled }} transaksi</p>
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
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Referensi</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Tanggal</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Donatur</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Nominal</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Status</th>
                            <th class="text-right px-4 py-3 font-semibold text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-mono text-xs text-gray-500 whitespace-nowrap">
                                    {{ $tx->order_id }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $tx->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-gray-900">
                                    <div class="font-medium">{{ $tx->donor_name }}</div>
                                    @if($tx->donor_whatsapp)
                                        <div class="text-xs text-gray-400">{{ $tx->donor_whatsapp }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                    Rp{{ number_format($tx->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $tx->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                                        {{ $tx->status === 'paid' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $tx->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                        {{ $tx->status === 'pending' ? 'Menunggu' : ($tx->status === 'paid' ? 'Lunas' : 'Dibatalkan') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    @if($tx->status === 'pending')
                                        <form action="{{ route('admin.donasi-transactions.mark-paid', $tx) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-sm font-medium text-green-600 hover:text-green-700"
                                                    onclick="return confirm('Tandai donasi ini sebagai Lunas? Pastikan pembayaran sudah diverifikasi.')">Tandai Lunas</button>
                                        </form>
                                        <form action="{{ route('admin.donasi-transactions.mark-cancelled', $tx) }}" method="POST" class="inline ml-2">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700"
                                                    onclick="return confirm('Batalkan donasi ini?')">Batal</button>
                                        </form>
                                    @elseif($tx->status === 'paid')
                                        <span class="text-xs text-green-600 font-medium">
                                            {{ $tx->paid_at ? 'Lunas ' . $tx->paid_at->format('d/m/Y') : '' }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                            @if($tx->note)
                                <tr class="bg-slate-50">
                                    <td colspan="6" class="px-4 py-2 text-xs text-gray-500">
                                        Catatan: {{ $tx->note }}
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-gray-500">
                                    Belum ada donasi masuk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())
                <div class="px-4 py-3 border-t border-slate-100">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
