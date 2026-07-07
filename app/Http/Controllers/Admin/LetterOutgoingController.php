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
        ]);

        return view('admin.letters.outgoings.show', compact('letterOutgoing'));
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

        return redirect()
            ->route('admin.letters.outgoings.show', $letterOutgoing)
            ->with('success', 'Draft surat keluar berhasil diperbarui.');
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

        return $this->streamPdf($letterOutgoing, false);
    }

    public function preview(LetterOutgoing $letterOutgoing)
    {
        return $this->streamPdf($letterOutgoing, $letterOutgoing->status !== 'issued');
    }

    private function streamPdf(LetterOutgoing $letterOutgoing, bool $isPreview)
    {
        $letterOutgoing->load([
            'letterType',
            'recipients',
            'signerOne',
            'signerTwo',
            'attachmentContent',
        ]);

        $schoolSetting = SchoolSetting::current();

        $pdf = Pdf::loadView('admin.letters.outgoings.pdf', [
            'letter' => $letterOutgoing,
            'schoolSetting' => $schoolSetting,
            'isPreview' => $isPreview,
            'logoSrc' => $this->storageImageDataUri($schoolSetting?->logo_path),
            'letterheadSrc' => $this->storageImageDataUri($schoolSetting?->letterhead_png),
            'signerOneSignatureSrc' => $this->storageImageDataUri($letterOutgoing->signerOne?->signature_path),
            'signerTwoSignatureSrc' => $this->storageImageDataUri($letterOutgoing->signerTwo?->signature_path),
        ])->setPaper('A4', 'portrait');

        $filenameSuffix = $isPreview
            ? 'preview-draft-' . $letterOutgoing->id
            : str_replace(['/', '\\'], '-', $letterOutgoing->letter_number);
        $filename = 'surat-keluar-' . $filenameSuffix . '.pdf';

        return $pdf->stream($filename);
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
