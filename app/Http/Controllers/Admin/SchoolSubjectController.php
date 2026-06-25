<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolSubject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class SchoolSubjectController extends Controller
{
    public function index()
    {
        $subjects = SchoolSubject::with('teachers')
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        return view('admin.website.subjects.index', compact('subjects'));
    }

    public function create()
    {
        $teachers = Teacher::where('is_active', true)->orderBy('name')->get();
        return view('admin.website.subjects.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:nasional,keislaman,teknologi,boarding'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'teacher_ids' => ['nullable', 'array'],
            'teacher_ids.*' => ['exists:teachers,id'],
        ]);

        $subject = SchoolSubject::create([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if (!empty($validated['teacher_ids'])) {
            $subject->teachers()->syncWithoutDetaching($validated['teacher_ids']);
        }

        return redirect()->route('admin.website.subjects.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit(SchoolSubject $schoolSubject)
    {
        $teachers = Teacher::where('is_active', true)->orderBy('name')->get();
        $schoolSubject->load('teachers');
        return view('admin.website.subjects.edit', compact('schoolSubject', 'teachers'));
    }

    public function update(Request $request, SchoolSubject $schoolSubject)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:nasional,keislaman,teknologi,boarding'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'teacher_ids' => ['nullable', 'array'],
            'teacher_ids.*' => ['exists:teachers,id'],
        ]);

        $schoolSubject->update([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if (!empty($validated['teacher_ids'])) {
            $schoolSubject->teachers()->sync($validated['teacher_ids']);
        } else {
            $schoolSubject->teachers()->sync([]);
        }

        return redirect()->route('admin.website.subjects.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function toggle(SchoolSubject $schoolSubject)
    {
        $schoolSubject->update([
            'is_active' => !$schoolSubject->is_active,
        ]);

        return redirect()->route('admin.website.subjects.index')
            ->with('success', 'Status mata pelajaran berhasil diubah.');
    }

    public function destroy(SchoolSubject $schoolSubject)
    {
        $schoolSubject->delete();

        return redirect()->route('admin.website.subjects.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
