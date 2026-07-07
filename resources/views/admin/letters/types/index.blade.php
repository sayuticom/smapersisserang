<x-admin-layout>
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Jenis Surat</h2>
                <p class="mt-1 text-sm text-gray-500">Kelola kode jenis surat untuk penomoran dan template.</p>
            </div>
            <a href="{{ route('admin.letters.outgoings.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                Surat Keluar
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif

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

        @include('admin.letters.types._form')

        <form method="GET" action="{{ route('admin.letters.types.index') }}" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid gap-3 md:grid-cols-[1fr_auto]">
                <div>
                    <label for="search" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Cari</label>
                    <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}"
                           class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                           placeholder="Nama atau kode jenis surat">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="inline-flex h-10 items-center justify-center rounded-lg bg-slate-900 px-4 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Filter
                    </button>
                    <a href="{{ route('admin.letters.types.index') }}" class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-300 px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
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
                            <th class="px-4 py-3 font-semibold">Kode</th>
                            <th class="px-4 py-3 font-semibold">Nama</th>
                            <th class="px-4 py-3 font-semibold">Urutan</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                            <th class="px-4 py-3 font-semibold">Dipakai</th>
                            <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($letterTypes as $type)
                            @php
                                $usageCount = $type->outgoing_letters_count + $type->incoming_letters_count + $type->templates_count;
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-mono text-xs font-bold text-slate-700">{{ $type->code }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-900">{{ $type->name }}</div>
                                    @if($type->description)
                                        <div class="mt-1 line-clamp-1 text-xs text-slate-500">{{ $type->description }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ $type->sort_order }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $type->is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-1 ring-slate-200' }}">
                                        {{ $type->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-600">
                                    {{ $usageCount }} data
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.letters.types.edit', $type) }}" class="text-xs font-semibold text-blue-700 hover:text-blue-900">Edit</a>
                                        <form method="POST" action="{{ route('admin.letters.types.destroy', $type) }}" onsubmit="return confirm('Hapus jenis surat ini? Data lama tetap aman karena soft delete.')" class="inline">
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
                                    Belum ada jenis surat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($letterTypes->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">
                    {{ $letterTypes->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
