<x-admin-layout>
    <div class="mx-auto max-w-3xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Tambah Tahun Pelajaran</h2>
                <p class="mt-1 text-sm text-gray-500">Buat tahun pelajaran baru untuk kalender pendidikan.</p>
            </div>
            <a href="{{ route('admin.akademik.tahun-pelajaran.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>

        <form method="POST" action="{{ route('admin.akademik.tahun-pelajaran.store') }}">
            @csrf
            @include('admin.academic.years._form')

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('admin.akademik.tahun-pelajaran.index') }}"
                   class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit"
                        class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 transition">
                    Simpan Tahun Pelajaran
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
