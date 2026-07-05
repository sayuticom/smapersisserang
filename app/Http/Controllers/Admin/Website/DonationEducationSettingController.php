<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\DonationEducationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DonationEducationSettingController extends Controller
{
    public function edit()
    {
        $setting = DonationEducationSetting::firstOrCreate(
            ['id' => 1],
            $this->defaultData()
        );

        $donationItemsText = collect($setting->donation_items ?? [])
            ->map(fn($item) => is_array($item) ? ($item['title'] ?? '') : $item)
            ->filter()
            ->implode(PHP_EOL);

        return view('admin.website.donasi-pendidikan.edit', compact('setting', 'donationItemsText'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'hero_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_hero_image' => 'nullable|boolean',
            'hadith_text' => 'nullable|string',
            'hadith_source' => 'nullable|string|max:255',
            'intro_title' => 'nullable|string|max:255',
            'intro_text' => 'nullable|string',
            'section_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_section_image' => 'nullable|boolean',
            'donation_qris_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'remove_donation_qris_image' => 'nullable|boolean',
            'donation_items_text' => 'nullable|string',
            'invitation_text' => 'nullable|string',
            'whatsapp_number' => 'nullable|string|max:50',
            'whatsapp_button_text' => 'nullable|string|max:255',
            'whatsapp_message' => 'nullable|string',
            'share_button_text' => 'nullable|string|max:255',
            'share_message' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $setting = DonationEducationSetting::firstOrCreate(['id' => 1], $this->defaultData());

        $data['donation_items'] = collect(preg_split('/\r\n|\r|\n/', $data['donation_items_text'] ?? ''))
            ->map(fn($line) => trim($line))
            ->filter()
            ->values()
            ->all();
        $data['whatsapp_number'] = preg_replace('/[^0-9]/', '', $data['whatsapp_number'] ?? '');
        $data['is_active'] = $request->boolean('is_active');

        $this->handleImageUpload($request, $data, $setting, 'hero_image');
        $this->handleImageUpload($request, $data, $setting, 'section_image');
        $this->handleImageUpload($request, $data, $setting, 'donation_qris_image');

        unset($data['donation_items_text'], $data['remove_hero_image'], $data['remove_section_image'], $data['remove_donation_qris_image']);

        $setting->update($data);

        return redirect()->route('admin.website.donasi-pendidikan.edit')
            ->with('success', 'Pengaturan Donasi Pendidikan berhasil disimpan.');
    }

    private function handleImageUpload(Request $request, array &$data, DonationEducationSetting $setting, string $field): void
    {
        $removeField = 'remove_' . $field;

        if ($request->boolean($removeField) && $setting->{$field}) {
            Storage::disk('public')->delete($setting->{$field});
            $data[$field] = null;
        }

        if ($request->hasFile($field)) {
            if ($setting->{$field}) {
                Storage::disk('public')->delete($setting->{$field});
            }

            $data[$field] = $request->file($field)->store('donasi-pendidikan', 'public');
        }
    }

    private function defaultData(): array
    {
        return [
            'hero_title' => 'Donasi Pendidikan & Makan Santri',
            'hero_subtitle' => 'Bersama mendukung pendidikan gratis dan kebutuhan makan santri SMA Persis Serang.',
            'hero_image' => null,
            'hadith_text' => 'Barangsiapa menempuh jalan untuk mencari ilmu, Allah akan mudahkan baginya jalan menuju surga.',
            'hadith_source' => 'HR. Muslim',
            'intro_title' => 'Menopang Pendidikan dan Kebutuhan Harian Santri',
            'intro_text' => 'SMA Persis Serang berikhtiar menghadirkan pendidikan yang terjangkau, bahkan menggratiskan biaya pendidikan dan biaya makan asrama bagi anak-anak yang membutuhkan. Program ini menjadi kesempatan bagi kaum muslimin untuk ikut menyuburkan ladang pahala melalui sedekah dan infak pendidikan.',
            'section_image' => null,
            'donation_items' => ['Beras', 'Telur', 'Sayuran', 'Lauk pauk', 'Sembako', 'Donasi uang', 'Kebutuhan dapur/asrama lainnya'],
            'invitation_text' => 'Yang memiliki beras, bisa menitipkan berasnya. Yang memiliki telur, bisa menitipkan telurnya. Yang memiliki sayuran, bisa menitipkan sayurannya. Apabila diperlukan, insyaAllah kami siap menjemput donasi ke tempat Bapak/Ibu/Saudara/i.',
            'whatsapp_number' => '6289661234569',
            'whatsapp_button_text' => 'Hubungi WA SMA Persis Serang',
            'whatsapp_message' => 'Assalamu\'alaikum, saya ingin berdonasi untuk program pendidikan dan makan santri SMA Persis Serang',
            'share_button_text' => 'Sebarkan Informasi Kebaikan Ini',
            'share_message' => 'Assalamu’alaikum. Mari ikut mendukung program pendidikan gratis dan makan santri SMA Persis Serang. Donasi bisa berupa beras, telur, sayur, sembako, atau uang. Hubungi WA 6289661234569.',
            'is_active' => true,
        ];
    }
}
