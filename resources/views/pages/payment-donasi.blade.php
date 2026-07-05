@extends('layouts.public')

@php
    $schoolName = $schoolSetting->school_name ?? 'SMA Persis Serang';
@endphp

@section('title', 'Pembayaran Donasi - ' . $schoolName)

@php
    $snapUrl = config('midtrans.is_production')
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
@endphp

@push('scripts')
<script src="{{ $snapUrl }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
@endpush

@section('content')

<section class="bg-[#FBF7EF] py-16 lg:py-20">
    <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">

        <div class="rounded-2xl border border-amber-200/60 bg-white p-6 shadow-lg shadow-emerald-950/5 lg:p-10">
            <div class="text-center">
                <p class="text-sm font-bold uppercase tracking-[0.24em] text-[#D4A017]">PEMBAYARAN</p>
                <h1 class="mt-3 font-serif text-2xl font-bold text-[#052E1F] lg:text-3xl">Pembayaran Donasi</h1>
                <p class="mt-2 text-sm leading-relaxed text-gray-500">Selesaikan pembayaran untuk melanjutkan donasi Anda.</p>
            </div>

            <div class="mt-6 rounded-xl border border-emerald-100 bg-emerald-50/50 p-5">
                <table class="w-full text-sm">
                    <tr>
                        <td class="py-1.5 pr-4 font-medium text-gray-500">Order ID</td>
                        <td class="py-1.5 font-semibold text-[#052E1F] font-mono">{{ $transaction->order_id }}</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 pr-4 font-medium text-gray-500">Nama</td>
                        <td class="py-1.5 font-semibold text-[#052E1F]">{{ $transaction->donor_name }}</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 pr-4 font-medium text-gray-500">Jenis Dukungan</td>
                        <td class="py-1.5 font-semibold text-[#052E1F]">{{ $transaction->support_type }}</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 pr-4 font-medium text-gray-500">Nominal</td>
                        <td class="py-1.5 font-semibold text-[#0F6B3A] text-lg">Rp{{ number_format($transaction->amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 pr-4 font-medium text-gray-500">Status</td>
                        <td class="py-1.5">
                            @if($transaction->isPaid())
                                <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-700">Lunas</span>
                            @elseif(in_array($transaction->status, ['expire', 'cancel', 'deny', 'failure']))
                                <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-700">{{ ucfirst($transaction->status) }}</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-sm font-semibold text-amber-700">Menunggu Pembayaran</span>
                            @endif
                        </td>
                    </tr>
                    @if($transaction->note)
                    <tr>
                        <td class="py-1.5 pr-4 font-medium text-gray-500">Catatan</td>
                        <td class="py-1.5 text-gray-600">{{ $transaction->note }}</td>
                    </tr>
                    @endif
                    @if($transaction->paid_at)
                    <tr>
                        <td class="py-1.5 pr-4 font-medium text-gray-500">Dibayar</td>
                        <td class="py-1.5 text-gray-600">{{ $transaction->paid_at->format('d M Y H:i') }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            @if($transaction->isPaid())
                <div class="mt-6 rounded-xl border border-green-200 bg-green-50 p-6 text-center">
                    <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-green-100 text-green-600">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-green-800">Terima kasih, donasi Bapak/Ibu sudah kami terima.</h3>
                    <p class="mt-2 text-sm text-green-700">Semoga menjadi amal jariyah dan keberkahan bagi kita semua.</p>
                </div>
            @elseif(in_array($transaction->status, ['expire', 'cancel', 'deny', 'failure']))
                <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-6 text-center">
                    <h3 class="text-lg font-bold text-red-800">Pembayaran {{ $transaction->status }}</h3>
                    <p class="mt-2 text-sm text-red-700">Silakan mencoba kembali atau hubungi admin melalui WhatsApp.</p>
                </div>
            @else
                <div class="mt-6 rounded-xl border border-amber-100 bg-amber-50/50 p-5 text-sm leading-6 text-gray-700">
                    <p>
                        Klik tombol <strong>Bayar Sekarang</strong> untuk memilih metode pembayaran. Tersedia berbagai metode seperti QRIS, e-wallet, transfer bank, dan lainnya.
                    </p>
                </div>
            @endif

            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                @if(!$transaction->isPaid() && !in_array($transaction->status, ['expire', 'cancel', 'deny', 'failure']))
                    <button type="button" id="pay-button"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#0F6B3A] px-7 py-4 text-sm font-bold text-white shadow-lg shadow-emerald-900/30 transition hover:bg-[#0A4F2B] sm:w-auto">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Bayar Sekarang
                    </button>
                @endif
                <a href="{{ $confirmWaUrl }}" target="_blank" rel="noopener"
                   class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-amber-400 to-amber-600 px-7 py-4 text-sm font-bold text-emerald-950 shadow-lg shadow-amber-900/20 transition hover:from-amber-300 hover:to-amber-500 sm:w-auto">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Konfirmasi / Hubungi Admin
                </a>
                <a href="{{ route('donasi-pendidikan') }}"
                   class="inline-flex w-full items-center justify-center rounded-xl border border-gray-300 bg-white px-7 py-4 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 sm:w-auto">
                    Kembali
                </a>
            </div>
        </div>

    </div>
</section>

@push('scripts')
@if($transaction->snap_token && !$transaction->isPaid() && !in_array($transaction->status, ['expire', 'cancel', 'deny', 'failure']))
<script>
document.getElementById('pay-button')?.addEventListener('click', function () {
    snap.pay('{{ $transaction->snap_token }}', {
        onSuccess: function(result) {
            window.location.reload();
        },
        onPending: function(result) {
            window.location.reload();
        },
        onError: function(result) {
            alert('Pembayaran belum berhasil. Silakan coba lagi.');
        },
        onClose: function() {
            console.log('Donatur menutup popup pembayaran.');
        }
    });
});
</script>
@endif
@endpush

@endsection
