<?php

use App\Http\Controllers\Admin\GalleryCategoryController;
use App\Http\Controllers\Admin\NavigationMenuController;
use App\Http\Controllers\Admin\OrganizationStructureController;
use App\Http\Controllers\Admin\PPDBApplicationController;
use App\Http\Controllers\Admin\SchoolImageController;
use App\Http\Controllers\Admin\FosterStudentController;
use App\Http\Controllers\Admin\DonationItemCommitmentController;
use App\Http\Controllers\Admin\DonationItemReceiptController;
use App\Http\Controllers\Admin\DonationTransactionController;
use App\Http\Controllers\Admin\DonationOutflowController;
use App\Http\Controllers\Admin\DonationTransferController;
use App\Http\Controllers\Admin\DonationDashboardController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\LetterIncomingController;
use App\Http\Controllers\Admin\LetterSettingController;
use App\Http\Controllers\Admin\LetterOutgoingController;
use App\Http\Controllers\Admin\LetterSignerController;
use App\Http\Controllers\Admin\LetterTemplateController;
use App\Http\Controllers\Admin\LetterTypeController;
use App\Http\Controllers\Admin\SarprasController;
use App\Http\Controllers\Admin\Website\DonationEducationSettingController;
use App\Http\Controllers\Admin\WebsitePageController;
use App\Http\Controllers\Admin\WebsiteSettingController;
use App\Http\Controllers\Admin\BoardingContentController;
use App\Http\Controllers\FosterParentController;
use App\Http\Controllers\PPDBController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\PublicLessonScheduleController;
use App\Http\Controllers\PublicProgressController;
use App\Http\Controllers\WaqfController;
use App\Http\Controllers\Admin\MenuAccessController;
use App\Http\Controllers\Admin\WaqfSettingController;
use App\Http\Controllers\Admin\WaqfTransactionController;
use App\Models\AdmissionYear;
use App\Models\AcademicCalendarEvent;
use App\Models\NavigationMenu;
use App\Models\SchoolImage;
use App\Models\SchoolSetting;
use App\Models\StudentApplication;
use App\Models\WebsitePage;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $schoolSetting = null;
    $heroImages = collect();
    try {
        $schoolSetting = SchoolSetting::current();
        $heroImages = SchoolImage::where('is_active', true)
            ->whereHas('categories', fn($q) => $q->where('slug', 'hero'))
            ->orderBy('sort_order')
            ->latest()
            ->get();
        $currentAdmissionYear = AdmissionYear::where('is_current', true)->first();
        $currentAdmissionProgram = $currentAdmissionYear?->programs()->first();
        $admissionStats = null;
        $homePage = null;

        if ($currentAdmissionYear) {
            $totalApplicants = StudentApplication::where('admission_year_id', $currentAdmissionYear->id)->count();
            $totalAccepted = StudentApplication::where('admission_year_id', $currentAdmissionYear->id)
                ->where('status', 'diterima')
                ->count();
            $remainingQuota = max(0, $currentAdmissionYear->quota - $totalAccepted);
            $admissionStats = compact('totalApplicants', 'totalAccepted', 'remainingQuota');
        }

        $homePage = WebsitePage::key('home');

        $schoolValues = \App\Models\SchoolValue::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $buildingImages = \App\Models\SchoolImage::where('is_active', true)
            ->whereHas('categories', fn($q) => $q->where('slug', 'fasilitas'))
            ->orderBy('sort_order')
            ->get();
        $agendaEvents = collect();
        try {
            $indonesianMonths = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            ];

            $calendarCategoryMap = collect([
                'awal-masuk' => ['label' => 'Awal Masuk Sekolah', 'dot_class' => 'bg-green-500', 'bg_class' => 'bg-green-100', 'text_class' => 'text-green-800'],
                'libur-nasional' => ['label' => 'Libur Nasional / Cuti Bersama', 'dot_class' => 'bg-red-500', 'bg_class' => 'bg-red-100', 'text_class' => 'text-red-800'],
                'penyerahan-rapor' => ['label' => 'Penyerahan Rapor', 'dot_class' => 'bg-orange-500', 'bg_class' => 'bg-orange-100', 'text_class' => 'text-orange-800'],
                'libur-ramadan' => ['label' => 'Libur Ramadan / Idulfitri', 'dot_class' => 'bg-amber-500', 'bg_class' => 'bg-amber-100', 'text_class' => 'text-amber-800'],
                'asesmen-ujian' => ['label' => 'Asesmen / Ujian', 'dot_class' => 'bg-blue-500', 'bg_class' => 'bg-blue-100', 'text_class' => 'text-blue-800'],
                'libur-semester' => ['label' => 'Libur Semester', 'dot_class' => 'bg-yellow-500', 'bg_class' => 'bg-yellow-100', 'text_class' => 'text-yellow-800'],
                'tka-asesmen-nasional' => ['label' => 'TKA / Asesmen Nasional', 'dot_class' => 'bg-purple-500', 'bg_class' => 'bg-purple-100', 'text_class' => 'text-purple-800'],
                'kegiatan-sekolah' => ['label' => 'Kegiatan Sekolah', 'dot_class' => 'bg-violet-500', 'bg_class' => 'bg-violet-100', 'text_class' => 'text-violet-800'],
                'kegiatan-pesantren' => ['label' => 'Kegiatan Pesantren', 'dot_class' => 'bg-teal-500', 'bg_class' => 'bg-teal-100', 'text_class' => 'text-teal-800'],
                'lainnya' => ['label' => 'Lainnya', 'dot_class' => 'bg-slate-500', 'bg_class' => 'bg-slate-100', 'text_class' => 'text-slate-800'],
            ]);

            $formatDateRange = function ($startDate, $endDate, $months) {
                $start = \Carbon\Carbon::parse($startDate);
                $end = \Carbon\Carbon::parse($endDate);

                if ($start->toDateString() === $end->toDateString()) {
                    return $start->format('j') . ' ' . $months[$start->month] . ' ' . $start->format('Y');
                }

                if ($start->month === $end->month && $start->year === $end->year) {
                    return $start->format('j') . '–' . $end->format('j') . ' ' . $months[$start->month] . ' ' . $start->format('Y');
                }

                if ($start->year === $end->year) {
                    return $start->format('j') . ' ' . $months[$start->month] . '–' . $end->format('j') . ' ' . $months[$end->month] . ' ' . $start->format('Y');
                }

                return $start->format('j') . ' ' . $months[$start->month] . ' ' . $start->format('Y') . '–' . $end->format('j') . ' ' . $months[$end->month] . ' ' . $end->format('Y');
            };

            $rawEvents = AcademicCalendarEvent::where('status', 'published')
                ->where(function ($q) {
                    $q->whereJsonContains('targets', 'semua')
                      ->orWhereJsonContains('targets', 'publik');
                })
                ->where('end_date', '>=', now()->toDateString())
                ->whereNull('deleted_at')
                ->orderByRaw("CASE WHEN ? BETWEEN start_date AND end_date THEN 0 ELSE 1 END", [now()->toDateString()])
                ->orderBy('start_date')
                ->orderBy('start_time')
                ->orderBy('title')
                ->limit(4)
                ->get();

            $agendaEvents = $rawEvents->map(function ($evt) use ($calendarCategoryMap, $formatDateRange, $indonesianMonths) {
                $cat = $calendarCategoryMap->get($evt->category, $calendarCategoryMap->get('lainnya'));

                return [
                    'id' => $evt->id,
                    'title' => $evt->title,
                    'category' => $evt->category,
                    'category_label' => $cat['label'],
                    'dot_class' => $cat['dot_class'],
                    'bg_class' => $cat['bg_class'],
                    'text_class' => $cat['text_class'],
                    'start_date' => $evt->start_date->toDateString(),
                    'end_date' => $evt->end_date->toDateString(),
                    'date_formatted' => $formatDateRange($evt->start_date->toDateString(), $evt->end_date->toDateString(), $indonesianMonths),
                    'start_time' => $evt->start_time ? \Carbon\Carbon::parse($evt->start_time)->format('H.i') : null,
                    'end_time' => $evt->end_time ? \Carbon\Carbon::parse($evt->end_time)->format('H.i') : null,
                    'description' => $evt->description,
                    'location' => $evt->location,
                ];
            });
        } catch (\Exception $e) {
            $agendaEvents = collect();
        }
    } catch (\Exception $e) {
        $schoolSetting = null;
        $currentAdmissionYear = null;
        $currentAdmissionProgram = null;
        $admissionStats = null;
        $homePage = null;
        $schoolValues = collect();
        $buildingImages = collect();
        $agendaEvents = collect();
    }

    return view('pages.welcome', compact(
        'schoolSetting', 'heroImages', 'currentAdmissionYear', 'currentAdmissionProgram', 'admissionStats', 'homePage', 'schoolValues', 'buildingImages', 'agendaEvents'
    ));
})->middleware('track.visitor');

use App\Models\VisitorLog;

Route::get('/dashboard', function () {
    $currentYear = \App\Models\AdmissionYear::where('is_current', true)->first();
    $counts = \App\Models\StudentApplication::when($currentYear, fn($q) => $q->where('admission_year_id', $currentYear->id))
        ->selectRaw("status, count(*) as total")
        ->groupBy('status')
        ->pluck('total', 'status');
    $quota = $currentYear?->quota ?? 0;
    $terisi = $counts->get('diterima', 0);
    $sisa = max(0, $quota - $terisi);
    $total = array_sum($counts->toArray()) ?: 0;
    $menunggu = $counts->get('menunggu_verifikasi', 0) + $counts->get('baru_daftar', 0);

    $now = now();
    $visitorToday = VisitorLog::whereDate('visited_at', $today = $now->toDateString())->count();
    $visitorTodayUnique = VisitorLog::whereDate('visited_at', $today)->distinct('ip_hash')->count('ip_hash');
    $visitor7Days = VisitorLog::where('visited_at', '>=', $now->copy()->subDays(7))->count();
    $visitor7DaysUnique = VisitorLog::where('visited_at', '>=', $now->copy()->subDays(7))->distinct('ip_hash')->count('ip_hash');
    $visitor30Days = VisitorLog::where('visited_at', '>=', $now->copy()->subDays(30))->count();
    $visitor30DaysUnique = VisitorLog::where('visited_at', '>=', $now->copy()->subDays(30))->distinct('ip_hash')->count('ip_hash');
    $totalVisits = VisitorLog::count();
    $spmbVisits = VisitorLog::where(function ($q) {
        $q->where('path', 'like', '%/spmb%')
          ->orWhere('path', 'like', '%/ppdb%');
    })->count();

    $donasiVisits = VisitorLog::where('path', '/donasi-pendidikan')->count();

    $topPages = VisitorLog::selectRaw('path, url, count(*) as total, max(visited_at) as last_visited')
        ->where('path', 'NOT LIKE', '/progress%')
        ->where('path', 'NOT LIKE', '%dashboard%')
        ->where('path', 'NOT LIKE', '/admin%')
        ->groupBy('path', 'url')
        ->orderByDesc('total')
        ->take(10)
        ->get();

    $topReferrers = VisitorLog::selectRaw('referrer, count(*) as total')
        ->where(function ($q) {
            $q->whereNull('referrer')
              ->orWhere('referrer', '')
              ->orWhere(function ($q2) {
                  $q2->whereNotNull('referrer')
                      ->where('referrer', '!=', '')
                      ->where('referrer', 'NOT LIKE', '%/progress/%')
                      ->where('referrer', 'NOT LIKE', '%/dashboard%')
                      ->where('referrer', 'NOT LIKE', '%/admin%');
              });
        })
        ->groupBy('referrer')
        ->orderByDesc('total')
        ->take(5)
        ->get()
        ->map(function ($item) {
            if (empty($item->referrer)) {
                $item->referrer = 'Langsung / WhatsApp';
            }
            return $item;
        });

    $deviceStats = VisitorLog::selectRaw("device, count(*) as total")
        ->whereNotNull('device')
        ->groupBy('device')
        ->orderByDesc('total')
        ->get();

    return view('dashboard', compact(
        'currentYear', 'counts', 'quota', 'terisi', 'sisa', 'total', 'menunggu',
        'visitorToday', 'visitorTodayUnique', 'visitor7Days', 'visitor7DaysUnique',
        'visitor30Days', 'visitor30DaysUnique', 'totalVisits', 'spmbVisits',
        'donasiVisits',
        'topPages', 'topReferrers', 'deviceStats',
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware('track.visitor')->group(function () {
    Route::get('/profil', [PublicPageController::class, 'profile'])->name('public.profile');
    Route::get('/program', [PublicPageController::class, 'program'])->name('public.program');
    Route::get('/boarding-school', [PublicPageController::class, 'boarding'])->name('public.boarding');
    Route::get('/galeri', [PublicPageController::class, 'gallery'])->name('public.gallery');
    Route::get('/tokoh-pembina', [PublicPageController::class, 'figures'])->name('public.figures');
    Route::get('/faq', [PublicPageController::class, 'faq'])->name('public.faq');
    Route::get('/guru', [PublicPageController::class, 'teachers'])->name('public.teachers');
    Route::get('/kalender-pendidikan', [PublicPageController::class, 'academicCalendar'])->name('public.academic-calendar');
    Route::get('/jadwal-pelajaran', [PublicLessonScheduleController::class, 'index'])->name('public.lesson-schedule');
    Route::get('/struktur-organisasi', function () {
        return redirect()->route('public.teachers', ['tab' => 'struktur'], 301);
    })->name('public.struktur-organisasi');
    Route::get('/infaq-uang', [PublicPageController::class, 'infaqUang'])->name('public.infaq-money');
    Route::get('/infaq-barang', [PublicPageController::class, 'infaqBarang'])->name('public.infaq-goods');
    Route::get('/donasi-pendidikan', [PublicPageController::class, 'donasiPendidikan'])->name('donasi-pendidikan');
    Route::get('/donasi-pendidikan/form-donatur', [PublicPageController::class, 'formDonatur'])->name('donasi-pendidikan.form-donatur');
    Route::post('/donasi-pendidikan/form-donatur', [PublicPageController::class, 'submitDonatur'])
        ->middleware('throttle:5,10')
        ->name('donasi-pendidikan.form-donatur.submit');
    Route::get('/donasi-pendidikan/pembayaran/{order_id}', [PublicPageController::class, 'payment'])->name('donasi-pendidikan.payment');
    Route::post('/donasi-pendidikan/qris/preview', [PublicPageController::class, 'previewQrisInline'])->name('donasi-pendidikan.qris.preview');
    Route::get('/donasi-pendidikan/qris', [PublicPageController::class, 'qris'])->name('donasi-pendidikan.qris');
    Route::get('/donasi-pendidikan/qris/download', [PublicPageController::class, 'downloadQris'])->name('donasi-pendidikan.qris.download');
    Route::get('/donasi-pendidikan/qris/download-hero', [PublicPageController::class, 'downloadQrisHero'])->name('donasi-pendidikan.qris.download-hero');
    Route::get('/donasi-pendidikan/sebarkan', [PublicPageController::class, 'sebarkan'])->name('donasi-pendidikan.sebarkan');
    Route::post('/donasi-pendidikan/sebarkan', [PublicPageController::class, 'submitSebarkan'])->name('donasi-pendidikan.sebarkan.submit');

    Route::get('/orang-tua-asuh', [FosterParentController::class, 'index'])->name('orang-tua-asuh');
    Route::post('/orang-tua-asuh/submit', [FosterParentController::class, 'submit'])
        ->middleware('throttle:5,10')
        ->name('orang-tua-asuh.submit');
    Route::get('/guru/edit/{token}', [\App\Http\Controllers\PublicTeacherProfileController::class, 'edit'])->name('public.teachers.edit-token');
    Route::put('/guru/edit/{token}', [\App\Http\Controllers\PublicTeacherProfileController::class, 'update'])->name('public.teachers.update-token');

    Route::get('/ppdb', [\App\Http\Controllers\PPDBController::class, 'info'])->name('ppdb.info');

    Route::name('ppdb.')->prefix('ppdb')->group(function () {
        Route::get('/daftar', [\App\Http\Controllers\PPDBController::class, 'create'])->name('create');
        Route::post('/daftar', [\App\Http\Controllers\PPDBController::class, 'store'])->name('store');
        Route::get('/sukses/{studentApplication}', [\App\Http\Controllers\PPDBController::class, 'success'])->name('success');
        Route::get('/cek-status', [\App\Http\Controllers\PPDBController::class, 'statusForm'])->name('status.form');
        Route::post('/cek-status', [\App\Http\Controllers\PPDBController::class, 'statusCheck'])->name('status.check');
    });

    Route::post('/midtrans/donation/notification', [PublicPageController::class, 'midtransNotification'])->withoutMiddleware([\App\Http\Middleware\TrackVisitorMiddleware::class])->name('midtrans.donation.notification');

    Route::post('/ai-chat/send', [\App\Http\Controllers\Public\AiChatController::class, 'send'])
        ->middleware('throttle:10,1')
        ->name('ai-chat.send');

    Route::get('/spmb', [\App\Http\Controllers\PPDBController::class, 'info'])->name('spmb.info');

    Route::name('spmb.')->prefix('spmb')->group(function () {
        Route::get('/daftar', [\App\Http\Controllers\PPDBController::class, 'create'])->name('create');
        Route::post('/daftar', [\App\Http\Controllers\PPDBController::class, 'store'])->name('store');
        Route::get('/sukses/{studentApplication}', [\App\Http\Controllers\PPDBController::class, 'success'])->name('success');
        Route::get('/cek-status', [\App\Http\Controllers\PPDBController::class, 'statusForm'])->name('status.form');
        Route::post('/cek-status', [\App\Http\Controllers\PPDBController::class, 'statusCheck'])->name('status.check');
        Route::get('/perbarui-data/{token}', [\App\Http\Controllers\PPDBController::class, 'editData'])->name('update-data');
        Route::post('/perbarui-data/{token}', [\App\Http\Controllers\PPDBController::class, 'updateData'])->name('update-data.store');
        Route::post('/perbarui-data/{token}/step/{step}', [\App\Http\Controllers\PPDBController::class, 'saveUpdateDataStep'])
            ->whereNumber('step')
            ->name('update-data.step');
        Route::post('/perbarui-data/{token}/final-submit', [\App\Http\Controllers\PPDBController::class, 'finalSubmitUpdateData'])
            ->name('update-data.final-submit');
    });
});

Route::middleware('track.visitor')->group(function () {
    Route::get('/wakaf-uang', [WaqfController::class, 'index'])->name('wakaf-uang.index');
    Route::get('/wakaf-uang/form', [WaqfController::class, 'form'])->name('wakaf-uang.form');
    Route::post('/wakaf-uang/qris/preview', [WaqfController::class, 'previewQris'])->name('wakaf-uang.qris.preview');
    Route::post('/wakaf-uang', [WaqfController::class, 'submit'])
        ->middleware('throttle:5,10')
        ->name('wakaf-uang.submit');
    Route::get('/wakaf-uang/qris/download', [WaqfController::class, 'downloadQris'])->name('wakaf-uang.qris.download');
});

Route::middleware('auth')->name('admin.')->prefix('admin')->group(function () {
    Route::get('/ppdb', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'dashboard'])
        ->middleware('permission:ppdb.dashboard.view,superadmin,admin,staf_tata_usaha,staf_kesiswaan,kepala_sekolah')
        ->name('ppdb.dashboard');

    Route::name('ppdb.applications.')->prefix('ppdb/pendaftar')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'index'])
                ->middleware('permission:ppdb.applications.view,superadmin,admin,staf_tata_usaha,staf_kesiswaan,kepala_sekolah')
                ->name('index');
            Route::get('/export', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'export'])
                ->middleware('permission:ppdb.applications.export,superadmin,admin,staf_tata_usaha,staf_kesiswaan')
                ->name('export');
            Route::get('/export-pdf', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'exportPdf'])
                ->middleware('permission:ppdb.applications.export,superadmin,admin,staf_tata_usaha,staf_kesiswaan')
                ->name('export-pdf');
            Route::get('/{studentApplication}', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'show'])
                ->middleware('permission:ppdb.applications.view,superadmin,admin,staf_tata_usaha,staf_kesiswaan,kepala_sekolah')
                ->name('show');
            Route::get('/{studentApplication}/print', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'print'])
                ->middleware('permission:ppdb.applications.export,superadmin,admin,staf_tata_usaha,staf_kesiswaan')
                ->name('print');
            Route::get('/{studentApplication}/requirements/download', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'downloadRequirements'])
                ->middleware('permission:ppdb.applications.download,superadmin,admin,staf_tata_usaha,staf_kesiswaan')
                ->name('requirements.download');
            Route::patch('/{studentApplication}/status', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'updateStatus'])
                ->middleware('permission:ppdb.applications.manage,superadmin,admin,staf_tata_usaha,staf_kesiswaan')
                ->name('update-status');
            Route::patch('/{studentApplication}/follow-up', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'updateFollowUp'])
                ->middleware('permission:ppdb.applications.manage,superadmin,admin,staf_tata_usaha,staf_kesiswaan')
                ->name('update-follow-up');
            Route::patch('/{studentApplication}/mark-data-complete', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'markDataComplete'])
                ->middleware('permission:ppdb.applications.manage,superadmin,admin,staf_tata_usaha,staf_kesiswaan')
                ->name('mark-data-complete');
            Route::post('/{studentApplication}/generate-update-link', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'generateUpdateLink'])
                ->middleware('permission:ppdb.applications.manage,superadmin,admin,staf_tata_usaha,staf_kesiswaan')
                ->name('generate-update-link');
            Route::delete('/{studentApplication}', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'destroy'])
                ->middleware('permission:ppdb.applications.delete,superadmin,admin')
                ->name('destroy');
        });

    Route::name('ppdb.settings.')->prefix('ppdb/pengaturan')
        ->middleware('permission:ppdb.settings.manage,superadmin,admin')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'settingsEdit'])->name('edit');
            Route::put('/', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'settingsUpdate'])->name('update');
        });

    Route::name('website.settings.')->prefix('website/pengaturan')
        ->middleware('role:superadmin,admin,staf_tata_usaha')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\WebsiteSettingController::class, 'edit'])->name('edit');
            Route::put('/', [\App\Http\Controllers\Admin\WebsiteSettingController::class, 'update'])->name('update');
        });

    Route::name('website.settings.')->prefix('website/pengaturan')
        ->middleware('role:superadmin,admin')
        ->group(function () {
            Route::post('/generate-token', [\App\Http\Controllers\Admin\WebsiteSettingController::class, 'generateToken'])->name('public-dashboard-token.generate');
        });

    Route::name('website.boarding.')->prefix('website/boarding')
        ->middleware('role:superadmin,admin,staf_tata_usaha')
        ->group(function () {
            Route::get('/', [BoardingContentController::class, 'index'])->name('index');
            Route::put('/settings', [BoardingContentController::class, 'updateSettings'])->name('settings.update');
            Route::post('/cards', [BoardingContentController::class, 'storeCard'])->name('cards.store');
            Route::put('/cards/{boardingCard}', [BoardingContentController::class, 'updateCard'])->name('cards.update');
            Route::delete('/cards/{boardingCard}', [BoardingContentController::class, 'destroyCard'])->name('cards.destroy');
            Route::post('/schedules', [BoardingContentController::class, 'storeSchedule'])->name('schedules.store');
            Route::put('/schedules/{boardingSchedule}', [BoardingContentController::class, 'updateSchedule'])->name('schedules.update');
            Route::delete('/schedules/{boardingSchedule}', [BoardingContentController::class, 'destroySchedule'])->name('schedules.destroy');
        });

    Route::get('/website/donasi-pendidikan', [DonationEducationSettingController::class, 'edit'])
        ->middleware('role:superadmin,admin,staf_keuangan')
        ->name('website.donasi-pendidikan.edit');
    Route::put('/website/donasi-pendidikan', [DonationEducationSettingController::class, 'update'])
        ->middleware('role:superadmin,admin,staf_keuangan')
        ->name('website.donasi-pendidikan.update');

    Route::name('donasi-transactions.')->prefix('donasi-transactions')
        ->middleware('role:superadmin,admin,staf_keuangan')
        ->group(function () {
            Route::get('/buat-bukti-penerimaan', [DonationTransactionController::class, 'createReceipt'])->name('create-receipt');
            Route::post('/buat-bukti-penerimaan/parse', [DonationTransactionController::class, 'parseReceipt'])->name('parse-receipt');
            Route::post('/buat-bukti-penerimaan', [DonationTransactionController::class, 'storeReceipt'])->name('store-receipt');
            Route::patch('/{transaction}/mark-paid', [DonationTransactionController::class, 'markPaid'])->name('mark-paid');
            Route::patch('/{transaction}/mark-cancelled', [DonationTransactionController::class, 'markCancelled'])->name('mark-cancelled');
            Route::patch('/{transaction}/payment-method', [DonationTransactionController::class, 'updatePaymentMethod'])
                ->middleware('superadmin')
                ->name('payment-method');
            Route::get('/{transaction}/edit', [DonationTransactionController::class, 'edit'])
                ->middleware('superadmin')
                ->name('edit');
            Route::put('/{transaction}', [DonationTransactionController::class, 'update'])
                ->middleware('superadmin')
                ->name('update');
        });

    Route::name('donation-outflows.')->prefix('donation-outflows')->group(function () {
        Route::get('/', [DonationOutflowController::class, 'index'])
            ->middleware('permission:donation.outflows.view,superadmin,admin,staf_keuangan')
            ->name('index');
        Route::get('/create', [DonationOutflowController::class, 'create'])
            ->middleware('permission:donation.outflows.create,superadmin,admin')
            ->name('create');
        Route::post('/', [DonationOutflowController::class, 'store'])
            ->middleware('permission:donation.outflows.create,superadmin,admin')
            ->name('store');
        Route::get('/{donationOutflow}', [DonationOutflowController::class, 'show'])
            ->middleware('permission:donation.outflows.view,superadmin,admin,staf_keuangan')
            ->name('show');
        Route::get('/{donationOutflow}/edit', [DonationOutflowController::class, 'edit'])
            ->middleware('role:superadmin,admin')
            ->name('edit');
        Route::put('/{donationOutflow}', [DonationOutflowController::class, 'update'])
            ->middleware('role:superadmin,admin')
            ->name('update');
        Route::post('/{donationOutflow}/approve', [DonationOutflowController::class, 'approve'])
            ->middleware('permission:donation.outflows.approve,superadmin,staf_keuangan')
            ->name('approve');
        Route::post('/{donationOutflow}/reject', [DonationOutflowController::class, 'reject'])
            ->middleware('permission:donation.outflows.reject,superadmin,staf_keuangan')
            ->name('reject');
    });

    Route::get('/donasi/dashboard', [DonationDashboardController::class, 'dashboard'])
        ->middleware('permission:donation.balance.view,superadmin,admin,staf_keuangan')
        ->name('donation.dashboard');

    Route::name('donation-transfers.')->prefix('donation-transfers')->group(function () {
        Route::get('/', [DonationTransferController::class, 'index'])
            ->middleware('permission:donation.transfers.view,superadmin,admin,staf_keuangan')
            ->name('index');
        Route::get('/create', [DonationTransferController::class, 'create'])
            ->middleware('permission:donation.transfers.create,superadmin,admin')
            ->name('create');
        Route::post('/', [DonationTransferController::class, 'store'])
            ->middleware('permission:donation.transfers.create,superadmin,admin')
            ->name('store');
        Route::get('/{donationTransfer}', [DonationTransferController::class, 'show'])
            ->middleware('permission:donation.transfers.view,superadmin,admin,staf_keuangan')
            ->name('show');
        Route::post('/{donationTransfer}/approve', [DonationTransferController::class, 'approve'])
            ->middleware('permission:donation.transfers.approve,superadmin,staf_keuangan')
            ->name('approve');
        Route::post('/{donationTransfer}/reject', [DonationTransferController::class, 'reject'])
            ->middleware('permission:donation.transfers.reject,superadmin,staf_keuangan')
            ->name('reject');
    });

    Route::name('donasi-transactions.')->prefix('donasi-transactions')
        ->middleware('role:superadmin,admin,staf_keuangan,kepala_sekolah')
        ->group(function () {
            Route::get('/', [DonationTransactionController::class, 'index'])->name('index');
            Route::get('/{transaction}', [DonationTransactionController::class, 'show'])->name('show');
        });

    Route::middleware('role:superadmin,admin,staf_keuangan')->group(function () {
        Route::delete('/donasi-transactions/{transaction}', [DonationTransactionController::class, 'destroy'])
            ->middleware('superadmin')
            ->name('donasi-transactions.destroy');
        Route::delete('/donation-outflows/{donationOutflow}', [DonationOutflowController::class, 'destroy'])
            ->middleware('superadmin')
            ->name('donation-outflows.destroy');
    });

    Route::middleware('role:superadmin,admin,staf_keuangan')->group(function () {
        Route::resource('infaq-barang', DonationItemReceiptController::class)
            ->only(['index', 'create', 'store', 'show'])
            ->parameters(['infaq-barang' => 'infaqBarang']);

        Route::get('/infaq-barang-wa', [DonationItemCommitmentController::class, 'index'])->name('infaq-barang-wa.index');
        Route::get('/infaq-barang-wa/create', [DonationItemCommitmentController::class, 'create'])->name('infaq-barang-wa.create');
        Route::post('/infaq-barang-wa/parse', [DonationItemCommitmentController::class, 'parse'])->name('infaq-barang-wa.parse');
        Route::post('/infaq-barang-wa', [DonationItemCommitmentController::class, 'store'])->name('infaq-barang-wa.store');
        Route::get('/infaq-barang-wa/{infaqBarangWa}', [DonationItemCommitmentController::class, 'show'])->name('infaq-barang-wa.show');
        Route::patch('/infaq-barang-wa/{infaqBarangWa}/status', [DonationItemCommitmentController::class, 'updateStatus'])->name('infaq-barang-wa.update-status');
    });

    Route::name('donasi-pendidikan.')->prefix('donasi-pendidikan')
        ->middleware('role:superadmin,admin,staf_keuangan')
        ->group(function () {
            Route::get('/share-template', [DonationEducationSettingController::class, 'shareTemplate'])->name('share-template');
            Route::put('/share-template', [DonationEducationSettingController::class, 'updateShareTemplate'])->name('share-template.update');
        });

    Route::name('wakaf.settings.')->prefix('wakaf/settings')
        ->middleware('role:superadmin,admin,staf_keuangan')
        ->group(function () {
            Route::get('/', [WaqfSettingController::class, 'edit'])->name('edit');
            Route::put('/', [WaqfSettingController::class, 'update'])->name('update');
        });

    Route::name('wakaf.transactions.')->prefix('wakaf/transactions')
        ->middleware('role:superadmin,admin,staf_keuangan')
        ->group(function () {
            Route::get('/buat-bukti-penerimaan', [WaqfTransactionController::class, 'createReceipt'])->name('create-receipt');
            Route::post('/buat-bukti-penerimaan/parse', [WaqfTransactionController::class, 'parseReceipt'])->name('parse-receipt');
            Route::post('/buat-bukti-penerimaan', [WaqfTransactionController::class, 'storeReceipt'])->name('store-receipt');
            Route::patch('/{transaction}/mark-paid', [WaqfTransactionController::class, 'markPaid'])->name('mark-paid');
            Route::patch('/{transaction}/mark-cancelled', [WaqfTransactionController::class, 'markCancelled'])->name('mark-cancelled');
        });

    Route::name('wakaf.transactions.')->prefix('wakaf/transactions')
        ->middleware('role:superadmin,admin,staf_keuangan,kepala_sekolah')
        ->group(function () {
            Route::get('/', [WaqfTransactionController::class, 'index'])->name('index');
            Route::get('/{transaction}', [WaqfTransactionController::class, 'show'])->name('show');
        });

    Route::name('finance.')->prefix('finance')
        ->middleware('role:superadmin,admin,staf_keuangan,kepala_sekolah')
        ->group(function () {
            Route::get('/', [FinanceController::class, 'dashboard'])->name('dashboard');
            Route::get('/laporan', [FinanceController::class, 'report'])->name('report');
        });

    Route::name('finance.')->prefix('finance')
        ->middleware('role:superadmin,admin,staf_keuangan')
        ->group(function () {
            Route::get('/pemasukan', [FinanceController::class, 'incomesIndex'])->name('incomes.index');
            Route::get('/pemasukan/create', [FinanceController::class, 'incomesCreate'])->name('incomes.create');
            Route::post('/pemasukan', [FinanceController::class, 'incomesStore'])->name('incomes.store');
            Route::get('/pemasukan/{financeIncome}/edit', [FinanceController::class, 'incomesEdit'])->name('incomes.edit');
            Route::put('/pemasukan/{financeIncome}', [FinanceController::class, 'incomesUpdate'])->name('incomes.update');
            Route::delete('/pemasukan/{financeIncome}', [FinanceController::class, 'incomesDestroy'])
                ->middleware('superadmin')
                ->name('incomes.destroy');

            Route::get('/pengeluaran', [FinanceController::class, 'expensesIndex'])->name('expenses.index');
            Route::get('/pengeluaran/create', [FinanceController::class, 'expensesCreate'])->name('expenses.create');
            Route::post('/pengeluaran', [FinanceController::class, 'expensesStore'])->name('expenses.store');
            Route::get('/pengeluaran/{financeExpense}/edit', [FinanceController::class, 'expensesEdit'])->name('expenses.edit');
            Route::put('/pengeluaran/{financeExpense}', [FinanceController::class, 'expensesUpdate'])->name('expenses.update');
        });

    Route::name('finance.')->prefix('finance')
        ->middleware('role:superadmin,admin')
        ->group(function () {
            Route::delete('/pengeluaran/{financeExpense}', [FinanceController::class, 'expensesDestroy'])
                ->middleware('superadmin')
                ->name('expenses.destroy');
        });

    Route::name('letters.outgoings.')->prefix('surat/keluar')
        ->middleware('role:superadmin,admin,staf_tata_usaha')
        ->group(function () {
            Route::get('/create', [LetterOutgoingController::class, 'create'])->name('create');
            Route::post('/', [LetterOutgoingController::class, 'store'])->name('store');
            Route::get('/{letterOutgoing}/edit', [LetterOutgoingController::class, 'edit'])->name('edit');
            Route::get('/{letterOutgoing}/print', [LetterOutgoingController::class, 'print'])->name('print');
            Route::get('/{letterOutgoing}/print-all', [LetterOutgoingController::class, 'printAll'])->name('print-all');
            Route::get('/{letterOutgoing}/print/{recipient}', [LetterOutgoingController::class, 'printRecipient'])->name('print-recipient');
            Route::put('/{letterOutgoing}', [LetterOutgoingController::class, 'update'])->name('update');
            Route::get('/{letterOutgoing}/issue', [LetterOutgoingController::class, 'issueGet'])->name('issue.get');
            Route::post('/{letterOutgoing}/issue', [LetterOutgoingController::class, 'issue'])->name('issue');
            Route::put('/{letterOutgoing}/attachment', [LetterOutgoingController::class, 'updateAttachment'])->name('update-attachment');
        });

    Route::name('letters.outgoings.')->prefix('surat/keluar')
        ->middleware('role:superadmin,admin,staf_tata_usaha,kepala_sekolah')
        ->group(function () {
            Route::get('/', [LetterOutgoingController::class, 'index'])->name('index');
            Route::get('/{letterOutgoing}', [LetterOutgoingController::class, 'show'])->name('show');
            Route::get('/{letterOutgoing}/preview', [LetterOutgoingController::class, 'preview'])->name('preview');
        });

    Route::name('letters.signers.')->prefix('surat/signers')
        ->middleware('role:superadmin,admin,staf_tata_usaha')
        ->group(function () {
            Route::get('/', [LetterSignerController::class, 'index'])->name('index');
            Route::post('/', [LetterSignerController::class, 'store'])->name('store');
            Route::get('/{letterSigner}/edit', [LetterSignerController::class, 'edit'])->name('edit');
            Route::put('/{letterSigner}', [LetterSignerController::class, 'update'])->name('update');
            Route::delete('/{letterSigner}', [LetterSignerController::class, 'destroy'])->name('destroy');
        });

    Route::name('letters.templates.')->prefix('surat/templates')
        ->middleware('role:superadmin,admin,staf_tata_usaha')
        ->group(function () {
            Route::get('/', [LetterTemplateController::class, 'index'])->name('index');
            Route::get('/create', [LetterTemplateController::class, 'create'])->name('create');
            Route::post('/', [LetterTemplateController::class, 'store'])->name('store');
            Route::get('/{letterTemplate}', [LetterTemplateController::class, 'show'])->name('show');
            Route::get('/{letterTemplate}/edit', [LetterTemplateController::class, 'edit'])->name('edit');
            Route::put('/{letterTemplate}', [LetterTemplateController::class, 'update'])->name('update');
            Route::delete('/{letterTemplate}', [LetterTemplateController::class, 'destroy'])->name('destroy');
        });

    Route::name('letters.types.')->prefix('surat/types')
        ->middleware('role:superadmin,admin,staf_tata_usaha')
        ->group(function () {
            Route::get('/', [LetterTypeController::class, 'index'])->name('index');
            Route::post('/', [LetterTypeController::class, 'store'])->name('store');
            Route::get('/{letterType}/edit', [LetterTypeController::class, 'edit'])->name('edit');
            Route::put('/{letterType}', [LetterTypeController::class, 'update'])->name('update');
            Route::delete('/{letterType}', [LetterTypeController::class, 'destroy'])->name('destroy');
        });

    Route::name('letters.settings.')->prefix('surat/settings')
        ->middleware('role:superadmin,admin,staf_tata_usaha')
        ->group(function () {
            Route::get('/', [LetterSettingController::class, 'edit'])->name('edit');
            Route::put('/', [LetterSettingController::class, 'update'])->name('update');
        });

    Route::name('letters.incomings.')->prefix('surat/masuk')
        ->middleware('role:superadmin,admin,staf_tata_usaha')
        ->group(function () {
            Route::get('/create', [LetterIncomingController::class, 'create'])->name('create');
            Route::post('/', [LetterIncomingController::class, 'store'])->name('store');
            Route::get('/{letterIncoming}/edit', [LetterIncomingController::class, 'edit'])->name('edit');
            Route::put('/{letterIncoming}', [LetterIncomingController::class, 'update'])->name('update');
            Route::delete('/{letterIncoming}', [LetterIncomingController::class, 'destroy'])->name('destroy');
        });

    Route::name('letters.incomings.')->prefix('surat/masuk')
        ->middleware('role:superadmin,admin,staf_tata_usaha,kepala_sekolah')
        ->group(function () {
            Route::get('/', [LetterIncomingController::class, 'index'])->name('index');
            Route::get('/{letterIncoming}', [LetterIncomingController::class, 'show'])->name('show');
        });

    Route::name('sarpras.')->prefix('sarpras')
        ->middleware('role:superadmin,admin,staf_sarpras,kepala_sekolah')
        ->group(function () {
            Route::get('/', [SarprasController::class, 'dashboard'])->name('dashboard');
            Route::get('/laporan', [SarprasController::class, 'laporan'])->name('laporan');
        });

    Route::name('sarpras.')->prefix('sarpras')
        ->middleware('role:superadmin,admin,staf_sarpras')
        ->group(function () {
            Route::get('/aset', [SarprasController::class, 'assetsIndex'])->name('assets.index');
            Route::get('/aset/create', [SarprasController::class, 'assetsCreate'])->name('assets.create');
            Route::post('/aset', [SarprasController::class, 'assetsStore'])->name('assets.store');
            Route::get('/aset/{asset}', [SarprasController::class, 'assetsShow'])->name('assets.show');
            Route::get('/aset/{asset}/edit', [SarprasController::class, 'assetsEdit'])->name('assets.edit');
            Route::put('/aset/{asset}', [SarprasController::class, 'assetsUpdate'])->name('assets.update');

            Route::get('/ruangan', [SarprasController::class, 'roomsIndex'])->name('rooms.index');
            Route::get('/ruangan/create', [SarprasController::class, 'roomsCreate'])->name('rooms.create');
            Route::post('/ruangan', [SarprasController::class, 'roomsStore'])->name('rooms.store');
            Route::get('/ruangan/{room}/edit', [SarprasController::class, 'roomsEdit'])->name('rooms.edit');
            Route::put('/ruangan/{room}', [SarprasController::class, 'roomsUpdate'])->name('rooms.update');

            Route::get('/kebutuhan', [SarprasController::class, 'needsIndex'])->name('needs.index');
            Route::get('/kebutuhan/create', [SarprasController::class, 'needsCreate'])->name('needs.create');
            Route::post('/kebutuhan', [SarprasController::class, 'needsStore'])->name('needs.store');
            Route::get('/kebutuhan/{need}/edit', [SarprasController::class, 'needsEdit'])->name('needs.edit');
            Route::put('/kebutuhan/{need}', [SarprasController::class, 'needsUpdate'])->name('needs.update');

            Route::get('/perbaikan', [SarprasController::class, 'maintenancesIndex'])->name('maintenances.index');
            Route::get('/perbaikan/create', [SarprasController::class, 'maintenancesCreate'])->name('maintenances.create');
            Route::post('/perbaikan', [SarprasController::class, 'maintenancesStore'])->name('maintenances.store');
            Route::get('/perbaikan/{maintenance}/edit', [SarprasController::class, 'maintenancesEdit'])->name('maintenances.edit');
            Route::put('/perbaikan/{maintenance}', [SarprasController::class, 'maintenancesUpdate'])->name('maintenances.update');

            Route::get('/pengadaan', [SarprasController::class, 'procurementsIndex'])->name('procurements.index');
            Route::get('/pengadaan/create', [SarprasController::class, 'procurementsCreate'])->name('procurements.create');
            Route::post('/pengadaan', [SarprasController::class, 'procurementsStore'])->name('procurements.store');
            Route::get('/pengadaan/{procurement}/edit', [SarprasController::class, 'procurementsEdit'])->name('procurements.edit');
            Route::put('/pengadaan/{procurement}', [SarprasController::class, 'procurementsUpdate'])->name('procurements.update');
        });

    Route::name('sarpras.')->prefix('sarpras')
        ->middleware('role:superadmin,admin')
        ->group(function () {
            Route::delete('/aset/{asset}', [SarprasController::class, 'assetsDestroy'])->name('assets.destroy');
            Route::delete('/ruangan/{room}', [SarprasController::class, 'roomsDestroy'])->name('rooms.destroy');
            Route::delete('/kebutuhan/{need}', [SarprasController::class, 'needsDestroy'])->name('needs.destroy');
            Route::delete('/perbaikan/{maintenance}', [SarprasController::class, 'maintenancesDestroy'])->name('maintenances.destroy');
            Route::delete('/pengadaan/{procurement}', [SarprasController::class, 'procurementsDestroy'])->name('procurements.destroy');
        });

    Route::name('orang-tua-asuh.')->prefix('orang-tua-asuh')
        ->middleware('role:superadmin,admin,staf_kesiswaan')
        ->group(function () {
            Route::get('/', [FosterStudentController::class, 'index'])->name('index');
            Route::get('/{submission}', [FosterStudentController::class, 'show'])->name('show');
            Route::patch('/{submission}/status', [FosterStudentController::class, 'updateStatus'])->name('update-status');
        });

    Route::name('orang-tua-asuh.')->prefix('orang-tua-asuh')
        ->middleware('role:superadmin,admin')
        ->group(function () {
            Route::delete('/{submission}', [FosterStudentController::class, 'destroy'])->name('destroy');
        });

    Route::name('website.media.')->prefix('website/media')
        ->middleware('permission:website.media.manage,superadmin,admin,staf_tata_usaha')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SchoolImageController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\SchoolImageController::class, 'mediaStore'])->name('store');
            Route::patch('/{schoolImage}/toggle', [\App\Http\Controllers\Admin\SchoolImageController::class, 'toggle'])->name('toggle');
            Route::delete('/{schoolImage}', [\App\Http\Controllers\Admin\SchoolImageController::class, 'destroy'])->name('destroy');
        });

    Route::name('website.hero-images.')->prefix('website/hero-images')
        ->middleware('role:superadmin,admin,staf_tata_usaha')
        ->group(function () {
            Route::post('/', [\App\Http\Controllers\Admin\SchoolImageController::class, 'store'])->name('store');
            Route::patch('/{schoolImage}/toggle', [\App\Http\Controllers\Admin\SchoolImageController::class, 'toggle'])->name('toggle');
            Route::delete('/{schoolImage}', [\App\Http\Controllers\Admin\SchoolImageController::class, 'destroy'])->name('destroy');
        });

    Route::name('website.gallery-images.')->prefix('website/gallery-images')
        ->middleware('role:superadmin,admin,staf_tata_usaha')
        ->group(function () {
            Route::post('/', [\App\Http\Controllers\Admin\SchoolImageController::class, 'galleryStore'])->name('store');
            Route::patch('/{schoolImage}/toggle', [\App\Http\Controllers\Admin\SchoolImageController::class, 'toggle'])->name('toggle');
            Route::delete('/{schoolImage}', [\App\Http\Controllers\Admin\SchoolImageController::class, 'destroy'])->name('destroy');
        });

    Route::name('website.categories.')->prefix('website/kategori-galeri')
        ->middleware('role:superadmin,admin,staf_tata_usaha')
        ->group(function () {
            Route::get('/', [GalleryCategoryController::class, 'index'])->name('index');
            Route::get('/create', [GalleryCategoryController::class, 'create'])->name('create');
            Route::post('/', [GalleryCategoryController::class, 'store'])->name('store');
            Route::get('/{galleryCategory}/edit', [GalleryCategoryController::class, 'edit'])->name('edit');
            Route::put('/{galleryCategory}', [GalleryCategoryController::class, 'update'])->name('update');
            Route::patch('/{galleryCategory}/toggle', [GalleryCategoryController::class, 'toggle'])->name('toggle');
            Route::delete('/{galleryCategory}', [GalleryCategoryController::class, 'destroy'])->name('destroy');
        });

    Route::name('website.figures.')->prefix('website/tokoh')
        ->middleware('role:superadmin,admin,staf_tata_usaha')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SchoolFigureController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\SchoolFigureController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\SchoolFigureController::class, 'store'])->name('store');
            Route::get('/{schoolFigure}/edit', [\App\Http\Controllers\Admin\SchoolFigureController::class, 'edit'])->name('edit');
            Route::put('/{schoolFigure}', [\App\Http\Controllers\Admin\SchoolFigureController::class, 'update'])->name('update');
            Route::patch('/{schoolFigure}/toggle', [\App\Http\Controllers\Admin\SchoolFigureController::class, 'toggle'])->name('toggle');
            Route::delete('/{schoolFigure}', [\App\Http\Controllers\Admin\SchoolFigureController::class, 'destroy'])->name('destroy');
        });

    Route::name('website.faq.')->prefix('website/faq')
        ->middleware('role:superadmin,admin,staf_tata_usaha')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\FaqController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\FaqController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\FaqController::class, 'store'])->name('store');
            Route::get('/{faq}/edit', [\App\Http\Controllers\Admin\FaqController::class, 'edit'])->name('edit');
            Route::put('/{faq}', [\App\Http\Controllers\Admin\FaqController::class, 'update'])->name('update');
            Route::patch('/{faq}/toggle', [\App\Http\Controllers\Admin\FaqController::class, 'toggle'])->name('toggle');
            Route::delete('/{faq}', [\App\Http\Controllers\Admin\FaqController::class, 'destroy'])->name('destroy');
        });

    Route::name('website.teachers.')->prefix('website/guru')
        ->middleware('permission:website.teachers.manage,superadmin,admin,staf_tata_usaha')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\TeacherController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\TeacherController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\TeacherController::class, 'store'])->name('store');
            Route::get('/{teacher}/edit', [\App\Http\Controllers\Admin\TeacherController::class, 'edit'])->name('edit');
            Route::put('/{teacher}', [\App\Http\Controllers\Admin\TeacherController::class, 'update'])->name('update');
            Route::patch('/{teacher}/toggle', [\App\Http\Controllers\Admin\TeacherController::class, 'toggle'])->name('toggle');
            Route::delete('/{teacher}', [\App\Http\Controllers\Admin\TeacherController::class, 'destroy'])->name('destroy');
            Route::post('/{teacher}/generate-token', [\App\Http\Controllers\Admin\TeacherController::class, 'generateToken'])->name('generate-token');
            Route::post('/{teacher}/reset-token', [\App\Http\Controllers\Admin\TeacherController::class, 'resetToken'])->name('reset-token');
        });

    Route::name('website.subjects.')->prefix('website/mata-pelajaran')
        ->middleware('permission:website.subjects.manage,superadmin,admin,staf_tata_usaha')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SchoolSubjectController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\SchoolSubjectController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\SchoolSubjectController::class, 'store'])->name('store');
            Route::get('/{schoolSubject}/edit', [\App\Http\Controllers\Admin\SchoolSubjectController::class, 'edit'])->name('edit');
            Route::put('/{schoolSubject}', [\App\Http\Controllers\Admin\SchoolSubjectController::class, 'update'])->name('update');
            Route::patch('/{schoolSubject}/toggle', [\App\Http\Controllers\Admin\SchoolSubjectController::class, 'toggle'])->name('toggle');
            Route::delete('/{schoolSubject}', [\App\Http\Controllers\Admin\SchoolSubjectController::class, 'destroy'])->name('destroy');
        });

    Route::name('akademik.kalender.')->prefix('akademik/kalender')
        ->middleware('permission:academic.calendar.view,superadmin,admin,staf_tata_usaha,staf_kesiswaan,kepala_sekolah,guru')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AcademicCalendarController::class, 'index'])->name('index');
        });

    Route::name('akademik.kalender.')->prefix('akademik/kalender')
        ->middleware('permission:academic.calendar.manage,superadmin,admin,staf_tata_usaha')
        ->group(function () {
            Route::get('/create', [\App\Http\Controllers\Admin\AcademicCalendarController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\AcademicCalendarController::class, 'store'])->name('store');
            Route::get('/{academicCalendarEvent}/edit', [\App\Http\Controllers\Admin\AcademicCalendarController::class, 'edit'])->name('edit');
            Route::put('/{academicCalendarEvent}', [\App\Http\Controllers\Admin\AcademicCalendarController::class, 'update'])->name('update');
            Route::delete('/{academicCalendarEvent}', [\App\Http\Controllers\Admin\AcademicCalendarController::class, 'destroy'])->name('destroy');
        });

    Route::name('akademik.tahun-pelajaran.')->prefix('akademik/tahun-pelajaran')
        ->middleware('permission:academic.years.view,superadmin,admin,staf_tata_usaha,staf_kesiswaan,kepala_sekolah')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AcademicYearController::class, 'index'])->name('index');
        });

    Route::name('akademik.tahun-pelajaran.')->prefix('akademik/tahun-pelajaran')
        ->middleware('permission:academic.years.manage,superadmin,admin')
        ->group(function () {
            Route::get('/create', [\App\Http\Controllers\Admin\AcademicYearController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\AcademicYearController::class, 'store'])->name('store');
            Route::get('/{academicYear}/edit', [\App\Http\Controllers\Admin\AcademicYearController::class, 'edit'])->name('edit');
            Route::put('/{academicYear}', [\App\Http\Controllers\Admin\AcademicYearController::class, 'update'])->name('update');
            Route::patch('/{academicYear}/set-current', [\App\Http\Controllers\Admin\AcademicYearController::class, 'setCurrent'])->name('set-current');
            Route::delete('/{academicYear}', [\App\Http\Controllers\Admin\AcademicYearController::class, 'destroy'])->name('destroy');
        });

    Route::name('akademik.kelas.')->prefix('akademik/kelas')
        ->middleware('permission:academic.classes.view,superadmin,admin,staf_tata_usaha,staf_kesiswaan,kepala_sekolah,guru')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SchoolClassController::class, 'index'])->name('index');
        });

    Route::name('akademik.kelas.')->prefix('akademik/kelas')
        ->middleware('permission:academic.classes.manage,superadmin,admin,staf_tata_usaha')
        ->group(function () {
            Route::get('/create', [\App\Http\Controllers\Admin\SchoolClassController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\SchoolClassController::class, 'store'])->name('store');
            Route::get('/{schoolClass}/edit', [\App\Http\Controllers\Admin\SchoolClassController::class, 'edit'])->name('edit');
            Route::put('/{schoolClass}', [\App\Http\Controllers\Admin\SchoolClassController::class, 'update'])->name('update');
        });

    Route::name('akademik.kelas.')->prefix('akademik/kelas')
        ->middleware('permission:academic.classes.delete,superadmin,admin')
        ->group(function () {
            Route::patch('/{schoolClass}/toggle', [\App\Http\Controllers\Admin\SchoolClassController::class, 'toggle'])->name('toggle');
            Route::delete('/{schoolClass}', [\App\Http\Controllers\Admin\SchoolClassController::class, 'destroy'])->name('destroy');
        });

    Route::name('akademik.jam-pelajaran.')->prefix('akademik/jam-pelajaran')
        ->middleware('permission:academic.hours.view,superadmin,admin,staf_tata_usaha,staf_kesiswaan,kepala_sekolah,guru')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\LessonScheduleSettingController::class, 'index'])->name('index');
        });

    Route::name('akademik.jam-pelajaran.')->prefix('akademik/jam-pelajaran')
        ->middleware('permission:academic.hours.manage,superadmin,admin,staf_tata_usaha')
        ->group(function () {
            Route::get('/create', [\App\Http\Controllers\Admin\LessonScheduleSettingController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\LessonScheduleSettingController::class, 'store'])->name('store');
            Route::get('/{lessonScheduleSetting}/edit', [\App\Http\Controllers\Admin\LessonScheduleSettingController::class, 'edit'])->name('edit');
            Route::put('/{lessonScheduleSetting}', [\App\Http\Controllers\Admin\LessonScheduleSettingController::class, 'update'])->name('update');
        });

    Route::name('akademik.jam-pelajaran.')->prefix('akademik/jam-pelajaran')
        ->middleware('permission:academic.hours.delete,superadmin,admin')
        ->group(function () {
            Route::patch('/{lessonScheduleSetting}/toggle', [\App\Http\Controllers\Admin\LessonScheduleSettingController::class, 'toggle'])->name('toggle');
            Route::delete('/{lessonScheduleSetting}', [\App\Http\Controllers\Admin\LessonScheduleSettingController::class, 'destroy'])->name('destroy');
        });

    Route::name('akademik.jadwal-pelajaran.')->prefix('akademik/jadwal-pelajaran')
        ->middleware('permission:academic.schedule.view,superadmin,admin,staf_tata_usaha,staf_kesiswaan,kepala_sekolah,guru')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\LessonScheduleController::class, 'index'])->name('index');
        });

    Route::name('akademik.jadwal-pelajaran.')->prefix('akademik/jadwal-pelajaran')
        ->middleware('permission:academic.schedule.manage,superadmin,admin,staf_tata_usaha')
        ->group(function () {
            Route::get('/create', [\App\Http\Controllers\Admin\LessonScheduleController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\LessonScheduleController::class, 'store'])->name('store');
            Route::get('/{lessonSchedule}/edit', [\App\Http\Controllers\Admin\LessonScheduleController::class, 'edit'])->name('edit');
            Route::put('/{lessonSchedule}', [\App\Http\Controllers\Admin\LessonScheduleController::class, 'update'])->name('update');
            Route::delete('/{lessonSchedule}', [\App\Http\Controllers\Admin\LessonScheduleController::class, 'destroy'])->name('destroy');
        });

    Route::name('website.pages.')->prefix('website/konten')
        ->middleware('role:superadmin,admin,staf_tata_usaha')
        ->group(function () {
            Route::get('/', [WebsitePageController::class, 'index'])->name('index');
            Route::get('/{websitePage}/edit', [WebsitePageController::class, 'edit'])->name('edit');
            Route::put('/{websitePage}', [WebsitePageController::class, 'update'])->name('update');
        });

    Route::name('website.menus.')->prefix('website/menu')
        ->middleware('role:superadmin,admin,staf_tata_usaha')
        ->group(function () {
            Route::get('/', [NavigationMenuController::class, 'index'])->name('index');
            Route::get('/{navigationMenu}/edit', [NavigationMenuController::class, 'edit'])->name('edit');
            Route::put('/{navigationMenu}', [NavigationMenuController::class, 'update'])->name('update');
            Route::patch('/{navigationMenu}/toggle', [NavigationMenuController::class, 'toggle'])->name('toggle');
        });

    Route::put('organization-structures/page', [OrganizationStructureController::class, 'updatePage'])
        ->middleware('permission:website.organization.manage,superadmin,admin,staf_tata_usaha')
        ->name('organization-structures.page.update');

    Route::resource('organization-structures', OrganizationStructureController::class)
        ->except(['show'])
        ->middleware('permission:website.organization.manage,superadmin,admin,staf_tata_usaha');

    Route::name('website.values.')->prefix('website/nilai-utama')
        ->middleware('role:superadmin,admin,staf_tata_usaha')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SchoolValueController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\SchoolValueController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\SchoolValueController::class, 'store'])->name('store');
            Route::get('/{schoolValue}/edit', [\App\Http\Controllers\Admin\SchoolValueController::class, 'edit'])->name('edit');
            Route::put('/{schoolValue}', [\App\Http\Controllers\Admin\SchoolValueController::class, 'update'])->name('update');
            Route::patch('/{schoolValue}/toggle', [\App\Http\Controllers\Admin\SchoolValueController::class, 'toggle'])->name('toggle');
            Route::delete('/{schoolValue}', [\App\Http\Controllers\Admin\SchoolValueController::class, 'destroy'])->name('destroy');
        });

    Route::name('users.')->prefix('users')->middleware('superadmin')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('update');
    });

    Route::name('ai-faqs.')->prefix('ai-faqs')
        ->middleware('role:superadmin,admin,staf_tata_usaha,guru')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AiFaqController::class, 'index'])->name('index');
        });

    Route::name('ai-faqs.')->prefix('ai-faqs')
        ->middleware('role:superadmin,admin,staf_tata_usaha')
        ->group(function () {
            Route::get('/create', [\App\Http\Controllers\Admin\AiFaqController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\AiFaqController::class, 'store'])->name('store');
            Route::get('/{aiFaq}/edit', [\App\Http\Controllers\Admin\AiFaqController::class, 'edit'])->name('edit');
            Route::put('/{aiFaq}', [\App\Http\Controllers\Admin\AiFaqController::class, 'update'])->name('update');
            Route::delete('/{aiFaq}', [\App\Http\Controllers\Admin\AiFaqController::class, 'destroy'])->name('destroy');
        });

    Route::name('menu-access.')->prefix('menu-access')
        ->middleware('role:superadmin')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\MenuAccessController::class, 'index'])->name('index');
            Route::put('/', [\App\Http\Controllers\Admin\MenuAccessController::class, 'update'])->name('update');
        });

    Route::name('roles.')->prefix('roles')
        ->middleware('role:superadmin')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('store');
            Route::put('/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('update');
            Route::patch('/{role}/toggle-active', [\App\Http\Controllers\Admin\RoleController::class, 'toggleActive'])->name('toggle-active');
            Route::delete('/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('destroy');
        });
});

Route::get('/progress/{token}', [PublicProgressController::class, 'show'])->name('public.progress');

// Disable public registration - only allows administrator account
