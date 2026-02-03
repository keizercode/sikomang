<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display a listing of published articles
     */
    public function index(Request $request)
    {
        $query = Article::published()
            ->with('user')
            ->latest('published_at');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('content', 'LIKE', "%{$search}%")
                    ->orWhere('excerpt', 'LIKE', "%{$search}%");
            });
        }

        // Filter by category (if you add category later)
        if ($request->filled('category')) {
            // Add category filter when implemented
        }

        $articles = $query->paginate(9);

        return view('pages.articles.index', compact('articles'));
    }

    /**
     * Display the specified article
     */
    public function show($slug)
    {
        $article = Article::where('slug', $slug)
            ->published()
            ->with('user')
            ->firstOrFail();

        // Increment views
        $article->incrementViews();

        // Get related articles
        $relatedArticles = Article::published()
            ->where('id', '!=', $article->id)
            ->when($article->is_featured, function ($query) {
                return $query->featured();
            })
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('pages.articles.show', compact('article', 'relatedArticles'));
    }
}
