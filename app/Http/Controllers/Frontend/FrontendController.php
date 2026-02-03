<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\MangroveLocation;
use App\Models\LocationImage;
use App\Models\LocationDamage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FrontendController extends Controller
{
    /**
     * Check if status column exists in mangrove_locations table
     */
    private function hasStatusColumn()
    {
        return Schema::hasColumn('mangrove_locations', 'status');
    }

    /**
     * Get base query for locations
     */
    private function getLocationsQuery()
    {
        $query = MangroveLocation::query();

        // Only filter by status if column exists
        if ($this->hasStatusColumn()) {
            $query->where('status', 'active');
        }

        return $query;
    }

    /**
     * Display homepage with location list
     */
    public function index()
    {
        // Get all published locations with their details and images
        $locations = $this->getLocationsQuery()
            ->with(['details', 'images'])
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        // Get statistics
        $stats = [
            'total_locations' => $this->getLocationsQuery()->count(),
            'total_area' => $this->getLocationsQuery()->sum('area'),
            'total_images' => LocationImage::count(),
            'total_species' => $this->getTotalSpecies(),
        ];

        return view('frontend.home', compact('locations', 'stats'));
    }

    /**
     * Display location detail page
     */
    public function detail($id)
    {
        $id = decode_id($id);

        $location = MangroveLocation::with([
            'details',
            'images' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'damages' => function ($query) {
                $query->with('actions')->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);

        // Parse JSON fields
        if ($location->details) {
            $location->details->vegetation = json_decode($location->details->vegetation, true) ?? [];
            $location->details->fauna = json_decode($location->details->fauna, true) ?? [];
            $location->details->activities_detail = json_decode($location->details->activities_detail, true);
            $location->details->forest_utilization = json_decode($location->details->forest_utilization, true) ?? [];
            $location->details->programs = json_decode($location->details->programs, true) ?? [];
            $location->details->stakeholders = json_decode($location->details->stakeholders, true) ?? [];
        }

        // Get related locations
        $relatedLocations = $this->getLocationsQuery()
            ->with('images')
            ->where('id', '!=', $location->id)
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('frontend.detail', compact('location', 'relatedLocations'));
    }

    /**
     * Display all locations on map
     */
    public function locations()
    {
        $locations = $this->getLocationsQuery()
            ->with(['details', 'images'])
            ->get();

        // Group by district/city for filtering
        $districts = $this->getLocationsQuery()
            ->select('district')
            ->distinct()
            ->orderBy('district')
            ->pluck('district');

        return view('frontend.locations', compact('locations', 'districts'));
    }

    /**
     * Display gallery page
     */
    public function gallery()
    {
        $query = LocationImage::with('location');

        // Filter by active status if column exists
        if ($this->hasStatusColumn()) {
            $query->whereHas('location', function ($q) {
                $q->where('status', 'active');
            });
        }

        $images = $query->orderBy('created_at', 'desc')->paginate(12);

        // Get images grouped by location
        $locationImagesQuery = LocationImage::with('location');

        if ($this->hasStatusColumn()) {
            $locationImagesQuery->whereHas('location', function ($q) {
                $q->where('status', 'active');
            });
        }

        $locationImages = $locationImagesQuery->get()->groupBy('location_id');

        return view('frontend.gallery', compact('images', 'locationImages'));
    }

    /**
     * Display monitoring/damage reports page
     */
    public function monitoring()
    {
        $query = LocationDamage::with(['location', 'actions']);

        // Filter by active locations if status column exists
        if ($this->hasStatusColumn()) {
            $query->whereHas('location', function ($q) {
                $q->where('status', 'active');
            });
        }

        $damages = $query->orderBy('created_at', 'desc')->paginate(10);

        // Statistics
        $stats = [
            'total_damages' => LocationDamage::count(),
            'pending' => LocationDamage::where('status', 'pending')->count(),
            'in_progress' => LocationDamage::where('status', 'in_progress')->count(),
            'resolved' => LocationDamage::where('status', 'resolved')->count(),
        ];

        return view('frontend.monitoring', compact('damages', 'stats'));
    }

    /**
     * Display about page
     */
    public function about()
    {
        $stats = [
            'total_locations' => $this->getLocationsQuery()->count(),
            'total_area' => $this->getLocationsQuery()->sum('area'),
            'total_images' => LocationImage::count(),
            'total_species' => $this->getTotalSpecies(),
            'total_damages' => LocationDamage::count(),
            'resolved_damages' => LocationDamage::where('status', 'resolved')->count(),
        ];

        return view('frontend.about', compact('stats'));
    }

    /**
     * Display contact page
     */
    public function contact()
    {
        return view('frontend.contact');
    }

    /**
     * Search locations
     */
    public function search(Request $request)
    {
        $query = $request->input('q');

        $locations = $this->getLocationsQuery()
            ->with(['details', 'images'])
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('address', 'like', "%{$query}%")
                    ->orWhere('district', 'like', "%{$query}%")
                    ->orWhere('city', 'like', "%{$query}%")
                    ->orWhere('province', 'like', "%{$query}%");
            })
            ->paginate(9);

        return view('frontend.search', compact('locations', 'query'));
    }

    /**
     * API: Get locations as JSON (for map)
     */
    public function apiLocations()
    {
        $locations = $this->getLocationsQuery()
            ->with('details')
            ->get()
            ->map(function ($location) {
                return [
                    'id' => encode_id($location->id),
                    'name' => $location->name,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'area' => $location->area,
                    'district' => $location->district,
                    'city' => $location->city,
                    'province' => $location->province,
                    'detail_url' => route('frontend.detail', encode_id($location->id)),
                ];
            });

        return response()->json($locations);
    }

    /**
     * API: Get location statistics
     */
    public function apiStats()
    {
        $stats = [
            'total_locations' => $this->getLocationsQuery()->count(),
            'total_area' => $this->getLocationsQuery()->sum('area'),
            'total_images' => LocationImage::count(),
            'total_species' => $this->getTotalSpecies(),
            'total_damages' => LocationDamage::count(),
            'pending_damages' => LocationDamage::where('status', 'pending')->count(),
            'in_progress_damages' => LocationDamage::where('status', 'in_progress')->count(),
            'resolved_damages' => LocationDamage::where('status', 'resolved')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Helper: Calculate total unique species
     */
    private function getTotalSpecies()
    {
        $allVegetation = [];
        $allFauna = [];

        $details = DB::table('location_details')->get();

        foreach ($details as $detail) {
            $vegetation = json_decode($detail->vegetation, true) ?? [];
            $fauna = json_decode($detail->fauna, true) ?? [];

            $allVegetation = array_merge($allVegetation, $vegetation);
            $allFauna = array_merge($allFauna, $fauna);
        }

        return count(array_unique($allVegetation)) + count(array_unique($allFauna));
    }
}
