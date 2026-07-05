<x-admin-layout>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Edit Pengadaan Barang</h2>
                <p class="mt-1 text-gray-500">Ubah data pengadaan sarana & prasarana.</p>
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
            <form method="POST" action="{{ route('admin.sarpras.procurements.update', $procurement) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf @method('PUT')
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="title" class="block text-sm font-semibold text-gray-700">Judul Pengadaan <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title', $procurement->title) }}"
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-green-500 focus:ring-green-500" required>
                        @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="procurement_date" class="block text-sm font-semibold text-gray-700">Tanggal Pengadaan <span class="text-red-500">*</span></label>
                        <input type="date" name="procurement_date" id="procurement_date" value="{{ old('procurement_date', $procurement->procurement_date?->format('Y-m-d')) }}"
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-green-500 focus:ring-green-500" required>
                        @error('procurement_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700">Status</label>
                        <select name="status" id="status"
                                class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-green-500 focus:ring-green-500">
                            @foreach(config('sarpras.procurement_statuses') as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $procurement->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="source_fund" class="block text-sm font-semibold text-gray-700">Sumber Dana <span class="text-red-500">*</span></label>
                        <input type="text" name="source_fund" id="source_fund" value="{{ old('source_fund', $procurement->source_fund) }}"
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
                        <input type="number" name="total_cost" id="total_cost" value="{{ old('total_cost', $procurement->total_cost) }}" min="0"
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-green-500 focus:ring-green-500" required>
                        @error('total_cost')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="vendor_name" class="block text-sm font-semibold text-gray-700">Nama Vendor <span class="text-red-500">*</span></label>
                        <input type="text" name="vendor_name" id="vendor_name" value="{{ old('vendor_name', $procurement->vendor_name) }}"
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-green-500 focus:ring-green-500" required>
                        @error('vendor_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="receipt" class="block text-sm font-semibold text-gray-700">Bukti Pembelian</label>
                        @if($procurement->receipt)
                            <div class="mt-1 mb-2">
                                @php $ext = pathinfo($procurement->receipt, PATHINFO_EXTENSION); @endphp
                                @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp']))
                                    <img src="{{ asset('storage/' . $procurement->receipt) }}" alt="Bukti pembelian"
                                         class="h-24 w-24 rounded-lg object-cover border border-slate-200">
                                @else
                                    <a href="{{ asset('storage/' . $procurement->receipt) }}" target="_blank"
                                       class="inline-flex items-center gap-1 text-sm text-blue-600 hover:underline">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Lihat File
                                    </a>
                                @endif
                            </div>
                        @endif
                        <input type="file" name="receipt" id="receipt" accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf"
                               class="mt-1.5 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                        <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengubah file. Maksimal 2MB. Format: JPG, PNG, WebP, PDF.</p>
                        @error('receipt')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-semibold text-gray-700">Deskripsi</label>
                        <textarea name="description" id="description" rows="4"
                                  class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-green-500 focus:ring-green-500">{{ old('description', $procurement->description) }}</textarea>
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
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
