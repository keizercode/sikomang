<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Display a listing of articles
     */
    public function index(Request $request)
    {
        $data['title'] = 'Manajemen Artikel';

        $query = Article::with('user');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $data['articles'] = $query->orderBy('created_at', 'desc')->paginate(10);
        $data['statuses'] = ['draft', 'published', 'archived'];

        return view('admin.articles.index', $data);
    }

    /**
     * Show the form for creating a new article
     */
    public function create()
    {
        $data['title'] = 'Tambah Artikel Baru';
        $data['statuses'] = ['draft', 'published', 'archived'];

        return view('admin.articles.create', $data);
    }

    /**
     * Store a newly created article
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        // Generate slug
        $validated['slug'] = Str::slug($validated['title']);

        // Handle published_at
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        // Handle image upload
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $filename = time() . '_' . Str::slug($validated['title']) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('articles', $filename, 'public');
            $validated['featured_image'] = $path;
        }

        $article = Article::create($validated);

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artikel berhasil ditambahkan!');
    }

    /**
     * Display the specified article
     */
    public function show(Article $article)
    {
        $data['title'] = 'Detail Artikel';
        $data['article'] = $article->load('user');

        return view('admin.articles.show', $data);
    }

    /**
     * Show the form for editing the specified article
     */
    public function edit(Article $article)
    {
        $data['title'] = 'Edit Artikel';
        $data['article'] = $article;
        $data['statuses'] = ['draft', 'published', 'archived'];

        return view('admin.articles.edit', $data);
    }

    /**
     * Update the specified article
     */
    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        // Handle published_at
        if ($validated['status'] === 'published' && empty($validated['published_at']) && $article->published_at === null) {
            $validated['published_at'] = now();
        }

        // Handle image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }

            $image = $request->file('featured_image');
            $filename = time() . '_' . Str::slug($validated['title']) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('articles', $filename, 'public');
            $validated['featured_image'] = $path;
        }

        $article->update($validated);

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artikel berhasil diperbarui!');
    }

    /**
     * Remove the specified article
     */
    public function destroy(Article $article)
    {
        // Delete image
        if ($article->featured_image) {
            Storage::disk('public')->delete($article->featured_image);
        }

        $article->delete();

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dihapus!');
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Article $article)
    {
        $article->update(['is_featured' => !$article->is_featured]);

        return response()->json([
            'success' => true,
            'is_featured' => $article->is_featured,
            'message' => 'Status featured berhasil diubah!'
        ]);
    }

    /**
     * Publish article
     */
    public function publish(Article $article)
    {
        $article->update([
            'status' => 'published',
            'published_at' => $article->published_at ?? now()
        ]);

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dipublikasikan!');
    }

    /**
     * Unpublish article (set to draft)
     */
    public function unpublish(Article $article)
    {
        $article->update(['status' => 'draft']);

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artikel berhasil di-unpublish!');
    }
}
