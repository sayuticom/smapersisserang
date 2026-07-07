<form method="POST"
      action="{{ $letterSigner ? route('admin.letters.signers.update', $letterSigner) : route('admin.letters.signers.store') }}"
      enctype="multipart/form-data"
      class="mt-5 space-y-4">
    @csrf
    @if($letterSigner)
        @method('PUT')
    @endif

    <div>
        <label for="name" class="block text-sm font-semibold text-slate-700">Nama <span class="text-red-500">*</span></label>
        <input type="text" name="name" id="name" value="{{ old('name', $letterSigner?->name) }}"
               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
    </div>

    <div>
        <label for="position" class="block text-sm font-semibold text-slate-700">Jabatan</label>
        <input type="text" name="position" id="position" value="{{ old('position', $letterSigner?->position) }}"
               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
               placeholder="Contoh: Kepala Sekolah">
    </div>

    <div>
        <label for="identity_number" class="block text-sm font-semibold text-slate-700">Nomor Identitas</label>
        <input type="text" name="identity_number" id="identity_number" value="{{ old('identity_number', $letterSigner?->identity_number) }}"
               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
               placeholder="NIP/NIY/NUPTK, opsional">
    </div>

    <div>
        <label for="signature" class="block text-sm font-semibold text-slate-700">File Tanda Tangan</label>
        <input type="file" name="signature" id="signature" accept="image/jpeg,image/png,image/webp"
               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 file:mr-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100">
        <p class="mt-1 text-xs text-slate-500">Format JPG, PNG, atau WebP. Maksimal 1MB.</p>

        @if($letterSigner?->signature_path)
            <div class="mt-3 rounded-lg border border-slate-200 bg-slate-50 p-3">
                <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Tanda tangan saat ini</div>
                <img src="{{ Storage::url($letterSigner->signature_path) }}"
                     alt="Tanda tangan {{ $letterSigner->name }}"
                     class="h-16 max-w-[180px] rounded border border-slate-200 bg-white object-contain p-1">
            </div>
        @endif
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="sort_order" class="block text-sm font-semibold text-slate-700">Urutan</label>
            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $letterSigner?->sort_order ?? 0) }}"
                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                   min="0">
        </div>
        <div class="flex items-end">
            <label class="flex w-full items-center gap-2 rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-semibold text-slate-700">
                <input type="checkbox" name="is_active" value="1"
                       class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                    {{ old('is_active', $letterSigner?->is_active ?? true) ? 'checked' : '' }}>
                Aktif
            </label>
        </div>
    </div>

    <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:justify-end">
        @if($letterSigner)
            <a href="{{ route('admin.letters.signers.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                Batal
            </a>
        @endif
        <button type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
            {{ $letterSigner ? 'Simpan Perubahan' : 'Tambah Penandatangan' }}
        </button>
    </div>
</form>
