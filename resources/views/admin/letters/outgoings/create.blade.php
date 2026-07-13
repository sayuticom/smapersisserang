<x-admin-layout>
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Tambah Draft Surat Keluar</h2>
                <p class="mt-1 text-sm text-gray-500">Nomor surat belum dibuat sampai draft diterbitkan.</p>
            </div>
            <a href="{{ route('admin.letters.outgoings.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                &larr; Kembali
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

        @if(request('template_id') && !$template)
            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                Template tidak ditemukan atau sedang tidak aktif. Form ditampilkan kosong.
            </div>
        @elseif($template)
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                Menggunakan template: <span class="font-semibold">{{ $template->title }}</span>.
            </div>
        @endif

        <form method="POST" action="{{ route('admin.letters.outgoings.store') }}">
            @csrf

            <div x-data="{
                tab: 'data',
                attachment: '{{ old('attachment', '') }}',
                hasExistingContent: false,
                get hasAttachment() {
                    if (this.hasExistingContent) return true;
                    const v = (this.attachment || '').toString().trim().toLowerCase();
                    return v !== '' && v !== 'none' && v !== 'tidak ada lampiran' && v !== '-';
                },
                init() {
                    this.$watch('attachment', (val) => {
                        if (!this.hasAttachment && this.tab === 'lampiran') {
                            this.tab = 'data';
                        }
                    });
                },
                startAttachmentEditor() {
                    const panel = document.getElementById('lampiran-panel');
                    const data = panel && window.Alpine && Alpine.$data(panel);
                    if (data && typeof data.startEditor === 'function') {
                        data.startEditor();
                    }
                }
            }">
                @include('admin.letters.outgoings.partials.draft-tabs')

                <div x-show="tab === 'data'" class="mt-6">
                    <div class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        @include('admin.letters.outgoings._form', [
                            'showAttachmentEditor' => false,
                            'letterOutgoing' => null,
                            'template' => $template,
                            'recipientRows' => collect(old('recipients', [['recipient_name' => '', 'recipient_institution' => '', 'recipient_address' => 'di Tempat', 'recipient_phone' => '', 'recipient_email' => '']]))
                        ])
                    </div>
                </div>

                @include('admin.letters.outgoings.partials.attachment-panel', [
                    'usesOwnForm' => false,
                    'editorName' => 'attachment_content',
                    'editorValue' => old('attachment_content', ''),
                    'initialMode' => 'edit',
                    'showViewMode' => false,
                    'saveLabel' => 'Kembali ke Data Surat',
                    'primaryButtonType' => 'button',
                    'primaryButtonClick' => "tab = 'data'",
                ])
            </div>
        </form>
    </div>

    @push('styles')
        <style>[x-cloak] { display: none !important; }</style>
    @endpush
</x-admin-layout>
