<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LessonSchedule;
use App\Models\LessonScheduleSetting;
use Illuminate\Http\Request;

class LessonScheduleSettingController extends Controller
{
    private array $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    public function index()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $settings = LessonScheduleSetting::orderBy('day')->orderBy('sort_order')->get()->groupBy('day');
        return view('admin.akademik.jam-pelajaran.index', compact('settings'));
    }

    public function create()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        return view('admin.akademik.jam-pelajaran.create')->with('days', $this->days);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'day' => ['required', 'in:' . implode(',', $this->days)],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'type' => ['required', 'in:pelajaran,istirahat,ishoma,upacara,pembiasaan,kegiatan_khusus'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        LessonScheduleSetting::create($validated);

        return redirect()->route('admin.akademik.jam-pelajaran.index')
            ->with('success', 'Jam pelajaran berhasil ditambahkan.');
    }

    public function edit(LessonScheduleSetting $lessonScheduleSetting)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        return view('admin.akademik.jam-pelajaran.edit', compact('lessonScheduleSetting'))->with('days', $this->days);
    }

    public function update(Request $request, LessonScheduleSetting $lessonScheduleSetting)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'day' => ['required', 'in:' . implode(',', $this->days)],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'type' => ['required', 'in:pelajaran,istirahat,ishoma,upacara,pembiasaan,kegiatan_khusus'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $lessonScheduleSetting->update($validated);

        return redirect()->route('admin.akademik.jam-pelajaran.index')
            ->with('success', 'Jam pelajaran berhasil diperbarui.');
    }

    public function toggle(LessonScheduleSetting $lessonScheduleSetting)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $lessonScheduleSetting->update(['is_active' => !$lessonScheduleSetting->is_active]);
        return redirect()->route('admin.akademik.jam-pelajaran.index')
            ->with('success', 'Status jam pelajaran berhasil diubah.');
    }

    public function destroy(LessonScheduleSetting $lessonScheduleSetting)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $scheduleCount = LessonSchedule::where('lesson_schedule_setting_id', $lessonScheduleSetting->id)->count();
        if ($scheduleCount > 0) {
            return redirect()->route('admin.akademik.jam-pelajaran.index')
                ->with('error', "Jam {$lessonScheduleSetting->name} tidak dapat dihapus karena masih digunakan oleh {$scheduleCount} jadwal pelajaran. Nonaktifkan jam saja.");
        }

        $lessonScheduleSetting->delete();
        return redirect()->route('admin.akademik.jam-pelajaran.index')
            ->with('success', 'Jam pelajaran berhasil dihapus.');
    }
}
