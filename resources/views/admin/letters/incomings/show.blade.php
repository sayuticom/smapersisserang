<x-admin-layout>
    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Detail Surat Masuk</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $letterIncoming->incoming_number }}</p>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                <a href="{{ route('admin.letters.incomings.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    &larr; Daftar
                </a>
                <a href="{{ route('admin.letters.incomings.edit', $letterIncoming) }}"
                   class="inline-flex items-center justify-center rounded-lg border border-blue-200 px-4 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-50">
                    Edit
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Nomor Surat</div>
                    <div class="mt-1 font-semibold text-slate-900">{{ $letterIncoming->incoming_number }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Status</div>
                    <div class="mt-1 font-semibold text-slate-900">{{ $statuses[$letterIncoming->status] ?? $letterIncoming->status }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pengirim</div>
                    <div class="mt-1 font-semibold text-slate-900">{{ $letterIncoming->sender }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Jenis</div>
                    <div class="mt-1 font-semibold text-slate-900">{{ $letterIncoming->letterType?->name ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tanggal Surat</div>
                    <div class="mt-1 font-semibold text-slate-900">{{ $letterIncoming->letter_date?->format('d/m/Y') ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tanggal Diterima</div>
                    <div class="mt-1 font-semibold text-slate-900">{{ $letterIncoming->received_date?->format('d/m/Y') ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Lampiran</div>
                    <div class="mt-1 font-semibold text-slate-900">{{ $letterIncoming->attachment ?: '-' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">File Surat</div>
                    <div class="mt-1">
                        @if($letterIncoming->file_path)
                            <a href="{{ Storage::url($letterIncoming->file_path) }}" target="_blank" class="font-semibold text-emerald-700 hover:text-emerald-900">
                                Lihat file
                            </a>
                        @else
                            <span class="font-semibold text-slate-900">-</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-6 border-t border-slate-100 pt-5">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Perihal</div>
                <h3 class="mt-1 text-xl font-bold text-slate-900">{{ $letterIncoming->subject }}</h3>
            </div>

            <div class="mt-5">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Catatan</div>
                <p class="mt-1 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $letterIncoming->notes ?: '-' }}</p>
            </div>
        </div>
    </div>
</x-admin-layout>
