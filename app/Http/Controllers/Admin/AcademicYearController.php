<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAcademicYearRequest;
use App\Http\Requests\Admin\UpdateAcademicYearRequest;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public function index(): View
    {
        $years = AcademicYear::withCount('events')
            ->orderByDesc('start_date')
            ->get();

        return view('admin.academic.years.index', compact('years'));
    }

    public function create(): View
    {
        return view('admin.academic.years.create');
    }

    public function store(StoreAcademicYearRequest $request)
    {
        $data = $request->validated();
        $data['is_current'] = $request->boolean('is_current');

        DB::transaction(function () use ($data) {
            if ($data['is_current']) {
                AcademicYear::where('is_current', true)->update(['is_current' => false]);
            }
            AcademicYear::create($data);
        });

        return redirect()->route('admin.akademik.tahun-pelajaran.index')
            ->with('success', 'Tahun pelajaran berhasil ditambahkan.');
    }

    public function edit(AcademicYear $academicYear): View
    {
        return view('admin.academic.years.edit', compact('academicYear'));
    }

    public function update(UpdateAcademicYearRequest $request, AcademicYear $academicYear)
    {
        $data = $request->validated();
        $data['is_current'] = $request->boolean('is_current');

        DB::transaction(function () use ($data, $academicYear) {
            if ($data['is_current']) {
                AcademicYear::where('is_current', true)
                    ->where('id', '!=', $academicYear->id)
                    ->update(['is_current' => false]);
            }
            $academicYear->update($data);
        });

        return redirect()->route('admin.akademik.tahun-pelajaran.index')
            ->with('success', 'Tahun pelajaran berhasil diubah.');
    }

    public function setCurrent(AcademicYear $academicYear)
    {
        DB::transaction(function () use ($academicYear) {
            AcademicYear::where('is_current', true)->update(['is_current' => false]);
            $academicYear->update(['is_current' => true]);
        });

        return redirect()->route('admin.akademik.tahun-pelajaran.index')
            ->with('success', 'Tahun pelajaran ' . e($academicYear->name) . ' berhasil diaktifkan.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->events()->exists()) {
            return redirect()->route('admin.akademik.tahun-pelajaran.index')
                ->with('error', 'Tahun pelajaran tidak dapat dihapus karena masih memiliki kegiatan kalender.');
        }

        try {
            $academicYear->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.akademik.tahun-pelajaran.index')
                ->with('error', 'Tahun pelajaran tidak dapat dihapus karena masih terhubung dengan data lain.');
        }

        return redirect()->route('admin.akademik.tahun-pelajaran.index')
            ->with('success', 'Tahun pelajaran berhasil dihapus.');
    }
}
