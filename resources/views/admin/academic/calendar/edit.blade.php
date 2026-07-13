<x-admin-layout>
    @push('styles')
    <style>
        .academic-event-form-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 1.25rem;
            align-items: start;
        }

        .academic-event-form-main {
            min-width: 0;
            padding-bottom: 100px;
        }

        .academic-event-preview {
            min-width: 0;
            position: sticky;
            top: 1.5rem;
        }

        @media (max-width: 1023px) {
            .academic-event-form-layout {
                grid-template-columns: minmax(0, 1fr);
            }

            .academic-event-preview {
                position: static;
            }
        }
    </style>
    @endpush
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Edit Kegiatan Kalender</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $currentAcademicYear?->name ?? '—' }}</p>
            </div>
            <a href="{{ route('admin.akademik.kalender.index', ['academic_year_id' => $academicCalendarEvent->academic_year_id]) }}"
               class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Kalender
            </a>
        </div>

        <form method="POST" action="{{ route('admin.akademik.kalender.update', $academicCalendarEvent) }}" class="contents">
            @csrf
            @method('PUT')
            <input type="hidden" name="academic_year_id" value="{{ old('academic_year_id', $academicCalendarEvent->academic_year_id) }}">
            @error('academic_year_id')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror

            @include('admin.academic.calendar._form', ['event' => $academicCalendarEvent])

            {{-- STICKY ACTION BAR --}}
            <div class="sticky bottom-0 z-10 mt-8 border-t border-slate-200 bg-white/95 px-0 py-4 backdrop-blur">
                <div class="flex flex-wrap items-center justify-end gap-3">
                    <a href="{{ route('admin.akademik.kalender.index', ['academic_year_id' => $academicCalendarEvent->academic_year_id]) }}"
                       class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit" name="status" value="draft"
                            class="rounded-lg border border-emerald-300 bg-emerald-50 px-5 py-2.5 text-sm font-medium text-emerald-700 hover:bg-emerald-100 transition">
                        Simpan Draft
                    </button>
                    <button type="submit" name="status" value="published"
                            class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 transition">
                        Publikasikan
                    </button>
                    <button type="submit" name="status" value="archived"
                            class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 transition"
                            onclick="return confirm('Arsipkan kegiatan kalender ini?')">
                        Arsipkan
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-admin-layout>
