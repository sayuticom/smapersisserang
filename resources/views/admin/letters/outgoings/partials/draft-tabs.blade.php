<div class="border-b border-slate-200">
    <nav class="-mb-px flex gap-6">
        <button type="button" @click="tab = 'data'" :class="tab === 'data' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700'" class="border-b-2 px-1 py-3 text-sm font-semibold transition">
            Data Surat
        </button>
        <button type="button"
                @click="hasAttachment && (tab = 'lampiran', $nextTick(() => startAttachmentEditor()))"
                :class="tab === 'lampiran' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700'"
                class="border-b-2 px-1 py-3 text-sm font-semibold transition disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="!hasAttachment"
                :title="!hasAttachment ? 'Tidak ada lampiran' : ''">
            Lampiran
        </button>
    </nav>
</div>
