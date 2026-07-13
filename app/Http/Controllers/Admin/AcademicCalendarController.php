<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAcademicCalendarEventRequest;
use App\Models\AcademicCalendarEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AcademicCalendarController extends Controller
{
    public function index(): View
    {
        return $this->renderIndex();
    }

    public function create(): View
    {
        $academicYear = '2026/2027';
        $academicYears = collect([
            ['value' => '2026/2027', 'label' => '2026/2027'],
            ['value' => '2025/2026', 'label' => '2025/2026'],
            ['value' => '2024/2025', 'label' => '2024/2025'],
        ]);

        $categories = collect([
            ['value' => 'awal-masuk', 'label' => 'Awal Masuk Sekolah'],
            ['value' => 'libur-nasional', 'label' => 'Libur Nasional / Cuti Bersama'],
            ['value' => 'penyerahan-rapor', 'label' => 'Penyerahan Rapor'],
            ['value' => 'libur-ramadan', 'label' => 'Libur Ramadan / Idulfitri'],
            ['value' => 'asesmen-ujian', 'label' => 'Asesmen / Ujian'],
            ['value' => 'libur-semester', 'label' => 'Libur Semester'],
            ['value' => 'tka-anas', 'label' => 'TKA / Asesmen Nasional'],
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
            ['value' => 'orang-tua', 'label' => 'Orang Tua'],
            ['value' => 'asrama', 'label' => 'Asrama'],
            ['value' => 'publik', 'label' => 'Publik'],
        ]);

        return view('admin.academic.calendar.create', compact(
            'academicYear',
            'academicYears',
            'categories',
            'sources',
            'statusDays',
            'targets',
        ));
    }

    public function store(StoreAcademicCalendarEventRequest $request)
    {
        $validated = $request->validated();

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

        if (!empty($validated['is_all_day'])) {
            $validated['start_time'] = null;
            $validated['end_time'] = null;
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
            ->route('admin.akademik.kalender.index')
            ->with('success', $message);
    }

    private function renderIndex(): View
    {
        $academicYear = '2026/2027';

        $academicYears = collect([
            ['value' => '2026/2027', 'label' => '2026/2027'],
            ['value' => '2025/2026', 'label' => '2025/2026'],
            ['value' => '2024/2025', 'label' => '2024/2025'],
        ]);

        $categoryMap = [
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
            'tka-anas' => [
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
        ];

        // Data contoh prototipe, bukan data resmi
        $rawEvents = [
            [
                'title' => 'Awal Masuk Sekolah TP 2026/2027',
                'category' => 'awal-masuk',
                'start_date' => '2026-07-13',
                'end_date' => '2026-07-15',
                'source' => 'sekolah',
                'is_holiday' => false,
                'is_effective_day' => false,
                'description' => 'Awal tahun pelajaran baru dan MPLS',
            ],
            [
                'title' => 'Hari Kemerdekaan RI',
                'category' => 'libur-nasional',
                'start_date' => '2026-08-17',
                'end_date' => '2026-08-17',
                'source' => 'pemerintah',
                'is_holiday' => true,
                'is_effective_day' => false,
                'description' => 'Hari Ulang Tahun RI',
            ],
            [
                'title' => 'Asesmen Tengah Semester Ganjil',
                'category' => 'asesmen-ujian',
                'start_date' => '2026-09-28',
                'end_date' => '2026-10-02',
                'source' => 'sekolah',
                'is_holiday' => false,
                'is_effective_day' => true,
                'description' => 'Penilaian tengah semester ganjil',
            ],
            [
                'title' => 'TKA / Asesmen Nasional',
                'category' => 'tka-anas',
                'start_date' => '2026-10-26',
                'end_date' => '2026-10-30',
                'source' => 'pemerintah',
                'is_holiday' => false,
                'is_effective_day' => true,
                'description' => 'TKA dan Asesmen Nasional',
            ],
            [
                'title' => 'Penyerahan Rapor Semester Ganjil',
                'category' => 'penyerahan-rapor',
                'start_date' => '2026-12-18',
                'end_date' => '2026-12-18',
                'source' => 'sekolah',
                'is_holiday' => false,
                'is_effective_day' => false,
                'description' => 'Pembagian rapor semester ganjil',
            ],
            [
                'title' => 'Libur Semester Ganjil',
                'category' => 'libur-semester',
                'start_date' => '2026-12-21',
                'end_date' => '2027-01-02',
                'source' => 'sekolah',
                'is_holiday' => true,
                'is_effective_day' => false,
                'description' => 'Libur setelah semester ganjil',
            ],
            [
                'title' => 'Awal Masuk Semester Genap',
                'category' => 'awal-masuk',
                'start_date' => '2027-01-04',
                'end_date' => '2027-01-06',
                'source' => 'sekolah',
                'is_holiday' => false,
                'is_effective_day' => false,
                'description' => 'Awal semester genap',
            ],
            [
                'title' => 'Kegiatan Pesantren Kilat',
                'category' => 'kegiatan-pesantren',
                'start_date' => '2027-02-15',
                'end_date' => '2027-02-17',
                'source' => 'sekolah',
                'is_holiday' => false,
                'is_effective_day' => false,
                'description' => 'Kegiatan pesantren kilat Ramadan',
            ],
            [
                'title' => 'Libur Awal Ramadan',
                'category' => 'libur-ramadan',
                'start_date' => '2027-03-01',
                'end_date' => '2027-03-01',
                'source' => 'pemerintah',
                'is_holiday' => true,
                'is_effective_day' => false,
                'description' => 'Libur awal bulan Ramadan',
            ],
            [
                'title' => 'Libur Hari Raya Idulfitri',
                'category' => 'libur-ramadan',
                'start_date' => '2027-03-29',
                'end_date' => '2027-04-07',
                'source' => 'pemerintah',
                'is_holiday' => true,
                'is_effective_day' => false,
                'description' => 'Libur Idulfitri',
            ],
            [
                'title' => 'Workshop Pengembangan Kurikulum',
                'category' => 'lainnya',
                'start_date' => '2027-05-10',
                'end_date' => '2027-05-12',
                'source' => 'sekolah',
                'is_holiday' => false,
                'is_effective_day' => false,
                'description' => 'Workshop guru',
            ],
            [
                'title' => 'Class Meeting',
                'category' => 'kegiatan-sekolah',
                'start_date' => '2027-05-17',
                'end_date' => '2027-05-21',
                'source' => 'sekolah',
                'is_holiday' => false,
                'is_effective_day' => false,
                'description' => 'Kegiatan class meeting akhir semester',
            ],
            [
                'title' => 'Asesmen Akhir Tahun',
                'category' => 'asesmen-ujian',
                'start_date' => '2027-05-24',
                'end_date' => '2027-05-28',
                'source' => 'sekolah',
                'is_holiday' => false,
                'is_effective_day' => true,
                'description' => 'Penilaian akhir tahun',
            ],
            [
                'title' => 'Penyerahan Rapor Semester Genap',
                'category' => 'penyerahan-rapor',
                'start_date' => '2027-06-14',
                'end_date' => '2027-06-14',
                'source' => 'sekolah',
                'is_holiday' => false,
                'is_effective_day' => false,
                'description' => 'Pembagian rapor semester genap',
            ],
            [
                'title' => 'Libur Akhir Tahun Pelajaran',
                'category' => 'libur-semester',
                'start_date' => '2027-06-16',
                'end_date' => '2027-06-30',
                'source' => 'sekolah',
                'is_holiday' => true,
                'is_effective_day' => false,
                'description' => 'Libur akhir tahun pelajaran',
            ],
        ];

        // --- EXPAND DATE RANGES ---
        $expandedEvents = collect();
        $holidayDates = collect();
        $assessmentDates = collect();

        foreach ($rawEvents as $evt) {
            $start = \Carbon\Carbon::parse($evt['start_date']);
            $end = \Carbon\Carbon::parse($evt['end_date']);
            $cat = $categoryMap[$evt['category']];

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
                if ($evt['is_effective_day']) {
                    $assessmentDates->push($dateStr);
                }
            }
        }

        $uniqueHolidayCount = $holidayDates->unique()->count();
        $uniqueAssessmentCount = $assessmentDates->unique()->count();

        // --- UPCOMING EVENTS ---
        $today = now()->format('Y-m-d');
        $upcomingEvents = collect($rawEvents)
            ->filter(fn($e) => $e['end_date'] >= $today)
            ->sortBy('start_date')
            ->take(4)
            ->map(function ($e) use ($categoryMap) {
                $cat = $categoryMap[$e['category']];
                return [
                    'date' => $e['start_date'],
                    'title' => $e['title'],
                    'category_label' => $cat['label'],
                    'dot_class' => $cat['dot_class'],
                    'bg_class' => $cat['bg_class'],
                ];
            })
            ->values();

        // --- CATEGORIES WITH COUNTS ---
        $categories = collect($categoryMap)->map(function ($c, $key) use ($expandedEvents) {
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

        // --- SUMMARY ---
        $summary = [
            'hari_efektif' => 220,
            'hari_libur' => $uniqueHolidayCount,
            'asesmen' => $uniqueAssessmentCount,
            'kegiatan_terdekat' => [
                'title' => $rawEvents[0]['title'],
                'date' => \Carbon\Carbon::parse($rawEvents[0]['start_date'])->translatedFormat('j F Y'),
                'end_date' => \Carbon\Carbon::parse($rawEvents[0]['end_date'])->translatedFormat('j F Y'),
                'days_left' => now()->diffInDays(\Carbon\Carbon::parse($rawEvents[0]['start_date']), false),
                'category' => $rawEvents[0]['category'],
            ],
        ];

        $monthlyEvents = $expandedEvents->groupBy(fn($e) => substr($e['date'], 0, 7));

        $events = $expandedEvents;

        return view('admin.academic.calendar.index', compact(
            'academicYear',
            'academicYears',
            'events',
            'summary',
            'categories',
            'upcomingEvents',
            'monthlyEvents',
            'rawEvents',
            'categoryMap',
        ));
    }
}
