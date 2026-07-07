<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\LetterHtmlSanitizer;
use App\Http\Controllers\Controller;
use App\Models\LetterTemplate;
use App\Models\LetterType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LetterTemplateController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['letter_type_id', 'search']);

        $templates = LetterTemplate::query()
            ->with(['letterType', 'creator'])
            ->when($request->filled('letter_type_id'), fn ($query) => $query->where('letter_type_id', $request->letter_type_id))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('subject_template', 'like', "%{$search}%")
                        ->orWhere('body_template', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.letters.templates.index', [
            'templates' => $templates,
            'letterTypes' => $this->letterTypes(),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('admin.letters.templates.create', [
            'letterTemplate' => null,
            'letterTypes' => $this->letterTypes(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['created_by'] = auth()->id();

        $template = LetterTemplate::query()->create($validated);

        return redirect()
            ->route('admin.letters.templates.show', $template)
            ->with('success', 'Template surat berhasil dibuat.');
    }

    public function show(LetterTemplate $letterTemplate): View
    {
        $letterTemplate->load(['letterType', 'creator', 'updater']);

        return view('admin.letters.templates.show', compact('letterTemplate'));
    }

    public function edit(LetterTemplate $letterTemplate): View
    {
        return view('admin.letters.templates.edit', [
            'letterTemplate' => $letterTemplate,
            'letterTypes' => $this->letterTypes(),
        ]);
    }

    public function update(Request $request, LetterTemplate $letterTemplate): RedirectResponse
    {
        $validated = $this->validatedData($request);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['updated_by'] = auth()->id();

        $letterTemplate->update($validated);

        return redirect()
            ->route('admin.letters.templates.show', $letterTemplate)
            ->with('success', 'Template surat berhasil diperbarui.');
    }

    public function destroy(LetterTemplate $letterTemplate): RedirectResponse
    {
        $letterTemplate->delete();

        return redirect()
            ->route('admin.letters.templates.index')
            ->with('success', 'Template surat berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'letter_type_id' => ['required', 'exists:letter_types,id'],
            'title' => ['required', 'string', 'max:255'],
            'subject_template' => ['nullable', 'string', 'max:255'],
            'opening_template' => ['nullable', 'string'],
            'body_template' => ['required', 'string'],
            'closing_template' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['opening_template'] = LetterHtmlSanitizer::sanitize($data['opening_template'] ?? null);
        $data['body_template'] = LetterHtmlSanitizer::sanitize($data['body_template']);
        $data['closing_template'] = LetterHtmlSanitizer::sanitize($data['closing_template'] ?? null);

        return $data;
    }

    private function letterTypes()
    {
        return LetterType::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }
}
