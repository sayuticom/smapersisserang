<x-admin-layout>
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('admin.ai-faqs.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">&larr; Kembali</a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">Tambah FAQ AI</h2>
        </div>
        <form method="POST" action="{{ route('admin.ai-faqs.store') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
            @csrf
            <div>
                <label for="question" class="block text-sm font-medium text-gray-700 mb-1">Pertanyaan <span class="text-red-500">*</span></label>
                <input type="text" name="question" id="question" value="{{ old('question') }}"
                       class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required maxlength="255">
                @error('question')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="answer" class="block text-sm font-medium text-gray-700 mb-1">Jawaban Resmi <span class="text-red-500">*</span></label>
                <div class="flex flex-wrap items-center gap-0.5 mb-2 border border-gray-200 rounded-lg p-1 bg-gray-50">
                    <button type="button" onclick="formatBold()" title="Tebal" class="p-1.5 rounded hover:bg-gray-200 text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 4h8a4 4 0 014 4 4 4 0 01-4 4H6z"/><path d="M6 12h9a4 4 0 014 4 4 4 0 01-4 4H6z"/></svg>
                    </button>
                    <span class="w-px h-4 bg-gray-300 mx-0.5"></span>
                    <button type="button" onclick="formatItalic()" title="Miring" class="p-1.5 rounded hover:bg-gray-200 text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="4" x2="10" y2="4"/><line x1="14" y1="20" x2="5" y2="20"/><line x1="15" y1="4" x2="9" y2="20"/></svg>
                    </button>
                    <span class="w-px h-4 bg-gray-300 mx-0.5"></span>
                    <button type="button" onclick="formatBullet()" title="Bullet List" class="p-1.5 rounded hover:bg-gray-200 text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="3" cy="6" r="1" fill="currentColor"/><circle cx="3" cy="12" r="1" fill="currentColor"/><circle cx="3" cy="18" r="1" fill="currentColor"/></svg>
                    </button>
                    <button type="button" onclick="formatNumbered()" title="Numbered List" class="p-1.5 rounded hover:bg-gray-200 text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="10" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="10" y1="18" x2="21" y2="18"/><path d="M4 6h1v4"/><path d="M4 10h2"/><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"/></svg>
                    </button>
                    <span class="w-px h-4 bg-gray-300 mx-0.5"></span>
                    <button type="button" onclick="formatLink()" title="Sisipkan Link" class="p-1.5 rounded hover:bg-gray-200 text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
                    </button>
                    <span class="w-px h-4 bg-gray-300 mx-0.5"></span>
                    <button type="button" onclick="insertExample()" title="Isi contoh format" class="p-1.5 rounded hover:bg-gray-200 text-gray-600 hover:text-gray-900 transition-colors text-xs font-medium flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        Contoh
                    </button>
                </div>
                <textarea name="answer" id="answer" rows="6"
                          class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm font-mono" required maxlength="5000">{{ old('answer') }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Gunakan format sederhana seperti <strong class="text-gray-500">**tebal**</strong>, <em class="text-gray-500">*miring*</em>, dan <strong class="text-gray-500">•</strong> bullet list agar jawaban chatbot lebih rapi.</p>
                @error('answer')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="keywords" class="block text-sm font-medium text-gray-700 mb-1">Kata Kunci</label>
                <input type="text" name="keywords" id="keywords" value="{{ old('keywords') }}"
                       class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" maxlength="500">
                <p class="text-xs text-gray-400 mt-1">Pisahkan dengan koma. Contoh: syarat, dokumen, berkas</p>
                @error('keywords')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="category" id="category"
                            class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        <option value="">Pilih Kategori</option>
                        <option value="SPMB" {{ old('category') === 'SPMB' ? 'selected' : '' }}>SPMB</option>
                        <option value="Biaya" {{ old('category') === 'Biaya' ? 'selected' : '' }}>Biaya</option>
                        <option value="Asrama" {{ old('category') === 'Asrama' ? 'selected' : '' }}>Asrama</option>
                        <option value="Program" {{ old('category') === 'Program' ? 'selected' : '' }}>Program</option>
                        <option value="Kontak" {{ old('category') === 'Kontak' ? 'selected' : '' }}>Kontak</option>
                        <option value="Umum" {{ old('category') === 'Umum' ? 'selected' : '' }}>Umum</option>
                    </select>
                    @error('category')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}"
                           class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" min="0">
                    @error('sort_order')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', true) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                <label for="is_active" class="text-sm font-medium text-gray-700">Aktif</label>
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.ai-faqs.index') }}"
                   class="px-6 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">Batal</a>
                <button type="submit"
                        class="px-6 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">Simpan</button>
            </div>
        </form>
    </div>
</x-admin-layout>

@push('scripts')
<script>
function getAnswerTextarea() {
    return document.getElementById('answer') || document.querySelector('textarea[name="answer"]');
}

function replaceSelection(transform, fallbackText) {
    const ta = getAnswerTextarea();
    if (!ta) return;
    const start = ta.selectionStart;
    const end = ta.selectionEnd;
    const value = ta.value;
    const selected = value.substring(start, end);
    const replacement = selected ? transform(selected) : fallbackText;
    ta.value = value.substring(0, start) + replacement + value.substring(end);
    ta.focus();
    const newPos = start + replacement.length;
    ta.selectionStart = newPos;
    ta.selectionEnd = newPos;
    ta.dispatchEvent(new Event('input', { bubbles: true }));
    ta.dispatchEvent(new Event('change', { bubbles: true }));
}

function formatBold() {
    replaceSelection(text => `**${text}**`, '**teks tebal**');
}

function formatItalic() {
    replaceSelection(text => `*${text}*`, '*teks miring*');
}

function formatBullet() {
    replaceSelection(
        text => text.split('\n').map(l => l.trim() ? `• ${l.replace(/^[-•]\s*/, '')}` : l).join('\n'),
        '• Item pertama'
    );
}

function formatNumbered() {
    let i = 1;
    replaceSelection(
        text => text.split('\n').map(l => l.trim() ? `${i++}. ${l.replace(/^\d+\.\s*/, '')}` : l).join('\n'),
        '1. Item pertama'
    );
}

function formatLink() {
    replaceSelection(
        text => `[${text}](https://contoh-link.com)`,
        '[Teks link](https://contoh-link.com)'
    );
}

function insertExample() {
    const example = 'Syarat pendaftaran meliputi:\n\n• Scan ijazah atau SKL\n• Scan akta kelahiran\n• Scan kartu keluarga\n\nUntuk melanjutkan pendaftaran, silakan hubungi **WA Official panitia SPMB**:\nhttps://wa.me/6289661234569';
    replaceSelection(() => example, example);
}
</script>
@endpush
