@php
    $title = 'Detail Wakaf - ' . $transaction->order_id;
@endphp
<x-admin-layout>
<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Detail Wakaf</h1>
            <p class="mt-1 text-sm text-slate-500">Detail transaksi wakaf uang.</p>
        </div>
        <a href="{{ route('admin.wakaf.transactions.index') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
            &larr; Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm" id="receipt-card">
        <div class="text-center border-b border-slate-100 pb-5">
            @php
                $schoolSetting = \App\Models\SchoolSetting::current();
            @endphp
            @if($schoolSetting?->logo_path)
                <img src="{{ asset('storage/' . $schoolSetting->logo_path) }}"
                     class="mx-auto h-16 w-16 rounded-xl border border-slate-200 bg-white object-contain p-1.5 shadow-sm">
            @endif
            <h2 class="mt-3 text-lg font-bold text-slate-900">{{ $schoolSetting->school_name ?? 'SMA Persis Serang' }}</h2>
            <p class="text-xs text-slate-500">Bukti Penerimaan Wakaf Uang</p>
        </div>

        <div class="mt-5 space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-slate-500">No. Bukti</span>
                <span class="font-mono font-semibold text-slate-900">{{ $transaction->order_id }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Tanggal</span>
                <span class="text-slate-900">{{ $transaction->paid_at ? $transaction->paid_at->format('d/m/Y H:i') : $transaction->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="border-t border-slate-100 pt-3"></div>
            <div class="flex justify-between">
                <span class="text-slate-500">Nama Wakif</span>
                <span class="font-semibold text-slate-900">{{ $transaction->wakif_name ?: 'Hamba Allah' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">WhatsApp</span>
                <span class="text-slate-900">{{ $transaction->wakif_whatsapp ?: '-' }}</span>
            </div>
            <div class="border-t border-slate-100 pt-3"></div>
            <div class="flex justify-between">
                <span class="text-slate-500">Nominal Wakaf</span>
                <span class="font-semibold text-slate-900">Rp{{ number_format($transaction->amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Biaya Admin (0,6%)</span>
                <span class="text-slate-900">Rp{{ number_format($transaction->admin_fee, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Kode Unik</span>
                <span class="text-slate-900">{{ str_pad((string) $transaction->unique_code, 3, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="flex justify-between text-base font-bold">
                <span class="text-slate-700">Total Transfer</span>
                <span class="text-emerald-700">Rp{{ number_format($transaction->total_transfer, 0, ',', '.') }}</span>
            </div>
            <div class="border-t border-slate-100 pt-3"></div>
            <div class="flex justify-between">
                <span class="text-slate-500">Status</span>
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
                <span class="inline-block rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses[$transaction->status] ?? 'bg-gray-100 text-gray-600' }}">
                    {{ $statusLabel[$transaction->status] ?? $transaction->status }}
                </span>
            </div>
            @if($transaction->note)
                <div class="border-t border-slate-100 pt-3"></div>
                <div>
                    <span class="text-slate-500 text-xs">Catatan:</span>
                    <p class="mt-1 text-slate-700 whitespace-pre-line text-xs">{{ $transaction->note }}</p>
                </div>
            @endif
        </div>

        <div class="mt-6 pt-4 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-400">Terima kasih telah berwakaf. Semoga menjadi amal jariyah yang terus mengalir pahalanya.</p>
        </div>
    </div>

    <div class="mt-6 flex flex-wrap gap-3">
        @if($transaction->status !== 'paid')
            <form method="POST" action="{{ route('admin.wakaf.transactions.mark-paid', $transaction) }}">
                @csrf
                @method('PATCH')
                <button type="submit"
                        class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500">
                    Tandai Lunas
                </button>
            </form>
        @endif

        @if($transaction->status !== 'cancelled')
            <form method="POST" action="{{ route('admin.wakaf.transactions.mark-cancelled', $transaction) }}">
                @csrf
                @method('PATCH')
                <button type="submit"
                        class="rounded-xl border border-red-300 px-5 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50"
                        onclick="return confirm('Batalkan transaksi ini?')">
                    Tandai Batal
                </button>
            </form>
        @endif

        @if($transaction->wakif_whatsapp)
            @php
                $waNumber = preg_replace('/[^0-9]/', '', $transaction->wakif_whatsapp);
                if (substr($waNumber, 0, 1) === '0') {
                    $waNumber = '62' . substr($waNumber, 1);
                }
                $thankYouMessage = "Assalamu'alaikum.\n\n"
                    . "Terima kasih, wakaf uang Anda sebesar Rp" . number_format($transaction->total_transfer, 0, ',', '.')
                    . " telah kami terima.\n\n"
                    . "Semoga Allah menerima dan menjadikannya amal jariyah bagi Anda.\n\n"
                    . "Bukti penerimaan: No. " . $transaction->order_id . "\n\n"
                    . "Wassalamu'alaikum.";
                $waUrl = 'https://wa.me/' . $waNumber . '?text=' . urlencode($thankYouMessage);
            @endphp
            <a href="{{ $waUrl }}" target="_blank"
               class="inline-flex items-center gap-2 rounded-xl border border-emerald-300 bg-emerald-50 px-5 py-2.5 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                Kirim ke WhatsApp Wakif
            </a>
        @endif
    </div>
</div>
</x-admin-layout>
