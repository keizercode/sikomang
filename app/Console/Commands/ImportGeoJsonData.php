<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\MangroveLocation;
use Illuminate\Support\Str;

class ImportGeoJsonData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'geojson:import {--density= : Import specific density (jarang|sedang|lebat)} {--force : Force reimport all data}';

    /**
     * The console command description.
     */
    protected $description = 'Import mangrove location data from GeoJSON URLs';

    /**
     * GeoJSON source URLs
     */
    protected array $geojsonSources = [
        'jarang' => 'https://asset.plovis.id/plovis/public/67f25022-a757-4f90-a114-16e3f3ad671c.geojson',
        'sedang' => 'https://asset.plovis.id/plovis/public/1c7b760f-7458-4353-bfd9-1ba6084cdce6.geojson',
        'lebat' => 'https://asset.plovis.id/plovis/public/cb7b89d7-2ac7-4fa4-a16c-02734432838e.geojson',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🌳 Starting GeoJSON Import...');
        $this->newLine();

        // Check PostGIS extension
        if (!$this->checkPostGIS()) {
            $this->error('❌ PostGIS extension not available. Please install PostGIS first.');
            return Command::FAILURE;
        }

        $density = $this->option('density');
        $force = $this->option('force');

        // Determine which densities to import
        $densities = $density ? [$density] : ['jarang', 'sedang', 'lebat'];

        foreach ($densities as $densityType) {
            if (!isset($this->geojsonSources[$densityType])) {
                $this->warn("⚠️  Unknown density type: {$densityType}. Skipping...");
                continue;
            }

            $this->importDensityData($densityType, $force);
        }

        $this->newLine();
        $this->info('✅ GeoJSON import completed successfully!');
        $this->displaySummary();

        return Command::SUCCESS;
    }

    /**
     * Check if PostGIS is available
     */
    protected function checkPostGIS(): bool
    {
        try {
            $result = DB::select("SELECT PostGIS_Version()");
            $this->info('✓ PostGIS version: ' . $result[0]->postgis_version);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Import data for specific density
     */
    protected function importDensityData(string $density, bool $force): void
    {
        $url = $this->geojsonSources[$density];

        $this->info("📥 Importing {$density} density data from:");
        $this->line("   {$url}");

        // Fetch GeoJSON
        $this->line('   Downloading GeoJSON...');
        $response = Http::timeout(60)->get($url);

        if (!$response->successful()) {
            $this->error("   ❌ Failed to fetch GeoJSON: HTTP {$response->status()}");
            return;
        }

        // Debug: Show first 500 chars of response
        $rawBody = $response->body();
        $this->line('   📝 Response preview (first 500 chars):');
        $this->line('   ' . substr($rawBody, 0, 500));
        $this->newLine();

        // Try to decode JSON
        $geojson = $response->json();

        if (!$geojson) {
            $this->error('   ❌ Failed to decode JSON response');
            $this->line('   Raw response saved to: storage/logs/geojson_error.json');
            file_put_contents(storage_path('logs/geojson_error.json'), $rawBody);
            return;
        }

        // Debug: Show structure
        $this->line('   🔍 GeoJSON structure:');
        $this->line('   Top-level keys: ' . implode(', ', array_keys($geojson)));

        // IMPORTANT: Plovis wraps GeoJSON in a "geojson" key
        if (isset($geojson['geojson']) && is_array($geojson['geojson'])) {
            $this->line('   ✓ Detected Plovis format - unwrapping "geojson" key');
            $geojson = $geojson['geojson'];
        }

        $this->line('   Type: ' . ($geojson['type'] ?? 'NOT SET'));
        $this->newLine();

        // Handle different GeoJSON structures
        $features = null;

        if (isset($geojson['features']) && is_array($geojson['features'])) {
            // Standard FeatureCollection
            $features = $geojson['features'];
        } elseif (isset($geojson['type']) && $geojson['type'] === 'Feature') {
            // Single feature - wrap in array
            $features = [$geojson];
        } elseif (isset($geojson['data']) && is_array($geojson['data'])) {
            // Sometimes wrapped in 'data' key
            if (isset($geojson['data']['features'])) {
                $features = $geojson['data']['features'];
            } else {
                $features = $geojson['data'];
            }
        }

        if (!$features || !is_array($features)) {
            $this->error('   ❌ Invalid GeoJSON format - no features found');
            $this->line('   Expected: {"type":"FeatureCollection","features":[...]}');
            $this->line('   Got: ' . json_encode(array_keys($geojson)));
            $this->line('   Full response saved to: storage/logs/geojson_debug.json');
            file_put_contents(storage_path('logs/geojson_debug.json'), json_encode($geojson, JSON_PRETTY_PRINT));
            return;
        }

        $this->line("   ✓ Found {$this->formatNumber(count($features))} features");

        // Delete existing data if force
        if ($force) {
            $deleted = MangroveLocation::where('density', $density)
                ->where('geojson_source', $url)
                ->delete();

            if ($deleted > 0) {
                $this->warn("   🗑️  Deleted {$deleted} existing records");
            }
        }

        // Import features
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar(count($features));
        $bar->setFormat('   %current%/%max% [%bar%] %percent:3s%% - %message%');
        $bar->setMessage('Starting import...');

        foreach ($features as $feature) {
            try {
                $result = $this->importFeature($feature, $density, $url, $force);

                if ($result === 'imported') {
                    $imported++;
                    $bar->setMessage('Imported');
                } elseif ($result === 'updated') {
                    $updated++;
                    $bar->setMessage('Updated');
                } elseif ($result === 'skipped') {
                    $skipped++;
                    $bar->setMessage('Skipped (exists)');
                }

                $bar->advance();
            } catch (\Exception $e) {
                $errors++;
                $bar->setMessage('Error: ' . $e->getMessage());
                $bar->advance();

                $this->newLine();
                $this->error("   Error importing feature: " . $e->getMessage());
            }
        }

        $bar->finish();
        $this->newLine();

        // Summary
        $this->info("   ✓ Imported: {$this->formatNumber($imported)}");
        if ($updated > 0) {
            $this->info("   ✓ Updated: {$this->formatNumber($updated)}");
        }
        if ($skipped > 0) {
            $this->warn("   ⊘ Skipped: {$this->formatNumber($skipped)}");
        }
        if ($errors > 0) {
            $this->error("   ❌ Errors: {$this->formatNumber($errors)}");
        }

        $this->newLine();
    }

    /**
     * Import single GeoJSON feature
     */
    protected function importFeature(array $feature, string $density, string $url, bool $force): string
    {
        $geometry = $feature['geometry'] ?? null;
        $properties = $feature['properties'] ?? [];

        if (!$geometry) {
            throw new \Exception('Feature has no geometry');
        }

        // Validate geometry has required fields
        if (!isset($geometry['type']) || !isset($geometry['coordinates'])) {
            throw new \Exception('Invalid geometry structure: ' . json_encode($geometry));
        }

        // Extract location name and other properties
        $name = $this->extractName($properties, $density);

        // Convert GeoJSON geometry to WKT (Well-Known Text) for PostGIS
        try {
            $wkt = $this->geojsonToWkt($geometry);
        } catch (\Exception $e) {
            throw new \Exception("Failed to convert geometry to WKT: " . $e->getMessage());
        }

        // Calculate centroid for latitude/longitude
        try {
            $centroid = $this->calculateCentroid($geometry);
        } catch (\Exception $e) {
            // Fallback: use first coordinate
            $coords = $this->getFirstCoordinate($geometry);
            $centroid = ['lng' => $coords[0], 'lat' => $coords[1]];
        }

        // Check if location already exists
        $existing = MangroveLocation::where('name', $name)
            ->where('density', $density)
            ->first();

        if ($existing && !$force) {
            return 'skipped';
        }

        // Prepare data
        $data = [
            'name' => $name,
            'slug' => Str::slug($name),
            'latitude' => $centroid['lat'],
            'longitude' => $centroid['lng'],
            'density' => $density,
            'type' => $this->determineType($properties, $density),
            'area' => $this->extractArea($properties),
            'region' => $this->extractRegion($properties),
            'manager' => $properties['PENGELOLA'] ?? $properties['manager'] ?? $properties['pengelola'] ?? 'DPHK',
            'year_established' => $this->extractYear($properties),
            'location_address' => $this->extractAddress($properties),
            'description' => $this->extractDescription($properties),
            'geojson_properties' => json_encode($properties),
            'geojson_source' => $url,
            'is_active' => true,
        ];

        // Ensure unique slug
        $originalSlug = $data['slug'];
        $counter = 1;
        while (true) {
            $query = DB::table('mangrove_locations')
                ->where('slug', $data['slug']);

            if ($existing) {
                $query->where('id', '!=', $existing->id);
            }

            if (!$query->exists()) {
                break;
            }

            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Insert or update with PostGIS geometry
        try {
            if ($existing) {
                DB::table('mangrove_locations')
                    ->where('id', $existing->id)
                    ->update(array_merge($data, [
                        'geometry' => DB::raw("ST_GeomFromText('{$wkt}', 4326)"),
                        'updated_at' => now(),
                    ]));

                return 'updated';
            } else {
                DB::table('mangrove_locations')->insert(array_merge($data, [
                    'geometry' => DB::raw("ST_GeomFromText('{$wkt}', 4326)"),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));

                return 'imported';
            }
        } catch (\Exception $e) {
            throw new \Exception("Database insert failed: " . $e->getMessage());
        }
    }

    /**
     * Extract name from properties with multiple fallbacks
     */
    protected function extractName(array $properties, string $density): string
    {
        $nameKeys = [
            'NAMA_INSTA',
            'NAMA_INST',
            'NAMA',
            'nama',
            'name',
            'Name',
            'NAME',
            'lokasi',
            'location',
            'LOKASI'
        ];

        foreach ($nameKeys as $key) {
            if (isset($properties[$key]) && !empty($properties[$key])) {
                return $properties[$key];
            }
        }

        // Generate meaningful name from Plovis data
        $parts = [];

        // Add density type
        $parts[] = "Mangrove " . ucfirst($density);

        // Add region if available
        if (isset($properties['WADMKK']) && !empty($properties['WADMKK'])) {
            $region = str_replace(['Kota Adm. ', 'Kabupaten '], '', $properties['WADMKK']);
            $parts[] = $region;
        } elseif (isset($properties['WADMKC']) && !empty($properties['WADMKC'])) {
            $parts[] = $properties['WADMKC'];
        }

        // Add unique identifier
        $parts[] = substr(md5(json_encode($properties)), 0, 6);

        return implode(' - ', $parts);
    }

    /**
     * Get first coordinate from geometry
     */
    protected function getFirstCoordinate(array $geometry): array
    {
        $coords = $geometry['coordinates'];

        // Navigate to first coordinate pair
        while (is_array($coords) && isset($coords[0]) && is_array($coords[0])) {
            $coords = $coords[0];
        }

        if (isset($coords[0]) && isset($coords[1]) && is_numeric($coords[0]) && is_numeric($coords[1])) {
            return [$coords[0], $coords[1]];
        }

        throw new \Exception('Could not extract valid coordinates');
    }

    /**
     * Convert GeoJSON geometry to WKT
     */
    protected function geojsonToWkt(array $geometry): string
    {
        $type = $geometry['type'] ?? null;
        $coordinates = $geometry['coordinates'] ?? null;

        if (!$type || !$coordinates) {
            throw new \Exception('Invalid geometry: missing type or coordinates');
        }

        switch ($type) {
            case 'Point':
                if (!isset($coordinates[0]) || !isset($coordinates[1])) {
                    throw new \Exception('Invalid Point coordinates');
                }
                return "POINT({$coordinates[0]} {$coordinates[1]})";

            case 'LineString':
                $points = array_map(function ($c) {
                    if (!isset($c[0]) || !isset($c[1])) {
                        throw new \Exception('Invalid coordinate in LineString');
                    }
                    return "{$c[0]} {$c[1]}";
                }, $coordinates);
                return "LINESTRING(" . implode(', ', $points) . ")";

            case 'Polygon':
                $rings = array_map(function ($ring) {
                    $points = array_map(function ($c) {
                        if (!isset($c[0]) || !isset($c[1])) {
                            throw new \Exception('Invalid coordinate in Polygon ring');
                        }
                        return "{$c[0]} {$c[1]}";
                    }, $ring);
                    return "(" . implode(', ', $points) . ")";
                }, $coordinates);
                return "POLYGON(" . implode(', ', $rings) . ")";

            case 'MultiPolygon':
                $polygons = array_map(function ($polygon) {
                    $rings = array_map(function ($ring) {
                        $points = array_map(function ($c) {
                            if (!isset($c[0]) || !isset($c[1])) {
                                throw new \Exception('Invalid coordinate in MultiPolygon');
                            }
                            return "{$c[0]} {$c[1]}";
                        }, $ring);
                        return "(" . implode(', ', $points) . ")";
                    }, $polygon);
                    return "(" . implode(', ', $rings) . ")";
                }, $coordinates);
                return "MULTIPOLYGON(" . implode(', ', $polygons) . ")";

            case 'GeometryCollection':
                // Handle GeometryCollection by converting first geometry
                if (isset($geometry['geometries']) && is_array($geometry['geometries']) && count($geometry['geometries']) > 0) {
                    return $this->geojsonToWkt($geometry['geometries'][0]);
                }
                throw new \Exception("GeometryCollection is empty");

            default:
                throw new \Exception("Unsupported geometry type: {$type}");
        }
    }

    /**
     * Calculate centroid from geometry
     */
    protected function calculateCentroid(array $geometry): array
    {
        $type = $geometry['type'];
        $coordinates = $geometry['coordinates'];

        if ($type === 'Point') {
            return ['lng' => $coordinates[0], 'lat' => $coordinates[1]];
        }

        // For polygons and multipolygons, calculate average of all points
        $allPoints = $this->flattenCoordinates($coordinates);

        if (empty($allPoints)) {
            throw new \Exception('No valid coordinates found');
        }

        $sumLng = array_sum(array_column($allPoints, 0));
        $sumLat = array_sum(array_column($allPoints, 1));
        $count = count($allPoints);

        return [
            'lng' => $sumLng / $count,
            'lat' => $sumLat / $count,
        ];
    }

    /**
     * Flatten nested coordinates array
     */
    protected function flattenCoordinates(array $coords): array
    {
        $result = [];

        foreach ($coords as $item) {
            if (is_array($item) && isset($item[0])) {
                if (is_numeric($item[0])) {
                    // This is a coordinate pair
                    $result[] = $item;
                } else {
                    // This is nested, recurse
                    $result = array_merge($result, $this->flattenCoordinates($item));
                }
            }
        }

        return $result;
    }

    /**
     * Extract helpers
     */
    protected function extractArea(array $properties): ?float
    {
        // Plovis uses LSMGR for area, LUAS_HA might also exist
        $areaKeys = ['LSMGR', 'LUAS_HA', 'luas', 'area', 'Shape_Area', 'AREA'];

        foreach ($areaKeys as $key) {
            if (isset($properties[$key]) && is_numeric($properties[$key])) {
                $area = (float) $properties[$key];
                // LSMGR might be in m², convert to hectares if > 1000
                if ($area > 1000 && $key === 'LSMGR') {
                    return round($area / 10000, 2); // m² to hectares
                }
                return $area;
            }
        }

        return null;
    }

    protected function extractRegion(array $properties): ?string
    {
        // Try various region/location keys
        return $properties['WADMKK']
            ?? $properties['WADMKC']
            ?? $properties['region']
            ?? $properties['wilayah']
            ?? $properties['KABKOT']
            ?? 'Jakarta Utara'; // Default for mangrove areas
    }

    protected function extractYear(array $properties): ?int
    {
        // THNBUA is Plovis field for year
        $year = $properties['THNBUA']
            ?? $properties['TAHUN']
            ?? $properties['year']
            ?? $properties['tahun']
            ?? null;

        if ($year && is_numeric($year)) {
            return (int) $year;
        }

        // Try to extract from SMBDT field (e.g., "CITRA ... TAHUN 2024")
        if (isset($properties['SMBDT']) && is_string($properties['SMBDT'])) {
            if (preg_match('/TAHUN\s+(\d{4})/i', $properties['SMBDT'], $matches)) {
                return (int) $matches[1];
            }
        }

        return null;
    }

    protected function extractAddress(array $properties): ?string
    {
        return $properties['address']
            ?? $properties['alamat']
            ?? $properties['location']
            ?? $properties['LOKASI']
            ?? null;
    }

    protected function extractDescription(array $properties): ?string
    {
        // Combine SMBDT (source) and other relevant info
        $parts = [];

        if (isset($properties['SMBDT'])) {
            $parts[] = "Sumber Data: " . $properties['SMBDT'];
        }

        if (isset($properties['BPDAS'])) {
            $parts[] = "BPDAS: " . $properties['BPDAS'];
        }

        if (isset($properties['KTTJ'])) {
            $parts[] = "Kategori: " . $properties['KTTJ'];
        }

        if (isset($properties['description'])) {
            $parts[] = $properties['description'];
        }

        return !empty($parts) ? implode('. ', $parts) : null;
    }

    protected function determineType(array $properties, string $density): string
    {
        // Check KTTJ field from Plovis or KAWASAN
        $kttj = $properties['KTTJ'] ?? $properties['kawasan'] ?? $properties['KAWASAN'] ?? null;

        if ($kttj) {
            $kttjUpper = strtoupper($kttj);

            if (str_contains($kttjUpper, 'APL') || str_contains($kttjUpper, 'PENGGUNAAN LAIN')) {
                return 'rehabilitasi';
            } elseif (str_contains($kttjUpper, 'HL') || str_contains($kttjUpper, 'LINDUNG')) {
                return 'dilindungi';
            } elseif (str_contains($kttjUpper, 'TWA') || str_contains($kttjUpper, 'WISATA')) {
                return 'dilindungi';
            } elseif (str_contains($kttjUpper, 'RESTORASI')) {
                return 'restorasi';
            }
        }

        // Default based on density
        return match ($density) {
            'lebat' => 'dilindungi',
            'sedang' => 'pengkayaan',
            'jarang' => 'rehabilitasi',
            default => 'pengkayaan'
        };
    }

    /**
     * Display summary statistics
     */
    protected function displaySummary(): void
    {
        $this->info('📊 Database Summary:');
        $this->newLine();

        $stats = DB::table('mangrove_locations')
            ->select('density', DB::raw('COUNT(*) as count'), DB::raw('SUM(area) as total_area'))
            ->groupBy('density')
            ->get();

        $table = [];
        foreach ($stats as $stat) {
            $table[] = [
                'Density' => ucfirst($stat->density),
                'Locations' => $this->formatNumber($stat->count),
                'Total Area (ha)' => $stat->total_area ? number_format($stat->total_area, 2) : 'N/A',
            ];
        }

        $this->table(['Density', 'Locations', 'Total Area (ha)'], $table);
    }

    /**
     * Format number with thousand separator
     */
    protected function formatNumber(int $number): string
    {
        return number_format($number);
    }
}
