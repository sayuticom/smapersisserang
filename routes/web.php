<?php

use App\Http\Controllers\Admin\GalleryCategoryController;
use App\Http\Controllers\Admin\NavigationMenuController;
use App\Http\Controllers\Admin\OrganizationStructureController;
use App\Http\Controllers\Admin\PPDBApplicationController;
use App\Http\Controllers\Admin\SchoolImageController;
use App\Http\Controllers\Admin\FosterStudentController;
use App\Http\Controllers\Admin\DonationTransactionController;
use App\Http\Controllers\Admin\SarprasController;
use App\Http\Controllers\Admin\Website\DonationEducationSettingController;
use App\Http\Controllers\Admin\WebsitePageController;
use App\Http\Controllers\Admin\WebsiteSettingController;
use App\Http\Controllers\FosterParentController;
use App\Http\Controllers\PPDBController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\PublicProgressController;
use App\Models\AdmissionYear;
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
    } catch (\Exception $e) {
        $schoolSetting = null;
        $currentAdmissionYear = null;
        $currentAdmissionProgram = null;
        $admissionStats = null;
        $homePage = null;
        $schoolValues = collect();
        $buildingImages = collect();
    }

    return view('pages.welcome', compact(
        'schoolSetting', 'heroImages', 'currentAdmissionYear', 'currentAdmissionProgram', 'admissionStats', 'homePage', 'schoolValues', 'buildingImages'
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
    Route::get('/struktur-organisasi', function () {
        return redirect()->route('public.teachers', ['tab' => 'struktur'], 301);
    })->name('public.struktur-organisasi');
    Route::get('/donasi-pendidikan', [PublicPageController::class, 'donasiPendidikan'])->name('donasi-pendidikan');
    Route::get('/donasi-pendidikan/form-donatur', [PublicPageController::class, 'formDonatur'])->name('donasi-pendidikan.form-donatur');
    Route::post('/donasi-pendidikan/form-donatur', [PublicPageController::class, 'submitDonatur'])
        ->middleware('throttle:5,10')
        ->name('donasi-pendidikan.form-donatur.submit');
    Route::get('/donasi-pendidikan/pembayaran/{order_id}', [PublicPageController::class, 'payment'])->name('donasi-pendidikan.payment');
    Route::post('/donasi-pendidikan/qris/preview', [PublicPageController::class, 'previewQrisInline'])->name('donasi-pendidikan.qris.preview');
    Route::get('/donasi-pendidikan/qris', [PublicPageController::class, 'qris'])->name('donasi-pendidikan.qris');
    Route::get('/donasi-pendidikan/qris/download', [PublicPageController::class, 'downloadQris'])->name('donasi-pendidikan.qris.download');

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
    });
});

Route::middleware('auth')->name('admin.')->prefix('admin')->group(function () {
    Route::get('/ppdb', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'dashboard'])->name('ppdb.dashboard');

    Route::name('ppdb.applications.')->prefix('ppdb/pendaftar')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'index'])->name('index');
        Route::get('/export', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'export'])->name('export');
        Route::get('/export-pdf', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/{studentApplication}', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'show'])->name('show');
        Route::get('/{studentApplication}/print', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'print'])->name('print');
        Route::get('/{studentApplication}/requirements/download', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'downloadRequirements'])->name('requirements.download');
        Route::patch('/{studentApplication}/status', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'updateStatus'])->name('update-status');
        Route::patch('/{studentApplication}/follow-up', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'updateFollowUp'])->name('update-follow-up');
        Route::patch('/{studentApplication}/mark-data-complete', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'markDataComplete'])->name('mark-data-complete');
        Route::post('/{studentApplication}/generate-update-link', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'generateUpdateLink'])->name('generate-update-link');
        Route::delete('/{studentApplication}', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'destroy'])->name('destroy');
    });

    Route::name('ppdb.settings.')->prefix('ppdb/pengaturan')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'settingsEdit'])->name('edit');
        Route::put('/', [\App\Http\Controllers\Admin\PPDBApplicationController::class, 'settingsUpdate'])->name('update');
    });
    Route::name('website.settings.')->prefix('website/pengaturan')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\WebsiteSettingController::class, 'edit'])->name('edit');
        Route::put('/', [\App\Http\Controllers\Admin\WebsiteSettingController::class, 'update'])->name('update');
        Route::post('/generate-token', [\App\Http\Controllers\Admin\WebsiteSettingController::class, 'generateToken'])->name('public-dashboard-token.generate');
    });

    Route::get('/website/donasi-pendidikan', [DonationEducationSettingController::class, 'edit'])
        ->name('website.donasi-pendidikan.edit');
    Route::put('/website/donasi-pendidikan', [DonationEducationSettingController::class, 'update'])
        ->name('website.donasi-pendidikan.update');

    Route::name('donasi-transactions.')->prefix('donasi-transactions')->group(function () {
        Route::get('/', [DonationTransactionController::class, 'index'])->name('index');
        Route::get('/buat-bukti-penerimaan', [DonationTransactionController::class, 'createReceipt'])->name('create-receipt');
        Route::post('/buat-bukti-penerimaan/parse', [DonationTransactionController::class, 'parseReceipt'])->name('parse-receipt');
        Route::post('/buat-bukti-penerimaan', [DonationTransactionController::class, 'storeReceipt'])->name('store-receipt');
        Route::get('/{transaction}', [DonationTransactionController::class, 'show'])->name('show');
        Route::patch('/{transaction}/mark-paid', [DonationTransactionController::class, 'markPaid'])->name('mark-paid');
        Route::patch('/{transaction}/mark-cancelled', [DonationTransactionController::class, 'markCancelled'])->name('mark-cancelled');
    });

    Route::name('sarpras.')->prefix('sarpras')->group(function () {
        Route::get('/', [SarprasController::class, 'dashboard'])->name('dashboard');

        Route::get('/aset', [SarprasController::class, 'assetsIndex'])->name('assets.index');
        Route::get('/aset/create', [SarprasController::class, 'assetsCreate'])->name('assets.create');
        Route::post('/aset', [SarprasController::class, 'assetsStore'])->name('assets.store');
        Route::get('/aset/{asset}', [SarprasController::class, 'assetsShow'])->name('assets.show');
        Route::get('/aset/{asset}/edit', [SarprasController::class, 'assetsEdit'])->name('assets.edit');
        Route::put('/aset/{asset}', [SarprasController::class, 'assetsUpdate'])->name('assets.update');
        Route::delete('/aset/{asset}', [SarprasController::class, 'assetsDestroy'])->name('assets.destroy');

        Route::get('/ruangan', [SarprasController::class, 'roomsIndex'])->name('rooms.index');
        Route::get('/ruangan/create', [SarprasController::class, 'roomsCreate'])->name('rooms.create');
        Route::post('/ruangan', [SarprasController::class, 'roomsStore'])->name('rooms.store');
        Route::get('/ruangan/{room}/edit', [SarprasController::class, 'roomsEdit'])->name('rooms.edit');
        Route::put('/ruangan/{room}', [SarprasController::class, 'roomsUpdate'])->name('rooms.update');
        Route::delete('/ruangan/{room}', [SarprasController::class, 'roomsDestroy'])->name('rooms.destroy');

        Route::get('/kebutuhan', [SarprasController::class, 'needsIndex'])->name('needs.index');
        Route::get('/kebutuhan/create', [SarprasController::class, 'needsCreate'])->name('needs.create');
        Route::post('/kebutuhan', [SarprasController::class, 'needsStore'])->name('needs.store');
        Route::get('/kebutuhan/{need}/edit', [SarprasController::class, 'needsEdit'])->name('needs.edit');
        Route::put('/kebutuhan/{need}', [SarprasController::class, 'needsUpdate'])->name('needs.update');
        Route::delete('/kebutuhan/{need}', [SarprasController::class, 'needsDestroy'])->name('needs.destroy');

        Route::get('/perbaikan', [SarprasController::class, 'maintenancesIndex'])->name('maintenances.index');
        Route::get('/perbaikan/create', [SarprasController::class, 'maintenancesCreate'])->name('maintenances.create');
        Route::post('/perbaikan', [SarprasController::class, 'maintenancesStore'])->name('maintenances.store');
        Route::get('/perbaikan/{maintenance}/edit', [SarprasController::class, 'maintenancesEdit'])->name('maintenances.edit');
        Route::put('/perbaikan/{maintenance}', [SarprasController::class, 'maintenancesUpdate'])->name('maintenances.update');
        Route::delete('/perbaikan/{maintenance}', [SarprasController::class, 'maintenancesDestroy'])->name('maintenances.destroy');

        Route::get('/pengadaan', [SarprasController::class, 'procurementsIndex'])->name('procurements.index');
        Route::get('/pengadaan/create', [SarprasController::class, 'procurementsCreate'])->name('procurements.create');
        Route::post('/pengadaan', [SarprasController::class, 'procurementsStore'])->name('procurements.store');
        Route::get('/pengadaan/{procurement}/edit', [SarprasController::class, 'procurementsEdit'])->name('procurements.edit');
        Route::put('/pengadaan/{procurement}', [SarprasController::class, 'procurementsUpdate'])->name('procurements.update');
        Route::delete('/pengadaan/{procurement}', [SarprasController::class, 'procurementsDestroy'])->name('procurements.destroy');

        Route::get('/laporan', [SarprasController::class, 'laporan'])->name('laporan');
    });

    Route::name('orang-tua-asuh.')->prefix('orang-tua-asuh')->group(function () {
        Route::get('/', [FosterStudentController::class, 'index'])->name('index');
        Route::get('/{submission}', [FosterStudentController::class, 'show'])->name('show');
        Route::patch('/{submission}/status', [FosterStudentController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{submission}', [FosterStudentController::class, 'destroy'])->name('destroy');
    });

    Route::name('website.media.')->prefix('website/media')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SchoolImageController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\SchoolImageController::class, 'mediaStore'])->name('store');
        Route::patch('/{schoolImage}/toggle', [\App\Http\Controllers\Admin\SchoolImageController::class, 'toggle'])->name('toggle');
        Route::delete('/{schoolImage}', [\App\Http\Controllers\Admin\SchoolImageController::class, 'destroy'])->name('destroy');
    });

    Route::name('website.hero-images.')->prefix('website/hero-images')->group(function () {
        Route::post('/', [\App\Http\Controllers\Admin\SchoolImageController::class, 'store'])->name('store');
        Route::patch('/{schoolImage}/toggle', [\App\Http\Controllers\Admin\SchoolImageController::class, 'toggle'])->name('toggle');
        Route::delete('/{schoolImage}', [\App\Http\Controllers\Admin\SchoolImageController::class, 'destroy'])->name('destroy');
    });

    Route::name('website.gallery-images.')->prefix('website/gallery-images')->group(function () {
        Route::post('/', [\App\Http\Controllers\Admin\SchoolImageController::class, 'galleryStore'])->name('store');
        Route::patch('/{schoolImage}/toggle', [\App\Http\Controllers\Admin\SchoolImageController::class, 'toggle'])->name('toggle');
        Route::delete('/{schoolImage}', [\App\Http\Controllers\Admin\SchoolImageController::class, 'destroy'])->name('destroy');
    });

    Route::name('website.categories.')->prefix('website/kategori-galeri')->group(function () {
        Route::get('/', [GalleryCategoryController::class, 'index'])->name('index');
        Route::get('/create', [GalleryCategoryController::class, 'create'])->name('create');
        Route::post('/', [GalleryCategoryController::class, 'store'])->name('store');
        Route::get('/{galleryCategory}/edit', [GalleryCategoryController::class, 'edit'])->name('edit');
        Route::put('/{galleryCategory}', [GalleryCategoryController::class, 'update'])->name('update');
        Route::patch('/{galleryCategory}/toggle', [GalleryCategoryController::class, 'toggle'])->name('toggle');
        Route::delete('/{galleryCategory}', [GalleryCategoryController::class, 'destroy'])->name('destroy');
    });

    Route::name('website.figures.')->prefix('website/tokoh')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SchoolFigureController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\SchoolFigureController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\SchoolFigureController::class, 'store'])->name('store');
        Route::get('/{schoolFigure}/edit', [\App\Http\Controllers\Admin\SchoolFigureController::class, 'edit'])->name('edit');
        Route::put('/{schoolFigure}', [\App\Http\Controllers\Admin\SchoolFigureController::class, 'update'])->name('update');
        Route::patch('/{schoolFigure}/toggle', [\App\Http\Controllers\Admin\SchoolFigureController::class, 'toggle'])->name('toggle');
        Route::delete('/{schoolFigure}', [\App\Http\Controllers\Admin\SchoolFigureController::class, 'destroy'])->name('destroy');
    });

    Route::name('website.faq.')->prefix('website/faq')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\FaqController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\FaqController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\FaqController::class, 'store'])->name('store');
        Route::get('/{faq}/edit', [\App\Http\Controllers\Admin\FaqController::class, 'edit'])->name('edit');
        Route::put('/{faq}', [\App\Http\Controllers\Admin\FaqController::class, 'update'])->name('update');
        Route::patch('/{faq}/toggle', [\App\Http\Controllers\Admin\FaqController::class, 'toggle'])->name('toggle');
        Route::delete('/{faq}', [\App\Http\Controllers\Admin\FaqController::class, 'destroy'])->name('destroy');
    });

    Route::name('website.teachers.')->prefix('website/guru')->group(function () {
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

    Route::name('website.subjects.')->prefix('website/mata-pelajaran')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SchoolSubjectController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\SchoolSubjectController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\SchoolSubjectController::class, 'store'])->name('store');
        Route::get('/{schoolSubject}/edit', [\App\Http\Controllers\Admin\SchoolSubjectController::class, 'edit'])->name('edit');
        Route::put('/{schoolSubject}', [\App\Http\Controllers\Admin\SchoolSubjectController::class, 'update'])->name('update');
        Route::patch('/{schoolSubject}/toggle', [\App\Http\Controllers\Admin\SchoolSubjectController::class, 'toggle'])->name('toggle');
        Route::delete('/{schoolSubject}', [\App\Http\Controllers\Admin\SchoolSubjectController::class, 'destroy'])->name('destroy');
    });

    Route::name('website.pages.')->prefix('website/konten')->group(function () {
        Route::get('/', [WebsitePageController::class, 'index'])->name('index');
        Route::get('/{websitePage}/edit', [WebsitePageController::class, 'edit'])->name('edit');
        Route::put('/{websitePage}', [WebsitePageController::class, 'update'])->name('update');
    });

    Route::name('website.menus.')->prefix('website/menu')->group(function () {
        Route::get('/', [NavigationMenuController::class, 'index'])->name('index');
        Route::get('/{navigationMenu}/edit', [NavigationMenuController::class, 'edit'])->name('edit');
        Route::put('/{navigationMenu}', [NavigationMenuController::class, 'update'])->name('update');
        Route::patch('/{navigationMenu}/toggle', [NavigationMenuController::class, 'toggle'])->name('toggle');
    });

    Route::put('organization-structures/page', [OrganizationStructureController::class, 'updatePage'])
        ->name('organization-structures.page.update');
    Route::resource('organization-structures', OrganizationStructureController::class)
        ->except(['show']);

    Route::name('website.values.')->prefix('website/nilai-utama')->group(function () {
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

    Route::name('ai-faqs.')->prefix('ai-faqs')->middleware('auth')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AiFaqController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\AiFaqController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\AiFaqController::class, 'store'])->name('store');
        Route::get('/{aiFaq}/edit', [\App\Http\Controllers\Admin\AiFaqController::class, 'edit'])->name('edit');
        Route::put('/{aiFaq}', [\App\Http\Controllers\Admin\AiFaqController::class, 'update'])->name('update');
        Route::delete('/{aiFaq}', [\App\Http\Controllers\Admin\AiFaqController::class, 'destroy'])->name('destroy');
    });
});

Route::get('/progress/{token}', [PublicProgressController::class, 'show'])->name('public.progress');

// Disable public registration - only allows administrator account
