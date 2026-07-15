<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\LessonSchedule;
use App\Models\LessonScheduleSetting;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicLessonScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $academicYears = AcademicYear::query()
            ->where('is_current', true)
            ->orderByDesc('start_date')
            ->get();
        $classes = SchoolClass::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $selectedClassValue = $request->string('class')->trim()->toString();
        $semester = 'ganjil';

        if ($selectedClassValue === '' && $classes->count() === 1) {
            $selectedClassValue = $classes->first()->name;
        }

        $selectedAcademicYear = $academicYears->first();
        $selectedClass = $classes->firstWhere('name', $selectedClassValue);
        $filtersComplete = (bool) ($selectedAcademicYear && $selectedClass);

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $timeSlotsByDay = collect();
        $schedules = collect();
        $hasSchedules = false;

        if ($filtersComplete) {
            $timeSlotsByDay = LessonScheduleSetting::query()
                ->where('is_active', true)
                ->whereIn('day', $days)
                ->orderBy('sort_order')
                ->get()
                ->groupBy('day');

            $schedules = LessonSchedule::query()
                ->with([
                    'schoolSubject:id,name',
                    'teacher:id,name',
                    'lessonScheduleSetting:id,name,day,start_time,end_time,type,sort_order',
                ])
                ->where('academic_year_id', $selectedAcademicYear->id)
                ->where('semester', $semester)
                ->where('school_class_id', $selectedClass->id)
                ->get()
                ->keyBy(fn(LessonSchedule $schedule) => $schedule->day . '|' . $schedule->lesson_schedule_setting_id);
            $hasSchedules = $schedules->isNotEmpty();
        }

        try {
            $schoolSetting = SchoolSetting::current();
        } catch (\Throwable) {
            $schoolSetting = null;
        }

        return view('pages.public-lesson-schedule', compact(
            'schoolSetting', 'academicYears', 'classes', 'selectedClassValue',
            'selectedAcademicYear', 'selectedClass', 'semester', 'filtersComplete',
            'hasSchedules', 'days', 'timeSlotsByDay', 'schedules'
        ));
    }
}
