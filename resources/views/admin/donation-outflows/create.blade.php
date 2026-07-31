<x-admin-layout>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.donation-outflows.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Kembali</a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">Penyerahan Dana Donasi ke Keuangan</h2>
            <p class="text-sm text-gray-500 mt-1">Transaksi akan berstatus Menunggu Verifikasi sampai diproses petugas Keuangan.</p>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.donation-outflows.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Penyerahan <span class="text-red-500">*</span></label>
                    <input type="date" name="handover_date" value="{{ old('handover_date', now()->format('Y-m-d')) }}" required class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sumber/Jenis Donasi <span class="text-red-500">*</span></label>
                    <input type="text" name="donation_source" value="{{ old('donation_source') }}" maxlength="255" required placeholder="Contoh: Donasi Pendidikan" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan/Periode</label>
                <textarea name="description" rows="2" placeholder="Contoh: Periode Juli 2026" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount') }}" min="1" step="0.01" required class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Metode Penyerahan <span class="text-red-500">*</span></label>
                    <select name="handover_method" required class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        <option value="">-- Pilih --</option>
                        <option value="cash" @selected(old('handover_method') === 'cash')>Tunai</option>
                        <option value="transfer" @selected(old('handover_method') === 'transfer')>Transfer</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kas/Rekening Tujuan <span class="text-red-500">*</span></label>
                <input type="text" name="destination_account" value="{{ old('destination_account') }}" maxlength="255" required placeholder="Contoh: Kas Sekolah / BSI 1234567890" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bukti Penyerahan</label>
                <input type="file" name="proof_file" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                <p class="mt-1 text-xs text-gray-400">JPG, JPEG, PNG, atau PDF. Maksimal 2 MB.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.donation-outflows.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</a>
                <button type="submit" class="px-6 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 shadow-sm">Simpan Donasi Keluar</button>
            </div>
        </form>
    </div>
</x-admin-layout>
