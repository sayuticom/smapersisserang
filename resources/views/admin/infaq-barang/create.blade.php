<x-admin-layout>
    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Tambah Penerimaan Infaq Barang</h2>
                <p class="mt-1 text-gray-500">Input manual setelah barang benar-benar diterima atau ditindaklanjuti admin.</p>
            </div>
            <a href="{{ route('admin.infaq-barang.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                &larr; Daftar Penerimaan
            </a>
        </div>

        @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-semibold">Periksa kembali data berikut:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <form action="{{ route('admin.infaq-barang.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @if($sourceCommitment ?? null)
                    <input type="hidden" name="source_wa_id" value="{{ $sourceCommitment->id }}">
                    <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm leading-6 text-emerald-800">
                        Form ini dibuat dari Data WA Infaq Barang {{ $sourceCommitment->reference_number }}. Setelah bukti disimpan, status Data WA akan menjadi Sudah Diterima.
                    </div>
                @endif

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="received_date" class="block text-sm font-semibold text-gray-700">Tanggal Terima <span class="text-red-500">*</span></label>
                        <input type="date" id="received_date" name="received_date" value="{{ old('received_date', now()->toDateString()) }}"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="received_by" class="block text-sm font-semibold text-gray-700">Diterima Oleh</label>
                        <input type="text" id="received_by" name="received_by" value="{{ old('received_by', auth()->user()?->name) }}"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="donor_name" class="block text-sm font-semibold text-gray-700">Nama Donatur</label>
                        <input type="text" id="donor_name" name="donor_name" value="{{ old('donor_name', $prefill['donor_name'] ?? '') }}"
                               placeholder="Hamba Allah"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="donor_phone" class="block text-sm font-semibold text-gray-700">Nomor WhatsApp</label>
                        <input type="text" id="donor_phone" name="donor_phone" value="{{ old('donor_phone', $prefill['donor_phone'] ?? '') }}"
                               placeholder="08xxxxxxxxxx"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="item_type" class="block text-sm font-semibold text-gray-700">Jenis Barang <span class="text-red-500">*</span></label>
                        <select id="item_type" name="item_type"
                                class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                            @php
                                $itemTypes = ['Beras', 'Telur', 'Sayuran', 'Lauk pauk', 'Sembako', 'Perlengkapan sekolah', 'Perlengkapan asrama', 'Lainnya'];
                                $selectedItemType = old('item_type', $prefill['item_type'] ?? '');
                            @endphp
                            @if($selectedItemType && !in_array($selectedItemType, $itemTypes, true))
                                <option value="{{ $selectedItemType }}" selected>{{ $selectedItemType }}</option>
                            @endif
                            @foreach($itemTypes as $type)
                                <option value="{{ $type }}" {{ $selectedItemType === $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="item_name" class="block text-sm font-semibold text-gray-700">Nama Barang</label>
                        <input type="text" id="item_name" name="item_name" value="{{ old('item_name', $prefill['item_name'] ?? '') }}"
                               placeholder="Contoh: Beras pandan wangi"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="quantity" class="block text-sm font-semibold text-gray-700">Jumlah</label>
                        <input type="text" id="quantity" name="quantity" value="{{ old('quantity', $prefill['quantity'] ?? '') }}"
                               placeholder="Contoh: 5"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="unit" class="block text-sm font-semibold text-gray-700">Satuan</label>
                        <input type="text" id="unit" name="unit" value="{{ old('unit') }}"
                               placeholder="kg, dus, kardus, pcs"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="item_condition" class="block text-sm font-semibold text-gray-700">Kondisi Barang</label>
                        <input type="text" id="item_condition" name="item_condition" value="{{ old('item_condition') }}"
                               placeholder="Baik, baru, layak pakai"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="delivery_method" class="block text-sm font-semibold text-gray-700">Cara Penyerahan</label>
                        <select id="delivery_method" name="delivery_method"
                                class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                            @foreach(['Diantar ke sekolah', 'Dijemput pihak sekolah', 'Titipan/kurir', 'Lainnya'] as $method)
                                <option value="{{ $method }}" {{ old('delivery_method', $prefill['delivery_method'] ?? '') === $method ? 'selected' : '' }}>{{ $method }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="proof_photo" class="block text-sm font-semibold text-gray-700">Foto Barang <span class="font-normal text-gray-400">(opsional)</span></label>
                        <input type="file" id="proof_photo" name="proof_photo" accept="image/*"
                               class="mt-1.5 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm file:mr-4 file:rounded-md file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100">
                        <p class="mt-1 text-xs text-slate-500">Maksimal 2 MB.</p>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="note" class="block text-sm font-semibold text-gray-700">Catatan</label>
                        <textarea id="note" name="note" rows="3"
                                  class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600"
                                  placeholder="Catatan tambahan penerimaan barang">{{ old('note', $prefill['note'] ?? '') }}</textarea>
                    </div>
                </div>

                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-6 text-amber-800">
                    Data ini dibuat manual oleh admin setelah barang diterima. Tidak ada data yang otomatis tersimpan dari form publik atau WhatsApp.
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.infaq-barang.index') }}"
                       class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-green-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800">
                        Simpan & Buat Bukti
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
