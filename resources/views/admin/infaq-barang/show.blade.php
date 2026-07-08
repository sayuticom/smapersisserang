<x-admin-layout>
    <div class="mx-auto max-w-4xl space-y-3 sm:space-y-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between print:hidden">
            <div>
                <h2 class="text-lg font-bold text-gray-900 sm:text-2xl">Bukti Penerimaan Infaq Barang</h2>
                <p class="mt-0.5 text-xs text-gray-500 sm:mt-1 sm:text-base">Preview bukti penerimaan untuk dicetak atau dikirim ke donatur.</p>
            </div>
            <a href="{{ $receipt->commitment ? route('admin.infaq-barang-wa.show', $receipt->commitment) : route('admin.infaq-barang-wa.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 sm:px-4 sm:py-2.5 sm:text-sm">
                &larr; Data WA Infaq Barang
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-xs text-green-700 print:hidden sm:px-4 sm:py-3 sm:text-base">
                {{ session('success') }}
            </div>
        @endif

        @php
            $logoUrl = $schoolSetting?->logo_path
                ? \Illuminate\Support\Facades\Storage::url($schoolSetting->logo_path)
                : asset('favicon.png');
            $proofPhotoUrl = $receipt->proof_photo ? \Illuminate\Support\Facades\Storage::url($receipt->proof_photo) : null;
        @endphp

        <div id="receipt-print-area" class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm sm:rounded-2xl sm:p-6 print:border-0 print:shadow-none">
            <div class="flex items-start gap-2.5 border-b border-slate-200 pb-3 sm:gap-4 sm:pb-5">
                <img src="{{ $logoUrl }}"
                     class="h-11 w-11 shrink-0 rounded-lg border bg-white object-contain p-1.5 sm:h-16 sm:w-16 sm:rounded-xl sm:p-2"
                     alt="Logo SMA Persis Serang">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wide text-emerald-700 sm:text-sm">{{ $schoolSetting?->school_name ?? 'SMA Persis Serang' }}</p>
                    <h1 class="mt-0.5 text-base font-black uppercase leading-tight tracking-wide text-slate-900 sm:mt-1 sm:text-2xl">BUKTI PENERIMAAN INFAQ BARANG</h1>
                    <p class="mt-1 max-w-2xl text-[11px] leading-snug text-slate-600 sm:mt-2 sm:text-sm sm:leading-6">
                        Infaq untuk kebutuhan pendidikan, makan, asrama, dan sarana prasarana SMA Persis Serang.
                    </p>
                </div>
            </div>

            <dl class="mt-3 grid grid-cols-2 gap-2 text-xs leading-snug sm:mt-5 sm:gap-x-6 sm:gap-y-3 sm:text-sm">
                <div class="rounded-lg bg-slate-50 px-2 py-1.5 sm:flex sm:justify-between sm:gap-4 sm:px-3 sm:py-2">
                    <dt class="text-slate-500">Nomor Bukti</dt>
                    <dd class="mt-0.5 font-semibold text-slate-900 sm:mt-0 sm:text-right">{{ $receipt->receipt_number }}</dd>
                </div>
                <div class="rounded-lg bg-slate-50 px-2 py-1.5 sm:flex sm:justify-between sm:gap-4 sm:px-3 sm:py-2">
                    <dt class="text-slate-500">Tanggal Terima</dt>
                    <dd class="mt-0.5 font-semibold text-slate-900 sm:mt-0 sm:text-right">{{ $receipt->received_date->format('d/m/Y') }}</dd>
                </div>
                <div class="px-2 py-1 sm:flex sm:justify-between sm:gap-4 sm:px-3 sm:py-2">
                    <dt class="text-slate-500">Nama Donatur</dt>
                    <dd class="mt-0.5 font-semibold text-slate-900 sm:mt-0 sm:text-right">{{ $receipt->donorNameLabel() }}</dd>
                </div>
                <div class="px-2 py-1 sm:flex sm:justify-between sm:gap-4 sm:px-3 sm:py-2">
                    <dt class="text-slate-500">Nomor WhatsApp</dt>
                    <dd class="mt-0.5 font-semibold text-slate-900 sm:mt-0 sm:text-right">{{ $receipt->donor_phone ?: '-' }}</dd>
                </div>
                <div class="px-2 py-1 sm:flex sm:justify-between sm:gap-4 sm:px-3 sm:py-2">
                    <dt class="text-slate-500">Jenis Barang</dt>
                    <dd class="mt-0.5 font-semibold text-slate-900 sm:mt-0 sm:text-right">{{ $receipt->item_type }}</dd>
                </div>
                <div class="px-2 py-1 sm:flex sm:justify-between sm:gap-4 sm:px-3 sm:py-2">
                    <dt class="text-slate-500">Nama Barang</dt>
                    <dd class="mt-0.5 font-semibold text-slate-900 sm:mt-0 sm:text-right">{{ $receipt->item_name ?: '-' }}</dd>
                </div>
                <div class="px-2 py-1 sm:flex sm:justify-between sm:gap-4 sm:px-3 sm:py-2">
                    <dt class="text-slate-500">Jumlah</dt>
                    <dd class="mt-0.5 font-semibold text-slate-900 sm:mt-0 sm:text-right">{{ $receipt->quantity ?: '-' }}</dd>
                </div>
                <div class="px-2 py-1 sm:flex sm:justify-between sm:gap-4 sm:px-3 sm:py-2">
                    <dt class="text-slate-500">Satuan</dt>
                    <dd class="mt-0.5 font-semibold text-slate-900 sm:mt-0 sm:text-right">{{ $receipt->unit ?: '-' }}</dd>
                </div>
                <div class="px-2 py-1 sm:flex sm:justify-between sm:gap-4 sm:px-3 sm:py-2">
                    <dt class="text-slate-500">Kondisi Barang</dt>
                    <dd class="mt-0.5 font-semibold text-slate-900 sm:mt-0 sm:text-right">{{ $receipt->item_condition ?: '-' }}</dd>
                </div>
                <div class="px-2 py-1 sm:flex sm:justify-between sm:gap-4 sm:px-3 sm:py-2">
                    <dt class="text-slate-500">Cara Penyerahan</dt>
                    <dd class="mt-0.5 font-semibold text-slate-900 sm:mt-0 sm:text-right">{{ $receipt->delivery_method ?: '-' }}</dd>
                </div>
                <div class="col-span-2 px-2 py-1 sm:px-3 sm:py-2">
                    <dt class="text-slate-500">Catatan</dt>
                    <dd class="mt-0.5 font-semibold text-slate-900 sm:mt-1">{{ $receipt->note ?: '-' }}</dd>
                </div>
                <div class="col-span-2 rounded-lg bg-emerald-50 px-2 py-1.5 sm:flex sm:justify-between sm:gap-4 sm:px-3 sm:py-2">
                    <dt class="font-semibold text-emerald-800">Diterima Oleh</dt>
                    <dd class="mt-0.5 font-bold text-emerald-800 sm:mt-0 sm:text-right">{{ $receipt->received_by ?: '-' }}</dd>
                </div>
            </dl>

            @if($proofPhotoUrl)
                <div class="mt-3 print:hidden sm:mt-5">
                    <p class="mb-1 text-xs font-semibold text-slate-700 sm:mb-2 sm:text-sm">Foto Barang</p>
                    <img src="{{ $proofPhotoUrl }}" alt="Foto barang infaq"
                         class="max-h-44 rounded-lg border border-slate-200 object-contain sm:max-h-80 sm:rounded-xl">
                </div>
            @endif

            <div class="mt-4 grid grid-cols-2 gap-3 text-xs sm:mt-8 sm:gap-6 sm:text-sm">
                <div></div>
                <div class="text-center">
                    <p>Serang, {{ $receipt->received_date->format('d/m/Y') }}</p>
                    <p class="mt-8 border-t border-slate-300 pt-1.5 font-semibold sm:mt-16 sm:pt-2">{{ $receipt->received_by ?: '-' }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 sm:gap-3 print:hidden">
            <button type="button"
                    onclick="window.print()"
                    class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:h-11">
                Cetak Bukti
            </button>
            @if($whatsappUrl)
                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
                   class="inline-flex h-10 items-center justify-center rounded-xl bg-emerald-700 px-4 text-sm font-semibold text-white hover:bg-emerald-800 sm:h-11">
                    Kirim Bukti via WhatsApp
                </a>
            @else
                <button type="button" disabled
                        class="inline-flex h-10 cursor-not-allowed items-center justify-center rounded-xl bg-slate-200 px-4 text-sm font-semibold text-slate-500 sm:h-11">
                    Kirim Bukti via WhatsApp
                </button>
            @endif
        </div>

        @push('styles')
            <style>
                @media print {
                    body {
                        background: #ffffff !important;
                    }

                    body * {
                        visibility: hidden;
                    }

                    #receipt-print-area,
                    #receipt-print-area * {
                        visibility: visible;
                    }

                    #receipt-print-area {
                        position: absolute;
                        inset: 0 auto auto 0;
                        width: 100%;
                    }
                }
            </style>
        @endpush
    </div>
</x-admin-layout>
