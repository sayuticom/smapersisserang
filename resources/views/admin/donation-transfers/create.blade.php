<x-admin-layout>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.donation-transfers.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Kembali</a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">Mutasi Dana Donasi ke Keuangan</h2>
            <p class="text-sm text-gray-500 mt-1">Transaksi akan berstatus Menunggu Verifikasi sampai diproses petugas Keuangan.</p>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.donation-transfers.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mutasi <span class="text-red-500">*</span></label>
                    <input type="date" name="transfer_date" value="{{ old('transfer_date', now()->format('Y-m-d')) }}" required class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Mutasi (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount') }}" min="1" step="0.01" required class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dari Akun Donasi <span class="text-red-500">*</span></label>
                <select id="from_account_id" name="from_account_id" required class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    <option value="">-- Pilih Akun Donasi --</option>
                    @foreach($donationAccounts as $account)
                        <option value="{{ $account->id }}"
                                data-balance="{{ $availableBalances[$account->id] ?? 0 }}"
                                @selected(old('from_account_id') == $account->id)>
                            {{ $account->name }} — Saldo tersedia: Rp{{ number_format($availableBalances[$account->id] ?? 0, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
                <p id="selected-balance" class="mt-2 text-sm font-medium text-emerald-700"></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ke Akun Keuangan <span class="text-red-500">*</span></label>
                <select name="to_account_id" required class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    <option value="">-- Pilih Akun Keuangan --</option>
                    @foreach($financeAccounts as $account)
                        <option value="{{ $account->id }}" @selected(old('to_account_id') == $account->id)>{{ $account->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bukti Mutasi</label>
                <input type="file" name="proof_file" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                <p class="mt-1 text-xs text-gray-400">JPG, JPEG, PNG, atau PDF. Maksimal 2 MB.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="note" rows="3" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('note') }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.donation-transfers.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</a>
                <button type="submit" class="px-6 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 shadow-sm">Simpan Mutasi Dana</button>
            </div>
        </form>
    </div>
    <script>
        (() => {
            const select = document.getElementById('from_account_id');
            const output = document.getElementById('selected-balance');
            const format = value => new Intl.NumberFormat('id-ID').format(Number(value));
            const update = () => {
                const option = select.options[select.selectedIndex];
                output.textContent = option?.dataset.balance === undefined ? '' : `Saldo tersedia untuk akun ini: Rp${format(option.dataset.balance)}`;
            };
            select.addEventListener('change', update);
            update();
        })();
    </script>
</x-admin-layout>
