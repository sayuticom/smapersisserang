<x-admin-layout>
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <a href="{{ route('admin.donation-outflows.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Kembali</a>
            <h2 class="mt-2 text-2xl font-bold text-gray-900">Edit Donasi Keluar</h2>
            <p class="mt-1 text-sm text-gray-500">Perbarui data tanpa mengubah nomor transaksi, status, atau pembuat.</p>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.donation-outflows.update', $donationOutflow) }}" enctype="multipart/form-data"
              class="space-y-5 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            @csrf @method('PUT')

            <div class="grid gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm sm:grid-cols-2">
                <div><span class="text-slate-500">Nomor:</span> <strong>{{ $donationOutflow->transaction_number }}</strong></div>
                <div><span class="text-slate-500">Status:</span> <strong>{{ ucfirst($donationOutflow->status) }}</strong></div>
            </div>

            @if($donationOutflow->status === App\Models\DonationOutflow::STATUS_APPROVED)
                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    Transaksi telah disetujui. Nominal dikunci agar Pemasukan Keuangan terkait tetap konsisten; perubahan administratif tidak mengubah atau membuat Pemasukan baru.
                </div>
            @endif

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Tanggal Penyerahan <span class="text-red-500">*</span></label>
                    <input type="date" name="handover_date" value="{{ old('handover_date', $donationOutflow->handover_date->format('Y-m-d')) }}" required
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Sumber/Jenis Donasi <span class="text-red-500">*</span></label>
                    <input type="text" name="donation_source" value="{{ old('donation_source', $donationOutflow->donation_source) }}" maxlength="255" required
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Keterangan/Periode</label>
                <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('description', $donationOutflow->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Nominal (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount', $donationOutflow->amount) }}" min="1" step="0.01" required
                           @readonly($donationOutflow->status !== App\Models\DonationOutflow::STATUS_PENDING)
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 read-only:bg-gray-100 read-only:text-gray-500">
                </div>
                <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Metode Pembayaran <span class="text-red-500">*</span></label>
                <select id="payment_method" name="payment_method" required class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @foreach(App\Enums\DonationPaymentMethod::labels() as $value => $label)
                        <option value="{{ $value }}" data-balance="{{ $availableByMethod[$value] }}" @selected(old('payment_method', $donationOutflow->payment_method) === $value)>
                            {{ $label }} — Saldo tersedia: Rp{{ number_format($availableByMethod[$value], 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
                <p id="selected-balance" class="mt-2 text-sm font-medium text-emerald-700"></p>
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Ganti Bukti Penyerahan</label>
                <input type="file" name="proof_file" accept=".jpg,.jpeg,.png,.pdf"
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                @if($donationOutflow->proof_file)
                    <p class="mt-1 text-xs text-gray-500">Kosongkan untuk mempertahankan bukti yang sudah ada.</p>
                @endif
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.donation-outflows.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</a>
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-green-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-800">Simpan Perubahan</button>
            </div>
        </form>
    </div>
    <script>
        (() => {
            const select = document.getElementById('payment_method');
            const output = document.getElementById('selected-balance');
            const format = value => new Intl.NumberFormat('id-ID').format(Number(value));
            const update = () => {
                const option = select.options[select.selectedIndex];
                output.textContent = `Saldo tersedia untuk metode ini: Rp${format(option.dataset.balance)}`;
            };
            select.addEventListener('change', update);
            update();
        })();
    </script>
</x-admin-layout>
