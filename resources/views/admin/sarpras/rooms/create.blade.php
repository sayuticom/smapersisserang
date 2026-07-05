<x-admin-layout>
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Tambah Ruangan Baru</h2>
                <p class="mt-1 text-gray-500">Lengkapi data ruangan sekolah.</p>
            </div>
            <a href="{{ route('admin.sarpras.rooms.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                &larr; Kembali
            </a>
        </div>

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

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.sarpras.rooms.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700">Nama Ruangan <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                           class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                    @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="room_type" class="block text-sm font-semibold text-gray-700">Tipe Ruangan <span class="text-red-500">*</span></label>
                    <input type="text" name="room_type" id="room_type" value="{{ old('room_type') }}"
                           list="roomTypeList"
                           class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                    <datalist id="roomTypeList">
                        @foreach(config('sarpras.room_types') as $type)
                            <option value="{{ $type }}">
                        @endforeach
                    </datalist>
                    <p class="mt-1 text-xs text-gray-400">Ketik atau pilih dari daftar.</p>
                    @error('room_type')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="capacity" class="block text-sm font-semibold text-gray-700">Kapasitas (orang)</label>
                    <input type="number" name="capacity" id="capacity" value="{{ old('capacity') }}"
                           min="0"
                           class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('capacity')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="person_in_charge" class="block text-sm font-semibold text-gray-700">Penanggung Jawab</label>
                    <input type="text" name="person_in_charge" id="person_in_charge" value="{{ old('person_in_charge') }}"
                           class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('person_in_charge')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="condition" class="block text-sm font-semibold text-gray-700">Kondisi <span class="text-red-500">*</span></label>
                    <select name="condition" id="condition"
                            class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                        <option value="">Pilih Kondisi</option>
                        @foreach(config('sarpras.room_conditions') as $val => $label)
                            <option value="{{ $val }}" {{ old('condition') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('condition')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="photo" class="block text-sm font-semibold text-gray-700">Foto Ruangan</label>
                    <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/jpg,image/webp"
                           class="mt-1.5 w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100">
                    <p class="mt-1 text-xs text-gray-400">Maksimal 2MB. Format: JPG, PNG, WebP.</p>
                    @error('photo')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="needs_note" class="block text-sm font-semibold text-gray-700">Catatan Kebutuhan</label>
                    <textarea name="needs_note" id="needs_note" rows="3"
                              class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('needs_note') }}</textarea>
                    <p class="mt-1 text-xs text-gray-400">Catatan terkait kebutuhan ruangan ini.</p>
                    @error('needs_note')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700">Deskripsi</label>
                    <textarea name="description" id="description" rows="4"
                              class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('description') }}</textarea>
                    @error('description')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('admin.sarpras.rooms.index') }}"
                       class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-green-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
