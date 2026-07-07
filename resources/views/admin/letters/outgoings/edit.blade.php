<x-admin-layout>
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Edit Draft Surat Keluar</h2>
                <p class="mt-1 text-sm text-gray-500">Perubahan hanya tersedia untuk status draft.</p>
            </div>
            <a href="{{ route('admin.letters.outgoings.show', $letterOutgoing) }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                &larr; Detail
            </a>
        </div>

        @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-semibold">Periksa kembali data berikut:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div x-data="{ tab: '{{ old("content") !== null ? "lampiran" : "data" }}' }">
            <div class="border-b border-slate-200">
                <nav class="-mb-px flex gap-6">
                    <button @click="tab = 'data'" :class="tab === 'data' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700'" class="border-b-2 px-1 py-3 text-sm font-semibold transition">
                        Data Surat
                    </button>
                    <button @click="tab = 'lampiran'; setTimeout(function(){ window.initLetterEditors(); }, 100)" :class="tab === 'lampiran' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700'" class="border-b-2 px-1 py-3 text-sm font-semibold transition">
                        Lampiran
                    </button>
                </nav>
            </div>

            <div x-show="tab === 'data'" class="mt-6">
                <form method="POST" action="{{ route('admin.letters.outgoings.update', $letterOutgoing) }}" class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    @csrf
                    @method('PUT')
                    @include('admin.letters.outgoings._form', [
                        'letterOutgoing' => $letterOutgoing,
                        'recipientRows' => collect(old('recipients', $recipientRows->map(fn($row) => [
                            'recipient_name' => $row->recipient_name,
                            'recipient_institution' => $row->recipient_institution,
                            'recipient_address' => $row->recipient_address,
                            'recipient_phone' => $row->recipient_phone,
                            'recipient_email' => $row->recipient_email,
                        ])->all()))
                    ])
                </form>
            </div>

@php $hasAttachment = !empty($attachmentContent?->content); @endphp
            <div x-show="tab === 'lampiran'" class="mt-6" x-cloak
                 x-data="{
                    mode: '{{ $hasAttachment ? 'view' : 'edit' }}',
                    startEdit() { this.mode = 'edit'; document.getElementById('lampiran-panel').querySelectorAll('.letter-ckeditor').forEach(function(el) { if (!el.ckeditorInstance && window.initLetterEditorElement) { el.dataset.ckeditorInitialized = 'false'; initLetterEditorElement(el); } }); },
                    cancelEdit() { location.reload(); }
                 }"
                 id="lampiran-panel">
                <div class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Lampiran Surat</h3>
                        <p class="mt-1 text-sm text-slate-500">Isi lampiran akan ditampilkan sebagai halaman terpisah setelah surat utama pada PDF.</p>
                    </div>

                    <form method="POST" action="{{ route('admin.letters.outgoings.update-attachment', $letterOutgoing) }}">
                        @csrf
                        @method('PUT')

                        <div x-show="mode === 'edit'" x-cloak>
                            <x-letter-editor
                                name="content"
                                label="Isi Lampiran"
                                :value="old('content', $attachmentContent?->content ?? '')"
                                :rows="16"
                            />
                        </div>

                        <div x-show="mode === 'view'" x-cloak class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <div class="mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Isi Lampiran</div>
                            <div class="max-w-none text-sm text-slate-800 [&_table]:w-full [&_table]:border-collapse [&_table]:mb-3 [&_td]:border [&_td]:border-slate-300 [&_td]:p-2 [&_th]:border [&_th]:border-slate-300 [&_th]:p-2 [&_th]:bg-slate-100 [&_th]:font-semibold [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_li]:mb-1">{!! $attachmentContent?->content ?? '' !!}</div>
                        </div>

                        <div class="mt-5 flex flex-col gap-3 sm:flex-row">

                            <button x-show="mode === 'edit'" type="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
                                {{ $hasAttachment ? 'Simpan Perubahan' : 'Simpan Lampiran' }}
                            </button>

                            <button x-show="mode === 'view' && {{ $letterOutgoing->status === 'draft' ? 'true' : 'false' }}" type="button" @click="startEdit"
                                    class="inline-flex items-center justify-center rounded-lg border border-emerald-200 px-5 py-2.5 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50">
                                Edit Lampiran
                            </button>

                            <button x-show="mode === 'edit'" type="button" @click="cancelEdit"
                                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                                Batal
                            </button>

                            <a href="{{ route('admin.letters.outgoings.preview', $letterOutgoing) }}" target="_blank"
                               class="inline-flex items-center justify-center rounded-lg border border-amber-200 px-5 py-2.5 text-sm font-semibold text-amber-700 transition hover:bg-amber-50">
                                Preview PDF
                            </a>

                            @if($letterOutgoing->status === 'issued')
                                <a href="{{ route('admin.letters.outgoings.print', $letterOutgoing) }}" target="_blank"
                                   class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
                                    Cetak PDF
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>[x-cloak] { display: none !important; }</style>
    @endpush
</x-admin-layout>
