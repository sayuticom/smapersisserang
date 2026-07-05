<x-admin-layout>
    <div class="max-w-6xl mx-auto">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Orang Tua Asuh</h2>
                <p class="text-gray-500 mt-1">Kelola data calon anak asuh / murid.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.orang-tua-asuh.submissions') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                    Pengajuan ({{ \App\Models\FosterParentSubmission::pending()->count() }})
                </a>
                <a href="{{ route('admin.orang-tua-asuh.create') }}"
                   class="inline-flex items-center justify-center rounded-lg bg-[#0F6B3A] px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-[#0A4F2B]">
                    + Tambah Murid
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Foto</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Nama</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Kelas</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Asal</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Status</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Prioritas</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Aktif</th>
                            <th class="text-right px-4 py-3 font-semibold text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($students as $student)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    @if($student->photo_url)
                                        <img src="{{ $student->photo_url }}" alt="{{ $student->name }}"
                                             class="w-10 h-10 rounded-lg object-cover">
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h18a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $student->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $student->class_name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $student->origin ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $student->foster_status === 'available' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $student->foster_status === 'assigned' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $student->foster_status === 'inactive' ? 'bg-gray-100 text-gray-600' : '' }}">
                                        {{ $student->foster_status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($student->is_priority)
                                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700">Prioritas</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($student->is_active)
                                        <span class="text-green-600">Ya</span>
                                    @else
                                        <span class="text-red-500">Tidak</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.orang-tua-asuh.edit', $student) }}"
                                       class="text-sm font-medium text-[#0F6B3A] hover:text-[#0A4F2B]">Edit</a>
                                    <form action="{{ route('admin.orang-tua-asuh.destroy', $student) }}" method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Hapus data murid ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ml-3 text-sm font-medium text-red-600 hover:text-red-700">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-gray-500">
                                    Belum ada data murid.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($students->hasPages())
                <div class="px-4 py-3 border-t border-slate-100">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
