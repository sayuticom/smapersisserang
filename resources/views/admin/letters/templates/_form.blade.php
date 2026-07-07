<div class="space-y-5">
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="letter_type_id" class="block text-sm font-semibold text-slate-700">Jenis Surat <span class="text-red-500">*</span></label>
            <select name="letter_type_id" id="letter_type_id" class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                <option value="">Pilih jenis surat</option>
                @foreach($letterTypes as $type)
                    <option value="{{ $type->id }}" {{ (string) old('letter_type_id', $letterTemplate?->letter_type_id) === (string) $type->id ? 'selected' : '' }}>
                        {{ $type->code }} - {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="title" class="block text-sm font-semibold text-slate-700">Judul Template <span class="text-red-500">*</span></label>
            <input type="text" name="title" id="title" value="{{ old('title', $letterTemplate?->title) }}"
                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
        </div>
        <div class="md:col-span-2">
            <label for="subject_template" class="block text-sm font-semibold text-slate-700">Template Perihal</label>
            <input type="text" name="subject_template" id="subject_template" value="{{ old('subject_template', $letterTemplate?->subject_template) }}"
                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <div class="md:col-span-2">
            <x-letter-editor
                name="opening_template"
                label="Template Pembuka"
                :value="$letterTemplate?->opening_template ?? ''"
                :rows="4"
            />
        </div>
        <div class="md:col-span-2">
            <x-letter-editor
                name="body_template"
                label="Template Isi Surat"
                :value="$letterTemplate?->body_template ?? ''"
                :rows="10"
                :required="true"
            />
        </div>
        <div class="md:col-span-2">
            <x-letter-editor
                name="closing_template"
                label="Template Penutup"
                :value="$letterTemplate?->closing_template ?? ''"
                :rows="4"
            />
        </div>
        <div class="md:col-span-2">
            <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" {{ old('is_active', $letterTemplate?->is_active ?? true) ? 'checked' : '' }}>
                Aktif
            </label>
            <p class="mt-1 text-xs text-slate-500">Template tidak aktif tidak bisa dipakai saat membuat surat keluar.</p>
        </div>
    </div>

    <div class="flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
        <a href="{{ $letterTemplate ? route('admin.letters.templates.show', $letterTemplate) : route('admin.letters.templates.index') }}"
           class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
            Batal
        </a>
        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
            Simpan Template
        </button>
    </div>
</div>
