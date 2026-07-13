@php
    $ay = $academicYear ?? null;
@endphp

<div class="space-y-6">

    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="text-base font-semibold text-slate-900">A. Informasi Tahun Pelajaran</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Tahun Pelajaran <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $ay?->name) }}"
                           class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 {{ $errors->has('name') ? 'border-red-400' : '' }}"
                           placeholder="Contoh: Tahun Pelajaran 2026/2027">
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="academic_year" class="block text-sm font-medium text-slate-700 mb-1">Tahun Pelajaran <span class="text-red-500">*</span></label>
                    <input type="text" id="academic_year" name="academic_year" value="{{ old('academic_year', $ay?->academic_year) }}"
                           class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 {{ $errors->has('academic_year') ? 'border-red-400' : '' }}"
                           placeholder="2026/2027">
                    @error('academic_year')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="start_date" class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $ay?->start_date?->format('Y-m-d')) }}"
                           class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 {{ $errors->has('start_date') ? 'border-red-400' : '' }}">
                    @error('start_date')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-slate-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $ay?->end_date?->format('Y-m-d')) }}"
                           class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 {{ $errors->has('end_date') ? 'border-red-400' : '' }}">
                    @error('end_date')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="inline-flex items-center gap-2">
                        <input type="hidden" name="is_current" value="0">
                        <input type="checkbox" name="is_current" value="1"
                               class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                               {{ old('is_current', $ay?->is_current ?? false) ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-slate-700">Jadikan Tahun Pelajaran Aktif</span>
                    </label>
                    @error('is_current')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="text-base font-semibold text-slate-900">B. Semester Ganjil</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="odd_semester_start_date" class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai</label>
                    <input type="date" id="odd_semester_start_date" name="odd_semester_start_date"
                           value="{{ old('odd_semester_start_date', $ay?->odd_semester_start_date?->format('Y-m-d')) }}"
                           class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 {{ $errors->has('odd_semester_start_date') ? 'border-red-400' : '' }}">
                    @error('odd_semester_start_date')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="odd_semester_end_date" class="block text-sm font-medium text-slate-700 mb-1">Tanggal Selesai</label>
                    <input type="date" id="odd_semester_end_date" name="odd_semester_end_date"
                           value="{{ old('odd_semester_end_date', $ay?->odd_semester_end_date?->format('Y-m-d')) }}"
                           class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 {{ $errors->has('odd_semester_end_date') ? 'border-red-400' : '' }}">
                    @error('odd_semester_end_date')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="text-base font-semibold text-slate-900">C. Semester Genap</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="even_semester_start_date" class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai</label>
                    <input type="date" id="even_semester_start_date" name="even_semester_start_date"
                           value="{{ old('even_semester_start_date', $ay?->even_semester_start_date?->format('Y-m-d')) }}"
                           class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 {{ $errors->has('even_semester_start_date') ? 'border-red-400' : '' }}">
                    @error('even_semester_start_date')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="even_semester_end_date" class="block text-sm font-medium text-slate-700 mb-1">Tanggal Selesai</label>
                    <input type="date" id="even_semester_end_date" name="even_semester_end_date"
                           value="{{ old('even_semester_end_date', $ay?->even_semester_end_date?->format('Y-m-d')) }}"
                           class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 {{ $errors->has('even_semester_end_date') ? 'border-red-400' : '' }}">
                    @error('even_semester_end_date')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="text-base font-semibold text-slate-900">D. Keterangan</h3>
        </div>
        <div class="p-6">
            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                <textarea id="description" name="description" rows="4"
                          class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 {{ $errors->has('description') ? 'border-red-400' : '' }}"
                          placeholder="Opsional">{{ old('description', $ay?->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

</div>
