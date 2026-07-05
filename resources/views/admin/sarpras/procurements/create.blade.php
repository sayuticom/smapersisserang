<x-admin-layout>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Tambah Pengadaan Barang</h2>
                <p class="mt-1 text-gray-500">Catat pengadaan sarana & prasarana baru.</p>
            </div>
            <a href="{{ route('admin.sarpras.procurements.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                &larr; Kembali
            </a>
        </div>

        @if($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-semibold">Periksa kembali data berikut:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.sarpras.procurements.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="title" class="block text-sm font-semibold text-gray-700">Judul Pengadaan <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}"
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-green-500 focus:ring-green-500" required>
                        @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="procurement_date" class="block text-sm font-semibold text-gray-700">Tanggal Pengadaan <span class="text-red-500">*</span></label>
                        <input type="date" name="procurement_date" id="procurement_date" value="{{ old('procurement_date', date('Y-m-d')) }}"
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-green-500 focus:ring-green-500" required>
                        @error('procurement_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700">Status</label>
                        <select name="status" id="status"
                                class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-green-500 focus:ring-green-500">
                            @foreach(config('sarpras.procurement_statuses') as $val => $label)
                                <option value="{{ $val }}" {{ old('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="source_fund" class="block text-sm font-semibold text-gray-700">Sumber Dana <span class="text-red-500">*</span></label>
                        <input type="text" name="source_fund" id="source_fund" value="{{ old('source_fund') }}"
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-green-500 focus:ring-green-500" required list="source-fund-list">
                        <datalist id="source-fund-list">
                            @foreach(config('sarpras.source_funds') as $fund)
                                <option value="{{ $fund }}">
                            @endforeach
                        </datalist>
                        @error('source_fund')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="total_cost" class="block text-sm font-semibold text-gray-700">Total Biaya (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="total_cost" id="total_cost" value="{{ old('total_cost') }}" min="0"
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-green-500 focus:ring-green-500" required>
                        @error('total_cost')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="vendor_name" class="block text-sm font-semibold text-gray-700">Nama Vendor <span class="text-red-500">*</span></label>
                        <input type="text" name="vendor_name" id="vendor_name" value="{{ old('vendor_name') }}"
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-green-500 focus:ring-green-500" required>
                        @error('vendor_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="receipt" class="block text-sm font-semibold text-gray-700">Bukti Pembelian</label>
                        <input type="file" name="receipt" id="receipt" accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf"
                               class="mt-1.5 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                        <p class="text-xs text-gray-400 mt-1">Maksimal 2MB. Format: JPG, PNG, WebP, PDF.</p>
                        @error('receipt')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-semibold text-gray-700">Deskripsi</label>
                        <textarea name="description" id="description" rows="4"
                                  class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-green-500 focus:ring-green-500">{{ old('description') }}</textarea>
                        @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.sarpras.procurements.index') }}"
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
