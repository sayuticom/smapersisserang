<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WaqfSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WaqfSettingController extends Controller
{
    public function edit()
    {
        $setting = WaqfSetting::activeSetting();

        if (!$setting) {
            $setting = WaqfSetting::create([
                'is_active' => true,
            ]);
        }

        return view('admin.wakaf.settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = WaqfSetting::activeSetting();

        if (!$setting) {
            $setting = WaqfSetting::create(['is_active' => true]);
        }

        $data = $request->validate([
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:1000',
            'hero_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'remove_hero_image' => 'nullable|boolean',
            'intro_title' => 'nullable|string|max:255',
            'intro_text' => 'nullable|string',
            'waqf_purpose_text' => 'nullable|string',
            'ikrar_text' => 'nullable|string',
            'qris_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'remove_qris_image' => 'nullable|boolean',
            'qris_payload' => ['nullable', 'string', 'max:5000'],
            'whatsapp_number' => 'nullable|string|max:30',
            'whatsapp_message_template' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->boolean('remove_hero_image') && $setting->hero_image) {
            Storage::delete($setting->hero_image);
            $data['hero_image'] = null;
        }

        if ($request->hasFile('hero_image')) {
            if ($setting->hero_image) {
                Storage::delete($setting->hero_image);
            }
            $data['hero_image'] = $request->file('hero_image')->store('waqf', 'public');
        }

        if ($request->boolean('remove_qris_image') && $setting->qris_image) {
            Storage::delete($setting->qris_image);
            $data['qris_image'] = null;
        }

        if ($request->hasFile('qris_image')) {
            if ($setting->qris_image) {
                Storage::delete($setting->qris_image);
            }
            $data['qris_image'] = $request->file('qris_image')->store('waqf', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $setting->update($data);

        return redirect()->route('admin.wakaf.settings.edit')
            ->with('success', 'Pengaturan Wakaf Uang berhasil diperbarui.');
    }
}
