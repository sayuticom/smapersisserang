<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\LessonSchedule;
use App\Models\LessonScheduleSetting;
use App\Models\SchoolClass;
use App\Models\SchoolSubject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class LessonScheduleController extends Controller
{
    private array $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    private array $schedulableTypes = ['pelajaran', 'kegiatan_khusus'];

    public function index(Request $request)
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $activeAcademicYears = $academicYears->where('is_current', true);
        $defaultAcademicYearId = $activeAcademicYears->count() === 1 ? $activeAcademicYears->first()->id : null;
        $academicYearId = $request->query('academic_year', $defaultAcademicYearId);
        $semester = $request->query('semester', 'ganjil');
        $classId = $request->query('class_id');

        $classes = SchoolClass::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        $timeSlotsByDay = LessonScheduleSetting::where('is_active', true)->orderBy('sort_order')->get()->groupBy('day');
        $teachers = Teacher::where('is_active', true)->orderBy('name')->get();
        $subjects = SchoolSubject::where('is_active', true)->orderBy('name')->get();

        $schedules = collect();
        $teacherSchedules = collect();

        if ($academicYearId && $semester && $classId) {
            $schedules = LessonSchedule::with(['lessonScheduleSetting', 'schoolSubject', 'teacher'])
                ->where('academic_year_id', $academicYearId)
                ->where('semester', $semester)
                ->where('school_class_id', $classId)
                ->get()
                ->groupBy(fn($s) => $s->day . '|' . $s->lesson_schedule_setting_id);
        }

        $teacherId = auth()->user()?->isAdmin() ? $request->query('teacher_id') : null;
        if ($academicYearId && $semester) {
            $q = LessonSchedule::with(['lessonScheduleSetting', 'schoolSubject', 'schoolClass'])
                ->where('academic_year_id', $academicYearId)
                ->where('semester', $semester);
            if ($teacherId) {
                $q->where('teacher_id', $teacherId);
            }
            $teacherSchedules = $q->get()->groupBy(fn($s) => $s->day . '|' . $s->lesson_schedule_setting_id);
        }

        $selectedAcademicYear = $academicYearId ? AcademicYear::find($academicYearId) : null;
        $days = $this->days;

        return view('admin.akademik.jadwal-pelajaran.index', compact(
            'academicYears', 'academicYearId', 'semester', 'classId',
            'classes', 'timeSlotsByDay', 'teachers', 'subjects',
            'schedules', 'teacherSchedules', 'teacherId',
            'selectedAcademicYear', 'days', 'activeAcademicYears'
        ));
    }

    public function create()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $classes = SchoolClass::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $timeSlotsByDay = LessonScheduleSetting::where('is_active', true)->whereIn('type', $this->schedulableTypes)->orderBy('sort_order')->get()->groupBy('day');
        $subjects = SchoolSubject::where('is_active', true)->orderBy('name')->get();
        $teachers = Teacher::where('is_active', true)->orderBy('name')->get();
        $days = $this->days;

        return view('admin.akademik.jadwal-pelajaran.create', compact(
            'academicYears', 'classes', 'timeSlotsByDay', 'subjects', 'teachers', 'days'
        ));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'semester' => ['required', 'in:ganjil,genap'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'day' => ['required', 'in:' . implode(',', $this->days)],
            'lesson_schedule_setting_id' => ['required', 'exists:lesson_schedule_settings,id'],
            'school_subject_id' => ['required', 'exists:school_subjects,id'],
            'teacher_id' => ['required', 'exists:teachers,id'],
            'room' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $day = $validated['day'];
        $setting = LessonScheduleSetting::findOrFail($validated['lesson_schedule_setting_id']);
        if ($setting->day !== $day) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['lesson_schedule_setting_id' => 'Slot ' . $setting->name . ' tidak tersedia untuk hari ' . $day . '.']);
        }
        if (!in_array($setting->type, $this->schedulableTypes)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['lesson_schedule_setting_id' => 'Slot ' . $setting->name . ' bukan jam pelajaran. Hanya slot pelajaran yang dapat diisi jadwal.']);
        }

        $collisions = $this->checkCollisions(
            $validated['academic_year_id'],
            $validated['semester'],
            $validated['day'],
            $validated['lesson_schedule_setting_id'],
            $validated['school_class_id'],
            $validated['teacher_id'],
            $validated['room'] ?? null,
            null
        );

        if (!empty($collisions)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['collision' => implode(' ', $collisions)]);
        }

        LessonSchedule::create($validated);

        return redirect()->route('admin.akademik.jadwal-pelajaran.index', [
            'academic_year' => $validated['academic_year_id'],
            'semester' => $validated['semester'],
            'class_id' => $validated['school_class_id'],
        ])->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(LessonSchedule $lessonSchedule)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $classes = SchoolClass::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $timeSlotsByDay = LessonScheduleSetting::where('is_active', true)->whereIn('type', $this->schedulableTypes)->orderBy('sort_order')->get()->groupBy('day');
        $subjects = SchoolSubject::where('is_active', true)->orderBy('name')->get();
        $teachers = Teacher::where('is_active', true)->orderBy('name')->get();
        $days = $this->days;

        return view('admin.akademik.jadwal-pelajaran.edit', compact(
            'lessonSchedule', 'academicYears', 'classes', 'timeSlotsByDay', 'subjects', 'teachers', 'days'
        ));
    }

    public function update(Request $request, LessonSchedule $lessonSchedule)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'semester' => ['required', 'in:ganjil,genap'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'day' => ['required', 'in:' . implode(',', $this->days)],
            'lesson_schedule_setting_id' => ['required', 'exists:lesson_schedule_settings,id'],
            'school_subject_id' => ['required', 'exists:school_subjects,id'],
            'teacher_id' => ['required', 'exists:teachers,id'],
            'room' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $collisions = $this->checkCollisions(
            $validated['academic_year_id'],
            $validated['semester'],
            $validated['day'],
            $validated['lesson_schedule_setting_id'],
            $validated['school_class_id'],
            $validated['teacher_id'],
            $validated['room'] ?? null,
            $lessonSchedule->id
        );

        if (!empty($collisions)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['collision' => implode(' ', $collisions)]);
        }

        $lessonSchedule->update($validated);

        return redirect()->route('admin.akademik.jadwal-pelajaran.index', [
            'academic_year' => $validated['academic_year_id'],
            'semester' => $validated['semester'],
            'class_id' => $validated['school_class_id'],
        ])->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(LessonSchedule $lessonSchedule)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $params = [
            'academic_year' => $lessonSchedule->academic_year_id,
            'semester' => $lessonSchedule->semester,
            'class_id' => $lessonSchedule->school_class_id,
        ];
        $lessonSchedule->delete();

        return redirect()->route('admin.akademik.jadwal-pelajaran.index', $params)
            ->with('success', 'Jadwal berhasil dihapus.');
    }

    private function checkCollisions(
        int $academicYearId,
        string $semester,
        string $day,
        int $timeSlotId,
        int $classId,
        int $teacherId,
        ?string $room,
        ?int $excludeId
    ): array {
        $errors = [];

        $existingClass = LessonSchedule::where('academic_year_id', $academicYearId)
            ->where('semester', $semester)
            ->where('day', $day)
            ->where('lesson_schedule_setting_id', $timeSlotId)
            ->where('school_class_id', $classId)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($existingClass) {
            $errors[] = 'Kelas ini sudah memiliki jadwal pada jam tersebut.';
        }

        $existingTeacher = LessonSchedule::where('academic_year_id', $academicYearId)
            ->where('semester', $semester)
            ->where('day', $day)
            ->where('lesson_schedule_setting_id', $timeSlotId)
            ->where('teacher_id', $teacherId)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($existingTeacher) {
            $errors[] = 'Guru ini sudah mengajar di kelas lain pada jam yang sama.';
        }

        if ($room) {
            $existingRoom = LessonSchedule::where('academic_year_id', $academicYearId)
                ->where('semester', $semester)
                ->where('day', $day)
                ->where('lesson_schedule_setting_id', $timeSlotId)
                ->where('room', $room)
                ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
                ->exists();

            if ($existingRoom) {
                $errors[] = 'Ruangan ini sudah digunakan pada jam yang sama.';
            }
        }

        return $errors;
    }
}
