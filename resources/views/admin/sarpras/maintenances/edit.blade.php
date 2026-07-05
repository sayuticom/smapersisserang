<x-admin-layout>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Edit Laporan Perbaikan</h2>
                <p class="mt-1 text-gray-500">Ubah data laporan kerusakan atau pemeliharaan.</p>
            </div>
            <a href="{{ route('admin.sarpras.maintenances.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                &larr; Kembali
            </a>
        </div>

        @if($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-semibold">Periksa kembali data berikut:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.sarpras.maintenances.update', $maintenance) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf @method('PUT')
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="asset_id" class="block text-sm font-semibold text-gray-700">Aset Terkait (opsional)</label>
                        <select name="asset_id" id="asset_id"
                                class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">-- Pilih Aset --</option>
                            @foreach($assets as $asset)
                                <option value="{{ $asset->id }}" {{ old('asset_id', $maintenance->asset_id) == $asset->id ? 'selected' : '' }}>{{ $asset->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="room_id" class="block text-sm font-semibold text-gray-700">Ruangan Terkait (opsional)</label>
                        <select name="room_id" id="room_id"
                                class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">-- Pilih Ruangan --</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" {{ old('room_id', $maintenance->room_id) == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="title" class="block text-sm font-semibold text-gray-700">Judul Laporan <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title', $maintenance->title) }}"
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-green-500 focus:ring-green-500" required>
                        @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="damage_description" class="block text-sm font-semibold text-gray-700">Deskripsi Kerusakan <span class="text-red-500">*</span></label>
                        <textarea name="damage_description" id="damage_description" rows="4"
                                  class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-green-500 focus:ring-green-500" required>{{ old('damage_description', $maintenance->damage_description) }}</textarea>
                        @error('damage_description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700">Status</label>
                        <select name="status" id="status"
                                class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-green-500 focus:ring-green-500">
                            @foreach(config('sarpras.maintenance_statuses') as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $maintenance->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="reported_by" class="block text-sm font-semibold text-gray-700">Dilaporkan Oleh <span class="text-red-500">*</span></label>
                        <input type="text" name="reported_by" id="reported_by" value="{{ old('reported_by', $maintenance->reported_by) }}"
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-green-500 focus:ring-green-500" required>
                        @error('reported_by')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="reported_at" class="block text-sm font-semibold text-gray-700">Tanggal Laporan <span class="text-red-500">*</span></label>
                        <input type="date" name="reported_at" id="reported_at" value="{{ old('reported_at', $maintenance->reported_at?->format('Y-m-d')) }}"
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-green-500 focus:ring-green-500" required>
                        @error('reported_at')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="estimated_cost" class="block text-sm font-semibold text-gray-700">Estimasi Biaya (Rp)</label>
                        <input type="number" name="estimated_cost" id="estimated_cost" value="{{ old('estimated_cost', $maintenance->estimated_cost) }}" min="0"
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-green-500 focus:ring-green-500">
                        @error('estimated_cost')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="actual_cost" class="block text-sm font-semibold text-gray-700">Biaya Aktual (Rp)</label>
                        <input type="number" name="actual_cost" id="actual_cost" value="{{ old('actual_cost', $maintenance->actual_cost) }}" min="0"
                               class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-green-500 focus:ring-green-500">
                        @error('actual_cost')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="photo" class="block text-sm font-semibold text-gray-700">Foto Kerusakan</label>
                        @if($maintenance->photo)
                            <div class="mt-1 mb-2">
                                <img src="{{ asset('storage/' . $maintenance->photo) }}" alt="Foto kerusakan"
                                     class="h-24 w-24 rounded-lg object-cover border border-slate-200">
                            </div>
                        @endif
                        <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/jpg,image/webp"
                               class="mt-1.5 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                        <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengubah foto. Maksimal 2MB. Format: JPG, PNG, WebP.</p>
                        @error('photo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label for="follow_up_note" class="block text-sm font-semibold text-gray-700">Catatan Tindak Lanjut</label>
                    <textarea name="follow_up_note" id="follow_up_note" rows="3"
                              class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-green-500 focus:ring-green-500">{{ old('follow_up_note', $maintenance->follow_up_note) }}</textarea>
                    @error('follow_up_note')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.sarpras.maintenances.index') }}"
                       class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-green-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
