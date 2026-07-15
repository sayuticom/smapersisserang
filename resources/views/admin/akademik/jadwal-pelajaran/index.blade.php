<x-admin-layout>
    @php
        $isAdmin = auth()->user()?->isAdmin();
        $displayMode = $teacherId ? 'teacher' : 'class';
        $hasPrimaryFilter = $academicYearId && $semester && ($classId || $teacherId);
        $schedulableTypes = ['pelajaran', 'kegiatan_khusus'];
        $nonScheduleStyles = [
            'istirahat' => 'border-amber-200 bg-amber-50 text-amber-800',
            'ishoma' => 'border-sky-200 bg-sky-50 text-sky-800',
            'upacara' => 'border-rose-200 bg-rose-50 text-rose-800',
            'pembiasaan' => 'border-violet-200 bg-violet-50 text-violet-800',
        ];
        $slotsByOrder = $timeSlotsByDay->flatten()->groupBy('sort_order')->sortKeys();
    @endphp

    <div class="space-y-6"
         x-data="{
             selectedClass: @js((string) ($classId ?? '')),
             selectedTeacher: @js((string) ($teacherId ?? '')),
             filterDirty: false,
             selectClass() {
                 if (this.selectedClass) this.selectedTeacher = '';
                 this.filterDirty = true;
             },
             selectTeacher() {
                 if (this.selectedTeacher) this.selectedClass = '';
                 this.filterDirty = true;
             }
         }">
        <header class="border-b border-slate-200 pb-5">
            <h1 class="text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">Jadwal Mata Pelajaran</h1>
            <p class="mt-1.5 text-sm text-slate-600">Lihat susunan jadwal mingguan berdasarkan kelas atau guru.</p>
        </header>

        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-inside list-disc space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6" aria-labelledby="filter-heading">
            <div class="mb-4">
                <h2 id="filter-heading" class="text-base font-bold text-slate-900">Filter Jadwal</h2>
                <p class="mt-0.5 text-sm text-slate-500">Pilih kelas atau guru, lalu tampilkan jadwal.</p>
            </div>

            @if($activeAcademicYears->isEmpty())
                <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800">
                    Belum ada tahun ajaran aktif.
                </div>
            @endif

            <form method="GET" action="{{ route('admin.akademik.jadwal-pelajaran.index') }}" class="grid grid-cols-1 items-end gap-4 sm:grid-cols-2 xl:grid-cols-5">
                <div>
                    <label for="academic_year" class="mb-1.5 block text-sm font-semibold text-slate-700">Tahun Ajaran</label>
                    <select id="academic_year" name="academic_year" class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Pilih tahun ajaran</option>
                        @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}" @selected($ay->id == $academicYearId)>{{ $ay->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="semester" class="mb-1.5 block text-sm font-semibold text-slate-700">Semester</label>
                    <select id="semester" name="semester" class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="ganjil" @selected($semester === 'ganjil')>Ganjil</option>
                        <option value="genap" @selected($semester === 'genap')>Genap</option>
                    </select>
                </div>

                <div>
                    <label for="class_id" class="mb-1.5 block text-sm font-semibold text-slate-700">Kelas</label>
                    <select id="class_id" name="class_id" x-model="selectedClass" @change="selectClass()"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Pilih kelas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" @selected($class->id == $classId)>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if($isAdmin)
                    <div>
                        <label for="teacher_id" class="mb-1.5 block text-sm font-semibold text-slate-700">Guru <span class="font-normal text-slate-400">(opsional)</span></label>
                        <select id="teacher_id" name="teacher_id" x-model="selectedTeacher" @change="selectTeacher()"
                                class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="" @selected(!$teacherId)>Pilih guru</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" @selected($teacher->id == $teacherId)>{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="flex gap-2 sm:col-span-2 xl:col-span-1">
                    <button type="submit" class="inline-flex flex-1 items-center justify-center whitespace-nowrap rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        Tampilkan Jadwal
                    </button>
                    <a href="{{ route('admin.akademik.jadwal-pelajaran.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Reset
                    </a>
                </div>
            </form>
        </section>

        @unless($hasPrimaryFilter)
            <section class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-white text-slate-400 shadow-sm">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3M5 11h14M6 21h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="mt-4 text-base font-bold text-slate-800">Jadwal belum ditampilkan</h2>
                <p class="mx-auto mt-1 max-w-xl text-sm leading-6 text-slate-600"
                   x-text="selectedClass || selectedTeacher ? 'Klik Tampilkan Jadwal untuk melihat jadwal.' : 'Pilih kelas atau guru untuk menampilkan jadwal.'">
                    Pilih kelas atau guru untuk menampilkan jadwal.
                </p>
            </section>
        @else
            <section x-show="filterDirty" x-cloak class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-white text-slate-400 shadow-sm">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3M5 11h14M6 21h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="mt-4 text-base font-bold text-slate-800">Filter belum diterapkan</h2>
                <p class="mx-auto mt-1 max-w-xl text-sm leading-6 text-slate-600"
                   x-text="selectedClass || selectedTeacher ? 'Klik Tampilkan Jadwal untuk melihat jadwal.' : 'Pilih kelas atau guru untuk menampilkan jadwal.'"></p>
            </section>

            <section x-show="!filterDirty" class="space-y-4">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">Jadwal Mingguan</p>
                        <h2 class="mt-1 text-xl font-bold text-slate-950">
                            @if($displayMode === 'teacher')
                                {{ $teachers->firstWhere('id', $teacherId)?->name }}
                            @else
                                Kelas {{ $classes->firstWhere('id', $classId)?->name }}
                            @endif
                        </h2>
                        <p class="mt-0.5 text-sm text-slate-500">{{ $selectedAcademicYear?->name }} · Semester {{ ucfirst($semester) }}</p>
                    </div>
                    @unless($displayMode === 'teacher' && $teacherSchedules->isEmpty())
                        <div class="flex flex-wrap gap-2 text-xs font-medium text-slate-600">
                            <span class="rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1">Istirahat</span>
                            <span class="rounded-full border border-sky-200 bg-sky-50 px-2.5 py-1">Ishoma</span>
                            <span class="rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1">Upacara</span>
                            <span class="rounded-full border border-violet-200 bg-violet-50 px-2.5 py-1">Pembiasaan</span>
                        </div>
                    @endunless
                </div>

                @if($displayMode === 'teacher' && $teacherSchedules->isEmpty())
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-white text-slate-400 shadow-sm">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2m7-10a4 4 0 100-8 4 4 0 000 8zm13 10v-2a4 4 0 00-3-3.87m-1-7.26a4 4 0 010 7.75"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-base font-bold text-slate-800">Belum ada jadwal mengajar</h3>
                        <p class="mx-auto mt-1 max-w-2xl text-sm leading-6 text-slate-600">
                            Belum ada jadwal mengajar untuk guru ini pada Tahun Ajaran {{ $selectedAcademicYear?->academic_year ?? $selectedAcademicYear?->name }} Semester {{ ucfirst($semester) }}.
                        </p>
                        <p class="mx-auto mt-2 max-w-2xl text-xs leading-5 text-slate-500">Kegiatan umum seperti istirahat tidak dihitung sebagai jadwal mengajar guru.</p>
                    </div>
                @else
                {{-- Mobile: tab per hari --}}
                <div class="sm:hidden" x-data="{ activeDay: 'Senin' }">
                    <div class="mb-4 overflow-x-auto pb-1">
                        <div class="inline-flex min-w-full gap-1 rounded-xl border border-slate-200 bg-slate-100 p-1">
                            @foreach($days as $day)
                                <button type="button" @click="activeDay = '{{ $day }}'"
                                        :class="activeDay === '{{ $day }}' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-600'"
                                        class="min-w-[72px] flex-1 rounded-lg px-3 py-2 text-sm font-semibold transition">
                                    {{ $day }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    @foreach($days as $day)
                        <div x-show="activeDay === '{{ $day }}'" x-cloak class="space-y-2">
                            @forelse($timeSlotsByDay->get($day, collect()) as $slot)
                                @php
                                    $key = $day . '|' . $slot->id;
                                    $entry = $displayMode === 'teacher' ? $teacherSchedules->get($key)?->first() : $schedules->get($key)?->first();
                                    $isSchedulable = in_array($slot->type, $schedulableTypes);
                                    $specialStyle = $nonScheduleStyles[$slot->type] ?? 'border-slate-200 bg-slate-100 text-slate-700';
                                @endphp
                                <article class="rounded-xl border p-3.5 {{ $isSchedulable ? 'border-slate-200 bg-white shadow-sm' : $specialStyle }}">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-wide {{ $isSchedulable ? 'text-slate-500' : '' }}">{{ $slot->name }}</p>
                                            <p class="mt-0.5 text-xs {{ $isSchedulable ? 'text-slate-400' : 'opacity-80' }}">
                                                {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}
                                            </p>
                                        </div>
                                        @if(!$isSchedulable)
                                            <span class="rounded-full bg-white/70 px-2 py-0.5 text-[10px] font-bold uppercase">Non-jadwal</span>
                                        @endif
                                    </div>

                                    @if($isSchedulable && $entry)
                                        <div class="mt-3 border-t border-slate-100 pt-3">
                                            <h3 class="font-bold leading-snug text-slate-950">{{ $entry->schoolSubject->name }}</h3>
                                            <p class="mt-1 text-sm text-slate-600">{{ $entry->teacher?->name ?? 'Guru belum ditentukan' }}</p>
                                            @if($displayMode === 'teacher')
                                                <p class="mt-0.5 text-xs font-semibold text-slate-600">Kelas {{ $entry->schoolClass->name }}</p>
                                            @endif
                                            @if($entry->room)
                                                <p class="mt-0.5 text-xs font-medium text-slate-500">Ruang {{ $entry->room }}</p>
                                            @endif
                                            @if($isAdmin)
                                                <div class="mt-3 flex items-center gap-3 border-t border-slate-100 pt-2.5">
                                                    <a href="{{ route('admin.akademik.jadwal-pelajaran.edit', $entry) }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">Edit</a>
                                                    <form method="POST" action="{{ route('admin.akademik.jadwal-pelajaran.destroy', $entry) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-xs font-bold text-red-600 hover:text-red-700">Hapus</button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    @elseif($isSchedulable && $isAdmin && $classId && !$teacherId)
                                        <a href="{{ route('admin.akademik.jadwal-pelajaran.create', ['academic_year' => $academicYearId, 'semester' => $semester, 'class_id' => $classId, 'day' => $day, 'time_slot' => $slot->id]) }}"
                                           class="mt-3 inline-flex items-center rounded-lg border border-dashed border-emerald-300 px-3 py-1.5 text-xs font-bold text-emerald-700 hover:bg-emerald-50">
                                            + Tambah
                                        </a>
                                    @endif
                                </article>
                            @empty
                                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">Belum ada slot untuk hari {{ $day }}.</div>
                            @endforelse
                        </div>
                    @endforeach
                </div>

                {{-- Desktop: matriks mingguan --}}
                <div class="hidden overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-sm sm:block">
                    <div class="overflow-x-auto">
                        <table class="min-w-[1100px] w-full table-fixed border-collapse text-left">
                            <thead>
                                <tr class="bg-slate-900 text-white">
                                    <th scope="col" class="w-16 border-r border-slate-700 px-4 py-3.5 text-xs font-bold uppercase tracking-wider">NO</th>
                                    @foreach($days as $day)
                                        <th scope="col" class="border-r border-slate-700 px-3 py-3.5 text-center text-sm font-bold last:border-r-0">{{ $day }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach($slotsByOrder as $sortOrder => $rowSlots)
                                    <tr class="align-top hover:bg-slate-50/60">
                                        <th scope="row" class="w-16 border-r border-slate-200 bg-slate-50 px-3 py-3 text-center">
                                            <span class="text-xs font-bold text-slate-900">{{ $sortOrder }}</span>
                                        </th>

                                        @foreach($days as $day)
                                            @php
                                                $slot = $timeSlotsByDay->get($day, collect())->firstWhere('sort_order', $sortOrder);
                                                $entry = $slot ? ($displayMode === 'teacher' ? $teacherSchedules->get($day . '|' . $slot->id)?->first() : $schedules->get($day . '|' . $slot->id)?->first()) : null;
                                                $isSchedulable = $slot && in_array($slot->type, $schedulableTypes);
                                                $specialStyle = $slot ? ($nonScheduleStyles[$slot->type] ?? '') : '';
                                            @endphp
                                            <td class="h-28 border-r border-slate-200 p-2 last:border-r-0">
                                                @if(!$slot)
                                                    <div class="h-full rounded-lg bg-slate-50/60"></div>
                                                @elseif(!$isSchedulable)
                                                    <div class="flex h-full min-h-24 flex-col items-center justify-center rounded-lg border px-2 py-3 text-center {{ $specialStyle }}">
                                                        <span class="text-xs font-bold">{{ $slot->name }}</span>
                                                        <span class="mt-1 text-[10px] font-medium opacity-75">{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</span>
                                                    </div>
                                                @elseif($entry)
                                                    <div class="flex h-full min-h-24 flex-col rounded-lg border border-emerald-200 bg-emerald-50/60 p-2.5">
                                                        <div class="text-[10px] font-bold uppercase tracking-wide text-slate-500">{{ $slot->name }}</div>
                                                        <div class="mt-0.5 text-[10px] font-semibold text-slate-400">{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</div>
                                                        <div class="mt-1 text-sm font-bold leading-snug text-slate-950">{{ $entry->schoolSubject->name }}</div>
                                                        <div class="mt-1 text-xs leading-snug text-slate-600">{{ $entry->teacher?->name ?? 'Guru belum ditentukan' }}</div>
                                                        @if($displayMode === 'teacher')
                                                            <div class="mt-0.5 text-[11px] font-semibold text-slate-600">Kelas {{ $entry->schoolClass->name }}</div>
                                                        @endif
                                                        @if($entry->room)
                                                            <div class="mt-0.5 text-[11px] font-medium text-slate-500">Ruang {{ $entry->room }}</div>
                                                        @endif
                                                        @if($isAdmin)
                                                            <div class="mt-auto flex items-center gap-2 border-t border-emerald-100 pt-2">
                                                                <a href="{{ route('admin.akademik.jadwal-pelajaran.edit', $entry) }}" class="text-[11px] font-bold text-emerald-700 hover:text-emerald-900">Edit</a>
                                                                <form method="POST" action="{{ route('admin.akademik.jadwal-pelajaran.destroy', $entry) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="text-[11px] font-bold text-red-600 hover:text-red-800">Hapus</button>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @elseif($isAdmin && $classId && !$teacherId)
                                                    <div class="flex h-full min-h-24 flex-col items-center justify-center rounded-lg border border-dashed border-slate-200 bg-white">
                                                        <span class="text-[10px] font-bold uppercase tracking-wide text-slate-500">{{ $slot->name }}</span>
                                                        <span class="mb-1 mt-0.5 text-[10px] font-semibold text-slate-400">{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</span>
                                                        <a href="{{ route('admin.akademik.jadwal-pelajaran.create', ['academic_year' => $academicYearId, 'semester' => $semester, 'class_id' => $classId, 'day' => $day, 'time_slot' => $slot->id]) }}"
                                                           class="rounded-md px-2.5 py-1.5 text-xs font-bold text-emerald-700 transition hover:bg-emerald-50">
                                                            + Tambah
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="flex h-full min-h-24 flex-col items-center justify-start rounded-lg bg-slate-50/50 pt-3">
                                                        <span class="text-[10px] font-bold uppercase tracking-wide text-slate-500">{{ $slot->name }}</span>
                                                        <span class="mt-0.5 text-[10px] font-semibold text-slate-400">{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</span>
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </section>
        @endunless
    </div>
</x-admin-layout>
