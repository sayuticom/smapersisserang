<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoardingCard;
use App\Models\BoardingPageSetting;
use App\Models\BoardingSchedule;
use App\Models\SchoolSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BoardingContentController extends Controller
{
    public function index()
    {
        $settings = BoardingPageSetting::first();
        $whyCards = BoardingCard::whyBoarding()->get();
        $focusCards = BoardingCard::focus()->get();
        $infoCards = BoardingCard::info()->get();
        $dailySchedules = BoardingSchedule::where('schedule_type', 'daily')->orderBy('sort_order')->get();
        $holidaySchedules = BoardingSchedule::where('schedule_type', 'holiday')->orderBy('sort_order')->get();

        return view('admin.website.boarding.index', compact('settings', 'whyCards', 'focusCards', 'infoCards', 'dailySchedules', 'holidaySchedules'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'section_label' => ['nullable', 'string', 'max:255'],
            'section_heading' => ['nullable', 'string', 'max:255'],
            'section_subtitle' => ['nullable', 'string'],
            'schedule_heading' => ['nullable', 'string', 'max:255'],
            'schedule_subtitle' => ['nullable', 'string'],
            'schedule_note' => ['nullable', 'string'],
            'focus_heading' => ['nullable', 'string', 'max:255'],
            'focus_subtitle' => ['nullable', 'string'],
            'why_heading' => ['nullable', 'string', 'max:255'],
            'why_subtitle' => ['nullable', 'string'],
        ]);

        BoardingPageSetting::updateOrCreate(
            ['id' => 1],
            $validated + ['is_active' => true]
        );

        return redirect()->route('admin.website.boarding.index')
            ->with('success', 'Pengaturan utama halaman boarding berhasil diperbarui.');
    }

    public function storeCard(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:why_boarding,focus'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        BoardingCard::create($validated + ['is_active' => true]);

        $typeLabel = $validated['type'] === 'why_boarding' ? 'Alasan Memilih Boarding' : 'Fokus Pembinaan';
        return redirect()->route('admin.website.boarding.index')
            ->with('success', "Kartu {$typeLabel} berhasil ditambahkan.");
    }

    public function updateCard(Request $request, BoardingCard $boardingCard): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $boardingCard->update($validated);

        return redirect()->route('admin.website.boarding.index')
            ->with('success', 'Kartu berhasil diperbarui.');
    }

    public function destroyCard(BoardingCard $boardingCard): RedirectResponse
    {
        $boardingCard->delete();

        return redirect()->route('admin.website.boarding.index')
            ->with('success', 'Kartu berhasil dihapus.');
    }

    public function storeSchedule(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'schedule_type' => ['required', 'string', 'in:daily,holiday'],
            'time' => ['required', 'string', 'max:30'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        BoardingSchedule::create($validated + ['is_active' => true]);

        $typeLabel = $validated['schedule_type'] === 'holiday' ? 'hari libur' : 'harian';
        return redirect()->route('admin.website.boarding.index')
            ->with('success', "Jadwal {$typeLabel} berhasil ditambahkan.");
    }

    public function updateSchedule(Request $request, BoardingSchedule $boardingSchedule): RedirectResponse
    {
        $validated = $request->validate([
            'time' => ['required', 'string', 'max:30'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $boardingSchedule->update($validated);

        return redirect()->route('admin.website.boarding.index')
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroySchedule(BoardingSchedule $boardingSchedule): RedirectResponse
    {
        $boardingSchedule->delete();

        return redirect()->route('admin.website.boarding.index')
            ->with('success', 'Jadwal harian berhasil dihapus.');
    }
}
