<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\GalleryCategory;
use App\Models\SchoolFigure;
use App\Models\SchoolImage;
use App\Models\SchoolSetting;
use App\Models\SchoolSubject;
use App\Models\SchoolValue;
use App\Models\Teacher;
use App\Models\WebsitePage;
use Illuminate\Http\Request;

class PublicPageController extends Controller
{
    public function profile()
    {
        try {
            $schoolSetting = SchoolSetting::current();
            $websitePage = WebsitePage::key('profile');

            $buildingImages = SchoolImage::where('is_active', true)
                ->whereHas('categories', fn($q) => $q->where('slug', 'fasilitas'))
                ->orderBy('sort_order')
                ->get();
        } catch (\Exception $e) {
            $schoolSetting = null;
            $websitePage = null;
            $buildingImages = collect();
        }

        return view('pages.profile', compact('schoolSetting', 'websitePage', 'buildingImages'));
    }

    public function program()
    {
        try {
            $schoolSetting = SchoolSetting::current();
            $websitePage = WebsitePage::key('program');
            $subjectsByCategory = SchoolSubject::with(['teachers' => function ($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                }])
                ->where('is_active', true)
                ->orderBy('category')
                ->orderBy('sort_order')
                ->get()
                ->groupBy('category');

            $schoolValues = SchoolValue::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        } catch (\Exception $e) {
            $schoolSetting = null;
            $websitePage = null;
            $subjectsByCategory = collect();
            $schoolValues = collect();
        }

        $subjectCategories = SchoolSubject::CATEGORIES;

        return view('pages.program', compact('schoolSetting', 'websitePage', 'subjectCategories', 'subjectsByCategory', 'schoolValues'));
    }

    public function boarding()
    {
        try {
            $schoolSetting = SchoolSetting::current();
            $websitePage = WebsitePage::key('boarding');
        } catch (\Exception $e) {
            $schoolSetting = null;
            $websitePage = null;
        }

        return view('pages.boarding', compact('schoolSetting', 'websitePage'));
    }

    public function gallery(Request $request)
    {
        try {
            $schoolSetting = SchoolSetting::current();
            $websitePage = WebsitePage::key('gallery');
            $galleryCategories = GalleryCategory::where('slug', '!=', 'hero')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        } catch (\Exception $e) {
            $schoolSetting = null;
            $websitePage = null;
            $galleryCategories = collect();
        }

        $category = $request->get('category');

        $query = SchoolImage::where('is_active', true)
            ->whereHas('categories', fn($q) => $q->where('slug', '!=', 'hero'))
            ->orderBy('sort_order')
            ->latest();

        if ($category && $galleryCategories->firstWhere('slug', $category)) {
            $query->whereHas('categories', fn($q) => $q->where('slug', $category));
        }

        $galleryImages = $query->with('categories')->get();

        $categories = $galleryCategories->pluck('name', 'slug');

        return view('pages.gallery', compact('schoolSetting', 'galleryImages', 'categories', 'category', 'websitePage'));
    }

    public function figures()
    {
        try {
            $schoolSetting = SchoolSetting::current();

            $figures = SchoolFigure::where('is_active', true)
                ->orderBy('sort_order')
                ->latest()
                ->get();

            $websitePage = WebsitePage::key('figures');
        } catch (\Exception $e) {
            $schoolSetting = null;
            $figures = collect();
            $websitePage = null;
        }

        return view('pages.figures', compact('schoolSetting', 'figures', 'websitePage'));
    }

    public function faq()
    {
        try {
            $schoolSetting = SchoolSetting::current();

            $faqs = Faq::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        } catch (\Exception $e) {
            $schoolSetting = null;
            $faqs = collect();
        }

        return view('pages.faq', compact('schoolSetting', 'faqs'));
    }

    public function strukturOrganisasi()
    {
        try {
            $schoolSetting = SchoolSetting::current();
        } catch (\Exception $e) {
            $schoolSetting = null;
        }

        $organisasi = [
            [
                'level' => 1,
                'jabatan' => 'Pembina / Pimpinan Persis',
                'deskripsi' => 'Dewan Pembina Pondok Pesantren dan Madrasah Persatuan Islam',
            ],
            [
                'level' => 1,
                'jabatan' => 'Bidang Pendidikan / Majelis Pendidikan',
                'deskripsi' => 'Majelis Pendidikan Persatuan Islam Cabang Serang',
            ],
            [
                'level' => 2,
                'jabatan' => 'Kepala SMA Persis Serang',
                'deskripsi' => 'Pimpinan tertinggi sekolah',
                'children' => [
                    [
                        'level' => 3,
                        'jabatan' => 'Wakil Kepala Sekolah Bidang Kurikulum',
                        'singkatan' => 'Waka Kurikulum',
                        'anggota' => ['Koordinator Pembelajaran', 'Guru Mata Pelajaran', 'Wali Kelas'],
                    ],
                    [
                        'level' => 3,
                        'jabatan' => 'Wakil Kepala Sekolah Bidang Kesiswaan',
                        'singkatan' => 'Waka Kesiswaan',
                        'anggota' => ['Pembina OSIS / IPP', 'Pembina Ekstrakurikuler', 'Bimbingan Konseling', 'Tim Kedisiplinan Santri'],
                    ],
                    [
                        'level' => 3,
                        'jabatan' => 'Wakil Kepala Sekolah Bidang Sarana dan Prasarana',
                        'singkatan' => 'Waka Sarpras',
                        'anggota' => ['Penanggung Jawab Ruang Kelas', 'Penanggung Jawab Laboratorium / Komputer', 'Penanggung Jawab Asrama', 'Penanggung Jawab Inventaris'],
                    ],
                    [
                        'level' => 3,
                        'jabatan' => 'Wakil Kepala Sekolah Bidang Humas dan Kerja Sama',
                        'singkatan' => 'Waka Humas',
                        'anggota' => ['Hubungan Orang Tua Santri', 'Kerja Sama Lembaga', 'Publikasi dan Media Sekolah', 'SPMB / PPDB'],
                    ],
                    [
                        'level' => 3,
                        'jabatan' => 'Kepala Asrama / Boarding School',
                        'singkatan' => 'Kepala Asrama',
                        'anggota' => ['Murobi Ikhwan', 'Murobi Akhwat', 'Koordinator Piket Asrama', 'Koordinator Makan Santri', 'Koordinator Kebersihan dan Keamanan Asrama'],
                    ],
                    [
                        'level' => 3,
                        'jabatan' => 'Tata Usaha',
                        'anggota' => ['Administrasi Sekolah', 'Keuangan', 'Operator Sekolah', 'Arsip dan Dokumen'],
                    ],
                    [
                        'level' => 3,
                        'jabatan' => 'Unit Pendukung',
                        'anggota' => ['Perpustakaan', 'Laboratorium Komputer', 'UKS', 'Keamanan', 'Kebersihan'],
                    ],
                ],
            ],
            [
                'level' => 2,
                'jabatan' => 'Komite Sekolah',
                'deskripsi' => 'Badan mandiri yang mewadahi peran serta masyarakat',
            ],
        ];

        return view('pages.struktur-organisasi', compact('schoolSetting', 'organisasi'));
    }

    public function teachers()
    {
        try {
            $schoolSetting = SchoolSetting::current();

            $heroImages = SchoolImage::where('is_active', true)
                ->whereHas('categories', fn($q) => $q->where('slug', 'guru'))
                ->orderBy('sort_order')
                ->latest()
                ->get();

            if ($heroImages->isEmpty()) {
                $heroImages = SchoolImage::where('is_active', true)
                    ->whereHas('categories', fn($q) => $q->where('slug', 'fasilitas'))
                    ->orderBy('sort_order')
                    ->latest()
                    ->get();
            }

            $headmaster = Teacher::with(['subjects' => function ($query) {
                    $query->where('school_subjects.is_active', true)
                        ->orderBy('school_subjects.sort_order');
                }])
                ->where('is_active', true)
                ->where('position', 'Kepala Sekolah')
                ->orderBy('sort_order')
                ->first();

            if ($headmaster) {
                $headmaster->setAttribute('label', 'KEPALA SEKOLAH');
            }

            $teachers = Teacher::with(['subjects' => function ($query) {
                    $query->where('school_subjects.is_active', true)
                        ->orderBy('school_subjects.sort_order');
                }])
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('position')
                        ->orWhere('position', '!=', 'Kepala Sekolah');
                })
                ->orderBy('sort_order')
                ->get();

            $groupedByCategory = collect();
            $orphanTeachers = collect();

            foreach ($teachers as $teacher) {
                $teacher->setAttribute('label', 'GURU PENGAMPU');
                $activeSubjects = $teacher->subjects;

                if ($activeSubjects->isEmpty()) {
                    $orphanTeachers->push($teacher);
                    continue;
                }

                $primarySubject = $activeSubjects->first();
                $category = $primarySubject->category;

                if (!$groupedByCategory->has($category)) {
                    $groupedByCategory->put($category, collect([
                        'category_label' => $primarySubject->category_label,
                        'teachers' => collect(),
                    ]));
                }

                $categoryGroup = $groupedByCategory->get($category);
                $alreadyAdded = $categoryGroup['teachers']->first(fn($t) => $t->id === $teacher->id);
                if (!$alreadyAdded) {
                    $categoryGroup['teachers']->push($teacher);
                }
            }

            $groupedByCategory = $groupedByCategory->sortKeys();

            $websitePage = WebsitePage::key('teachers');
        } catch (\Exception $e) {
            $schoolSetting = null;
            $headmaster = null;
            $groupedByCategory = collect();
            $orphanTeachers = collect();
            $websitePage = null;
            $heroImages = collect();
        }

        return view('pages.teachers', compact('schoolSetting', 'websitePage', 'headmaster', 'groupedByCategory', 'orphanTeachers', 'heroImages'));
    }
}
