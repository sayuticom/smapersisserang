@php
    $categories ??= collect();
    $sources ??= collect();
    $statusDays ??= collect();
    $targets ??= collect();
    $event ??= null;
    $isEdit = $event !== null;

    $evtTargets = old('targets', $event?->targets ?? []);
    if (is_string($evtTargets)) {
        $evtTargets = json_decode($evtTargets, true) ?? [];
    }
    $evtTargets = (array) $evtTargets;

    $evtRespType = old('responsible_type', $event?->teacher_id ? 'teacher' : ($event?->person_in_charge ? 'other' : 'teacher'));
    $evtTeacherId = old('teacher_id', $event?->teacher_id ?? '');
    $evtPersonInCharge = old('person_in_charge', $event?->person_in_charge ?? '');
@endphp

<div x-data="calendarForm()" class="academic-event-form-layout">
    @if ($errors->any())
        <div class="academic-event-form-main col-span-full">
            <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-red-800">Form belum dapat disimpan.</p>
                        <ul class="mt-2 list-inside list-disc text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- LEFT: FORM --}}
    <div class="academic-event-form-main space-y-6">
        {{-- A. Informasi Kegiatan --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="mb-4 flex items-center gap-2 text-base font-semibold text-slate-900">
                <svg class="h-5 w-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                A. Informasi Kegiatan
            </h3>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Nama Kegiatan</label>
                    <input type="text" name="title" x-model="nama" placeholder="Masukkan nama kegiatan"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('title')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-slate-400">Gunakan nama singkat dan jelas.</p>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Kategori</label>
                    <select name="category" x-model="kategori"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Pilih kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat['value'] }}">{{ $cat['label'] }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Sumber</label>
                    <div class="flex flex-wrap gap-4 pt-1">
                        @foreach($sources as $src)
                            <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                                <input type="radio" name="source" x-model="sumber" value="{{ $src['value'] }}"
                                       class="rounded-full border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                {{ $src['label'] }}
                            </label>
                        @endforeach
                    </div>
                    @error('source')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs leading-relaxed text-slate-400">Pilih Sekolah untuk agenda internal, Pemerintah untuk agenda resmi.</p>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Deskripsi</label>
                    <textarea name="description" x-model="deskripsi" rows="3" placeholder="Deskripsi kegiatan"
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- B. Waktu Pelaksanaan --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="mb-4 flex items-center gap-2 text-base font-semibold text-slate-900">
                <svg class="h-5 w-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                B. Waktu Pelaksanaan
            </h3>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Tanggal Mulai</label>
                    <input type="date" name="start_date" x-model="startDate"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('start_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Tanggal Selesai</label>
                    <input type="hidden" name="end_date" :value="endDate">
                    <input type="date" x-model="endDate" :disabled="isSingleDay"
                           :min="startDate"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                           :class="isSingleDay ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : ''">
                    @error('end_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex flex-wrap items-center gap-6">
                    <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                        <input type="checkbox" x-model="isSingleDay"
                               class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        Kegiatan satu hari
                    </label>
                    <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                        <input type="hidden" name="is_all_day" :value="isAllDay ? '1' : '0'">
                        <input type="checkbox" x-model="isAllDay"
                               class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        Sepanjang hari
                    </label>
                </div>
                <div></div>
                <template x-if="!isAllDay">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Jam Mulai</label>
                        <input type="time" name="start_time" x-model="jamMulai"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @error('start_time')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </template>
                <template x-if="!isAllDay">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Jam Selesai</label>
                        <input type="time" name="end_time" x-model="jamSelesai"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @error('end_time')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </template>
            </div>
        </div>

        {{-- C. Status Kalender --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="mb-4 flex items-center gap-2 text-base font-semibold text-slate-900">
                <svg class="h-5 w-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                C. Status Kalender
            </h3>
            <p class="mb-3 text-xs text-slate-400">Menentukan apakah tanggal dihitung sebagai hari efektif atau libur.</p>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Status Hari</label>
                    <select name="day_status" x-model="statusDay" @change="onStatusChange"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Pilih status</option>
                        @foreach($statusDays as $sd)
                            <option value="{{ $sd['value'] }}">{{ $sd['label'] }}</option>
                        @endforeach
                    </select>
                    @error('day_status')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex flex-col gap-3 pt-1">
                    <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer"
                           :class="statusDay && statusDay !== 'kegiatan-khusus' ? 'opacity-60' : ''">
                        <input type="hidden" name="is_holiday" :value="isHoliday ? '1' : '0'">
                        <input type="checkbox" x-model="isHoliday"
                               :disabled="statusDay && statusDay !== 'kegiatan-khusus'"
                               class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                               :class="statusDay && statusDay !== 'kegiatan-khusus' ? 'cursor-not-allowed' : ''">
                        Termasuk hari libur
                    </label>
                    <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer"
                           :class="statusDay && statusDay !== 'kegiatan-khusus' ? 'opacity-60' : ''">
                        <input type="hidden" name="is_effective_day" :value="isEffective ? '1' : '0'">
                        <input type="checkbox" x-model="isEffective"
                               :disabled="statusDay && statusDay !== 'kegiatan-khusus'"
                               class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                               :class="statusDay && statusDay !== 'kegiatan-khusus' ? 'cursor-not-allowed' : ''">
                        Dihitung sebagai hari efektif
                    </label>
                </div>
            </div>
        </div>

        {{-- D. Sasaran --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="mb-4 flex items-center gap-2 text-base font-semibold text-slate-900">
                <svg class="h-5 w-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                D. Sasaran
            </h3>
            @php
                $alpineTargetKey = [
                    'semua' => 'semua',
                    'guru' => 'guru',
                    'siswa' => 'siswa',
                    'orang_tua' => 'orangTua',
                    'asrama' => 'asrama',
                    'publik' => 'publik',
                ];
            @endphp
            <div class="flex flex-wrap gap-4">
                <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                    <input type="checkbox" name="targets[]" value="semua"
                           x-model="targets.semua" @change="onSelectAll"
                           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    Semua
                </label>
                @foreach($targets as $t)
                    @if($t['value'] !== 'semua')
                        <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer"
                               :class="targets.semua ? 'opacity-60' : ''">
                            <input type="checkbox" name="targets[]" value="{{ $t['value'] }}"
                                   x-model="targets.{{ $alpineTargetKey[$t['value']] ?? $t['value'] }}"
                                   :disabled="targets.semua"
                                   class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                   :class="targets.semua ? 'cursor-not-allowed' : ''">
                            {{ $t['label'] }}
                        </label>
                    @endif
                @endforeach
            </div>
            @error('targets')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
            @error('targets.*')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- E. Informasi Tambahan --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="mb-4 flex items-center gap-2 text-base font-semibold text-slate-900">
                <svg class="h-5 w-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                E. Informasi Tambahan
            </h3>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Lokasi</label>
                    <input type="text" name="location" x-model="lokasi" placeholder="Lokasi kegiatan"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('location')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Penanggung Jawab</label>
                    <div class="flex flex-wrap gap-4 mb-3">
                        <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                            <input type="radio" name="responsible_type" value="teacher"
                                   x-model="responsibleType"
                                   class="border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            Guru
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                            <input type="radio" name="responsible_type" value="other"
                                   x-model="responsibleType"
                                   class="border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            Lainnya
                        </label>
                    </div>
                    @error('responsible_type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    @error('teacher_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    @error('person_in_charge')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <template x-if="responsibleType === 'teacher'">
                        <div>
                            <select name="teacher_id" x-model="teacherId"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">-- Pilih Guru --</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">
                                        {{ $teacher->name }}{{ $teacher->position ? ' - ' . $teacher->position : '' }}{{ !$teacher->is_active ? ' (Tidak Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </template>
                    <template x-if="responsibleType === 'other'">
                        <div>
                            <input type="text" name="person_in_charge" x-model="penanggungJawab" placeholder="Nama penanggung jawab"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                    </template>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Catatan Internal</label>
                    <textarea name="internal_notes" x-model="catatanInternal" rows="3" placeholder="Catatan internal (tidak tampil di publik)"
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                    @error('internal_notes')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-slate-400">Hanya terlihat oleh admin dan tidak tampil di kalender publik.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: PREVIEW --}}
    <div class="academic-event-preview" x-cloak>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold text-slate-900">
                <svg class="h-4 w-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Preview
            </h3>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-xs text-slate-400">Nama Kegiatan</p>
                    <p class="font-medium text-slate-900 break-words" x-text="nama || '—'"></p>
                </div>
                <div class="border-t border-slate-100"></div>
                <div>
                    <p class="text-xs text-slate-400">Kategori</p>
                    <p class="font-medium text-slate-900" x-text="getKategoriLabel()"></p>
                </div>
                <div class="border-t border-slate-100"></div>
                <div>
                    <p class="text-xs text-slate-400">Sumber</p>
                    <p class="font-medium text-slate-900" x-text="sumber === 'sekolah' ? 'Sekolah' : 'Pemerintah'"></p>
                </div>
                <div class="border-t border-slate-100"></div>
                <div>
                    <p class="text-xs text-slate-400">Tanggal</p>
                    <p class="font-medium text-slate-900">
                        <span x-text="formatDate(startDate) || '—'"></span>
                        <template x-if="startDate && endDate && endDate !== startDate">
                            <span> — <span x-text="formatDate(endDate)"></span></span>
                        </template>
                    </p>
                </div>
                <div class="border-t border-slate-100"></div>
                <div>
                    <p class="text-xs text-slate-400">Jam</p>
                    <p class="font-medium text-slate-900">
                        <template x-if="isAllDay">
                            <span>Sepanjang hari</span>
                        </template>
                        <template x-if="!isAllDay && (jamMulai || jamSelesai)">
                            <span><span x-text="jamMulai || '—'"></span> — <span x-text="jamSelesai || '—'"></span></span>
                        </template>
                        <template x-if="!isAllDay && !jamMulai && !jamSelesai">
                            <span class="text-slate-400">—</span>
                        </template>
                    </p>
                </div>
                <div class="border-t border-slate-100"></div>
                <div>
                    <p class="text-xs text-slate-400">Status Hari</p>
                    <p class="font-medium text-slate-900" x-text="getStatusDayLabel()"></p>
                </div>
                <div class="border-t border-slate-100"></div>
                <div>
                    <p class="text-xs text-slate-400">Sasaran</p>
                    <p class="font-medium text-slate-900" x-text="getSelectedTargets() || '—'"></p>
                </div>
                <div class="border-t border-slate-100"></div>
                <div>
                    <p class="text-xs text-slate-400">Penanggung Jawab</p>
                    <p class="font-medium text-slate-900" x-text="getPersonInCharge() || '—'"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calendarForm() {
    return {
        // Form data — initialized from old() for validation persistence, falls back to event data
        nama: @js(old('title', $event?->title ?? '')),
        kategori: @js(old('category', $event?->category ?? '')),
        sumber: @js(old('source', $event?->source ?? 'sekolah')),
        deskripsi: @js(old('description', $event?->description ?? '')),
        startDate: @js(old('start_date', $event?->start_date?->format('Y-m-d') ?? '')),
        endDate: @js(old('end_date', $event?->end_date?->format('Y-m-d') ?? '')),
        isSingleDay: @js($event && $event->start_date?->toDateString() === $event->end_date?->toDateString()),
        isAllDay: @js((bool) old('is_all_day', $event?->is_all_day ?? false)),
        jamMulai: @js(old('start_time', $event?->start_time ?? '')),
        jamSelesai: @js(old('end_time', $event?->end_time ?? '')),
        statusDay: @js(old('day_status', $event?->day_status ?? '')),
        isHoliday: @js((bool) old('is_holiday', $event?->is_holiday ?? false)),
        isEffective: @js((bool) old('is_effective_day', $event?->is_effective_day ?? false)),
        targets: {
            semua: @js(in_array('semua', $evtTargets)),
            guru: @js(in_array('guru', $evtTargets)),
            siswa: @js(in_array('siswa', $evtTargets)),
            orangTua: @js(in_array('orang_tua', $evtTargets)),
            asrama: @js(in_array('asrama', $evtTargets)),
            publik: @js(in_array('publik', $evtTargets)),
        },
        lokasi: @js(old('location', $event?->location ?? '')),
        responsibleType: @js($evtRespType),
        teacherId: @js($evtTeacherId),
        penanggungJawab: @js($evtPersonInCharge),
        catatanInternal: @js(old('internal_notes', $event?->internal_notes ?? '')),

        // Category labels from server
        categoryLabels: {
            @foreach($categories as $cat)
                '{{ $cat['value'] }}': '{{ $cat['label'] }}',
            @endforeach
        },

        init() {
            this.$watch('isSingleDay', val => {
                if (val && this.startDate) {
                    this.endDate = this.startDate;
                }
            });
            this.$watch('startDate', val => {
                if (this.isSingleDay) {
                    this.endDate = val;
                }
            });
            this.$watch('isAllDay', val => {
                if (val) {
                    this.jamMulai = '';
                    this.jamSelesai = '';
                }
            });
            this.$watch('responsibleType', val => {
                if (val === 'teacher') {
                    this.penanggungJawab = '';
                } else if (val === 'other') {
                    this.teacherId = '';
                }
            });
        },

        onStatusChange() {
            if (this.statusDay === 'libur') {
                this.isHoliday = true;
                this.isEffective = false;
            } else if (this.statusDay === 'efektif') {
                this.isHoliday = false;
                this.isEffective = true;
            } else if (this.statusDay === 'tidak-efektif') {
                this.isHoliday = false;
                this.isEffective = false;
            }
            // Kegiatan Khusus: leave as user chooses
        },

        onSelectAll() {
            const val = this.targets.semua;
            for (let key in this.targets) {
                if (key !== 'semua') {
                    this.targets[key] = val;
                }
            }
        },

        getKategoriLabel() {
            return this.categoryLabels[this.kategori] || '—';
        },

        getStatusDayLabel() {
            const map = {
                'efektif': 'Hari Efektif',
                'tidak-efektif': 'Hari Tidak Efektif',
                'libur': 'Libur',
                'kegiatan-khusus': 'Kegiatan Khusus'
            };
            return map[this.statusDay] || '—';
        },

        getSelectedTargets() {
            const labels = [];
            const map = {
                semua: 'Semua',
                guru: 'Guru',
                siswa: 'Siswa',
                orangTua: 'Orang Tua',
                asrama: 'Asrama',
                publik: 'Publik'
            };
            for (let key in this.targets) {
                if (this.targets[key]) {
                    labels.push(map[key]);
                }
            }
            if (labels.length === 0) return '';
            if (labels.includes('Semua')) return 'Semua';
            return labels.join(', ');
        },

        getPersonInCharge() {
            if (this.responsibleType === 'teacher' && this.teacherId) {
                const select = document.querySelector('select[name="teacher_id"]');
                if (select) {
                    const opt = select.querySelector(`option[value="${this.teacherId}"]`);
                    return opt ? opt.text : '—';
                }
            }
            if (this.responsibleType === 'other' && this.penanggungJawab) {
                return this.penanggungJawab;
            }
            return '—';
        },

        formatDate(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr + 'T00:00:00');
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        }
    };
}
</script>
