<!DOCTYPE html>
<html lang="id">
@php
    \Carbon\Carbon::setLocale('id');

    $pdfFontSize = (int) ($letter->pdf_font_size ?? 11);
    $blockMargin = match($pdfFontSize) {
        9 => 5,
        10 => 6,
        12 => 8,
        default => 7,
    };

    $arabicPdf = app(\App\Services\Letters\ArabicPdfTextService::class);

    $cleanText = function (?string $value): string {
        $value = (string) $value;
        $value = str_replace("\t", ' ', $value);
        $value = preg_replace('/[ ]{2,}/', ' ', $value);
        $value = preg_replace("/(\r\n|\r|\n){3,}/", "\n\n", $value);
        return trim($value);
    };

    $hasSignerTwo = ! is_null($letter->signerTwo);

    $leftSigner = $hasSignerTwo ? $letter->signerOne : null;
    $leftSignatureSrc = $hasSignerTwo ? $signerOneSignatureSrc : null;
    $rightSigner = $hasSignerTwo ? $letter->signerTwo : $letter->signerOne;
    $rightSignatureSrc = $hasSignerTwo ? $signerTwoSignatureSrc : $signerOneSignatureSrc;

@endphp
<head>
    <meta charset="utf-8">
    <title>{{ $letter->letter_number ?: 'Preview Draft Surat' }}</title>
    <style>
        @page {
            margin: 32px 46px 42px 56px;
        }

        body {
            color: #111827;
            font-family: "DejaVu Serif", "Times New Roman", serif;
            font-size: {{ $pdfFontSize }}pt;
            line-height: 1.08;
            letter-spacing: normal;
            word-spacing: normal;
            white-space: normal;
        }

        .font-typewriter {
            font-family: "DejaVu Sans Mono", "Courier New", monospace;
        }

        .letterhead-image {
            width: 100%;
            max-height: 108px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        .generated-letterhead {
            width: 100%;
            border-bottom: 3px solid #047857;
            padding-bottom: 8px;
        }

        .generated-letterhead table {
            width: 100%;
            border-collapse: collapse;
        }

        .generated-letterhead img {
            width: 64px;
            height: 64px;
            object-fit: contain;
        }

        .school-name {
            color: #064e3b;
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            line-height: 1.15;
        }

        .school-meta {
            color: #374151;
            font-size: 8.5pt;
            text-align: center;
            line-height: 1.25;
        }

        .thin-line {
            border-bottom: 1px solid #047857;
            margin-top: 2px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
            margin-bottom: 4px;
        }

        .meta-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .label {
            width: 72px;
        }

        .colon {
            width: 10px;
        }

        .date-cell {
            text-align: right;
            white-space: nowrap;
        }

        .recipient {
            line-height: 1.08;
            margin-top: 18px;
            margin-bottom: 10px;
        }

        .recipient-body {
            margin-left: 0;
            line-height: 1.08;
        }

        .recipient-body div {
            margin: 0;
            line-height: 1.08;
        }

        .letter-content {
            margin-top: 14px;
            font-size: {{ $pdfFontSize }}pt;
            line-height: 1.08;
            text-align: left;
            word-spacing: normal;
            letter-spacing: normal;
            white-space: normal;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }

        .letter-content p {
            margin: 0 0 5px 0;
            line-height: 1.08;
            text-align: left;
        }

        .letter-content p + p {
            margin-top: 3px;
        }

        .letter-content div {
            line-height: 1.08;
        }

        .letter-content ul,
        .letter-content ol {
            margin-top: 2px;
            margin-bottom: 3px;
            padding-left: 18px;
            line-height: 1.08;
        }

        .letter-content ol {
            list-style-type: decimal;
        }

        .letter-content ul {
            list-style-type: disc;
        }

        .letter-content li {
            margin: 0 0 1px 0;
            line-height: 1.08;
        }

        .letter-content ol ol {
            list-style-type: lower-alpha;
        }

        .letter-content ol ol ol {
            list-style-type: lower-roman;
        }

        .letter-content ul ul {
            list-style-type: circle;
        }

        .letter-content figure.table {
            margin-left: 0;
            margin-right: auto;
            text-align: left;
        }

        .letter-content figure.table table {
            margin-left: 0;
            margin-right: auto;
        }

        .letter-content table {
            border-collapse: collapse;
            margin: 3px 0 5px 0;
            margin-left: 0;
            margin-right: auto;
            width: 100%;
        }

        .letter-content th,
        .letter-content td {
            border: 1px solid #333;
            padding: 1px 4px;
            line-height: 1.05;
            vertical-align: top;
            text-align: left;
        }

        .letter-content .no-border-table table td,
        .letter-content .no-border-table table th,
        .letter-content table.no-border-table td,
        .letter-content table.no-border-table th {
            border: none;
            padding: 1px 4px;
        }

        .letter-content .ql-indent-1 { padding-left: 24px; }
        .letter-content .ql-indent-2 { padding-left: 48px; }
        .letter-content .ql-indent-3 { padding-left: 72px; }
        .letter-content .ql-indent-4 { padding-left: 96px; }
        .letter-content .ql-indent-5 { padding-left: 120px; }
        .letter-content .ql-indent-6 { padding-left: 144px; }

        .letter-content br {
            line-height: 1.08;
        }

        .signature-row {
            width: 100%;
            margin-top: 18px;
            display: table;
            table-layout: fixed;
            page-break-inside: avoid;
        }

        .signature-col {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            line-height: 1.05;
        }

        .signature-title {
            margin: 0 0 2px 0;
            line-height: 1.05;
        }

        .signature-image {
            max-width: 248px;
            max-height: 128px;
            object-fit: contain;
            display: block;
            margin: 0 auto -8px auto;
        }

        .signature-space {
            height: 92px;
        }

        .signature-name {
            margin: 0;
            font-weight: bold;
            text-decoration: underline;
            line-height: 1.05;
        }

        .signature-identity {
            margin: 0 0 2px 0;
            line-height: 1.05;
        }

        .cc {
            margin-top: 16px;
            font-size: 8pt;
            page-break-inside: avoid;
        }

        .draft-watermark {
            position: fixed;
            top: 45%;
            left: 6%;
            width: 88%;
            text-align: center;
            color: #f59e0b;
            font-size: 46pt;
            font-weight: bold;
            opacity: 0.08;
            transform: rotate(-22deg);
            z-index: -1;
        }

        .arabic-center-table {
            width: 100%;
            border: none !important;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
        }

        .arabic-center-table td {
            border: none !important;
            text-align: center;
            padding: 0;
        }

        .basmallah-image {
            display: inline;
            max-width: 180px;
            max-height: 28px;
            height: auto;
        }

        .closing-dua-image {
            display: inline;
            max-width: 260px;
            max-height: 30px;
            height: auto;
        }

        .attachment-title {
            font-size: 13pt;
            font-weight: bold;
            text-align: center;
            text-decoration: underline;
            margin-bottom: 14px;
        }

        .attachment-content {
            font-size: {{ $pdfFontSize }}pt;
            line-height: 1.08;
            text-align: left;
        }

    </style>
</head>
<body>
@if($isPreview ?? false)
    <div class="draft-watermark">PRATINJAU - BELUM RESMI</div>
@endif

@if($letter->use_letterhead)
    @if($letterheadSrc)
        <img class="letterhead-image" src="{{ $letterheadSrc }}" alt="Kop Surat">
    @else
        <div class="generated-letterhead">
            <table>
                <tr>
                    <td style="width: 84px;">
                        @if($logoSrc)
                            <img src="{{ $logoSrc }}" alt="Logo">
                        @endif
                    </td>
                    <td>
                        <div class="school-name">
                            {{ $schoolSetting?->school_name ?? 'SMA PERSIS SERANG' }}
                        </div>
                        @if($schoolSetting?->short_name)
                            <div class="school-meta">{{ $schoolSetting->short_name }}</div>
                        @endif
                        <div class="school-meta">
                            {{ $schoolSetting?->address ?? '' }}
                            @if($schoolSetting?->city), {{ $schoolSetting->city }}@endif
                            @if($schoolSetting?->province), {{ $schoolSetting->province }}@endif
                        </div>
                        <div class="school-meta">
                            @if($schoolSetting?->whatsapp_number) WA: {{ $schoolSetting->whatsapp_number }} @endif
                            @if($schoolSetting?->email) | Email: {{ $schoolSetting->email }} @endif
                            @if($schoolSetting?->website_url) | {{ $schoolSetting->website_url }} @endif
                        </div>
                    </td>
                    <td style="width: 84px;"></td>
                </tr>
            </table>
        </div>
        <div class="thin-line"></div>
    @endif
@endif

<table class="meta-table">
    <tr>
        <td style="width: 62%;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td class="label">Nomor</td>
                    <td class="colon">:</td>
                    <td>{{ $letter->letter_number ?: 'DRAFT / BELUM DITERBITKAN' }}</td>
                </tr>
                <tr>
                    <td class="label">Lampiran</td>
                    <td class="colon">:</td>
                    <td>{{ $letter->attachment ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Perihal</td>
                    <td class="colon">:</td>
                    <td><strong>{{ $letter->subject }}</strong></td>
                </tr>
            </table>
        </td>
        <td class="date-cell">
            {{ $schoolSetting?->city ?: 'Serang' }},
            @if($letter->hijri_date)
                <br>{{ $letter->hijri_date }}
            @endif
            <br>{{ $letter->letter_date?->translatedFormat('d F Y') }}
        </td>
    </tr>
</table>

<div class="recipient">
    <strong>Kepada Yth.</strong>
    <div class="recipient-body">
        @php $recipients = $singleRecipient ? [$singleRecipient] : $letter->recipients; @endphp
        @foreach($recipients as $recipient)
            <div>
                {!! nl2br(e($cleanText($recipient->recipient_name))) !!}
                @if($recipient->recipient_address)
                    <br>{!! nl2br(e($cleanText($recipient->recipient_address))) !!}
                @endif
            </div>
        @endforeach
    </div>
</div>

<div class="letter-content">
    @if($schoolSetting?->default_letter_show_basmallah ?? true)
        {!! $arabicPdf->renderBasmallah($schoolSetting?->default_letter_basmallah_text) !!}
    @endif
    {!! \App\Helpers\LetterHtmlSanitizer::render($letter->opening_paragraph) !!}
    {!! \App\Helpers\LetterHtmlSanitizer::render($letter->body) !!}
    {!! \App\Helpers\LetterHtmlSanitizer::render($letter->closing_paragraph) !!}
    @if($schoolSetting?->default_letter_show_closing_dua ?? true)
        {!! $arabicPdf->renderClosingDua($schoolSetting?->default_letter_closing_dua_text) !!}
    @endif
</div>

<div class="signature-row">
    <div style="text-align: center; margin-bottom: 10px; line-height: 1.15;">
        {{ $schoolSetting?->city ?: 'Serang' }},
        @if($letter->hijri_date)
            <br>{{ $letter->hijri_date }}
        @endif
        <br>{{ $letter->letter_date?->translatedFormat('d F Y') }}
    </div>
    <div class="signature-col">
        @if($leftSigner)
            <div class="signature-title">{{ $leftSigner->position ?: 'Penandatangan' }}</div>
            @if($leftSignatureSrc)
                <img class="signature-image" src="{{ $leftSignatureSrc }}" alt="Tanda tangan">
            @else
                <div class="signature-space"></div>
            @endif
            <div class="signature-name">{{ $leftSigner->name }}</div>
            @if($leftSigner->identity_number)
                <div class="signature-identity">{{ $leftSigner->identity_number }}</div>
            @endif
        @endif
    </div>
    <div class="signature-col">
        @if($rightSigner)
            <div class="signature-title">{{ $rightSigner->position ?: 'Penandatangan' }}</div>
            @if($rightSignatureSrc)
                <img class="signature-image" src="{{ $rightSignatureSrc }}" alt="Tanda tangan">
            @else
                <div class="signature-space"></div>
            @endif
            <div class="signature-name">{{ $rightSigner->name }}</div>
            @if($rightSigner->identity_number)
                <div class="signature-identity">{{ $rightSigner->identity_number }}</div>
            @endif
        @endif
    </div>
</div>

@if($letter->cc)
    <div class="cc">
        <strong>Tembusan:</strong>
        <div>{!! \App\Helpers\LetterHtmlSanitizer::render($letter->cc) !!}</div>
    </div>
@endif

@if($letter->relationLoaded('attachmentContent') && $letter->attachmentContent?->content)
    <div style="page-break-before: always;"></div>
    <div class="attachment-page">
        <h3 class="attachment-title">LAMPIRAN</h3>
        <div class="attachment-content">
            {!! \App\Helpers\LetterHtmlSanitizer::render($letter->attachmentContent->content) !!}
        </div>
    </div>
@endif
</body>
</html>
