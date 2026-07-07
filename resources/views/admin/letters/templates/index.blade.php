<x-admin-layout>
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Template Surat</h2>
                <p class="mt-1 text-sm text-gray-500">Kelola contoh isi surat untuk mempercepat pembuatan surat keluar.</p>
            </div>
            <a href="{{ route('admin.letters.templates.create') }}"
               class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                Tambah Template
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('admin.letters.templates.index') }}" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid gap-3 md:grid-cols-[1fr_220px_auto]">
                <div>
                    <label for="search" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Cari</label>
                    <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}"
                           class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                           placeholder="Judul, perihal, atau isi">
                </div>
                <div>
                    <label for="letter_type_id" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Jenis Surat</label>
                    <select name="letter_type_id" id="letter_type_id" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Semua jenis</option>
                        @foreach($letterTypes as $type)
                            <option value="{{ $type->id }}" {{ (string) ($filters['letter_type_id'] ?? '') === (string) $type->id ? 'selected' : '' }}>
                                {{ $type->code }} - {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="inline-flex h-10 items-center justify-center rounded-lg bg-slate-900 px-4 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Filter
                    </button>
                    <a href="{{ route('admin.letters.templates.index') }}" class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-300 px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Judul</th>
                            <th class="px-4 py-3 font-semibold">Jenis</th>
                            <th class="px-4 py-3 font-semibold">Perihal</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                            <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($templates as $template)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-900">{{ $template->title }}</div>
                                    <div class="mt-1 line-clamp-1 text-xs text-slate-500">{{ $template->body_template }}</div>
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ $template->letterType?->code }} - {{ $template->letterType?->name }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $template->subject_template ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $template->is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-1 ring-slate-200' }}">
                                        {{ $template->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.letters.templates.show', $template) }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">Detail</a>
                                        <a href="{{ route('admin.letters.templates.edit', $template) }}" class="text-xs font-semibold text-blue-700 hover:text-blue-900">Edit</a>
                                        <form method="POST" action="{{ route('admin.letters.templates.destroy', $template) }}" onsubmit="return confirm('Hapus template surat ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">
                                    Belum ada template surat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($templates->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">
                    {{ $templates->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
