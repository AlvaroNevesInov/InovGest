<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\VatRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Article::with('vatRate');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('active')) {
            $query->where('active', $request->active);
        }

        $articles = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('Articles/Index', [
            'articles' => $articles,
            'filters' => $request->only(['search', 'active']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vatRates = VatRate::active()->orderBy('rate')->get();

        return Inertia::render('Articles/Create', [
            'vatRates' => $vatRates,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'required|string|max:255|unique:articles,reference',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'vat_rate_id' => 'required|exists:vat_rates,id',
            'photo' => 'nullable|image|max:2048', // 2MB max
            'notes' => 'nullable|string',
            'active' => 'boolean',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('articles', 'public');
        }

        Article::create($validated);

        return redirect()->route('settings.articles.index')->with('success', 'Artigo criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        $article->load('vatRate');

        return Inertia::render('Articles/Show', [
            'article' => $article,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        $vatRates = VatRate::active()->orderBy('rate')->get();

        return Inertia::render('Articles/Edit', [
            'article' => $article,
            'vatRates' => $vatRates,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'reference' => 'required|string|max:255|unique:articles,reference,' . $article->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'vat_rate_id' => 'required|exists:vat_rates,id',
            'photo' => 'nullable|image|max:2048', // 2MB max
            'notes' => 'nullable|string',
            'active' => 'boolean',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($article->photo) {
                Storage::disk('public')->delete($article->photo);
            }
            $validated['photo'] = $request->file('photo')->store('articles', 'public');
        }

        $article->update($validated);

        return redirect()->route('settings.articles.index')->with('success', 'Artigo atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        // Delete photo if exists
        if ($article->photo) {
            Storage::disk('public')->delete($article->photo);
        }

        $article->delete();

        return redirect()->route('settings.articles.index')->with('success', 'Artigo removido com sucesso!');
    }
}
