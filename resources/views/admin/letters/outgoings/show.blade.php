<x-admin-layout>
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Detail Surat Keluar</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $letterOutgoing->letter_number ?: 'Draft belum memiliki nomor surat' }}</p>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                <a href="{{ route('admin.letters.outgoings.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Kembali
                </a>
                @if(auth()->user()?->isAdmin())
                    <a href="{{ route('admin.letters.outgoings.edit', $letterOutgoing) }}"
                       @if($letterOutgoing->status === 'issued') onclick="return confirm('Surat ini sudah diterbitkan. Perubahan akan memengaruhi isi surat dan PDF. Lanjutkan mengedit?')" @endif
                       class="inline-flex items-center justify-center rounded-lg border border-blue-200 px-4 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-50">
                        Edit
                    </a>
                @endif
                @if($letterOutgoing->status !== 'issued')
                    @if($letterOutgoing->recipients->count() > 1)
                        <div class="flex flex-col gap-2">
                            <details class="group">
                                <summary class="cursor-pointer text-xs font-semibold text-amber-700 hover:text-amber-800">Preview Penerima Tertentu</summary>
                                <div class="mt-2 space-y-1">
                                    @foreach($letterOutgoing->recipients as $recipient)
                                        <a href="{{ route('admin.letters.outgoings.preview', [$letterOutgoing, 'recipient' => $recipient->id]) }}" target="_blank"
                                           class="block rounded-md bg-amber-50 px-3 py-1.5 text-xs font-medium text-amber-800 transition hover:bg-amber-100">
                                            {{ $recipient->recipient_name }}
                                        </a>
                                    @endforeach
                                </div>
                            </details>
                        </div>
                    @else
                        <a href="{{ route('admin.letters.outgoings.preview', $letterOutgoing) }}" target="_blank"
                           class="inline-flex items-center justify-center rounded-lg border border-amber-200 px-4 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-50">
                            Preview PDF
                        </a>
                    @endif
                    <form method="POST" action="{{ route('admin.letters.outgoings.issue', $letterOutgoing) }}" onsubmit="return confirm('Terbitkan surat ini dan buat nomor surat? Nomor, jenis, dan tanggal tidak dapat diubah setelah diterbitkan.');">
                        @csrf
                        <button type="submit"
                                class="inline-flex w-full items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                            Terbitkan Surat
                        </button>
                    </form>
                @else
                    @if($letterOutgoing->recipients->count() > 1)
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('admin.letters.outgoings.print-all', $letterOutgoing) }}"
                               class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                                Cetak Semua Penerima (ZIP)
                            </a>
                            <details class="group">
                                <summary class="cursor-pointer text-xs font-semibold text-emerald-700 hover:text-emerald-800">Cetak Penerima Tertentu</summary>
                                <div class="mt-2 space-y-1">
                                    @foreach($letterOutgoing->recipients as $recipient)
                                        <a href="{{ route('admin.letters.outgoings.print-recipient', [$letterOutgoing, $recipient]) }}" target="_blank"
                                           class="block rounded-md bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-800 transition hover:bg-emerald-100">
                                            {{ $recipient->recipient_name }}
                                        </a>
                                    @endforeach
                                </div>
                            </details>
                        </div>
                    @else
                        <a href="{{ route('admin.letters.outgoings.print', $letterOutgoing) }}" target="_blank"
                           class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                            Cetak / Download PDF
                        </a>
                    @endif
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-3">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Ringkasan Administrasi</h3>
            </div>
            <div class="grid grid-cols-2 gap-x-8 gap-y-2 px-6 py-4 text-sm md:grid-cols-3">
                <div>
                    <span class="font-medium text-slate-500">Status:</span>
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $letterOutgoing->status === 'issued' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                        {{ $letterOutgoing->status === 'issued' ? 'Terbit' : 'Draft' }}
                    </span>
                </div>
                <div>
                    <span class="font-medium text-slate-500">Nomor Surat:</span>
                    <span class="font-mono">{{ $letterOutgoing->letter_number ?: '-' }}</span>
                </div>
                <div>
                    <span class="font-medium text-slate-500">Jenis:</span>
                    {{ $letterOutgoing->letterType?->code ? $letterOutgoing->letterType->code . ' - ' . $letterOutgoing->letterType->name : '-' }}
                </div>
                <div>
                    <span class="font-medium text-slate-500">Kode Klasifikasi:</span>
                    {{ $letterOutgoing->letter_classification_code ?? '421.3' }}
                </div>
                <div>
                    <span class="font-medium text-slate-500">Kode Sekolah:</span>
                    {{ $letterOutgoing->letter_school_code ?? 'SMA-PERSIS-SRG' }}
                </div>
                <div>
                    <span class="font-medium text-slate-500">Diterbitkan:</span>
                    {{ $letterOutgoing->issued_at?->format('d/m/Y H:i') ?: '-' }}
                </div>
            </div>
        </div>

        <style>
            .document-content { line-height: 1.6; color: #1f2937; }
            .document-content .letter-meta td { padding: 1px 0; vertical-align: top; }
            .document-content .letter-meta .label { width: 80px; }
            .document-content .letter-meta .colon { width: 12px; text-align: center; }
            .document-content .recipient-block p { margin: 0 0 2px 0; }
            .document-content .signature-area { margin-top: 48px; }
            .document-content .signature-col { text-align: center; }
            .document-content .signature-col .signature-name { font-weight: 600; text-decoration: underline; }
            .document-content .signature-col .signature-title { margin-bottom: 2px; }
            .document-content .signature-col .signature-space { height: 80px; }
            .document-content figure.table { margin-left: 0; margin-right: auto; text-align: left; }
            .document-content table { border-collapse: collapse; margin: 8px 0; width: 100%; table-layout: fixed; }
            .document-content th, .document-content td { border: 1px solid #333; padding: 5px 7px; vertical-align: top; text-align: left; }
            .document-content .no-border-table table td,
            .document-content .no-border-table table th,
            .document-content table.no-border-table td,
            .document-content table.no-border-table th { border: none !important; padding: 2px 6px; }
            .document-content .letter-body > div > p { margin: 0 0 8px 0; }
            .document-content .letter-body ul,
            .document-content .letter-body ol { margin: 0.5rem 0; padding-left: 1.75rem; }
            .document-content .letter-body ol { list-style-type: decimal; }
            .document-content .letter-body ul { list-style-type: disc; }
            .document-content .letter-body li { margin: 0.2rem 0; }
            .document-content .letter-body ol ol { list-style-type: lower-alpha; }
            .document-content .letter-body ol ol ol { list-style-type: lower-roman; }
            .document-content .letter-body ul ul { list-style-type: circle; }
            .document-content .ql-indent-1 { padding-left: 32px; }
            .document-content .ql-indent-2 { padding-left: 64px; }
            .document-content .ql-indent-3 { padding-left: 96px; }
            .document-content .ql-indent-4 { padding-left: 128px; }
            .document-content .ql-indent-5 { padding-left: 160px; }
            .document-content .ql-indent-6 { padding-left: 192px; }
            .cc-section p,
            .cc-section div,
            .cc-section li { line-height: 1.15; margin: 0 0 1px 0; }
            .cc-section ol, .cc-section ul { margin: 1px 0 0 0; padding-left: 18px; }
            .cc-section li { margin-bottom: 0; }
        </style>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm document-content">
            <div class="mx-auto max-w-[850px] px-6 py-8 md:px-12 md:py-12">
                @include('admin.letters.outgoings.partials.document-content', [
                    'letter' => $letterOutgoing,
                    'mode' => 'web',
                    'schoolSetting' => $schoolSetting,
                ])
            </div>
        </div>
    </div>
</x-admin-layout>
