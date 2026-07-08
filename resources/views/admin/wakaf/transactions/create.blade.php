@php
    $title = 'Buat Bukti Penerimaan Wakaf';
@endphp
<x-admin-layout>
<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Buat Bukti Penerimaan Wakaf</h1>
            <p class="mt-1 text-sm text-slate-500">Tempel pesan WhatsApp konfirmasi dari wakif untuk mengambil data otomatis.</p>
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

    @if($errors->any())
        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-600">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900">Langkah 1: Tempel Pesan WhatsApp</h2>
        <p class="mt-1 text-xs text-slate-400">Copy paste seluruh pesan konfirmasi dari wakif ke kolom di bawah, lalu klik "Ambil Data".</p>

        <form action="{{ route('admin.wakaf.transactions.parse-receipt') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <textarea name="wa_message" rows="6"
                      class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                      placeholder="Tempel pesan WhatsApp di sini...">{{ old('wa_message') }}</textarea>
            <button type="submit"
                    class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500">
                Ambil Data
            </button>
        </form>
    </div>

    @php $parsed = session('parsed'); @endphp

    @if($parsed)
    <div class="mt-6 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900">Langkah 2: Verifikasi & Simpan</h2>
        <p class="mt-1 text-xs text-slate-400">Periksa data yang sudah diambil, lalu klik "Terbitkan Bukti Penerimaan".</p>

        <form action="{{ route('admin.wakaf.transactions.store-receipt') }}" method="POST" class="mt-4 space-y-5">
            @csrf

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Nama Wakif</label>
                    <input type="text" name="wakif_name" value="{{ old('wakif_name', $parsed['wakif_name'] ?? '') }}"
                           class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Nomor WhatsApp</label>
                    <input type="text" name="wakif_whatsapp" value="{{ old('wakif_whatsapp', $parsed['wakif_whatsapp'] ?? '') }}"
                           class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Nominal Wakaf</label>
                    <input type="text" name="amount" value="{{ old('amount', $parsed['amount'] ?? '') }}"
                           class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Biaya Admin</label>
                    <input type="text" name="admin_fee" value="{{ old('admin_fee', $parsed['admin_fee'] ?? '') }}"
                           class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Kode Unik</label>
                    <input type="text" name="unique_code" value="{{ old('unique_code', $parsed['unique_code'] ?? '') }}"
                           class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Total Transfer</label>
                    <input type="text" name="total_transfer" value="{{ old('total_transfer', $parsed['total_transfer'] ?? '') }}"
                           class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Tanggal Transfer</label>
                    <input type="text" name="transfer_date" value="{{ old('transfer_date', $parsed['transfer_date'] ?? '') }}"
                           class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700">Catatan</label>
                <textarea name="note" rows="2" class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">{{ old('note', $parsed['note'] ?? '') }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="rounded-xl bg-emerald-600 px-6 py-3 text-sm font-bold text-white shadow-sm hover:bg-emerald-500">
                    Terbitkan Bukti Penerimaan
                </button>
                <a href="{{ route('admin.wakaf.transactions.create-receipt') }}"
                   class="rounded-xl border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                    Reset
                </a>
            </div>
        </form>
    </div>
    @endif
</div>
</x-admin-layout>
