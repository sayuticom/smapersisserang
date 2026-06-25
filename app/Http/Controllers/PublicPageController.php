<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\SchoolFigure;
use App\Models\SchoolImage;
use App\Models\SchoolSetting;
use App\Models\SchoolSubject;
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
        } catch (\Exception $e) {
            $schoolSetting = null;
            $websitePage = null;
        }

        return view('pages.profile', compact('schoolSetting', 'websitePage'));
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
        } catch (\Exception $e) {
            $schoolSetting = null;
            $websitePage = null;
            $subjectsByCategory = collect();
        }

        $subjectCategories = SchoolSubject::CATEGORIES;

        return view('pages.program', compact('schoolSetting', 'websitePage', 'subjectCategories', 'subjectsByCategory'));
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
        } catch (\Exception $e) {
            $schoolSetting = null;
            $websitePage = null;
        }

        $category = $request->get('category');

        $query = SchoolImage::where('category', '!=', 'hero')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->latest();

        if ($category && in_array($category, ['gedung', 'kegiatan', 'kelas', 'santri', 'kajian', 'teknologi'])) {
            $query->where('category', $category);
        }

        $galleryImages = $query->get();

        $categories = [
            'gedung' => 'Gedung',
            'kegiatan' => 'Kegiatan',
            'kelas' => 'Kelas',
            'santri' => 'Santri',
            'kajian' => 'Kajian',
            'teknologi' => 'Teknologi',
        ];

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

    public function teachers()
    {
        try {
            $schoolSetting = SchoolSetting::current();

            $subjectsByCategory = SchoolSubject::with(['teachers' => function ($q) {
                    $q->where('is_active', true)->orderBy('sort_order');
                }])
                ->where('is_active', true)
                ->orderBy('category')
                ->orderBy('sort_order')
                ->get()
                ->groupBy('category');

            $headmaster = Teacher::where('position', 'Kepala Sekolah')->first();

            $websitePage = WebsitePage::key('teachers');
        } catch (\Exception $e) {
            $schoolSetting = null;
            $subjectsByCategory = collect();
            $headmaster = null;
            $websitePage = null;
        }

        $subjectCategories = SchoolSubject::CATEGORIES;

        return view('pages.teachers', compact('schoolSetting', 'websitePage', 'subjectsByCategory', 'subjectCategories', 'headmaster'));
    }
}
