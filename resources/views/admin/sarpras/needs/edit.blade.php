<x-admin-layout>
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Edit Kebutuhan</h2>
                <p class="mt-1 text-gray-500">Perbarui data kebutuhan {{ $need->title }}.</p>
            </div>
            <a href="{{ route('admin.sarpras.needs.index') }}"
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
            <form method="POST" action="{{ route('admin.sarpras.needs.update', $need) }}" class="space-y-5">
                @csrf @method('PUT')

                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700">Judul <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $need->title) }}"
                           class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                    @error('title')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="category" class="block text-sm font-semibold text-gray-700">Kategori</label>
                    <input type="text" name="category" id="category" value="{{ old('category', $need->category) }}"
                           list="category-list"
                           class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                           placeholder="Contoh: Meubelair, Elektronik, Alat Peraga">
                    <datalist id="category-list">
                        @foreach(config('sarpras.need_categories') as $cat)
                            <option value="{{ $cat }}">
                        @endforeach
                    </datalist>
                    @error('category')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label for="quantity_needed" class="block text-sm font-semibold text-gray-700">Jumlah</label>
                        <input type="number" name="quantity_needed" id="quantity_needed" value="{{ old('quantity_needed', $need->quantity_needed) }}"
                               min="1" step="1"
                               class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @error('quantity_needed')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="unit" class="block text-sm font-semibold text-gray-700">Satuan</label>
                        <input type="text" name="unit" id="unit" value="{{ old('unit', $need->unit) }}"
                               list="unit-list"
                               class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                               placeholder="unit, buah, set">
                        <datalist id="unit-list">
                            @foreach(config('sarpras.units') as $unit)
                                <option value="{{ $unit }}">
                            @endforeach
                        </datalist>
                        @error('unit')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="estimated_cost" class="block text-sm font-semibold text-gray-700">Estimasi Biaya (Rp)</label>
                        <input type="number" name="estimated_cost" id="estimated_cost" value="{{ old('estimated_cost', $need->estimated_cost) }}"
                               min="0" step="1"
                               class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                               placeholder="0">
                        @error('estimated_cost')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="priority" class="block text-sm font-semibold text-gray-700">Prioritas <span class="text-red-500">*</span></label>
                    <select name="priority" id="priority"
                            class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                        <option value="">Pilih Prioritas</option>
                        @foreach(config('sarpras.need_priorities') as $val => $label)
                            <option value="{{ $val }}" {{ old('priority', $need->priority) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('priority')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700">Status</label>
                    <select name="status" id="status"
                            class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Pilih Status</option>
                        @foreach(config('sarpras.need_statuses') as $val => $label)
                            <option value="{{ $val }}" {{ old('status', $need->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-400">Ubah status untuk memantau perkembangan kebutuhan.</p>
                    @error('status')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700">Deskripsi</label>
                    <textarea name="description" id="description" rows="4"
                              class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('description', $need->description) }}</textarea>
                    @error('description')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('admin.sarpras.needs.index') }}"
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
