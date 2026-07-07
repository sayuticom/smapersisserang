<div class="space-y-5">
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="letter_type_id" class="block text-sm font-semibold text-slate-700">Jenis Surat</label>
            <select name="letter_type_id" id="letter_type_id" class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">Tanpa jenis</option>
                @foreach($letterTypes as $type)
                    <option value="{{ $type->id }}" {{ (string) old('letter_type_id', $letterIncoming?->letter_type_id) === (string) $type->id ? 'selected' : '' }}>
                        {{ $type->code }} - {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="status" class="block text-sm font-semibold text-slate-700">Status <span class="text-red-500">*</span></label>
            <select name="status" id="status" class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}" {{ old('status', $letterIncoming?->status ?? 'received') === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="incoming_number" class="block text-sm font-semibold text-slate-700">Nomor Surat Masuk <span class="text-red-500">*</span></label>
            <input type="text" name="incoming_number" id="incoming_number" value="{{ old('incoming_number', $letterIncoming?->incoming_number) }}"
                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
        </div>
        <div>
            <label for="sender" class="block text-sm font-semibold text-slate-700">Pengirim <span class="text-red-500">*</span></label>
            <input type="text" name="sender" id="sender" value="{{ old('sender', $letterIncoming?->sender) }}"
                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
        </div>
        <div>
            <label for="letter_date" class="block text-sm font-semibold text-slate-700">Tanggal Surat</label>
            <input type="date" name="letter_date" id="letter_date" value="{{ old('letter_date', $letterIncoming?->letter_date?->format('Y-m-d')) }}"
                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <div>
            <label for="received_date" class="block text-sm font-semibold text-slate-700">Tanggal Diterima <span class="text-red-500">*</span></label>
            <input type="date" name="received_date" id="received_date" value="{{ old('received_date', $letterIncoming?->received_date?->format('Y-m-d') ?? now()->toDateString()) }}"
                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
        </div>
        <div class="md:col-span-2">
            <label for="subject" class="block text-sm font-semibold text-slate-700">Perihal <span class="text-red-500">*</span></label>
            <input type="text" name="subject" id="subject" value="{{ old('subject', $letterIncoming?->subject) }}"
                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
        </div>
        <div>
            <label for="attachment" class="block text-sm font-semibold text-slate-700">Lampiran</label>
            <input type="text" name="attachment" id="attachment" value="{{ old('attachment', $letterIncoming?->attachment) }}"
                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <div>
            <label for="file" class="block text-sm font-semibold text-slate-700">File Surat</label>
            <input type="file" name="file" id="file" accept=".pdf,image/jpeg,image/png,image/webp"
                   class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 file:mr-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100">
            <p class="mt-1 text-xs text-slate-500">PDF, JPG, PNG, atau WebP. Maksimal 2MB.</p>
            @if($letterIncoming?->file_path)
                <p class="mt-2 text-xs text-slate-600">
                    File saat ini:
                    <a href="{{ Storage::url($letterIncoming->file_path) }}" target="_blank" class="font-semibold text-emerald-700 hover:text-emerald-900">Lihat file</a>
                </p>
            @endif
        </div>
        <div class="md:col-span-2">
            <label for="notes" class="block text-sm font-semibold text-slate-700">Catatan</label>
            <textarea name="notes" id="notes" rows="4" class="mt-1.5 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('notes', $letterIncoming?->notes) }}</textarea>
        </div>
    </div>

    <div class="flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
        <a href="{{ $letterIncoming ? route('admin.letters.incomings.show', $letterIncoming) : route('admin.letters.incomings.index') }}"
           class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
            Batal
        </a>
        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
            Simpan Surat Masuk
        </button>
    </div>
</div>
