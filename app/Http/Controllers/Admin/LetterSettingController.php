<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LetterSettingController extends Controller
{
    public function edit(): View
    {
        $settings = SchoolSetting::current();

        return view('admin.letters.settings.edit', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $settings = SchoolSetting::current();

        if (!$settings) {
            $settings = SchoolSetting::query()->create(['is_active' => true]);
        }

        $data = $request->validate([
            'default_letter_classification_code' => ['nullable', 'string', 'max:30'],
            'default_letter_school_code' => ['nullable', 'string', 'max:50'],
            'default_letter_show_basmallah' => ['nullable', 'boolean'],
            'default_letter_basmallah_text' => ['nullable', 'string', 'max:500'],
            'default_letter_show_closing_dua' => ['nullable', 'boolean'],
            'default_letter_closing_dua_text' => ['nullable', 'string', 'max:500'],
            'default_letter_pdf_font_size' => ['nullable', 'integer', 'in:9,10,11,12'],
            'basmallah_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:1024'],
            'closing_dua_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:1024'],
            'delete_basmallah_image' => ['nullable', 'boolean'],
            'delete_closing_dua_image' => ['nullable', 'boolean'],
        ]);

        $data['default_letter_show_basmallah'] = $request->boolean('default_letter_show_basmallah');
        $data['default_letter_show_closing_dua'] = $request->boolean('default_letter_show_closing_dua');

        if ($request->boolean('delete_basmallah_image')) {
            if ($settings->basmallah_image_path) {
                Storage::disk('public')->delete($settings->basmallah_image_path);
            }
            $data['basmallah_image_path'] = null;
        }

        if ($request->boolean('delete_closing_dua_image')) {
            if ($settings->closing_dua_image_path) {
                Storage::disk('public')->delete($settings->closing_dua_image_path);
            }
            $data['closing_dua_image_path'] = null;
        }

        if ($request->hasFile('basmallah_image')) {
            if ($settings->basmallah_image_path) {
                Storage::disk('public')->delete($settings->basmallah_image_path);
            }
            $data['basmallah_image_path'] = $request->file('basmallah_image')
                ->store('letters/islamic', 'public');
        }

        if ($request->hasFile('closing_dua_image')) {
            if ($settings->closing_dua_image_path) {
                Storage::disk('public')->delete($settings->closing_dua_image_path);
            }
            $data['closing_dua_image_path'] = $request->file('closing_dua_image')
                ->store('letters/islamic', 'public');
        }

        unset(
            $data['basmallah_image'],
            $data['closing_dua_image'],
            $data['delete_basmallah_image'],
            $data['delete_closing_dua_image'],
        );

        $settings->update($data);

        return redirect()
            ->route('admin.letters.settings.edit')
            ->with('success', 'Pengaturan surat berhasil disimpan.');
    }
}
