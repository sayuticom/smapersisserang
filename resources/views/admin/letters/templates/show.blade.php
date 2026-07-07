<x-admin-layout>
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $letterTemplate->title }}</h2>
                <p class="mt-1 text-sm text-gray-500">Detail template surat.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.letters.templates.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    &larr; Template
                </a>
                @if($letterTemplate->is_active)
                    <a href="{{ route('admin.letters.outgoings.create', ['template_id' => $letterTemplate->id]) }}" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                        Gunakan Template
                    </a>
                @endif
                <a href="{{ route('admin.letters.templates.edit', $letterTemplate) }}" class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                    Edit
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <dl class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
                <div>
                    <dt class="font-semibold text-slate-500">Jenis Surat</dt>
                    <dd class="mt-1 text-slate-900">{{ $letterTemplate->letterType?->code }} - {{ $letterTemplate->letterType?->name }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-500">Status</dt>
                    <dd class="mt-1">
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $letterTemplate->is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-1 ring-slate-200' }}">
                            {{ $letterTemplate->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="font-semibold text-slate-500">Template Perihal</dt>
                    <dd class="mt-1 whitespace-pre-line text-slate-900">{{ $letterTemplate->subject_template ?: '-' }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="font-semibold text-slate-500">Template Pembuka</dt>
                    <dd class="mt-1 rounded-lg bg-slate-50 p-4 text-slate-900 letter-content-display">{!! \App\Helpers\LetterHtmlSanitizer::render($letterTemplate->opening_template) ?: '-' !!}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="font-semibold text-slate-500">Template Isi Surat</dt>
                    <dd class="mt-1 rounded-lg bg-slate-50 p-4 text-slate-900 letter-content-display">{!! \App\Helpers\LetterHtmlSanitizer::render($letterTemplate->body_template) !!}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="font-semibold text-slate-500">Template Penutup</dt>
                    <dd class="mt-1 rounded-lg bg-slate-50 p-4 text-slate-900 letter-content-display">{!! \App\Helpers\LetterHtmlSanitizer::render($letterTemplate->closing_template) ?: '-' !!}</dd>
                </div>
            </dl>
        </div>
    </div>

    <style>
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
</x-admin-layout>
