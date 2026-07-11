<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\LetterHtmlSanitizer;
use App\Http\Controllers\Controller;
use App\Models\LetterOutgoing;
use App\Models\LetterRecipient;
use App\Models\LetterSigner;
use App\Models\LetterType;
use App\Models\SchoolSetting;
use App\Services\Letters\HijriDateService;
use App\Services\Letters\LetterNumberService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LetterOutgoingController extends Controller
{
    public function index(): View
    {
        $letters = LetterOutgoing::query()
            ->with(['letterType', 'creator'])
            ->latest('letter_date')
            ->latest()
            ->paginate(20);

        return view('admin.letters.outgoings.index', compact('letters'));
    }

    public function create(Request $request): View
    {
        $template = null;
        if ($request->filled('template_id')) {
            $template = LetterTemplate::query()
                ->where('is_active', true)
                ->find($request->template_id);
        }

        return view('admin.letters.outgoings.create', array_merge(
            $this->formData(),
            [
                'template' => $template,
                'defaultSettings' => SchoolSetting::current(),
            ]
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);
        $settings = SchoolSetting::current();
        $validated['status'] = 'draft';
        $validated['created_by'] = auth()->id();
        $validated['show_basmallah'] = (bool) ($settings?->default_letter_show_basmallah ?? true);
        $validated['show_closing_dua'] = (bool) ($settings?->default_letter_show_closing_dua ?? true);
        $validated['basmallah_text'] = $settings?->default_letter_basmallah_text;
        $validated['closing_dua_text'] = $settings?->default_letter_closing_dua_text;
        $validated['letter_classification_code'] = $validated['letter_classification_code'] ?: '421.3';
        $validated['letter_school_code'] = $validated['letter_school_code'] ?: 'SMA-PERSIS-SRG';
        $this->applyDateParts($validated);

        $letter = DB::transaction(function () use ($validated) {
            $recipients = $validated['recipients'];
            unset($validated['recipients']);

            $letter = LetterOutgoing::query()->create($validated);
            $this->syncRecipients($letter, $recipients);

            return $letter;
        });

        if (!empty($validated['attachment']) && $request->filled('attachment_content')) {
            $letter->attachmentContent()->create([
                'content' => LetterHtmlSanitizer::sanitize($request->input('attachment_content')),
            ]);
        }

        return redirect()
            ->route('admin.letters.outgoings.show', $letter)
            ->with('success', 'Draft surat keluar berhasil dibuat.');
    }

    public function show(LetterOutgoing $letterOutgoing): View
    {
        $letterOutgoing->load([
            'letterType',
            'recipients',
            'signerOne',
            'signerTwo',
            'creator',
            'updater',
            'attachmentContent',
        ]);

        $schoolSetting = SchoolSetting::current();

        return view('admin.letters.outgoings.show', [
            'letterOutgoing' => $letterOutgoing,
            'schoolSetting' => $schoolSetting,
            'hasAttachment' => $letterOutgoing->relationLoaded('attachmentContent') && $letterOutgoing->attachmentContent?->content,
        ]);
    }

    public function edit(LetterOutgoing $letterOutgoing): View|RedirectResponse
    {
        if ($letterOutgoing->status === 'issued') {
            return redirect()
                ->route('admin.letters.outgoings.show', $letterOutgoing)
                ->with('error', 'Surat yang sudah diterbitkan tidak dapat diedit pada tahap ini.');
        }

        $letterOutgoing->load('recipients', 'attachmentContent');

        return view('admin.letters.outgoings.edit', array_merge(
            $this->formData(),
            [
                'letterOutgoing' => $letterOutgoing,
                'recipientRows' => $letterOutgoing->recipients->values(),
                'defaultSettings' => SchoolSetting::current(),
                'attachmentContent' => $letterOutgoing->attachmentContent,
            ]
        ));
    }

    public function update(Request $request, LetterOutgoing $letterOutgoing): RedirectResponse
    {
        if ($letterOutgoing->status === 'issued') {
            return redirect()
                ->route('admin.letters.outgoings.show', $letterOutgoing)
                ->with('error', 'Surat yang sudah diterbitkan tidak dapat diedit.');
        }

        $validated = $this->validatedData($request);
        $settings = SchoolSetting::current();
        $validated['updated_by'] = auth()->id();
        $validated['show_basmallah'] = (bool) ($settings?->default_letter_show_basmallah ?? true);
        $validated['show_closing_dua'] = (bool) ($settings?->default_letter_show_closing_dua ?? true);
        $validated['basmallah_text'] = $settings?->default_letter_basmallah_text;
        $validated['closing_dua_text'] = $settings?->default_letter_closing_dua_text;
        $this->applyDateParts($validated);

        DB::transaction(function () use ($letterOutgoing, $validated) {
            $recipients = $validated['recipients'];
            unset($validated['recipients']);

            $letterOutgoing->update($validated);
            $this->syncRecipients($letterOutgoing, $recipients);
        });

        $attachmentContent = $request->input('attachment_content');
        if (!empty($validated['attachment'])) {
            if ($attachmentContent) {
                $sanitized = LetterHtmlSanitizer::sanitize($attachmentContent);
                if ($letterOutgoing->attachmentContent) {
                    $letterOutgoing->attachmentContent()->update(['content' => $sanitized]);
                } else {
                    $letterOutgoing->attachmentContent()->create(['content' => $sanitized]);
                }
            }
        } else {
            if ($letterOutgoing->attachmentContent) {
                $letterOutgoing->attachmentContent()->delete();
            }
        }

        return redirect()
            ->route('admin.letters.outgoings.show', $letterOutgoing)
            ->with('success', 'Draft surat keluar berhasil diperbarui.');
    }

    public function issueGet(LetterOutgoing $letterOutgoing): RedirectResponse
    {
        return redirect()->route('admin.letters.outgoings.show', $letterOutgoing);
    }

    public function issue(LetterOutgoing $letterOutgoing, LetterNumberService $numberService): RedirectResponse
    {
        if ($letterOutgoing->status === 'issued') {
            return redirect()
                ->route('admin.letters.outgoings.show', $letterOutgoing)
                ->with('success', 'Surat sudah diterbitkan. Nomor surat tidak dibuat ulang.');
        }

        DB::transaction(function () use ($letterOutgoing, $numberService) {
            $letter = LetterOutgoing::query()
                ->whereKey($letterOutgoing->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($letter->status === 'issued') {
                return;
            }

            $number = $numberService->generate($letter->letter_type_id, $letter->letter_date, $letter->letter_classification_code, $letter->letter_school_code);

            $letter->update([
                'sequence_number' => $number['sequence_number'],
                'letter_number' => $number['letter_number'],
                'letter_month' => $number['month'],
                'letter_year' => $number['year'],
                'status' => 'issued',
                'issued_at' => now(),
                'updated_by' => auth()->id(),
            ]);
        });

        return redirect()
            ->route('admin.letters.outgoings.show', $letterOutgoing)
            ->with('success', 'Surat berhasil diterbitkan dan nomor surat sudah dibuat.');
    }

    public function updateAttachment(Request $request, LetterOutgoing $letterOutgoing): RedirectResponse
    {
        $data = $request->validate([
            'content' => ['nullable', 'string'],
        ]);

        if ($letterOutgoing->attachmentContent) {
            $letterOutgoing->attachmentContent->update($data);
        } else {
            $letterOutgoing->attachmentContent()->create($data);
        }

        return redirect()
            ->route('admin.letters.outgoings.edit', $letterOutgoing)
            ->with('success', 'Lampiran surat berhasil disimpan.');
    }

    public function print(LetterOutgoing $letterOutgoing)
    {
        if ($letterOutgoing->status !== 'issued') {
            return redirect()
                ->route('admin.letters.outgoings.show', $letterOutgoing)
                ->with('error', 'Draft belum dapat dicetak sebagai surat resmi. Terbitkan surat terlebih dahulu.');
        }

        $letterOutgoing->load('recipients');

        if ($letterOutgoing->recipients->isEmpty()) {
            return redirect()
                ->route('admin.letters.outgoings.show', $letterOutgoing)
                ->with('error', 'Surat tidak memiliki penerima.');
        }

        if ($letterOutgoing->recipients->count() > 1) {
            return redirect()
                ->route('admin.letters.outgoings.show', $letterOutgoing)
                ->with('error', 'Pilih penerima tertentu untuk dicetak.');
        }

        return $this->streamPdf($letterOutgoing, false, $letterOutgoing->recipients->first());
    }

    public function printRecipient(LetterOutgoing $letterOutgoing, LetterRecipient $recipient)
    {
        if ($letterOutgoing->status !== 'issued') {
            return redirect()
                ->route('admin.letters.outgoings.show', $letterOutgoing)
                ->with('error', 'Draft belum dapat dicetak sebagai surat resmi. Terbitkan surat terlebih dahulu.');
        }

        abort_if($recipient->letter_outgoing_id !== $letterOutgoing->id, 404);

        return $this->streamPdf($letterOutgoing, false, $recipient);
    }

    public function printAll(LetterOutgoing $letterOutgoing)
    {
        if ($letterOutgoing->status !== 'issued') {
            return redirect()
                ->route('admin.letters.outgoings.show', $letterOutgoing)
                ->with('error', 'Draft belum dapat dicetak sebagai surat resmi. Terbitkan surat terlebih dahulu.');
        }

        $letterOutgoing->load('recipients');

        if ($letterOutgoing->recipients->isEmpty()) {
            return redirect()
                ->route('admin.letters.outgoings.show', $letterOutgoing)
                ->with('error', 'Surat tidak memiliki penerima.');
        }

        $zipData = $this->createZip($letterOutgoing);

        $zipName = 'surat-keluar-' . str_replace(['/', '\\'], '-', $letterOutgoing->letter_number) . '.zip';

        return response()->streamDownload(function () use ($zipData) {
            echo $zipData;
        }, $zipName);
    }

    public function preview(LetterOutgoing $letterOutgoing)
    {
        $letterOutgoing->load('recipients');

        if ($letterOutgoing->recipients->isEmpty()) {
            return redirect()
                ->route('admin.letters.outgoings.show', $letterOutgoing)
                ->with('error', 'Surat tidak memiliki penerima.');
        }

        $recipientId = request('recipient');

        if ($letterOutgoing->recipients->count() > 1 && !$recipientId) {
            return redirect()
                ->route('admin.letters.outgoings.show', $letterOutgoing)
                ->with('error', 'Pilih penerima untuk preview surat.');
        }

        $singleRecipient = $recipientId
            ? $letterOutgoing->recipients->firstWhere('id', $recipientId)
            : $letterOutgoing->recipients->first();

        abort_unless($singleRecipient, 404);

        return $this->streamPdf($letterOutgoing, $letterOutgoing->status !== 'issued', $singleRecipient);
    }

    private function streamPdf(LetterOutgoing $letterOutgoing, bool $isPreview, ?LetterRecipient $singleRecipient = null)
    {
        $pdf = $this->buildPdf($letterOutgoing, $isPreview, $singleRecipient);

        $filenameSuffix = $isPreview
            ? 'preview-draft-' . $letterOutgoing->id
            : str_replace(['/', '\\'], '-', $letterOutgoing->letter_number);
        $filename = 'surat-keluar-' . $filenameSuffix . '.pdf';

        return $pdf->stream($filename);
    }

    private function buildPdf(LetterOutgoing $letterOutgoing, bool $isPreview, ?LetterRecipient $singleRecipient = null): \Barryvdh\DomPDF\PDF
    {
        $letterOutgoing->load([
            'letterType',
            'recipients',
            'signerOne',
            'signerTwo',
            'attachmentContent',
        ]);

        $schoolSetting = SchoolSetting::current();

        return Pdf::loadView('admin.letters.outgoings.pdf', [
            'letter' => $letterOutgoing,
            'schoolSetting' => $schoolSetting,
            'isPreview' => $isPreview,
            'singleRecipient' => $singleRecipient,
            'logoSrc' => $this->storageImageDataUri($schoolSetting?->logo_path),
            'letterheadSrc' => $this->storageImageDataUri($schoolSetting?->letterhead_png),
            'signerOneSignatureSrc' => $this->storageImageDataUri($letterOutgoing->signerOne?->signature_path),
            'signerTwoSignatureSrc' => $this->storageImageDataUri($letterOutgoing->signerTwo?->signature_path),
        ])->setPaper('A4', 'portrait');
    }

    private function formData(): array
    {
        return [
            'letterTypes' => LetterType::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            'letterSigners' => LetterSigner::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        ];
    }

    private function createZip(LetterOutgoing $letterOutgoing): string
    {
        $files = [];
        foreach ($letterOutgoing->recipients as $recipient) {
            $pdf = $this->buildPdf($letterOutgoing, false, $recipient);
            $safeName = preg_replace('/[^a-zA-Z0-9\s-]/', '', $recipient->recipient_name);
            $safeName = trim(str_replace(' ', '-', $safeName));
            $baseName = str_replace(['/', '\\'], '-', $letterOutgoing->letter_number);
            $files["surat-keluar-{$baseName}-{$safeName}.pdf"] = $pdf->output();
        }

        $zipData = '';
        $centralDir = '';
        $offset = 0;

        foreach ($files as $name => $content) {
            $crc = hash('crc32b', $content);
            $crc = hexdec($crc);
            $size = strlen($content);
            $nameLen = strlen($name);

            // Local file header
            $localHeader = pack('V', 0x04034b50); // signature
            $localHeader .= pack('v', 20); // version needed
            $localHeader .= pack('v', 0); // general purpose bit flag
            $localHeader .= pack('v', 0); // compression method (store)
            $localHeader .= pack('V', 0); // last mod file time
            $localHeader .= pack('V', 0); // last mod file date
            $localHeader .= pack('V', $crc); // crc-32
            $localHeader .= pack('V', $size); // compressed size
            $localHeader .= pack('V', $size); // uncompressed size
            $localHeader .= pack('v', $nameLen); // file name length
            $localHeader .= pack('v', 0); // extra field length
            $localHeader .= $name; // file name

            $zipData .= $localHeader . $content;

            // Central directory entry
            $entry = pack('V', 0x02014b50); // signature
            $entry .= pack('v', 20); // version made by
            $entry .= pack('v', 20); // version needed
            $entry .= pack('v', 0); // general purpose bit flag
            $entry .= pack('v', 0); // compression method
            $entry .= pack('V', 0); // last mod file time
            $entry .= pack('V', 0); // last mod file date
            $entry .= pack('V', $crc); // crc-32
            $entry .= pack('V', $size); // compressed size
            $entry .= pack('V', $size); // uncompressed size
            $entry .= pack('v', $nameLen); // file name length
            $entry .= pack('v', 0); // extra field length
            $entry .= pack('v', 0); // file comment length
            $entry .= pack('v', 0); // disk number start
            $entry .= pack('v', 0); // internal file attributes
            $entry .= pack('V', 0); // external file attributes
            $entry .= pack('V', $offset); // relative offset
            $entry .= $name; // file name

            $centralDir .= $entry;
            $offset += strlen($localHeader) + $size;
        }

        // End of central directory record
        $eocd = pack('V', 0x06054b50); // signature
        $eocd .= pack('v', 0); // number of this disk
        $eocd .= pack('v', 0); // disk where central directory starts
        $eocd .= pack('v', count($files)); // entries on this disk
        $eocd .= pack('v', count($files)); // total entries
        $eocd .= pack('V', strlen($centralDir)); // size of central directory
        $eocd .= pack('V', strlen($zipData)); // offset of central directory
        $eocd .= pack('v', 0); // comment length

        return $zipData . $centralDir . $eocd;
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'letter_type_id' => ['required', 'exists:letter_types,id'],
            'letter_date' => ['required', 'date'],
            'subject' => ['required', 'string', 'max:255'],
            'attachment' => ['nullable', 'string', 'max:255'],
            'opening_paragraph' => ['nullable', 'string'],
            'body' => ['required', 'string'],
            'closing_paragraph' => ['nullable', 'string'],
            'cc' => ['nullable', 'string'],
            'letter_classification_code' => ['nullable', 'string', 'max:30'],
            'letter_school_code' => ['nullable', 'string', 'max:50'],
            'pdf_font_size' => ['nullable', 'integer', 'in:9,10,11,12'],
            'signer_1_id' => ['nullable', 'exists:letter_signers,id'],
            'signer_2_id' => ['nullable', 'exists:letter_signers,id'],
            'recipients' => ['required', 'array', 'min:1'],
            'recipients.*.recipient_name' => ['required', 'string'],
            'recipients.*.recipient_institution' => ['nullable', 'string', 'max:255'],
            'recipients.*.recipient_address' => ['nullable', 'string'],
            'recipients.*.recipient_phone' => ['nullable', 'string', 'max:50'],
            'recipients.*.recipient_email' => ['nullable', 'email', 'max:255'],
            'hijri_date' => ['nullable', 'string', 'max:100'],
        ]);

        $data['opening_paragraph'] = LetterHtmlSanitizer::sanitize($data['opening_paragraph'] ?? null);
        $data['body'] = LetterHtmlSanitizer::sanitize($data['body']);
        $data['closing_paragraph'] = LetterHtmlSanitizer::sanitize($data['closing_paragraph'] ?? null);

        return $data;
    }

    private function applyDateParts(array &$data): void
    {
        $date = \Carbon\CarbonImmutable::parse($data['letter_date']);
        $data['letter_month'] = (int) $date->month;
        $data['letter_year'] = (int) $date->year;

        if (empty($data['hijri_date'])) {
            $data['hijri_date'] = app(HijriDateService::class)->convert($date);
        }
    }

    private function syncRecipients(LetterOutgoing $letter, array $recipients): void
    {
        $letter->recipients()->delete();

        foreach (array_values($recipients) as $index => $recipient) {
            $letter->recipients()->create([
                'recipient_name' => $recipient['recipient_name'],
                'recipient_institution' => $recipient['recipient_institution'] ?? null,
                'recipient_address' => $recipient['recipient_address'] ?? null,
                'recipient_phone' => $recipient['recipient_phone'] ?? null,
                'recipient_email' => $recipient['recipient_email'] ?? null,
                'sort_order' => $index + 1,
            ]);
        }
    }

    private function storageImageDataUri(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $normalizedPath = trim(str_replace('\\', '/', $path), '/');

        if (Storage::disk('public')->exists($normalizedPath)) {
            $contents = Storage::disk('public')->get($normalizedPath);
            $mime = Storage::disk('public')->mimeType($normalizedPath) ?: 'image/png';

            return 'data:' . $mime . ';base64,' . base64_encode($contents);
        }

        $publicPath = public_path($normalizedPath);

        if (is_file($publicPath)) {
            $mime = mime_content_type($publicPath) ?: 'image/png';

            return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($publicPath));
        }

        return null;
    }
}
