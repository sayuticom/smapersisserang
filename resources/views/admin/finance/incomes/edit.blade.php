<x-admin-layout>
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.finance.incomes.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Kembali ke Pemasukan</a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">Edit Pemasukan</h2>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-6">
                <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        @if(!empty($isIntegrated))
            <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-lg text-sm mb-6">
                Pemasukan ini berasal dari Donasi Keluar. Perubahan akan memperbarui data Donasi Keluar terkait dan memengaruhi Saldo Donasi serta laporan Keuangan.
            </div>
        @endif

        <form method="POST" action="{{ route('admin.finance.incomes.update', $financeIncome) }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="date" value="{{ old('date', $financeIncome->date->format('Y-m-d')) }}" required class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Pemasukan <span class="text-red-500">*</span></label>
                    <select name="income_type" {{ !empty($isIntegrated) ? 'disabled' : '' }} required class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        <option value="">-- Pilih --</option>
                        @foreach($incomeTypes as $type)
                            <option value="{{ $type }}" {{ old('income_type', $financeIncome->income_type) === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                    @if(!empty($isIntegrated))
                        <input type="hidden" name="income_type" value="{{ $financeIncome->income_type }}">
                        <p class="text-xs text-gray-500 mt-1">Jenis Pemasukan berasal dari Donasi Keluar dan tidak dapat diubah.</p>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount', $financeIncome->amount) }}" required min="1" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
                <div>
                    @if(!empty($isIntegrated))
                        <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran <span class="text-red-500">*</span></label>
                        <select name="payment_method" required class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            <option value="">-- Pilih --</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method }}" {{ old('payment_method', $financeIncome->payment_method) === $method ? 'selected' : '' }}>{{ $method }}</option>
                            @endforeach
                        </select>
                    @else
                        <label class="block text-sm font-medium text-gray-700 mb-1">Masuk ke Akun Keuangan <span class="text-red-500">*</span></label>
                        <select name="finance_account_id" required class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            <option value="">-- Pilih Akun Keuangan --</option>
                            @foreach($financeAccounts as $account)
                                <option value="{{ $account->id }}" {{ (int) old('finance_account_id', $financeIncome->finance_account_id) === (int) $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Hanya akun kategori Keuangan yang aktif yang tersedia.</p>
                    @endif
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sumber / Nama Pemberi</label>
                <input type="text" name="source_name" value="{{ old('source_name', $financeIncome->source_name) }}" maxlength="255" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="description" rows="2" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('description', $financeIncome->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Bukti (opsional, maks 2MB, format: jpg/png/pdf)</label>
                @if($financeIncome->proof_file)
                    <div class="mb-2 flex items-center gap-2">
                        <span class="text-xs text-gray-500">File saat ini:</span>
                        <a href="{{ asset('storage/' . $financeIncome->proof_file) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat file</a>
                    </div>
                @endif
                <input type="file" name="proof_file" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('admin.finance.incomes.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</a>
                <button type="submit" class="px-6 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">Simpan</button>
            </div>
        </form>
    </div>
</x-admin-layout>
