<x-admin-layout>
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('admin.akademik.jam-pelajaran.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700">&larr; Kembali</a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">Tambah Jam Pelajaran</h2>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Jam <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm" placeholder="Jam ke-1">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hari <span class="text-red-500">*</span></label>
                <select name="day" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @foreach($days as $day)
                        <option value="{{ $day }}" {{ old('day') === $day ? 'selected' : '' }}>{{ $day }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                    <input type="time" name="start_time" value="{{ old('start_time') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai <span class="text-red-500">*</span></label>
                    <input type="time" name="end_time" value="{{ old('end_time') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis <span class="text-red-500">*</span></label>
                <select name="type" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    <option value="pelajaran" {{ old('type') === 'pelajaran' ? 'selected' : '' }}>Pelajaran</option>
                    <option value="istirahat" {{ old('type') === 'istirahat' ? 'selected' : '' }}>Istirahat</option>
                    <option value="ishoma" {{ old('type') === 'ishoma' ? 'selected' : '' }}>Ishoma</option>
                    <option value="upacara" {{ old('type') === 'upacara' ? 'selected' : '' }}>Upacara</option>
                    <option value="pembiasaan" {{ old('type') === 'pembiasaan' ? 'selected' : '' }}>Pembiasaan</option>
                    <option value="kegiatan_khusus" {{ old('type') === 'kegiatan_khusus' ? 'selected' : '' }}>Kegiatan Khusus</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="is_active"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active', '1') == '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="pt-2">
                <button type="submit" class="px-6 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
