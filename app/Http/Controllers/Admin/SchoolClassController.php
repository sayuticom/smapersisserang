<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LessonSchedule;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $classes = SchoolClass::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.akademik.kelas.index', compact('classes'));
    }

    public function create()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        return view('admin.akademik.kelas.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'grade_level' => ['nullable', 'string', 'max:50'],
            'group' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        SchoolClass::create($validated);

        return redirect()->route('admin.akademik.kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(SchoolClass $schoolClass)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        return view('admin.akademik.kelas.edit', compact('schoolClass'));
    }

    public function update(Request $request, SchoolClass $schoolClass)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'grade_level' => ['nullable', 'string', 'max:50'],
            'group' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $schoolClass->update($validated);

        return redirect()->route('admin.akademik.kelas.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    public function toggle(SchoolClass $schoolClass)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $schoolClass->update(['is_active' => !$schoolClass->is_active]);
        return redirect()->route('admin.akademik.kelas.index')
            ->with('success', 'Status kelas berhasil diubah.');
    }

    public function destroy(SchoolClass $schoolClass)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $scheduleCount = LessonSchedule::where('school_class_id', $schoolClass->id)->count();
        if ($scheduleCount > 0) {
            return redirect()->route('admin.akademik.kelas.index')
                ->with('error', "Kelas {$schoolClass->name} tidak dapat dihapus karena masih digunakan oleh {$scheduleCount} jadwal pelajaran. Nonaktifkan kelas saja.");
        }

        $schoolClass->delete();
        return redirect()->route('admin.akademik.kelas.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }
}
