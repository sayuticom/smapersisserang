<?php

namespace App\Http\Controllers;

use App\Models\FosterParentSubmission;
use App\Models\SchoolSetting;
use App\Models\StudentApplication;
use Illuminate\Http\Request;

class FosterParentController extends Controller
{
    public function index()
    {
        try {
            $schoolSetting = SchoolSetting::current();
        } catch (\Exception $e) {
            $schoolSetting = null;
        }

        $students = StudentApplication::whereNotNull('diterima_di_kelas')
            ->orderBy('student_name')
            ->get(['id', 'student_name', 'gender', 'diterima_di_kelas']);

        return view('pages.orang-tua-asuh', compact('schoolSetting', 'students'));
    }

    public function submit(Request $request)
    {
        if ($request->filled('website_url')) {
            return redirect()->route('orang-tua-asuh')
                ->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }

        $formStartedAt = $request->input('form_started_at');
        if ($formStartedAt) {
            $started = strtotime($formStartedAt);
            $elapsed = time() - $started;
            if ($elapsed < 3) {
                return redirect()->route('orang-tua-asuh')
                    ->with('error', 'Terlalu cepat. Silakan isi form dengan benar.');
            }
        }

        $data = $request->validate([
            'student_id' => ['nullable', 'exists:student_applications,id'],
            'donor_name' => ['nullable', 'string', 'max:100'],
            'donor_phone' => ['required', 'string', 'max:30'],
            'amount' => ['required', 'string', 'max:50'],
            'custom_amount' => ['nullable', 'numeric', 'min:1000', 'max:50000000'],
            'commitment_duration' => ['required', 'string', 'max:50'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $amount = $data['amount'] === 'lainnya' && $data['custom_amount']
            ? (int) str_replace(['.', ','], '', $data['custom_amount'])
            : ($data['amount'] !== 'lainnya' ? (int) $data['amount'] : null);

        if ($amount !== null && $amount < 1000) {
            return back()->withErrors(['amount' => 'Minimal donasi Rp1.000'])->withInput();
        }

        FosterParentSubmission::create([
            'student_id' => $data['student_id'] ?? null,
            'donor_name' => trim($data['donor_name'] ?? '') ?: null,
            'donor_phone' => trim($data['donor_phone'] ?? ''),
            'amount' => $amount,
            'commitment_duration' => $data['commitment_duration'],
            'note' => $data['note'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('orang-tua-asuh')
            ->with('success', 'Pengajuan berhasil dikirim. Tim kami akan menghubungi Anda melalui WhatsApp.');
    }
}
