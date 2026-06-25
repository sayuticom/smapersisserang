<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::orderBy('sort_order')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $teachers = $query->get();

        return view('admin.website.teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('admin.website.teachers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'teacher_quote' => ['nullable', 'string', 'max:1000'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('school/teachers', 'public');
        }

        Teacher::create([
            'name' => $validated['name'],
            'subject' => $validated['subject'] ?? null,
            'position' => $validated['position'] ?? null,
            'description' => $validated['description'] ?? null,
            'teacher_quote' => $validated['teacher_quote'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
            'photo_path' => $validated['photo_path'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('admin.website.teachers.index')
            ->with('success', 'Guru berhasil ditambahkan.');
    }

    public function edit(Teacher $teacher)
    {
        return view('admin.website.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'teacher_quote' => ['nullable', 'string', 'max:1000'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('photo')) {
            if ($teacher->photo_path) {
                Storage::disk('public')->delete($teacher->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('school/teachers', 'public');
        }

        $teacher->update($validated);

        return redirect()->route('admin.website.teachers.index')
            ->with('success', 'Guru berhasil diperbarui.');
    }

    public function toggle(Teacher $teacher)
    {
        $teacher->update([
            'is_active' => !$teacher->is_active,
        ]);

        return redirect()->route('admin.website.teachers.index')
            ->with('success', 'Status guru berhasil diubah.');
    }

    public function destroy(Teacher $teacher)
    {
        if ($teacher->photo_path) {
            Storage::disk('public')->delete($teacher->photo_path);
        }

        $teacher->delete();

        return redirect()->route('admin.website.teachers.index')
            ->with('success', 'Guru berhasil dihapus.');
    }

    public function generateToken(Teacher $teacher)
    {
        $teacher->generatePublicEditToken();

        return redirect()->route('admin.website.teachers.edit', $teacher)
            ->with('success', 'Token edit mandiri guru berhasil dibuat.');
    }

    public function resetToken(Teacher $teacher)
    {
        $teacher->generatePublicEditToken();

        return redirect()->route('admin.website.teachers.edit', $teacher)
            ->with('success', 'Token edit mandiri guru berhasil direset. Token lama sudah tidak berlaku.');
    }
}