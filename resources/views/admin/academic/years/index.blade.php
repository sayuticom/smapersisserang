<x-admin-layout>
    <div class="mx-auto max-w-7xl space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Tahun Pelajaran</h2>
                <p class="mt-1 text-sm text-gray-500">Kelola tahun pelajaran untuk kalender pendidikan.</p>
            </div>
            <a href="{{ route('admin.akademik.tahun-pelajaran.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 transition shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Tahun Pelajaran
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @php $hasActive = $years->contains(fn($y) => $y->is_current); @endphp
        @unless($hasActive)
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-3 text-sm text-amber-700">
                Tidak ada tahun pelajaran yang aktif. Silakan aktifkan salah satu tahun pelajaran.
            </div>
        @endunless

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50">
                            <th class="px-5 py-3 font-semibold text-slate-700">Tahun Pelajaran</th>
                            <th class="px-5 py-3 font-semibold text-slate-700">Rentang</th>
                            <th class="px-5 py-3 font-semibold text-slate-700">Semester Ganjil</th>
                            <th class="px-5 py-3 font-semibold text-slate-700">Semester Genap</th>
                            <th class="px-5 py-3 font-semibold text-slate-700">Status</th>
                            <th class="px-5 py-3 font-semibold text-slate-700">Kegiatan</th>
                            <th class="px-5 py-3 font-semibold text-slate-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                            $idMonths = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                            $fmtId = fn($d) => $d->format('j') . ' ' . $idMonths[$d->month] . ' ' . $d->format('Y');
                        @endphp
                        @forelse($years as $year)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3">
                                    <p class="font-medium text-slate-900">{{ $year->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $year->academic_year }}</p>
                                </td>
                                <td class="px-5 py-3 text-sm text-slate-600 whitespace-nowrap">
                                    {{ $fmtId($year->start_date) }}
                                    – {{ $fmtId($year->end_date) }}
                                </td>
                                <td class="px-5 py-3 text-sm text-slate-600 whitespace-nowrap">
                                    @if($year->odd_semester_start_date)
                                        {{ $fmtId($year->odd_semester_start_date) }}
                                        – {{ $fmtId($year->odd_semester_end_date) }}
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-sm text-slate-600 whitespace-nowrap">
                                    @if($year->even_semester_start_date)
                                        {{ $fmtId($year->even_semester_start_date) }}
                                        – {{ $fmtId($year->even_semester_end_date) }}
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    @if($year->is_current)
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-700">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-500">
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-sm text-slate-600">
                                    {{ $year->events_count }}
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('admin.akademik.tahun-pelajaran.edit', $year) }}"
                                           class="inline-flex items-center gap-1 rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </a>

                                        @if(!$year->is_current)
                                            <form method="POST" action="{{ route('admin.akademik.tahun-pelajaran.set-current', $year) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1 rounded-lg border border-emerald-300 px-2.5 py-1.5 text-xs font-medium text-emerald-700 hover:bg-emerald-50 transition"
                                                        onclick="return confirm('Jadikan {{ addslashes($year->name) }} sebagai tahun pelajaran aktif?')">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Jadikan Aktif
                                                </button>
                                            </form>
                                        @endif

                                        @if($year->events_count > 0)
                                            <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-400 bg-slate-50 cursor-not-allowed"
                                                  title="Tahun pelajaran tidak dapat dihapus karena masih memiliki {{ $year->events_count }} kegiatan kalender.">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Hapus
                                            </span>
                                        @else
                                            <form method="POST" action="{{ route('admin.akademik.tahun-pelajaran.destroy', $year) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1 rounded-lg border border-red-200 px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition"
                                                        onclick="return confirm('Hapus tahun pelajaran {{ addslashes($year->name) }}?')">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400">
                                    Belum ada tahun pelajaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
