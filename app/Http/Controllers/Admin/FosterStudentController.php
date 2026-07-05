<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FosterParentSubmission;
use App\Models\FosterStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FosterStudentController extends Controller
{
    public function index()
    {
        $students = FosterStudent::orderBy('is_priority', 'desc')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.orang-tua-asuh.index', compact('students'));
    }

    public function create()
    {
        return view('admin.orang-tua-asuh.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'class_name' => 'required|string|max:100',
            'origin' => 'nullable|string|max:200',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'need_description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'is_priority' => 'boolean',
            'foster_status' => 'required|in:available,assigned,inactive',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['is_priority'] = $request->boolean('is_priority');

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('foster-students', 'public');
        }

        FosterStudent::create($data);

        return redirect()->route('admin.orang-tua-asuh.index')
            ->with('success', 'Data murid berhasil ditambahkan.');
    }

    public function edit(FosterStudent $fosterStudent)
    {
        return view('admin.orang-tua-asuh.edit', compact('fosterStudent'));
    }

    public function update(Request $request, FosterStudent $fosterStudent)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'class_name' => 'required|string|max:100',
            'origin' => 'nullable|string|max:200',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'need_description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'is_priority' => 'boolean',
            'foster_status' => 'required|in:available,assigned,inactive',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['is_priority'] = $request->boolean('is_priority');

        if ($request->hasFile('photo')) {
            if ($fosterStudent->photo_path) {
                Storage::disk('public')->delete($fosterStudent->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('foster-students', 'public');
        }

        $fosterStudent->update($data);

        return redirect()->route('admin.orang-tua-asuh.index')
            ->with('success', 'Data murid berhasil diperbarui.');
    }

    public function destroy(FosterStudent $fosterStudent)
    {
        if ($fosterStudent->photo_path) {
            Storage::disk('public')->delete($fosterStudent->photo_path);
        }

        $fosterStudent->submissions()->delete();
        $fosterStudent->delete();

        return redirect()->route('admin.orang-tua-asuh.index')
            ->with('success', 'Data murid berhasil dihapus.');
    }

    public function submissions()
    {
        $submissions = FosterParentSubmission::with('fosterStudent')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.orang-tua-asuh.submissions', compact('submissions'));
    }

    public function markPaid(FosterParentSubmission $submission)
    {
        $submission->update(['payment_status' => 'paid']);

        if ($submission->foster_student_id) {
            $submission->fosterStudent->update(['foster_status' => 'assigned']);
        }

        return back()->with('success', 'Pembayaran ditandai sebagai lunas.');
    }

    public function markCancelled(FosterParentSubmission $submission)
    {
        $submission->update(['payment_status' => 'cancelled']);

        return back()->with('success', 'Pembayaran ditandai sebagai batal.');
    }
}
