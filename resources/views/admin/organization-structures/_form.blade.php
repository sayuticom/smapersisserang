@php
    $selectedParent = old('parent_key', $organizationStructure->parent_key);
    $selectedPersonId = old('person_id', $organizationStructure->person_id);
    $isActive = old('is_active', $organizationStructure->exists ? $organizationStructure->is_active : true);
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Jabatan / Label <span class="text-red-500">*</span></label>
        <input type="text" name="label" value="{{ old('label', $organizationStructure->label) }}"
               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
        @error('label') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Structure Key / Slug <span class="text-red-500">*</span></label>
        <input type="text" name="structure_key" value="{{ old('structure_key', $organizationStructure->structure_key) }}"
               placeholder="contoh: waka-kurikulum"
               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
        @error('structure_key') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Pejabat / Penanggung Jawab dari Data Guru</label>
        <select name="person_id"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
            <option value="">-- Belum dipilih --</option>
            @foreach($people as $person)
                @php
                    $personDetail = $person->subject ?: $person->position;
                @endphp
                <option value="{{ $person->id }}" {{ (string) $selectedPersonId === (string) $person->id ? 'selected' : '' }}>
                    {{ $person->name }}{{ $personDetail ? ' - ' . $personDetail : '' }}
                </option>
            @endforeach
        </select>
        @error('person_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Manual / Fallback</label>
        <input type="text" name="person_name" value="{{ old('person_name', $organizationStructure->person_name) }}"
               placeholder="Contoh: Ust. Ahmad Sayuti, S.Pd."
               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
        <p class="text-xs text-gray-400 mt-1">Diisi hanya jika nama belum ada di data guru/tendik.</p>
        @error('person_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
    <textarea name="description" rows="3"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">{{ old('description', $organizationStructure->description) }}</textarea>
    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Anggota / Tugas</label>
    <textarea name="members_text" rows="6"
              placeholder="Satu baris satu anggota atau tugas"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">{{ old('members_text', $membersText) }}</textarea>
    <p class="text-xs text-gray-400 mt-1">Tulis satu item per baris. Baris kosong akan diabaikan.</p>
    @error('members_text') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Parent Key</label>
        <select name="parent_key"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
            <option value="">Tidak ada</option>
            @foreach($parentOptions as $parent)
                <option value="{{ $parent->structure_key }}" {{ $selectedParent === $parent->structure_key ? 'selected' : '' }}>
                    {{ $parent->label }} ({{ $parent->structure_key }})
                </option>
            @endforeach
        </select>
        @error('parent_key') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Card Type</label>
        <input type="text" name="card_type" value="{{ old('card_type', $organizationStructure->card_type) }}"
               placeholder="top, principal, committee, unit"
               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
        @error('card_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Level <span class="text-red-500">*</span></label>
        <input type="number" name="level" value="{{ old('level', $organizationStructure->level) }}" min="1"
               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
        @error('level') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Urutan <span class="text-red-500">*</span></label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $organizationStructure->sort_order) }}" min="0"
               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-[#0F6B3A] focus:border-[#0F6B3A]">
        @error('sort_order') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div class="flex items-center gap-2">
    <input type="checkbox" name="is_active" id="is_active" value="1"
           {{ $isActive ? 'checked' : '' }}
           class="rounded border-gray-300 text-[#0F6B3A] focus:ring-[#0F6B3A]">
    <label for="is_active" class="text-sm font-medium text-gray-700">Aktif</label>
</div>
