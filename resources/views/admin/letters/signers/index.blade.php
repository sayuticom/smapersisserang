<x-admin-layout>
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Master Penandatangan</h2>
                <p class="mt-1 text-sm text-gray-500">Kelola pejabat dan file tanda tangan untuk surat keluar.</p>
            </div>
            @if($letterSigner)
                <a href="{{ route('admin.letters.signers.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Batal Edit
                </a>
            @endif
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

        <div class="grid gap-6 lg:grid-cols-[minmax(0,420px)_1fr]">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900">{{ $letterSigner ? 'Edit Penandatangan' : 'Tambah Penandatangan' }}</h3>
                <p class="mt-1 text-sm text-slate-500">File tanda tangan opsional dan bisa ditambahkan nanti.</p>

                @include('admin.letters.signers._form', ['letterSigner' => $letterSigner])
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="font-bold text-slate-900">Daftar Penandatangan</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Urutan</th>
                                <th class="px-4 py-3 font-semibold">Nama</th>
                                <th class="px-4 py-3 font-semibold">Jabatan</th>
                                <th class="px-4 py-3 font-semibold">TTD</th>
                                <th class="px-4 py-3 font-semibold">Status</th>
                                <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($signers as $signer)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 text-slate-600">{{ $signer->sort_order }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-slate-900">{{ $signer->name }}</div>
                                        <div class="text-xs text-slate-500">{{ $signer->identity_number ?: '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-700">{{ $signer->position ?: '-' }}</td>
                                    <td class="px-4 py-3">
                                        @if($signer->signature_path)
                                            <img src="{{ Storage::url($signer->signature_path) }}"
                                                 alt="Tanda tangan {{ $signer->name }}"
                                                 class="h-12 max-w-[120px] rounded border border-slate-200 bg-white object-contain p-1">
                                        @else
                                            <span class="text-xs text-slate-400">Belum ada</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $signer->is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-1 ring-slate-200' }}">
                                            {{ $signer->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('admin.letters.signers.edit', $signer) }}"
                                               class="text-xs font-semibold text-blue-700 hover:text-blue-900">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('admin.letters.signers.destroy', $signer) }}" onsubmit="return confirm('Hapus penandatangan ini?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">
                                        Belum ada master penandatangan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
