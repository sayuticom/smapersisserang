<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiFaq;
use Illuminate\Http\Request;

class AiFaqController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $faqs = AiFaq::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('question', 'like', "%{$search}%")
                      ->orWhere('answer', 'like', "%{$search}%")
                      ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.ai-faqs.index', compact('faqs', 'search'));
    }

    public function create()
    {
        return view('admin.ai-faqs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:5000'],
            'category' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        AiFaq::create([
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'category' => $validated['category'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.ai-faqs.index')
            ->with('success', 'FAQ AI berhasil ditambahkan.');
    }

    public function edit(AiFaq $aiFaq)
    {
        return view('admin.ai-faqs.edit', compact('aiFaq'));
    }

    public function update(Request $request, AiFaq $aiFaq)
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:5000'],
            'category' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $aiFaq->update([
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'category' => $validated['category'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.ai-faqs.index')
            ->with('success', 'FAQ AI berhasil diperbarui.');
    }

    public function destroy(AiFaq $aiFaq)
    {
        $question = $aiFaq->question;
        $aiFaq->delete();

        return redirect()->route('admin.ai-faqs.index')
            ->with('success', "FAQ \"{$question}\" berhasil dihapus.");
    }
}
