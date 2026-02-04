<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\MangroveLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class GalleryController extends Controller
{
    /**
     * Display a listing of galleries
     */
    public function index(Request $request)
    {
        $data['title'] = 'Manajemen Galeri';

        $query = Gallery::with(['user', 'location']);

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by location
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('photographer', 'like', "%{$search}%");
            });
        }

        $data['galleries'] = $query->ordered()->paginate(12);
        $data['categories'] = ['mangrove', 'kegiatan', 'lokasi', 'flora', 'fauna', 'lainnya'];
        $data['locations'] = MangroveLocation::where('is_active', true)->get();

        return view('pages.admin.galleries.index', $data);
    }

    /**
     * Show the form for creating a new gallery
     */
    public function create()
    {
        $data['title'] = 'Tambah Foto Galeri';
        $data['categories'] = ['mangrove', 'kegiatan', 'lokasi', 'flora', 'fauna', 'lainnya'];
        $data['locations'] = MangroveLocation::where('is_active', true)->get();

        return view('pages.admin.galleries.create', $data);
    }

    /**
     * Store a newly created gallery
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB
            'category' => 'required|in:mangrove,kegiatan,lokasi,flora,fauna,lainnya',
            'location_id' => 'nullable|exists:mangrove_locations,id',
            'date_taken' => 'nullable|date',
            'photographer' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();

            // Store original image
            $path = $image->storeAs('galleries', $filename, 'public');
            $validated['image_path'] = $path;

            // Create thumbnail (optional - requires intervention/image package)
            // $thumbnailFilename = 'thumb_' . $filename;
            // $thumbnailPath = storage_path('app/public/galleries/' . $thumbnailFilename);
            // Image::make($image)->fit(300, 200)->save($thumbnailPath);
            // $validated['thumbnail_path'] = 'galleries/' . $thumbnailFilename;
        }

        $gallery = Gallery::create($validated);

        return redirect()
            ->route('pages.admin.galleries.index')
            ->with('success', 'Foto berhasil ditambahkan ke galeri!');
    }

    /**
     * Display the specified gallery
     */
    public function show(Gallery $gallery)
    {
        $data['title'] = 'Detail Foto';
        $data['gallery'] = $gallery->load(['user', 'location']);

        return view('pages.admin.galleries.show', $data);
    }

    /**
     * Show the form for editing the specified gallery
     */
    public function edit(Gallery $gallery)
    {
        $data['title'] = 'Edit Foto Galeri';
        $data['gallery'] = $gallery;
        $data['categories'] = ['mangrove', 'kegiatan', 'lokasi', 'flora', 'fauna', 'lainnya'];
        $data['locations'] = MangroveLocation::where('is_active', true)->get();

        return view('pages.admin.galleries.edit', $data);
    }

    /**
     * Update the specified gallery
     */
    public function update(Request $request, Gallery $gallery)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'category' => 'required|in:mangrove,kegiatan,lokasi,flora,fauna,lainnya',
            'location_id' => 'nullable|exists:mangrove_locations,id',
            'date_taken' => 'nullable|date',
            'photographer' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old images
            if ($gallery->image_path) {
                Storage::disk('public')->delete($gallery->image_path);
            }
            if ($gallery->thumbnail_path) {
                Storage::disk('public')->delete($gallery->thumbnail_path);
            }

            $image = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();

            // Store original image
            $path = $image->storeAs('galleries', $filename, 'public');
            $validated['image_path'] = $path;
        }

        $gallery->update($validated);

        return redirect()
            ->route('pages.admin.galleries.index')
            ->with('success', 'Foto berhasil diperbarui!');
    }

    /**
     * Remove the specified gallery
     */
    public function destroy(Gallery $gallery)
    {
        // Delete images
        if ($gallery->image_path) {
            Storage::disk('public')->delete($gallery->image_path);
        }
        if ($gallery->thumbnail_path) {
            Storage::disk('public')->delete($gallery->thumbnail_path);
        }

        $gallery->delete();

        return redirect()
            ->route('pages.admin.galleries.index')
            ->with('success', 'Foto berhasil dihapus!');
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Gallery $gallery)
    {
        $gallery->update(['is_featured' => !$gallery->is_featured]);

        return response()->json([
            'success' => true,
            'is_featured' => $gallery->is_featured,
            'message' => 'Status featured berhasil diubah!'
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive(Gallery $gallery)
    {
        $gallery->update(['is_active' => !$gallery->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $gallery->is_active,
            'message' => 'Status aktif berhasil diubah!'
        ]);
    }

    /**
     * Update order/position
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:galleries,id',
            'items.*.order' => 'required|integer',
        ]);

        foreach ($request->items as $item) {
            Gallery::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan foto berhasil diperbarui!'
        ]);
    }

    /**
     * Bulk upload
     */
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'category' => 'required|in:mangrove,kegiatan,lokasi,flora,fauna,lainnya',
            'location_id' => 'nullable|exists:mangrove_locations,id',
        ]);

        $uploaded = 0;

        foreach ($request->file('images') as $image) {
            $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('galleries', $filename, 'public');

            Gallery::create([
                'user_id' => Auth::id(),
                'title' => 'Foto ' . date('Y-m-d H:i:s'),
                'image_path' => $path,
                'category' => $request->category,
                'location_id' => $request->location_id,
                'is_active' => true,
            ]);

            $uploaded++;
        }

        return redirect()
            ->route('pages.admin.galleries.index')
            ->with('success', "{$uploaded} foto berhasil diupload!");
    }
}
