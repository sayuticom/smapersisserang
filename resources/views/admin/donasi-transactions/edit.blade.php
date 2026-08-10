<x-admin-layout>
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Edit Donasi Masuk</h2>
                <p class="mt-1 text-gray-500">Perbarui data donasi tanpa mengubah referensi dan status transaksi.</p>
            </div>
            <a href="{{ route('admin.donasi-transactions.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                &larr; Donasi Masuk
            </a>
        </div>

        @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-semibold">Periksa kembali data berikut:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5 grid gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm sm:grid-cols-2">
                <div><span class="text-slate-500">Referensi:</span> <strong>{{ $transaction->order_id }}</strong></div>
                <div><span class="text-slate-500">Status:</span> <strong>{{ ucfirst($transaction->status) }}</strong></div>
            </div>

            <form action="{{ route('admin.donasi-transactions.update', $transaction) }}" method="POST" class="space-y-5">
                @csrf @method('PUT')
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="donor_name" class="block text-sm font-semibold text-gray-700">Nama Donatur</label>
                        <input type="text" id="donor_name" name="donor_name" required value="{{ old('donor_name', $transaction->donor_name) }}"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="donor_phone" class="block text-sm font-semibold text-gray-700">Nomor Telepon / WhatsApp</label>
                        <input type="text" id="donor_phone" name="donor_phone" value="{{ old('donor_phone', $transaction->donor_whatsapp) }}"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="donor_email" class="block text-sm font-semibold text-gray-700">Email Donatur</label>
                        <input type="email" id="donor_email" name="donor_email" value="{{ old('donor_email', $transaction->donor_email) }}"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="donation_date" class="block text-sm font-semibold text-gray-700">Tanggal Donasi</label>
                        <input type="date" id="donation_date" name="donation_date" required
                               value="{{ old('donation_date', optional($transaction->paid_at ?? $transaction->created_at)->format('Y-m-d')) }}"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="amount" class="block text-sm font-semibold text-gray-700">Nominal Donasi</label>
                        <input type="number" id="amount" name="amount" min="1" required value="{{ old('amount', $transaction->amount) }}"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="payment_method" class="block text-sm font-semibold text-gray-700">Metode Pembayaran</label>
                        <select id="payment_method" name="payment_method"
                                class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                            <option value="" @selected(old('payment_method', $transaction->payment_method) === null)>Belum Ditentukan</option>
                            @foreach(App\Enums\DonationPaymentMethod::labels() as $value => $label)
                                <option value="{{ $value }}" @selected(old('payment_method', $transaction->payment_method) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="note" class="block text-sm font-semibold text-gray-700">Catatan</label>
                        <textarea id="note" name="note" rows="5"
                                  class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">{{ old('note', $transaction->note) }}</textarea>
                    </div>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.donasi-transactions.index') }}"
                       class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">Batal</a>
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-green-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
