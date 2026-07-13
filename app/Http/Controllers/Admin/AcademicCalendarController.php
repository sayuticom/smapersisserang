<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAcademicCalendarEventRequest;
use App\Http\Requests\Admin\UpdateAcademicCalendarEventRequest;
use App\Models\AcademicCalendarEvent;
use App\Models\AcademicYear;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AcademicCalendarController extends Controller
{
    public function index(Request $request): View
    {
        $categoryMap = $this->getCategoryMap();

        $indonesianMonths = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        if ($academicYears->isEmpty()) {
            return view('admin.academic.calendar.index', [
                'academicYear' => null,
                'academicYearId' => null,
                'academicYears' => collect(),
                'events' => collect(),
                'summary' => [
                    'hari_efektif' => 0,
                    'hari_libur' => 0,
                    'asesmen' => 0,
                    'total' => 0,
                    'kegiatan_terdekat' => null,
                ],
                'categories' => $categoryMap->values()->map(fn($c) => ['key' => '', 'label' => $c['label'], 'dot_class' => $c['dot_class'], 'bg_class' => $c['bg_class'], 'text_class' => $c['text_class'], 'cell_bg' => $c['cell_bg'], 'count' => 0]),
                'upcomingEvents' => collect(),
                'categoryMap' => $categoryMap,
                'rawEvents' => collect(),
                'months' => [],
            ]);
        }

        $selectedYearId = $request->integer('academic_year_id');

        if (!$selectedYearId || !$academicYears->contains('id', $selectedYearId)) {
            $current = $academicYears->firstWhere('is_current', true) ?? $academicYears->first();
            $selectedYearId = $current->id;
        }

        $selectedYear = $academicYears->firstWhere('id', $selectedYearId);

        $dbEvents = AcademicCalendarEvent::with('teacher')
            ->where('academic_year_id', $selectedYearId)
            ->where('status', 'published')
            ->orderBy('start_date')
            ->orderBy('title')
            ->get();

        $rawEvents = $dbEvents->map(function ($evt) use ($categoryMap) {
            $cat = $categoryMap[$evt->category] ?? $categoryMap['lainnya'];
            return [
                'id' => $evt->id,
                'title' => $evt->title,
                'category' => $evt->category,
                'category_label' => $cat['label'],
                'source' => $evt->source,
                'start_date' => $evt->start_date->toDateString(),
                'end_date' => $evt->end_date->toDateString(),
                'is_holiday' => $evt->is_holiday,
                'is_effective_day' => $evt->is_effective_day,
                'day_status' => $evt->day_status,
                'description' => $evt->description,
                'location' => $evt->location,
                'start_time' => $evt->start_time ? \Carbon\Carbon::parse($evt->start_time)->format('H:i') : null,
                'end_time' => $evt->end_time ? \Carbon\Carbon::parse($evt->end_time)->format('H:i') : null,
                'person_in_charge' => $evt->person_in_charge,
                'teacher' => $evt->teacher,
                'targets' => $evt->targets,
            ];
        });

        // Expand date ranges for calendar
        $expandedEvents = collect();
        $holidayDates = collect();
        $assessmentDates = collect();

        foreach ($rawEvents as $evt) {
            $start = \Carbon\Carbon::parse($evt['start_date']);
            $end = \Carbon\Carbon::parse($evt['end_date']);
            $cat = $categoryMap[$evt['category']] ?? $categoryMap['lainnya'];

            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                $dateStr = $d->format('Y-m-d');
                $expandedEvents->push([
                    'date' => $dateStr,
                    'title' => $evt['title'],
                    'category' => $evt['category'],
                    'category_label' => $cat['label'],
                    'dot_class' => $cat['dot_class'],
                    'bg_class' => $cat['bg_class'],
                    'text_class' => $cat['text_class'],
                    'cell_bg' => $cat['cell_bg'],
                    'source' => $evt['source'],
                    'is_holiday' => $evt['is_holiday'],
                    'is_effective_day' => $evt['is_effective_day'],
                    'description' => $evt['description'],
                ]);

                if ($evt['is_holiday']) {
                    $holidayDates->push($dateStr);
                }
                if (in_array($evt['category'], ['asesmen-ujian', 'tka-asesmen-nasional'])) {
                    $assessmentDates->push($dateStr);
                }
            }
        }

        $uniqueHolidayCount = $holidayDates->unique()->count();
        $uniqueAssessmentCount = $assessmentDates->unique()->count();

        // Categories with date counts
        $categories = $categoryMap->map(function ($c, $key) use ($expandedEvents) {
            $uniqueDates = $expandedEvents->where('category', $key)->pluck('date')->unique();
            return [
                'key' => $key,
                'label' => $c['label'],
                'dot_class' => $c['dot_class'],
                'bg_class' => $c['bg_class'],
                'text_class' => $c['text_class'],
                'cell_bg' => $c['cell_bg'],
                'count' => $uniqueDates->count(),
            ];
        })->values();

        // Upcoming events
        $today = now()->format('Y-m-d');
        $upcomingEvents = collect();
        $nearestEvent = null;

        $sortedEvents = $rawEvents->filter(fn($e) => $e['end_date'] >= $today)->sort(function ($a, $b) use ($today) {
            $aOngoing = ($a['start_date'] <= $today && $a['end_date'] >= $today) ? 0 : 1;
            $bOngoing = ($b['start_date'] <= $today && $b['end_date'] >= $today) ? 0 : 1;
            if ($aOngoing !== $bOngoing) return $aOngoing - $bOngoing;
            if ($a['start_date'] !== $b['start_date']) return $a['start_date'] <=> $b['start_date'];
            return $a['title'] <=> $b['title'];
        });

        foreach ($sortedEvents->take(4) as $e) {
            $cat = $categoryMap[$e['category']] ?? $categoryMap['lainnya'];
            $startTime = $e['start_time'] ?? null;
            $endTime = $e['end_time'] ?? null;
            $timeStr = $startTime ? $startTime . '–' . $endTime : null;
            $upcomingEvents->push([
                'date' => $this->formatEventDateRange($e['start_date'], $e['end_date'], $indonesianMonths),
                'time' => $timeStr,
                'title' => $e['title'],
                'category_label' => $cat['label'],
                'dot_class' => $cat['dot_class'],
                'bg_class' => $cat['bg_class'],
            ]);
        }

        if ($sortedEvents->isNotEmpty()) {
            $first = $sortedEvents->first();
            $cat = $categoryMap[$first['category']] ?? $categoryMap['lainnya'];
            $nearestEventStartTime = $first['start_time'] ?? null;
            $nearestEventEndTime = $first['end_time'] ?? null;
            $nearestEventTimeStr = $nearestEventStartTime ? $nearestEventStartTime . '–' . $nearestEventEndTime : null;
            $nearestEvent = [
                'title' => $first['title'],
                'date' => $this->formatEventDateRange($first['start_date'], $first['end_date'], $indonesianMonths),
                'time' => $nearestEventTimeStr,
                'category' => $first['category'],
                'cat' => $cat,
            ];
        }

        $summary = [
            'hari_efektif' => 220,
            'hari_libur' => $uniqueHolidayCount,
            'asesmen' => $uniqueAssessmentCount,
            'total' => $dbEvents->count(),
            'kegiatan_terdekat' => $nearestEvent,
        ];

        $months = [];
        $startMonth = $selectedYear->start_date->copy()->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $date = $startMonth->copy()->addMonths($i);
            $months[] = [
                'num' => $date->month,
                'year' => $date->year,
                'name' => $indonesianMonths[$date->month],
            ];
        }

        $events = $expandedEvents;

        return view('admin.academic.calendar.index', compact(
            'academicYears',
            'events',
            'summary',
            'categories',
            'upcomingEvents',
            'categoryMap',
            'rawEvents',
            'months',
            'indonesianMonths',
        ) + [
            'academicYear' => $selectedYear,
            'academicYearId' => $selectedYearId,
        ]);
    }

    private function getCategoryMap(): \Illuminate\Support\Collection
    {
        return collect([
            'awal-masuk' => [
                'label' => 'Awal Masuk Sekolah',
                'dot_class' => 'bg-green-500',
                'bg_class' => 'bg-green-100',
                'text_class' => 'text-green-800',
                'cell_bg' => 'bg-green-100',
            ],
            'libur-nasional' => [
                'label' => 'Libur Nasional / Cuti Bersama',
                'dot_class' => 'bg-red-500',
                'bg_class' => 'bg-red-100',
                'text_class' => 'text-red-800',
                'cell_bg' => 'bg-red-100',
            ],
            'penyerahan-rapor' => [
                'label' => 'Penyerahan Rapor',
                'dot_class' => 'bg-orange-500',
                'bg_class' => 'bg-orange-100',
                'text_class' => 'text-orange-800',
                'cell_bg' => 'bg-orange-100',
            ],
            'libur-ramadan' => [
                'label' => 'Libur Ramadan / Idulfitri',
                'dot_class' => 'bg-amber-500',
                'bg_class' => 'bg-amber-100',
                'text_class' => 'text-amber-800',
                'cell_bg' => 'bg-amber-100',
            ],
            'asesmen-ujian' => [
                'label' => 'Asesmen / Ujian',
                'dot_class' => 'bg-blue-500',
                'bg_class' => 'bg-blue-100',
                'text_class' => 'text-blue-800',
                'cell_bg' => 'bg-blue-100',
            ],
            'libur-semester' => [
                'label' => 'Libur Semester',
                'dot_class' => 'bg-yellow-500',
                'bg_class' => 'bg-yellow-100',
                'text_class' => 'text-yellow-800',
                'cell_bg' => 'bg-yellow-100',
            ],
            'tka-asesmen-nasional' => [
                'label' => 'TKA / Asesmen Nasional',
                'dot_class' => 'bg-purple-500',
                'bg_class' => 'bg-purple-100',
                'text_class' => 'text-purple-800',
                'cell_bg' => 'bg-purple-100',
            ],
            'kegiatan-sekolah' => [
                'label' => 'Kegiatan Sekolah',
                'dot_class' => 'bg-violet-500',
                'bg_class' => 'bg-violet-100',
                'text_class' => 'text-violet-800',
                'cell_bg' => 'bg-violet-100',
            ],
            'kegiatan-pesantren' => [
                'label' => 'Kegiatan Pesantren',
                'dot_class' => 'bg-teal-500',
                'bg_class' => 'bg-teal-100',
                'text_class' => 'text-teal-800',
                'cell_bg' => 'bg-teal-100',
            ],
            'lainnya' => [
                'label' => 'Lainnya',
                'dot_class' => 'bg-slate-500',
                'bg_class' => 'bg-slate-100',
                'text_class' => 'text-slate-800',
                'cell_bg' => 'bg-slate-100',
            ],
        ]);
    }

    public function create(): View
    {
        try {
            $currentAcademicYear = AcademicYear::where('is_current', true)->first();
        } catch (\Exception $e) {
            $currentAcademicYear = null;
        }

        $categories = collect([
            ['value' => 'awal-masuk', 'label' => 'Awal Masuk Sekolah'],
            ['value' => 'libur-nasional', 'label' => 'Libur Nasional / Cuti Bersama'],
            ['value' => 'penyerahan-rapor', 'label' => 'Penyerahan Rapor'],
            ['value' => 'libur-ramadan', 'label' => 'Libur Ramadan / Idulfitri'],
            ['value' => 'asesmen-ujian', 'label' => 'Asesmen / Ujian'],
            ['value' => 'libur-semester', 'label' => 'Libur Semester'],
            ['value' => 'tka-asesmen-nasional', 'label' => 'TKA / Asesmen Nasional'],
            ['value' => 'kegiatan-sekolah', 'label' => 'Kegiatan Sekolah'],
            ['value' => 'kegiatan-pesantren', 'label' => 'Kegiatan Pesantren'],
            ['value' => 'lainnya', 'label' => 'Lainnya'],
        ]);

        $sources = collect([
            ['value' => 'sekolah', 'label' => 'Sekolah'],
            ['value' => 'pemerintah', 'label' => 'Pemerintah'],
        ]);

        $statusDays = collect([
            ['value' => 'efektif', 'label' => 'Hari Efektif'],
            ['value' => 'tidak-efektif', 'label' => 'Hari Tidak Efektif'],
            ['value' => 'libur', 'label' => 'Libur'],
            ['value' => 'kegiatan-khusus', 'label' => 'Kegiatan Khusus'],
        ]);

        $targets = collect([
            ['value' => 'semua', 'label' => 'Semua'],
            ['value' => 'guru', 'label' => 'Guru'],
            ['value' => 'siswa', 'label' => 'Siswa'],
            ['value' => 'orang_tua', 'label' => 'Orang Tua'],
            ['value' => 'asrama', 'label' => 'Asrama'],
            ['value' => 'publik', 'label' => 'Publik'],
        ]);

        $teachers = Teacher::where('is_active', true)->orderBy('name')->get();

        return view('admin.academic.calendar.create', compact(
            'categories',
            'sources',
            'statusDays',
            'targets',
            'teachers',
            'currentAcademicYear',
        ));
    }

    public function store(StoreAcademicCalendarEventRequest $request)
    {
        $validated = $request->validated();

        $responsibleType = $validated['responsible_type'];
        unset($validated['responsible_type']);

        $dayStatus = $validated['day_status'];

        if ($dayStatus === 'efektif') {
            $validated['is_holiday'] = false;
            $validated['is_effective_day'] = true;
        } elseif ($dayStatus === 'libur') {
            $validated['is_holiday'] = true;
            $validated['is_effective_day'] = false;
        } elseif ($dayStatus === 'tidak-efektif') {
            $validated['is_holiday'] = false;
            $validated['is_effective_day'] = false;
        }

        $validated['is_all_day'] = false;

        if ($responsibleType === 'teacher') {
            $teacher = Teacher::find($validated['teacher_id']);
            $validated['person_in_charge'] = $teacher?->name;
        } else {
            $validated['teacher_id'] = null;
        }

        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        $event = DB::transaction(function () use ($validated) {
            return AcademicCalendarEvent::create($validated);
        });

        $message = $validated['status'] === 'draft'
            ? 'Draft kegiatan kalender berhasil disimpan.'
            : 'Kegiatan kalender berhasil dipublikasikan.';

        return redirect()
            ->route('admin.akademik.kalender.index', ['academic_year_id' => $event->academic_year_id])
            ->with('success', $message);
    }

    public function edit(AcademicCalendarEvent $academicCalendarEvent): View
    {
        $currentAcademicYear = $academicCalendarEvent->academicYear;

        $categories = collect([
            ['value' => 'awal-masuk', 'label' => 'Awal Masuk Sekolah'],
            ['value' => 'libur-nasional', 'label' => 'Libur Nasional / Cuti Bersama'],
            ['value' => 'penyerahan-rapor', 'label' => 'Penyerahan Rapor'],
            ['value' => 'libur-ramadan', 'label' => 'Libur Ramadan / Idulfitri'],
            ['value' => 'asesmen-ujian', 'label' => 'Asesmen / Ujian'],
            ['value' => 'libur-semester', 'label' => 'Libur Semester'],
            ['value' => 'tka-asesmen-nasional', 'label' => 'TKA / Asesmen Nasional'],
            ['value' => 'kegiatan-sekolah', 'label' => 'Kegiatan Sekolah'],
            ['value' => 'kegiatan-pesantren', 'label' => 'Kegiatan Pesantren'],
            ['value' => 'lainnya', 'label' => 'Lainnya'],
        ]);

        $sources = collect([
            ['value' => 'sekolah', 'label' => 'Sekolah'],
            ['value' => 'pemerintah', 'label' => 'Pemerintah'],
        ]);

        $statusDays = collect([
            ['value' => 'efektif', 'label' => 'Hari Efektif'],
            ['value' => 'tidak-efektif', 'label' => 'Hari Tidak Efektif'],
            ['value' => 'libur', 'label' => 'Libur'],
            ['value' => 'kegiatan-khusus', 'label' => 'Kegiatan Khusus'],
        ]);

        $targets = collect([
            ['value' => 'semua', 'label' => 'Semua'],
            ['value' => 'guru', 'label' => 'Guru'],
            ['value' => 'siswa', 'label' => 'Siswa'],
            ['value' => 'orang_tua', 'label' => 'Orang Tua'],
            ['value' => 'asrama', 'label' => 'Asrama'],
            ['value' => 'publik', 'label' => 'Publik'],
        ]);

        $teachers = Teacher::where('is_active', true)->orderBy('name')->get();

        if ($academicCalendarEvent->teacher_id) {
            $eventTeacher = Teacher::find($academicCalendarEvent->teacher_id);
            if ($eventTeacher && !$eventTeacher->is_active) {
                $teachers = $teachers->push($eventTeacher)->sortBy('name');
            }
        }

        return view('admin.academic.calendar.edit', compact(
            'academicCalendarEvent',
            'categories',
            'sources',
            'statusDays',
            'targets',
            'teachers',
            'currentAcademicYear',
        ));
    }

    public function update(UpdateAcademicCalendarEventRequest $request, AcademicCalendarEvent $academicCalendarEvent)
    {
        $validated = $request->validated();

        $responsibleType = $validated['responsible_type'];
        unset($validated['responsible_type']);

        $dayStatus = $validated['day_status'];

        if ($dayStatus === 'efektif') {
            $validated['is_holiday'] = false;
            $validated['is_effective_day'] = true;
        } elseif ($dayStatus === 'libur') {
            $validated['is_holiday'] = true;
            $validated['is_effective_day'] = false;
        } elseif ($dayStatus === 'tidak-efektif') {
            $validated['is_holiday'] = false;
            $validated['is_effective_day'] = false;
        }

        $validated['is_all_day'] = false;

        if ($responsibleType === 'teacher') {
            $teacher = Teacher::find($validated['teacher_id']);
            $validated['person_in_charge'] = $teacher?->name;
        } else {
            $validated['teacher_id'] = null;
        }

        $validated['updated_by'] = auth()->id();

        DB::transaction(function () use ($validated, $academicCalendarEvent) {
            $academicCalendarEvent->update($validated);
        });

        return redirect()
            ->route('admin.akademik.kalender.index', ['academic_year_id' => $academicCalendarEvent->academic_year_id])
            ->with('success', 'Kegiatan kalender berhasil diperbarui.');
    }

    public function destroy(AcademicCalendarEvent $academicCalendarEvent)
    {
        $yearId = $academicCalendarEvent->academic_year_id;

        $academicCalendarEvent->delete();

        return redirect()
            ->route('admin.akademik.kalender.index', ['academic_year_id' => $yearId])
            ->with('success', 'Kegiatan kalender berhasil dihapus.');
    }

    private function formatEventDateRange(string $startDate, string $endDate, array $indonesianMonths): string
    {
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);

        if ($start->toDateString() === $end->toDateString()) {
            return $start->format('j') . ' ' . $indonesianMonths[$start->month] . ' ' . $start->format('Y');
        }

        if ($start->month === $end->month && $start->year === $end->year) {
            return $start->format('j') . '–' . $end->format('j') . ' ' . $indonesianMonths[$start->month] . ' ' . $start->format('Y');
        }

        if ($start->year === $end->year) {
            return $start->format('j') . ' ' . $indonesianMonths[$start->month] . '–' . $end->format('j') . ' ' . $indonesianMonths[$end->month] . ' ' . $start->format('Y');
        }

        return $start->format('j') . ' ' . $indonesianMonths[$start->month] . ' ' . $start->format('Y') . '–' . $end->format('j') . ' ' . $indonesianMonths[$end->month] . ' ' . $end->format('Y');
    }
}
