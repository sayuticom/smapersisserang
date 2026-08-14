<x-admin-layout>
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Pengingat Donasi</h2>
                <p class="mt-1 text-gray-500">Kelola donatur yang bersedia menerima pengingat donasi melalui WhatsApp.</p>
            </div>
            <a href="{{ route('admin.donation.dashboard') }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                Dashboard Donasi
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-rose-700">Perlu Diingatkan</p>
                <p class="mt-2 text-2xl font-bold text-rose-800">{{ $dueCount }}</p>
            </div>
            <div class="rounded-2xl border border-sky-200 bg-sky-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-sky-700">Akan Datang</p>
                <p class="mt-2 text-2xl font-bold text-sky-800">{{ $upcomingCount }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Pengingat Aktif</p>
                <p class="mt-2 text-2xl font-bold text-emerald-800">{{ $activeCount }}</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <form method="GET" action="{{ route('admin.donor-reminders.index') }}" class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <label for="q" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Cari Donatur</label>
                    <input id="q" name="q" value="{{ request('q') }}" type="text"
                           class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                           placeholder="Nama atau nomor WhatsApp">
                </div>
                <div class="sm:w-48">
                    <label for="status" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Status</label>
                    <select id="status" name="status"
                            class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Semua</option>
                        <option value="enabled" @selected(request('status') === 'enabled')>Aktif</option>
                        <option value="disabled" @selected(request('status') === 'disabled')>Nonaktif</option>
                    </select>
                </div>
                <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                    Filter
                </button>
            </form>

            <div class="overflow-x-auto">
                @php
                    $formatWhatsapp = function (?string $number): string {
                        $clean = preg_replace('/[^0-9]/', '', (string) $number);

                        if ($clean === '') {
                            return '-';
                        }

                        if (str_starts_with($clean, '62')) {
                            $local = substr($clean, 2);
                            $prefix = '+62 ';
                        } elseif (str_starts_with($clean, '0')) {
                            $local = substr($clean, 1);
                            $prefix = '+62 ';
                        } else {
                            $local = $clean;
                            $prefix = '';
                        }

                        $parts = [];
                        if (strlen($local) > 0) {
                            $parts[] = substr($local, 0, 3);
                        }
                        if (strlen($local) > 3) {
                            $parts[] = substr($local, 3, 4);
                        }
                        if (strlen($local) > 7) {
                            $parts[] = substr($local, 7);
                        }

                        return $prefix.implode('-', array_filter($parts));
                    };
                @endphp
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Donatur</th>
                            <th class="px-4 py-3 text-left font-semibold">WhatsApp</th>
                            <th class="px-4 py-3 text-left font-semibold">Donasi Terakhir</th>
                            <th class="px-4 py-3 text-left font-semibold">Jadwal</th>
                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                            <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($donors as $donor)
                            @php($message = $reminderService->defaultMessage($donor))
                            @php($whatsappUrl = $reminderService->whatsappUrl($donor, $message))
                            @php($isDue = $reminderService->isDue($donor))
                            <tr x-data="{ editing: false }" class="align-top hover:bg-slate-50">
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-gray-900">{{ $donor->name }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-gray-700">
                                    {{ $formatWhatsapp($donor->whatsapp_number) }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-gray-700">
                                    {{ $donor->last_donation_at?->format('d/m/Y') ?? '-' }}
                                </td>
                                <td class="px-4 py-4">
                                    <div class="min-w-44">
                                        @if($donor->reminder_enabled)
                                            <div class="font-semibold text-slate-800">Bulanan</div>
                                            <div class="mt-0.5 text-xs text-slate-500">Tanggal {{ (int) $donor->reminder_day }}</div>
                                            <div class="mt-0.5 text-xs text-slate-400">Berikutnya: {{ $donor->next_reminder_at?->format('d/m/Y') ?? '-' }}</div>
                                        @else
                                            <div class="font-semibold text-slate-500">Nonaktif</div>
                                        @endif

                                        <div x-show="editing" x-cloak class="mt-3 rounded-lg border border-slate-200 bg-slate-50 p-3">
                                            <form method="POST" action="{{ route('admin.donor-reminders.update', $donor) }}" class="space-y-3">
                                                @csrf
                                                @method('PATCH')
                                                <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                                                    <input type="checkbox" name="reminder_enabled" value="1"
                                                           class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                                           checked>
                                                    Aktifkan Pengingat WhatsApp
                                                </label>
                                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                                    <div>
                                                        <label class="block text-xs font-semibold text-slate-500">Frekuensi</label>
                                                        <select name="reminder_frequency"
                                                                class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                                            <option value="monthly" @selected($donor->reminder_frequency === 'monthly' || $donor->reminder_frequency === null)>Bulanan</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-semibold text-slate-500">Tanggal</label>
                                                        <input name="reminder_day" value="{{ old('reminder_day', $donor->reminder_day ?: 5) }}" type="number" min="1" max="28"
                                                               class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                                    </div>
                                                </div>
                                                <div class="flex flex-wrap gap-2">
                                                    <button type="submit"
                                                            class="inline-flex items-center rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">
                                                        {{ $donor->reminder_enabled ? 'Simpan' : 'Aktifkan' }}
                                                    </button>
                                                    <button type="button" @click="editing = false"
                                                            class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                                        Batal
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    @if($donor->reminder_enabled)
                                        @if($isDue)
                                            <span class="rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-semibold text-rose-700">Perlu Diingatkan</span>
                                        @else
                                            <span class="rounded-full bg-sky-100 px-2 py-0.5 text-[10px] font-semibold text-sky-700">Akan Datang</span>
                                        @endif
                                    @else
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        @if($donor->reminder_enabled && $whatsappUrl)
                                            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
                                               class="inline-flex items-center rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100">WhatsApp</a>
                                        @endif
                                        @if($donor->reminder_enabled && $isDue)
                                            <form method="POST" action="{{ route('admin.donor-reminders.mark-reminded', $donor) }}">
                                                @csrf
                                                <input type="hidden" name="message_snapshot" value="{{ $message }}">
                                                <button type="submit"
                                                        class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                                    Tandai Sudah Diingatkan
                                                </button>
                                            </form>
                                        @endif
                                        <button type="button" @click="editing = ! editing"
                                                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                            <span x-show="! editing">{{ $donor->reminder_enabled ? 'Ubah' : 'Aktifkan Pengingat' }}</span>
                                            <span x-show="editing" x-cloak>Tutup</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">
                                    Belum ada donatur tetap.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 px-5 py-4">
                {{ $donors->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
