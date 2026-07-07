<x-admin-layout>
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Surat Masuk</h2>
                <p class="mt-1 text-sm text-gray-500">Catat dan kelola arsip surat masuk sekolah.</p>
            </div>
            <a href="{{ route('admin.letters.incomings.create') }}"
               class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                Tambah Surat Masuk
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('admin.letters.incomings.index') }}" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid gap-3 md:grid-cols-[1fr_180px_180px_auto]">
                <div>
                    <label for="search" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Cari</label>
                    <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}"
                           class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                           placeholder="Nomor, pengirim, atau perihal">
                </div>
                <div>
                    <label for="status" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Status</label>
                    <select name="status" id="status" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Semua status</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ ($filters['status'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="received_date" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Tanggal Diterima</label>
                    <input type="date" name="received_date" id="received_date" value="{{ $filters['received_date'] ?? '' }}"
                           class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="inline-flex h-10 items-center justify-center rounded-lg bg-slate-900 px-4 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Filter
                    </button>
                    <a href="{{ route('admin.letters.incomings.index') }}" class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-300 px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
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
                            <th class="px-4 py-3 font-semibold">Tanggal Terima</th>
                            <th class="px-4 py-3 font-semibold">Nomor</th>
                            <th class="px-4 py-3 font-semibold">Pengirim</th>
                            <th class="px-4 py-3 font-semibold">Perihal</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                            <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($letters as $letter)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 whitespace-nowrap text-slate-700">{{ $letter->received_date?->format('d/m/Y') ?? '-' }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-slate-700">{{ $letter->incoming_number }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-900">{{ $letter->sender }}</div>
                                    <div class="text-xs text-slate-500">{{ $letter->letterType?->code ?? 'Tanpa jenis' }}</div>
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ $letter->subject }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">
                                        {{ $statuses[$letter->status] ?? $letter->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.letters.incomings.show', $letter) }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">Detail</a>
                                        <a href="{{ route('admin.letters.incomings.edit', $letter) }}" class="text-xs font-semibold text-blue-700 hover:text-blue-900">Edit</a>
                                        <form method="POST" action="{{ route('admin.letters.incomings.destroy', $letter) }}" onsubmit="return confirm('Hapus surat masuk ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">
                                    Belum ada data surat masuk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($letters->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">
                    {{ $letters->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
