<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LetterSigner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LetterSignerController extends Controller
{
    public function index(): View
    {
        $signers = LetterSigner::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.letters.signers.index', [
            'signers' => $signers,
            'letterSigner' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['created_by'] = auth()->id();

        if ($request->hasFile('signature')) {
            $validated['signature_path'] = $request->file('signature')->store('letter-signatures', 'public');
        }

        LetterSigner::query()->create($validated);

        return redirect()
            ->route('admin.letters.signers.index')
            ->with('success', 'Penandatangan berhasil ditambahkan.');
    }

    public function edit(LetterSigner $letterSigner): View
    {
        $signers = LetterSigner::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.letters.signers.index', compact('signers', 'letterSigner'));
    }

    public function update(Request $request, LetterSigner $letterSigner): RedirectResponse
    {
        $validated = $this->validatedData($request);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['updated_by'] = auth()->id();

        if ($request->hasFile('signature')) {
            if ($letterSigner->signature_path) {
                Storage::disk('public')->delete($letterSigner->signature_path);
            }

            $validated['signature_path'] = $request->file('signature')->store('letter-signatures', 'public');
        }

        $letterSigner->update($validated);

        return redirect()
            ->route('admin.letters.signers.index')
            ->with('success', 'Penandatangan berhasil diperbarui.');
    }

    public function destroy(LetterSigner $letterSigner): RedirectResponse
    {
        $letterSigner->delete();

        return redirect()
            ->route('admin.letters.signers.index')
            ->with('success', 'Penandatangan berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'identity_number' => ['nullable', 'string', 'max:100'],
            'signature' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
