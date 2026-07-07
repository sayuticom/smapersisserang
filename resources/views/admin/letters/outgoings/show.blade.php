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
                @if($letterOutgoing->status !== 'issued')
                    <a href="{{ route('admin.letters.outgoings.edit', $letterOutgoing) }}"
                       class="inline-flex items-center justify-center rounded-lg border border-blue-200 px-4 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-50">
                        Edit Draft
                    </a>
                    <a href="{{ route('admin.letters.outgoings.preview', $letterOutgoing) }}" target="_blank"
                       class="inline-flex items-center justify-center rounded-lg border border-amber-200 px-4 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-50">
                        Preview PDF
                    </a>
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

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Status</div>
                    <div class="mt-1 font-semibold text-slate-900">{{ $letterOutgoing->status === 'issued' ? 'Terbit' : 'Draft' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Nomor Surat</div>
                    <div class="mt-1 font-mono text-sm font-semibold text-slate-900">{{ $letterOutgoing->letter_number ?: '-' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tanggal Surat</div>
                    <div class="mt-1 font-semibold text-slate-900">{{ $letterOutgoing->letter_date?->format('d/m/Y') ?? '-' }}</div>
                    @if($letterOutgoing->hijri_date)
                        <div class="text-xs text-slate-500 mt-0.5">{{ $letterOutgoing->hijri_date }}</div>
                    @endif
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Jenis</div>
                    <div class="mt-1 font-semibold text-slate-900">{{ $letterOutgoing->letterType?->code }} - {{ $letterOutgoing->letterType?->name }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Lampiran</div>
                    <div class="mt-1 font-semibold text-slate-900">{{ $letterOutgoing->attachment ?: '-' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Diterbitkan</div>
                    <div class="mt-1 font-semibold text-slate-900">{{ $letterOutgoing->issued_at?->format('d/m/Y H:i') ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Kode Klasifikasi</div>
                    <div class="mt-1 font-semibold text-slate-900">{{ $letterOutgoing->letter_classification_code ?: '421.3' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Kode Sekolah</div>
                    <div class="mt-1 font-semibold text-slate-900">{{ $letterOutgoing->letter_school_code ?: 'SMA-PERSIS-SRG' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Basmallah</div>
                    <div class="mt-1 font-semibold text-slate-900">{{ ($letterOutgoing->show_basmallah ?? true) ? 'Ditampilkan' : 'Tidak' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Doa Penutup</div>
                    <div class="mt-1 font-semibold text-slate-900">{{ ($letterOutgoing->show_closing_dua ?? true) ? 'Ditampilkan' : 'Tidak' }}</div>
                </div>
            </div>

            <div class="mt-6 border-t border-slate-100 pt-5">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Perihal</div>
                <h3 class="mt-1 text-xl font-bold text-slate-900">{{ $letterOutgoing->subject }}</h3>
            </div>

            <div class="mt-5 grid gap-5 lg:grid-cols-2">
                <div class="rounded-xl border border-slate-200 p-4">
                    <h4 class="font-semibold text-slate-900">Penerima</h4>
                    <div class="mt-3 space-y-3 text-sm">
                        @foreach($letterOutgoing->recipients as $recipient)
                            <div class="rounded-lg bg-slate-50 p-3">
                                <div class="font-semibold text-slate-900">{{ $recipient->recipient_name }}</div>
                                @if($recipient->recipient_address)
                                    <div class="mt-1 text-xs text-slate-500">{{ $recipient->recipient_address }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="rounded-xl border border-slate-200 p-4">
                    <h4 class="font-semibold text-slate-900">Penandatangan</h4>
                    <dl class="mt-3 space-y-3 text-sm">
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-500">Penandatangan 1</dt>
                            <dd class="mt-1 text-slate-900">{{ $letterOutgoing->signerOne?->name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-500">Penandatangan 2</dt>
                            <dd class="mt-1 text-slate-900">{{ $letterOutgoing->signerTwo?->name ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <style>
                .letter-text-block {
                    margin-bottom: 8px;
                    line-height: 1.55;
                }
                .letter-content-display p {
                    margin: 0 0 8px 0;
                    line-height: 1.55;
                    text-align: left;
                }
                .letter-content-display ul,
                .letter-content-display ol,
                .letter-content ul,
                .letter-content ol {
                    margin: 0.5rem 0;
                    padding-left: 1.75rem;
                }
                .letter-content-display ol,
                .letter-content ol {
                    list-style-type: decimal;
                }
                .letter-content-display ul,
                .letter-content ul {
                    list-style-type: disc;
                }
                .letter-content-display li,
                .letter-content li {
                    margin: 0.2rem 0;
                    line-height: 1.55;
                }
                .letter-content-display ol ol,
                .letter-content ol ol {
                    list-style-type: lower-alpha;
                }
                .letter-content-display ol ol ol,
                .letter-content ol ol ol {
                    list-style-type: lower-roman;
                }
                .letter-content-display ul ul,
                .letter-content ul ul {
                    list-style-type: circle;
                }
                .letter-content-display figure.table {
                    margin-left: 0 !important;
                    margin-right: auto !important;
                    text-align: left;
                }
                .letter-content-display figure.table table {
                    margin-left: 0 !important;
                    margin-right: auto !important;
                }
                .letter-content-display table {
                    border-collapse: collapse;
                    margin: 8px 0;
                    margin-left: 0 !important;
                    margin-right: auto !important;
                    width: 100%;
                }
                .letter-content-display th,
                .letter-content-display td {
                    border: 1px solid #333;
                    padding: 5px 7px;
                    vertical-align: top;
                    text-align: left;
                }
                .letter-content-display .no-border-table table,
                .letter-content-display table.no-border-table {
                    border-collapse: collapse;
                }
                .letter-content-display .no-border-table table td,
                .letter-content-display .no-border-table table th,
                .letter-content-display table.no-border-table td,
                .letter-content-display table.no-border-table th {
                    border: none !important;
                    padding: 2px 6px;
                }
                .letter-content-display .ql-indent-1 { padding-left: 32px; }
                .letter-content-display .ql-indent-2 { padding-left: 64px; }
                .letter-content-display .ql-indent-3 { padding-left: 96px; }
                .letter-content-display .ql-indent-4 { padding-left: 128px; }
                .letter-content-display .ql-indent-5 { padding-left: 160px; }
                .letter-content-display .ql-indent-6 { padding-left: 192px; }
            </style>
            <div class="mt-5 space-y-4 text-sm leading-relaxed text-slate-700 letter-content-display">
                @if($letterOutgoing->opening_paragraph)
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pembuka</div>
                        <div class="mt-1">{!! \App\Helpers\LetterHtmlSanitizer::render($letterOutgoing->opening_paragraph) !!}</div>
                    </div>
                @endif
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Isi Surat</div>
                    <div class="mt-1">{!! \App\Helpers\LetterHtmlSanitizer::render($letterOutgoing->body) !!}</div>
                </div>
                @if($letterOutgoing->closing_paragraph)
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Penutup</div>
                        <div class="mt-1">{!! \App\Helpers\LetterHtmlSanitizer::render($letterOutgoing->closing_paragraph) !!}</div>
                    </div>
                @endif
                @if($letterOutgoing->cc)
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tembusan</div>
                        <div class="mt-1">{!! \App\Helpers\LetterHtmlSanitizer::render($letterOutgoing->cc) !!}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
