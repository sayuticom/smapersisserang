<x-admin-layout>
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Surat Keluar</h2>
                <p class="mt-1 text-sm text-gray-500">Kelola draft dan surat keluar yang sudah diterbitkan.</p>
            </div>
            <a href="{{ route('admin.letters.outgoings.create') }}"
               class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                Tambah Draft
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
        @endif

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-500">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Nomor</th>
                            <th class="px-4 py-3 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 font-semibold">Jenis</th>
                            <th class="px-4 py-3 font-semibold">Perihal</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                            <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($letters as $letter)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono text-xs text-gray-700 whitespace-nowrap">
                                    {{ $letter->letter_number ?: 'Draft' }}
                                </td>
                                <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
                                    {{ $letter->letter_date?->format('d/m/Y') ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $letter->letterType?->code ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-900">{{ $letter->subject }}</div>
                                    <div class="text-xs text-gray-500">Dibuat oleh {{ $letter->creator?->name ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $letter->status === 'issued' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-amber-50 text-amber-700 ring-1 ring-amber-200' }}">
                                        {{ $letter->status === 'issued' ? 'Terbit' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.letters.outgoings.show', $letter) }}" class="text-emerald-700 hover:text-emerald-900 text-xs font-semibold">Detail</a>
                                        @if($letter->status !== 'issued')
                                            <a href="{{ route('admin.letters.outgoings.edit', $letter) }}" class="text-blue-700 hover:text-blue-900 text-xs font-semibold">Edit</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">
                                    Belum ada surat keluar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($letters->hasPages())
                <div class="border-t border-gray-100 px-4 py-3">
                    {{ $letters->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
