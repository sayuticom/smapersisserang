<x-admin-layout>
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Tambah Data WA Infaq Barang</h2>
                <p class="mt-1 text-gray-500">Catat pesan WhatsApp donatur sebagai komitmen, bukan bukti penerimaan.</p>
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
            <form action="{{ route('admin.infaq-barang-wa.parse') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="parse_raw_whatsapp_message" class="block text-sm font-semibold text-gray-700">Isi Pesan WhatsApp Asli</label>
                    <textarea id="parse_raw_whatsapp_message"
                              name="raw_whatsapp_message"
                              rows="8"
                              class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600"
                              placeholder="Paste pesan WA Infaq Barang dari donatur...">{{ old('raw_whatsapp_message', $rawMessage) }}</textarea>
                </div>
                <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">
                    Ambil Data dari Pesan
                </button>
            </form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <form action="{{ route('admin.infaq-barang-wa.store') }}" method="POST" class="space-y-5">
                @csrf
                <textarea name="raw_whatsapp_message" class="hidden">{{ old('raw_whatsapp_message', $rawMessage) }}</textarea>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="received_at" class="block text-sm font-semibold text-gray-700">Tanggal/Jam WA Masuk <span class="text-red-500">*</span></label>
                        <input type="datetime-local" id="received_at" name="received_at"
                               value="{{ old('received_at', now()->format('Y-m-d\TH:i')) }}"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700">Status</label>
                        <select id="status" name="status"
                                class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ old('status', 'pending') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="donor_name" class="block text-sm font-semibold text-gray-700">Nama Donatur</label>
                        <input type="text" id="donor_name" name="donor_name"
                               value="{{ old('donor_name', $parsed['donor_name'] ?? '') }}"
                               placeholder="Hamba Allah"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="donor_phone" class="block text-sm font-semibold text-gray-700">Nomor WhatsApp</label>
                        <input type="text" id="donor_phone" name="donor_phone"
                               value="{{ old('donor_phone', $parsed['donor_phone'] ?? '') }}"
                               placeholder="08xxxxxxxxxx"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="item_type" class="block text-sm font-semibold text-gray-700">Jenis Barang <span class="text-red-500">*</span></label>
                        <input type="text" id="item_type" name="item_type"
                               value="{{ old('item_type', $parsed['item_type'] ?? '') }}"
                               placeholder="Beras, Telur, Sayuran, Sembako"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="item_name" class="block text-sm font-semibold text-gray-700">Nama Barang</label>
                        <input type="text" id="item_name" name="item_name"
                               value="{{ old('item_name', $parsed['item_name'] ?? '') }}"
                               placeholder="Nama barang jika lebih spesifik"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="quantity_estimate" class="block text-sm font-semibold text-gray-700">Jumlah / Perkiraan</label>
                        <input type="text" id="quantity_estimate" name="quantity_estimate"
                               value="{{ old('quantity_estimate', $parsed['quantity_estimate'] ?? '') }}"
                               placeholder="Contoh: 5 kg, 1 dus"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="delivery_method" class="block text-sm font-semibold text-gray-700">Cara Penyerahan</label>
                        <input type="text" id="delivery_method" name="delivery_method"
                               value="{{ old('delivery_method', $parsed['delivery_method'] ?? '') }}"
                               placeholder="Diantar ke sekolah / Dijemput pihak sekolah"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="note" class="block text-sm font-semibold text-gray-700">Catatan</label>
                        <textarea id="note" name="note" rows="3"
                                  class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600"
                                  placeholder="Catatan tambahan dari donatur">{{ old('note', $parsed['note'] ?? '') }}</textarea>
                    </div>
                </div>

                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-6 text-amber-800">
                    Data ini belum menjadi bukti penerimaan. Terbitkan bukti hanya setelah barang benar-benar diterima.
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.infaq-barang-wa.index') }}"
                       class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-green-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800">
                        Simpan Data WA
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
