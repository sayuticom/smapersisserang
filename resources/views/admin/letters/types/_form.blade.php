@php
    $isEditing = filled($letterType);
@endphp

<form method="POST" action="{{ $isEditing ? route('admin.letters.types.update', $letterType) : route('admin.letters.types.store') }}" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
    @csrf
    @if($isEditing)
        @method('PUT')
    @endif

    <div class="mb-4">
        <h3 class="text-lg font-bold text-slate-900">{{ $isEditing ? 'Edit Jenis Surat' : 'Tambah Jenis Surat' }}</h3>
        <p class="mt-1 text-sm text-slate-500">Kode akan otomatis disimpan dalam huruf kapital.</p>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="code" class="block text-sm font-semibold text-slate-700">Kode <span class="text-red-500">*</span></label>
            <input type="text" name="code" id="code" value="{{ old('code', $letterType?->code) }}" maxlength="20"
                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm uppercase focus:border-emerald-500 focus:ring-emerald-500" required>
        </div>
        <div>
            <label for="name" class="block text-sm font-semibold text-slate-700">Nama <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $letterType?->name) }}"
                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
        </div>
        <div>
            <label for="sort_order" class="block text-sm font-semibold text-slate-700">Urutan</label>
            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $letterType?->sort_order ?? 0) }}" min="0"
                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <div class="flex items-end">
            <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" {{ old('is_active', $letterType?->is_active ?? true) ? 'checked' : '' }}>
                Aktif
            </label>
        </div>
        <div class="md:col-span-2">
            <label for="description" class="block text-sm font-semibold text-slate-700">Deskripsi</label>
            <textarea name="description" id="description" rows="3" class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('description', $letterType?->description) }}</textarea>
        </div>
    </div>

    <div class="mt-5 flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
        @if($isEditing)
            <a href="{{ route('admin.letters.types.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                Batal Edit
            </a>
        @endif
        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
            {{ $isEditing ? 'Simpan Perubahan' : 'Tambah Jenis' }}
        </button>
    </div>
</form>
