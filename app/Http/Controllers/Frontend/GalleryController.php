<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\MangroveLocation;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /**
     * Display gallery page
     */
    public function index(Request $request)
    {
        $query = Gallery::active()
            ->with(['location', 'user'])
            ->ordered();

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by location
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $galleries = $query->paginate(12);

        // Get categories and locations for filters
        $categories = Gallery::active()
            ->select('category')
            ->distinct()
            ->pluck('category');

        $locations = MangroveLocation::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('pages.gallery.index', compact('galleries', 'categories', 'locations'));
    }

    /**
     * Display single gallery item
     */
    public function show(Gallery $gallery)
    {
        $gallery->load(['location', 'user']);

        // Get related images from same location or category
        $relatedGalleries = Gallery::active()
            ->where('id', '!=', $gallery->id)
            ->when($gallery->location_id, function ($query) use ($gallery) {
                return $query->where('location_id', $gallery->location_id);
            }, function ($query) use ($gallery) {
                return $query->where('category', $gallery->category);
            })
            ->limit(6)
            ->get();

        return view('pages.gallery.show', compact('gallery', 'relatedGalleries'));
    }
}
