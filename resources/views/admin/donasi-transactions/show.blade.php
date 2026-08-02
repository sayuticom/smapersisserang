<x-admin-layout>
    <div class="mx-auto max-w-4xl space-y-4 sm:space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Bukti Penerimaan Donasi</h2>
                <p class="mt-1 text-gray-500">Bukti resmi setelah pembayaran diverifikasi admin.</p>
            </div>
            <a href="{{ route('admin.donasi-transactions.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                &larr; Donasi Masuk
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('regular_donor_message'))
            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                {{ session('regular_donor_message') }}
            </div>
        @endif

        @php
            $logoUrl = $schoolSetting?->logo_path
                ? \Illuminate\Support\Facades\Storage::url($schoolSetting->logo_path)
                : asset('favicon.png');
        @endphp

        <div class="mx-auto max-w-md">
            <button type="button" id="copyReceiptBtn"
                    class="mb-4 inline-flex w-full items-center justify-center rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">
                Salin Gambar Bukti
            </button>

            <div id="receipt-card" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-start gap-3">
                    <img src="{{ $logoUrl }}"
                         class="h-16 w-16 shrink-0 rounded-xl border bg-white object-contain p-2"
                         alt="Logo SMA Persis Serang"
                         crossorigin="anonymous">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold uppercase tracking-wide text-emerald-700">SMA PERSIS SERANG</p>
                        <h3 class="mt-1 text-xl font-bold leading-tight text-slate-900">Bukti Penerimaan Donasi Pendidikan</h3>
                        <p class="mt-2 block text-base leading-normal text-slate-500">Nomor Bukti: {{ $receipt['receipt_number'] }}</p>
                    </div>
                </div>

                <div class="receipt-separator"></div>

                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Tanggal Penerimaan</dt>
                        <dd class="text-right font-semibold text-slate-900">{{ $receipt['received_at']->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Nama Donatur</dt>
                        <dd class="text-right font-semibold text-slate-900">{{ $receipt['donor_name'] }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Nomor WhatsApp</dt>
                        <dd class="text-right font-semibold text-slate-900">{{ $receipt['donor_whatsapp'] }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Nominal Donasi</dt>
                        <dd class="text-right font-semibold text-slate-900">Rp{{ number_format($receipt['nominal_amount'], 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Biaya Admin</dt>
                        <dd class="text-right font-semibold text-slate-900">Rp{{ number_format($receipt['admin_fee'], 0, ',', '.') }}</dd>
                    </div>
                    @if($receipt['payment_method'] && $receipt['payment_method'] !== '-')
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500">Metode Pembayaran</dt>
                            <dd class="text-right font-semibold text-slate-900">{{ $receipt['payment_method'] }}</dd>
                        </div>
                    @endif
                    @if(strtolower($receipt['unique_code']) !== 'tidak digunakan')
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500">Kode Unik</dt>
                            <dd class="text-right font-semibold text-slate-900">{{ $receipt['unique_code'] }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between gap-4 rounded-xl bg-emerald-50 p-3">
                        <dt class="font-semibold text-emerald-800">Total Transfer</dt>
                        <dd class="text-right text-lg font-bold text-emerald-800">Rp{{ number_format($receipt['total_transfer'], 0, ',', '.') }}</dd>
                    </div>
                    @if($receipt['note'] && $receipt['note'] !== '-')
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500">Catatan</dt>
                            <dd class="text-right font-semibold text-slate-900">{{ $receipt['note'] }}</dd>
                        </div>
                    @endif
                </dl>

                <div class="mt-4 rounded-xl bg-slate-50 p-3 text-xs leading-relaxed text-slate-600">
                    Terima kasih atas donasi Bapak/Ibu. Semoga Allah membalas dengan kebaikan yang berlipat.
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <button type="button"
                        onclick="window.print()"
                        class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Cetak
                </button>
                @if($whatsappUrl)
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
                       class="inline-flex h-11 items-center justify-center rounded-xl bg-emerald-700 px-4 text-sm font-semibold text-white hover:bg-emerald-800">
                        Kirim ke WhatsApp Donatur
                    </a>
                @endif
            </div>
        </div>

    @push('styles')
        <style>
            #receipt-card * {
                box-sizing: border-box;
            }

            #receipt-card .receipt-separator {
                display: block;
                width: 100%;
                height: 1px;
                margin-top: 20px;
                margin-bottom: 16px;
                background: #e2e8f0;
                border: 0;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const receipt = document.getElementById('receipt-card');
                const copyBtn = document.getElementById('copyReceiptBtn');

                async function makeCanvas() {
                    receipt.classList.add('capturing');

                    try {
                        return await html2canvas(receipt, {
                            scale: 2,
                            backgroundColor: '#ffffff',
                            useCORS: true,
                            scrollX: 0,
                            scrollY: 0,
                            windowWidth: document.documentElement.offsetWidth,
                            windowHeight: document.documentElement.offsetHeight
                        });
                    } finally {
                        receipt.classList.remove('capturing');
                    }
                }

                if (copyBtn) {
                    copyBtn.addEventListener('click', async function () {
                        try {
                            const canvas = await makeCanvas();

                            canvas.toBlob(async function (blob) {
                                try {
                                    await navigator.clipboard.write([
                                        new ClipboardItem({
                                            'image/png': blob
                                        })
                                    ]);

                                    alert('Gambar tanda terima berhasil disalin. Silakan paste/kirim di WhatsApp.');
                                } catch (err) {
                                    alert('Browser tidak mendukung salin gambar otomatis. Silakan gunakan fitur screenshot perangkat.');
                                }
                            }, 'image/png');
                        } catch (error) {
                            alert('Gagal membuat gambar tanda terima.');
                        }
                    });
                }
            });
        </script>
    @endpush
</x-admin-layout>
