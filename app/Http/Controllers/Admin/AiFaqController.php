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
        $category = $request->input('category');
        $status = $request->input('status');

        $faqs = AiFaq::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('question', 'like', "%{$search}%")
                      ->orWhere('answer', 'like', "%{$search}%")
                      ->orWhere('keywords', 'like', "%{$search}%")
                      ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->when($category, function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->when($status === 'active', function ($query) {
                $query->where('is_active', true);
            })
            ->when($status === 'inactive', function ($query) {
                $query->where('is_active', false);
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        $categories = AiFaq::select('category')->distinct()->whereNotNull('category')->orderBy('category')->pluck('category');

        return view('admin.ai-faqs.index', compact('faqs', 'search', 'category', 'status', 'categories'));
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
            'keywords' => ['nullable', 'string', 'max:500'],
            'category' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        AiFaq::create([
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'keywords' => $validated['keywords'] ?? null,
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
            'keywords' => ['nullable', 'string', 'max:500'],
            'category' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $aiFaq->update([
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'keywords' => $validated['keywords'] ?? null,
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
