<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolImageController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category');

        $query = SchoolImage::orderBy('sort_order')->latest();

        if ($category && in_array($category, ['hero', 'gedung', 'kegiatan', 'kelas', 'santri', 'kajian', 'teknologi'])) {
            $query->where('category', $category);
        }

        $mediaImages = $query->get();

        $categories = [
            'hero' => 'Hero Slider',
            'gedung' => 'Gedung',
            'kegiatan' => 'Kegiatan',
            'kelas' => 'Kelas',
            'santri' => 'Santri',
            'kajian' => 'Kajian',
            'teknologi' => 'Teknologi',
        ];

        return view('admin.website.media.index', compact('mediaImages', 'categories', 'category'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $path = $request->file('image')->store('school/hero', 'public');

        SchoolImage::create([
            'title' => $validated['title'] ?? null,
            'image_path' => $path,
            'category' => 'hero',
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('admin.website.media.index')
            ->with('success', 'Gambar hero berhasil ditambahkan.');
    }

    public function galleryStore(Request $request)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:gedung,kegiatan,kelas,santri,kajian,teknologi'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $path = $request->file('image')->store('school/gallery', 'public');

        SchoolImage::create([
            'title' => $validated['title'] ?? null,
            'image_path' => $path,
            'category' => $validated['category'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('admin.website.media.index')
            ->with('success', 'Gambar galeri berhasil ditambahkan.');
    }

    public function mediaStore(Request $request)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:hero,gedung,kegiatan,kelas,santri,kajian,teknologi'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($validated['category'] === 'hero') {
            $path = $request->file('image')->store('school/hero', 'public');
        } else {
            $path = $request->file('image')->store('school/gallery', 'public');
        }

        SchoolImage::create([
            'title' => $validated['title'] ?? null,
            'image_path' => $path,
            'category' => $validated['category'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('admin.website.media.index')
            ->with('success', 'Gambar berhasil ditambahkan.');
    }

    public function toggle(SchoolImage $schoolImage)
    {
        $schoolImage->update([
            'is_active' => !$schoolImage->is_active,
        ]);

        return redirect()->route('admin.website.media.index')
            ->with('success', 'Status gambar berhasil diubah.');
    }

    public function destroy(SchoolImage $schoolImage)
    {
        try {
            Storage::disk('public')->delete($schoolImage->image_path);
        } catch (\Exception $e) {
        }

        $schoolImage->delete();

        return redirect()->route('admin.website.media.index')
            ->with('success', 'Gambar berhasil dihapus.');
    }
}
