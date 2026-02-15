<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\MangroveLocation;
use Illuminate\Http\Request;

class GeoJsonApiController extends Controller
{
    /**
     * Get all locations as GeoJSON FeatureCollection
     */
    public function getAllGeoJson(Request $request)
    {
        $query = MangroveLocation::active()->with(['images', 'damages']);

        // Filter by density if specified
        if ($request->has('density')) {
            $query->where('density', $request->density);
        }

        // Filter by region if specified
        if ($request->has('region')) {
            $query->where('region', 'like', '%' . $request->region . '%');
        }

        // Get locations with geometry
        $locations = $query->get();

        // Build GeoJSON FeatureCollection
        $featureCollection = MangroveLocation::toGeoJsonFeatureCollection($locations);

        return response()->json($featureCollection);
    }

    /**
     * Get GeoJSON for specific density
     */
    public function getByDensity(string $density)
    {
        $locations = MangroveLocation::active()
            ->byDensity($density)
            ->with(['images', 'damages'])
            ->get();

        $featureCollection = MangroveLocation::toGeoJsonFeatureCollection($locations);

        return response()->json($featureCollection);
    }

    /**
     * Get GeoJSON for locations within bounding box
     */
    public function getWithinBounds(Request $request)
    {
        $request->validate([
            'minLat' => 'required|numeric',
            'minLng' => 'required|numeric',
            'maxLat' => 'required|numeric',
            'maxLng' => 'required|numeric',
        ]);

        $locations = MangroveLocation::active()
            ->withinBounds(
                $request->minLat,
                $request->minLng,
                $request->maxLat,
                $request->maxLng
            )
            ->with(['images', 'damages'])
            ->get();

        $featureCollection = MangroveLocation::toGeoJsonFeatureCollection($locations);

        return response()->json($featureCollection);
    }

    /**
     * Get single location as GeoJSON Feature
     */
    public function getSingleFeature($id)
    {
        $location = MangroveLocation::active()
            ->with(['images', 'damages', 'details'])
            ->findOrFail($id);

        return response()->json($location->geojson_feature);
    }
}
