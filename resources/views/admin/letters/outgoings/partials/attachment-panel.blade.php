@php
    $usesOwnForm = $usesOwnForm ?? false;
    $formAction = $formAction ?? null;
    $formMethod = $formMethod ?? 'POST';
    $editorName = $editorName ?? 'attachment_content';
    $editorValue = $editorValue ?? '';
    $hasAttachmentContent = !empty($editorValue);
    $initialMode = $initialMode ?? ($hasAttachmentContent ? 'view' : 'edit');
    $showViewMode = $showViewMode ?? false;
    $showPreviewButton = $showPreviewButton ?? false;
    $showPrintButton = $showPrintButton ?? false;
    $previewUrl = $previewUrl ?? null;
    $printUrl = $printUrl ?? null;
    $primaryButtonType = $primaryButtonType ?? 'submit';
    $primaryButtonClick = $primaryButtonClick ?? null;
@endphp

<div x-show="tab === 'lampiran'" class="mt-6" x-cloak id="lampiran-panel"
     x-data="{
        mode: '{{ $initialMode }}',
        editorInited: false,
        init() {
            this.$watch('mode', (val) => {
                if (val === 'edit') {
                    this.$nextTick(() => this.startEditor());
                }
            });
            if (this.mode === 'edit') {
                this.$nextTick(() => this.startEditor());
            }
        },
        startEditor() {
            if (this.editorInited) return;
            const panel = document.getElementById('lampiran-panel');
            if (!panel || panel.offsetParent === null) {
                return;
            }
            this.editorInited = true;
            document.querySelectorAll('#lampiran-panel .letter-ckeditor').forEach(function(el){
                if (!el.ckeditorInstance && window.initSingleLetterEditor) {
                    el.classList.remove('hidden');
                    el.dataset.ckeditorInitialized = 'false';
                    window.initSingleLetterEditor(el);
                }
            });
        },
        startEdit() {
            this.mode = 'edit';
        },
        cancelEdit() { location.reload(); }
     }">
    <div class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div>
            <h3 class="text-lg font-semibold text-slate-900">Lampiran Surat</h3>
            <p class="mt-1 text-sm text-slate-500">Isi lampiran akan ditampilkan sebagai halaman terpisah setelah surat utama pada PDF.</p>
        </div>

        @if($usesOwnForm)
            <form method="POST" action="{{ $formAction }}">
                @csrf
                @if(strtoupper($formMethod) !== 'POST')
                    @method($formMethod)
                @endif
        @endif

        <div x-show="mode === 'edit'" x-cloak>
            <x-letter-editor
                :name="$editorName"
                label="Isi Lampiran"
                :value="$editorValue"
                :rows="16"
                :showTableButtons="true"
                :auto-init="false"
            />
        </div>

        @if($showViewMode)
            <div x-show="mode === 'view'" x-cloak class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Isi Lampiran</div>
                <div class="max-w-none text-sm text-slate-800 [&_table]:mb-3 [&_table]:w-full [&_table]:table-fixed [&_table]:border-collapse [&_td]:border [&_td]:border-slate-300 [&_td]:p-2 [&_th]:border [&_th]:border-slate-300 [&_th]:bg-slate-100 [&_th]:p-2 [&_th]:font-semibold [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_li]:mb-1">{!! $editorValue !!}</div>
            </div>
        @endif

        <div class="mt-5 flex flex-col gap-3 sm:flex-row">
            <button x-show="mode === 'edit'" type="{{ $primaryButtonType }}"
                    @if($primaryButtonClick) @click="{{ $primaryButtonClick }}" @endif
                    class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
                {{ $saveLabel ?? ($hasAttachmentContent ? 'Simpan Perubahan' : 'Simpan Lampiran') }}
            </button>

            @if($showViewMode)
                <button x-show="mode === 'view' && {{ ($canEditAttachment ?? true) ? 'true' : 'false' }}" type="button" @click="startEdit"
                        class="inline-flex items-center justify-center rounded-lg border border-emerald-200 px-5 py-2.5 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50">
                    Edit Lampiran
                </button>

                <button x-show="mode === 'edit'" type="button" @click="cancelEdit"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Batal
                </button>
            @endif

            @if($showPreviewButton && $previewUrl)
                <a href="{{ $previewUrl }}" target="_blank"
                   class="inline-flex items-center justify-center rounded-lg border border-amber-200 px-5 py-2.5 text-sm font-semibold text-amber-700 transition hover:bg-amber-50">
                    Preview PDF
                </a>
            @endif

            @if($showPrintButton && $printUrl)
                <a href="{{ $printUrl }}" target="_blank"
                   class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
                    Cetak PDF
                </a>
            @endif
        </div>

        @if($usesOwnForm)
            </form>
        @endif
    </div>
</div>
