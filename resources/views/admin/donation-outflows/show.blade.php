<x-admin-layout>
    @php
        $statusClass = match($donationOutflow->status) {
            'approved' => 'bg-emerald-100 text-emerald-700',
            'rejected' => 'bg-red-100 text-red-700',
            default => 'bg-amber-100 text-amber-700',
        };
        $statusLabel = match($donationOutflow->status) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Menunggu Verifikasi',
        };
        $canApprove = auth()->user()->hasPermissionTo('donation.outflows.approve');
        $canReject = auth()->user()->hasPermissionTo('donation.outflows.reject');
    @endphp

    <div class="max-w-4xl mx-auto space-y-6" x-data="{ rejectOpen: false }">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <a href="{{ route('admin.donation-outflows.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Kembali</a>
                <h2 class="text-2xl font-bold text-gray-900 mt-2">Penyerahan Dana Donasi ke Keuangan</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $donationOutflow->transaction_number }}</p>
            </div>
            <span class="inline-flex self-start rounded-full px-3 py-1.5 text-sm font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5 text-sm">
                <div><dt class="text-gray-500">Tanggal Penyerahan</dt><dd class="mt-1 font-medium text-gray-900">{{ $donationOutflow->handover_date->format('d/m/Y') }}</dd></div>
                <div><dt class="text-gray-500">Sumber/Jenis Donasi</dt><dd class="mt-1 font-medium text-gray-900">{{ $donationOutflow->donation_source }}</dd></div>
                <div><dt class="text-gray-500">Nominal</dt><dd class="mt-1 font-bold text-emerald-700">Rp {{ number_format($donationOutflow->amount, 0, ',', '.') }}</dd></div>
                <div><dt class="text-gray-500">Metode</dt><dd class="mt-1 font-medium text-gray-900">{{ $donationOutflow->handover_method === 'cash' ? 'Tunai' : 'Transfer' }}</dd></div>
                <div><dt class="text-gray-500">Kas/Rekening Tujuan</dt><dd class="mt-1 font-medium text-gray-900">{{ $donationOutflow->destination_account }}</dd></div>
                <div><dt class="text-gray-500">Bukti Penyerahan</dt><dd class="mt-1">@if($donationOutflow->proof_file)<a href="{{ asset('storage/'.$donationOutflow->proof_file) }}" target="_blank" class="font-medium text-blue-600 hover:underline">Lihat Bukti</a>@else<span class="text-gray-400">Tidak ada</span>@endif</dd></div>
                <div class="sm:col-span-2"><dt class="text-gray-500">Keterangan/Periode</dt><dd class="mt-1 text-gray-900 whitespace-pre-line">{{ $donationOutflow->description ?: '-' }}</dd></div>
                <div class="sm:col-span-2"><dt class="text-gray-500">Catatan</dt><dd class="mt-1 text-gray-900 whitespace-pre-line">{{ $donationOutflow->notes ?: '-' }}</dd></div>
                <div><dt class="text-gray-500">Dibuat oleh</dt><dd class="mt-1 text-gray-900">{{ $donationOutflow->creator?->name ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Waktu dibuat</dt><dd class="mt-1 text-gray-900">{{ $donationOutflow->created_at->format('d/m/Y H:i') }}</dd></div>
            </dl>
        </div>

        @if($donationOutflow->status === 'approved')
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 text-sm">
                <h3 class="font-semibold text-emerald-800">Informasi Persetujuan</h3>
                <p class="mt-2 text-emerald-700">Disetujui oleh {{ $donationOutflow->approver?->name ?? '-' }} pada {{ $donationOutflow->approved_at?->format('d/m/Y H:i') }}.</p>
                @if($donationOutflow->financeIncome)
                    <p class="mt-1 text-emerald-700">Pemasukan Keuangan #{{ $donationOutflow->financeIncome->id }} telah terbentuk.</p>
                @endif
            </div>
        @elseif($donationOutflow->status === 'rejected')
            <div class="rounded-xl border border-red-200 bg-red-50 p-5 text-sm">
                <h3 class="font-semibold text-red-800">Informasi Penolakan</h3>
                <p class="mt-2 text-red-700">Ditolak oleh {{ $donationOutflow->rejector?->name ?? '-' }} pada {{ $donationOutflow->rejected_at?->format('d/m/Y H:i') }}.</p>
                <p class="mt-2 text-red-800"><strong>Alasan:</strong> {{ $donationOutflow->rejection_reason }}</p>
            </div>
        @endif

        @if($donationOutflow->status === 'pending' && ($canApprove || $canReject))
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                @if($canReject)
                    <button type="button" @click="rejectOpen = true" class="px-5 py-2.5 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-700">Tolak</button>
                @endif
                @if($canApprove)
                    <form method="POST" action="{{ route('admin.donation-outflows.approve', $donationOutflow) }}" onsubmit="return confirm('Setujui transaksi ini dan buat Pemasukan Keuangan?')">
                        @csrf
                        <button type="submit" class="w-full px-5 py-2.5 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">Setujui</button>
                    </form>
                @endif
            </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="font-semibold text-gray-900">Riwayat Status</h3>
            <div class="mt-4 space-y-4">
                @foreach($donationOutflow->statusHistories as $history)
                    <div class="flex gap-3 text-sm">
                        <div class="mt-1.5 h-2.5 w-2.5 rounded-full bg-emerald-500 flex-shrink-0"></div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $history->from_status ?: 'Belum ada status' }} &rarr; {{ $history->to_status }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $history->created_at->format('d/m/Y H:i') }} · {{ $history->changedBy?->name ?? 'Sistem' }}</p>
                            @if($history->reason)<p class="text-gray-700 mt-1">{{ $history->reason }}</p>@endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div x-show="rejectOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="rejectOpen = false"></div>
            <div class="relative w-full max-w-lg rounded-xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-gray-900">Tolak Donasi Keluar</h3>
                <p class="mt-1 text-sm text-gray-500">Alasan penolakan wajib diisi.</p>
                <form method="POST" action="{{ route('admin.donation-outflows.reject', $donationOutflow) }}" class="mt-4">
                    @csrf
                    <textarea name="rejection_reason" rows="4" required maxlength="2000" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm" placeholder="Tuliskan alasan penolakan...">{{ old('rejection_reason') }}</textarea>
                    <div class="mt-4 flex justify-end gap-3">
                        <button type="button" @click="rejectOpen = false" class="px-4 py-2 rounded-lg bg-gray-100 text-sm font-medium text-gray-700">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-sm font-semibold text-white hover:bg-red-700">Tolak Transaksi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
