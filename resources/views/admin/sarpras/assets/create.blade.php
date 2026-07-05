<x-admin-layout>
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Tambah Aset Baru</h2>
                <p class="text-gray-500 mt-1">Lengkapi data aset inventaris sekolah.</p>
            </div>
            <a href="{{ route('admin.sarpras.assets.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                &larr; Kembali
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

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.sarpras.assets.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700">Nama Aset <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                    </div>
                    <div>
                        <label for="inventory_code" class="block text-sm font-semibold text-gray-700">Kode Inventaris</label>
                        <input type="text" name="inventory_code" id="inventory_code" value="{{ old('inventory_code') }}"
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                               placeholder="Otomatis jika dikosongkan">
                    </div>
                    <div>
                        <label for="category" class="block text-sm font-semibold text-gray-700">Kategori</label>
                        <input type="text" name="category" id="category" value="{{ old('category') }}"
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                               list="categoryList" placeholder="Ketik atau pilih">
                        <datalist id="categoryList">
                            @foreach(config('sarpras.asset_categories') as $cat)
                                <option value="{{ $cat }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label for="quantity" class="block text-sm font-semibold text-gray-700">Jumlah <span class="text-red-500">*</span></label>
                        <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}"
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" required min="1">
                    </div>
                    <div>
                        <label for="unit" class="block text-sm font-semibold text-gray-700">Satuan</label>
                        <input type="text" name="unit" id="unit" value="{{ old('unit') }}"
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                               list="unitList" placeholder="Contoh: unit, buah, set">
                        <datalist id="unitList">
                            @foreach(config('sarpras.units') as $unit)
                                <option value="{{ $unit }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label for="location" class="block text-sm font-semibold text-gray-700">Lokasi</label>
                        <input type="text" name="location" id="location" value="{{ old('location') }}"
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                               list="locationList" placeholder="Contoh: Lab Komputer">
                        <datalist id="locationList">
                            @foreach(config('sarpras.default_locations') as $loc)
                                <option value="{{ $loc }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label for="condition" class="block text-sm font-semibold text-gray-700">Kondisi <span class="text-red-500">*</span></label>
                        <select name="condition" id="condition"
                                class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                            <option value="">Pilih Kondisi</option>
                            @foreach(config('sarpras.asset_conditions') as $val => $label)
                                <option value="{{ $val }}" {{ old('condition') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="procurement_year" class="block text-sm font-semibold text-gray-700">Tahun Pengadaan</label>
                        <input type="number" name="procurement_year" id="procurement_year" value="{{ old('procurement_year') }}"
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                               placeholder="{{ date('Y') }}" min="1900" max="{{ date('Y') + 5 }}">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="source_fund" class="block text-sm font-semibold text-gray-700">Sumber Dana</label>
                        <input type="text" name="source_fund" id="source_fund" value="{{ old('source_fund') }}"
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                               list="sourceFundList" placeholder="Contoh: BOS, APBD, Komite">
                        <datalist id="sourceFundList">
                            @foreach(config('sarpras.source_funds') as $fund)
                                <option value="{{ $fund }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="photo" class="block text-sm font-semibold text-gray-700">Foto Aset</label>
                        <input type="file" name="photo" id="photo" accept="image/*"
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-semibold text-gray-700">Deskripsi</label>
                        <textarea name="description" id="description" rows="4"
                                  class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end pt-2">
                    <a href="{{ route('admin.sarpras.assets.index') }}"
                       class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-green-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
