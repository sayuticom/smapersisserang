<x-admin-layout>
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('admin.akademik.jadwal-pelajaran.index', ['academic_year' => $lessonSchedule->academic_year_id, 'semester' => $lessonSchedule->semester, 'class_id' => $lessonSchedule->school_class_id]) }}"
               class="text-sm text-emerald-600 hover:text-emerald-700">&larr; Kembali ke Jadwal</a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">Edit Jadwal</h2>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-6">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.akademik.jadwal-pelajaran.update', $lessonSchedule) }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran <span class="text-red-500">*</span></label>
                    <select name="academic_year_id" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}" {{ $ay->id == old('academic_year_id', $lessonSchedule->academic_year_id) ? 'selected' : '' }}>{{ $ay->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Semester <span class="text-red-500">*</span></label>
                    <select name="semester" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        <option value="ganjil" {{ old('semester', $lessonSchedule->semester) === 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="genap" {{ old('semester', $lessonSchedule->semester) === 'genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas <span class="text-red-500">*</span></label>
                    <select name="school_class_id" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $c->id == old('school_class_id', $lessonSchedule->school_class_id) ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hari <span class="text-red-500">*</span></label>
                    <select name="day" id="day-select" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        @foreach($days as $day)
                            <option value="{{ $day }}" {{ $day === old('day', $lessonSchedule->day) ? 'selected' : '' }}>{{ $day }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Pelajaran <span class="text-red-500">*</span></label>
                <select name="lesson_schedule_setting_id" id="time-slot-select" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @foreach($days as $day)
                        @php $daySlots = $timeSlotsByDay->get($day, collect()); @endphp
                        @foreach($daySlots as $slot)
                            <option value="{{ $slot->id }}" data-day="{{ $day }}" {{ $slot->id == old('lesson_schedule_setting_id', $lessonSchedule->lesson_schedule_setting_id) ? 'selected' : '' }}>
                                {{ $slot->name }} ({{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }})
                            </option>
                        @endforeach
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran <span class="text-red-500">*</span></label>
                    <select name="school_subject_id" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}" {{ $s->id == old('school_subject_id', $lessonSchedule->school_subject_id) ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Guru <span class="text-red-500">*</span></label>
                    <select name="teacher_id" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}" {{ $t->id == old('teacher_id', $lessonSchedule->teacher_id) ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ruangan</label>
                    <input type="text" name="room" value="{{ old('room', $lessonSchedule->room) }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <input type="text" name="notes" value="{{ old('notes', $lessonSchedule->notes) }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="px-6 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                    Simpan
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const daySelect = document.getElementById('day-select');
            const timeSlotSelect = document.getElementById('time-slot-select');

            function filterTimeSlots() {
                const selectedDay = daySelect.value;
                const options = timeSlotSelect.querySelectorAll('option');
                let firstVisible = null;
                options.forEach(function (opt) {
                    if (opt.dataset.day === selectedDay) {
                        opt.style.display = '';
                        if (!firstVisible) firstVisible = opt;
                    } else {
                        opt.style.display = 'none';
                    }
                });
            }

            if (daySelect) {
                daySelect.addEventListener('change', filterTimeSlots);
                filterTimeSlots();
            }
        });
    </script>
    @endpush
</x-admin-layout>
