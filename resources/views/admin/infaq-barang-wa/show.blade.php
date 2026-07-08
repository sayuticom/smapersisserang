<x-admin-layout>
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Detail Data WA Infaq Barang</h2>
                <p class="mt-1 text-gray-500">Referensi {{ $commitment->reference_number }}</p>
            </div>
            <a href="{{ route('admin.infaq-barang-wa.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                &larr; Data WA
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-5 lg:grid-cols-[1fr_320px]">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <dl class="grid gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
                    <div class="flex justify-between gap-4 rounded-lg bg-slate-50 px-3 py-2">
                        <dt class="text-slate-500">Nomor Referensi</dt>
                        <dd class="text-right font-semibold text-slate-900">{{ $commitment->reference_number }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 rounded-lg bg-slate-50 px-3 py-2">
                        <dt class="text-slate-500">Tanggal/Jam WA</dt>
                        <dd class="text-right font-semibold text-slate-900">{{ $commitment->received_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 px-3 py-2">
                        <dt class="text-slate-500">Nama Donatur</dt>
                        <dd class="text-right font-semibold text-slate-900">{{ $commitment->donorNameLabel() }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 px-3 py-2">
                        <dt class="text-slate-500">Nomor WhatsApp</dt>
                        <dd class="text-right font-semibold text-slate-900">{{ $commitment->donor_phone ?: '-' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 px-3 py-2">
                        <dt class="text-slate-500">Jenis Barang</dt>
                        <dd class="text-right font-semibold text-slate-900">{{ $commitment->item_type }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 px-3 py-2">
                        <dt class="text-slate-500">Nama Barang</dt>
                        <dd class="text-right font-semibold text-slate-900">{{ $commitment->item_name ?: '-' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 px-3 py-2">
                        <dt class="text-slate-500">Jumlah / Perkiraan</dt>
                        <dd class="text-right font-semibold text-slate-900">{{ $commitment->quantity_estimate ?: '-' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 px-3 py-2">
                        <dt class="text-slate-500">Cara Penyerahan</dt>
                        <dd class="text-right font-semibold text-slate-900">{{ $commitment->delivery_method ?: '-' }}</dd>
                    </div>
                    <div class="px-3 py-2 sm:col-span-2">
                        <dt class="text-slate-500">Catatan</dt>
                        <dd class="mt-1 font-semibold text-slate-900">{{ $commitment->note ?: '-' }}</dd>
                    </div>
                    <div class="px-3 py-2 sm:col-span-2">
                        <dt class="text-slate-500">Isi Pesan WhatsApp Asli</dt>
                        <dd class="mt-2 whitespace-pre-line rounded-xl bg-slate-50 p-3 text-slate-800">{{ $commitment->raw_whatsapp_message ?: '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-semibold text-slate-700">Status</p>
                    <p class="mt-2 inline-flex rounded-full bg-emerald-50 px-3 py-1 text-sm font-bold text-emerald-700">
                        {{ $commitment->statusLabel() }}
                    </p>

                    <form action="{{ route('admin.infaq-barang-wa.update-status', $commitment) }}" method="POST" class="mt-4 space-y-3">
                        @csrf
                        @method('PATCH')
                        <select name="status"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ $commitment->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button type="submit"
                                class="inline-flex w-full items-center justify-center rounded-lg bg-green-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800">
                            Update Status
                        </button>
                    </form>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-semibold text-slate-700">Bukti Penerimaan</p>
                    @if($commitment->receipt)
                        <p class="mt-2 text-sm text-slate-600">Sudah diterima dengan bukti {{ $commitment->receipt->receipt_number }}.</p>
                        <a href="{{ route('admin.infaq-barang.show', $commitment->receipt) }}"
                           class="mt-3 inline-flex w-full items-center justify-center rounded-lg border border-emerald-200 px-4 py-2.5 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">
                            Lihat Bukti Penerimaan
                        </a>
                    @else
                        <p class="mt-2 text-sm text-slate-600">Belum ada bukti penerimaan. Terbitkan hanya setelah barang benar-benar diterima.</p>
                        <a href="{{ route('admin.infaq-barang.create', ['source_wa' => $commitment->id]) }}"
                           class="mt-3 inline-flex w-full items-center justify-center rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-bold text-emerald-950 hover:bg-amber-400">
                            Terbitkan Bukti Penerimaan
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
