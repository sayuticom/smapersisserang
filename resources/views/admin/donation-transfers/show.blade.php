<x-admin-layout>
    @php
        $statusClass = match($donationTransfer->status) {
            'approved' => 'bg-emerald-100 text-emerald-700',
            'rejected' => 'bg-red-100 text-red-700',
            default => 'bg-amber-100 text-amber-700',
        };
        $statusLabel = match($donationTransfer->status) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Menunggu Verifikasi',
        };
        $isFinanceOfficer = auth()->user()->isFinanceOfficer();
        $isCreator = (int) $donationTransfer->requested_by === (int) auth()->id();
        $canApprove = $isFinanceOfficer && ! $isCreator && auth()->user()->hasPermissionTo('donation.transfers.approve');
        $canReject = $isFinanceOfficer && ! $isCreator && auth()->user()->hasPermissionTo('donation.transfers.reject');
    @endphp

    <div class="max-w-4xl mx-auto space-y-6" x-data="{ rejectOpen: false }">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <a href="{{ route('admin.donation-transfers.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Kembali</a>
                <h2 class="text-2xl font-bold text-gray-900 mt-2">Mutasi Dana Donasi</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $donationTransfer->transfer_number }}</p>
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
                <div><dt class="text-gray-500">Tanggal Mutasi</dt><dd class="mt-1 font-medium text-gray-900">{{ $donationTransfer->transfer_date?->format('d/m/Y') ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Nominal</dt><dd class="mt-1 font-bold text-emerald-700">Rp {{ number_format($donationTransfer->amount, 0, ',', '.') }}</dd></div>
                <div><dt class="text-gray-500">Dari Akun Donasi</dt><dd class="mt-1 font-medium text-gray-900">{{ $donationTransfer->fromAccount?->name ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Ke Akun Keuangan</dt><dd class="mt-1 font-medium text-gray-900">{{ $donationTransfer->toAccount?->name ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Bukti Mutasi</dt><dd class="mt-1">@if($donationTransfer->proof_file)<a href="{{ asset('storage/'.$donationTransfer->proof_file) }}" target="_blank" class="font-medium text-blue-600 hover:underline">Lihat Bukti</a>@else<span class="text-gray-400">Tidak ada</span>@endif</dd></div>
                <div><dt class="text-gray-500">Diinput oleh</dt><dd class="mt-1 text-gray-900">{{ $donationTransfer->requester?->name ?? 'Pengguna tidak tersedia' }}</dd></div>
                <div class="sm:col-span-2"><dt class="text-gray-500">Keterangan</dt><dd class="mt-1 text-gray-900 whitespace-pre-line">{{ $donationTransfer->note ?: '-' }}</dd></div>
                <div><dt class="text-gray-500">Waktu diinput</dt><dd class="mt-1 text-gray-900">{{ $donationTransfer->created_at->format('d/m/Y H:i') }}</dd></div>
            </dl>
        </div>

        @if($donationTransfer->status === 'approved')
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 text-sm">
                <h3 class="font-semibold text-emerald-800">Informasi Persetujuan</h3>
                <p class="mt-2 text-emerald-700">Disetujui oleh {{ $donationTransfer->approver?->name ?? '-' }} pada {{ $donationTransfer->approved_at?->format('d/m/Y H:i') }}.</p>
            </div>
        @elseif($donationTransfer->status === 'rejected')
            <div class="rounded-xl border border-red-200 bg-red-50 p-5 text-sm">
                <h3 class="font-semibold text-red-800">Informasi Penolakan</h3>
                <p class="mt-2 text-red-700">Ditolak oleh {{ $donationTransfer->rejector?->name ?? '-' }} pada {{ $donationTransfer->rejected_at?->format('d/m/Y H:i') }}.</p>
                <p class="mt-2 text-red-800"><strong>Alasan:</strong> {{ $donationTransfer->rejection_reason }}</p>
            </div>
        @endif

        @if($donationTransfer->status === 'pending' && $isCreator)
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 text-sm">
                <p class="font-medium text-amber-800">Menunggu verifikasi dari bagian Keuangan.</p>
            </div>
        @elseif($donationTransfer->status === 'pending' && ($canApprove || $canReject))
            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <h3 class="text-sm font-semibold text-gray-700">Tindakan Verifikasi</h3>
                <p class="mt-1 text-xs text-gray-500">Periksa bukti dan data mutasi sebelum memutuskan.</p>
                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:justify-end">
                    @if($canReject)
                        <button type="button" @click="rejectOpen = true" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-red-200 bg-red-50 px-5 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-100 sm:w-auto">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Tolak
                        </button>
                    @endif
                    @if($canApprove)
                        <form method="POST" action="{{ route('admin.donation-transfers.approve', $donationTransfer) }}" onsubmit="return confirm('Setujui transaksi Mutasi Dana ini?')" class="w-full sm:w-auto">
                            @csrf
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 px-6 py-2.5 text-sm font-bold text-white shadow-md ring-2 ring-emerald-300 hover:bg-emerald-700">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Setujui / ACC
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif

        @if($canReject)
        <div x-show="rejectOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="rejectOpen = false"></div>
            <div class="relative w-full max-w-lg rounded-xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-gray-900">Tolak Mutasi Dana</h3>
                <p class="mt-1 text-sm text-gray-500">Alasan penolakan wajib diisi.</p>
                <form method="POST" action="{{ route('admin.donation-transfers.reject', $donationTransfer) }}" class="mt-4">
                    @csrf
                    <textarea name="rejection_reason" rows="4" required maxlength="2000" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm" placeholder="Tuliskan alasan penolakan...">{{ old('rejection_reason') }}</textarea>
                    <div class="mt-4 flex justify-end gap-3">
                        <button type="button" @click="rejectOpen = false" class="px-4 py-2 rounded-lg bg-gray-100 text-sm font-medium text-gray-700">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-sm font-semibold text-white hover:bg-red-700">Tolak Transaksi</button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
</x-admin-layout>
