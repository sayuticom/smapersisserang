<x-admin-layout>
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Buat Bukti Penerimaan</h2>
                <p class="mt-1 text-gray-500">Paste pesan WhatsApp donatur, cek mutasi pembayaran, lalu terbitkan bukti resmi.</p>
            </div>
            <a href="{{ route('admin.donasi-transactions.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                &larr; Donasi Masuk
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif

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
            <form action="{{ route('admin.donasi-transactions.parse-receipt') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="confirmation_message" class="block text-sm font-semibold text-gray-700">Pesan WhatsApp Konfirmasi</label>
                    <textarea id="confirmation_message"
                              name="confirmation_message"
                              rows="10"
                              class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600"
                              placeholder="Paste pesan WhatsApp konfirmasi donatur di sini...">{{ old('confirmation_message', $rawMessage) }}</textarea>
                </div>
                <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-green-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800">
                    Ambil Data
                </button>
            </form>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <form action="{{ route('admin.donasi-transactions.store-receipt') }}" method="POST" class="space-y-5">
                @csrf
                <textarea name="confirmation_message" class="hidden">{{ old('confirmation_message', $rawMessage) }}</textarea>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="donor_name" class="block text-sm font-semibold text-gray-700">Nama Donatur</label>
                        <input type="text" id="donor_name" name="donor_name"
                               value="{{ old('donor_name', $parsed['donor_name'] ?? 'Hamba Allah') }}"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="transfer_date" class="block text-sm font-semibold text-gray-700">Tanggal Transfer</label>
                        <input type="text" id="transfer_date" name="transfer_date"
                               value="{{ old('transfer_date', $parsed['transfer_date'] ?? now()->format('d/m/Y')) }}"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="allow_future_donation_contact" class="block text-sm font-semibold text-gray-700">Bersedia Dihubungi</label>
                        <select id="allow_future_donation_contact" name="allow_future_donation_contact"
                                class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                            @php($allowContactValue = old('allow_future_donation_contact', $parsed['allow_future_donation_contact'] ?? 'Tidak'))
                            <option value="Ya" {{ strtolower($allowContactValue) === 'ya' ? 'selected' : '' }}>Ya</option>
                            <option value="Tidak" {{ strtolower($allowContactValue) !== 'ya' ? 'selected' : '' }}>Tidak</option>
                        </select>
                    </div>
                    <div>
                        <label for="donor_whatsapp" class="block text-sm font-semibold text-gray-700">Nomor WhatsApp</label>
                        <input type="text" id="donor_whatsapp" name="donor_whatsapp"
                               value="{{ old('donor_whatsapp', $parsed['donor_whatsapp'] ?? '-') }}"
                               placeholder="Contoh: 0877712621100"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="nominal_amount" class="block text-sm font-semibold text-gray-700">Nominal Donasi</label>
                        <input type="text" id="nominal_amount" name="nominal_amount"
                               value="{{ old('nominal_amount', $parsed['nominal_amount'] ?? '') }}"
                               placeholder="Rp50.000"
                               oninput="updateTotalTransfer()"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="admin_fee" class="block text-sm font-semibold text-gray-700">Biaya Admin</label>
                        <input type="text" id="admin_fee" name="admin_fee"
                               value="{{ old('admin_fee', $parsed['admin_fee'] ?? '') }}"
                               placeholder="Rp300"
                               oninput="updateTotalTransfer()"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="payment_method" class="block text-sm font-semibold text-gray-700">Metode Pembayaran</label>
                        <select id="payment_method" name="payment_method"
                                class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                            @php($paymentMethodValue = old('payment_method', $parsed['payment_method'] ?? 'Transfer Bank'))
                            <option value="QRIS" {{ $paymentMethodValue === 'QRIS' ? 'selected' : '' }}>QRIS</option>
                            <option value="Transfer Bank" {{ $paymentMethodValue === 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                            <option value="Tunai" {{ $paymentMethodValue === 'Tunai' ? 'selected' : '' }}>Tunai</option>
                            <option value="Lainnya" {{ $paymentMethodValue === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    @php($useUniqueValue = old('use_unique_code', (is_numeric($parsedCodeValue = old('unique_code', $parsed['unique_code'] ?? '-')) && (int) $parsedCodeValue > 0) ? 'Ya' : 'Tidak'))
                    <div>
                        <label for="use_unique_code" class="block text-sm font-semibold text-gray-700">Gunakan Kode Unik</label>
                        <select id="use_unique_code" name="use_unique_code"
                                class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600"
                                onchange="toggleUniqueCode()">
                            <option value="Ya" {{ $useUniqueValue === 'Ya' ? 'selected' : '' }}>Ya</option>
                            <option value="Tidak" {{ $useUniqueValue === 'Tidak' ? 'selected' : '' }}>Tidak</option>
                        </select>
                    </div>
                    <div id="unique_code_wrap">
                        <label for="unique_code" class="block text-sm font-semibold text-gray-700">Kode Unik</label>
                        <input type="text" id="unique_code" name="unique_code"
                               value="{{ old('unique_code', $parsed['unique_code'] ?? '') }}"
                               placeholder="127"
                               oninput="updateTotalTransfer()"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                        <p class="mt-1 text-xs text-gray-500">Isi 001 sampai 299 (Rp1 &ndash; Rp299).</p>
                    </div>
                    <div>
                        <label for="total_transfer" class="block text-sm font-semibold text-gray-700">Total Transfer</label>
                        <input type="text" id="total_transfer"
                               value="{{ old('total_transfer', $parsed['total_transfer'] ?? '') }}"
                               placeholder="Rp50.127"
                               readonly
                               class="mt-1.5 w-full rounded-lg border-gray-300 bg-gray-50 text-sm text-gray-700 focus:border-green-600 focus:ring-green-600">
                    </div>
                    <div>
                        <label for="note" class="block text-sm font-semibold text-gray-700">Catatan</label>
                        <input type="text" id="note" name="note"
                               value="{{ old('note', $parsed['note'] ?? '-') }}"
                               class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600">
                    </div>
                </div>

                @if(strtolower(trim(old('allow_future_donation_contact', $parsed['allow_future_donation_contact'] ?? 'Tidak'))) === 'ya' && trim(old('donor_whatsapp', $parsed['donor_whatsapp'] ?? '-')) !== '' && trim(old('donor_whatsapp', $parsed['donor_whatsapp'] ?? '-')) !== '-')
                    <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        Donatur ini akan otomatis disimpan sebagai Donatur Tetap setelah bukti diterbitkan.
                    </div>
                @elseif(strtolower(trim(old('allow_future_donation_contact', $parsed['allow_future_donation_contact'] ?? 'Tidak'))) === 'ya')
                    <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        Nomor WhatsApp kosong, donatur tidak dapat disimpan sebagai Donatur Tetap.
                    </div>
                @else
                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                        Donatur tidak akan disimpan sebagai Donatur Tetap.
                    </div>
                @endif

                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-6 text-amber-800">
                    Pastikan total transfer sudah cocok dengan mutasi pembayaran sebelum menerbitkan bukti penerimaan.
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.donasi-transactions.index') }}"
                       class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-green-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800"
                            onclick="return confirm('Terbitkan bukti penerimaan? Pastikan pembayaran sudah masuk di mutasi.')">
                        Terbitkan Bukti Penerimaan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function parseRupiah(value) {
                const digits = String(value || '').replace(/[^0-9]/g, '');
                return digits ? parseInt(digits, 10) : 0;
            }

            function formatRupiah(value) {
                return 'Rp' + Number(value).toLocaleString('id-ID');
            }

            function useUniqueCodeEnabled() {
                const el = document.getElementById('use_unique_code');
                return !!el && el.value === 'Ya';
            }

            function toggleUniqueCode() {
                const enabled = useUniqueCodeEnabled();
                const wrap = document.getElementById('unique_code_wrap');
                const input = document.getElementById('unique_code');
                if (wrap) {
                    wrap.style.display = enabled ? '' : 'none';
                }
                if (input) {
                    input.disabled = !enabled;
                }
                updateTotalTransfer();
            }

            function updateTotalTransfer() {
                const nominal = parseRupiah(document.getElementById('nominal_amount')?.value);
                const adminFee = parseRupiah(document.getElementById('admin_fee')?.value);
                const unique = useUniqueCodeEnabled() ? parseRupiah(document.getElementById('unique_code')?.value) : 0;
                const totalEl = document.getElementById('total_transfer');
                if (totalEl) {
                    totalEl.value = formatRupiah(nominal + adminFee + unique);
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                toggleUniqueCode();
            });
        </script>
    @endpush
</x-admin-layout>
