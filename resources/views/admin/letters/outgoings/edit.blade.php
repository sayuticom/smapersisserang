<x-admin-layout>
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Edit Draft Surat Keluar</h2>
                <p class="mt-1 text-sm text-gray-500">Perubahan hanya tersedia untuk status draft.</p>
            </div>
            <a href="{{ route('admin.letters.outgoings.show', $letterOutgoing) }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                &larr; Detail
            </a>
        </div>

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

        <form method="POST" action="{{ route('admin.letters.outgoings.update', $letterOutgoing) }}" class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')
            @include('admin.letters.outgoings._form', [
                'letterOutgoing' => $letterOutgoing,
                'recipientRows' => collect(old('recipients', $recipientRows->map(fn($row) => [
                    'recipient_name' => $row->recipient_name,
                    'recipient_institution' => $row->recipient_institution,
                    'recipient_address' => $row->recipient_address,
                    'recipient_phone' => $row->recipient_phone,
                    'recipient_email' => $row->recipient_email,
                ])->all()))
            ])
        </form>
    </div>
</x-admin-layout>
