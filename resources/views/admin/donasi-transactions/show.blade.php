<x-admin-layout>
    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Bukti Penerimaan Donasi</h2>
                <p class="mt-1 text-gray-500">Bukti resmi setelah pembayaran diverifikasi admin.</p>
            </div>
            <a href="{{ route('admin.donasi-transactions.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                &larr; Donasi Masuk
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('regular_donor_message'))
            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                {{ session('regular_donor_message') }}
            </div>
        @endif

        <div id="receipt-print-area" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="border-b border-slate-200 pb-5">
                <p class="text-sm font-semibold uppercase tracking-wider text-green-700">SMA Persis Serang</p>
                <h3 class="mt-2 text-2xl font-bold text-gray-900">Bukti Penerimaan Donasi Pendidikan</h3>
                <p class="mt-1 text-sm text-gray-500">Nomor Bukti: {{ $receipt['receipt_number'] }}</p>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-lg bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tanggal Penerimaan</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ $receipt['received_at']->format('d/m/Y H:i') }}</p>
                </div>
                <div class="rounded-lg bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Status</p>
                    <p class="mt-1 font-semibold text-green-700">{{ $transaction->isPaid() ? 'Terverifikasi / Lunas' : ucfirst($receipt['status']) }}</p>
                </div>
                <div class="rounded-lg bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Nama Donatur</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ $receipt['donor_name'] }}</p>
                </div>
                <div class="rounded-lg bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Nomor WhatsApp</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ $receipt['donor_whatsapp'] }}</p>
                </div>
                <div class="rounded-lg bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Nominal Donasi</p>
                    <p class="mt-1 font-semibold text-gray-900">Rp{{ number_format($receipt['nominal_amount'], 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kode Unik</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ $receipt['unique_code'] }}</p>
                </div>
                <div class="rounded-lg bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Transfer</p>
                    <p class="mt-1 font-semibold text-gray-900">Rp{{ number_format($receipt['total_transfer'], 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tanggal Transfer</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ $receipt['transfer_date'] }}</p>
                </div>
            </div>

            <div class="mt-4 rounded-lg bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Catatan</p>
                <p class="mt-1 text-gray-900">{{ $receipt['note'] }}</p>
            </div>

            <div class="mt-8 border-t border-slate-200 pt-5 text-sm leading-6 text-gray-600">
                <p>Donasi ini telah dicatat oleh admin setelah pengecekan mutasi pembayaran.</p>
                <p class="mt-2 font-semibold text-gray-900">SMA Persis Serang</p>
            </div>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
            <button type="button"
                    onclick="window.print()"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                Cetak
            </button>
            @if($whatsappUrl)
                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
                   class="inline-flex items-center justify-center rounded-lg bg-green-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800">
                    Kirim ke WhatsApp Donatur
                </a>
            @endif
        </div>
    </div>
</x-admin-layout>
