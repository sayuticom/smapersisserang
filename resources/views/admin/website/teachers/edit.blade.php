<x-admin-layout>
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('admin.website.teachers.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">&larr; Kembali</a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">Edit Guru</h2>
        </div>
        <form method="POST" action="{{ route('admin.website.teachers.update', $teacher) }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
            @csrf @method('PUT')
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $teacher->name) }}" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran</label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject', $teacher->subject) }}" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                    <input type="text" name="position" id="position" value="{{ old('position', $teacher->position) }}" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" id="description" rows="4" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('description', $teacher->description) }}</textarea>
            </div>
            <div>
                <label for="teacher_quote" class="block text-sm font-medium text-gray-700 mb-1">Moto / Kutipan Guru</label>
                <textarea name="teacher_quote" id="teacher_quote" rows="3" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" placeholder="Contoh: Pendidikan adalah jalan terbaik untuk menyiapkan masa depan yang berakar pada nilai dan akhlak.">{{ old('teacher_quote', $teacher->teacher_quote) }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Teks ini akan tampil di card guru pada halaman publik.</p>
            </div>
            <div>
                <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
                <input type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number', $teacher->whatsapp_number) }}" placeholder="Contoh: 081234567890" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $teacher->sort_order) }}" min="0" class="w-32 rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Saat Ini</label>
                @if($teacher->photo_path)
                    <div class="mb-3"><img src="{{ asset('storage/' . $teacher->photo_path) }}" alt="{{ $teacher->name }}" class="h-24 w-24 rounded-xl object-cover border border-gray-200"></div>
                @else
                    <p class="text-sm text-gray-400 mb-3">Belum ada foto.</p>
                @endif
                <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">Ganti Foto</label>
                <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/jpg,image/webp" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                <p class="text-xs text-gray-400 mt-1">Maksimal 2MB. Format: JPG, PNG, WebP.</p>
                @error('photo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">Simpan Perubahan</button>
            </div>
        </form>

        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Link Edit Mandiri Guru</h3>
            @if($teacher->public_edit_token)
                @php
                    $editUrl = route('public.teachers.edit-token', $teacher->public_edit_token);
                    $waDisabled = !$teacher->whatsapp_number;
                @endphp
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-lg p-3 break-all text-sm text-gray-700 border border-gray-200">
                        <a href="{{ $editUrl }}" target="_blank" class="text-emerald-600 hover:text-emerald-700 underline">{{ $editUrl }}</a>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="var i=document.createElement('input');i.value='{{ $editUrl }}';document.body.appendChild(i);i.select();document.execCommand('copy');document.body.removeChild(i);this.textContent='Tersalin!';setTimeout(()=>this.textContent='Salin Link',2000);"
                                class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                            Salin Link
                        </button>
                        @if($waDisabled)
                            <span class="px-4 py-2 bg-gray-100 text-gray-400 text-sm font-medium rounded-lg cursor-not-allowed" title="Nomor WhatsApp belum tersedia">
                                Kirim via WhatsApp
                            </span>
                        @else
                            @php
                                $cleanWa = preg_replace('/[^0-9]/', '', $teacher->whatsapp_number);
                                if (str_starts_with($cleanWa, '0')) { $cleanWa = '62' . substr($cleanWa, 1); }
                                $message = "Assalamu'alaikum.\nBerikut link edit mandiri data guru SMA Persis Serang:\n{$editUrl}\n\nSilakan gunakan link tersebut untuk memperbarui data diri Anda.\nMohon jangan membagikan link ini kepada orang lain.";
                                $waUrl = 'https://wa.me/' . $cleanWa . '?text=' . urlencode($message);
                            @endphp
                            <a href="{{ $waUrl }}" target="_blank"
                               class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                                Kirim via WhatsApp
                            </a>
                        @endif
                        <form method="POST" action="{{ route('admin.website.teachers.reset-token', $teacher) }}" class="inline" onsubmit="return confirm('Yakin ingin mereset token? Token lama tidak akan bisa digunakan lagi.');">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-amber-500 text-white text-sm font-medium rounded-lg hover:bg-amber-600 transition-colors shadow-sm">
                                Reset Token
                            </button>
                        </form>
                    </div>
                    @if($waDisabled)
                        <p class="text-xs text-amber-600">Isi nomor WhatsApp guru terlebih dahulu agar bisa mengirim link via WhatsApp.</p>
                    @endif
                    <p class="text-xs text-gray-400">Token dibuat: {{ $teacher->token_generated_at ? $teacher->token_generated_at->format('d/m/Y H:i') : '-' }}</p>
                </div>
            @else
                <p class="text-sm text-gray-500 mb-4">Belum ada token. Buat token agar guru bisa mengedit data sendiri.</p>
                <form method="POST" action="{{ route('admin.website.teachers.generate-token', $teacher) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">Buat Token Edit Mandiri</button>
                </form>
            @endif
        </div>
    </div>
</x-admin-layout>