<x-admin-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <div>
            <h2 class="text-xl font-bold text-gray-900 sm:text-2xl">Template Share WA</h2>
            <p class="mt-1 text-sm text-gray-500">Pesan WhatsApp yang akan dikirim saat pengguna membagikan informasi donasi pendidikan.</p>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <form method="POST" action="{{ route('admin.donasi-pendidikan.share-template.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700">Judul Template</label>
                    <input type="text" name="title" id="title" required
                           value="{{ old('title', $template->title) }}"
                           class="mt-1.5 block w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                </div>

                <div>
                    <label for="message_template" class="block text-sm font-semibold text-gray-700">Template Pesan</label>
                    <p class="mt-1 text-xs text-gray-400">
                        Gunakan placeholder: <code class="text-emerald-600 bg-emerald-50 px-1 rounded">{sapaan}</code>,
                        <code class="text-emerald-600 bg-emerald-50 px-1 rounded">{nama_tujuan}</code>,
                        <code class="text-emerald-600 bg-emerald-50 px-1 rounded">{link_donasi}</code>
                    </p>
                    <textarea name="message_template" id="message_template" rows="16" required
                              class="mt-1.5 block w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 font-mono">{{ old('message_template', $template->message_template) }}</textarea>
                </div>

                <div class="flex items-center gap-3">
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" name="is_active" value="1" class="peer sr-only"
                               {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                        <div class="h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:bg-emerald-500 peer-checked:after:translate-x-full peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-emerald-500/30"></div>
                    </label>
                    <span class="text-sm font-medium text-gray-700">Aktif</span>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Template
                    </button>
                    <a href="{{ route('admin.donasi-pendidikan.share-template') }}"
                       class="text-sm font-medium text-slate-500 hover:text-slate-700 transition-colors">Reset</a>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Pratinjau Pesan</h3>
            <div id="preview" class="text-sm text-gray-600 whitespace-pre-wrap rounded-lg bg-slate-50 border border-slate-200 p-4">
                {{ $template->message_template }}
            </div>
            <p class="mt-2 text-xs text-gray-400">
                Pratinjau akan menampilkan placeholder. Saat dikirim, <code class="text-emerald-600 bg-emerald-50 px-1 rounded">{sapaan}</code>,
                <code class="text-emerald-600 bg-emerald-50 px-1 rounded">{nama_tujuan}</code>, dan
                <code class="text-emerald-600 bg-emerald-50 px-1 rounded">{link_donasi}</code> akan diganti dengan data yang diisi pengguna.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Cara Kerja</h3>
            <ol class="list-decimal list-inside space-y-2 text-sm text-gray-600">
                <li>Pengguna membuka halaman <strong>Sebarkan Informasi</strong> dari tombol di halaman Donasi Pendidikan.</li>
                <li>Pengguna memilih sapaan, mengisi nama tujuan, dan opsional nomor WhatsApp.</li>
                <li>System mengganti placeholder <code class="text-emerald-600 bg-emerald-50 px-1 rounded">{sapaan}</code>, <code class="text-emerald-600 bg-emerald-50 px-1 rounded">{nama_tujuan}</code>, dan <code class="text-emerald-600 bg-emerald-50 px-1 rounded">{link_donasi}</code> dengan data yang diisi.</li>
                <li>Pengguna diarahkan ke WhatsApp dengan pesan yang sudah dipersonalisasi.</li>
            </ol>
        </div>
    </div>
</x-admin-layout>
