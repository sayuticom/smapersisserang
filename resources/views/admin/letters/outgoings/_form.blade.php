@php
    $template = $template ?? null;
    $defaultSettings = $defaultSettings ?? null;
    $selectedType = old('letter_type_id', $letterOutgoing?->letter_type_id ?? $template?->letter_type_id);
    $selectedSignerOne = old('signer_1_id', $letterOutgoing?->signer_1_id);
    $selectedSignerTwo = old('signer_2_id', $letterOutgoing?->signer_2_id);
@endphp

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label for="letter_type_id" class="block text-sm font-semibold text-gray-700">Jenis Surat <span class="text-red-500">*</span></label>
        <select name="letter_type_id" id="letter_type_id" class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
            <option value="">Pilih jenis surat</option>
            @foreach($letterTypes as $type)
                <option value="{{ $type->id }}" {{ (string) $selectedType === (string) $type->id ? 'selected' : '' }}>
                    {{ $type->code }} - {{ $type->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="letter_date" class="block text-sm font-semibold text-gray-700">Tanggal Surat <span class="text-red-500">*</span></label>
        <input type="date" name="letter_date" id="letter_date" value="{{ old('letter_date', $letterOutgoing?->letter_date?->format('Y-m-d') ?? now()->toDateString()) }}"
               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
        <div id="hijriPreview" class="mt-1.5 text-xs text-emerald-700"></div>
        <p class="mt-1 text-[11px] text-slate-400 italic">* Tanggal hijriyah merupakan hasil konversi otomatis dan dapat berbeda 1 hari berdasarkan metode kalender hijriyah.</p>
    </div>
    <div class="md:col-span-2">
        <label for="subject" class="block text-sm font-semibold text-gray-700">Perihal <span class="text-red-500">*</span></label>
        <input type="text" name="subject" id="subject" value="{{ old('subject', $letterOutgoing?->subject ?? $template?->subject_template) }}"
               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
    </div>
    <div>
        <label for="attachment" class="block text-sm font-semibold text-gray-700">Lampiran</label>
        <input type="text" name="attachment" id="attachment" value="{{ old('attachment', $letterOutgoing?->attachment) }}"
               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Contoh: 1 berkas">
    </div>
</div>

<div class="border-t border-slate-100 pt-5">
    <h3 class="mb-3 font-semibold text-slate-900">Pengaturan Surat</h3>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="letter_classification_code" class="block text-sm font-semibold text-gray-700">Kode Klasifikasi Surat</label>
            <input type="text" name="letter_classification_code" id="letter_classification_code"
                   value="{{ old('letter_classification_code', $letterOutgoing?->letter_classification_code ?? $defaultSettings?->default_letter_classification_code ?? '421.3') }}"
                   placeholder="421.3"
                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <div>
            <label for="letter_school_code" class="block text-sm font-semibold text-gray-700">Kode Sekolah</label>
            <input type="text" name="letter_school_code" id="letter_school_code"
                   value="{{ old('letter_school_code', $letterOutgoing?->letter_school_code ?? $defaultSettings?->default_letter_school_code ?? 'SMA-PERSIS-SRG') }}"
                   placeholder="SMA-PERSIS-SRG"
                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <div>
            <label for="pdf_font_size" class="block text-sm font-semibold text-gray-700">Ukuran Huruf PDF</label>
            <select name="pdf_font_size" id="pdf_font_size" class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="9" {{ (string) old('pdf_font_size', $letterOutgoing?->pdf_font_size ?? $defaultSettings?->default_letter_pdf_font_size ?? 11) === '9' ? 'selected' : '' }}>9 &ndash; Ekstra Kecil</option>
                <option value="10" {{ (string) old('pdf_font_size', $letterOutgoing?->pdf_font_size ?? $defaultSettings?->default_letter_pdf_font_size ?? 11) === '10' ? 'selected' : '' }}>10 &ndash; Kecil</option>
                <option value="11" {{ (string) old('pdf_font_size', $letterOutgoing?->pdf_font_size ?? $defaultSettings?->default_letter_pdf_font_size ?? 11) === '11' ? 'selected' : '' }}>11 &ndash; Normal</option>
                <option value="12" {{ (string) old('pdf_font_size', $letterOutgoing?->pdf_font_size ?? $defaultSettings?->default_letter_pdf_font_size ?? 11) === '12' ? 'selected' : '' }}>12 &ndash; Besar</option>
            </select>
        </div>
    </div>
</div>

<div class="border-t border-slate-100 pt-5">
    <div class="mb-3 flex items-center justify-between gap-3">
        <div>
            <h3 class="font-semibold text-slate-900">Penerima</h3>
            <p class="text-xs text-slate-500">Minimal satu penerima surat.</p>
        </div>
        <button type="button" id="addRecipientBtn" class="rounded-lg border border-emerald-200 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-50">
            Tambah Penerima
        </button>
    </div>

    <div id="recipientRows" class="space-y-3">
        @foreach($recipientRows as $index => $recipient)
            <div class="recipient-row rounded-xl border border-slate-200 bg-slate-50 p-4" data-index="{{ $index }}">
                <div class="mb-3 flex items-center justify-between">
                    <div class="text-sm font-semibold text-slate-700">Penerima <span class="recipient-number">{{ $index + 1 }}</span></div>
                    <button type="button" class="remove-recipient text-xs font-semibold text-red-600 hover:text-red-800">Hapus</button>
                </div>
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Nama Penerima <span class="text-red-500">*</span></label>
                        <textarea name="recipients[{{ $index }}][recipient_name]" rows="2" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Wali Murid" required>{{ is_array($recipient) ? ($recipient['recipient_name'] ?? '') : $recipient->recipient_name }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Alamat / Tempat</label>
                        <textarea name="recipients[{{ $index }}][recipient_address]" rows="2" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="di Tempat">{{ is_array($recipient) ? ($recipient['recipient_address'] ?? '') : $recipient->recipient_address }}</textarea>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="grid grid-cols-1 gap-4">
    <div>
        <x-letter-editor
            name="opening_paragraph"
            label="Pembuka"
            :value="$letterOutgoing?->opening_paragraph ?? $template?->opening_template ?? ''"
            :rows="3"
        />
    </div>
    <div>
        <x-letter-editor
            name="body"
            label="Isi Surat"
            :value="$letterOutgoing?->body ?? $template?->body_template ?? ''"
            :rows="10"
            :required="true"
        />
    </div>
    <div>
        <x-letter-editor
            name="closing_paragraph"
            label="Penutup"
            :value="$letterOutgoing?->closing_paragraph ?? $template?->closing_template ?? ''"
            :rows="3"
        />
    </div>
    <div>
        <x-letter-editor
            name="cc"
            label="Tembusan"
            :value="$letterOutgoing?->cc ?? ''"
            :rows="3"
        />
    </div>
</div>

<div class="border-t border-slate-100 pt-5">
    <h3 class="mb-3 font-semibold text-slate-900">Penandatangan</h3>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="signer_1_id" class="block text-sm font-semibold text-gray-700">Penandatangan 1</label>
            <select name="signer_1_id" id="signer_1_id" class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">Belum dipilih</option>
                @foreach($letterSigners as $signer)
                    <option value="{{ $signer->id }}" {{ (string) $selectedSignerOne === (string) $signer->id ? 'selected' : '' }}>
                        {{ $signer->name }}{{ $signer->position ? ' - ' . $signer->position : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="signer_2_id" class="block text-sm font-semibold text-gray-700">Penandatangan 2</label>
            <select name="signer_2_id" id="signer_2_id" class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">Belum dipilih</option>
                @foreach($letterSigners as $signer)
                    <option value="{{ $signer->id }}" {{ (string) $selectedSignerTwo === (string) $signer->id ? 'selected' : '' }}>
                        {{ $signer->name }}{{ $signer->position ? ' - ' . $signer->position : '' }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
    <a href="{{ $letterOutgoing ? route('admin.letters.outgoings.show', $letterOutgoing) : route('admin.letters.outgoings.index') }}"
       class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
        Batal
    </a>
    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
        Simpan Draft
    </button>
</div>

<template id="recipientTemplate">
    <div class="recipient-row rounded-xl border border-slate-200 bg-slate-50 p-4" data-index="__INDEX__">
        <div class="mb-3 flex items-center justify-between">
            <div class="text-sm font-semibold text-slate-700">Penerima <span class="recipient-number">__NUMBER__</span></div>
            <button type="button" class="remove-recipient text-xs font-semibold text-red-600 hover:text-red-800">Hapus</button>
        </div>
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div>
                <label class="block text-xs font-semibold text-gray-600">Nama Penerima <span class="text-red-500">*</span></label>
                <textarea name="recipients[__INDEX__][recipient_name]" rows="2" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Wali Murid" required></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600">Alamat / Tempat</label>
                <textarea name="recipients[__INDEX__][recipient_address]" rows="2" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="di Tempat"></textarea>
            </div>
        </div>
    </div>
</template>

<script>
function gregorianToHijri(dateStr) {
    if (!dateStr) return '';
    const parts = dateStr.split('-');
    const gYear = parseInt(parts[0], 10);
    const gMonth = parseInt(parts[1], 10);
    const gDay = parseInt(parts[2], 10);

    let y = gYear, m = gMonth, d = gDay;
    if (m <= 2) { y--; m += 12; }
    const a = Math.floor(y / 100);
    const b = 2 - a + Math.floor(a / 4);
    const jd = Math.floor(365.25 * (y + 4716)) + Math.floor(30.6001 * (m + 1)) + d + b - 1524;

    const islamicEpoch = 1948440;
    const day = jd - islamicEpoch;
    const hijriYear = Math.floor((30 * day + 10646) / 10631);
    const yearStart = Math.floor((10631 * hijriYear - 10631) / 30);
    let remaining = day - yearStart;

    const isLeap = (hijriYear * 11 + 14) % 30 < 11;
    const monthLengths = [30, 29, 30, 29, 30, 29, 30, 29, 30, 29, 30, isLeap ? 30 : 29];

    let hijriMonth = 0;
    while (hijriMonth < 12 && remaining > monthLengths[hijriMonth]) {
        remaining -= monthLengths[hijriMonth];
        hijriMonth++;
    }
    hijriMonth = Math.min(hijriMonth, 11);

    const hijriDay = remaining + 1;
    const monthNames = [
        'Muharram', 'Safar', "Rabi'ul Awwal", "Rabi'ul Akhir",
        'Jumadil Awwal', 'Jumadil Akhir', 'Rajab', "Sya'ban",
        'Ramadhan', 'Syawwal', "Dzulqa'dah", 'Dzulhijjah'
    ];

    return hijriDay + ' ' + monthNames[hijriMonth] + ' ' + hijriYear + ' H';
}

document.addEventListener('DOMContentLoaded', function () {
    const dateInput = document.getElementById('letter_date');
    const hijriPreview = document.getElementById('hijriPreview');

    function updateHijriPreview() {
        const hijri = gregorianToHijri(dateInput.value);
        hijriPreview.textContent = hijri ? 'Hijriyah: ' + hijri : '';
    }

    if (dateInput) {
        dateInput.addEventListener('change', updateHijriPreview);
        dateInput.addEventListener('input', updateHijriPreview);
        updateHijriPreview();
    }

    const rows = document.getElementById('recipientRows');
    const addButton = document.getElementById('addRecipientBtn');
    const template = document.getElementById('recipientTemplate');

    function refreshRecipientRows() {
        rows.querySelectorAll('.recipient-row').forEach(function (row, index) {
            row.dataset.index = index;
            row.querySelector('.recipient-number').textContent = index + 1;
            row.querySelectorAll('[name]').forEach(function (input) {
                input.name = input.name.replace(/recipients\[\d+\]/, 'recipients[' + index + ']');
            });
        });

        const removeButtons = rows.querySelectorAll('.remove-recipient');
        removeButtons.forEach(function (button) {
            button.classList.toggle('hidden', removeButtons.length <= 1);
        });
    }

    addButton?.addEventListener('click', function () {
        const index = rows.querySelectorAll('.recipient-row').length;
        const html = template.innerHTML
            .replaceAll('__INDEX__', index)
            .replaceAll('__NUMBER__', index + 1);

        rows.insertAdjacentHTML('beforeend', html);
        refreshRecipientRows();
    });

    rows?.addEventListener('click', function (event) {
        if (!event.target.classList.contains('remove-recipient')) {
            return;
        }

        if (rows.querySelectorAll('.recipient-row').length <= 1) {
            return;
        }

        event.target.closest('.recipient-row').remove();
        refreshRecipientRows();
    });

    refreshRecipientRows();
});
</script>
