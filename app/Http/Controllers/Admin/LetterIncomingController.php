<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LetterIncoming;
use App\Models\LetterType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LetterIncomingController extends Controller
{
    public const STATUSES = [
        'received' => 'Diterima',
        'reviewed' => 'Ditinjau',
        'followed_up' => 'Ditindaklanjuti',
        'completed' => 'Selesai',
        'archived' => 'Diarsipkan',
    ];

    public function index(Request $request): View
    {
        $query = LetterIncoming::query()->with(['letterType', 'creator']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('received_date')) {
            $query->whereDate('received_date', $request->received_date);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('incoming_number', 'like', "%{$search}%")
                    ->orWhere('sender', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $letters = $query
            ->orderByDesc('received_date')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.letters.incomings.index', [
            'letters' => $letters,
            'statuses' => self::STATUSES,
            'filters' => $request->only(['status', 'received_date', 'search']),
        ]);
    }

    public function create(): View
    {
        return view('admin.letters.incomings.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);
        $validated['created_by'] = auth()->id();

        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('letter-incomings', 'public');
        }

        $letter = LetterIncoming::query()->create($validated);

        return redirect()
            ->route('admin.letters.incomings.show', $letter)
            ->with('success', 'Surat masuk berhasil dicatat.');
    }

    public function show(LetterIncoming $letterIncoming): View
    {
        $letterIncoming->load(['letterType', 'creator', 'updater']);

        return view('admin.letters.incomings.show', [
            'letterIncoming' => $letterIncoming,
            'statuses' => self::STATUSES,
        ]);
    }

    public function edit(LetterIncoming $letterIncoming): View
    {
        return view('admin.letters.incomings.edit', array_merge(
            $this->formData(),
            ['letterIncoming' => $letterIncoming]
        ));
    }

    public function update(Request $request, LetterIncoming $letterIncoming): RedirectResponse
    {
        $validated = $this->validatedData($request);
        $validated['updated_by'] = auth()->id();

        if ($request->hasFile('file')) {
            if ($letterIncoming->file_path) {
                Storage::disk('public')->delete($letterIncoming->file_path);
            }

            $validated['file_path'] = $request->file('file')->store('letter-incomings', 'public');
        }

        $letterIncoming->update($validated);

        return redirect()
            ->route('admin.letters.incomings.show', $letterIncoming)
            ->with('success', 'Surat masuk berhasil diperbarui.');
    }

    public function destroy(LetterIncoming $letterIncoming): RedirectResponse
    {
        $letterIncoming->delete();

        return redirect()
            ->route('admin.letters.incomings.index')
            ->with('success', 'Surat masuk berhasil dihapus.');
    }

    private function formData(): array
    {
        return [
            'letterTypes' => LetterType::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            'statuses' => self::STATUSES,
        ];
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'letter_type_id' => ['nullable', 'exists:letter_types,id'],
            'incoming_number' => ['required', 'string', 'max:255'],
            'sender' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'letter_date' => ['nullable', 'date'],
            'received_date' => ['required', 'date'],
            'attachment' => ['nullable', 'string', 'max:255'],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', 'in:' . implode(',', array_keys(self::STATUSES))],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
