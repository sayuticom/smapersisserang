<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LetterType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LetterTypeController extends Controller
{
    public function index(Request $request): View
    {
        $letterTypes = LetterType::query()
            ->withCount(['outgoingLetters', 'incomingLetters', 'templates'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.letters.types.index', [
            'letterTypes' => $letterTypes,
            'letterType' => null,
            'filters' => $request->only('search'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);
        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active');

        LetterType::query()->create($validated);

        return redirect()
            ->route('admin.letters.types.index')
            ->with('success', 'Jenis surat berhasil dibuat.');
    }

    public function edit(LetterType $letterType): View
    {
        $letterTypes = LetterType::query()
            ->withCount(['outgoingLetters', 'incomingLetters', 'templates'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.letters.types.index', [
            'letterTypes' => $letterTypes,
            'letterType' => $letterType,
            'filters' => [],
        ]);
    }

    public function update(Request $request, LetterType $letterType): RedirectResponse
    {
        $validated = $this->validatedData($request, $letterType);
        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active');

        $letterType->update($validated);

        return redirect()
            ->route('admin.letters.types.index')
            ->with('success', 'Jenis surat berhasil diperbarui.');
    }

    public function destroy(LetterType $letterType): RedirectResponse
    {
        $letterType->delete();

        return redirect()
            ->route('admin.letters.types.index')
            ->with('success', 'Jenis surat berhasil dihapus. Data lama tetap tersimpan.');
    }

    private function validatedData(Request $request, ?LetterType $letterType = null): array
    {
        return $request->validate([
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('letter_types', 'code')
                    ->ignore($letterType)
                    ->whereNull('deleted_at'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
