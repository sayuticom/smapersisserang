@php
    \Carbon\Carbon::setLocale('id');
    $arabicPdf = app(\App\Services\Letters\ArabicPdfTextService::class);
    $city = $schoolSetting?->city ?? 'Serang';

    $hasSignerTwo = ! is_null($letter->signerTwo);
    $leftSigner = $hasSignerTwo ? $letter->signerOne : null;
    $rightSigner = $hasSignerTwo ? $letter->signerTwo : $letter->signerOne;
@endphp

<style>
    .basmallah-image,
    .closing-dua-image {
        display: block;
        width: auto;
        max-width: 420px;
        max-height: 58px;
        margin: 14px auto;
        object-fit: contain;
    }

    @media (max-width: 640px) {
        .basmallah-image,
        .closing-dua-image {
            max-width: 85%;
            max-height: 48px;
        }
    }

    .arabic-center-table {
        width: auto !important;
        margin: 14px auto !important;
        border: none !important;
        border-collapse: collapse;
    }
    .arabic-center-table td {
        border: none !important;
        text-align: center !important;
        padding: 0;
    }

    .signature-image {
        max-width: 180px;
        max-height: 100px;
        width: auto;
        height: auto;
        object-fit: contain;
    }

    .letter-body table td,
    .letter-body table th,
    .attachment-content table td,
    .attachment-content table th {
        vertical-align: top !important;
    }

    .letter-meta,
    .letter-meta td,
    .letter-meta th {
        border: none !important;
        border-collapse: collapse;
        padding: 2px 0;
    }
    .letter-meta .label {
        width: 80px;
    }
    .letter-meta .colon {
        width: 12px;
        text-align: center;
    }
</style>

@if($mode === 'pdf')
    @if($isPreview ?? false)
        <div class="draft-watermark">PRATINJAU - BELUM RESMI</div>
    @endif

    @if($letter->use_letterhead)
        @if($letterheadSrc ?? false)
            <img class="letterhead-image" src="{{ $letterheadSrc }}" alt="Kop Surat">
        @else
            <div class="generated-letterhead">
                <table>
                    <tr>
                        <td style="width: 84px;">
                            @if($logoSrc ?? false)
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
@endif

<div class="mb-8 flex justify-end">
    <div class="text-right">
        @if($letter->hijri_date)
            <div>{{ $city }}, {{ $letter->hijri_date }}</div>
        @else
            <div>{{ $city }},</div>
        @endif
        <div>{{ $letter->letter_date?->translatedFormat('d F Y') }}</div>
    </div>
</div>

<table class="letter-meta mb-8">
    <tr>
        <td class="label align-top">Nomor</td>
        <td class="colon align-top">:</td>
        <td>{{ $letter->letter_number ?: 'DRAFT' }}</td>
    </tr>
    <tr>
        <td class="align-top">Lampiran</td>
        <td class="colon align-top">:</td>
        <td>{{ $letter->attachment ?: '-' }}</td>
    </tr>
    <tr>
        <td class="align-top">Perihal</td>
        <td class="colon align-top">:</td>
        <td><strong>{{ $letter->subject }}</strong></td>
    </tr>
</table>

<div class="recipient-block mb-8">
    <p><strong>Kepada Yth.</strong></p>
    @if($mode === 'pdf' && isset($singleRecipient))
        <p class="font-semibold">{{ $singleRecipient->recipient_name }}</p>
        @if($singleRecipient->recipient_address)
            <p>{{ $singleRecipient->recipient_address }}</p>
        @endif
    @else
        @foreach($letter->recipients as $recipient)
            <p class="font-semibold">{{ $recipient->recipient_name }}</p>
            @if($recipient->recipient_address)
                <p>{{ $recipient->recipient_address }}</p>
            @endif
        @endforeach
    @endif
    @if(!$letter->recipients->pluck('recipient_address')->filter()->count())
        <p>di Tempat</p>
    @endif
</div>

@php
    $showBasmallah = $letter->show_basmallah ?? $schoolSetting?->default_letter_show_basmallah ?? true;
    $basmallahText = $letter->basmallah_text ?? $schoolSetting?->default_letter_basmallah_text;
@endphp
@if($showBasmallah)
    <div class="mb-4">
        {!! $arabicPdf->renderBasmallah($basmallahText) !!}
    </div>
@endif

<div class="letter-body">
    @if($letter->opening_paragraph)
        <div>{!! \App\Helpers\LetterHtmlSanitizer::render($letter->opening_paragraph) !!}</div>
    @endif

    <div>{!! $mode === 'pdf'
        ? \App\Helpers\LetterHtmlSanitizer::normalizeAttachmentTablesForPdf(\App\Helpers\LetterHtmlSanitizer::render($letter->body))
        : \App\Helpers\LetterHtmlSanitizer::render($letter->body)
    !!}</div>

    @if($letter->closing_paragraph)
        <div>{!! \App\Helpers\LetterHtmlSanitizer::render($letter->closing_paragraph) !!}</div>
    @endif

    @php
        $showClosingDua = $letter->show_closing_dua ?? $schoolSetting?->default_letter_show_closing_dua ?? true;
        $closingDuaText = $letter->closing_dua_text ?? $schoolSetting?->default_letter_closing_dua_text;
    @endphp
    @if($showClosingDua)
        <div class="mt-4">
            @if($closingDuaText)
                {!! $arabicPdf->renderClosingDua($closingDuaText) !!}
            @else
                <div class="closing-dua-image">Wassalamu&rsquo;alaikum warahmatullahi wabarakatuh</div>
            @endif
        </div>
    @endif
</div>

<div class="signature-area flex justify-end gap-8">
    @if($leftSigner)
        <div class="signature-col">
            <div class="signature-title">{{ $leftSigner->position ?: 'Penandatangan' }}</div>
            @if($mode === 'pdf' && isset($signerOneSignatureSrc) && $signerOneSignatureSrc)
                <img class="signature-image" src="{{ $signerOneSignatureSrc }}" alt="Tanda tangan">
            @elseif($mode === 'web' && $leftSigner->signature_path)
                @php
                    $sigUrl = \Illuminate\Support\Facades\Storage::disk('public')->exists($leftSigner->signature_path)
                        ? \Illuminate\Support\Facades\Storage::disk('public')->url($leftSigner->signature_path)
                        : null;
                @endphp
                @if($sigUrl)
                    <img class="signature-image" src="{{ $sigUrl }}" alt="Tanda tangan">
                @else
                    <div class="signature-space"></div>
                @endif
            @else
                <div class="signature-space"></div>
            @endif
            <div class="signature-name">{{ $leftSigner->name }}</div>
            @if($leftSigner->identity_number)
                <div>{{ $leftSigner->identity_number }}</div>
            @endif
        </div>
    @endif
    <div class="signature-col">
        <div class="signature-title">{{ $rightSigner->position ?: 'Penandatangan' }}</div>
        @if($mode === 'pdf' && isset($signerTwoSignatureSrc) && $signerTwoSignatureSrc)
            <img class="signature-image" src="{{ $signerTwoSignatureSrc }}" alt="Tanda tangan">
        @elseif($mode === 'web' && $rightSigner->signature_path)
            @php
                $sigUrl = \Illuminate\Support\Facades\Storage::disk('public')->exists($rightSigner->signature_path)
                    ? \Illuminate\Support\Facades\Storage::disk('public')->url($rightSigner->signature_path)
                    : null;
            @endphp
            @if($sigUrl)
                <img class="signature-image" src="{{ $sigUrl }}" alt="Tanda tangan">
            @else
                <div class="signature-space"></div>
            @endif
        @else
            <div class="signature-space"></div>
        @endif
        <div class="signature-name">{{ $rightSigner->name }}</div>
        @if($rightSigner->identity_number)
            <div>{{ $rightSigner->identity_number }}</div>
        @endif
    </div>
</div>

@if($letter->cc)
    <div class="cc-section mt-8">
        <strong>Tembusan:</strong>
        <div>{!! \App\Helpers\LetterHtmlSanitizer::render($letter->cc) !!}</div>
    </div>
@endif

@if($letter->relationLoaded('attachmentContent') && $letter->attachmentContent?->content)
    @if($mode === 'pdf')
        <div style="page-break-before: always;"></div>
    @else
        <hr class="my-8 border-dashed border-slate-300">
    @endif
    <div class="attachment-page">
        <h3 class="mb-4 text-center text-lg font-bold underline">LAMPIRAN</h3>
        <div class="attachment-content">
            {!! $mode === 'pdf'
                ? \App\Helpers\LetterHtmlSanitizer::normalizeAttachmentTablesForPdf(\App\Helpers\LetterHtmlSanitizer::render($letter->attachmentContent->content))
                : \App\Helpers\LetterHtmlSanitizer::render($letter->attachmentContent->content)
            !!}
        </div>
    </div>
@endif
