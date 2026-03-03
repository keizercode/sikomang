<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\MangroveLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GeoJsonApiController extends Controller
{
    /**
     * URL sumber GeoJSON dari Plovis (hanya diakses server-side)
     */
    protected array $plovisUrls = [
        'jarang' => 'https://asset.plovis.id/plovis/public/67f25022-a757-4f90-a114-16e3f3ad671c.geojson',
        'sedang' => 'https://asset.plovis.id/plovis/public/1c7b760f-7458-4353-bfd9-1ba6084cdce6.geojson',
        'lebat'  => 'https://asset.plovis.id/plovis/public/cb7b89d7-2ac7-4fa4-a16c-02734432838e.geojson',
        'mangrove_2025' => 'public/geojson/mangrove_2025.geojson',
    ];

    /**
     * Proxy GeoJSON dari Plovis — dipanggil dari monitoring.blade.php
     * Cache 1 jam agar tidak tiap request ke Plovis
     */
    public function proxyPlovis(string $density)
    {
        abort_if(!isset($this->plovisUrls[$density]), 404, 'Density tidak valid');

        $cacheKey = "geojson_plovis_{$density}";

        $data = Cache::remember($cacheKey, 3600, function () use ($density) {
            $response = Http::timeout(30)
                ->withHeaders(['Accept' => 'application/json'])
                ->get($this->plovisUrls[$density]);

            abort_if(!$response->successful(), 502, 'Gagal mengambil data GeoJSON dari sumber');

            $json = $response->json();

            // Plovis membungkus data dalam key "geojson"
            return isset($json['geojson']) ? $json['geojson'] : $json;
        });

        return response()->json($data)
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Get all locations as GeoJSON FeatureCollection
     */
    public function getAllGeoJson(Request $request)
    {
        $query = MangroveLocation::active()->with(['images', 'damages']);

        if ($request->has('density')) {
            $query->where('density', $request->density);
        }

        if ($request->has('region')) {
            $query->where('region', 'like', '%' . $request->region . '%');
        }

        $locations = $query->get();
        $featureCollection = MangroveLocation::toGeoJsonFeatureCollection($locations);

        return response()->json($featureCollection);
    }

    /**
     * Get GeoJSON for specific density (dari database)
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
