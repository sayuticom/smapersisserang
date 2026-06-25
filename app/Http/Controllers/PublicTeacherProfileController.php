<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicTeacherProfileController extends Controller
{
    public function edit(string $token)
    {
        $teacher = Teacher::where('public_edit_token', $token)->firstOrFail();

        return view('teachers.edit-token', compact('teacher'));
    }

    public function update(Request $request, string $token)
    {
        $teacher = Teacher::where('public_edit_token', $token)->firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string'],
            'teacher_quote' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
            'description' => $validated['description'] ?? null,
            'teacher_quote' => $validated['teacher_quote'] ?? null,
        ];

        if ($request->hasFile('photo')) {
            if ($teacher->photo_path) {
                Storage::disk('public')->delete($teacher->photo_path);
            }
            $updateData['photo_path'] = $request->file('photo')->store('school/teachers', 'public');
        }

        $teacher->update($updateData);

        return redirect()->route('public.teachers.edit-token', $token)
            ->with('success', 'Data guru berhasil diperbarui.');
    }
}
