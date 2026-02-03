<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Display a listing of the articles with filters and search
     */
    public function index(Request $request)
    {
        $query = Article::with('user')->latest();

        // Search by title or content
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('content', 'LIKE', "%{$search}%")
                    ->orWhere('excerpt', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by featured
        if ($request->filled('is_featured')) {
            $query->where('is_featured', $request->is_featured);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Paginate results (preserve query parameters)
        $articles = $query->paginate(10)->withQueryString();

        // Get all statuses for filter
        $statuses = ['draft', 'published', 'archived'];

        return view('admin.articles.index', [
            'title' => 'Daftar Artikel',
            'articles' => $articles,
            'statuses' => $statuses
        ]);
    }

    /**
     * Show the form for creating a new article
     */
    public function create()
    {
        return view('admin.articles.create', [
            'title' => 'Tambah Artikel Baru'
        ]);
    }

    /**
     * Store a newly created article
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'excerpt' => 'nullable|max:500',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable|max:500',
            'meta_keywords' => 'nullable|max:255',
        ]);

        // Auto-generate slug from title
        $validated['slug'] = Str::slug($validated['title']);

        // Check if slug exists, make it unique
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Article::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $imageName = time() . '_' . Str::slug($validated['title']) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('articles', $imageName, 'public');
            $validated['featured_image'] = $imagePath;
        }

        // Set user_id
        $validated['user_id'] = Auth::id();

        // Convert is_featured checkbox
        $validated['is_featured'] = $request->has('is_featured') ? true : false;

        // Set published_at if status is published and date not set
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        // Create article
        $article = Article::create($validated);

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artikel berhasil ditambahkan');
    }

    /**
     * Display the specified article
     */
    public function show(Article $article)
    {
        $article->load('user');

        return view('admin.articles.show', [
            'title' => 'Detail Artikel',
            'article' => $article
        ]);
    }

    /**
     * Show the form for editing the specified article
     */
    public function edit(Article $article)
    {
        return view('admin.articles.edit', [
            'title' => 'Edit Artikel',
            'article' => $article
        ]);
    }

    /**
     * Update the specified article
     */
    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'excerpt' => 'nullable|max:500',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable|max:500',
            'meta_keywords' => 'nullable|max:255',
        ]);

        // Update slug if title changed
        if ($validated['title'] !== $article->title) {
            $validated['slug'] = Str::slug($validated['title']);

            // Check if slug exists (excluding current article)
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Article::where('slug', $validated['slug'])->where('id', '!=', $article->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($article->featured_image && Storage::disk('public')->exists($article->featured_image)) {
                Storage::disk('public')->delete($article->featured_image);
            }

            $image = $request->file('featured_image');
            $imageName = time() . '_' . Str::slug($validated['title']) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('articles', $imageName, 'public');
            $validated['featured_image'] = $imagePath;
        }

        // Convert is_featured checkbox
        $validated['is_featured'] = $request->has('is_featured') ? true : false;

        // Set published_at if status changed to published and date not set
        if ($validated['status'] === 'published' && $article->status !== 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        // Update article
        $article->update($validated);

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artikel berhasil diperbarui');
    }

    /**
     * Remove the specified article (soft delete)
     */
    public function destroy(Article $article)
    {
        // Delete featured image if exists
        if ($article->featured_image && Storage::disk('public')->exists($article->featured_image)) {
            Storage::disk('public')->delete($article->featured_image);
        }

        // Soft delete
        $article->delete();

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dihapus');
    }

    /**
     * Toggle featured status via AJAX
     */
    public function toggleFeatured(Article $article)
    {
        try {
            $article->is_featured = !$article->is_featured;
            $article->save();

            return response()->json([
                'success' => true,
                'message' => $article->is_featured
                    ? 'Artikel berhasil di-featured'
                    : 'Artikel berhasil di-unfeatured',
                'is_featured' => $article->is_featured
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status featured: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Publish a draft article
     */
    public function publish(Article $article)
    {
        if ($article->status !== 'draft') {
            return redirect()
                ->back()
                ->with('error', 'Artikel sudah dipublikasikan atau diarsipkan');
        }

        $article->update([
            'status' => 'published',
            'published_at' => now()
        ]);

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dipublikasikan');
    }

    /**
     * Unpublish an article
     */
    public function unpublish(Article $article)
    {
        if ($article->status !== 'published') {
            return redirect()
                ->back()
                ->with('error', 'Artikel tidak dalam status published');
        }

        $article->update([
            'status' => 'draft',
            'published_at' => null
        ]);

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artikel berhasil di-unpublish');
    }
}
