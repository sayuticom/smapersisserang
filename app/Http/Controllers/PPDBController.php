<?php

namespace App\Http\Controllers;

use App\Models\AdmissionYear;
use App\Models\AdmissionProgram;
use App\Models\StudentApplication;
use App\Models\ApplicationStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PPDBController extends Controller
{
    private array $statusLabels = [
        'baru_daftar' => 'Pendaftaran Berhasil Diterima',
        'menunggu_verifikasi' => 'Menunggu Verifikasi Panitia',
        'data_kurang' => 'Data Perlu Dilengkapi',
        'terverifikasi' => 'Data Terverifikasi',
        'wawancara' => 'Proses Wawancara',
        'lulus' => 'Lulus Seleksi',
        'cadangan' => 'Masuk Daftar Cadangan',
        'tidak_lulus' => 'Belum Lulus Seleksi',
        'diterima' => 'Diterima sebagai Siswa',
        'mengundurkan_diri' => 'Mengundurkan Diri',
    ];

    private array $statusMessages = [
        'baru_daftar' => 'Pendaftaran sudah diterima. Panitia akan melakukan pengecekan data.',
        'menunggu_verifikasi' => 'Data sedang diperiksa oleh panitia SPMB.',
        'data_kurang' => 'Ada data yang perlu dilengkapi. Silakan hubungi admin SPMB.',
        'terverifikasi' => 'Data pendaftaran sudah diverifikasi.',
        'wawancara' => 'Calon siswa masuk tahap wawancara. Panitia akan menghubungi orang tua.',
        'lulus' => 'Selamat, calon siswa dinyatakan lulus seleksi.',
        'cadangan' => 'Calon siswa masuk daftar cadangan.',
        'tidak_lulus' => 'Mohon maaf, calon siswa belum lulus seleksi.',
        'diterima' => 'Selamat, calon siswa diterima sebagai siswa SMA Persis Serang.',
        'mengundurkan_diri' => 'Status pendaftaran tercatat mengundurkan diri.',
    ];
    public function create()
    {
        $admissionYear = AdmissionYear::where('is_current', true)->first();

        if (!$admissionYear) {
            return view('ppdb.closed', [
                'title' => 'SPMB Belum Tersedia',
                'message' => 'SPMB belum tersedia saat ini. Silakan hubungi sekolah untuk informasi lebih lanjut.',
            ]);
        }

        $statusMessages = [
            'draft' => 'Pendaftaran SPMB belum dibuka. Silakan kembali lagi nanti.',
            'almost_full' => null,
            'quota_full' => 'Kuota pendaftaran SPMB sudah penuh. Terima kasih atas minat Anda.',
            'closed' => 'Pendaftaran SPMB sudah ditutup. Terima kasih atas partisipasi Anda.',
            'announcement' => 'Pendaftaran SPMB sudah ditutup. Saat ini dalam masa pengumuman.',
            'archived' => 'Pendaftaran SPMB untuk tahun ajaran ini sudah tidak tersedia.',
        ];

        if (isset($statusMessages[$admissionYear->status]) && $statusMessages[$admissionYear->status] !== null) {
            return view('ppdb.closed', [
                'title' => 'Pendaftaran Ditutup',
                'message' => $statusMessages[$admissionYear->status],
            ]);
        }

        $now = now();
        if ($now->lt($admissionYear->start_date) || $now->gt($admissionYear->end_date)) {
            return view('ppdb.closed', [
                'title' => 'Pendaftaran Ditutup',
                'message' => 'Maaf, masa pendaftaran SPMB sudah berakhir atau belum dimulai.',
            ]);
        }

        $program = AdmissionProgram::where('admission_year_id', $admissionYear->id)
            ->where('status', 'open')
            ->orderBy('sort_order')
            ->first();

        if (!$program) {
            return view('ppdb.closed', [
                'title' => 'Program Belum Tersedia',
                'message' => 'Maaf, belum ada program pendaftaran yang tersedia saat ini.',
            ]);
        }

        if ($program->applications()->count() >= $program->quota) {
            return view('ppdb.closed', [
                'title' => 'Kuota Program Penuh',
                'message' => 'Maaf, kuota program pendaftaran sudah penuh.',
            ]);
        }

        return view('ppdb.create', [
            'title' => 'Form Pendaftaran SPMB',
        ]);
    }

    public function store(Request $request)
    {
        $admissionYear = AdmissionYear::where('is_current', true)
            ->whereIn('status', ['open', 'almost_full'])
            ->first();

        if (!$admissionYear) {
            return back()->with('error', 'Maaf, pendaftaran SPMB belum dibuka.');
        }

        $program = AdmissionProgram::where('admission_year_id', $admissionYear->id)
            ->where('status', 'open')
            ->orderBy('sort_order')
            ->first();

        if (!$program) {
            return back()->with('error', 'Maaf, belum ada program pendaftaran yang tersedia.');
        }

        if ($program->applications()->count() >= $program->quota) {
            return back()->with('error', 'Maaf, kuota program pendaftaran sudah penuh.');
        }

        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'gender' => 'required|in:laki_laki,perempuan',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'previous_school' => 'required|string|max:255',
            'address' => 'required|string',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'parent_whatsapp' => 'required|string|max:20',
            'parent_job' => 'nullable|string|max:255',
            'boarding_ready' => 'required|in:0,1',
            'quran_reading_ability' => 'required|in:belum_bisa,terbata_bata,lancar,baik',
            'health_notes' => 'nullable|string',
            'motivation' => 'required|string',
        ]);

        return DB::transaction(function () use ($validated, $admissionYear, $program) {
            $yearPrefix = explode('/', $admissionYear->academic_year)[0];
            $count = StudentApplication::where('admission_year_id', $admissionYear->id)->count();
            $regNumber = 'SPMB-' . $yearPrefix . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

            $application = StudentApplication::create([
                'registration_number' => $regNumber,
                'admission_year_id' => $admissionYear->id,
                'admission_program_id' => $program->id,
                'status' => 'baru_daftar',
                'student_name' => $validated['student_name'],
                'gender' => $validated['gender'],
                'birth_place' => $validated['birth_place'],
                'birth_date' => $validated['birth_date'],
                'previous_school' => $validated['previous_school'],
                'address' => $validated['address'],
                'father_name' => $validated['father_name'],
                'mother_name' => $validated['mother_name'],
                'parent_whatsapp' => $validated['parent_whatsapp'],
                'parent_job' => $validated['parent_job'],
                'boarding_ready' => (bool) $validated['boarding_ready'],
                'quran_reading_ability' => $validated['quran_reading_ability'],
                'health_notes' => $validated['health_notes'],
                'motivation' => $validated['motivation'],
                'submitted_at' => now(),
            ]);

            ApplicationStatusHistory::create([
                'student_application_id' => $application->id,
                'from_status' => null,
                'to_status' => 'baru_daftar',
                'changed_by' => null,
                'notes' => 'Pendaftaran dibuat melalui form SPMB online.',
            ]);

            return redirect()->route('ppdb.success', $application);
        });
    }

    public function success(StudentApplication $studentApplication)
    {
        $studentApplication->load(['admissionYear', 'admissionProgram']);

        return view('ppdb.success', [
            'title' => 'Pendaftaran Berhasil',
            'application' => $studentApplication,
        ]);
    }

    public function statusForm()
    {
        return view('ppdb.status-form', [
            'title' => 'Cek Status Pendaftaran',
        ]);
    }

    public function statusCheck(Request $request)
    {
        $validated = $request->validate([
            'registration_number' => 'nullable|string|max:50',
            'parent_whatsapp' => 'nullable|string|max:50',
        ]);

        $regNumber = $validated['registration_number'] ?? null;
        $waNumber = $validated['parent_whatsapp'] ?? null;

        if (blank($regNumber) && blank($waNumber)) {
            return back()
                ->withErrors(['search' => 'Harap isi nomor pendaftaran atau nomor WhatsApp orang tua.'])
                ->withInput();
        }

        $query = StudentApplication::query();

        if (!blank($regNumber)) {
            $query->where('registration_number', $regNumber);
        } else {
            $normalized = preg_replace('/[\s\+\-]/', '', $waNumber);
            $query->where('parent_whatsapp', 'LIKE', '%' . $normalized);
        }

        $application = $query->with(['admissionYear', 'admissionProgram'])->first();

        if (!$application) {
            return back()
                ->withErrors(['search' => 'Data pendaftaran tidak ditemukan. Pastikan nomor pendaftaran atau WhatsApp yang dimasukkan benar.'])
                ->withInput();
        }

        return view('ppdb.status-form', [
            'title' => 'Hasil Cek Status Pendaftaran',
            'application' => $application,
            'statusLabel' => $this->statusLabels[$application->status] ?? $application->status,
            'statusMessage' => $this->statusMessages[$application->status] ?? '',
        ]);
    }
}
