<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FosterParentSubmission;
use Illuminate\Http\Request;

class FosterStudentController extends Controller
{
    public function index()
    {
        $submissions = FosterParentSubmission::with('student')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.orang-tua-asuh.index', compact('submissions'));
    }

    public function show(FosterParentSubmission $submission)
    {
        $submission->load('student');
        return view('admin.orang-tua-asuh.show', compact('submission'));
    }

    public function updateStatus(Request $request, FosterParentSubmission $submission)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,dihubungi,aktif,batal',
        ]);

        $submission->update($data);

        $label = match ($data['status']) {
            'pending' => 'Menunggu',
            'dihubungi' => 'Perlu Dihubungi',
            'aktif' => 'Aktif',
            'batal' => 'Batal',
        };

        return back()->with('success', "Status pengajuan diubah menjadi {$label}.");
    }

    public function destroy(FosterParentSubmission $submission)
    {
        $submission->delete();

        return redirect()->route('admin.orang-tua-asuh.index')
            ->with('success', 'Pengajuan berhasil dihapus.');
    }
}
