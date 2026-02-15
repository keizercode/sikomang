<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MangroveLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'latitude',
        'longitude',
        'area',
        'density',
        'type',
        'year_established',
        'health_percentage',
        'health_score',
        'manager',
        'region',
        'location_address',
        'description',
        'species',
        'carbon_data',
        'geojson_properties',
        'geojson_source',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'area' => 'decimal:2',
        'health_percentage' => 'decimal:2',
        'is_active' => 'boolean',
        'geojson_properties' => 'array',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'geometry', // Hide raw geometry from JSON output
    ];

    // Relationships
    public function details()
    {
        return $this->hasOne(LocationDetail::class);
    }

    public function images()
    {
        return $this->hasMany(LocationImage::class)->orderBy('order');
    }

    public function damages()
    {
        return $this->hasMany(LocationDamage::class);
    }

    public function activeDamages()
    {
        return $this->hasMany(LocationDamage::class)
            ->whereIn('status', ['pending', 'in_progress']);
    }

    // Accessors
    public function getCoordsAttribute()
    {
        return $this->latitude . ', ' . $this->longitude;
    }

    public function getAreaFormattedAttribute()
    {
        return $this->area ? $this->area . ' ha' : 'N/A';
    }

    public function getHealthFormattedAttribute()
    {
        return $this->health_percentage ? $this->health_percentage . '% Sehat' : 'N/A';
    }

    public function getDamageCountAttribute()
    {
        return $this->activeDamages()->count();
    }

    public function getGroupAttribute()
    {
        // Determine group based on region
        $region = strtolower($this->region ?? '');

        if (str_contains($region, 'penjaringan')) {
            return 'penjaringan';
        } elseif (str_contains($region, 'cilincing')) {
            return 'cilincing';
        } elseif (str_contains($region, 'seribu utara') || str_contains($region, 'kepulauan seribu utara')) {
            return 'kep-seribu-utara';
        } elseif (str_contains($region, 'seribu selatan') || str_contains($region, 'kepulauan seribu selatan')) {
            return 'kep-seribu-selatan';
        }

        return 'all';
    }

    /**
     * Get GeoJSON representation of this location
     */
    public function getGeojsonFeatureAttribute(): array
    {
        $geometry = $this->getGeometryAsGeoJson();

        return [
            'type' => 'Feature',
            'geometry' => $geometry,
            'properties' => [
                'id' => $this->id,
                'name' => $this->name,
                'slug' => $this->slug,
                'density' => $this->density,
                'type' => $this->type,
                'area' => $this->area,
                'region' => $this->region,
                'manager' => $this->manager,
                'year_established' => $this->year_established,
                'health_percentage' => $this->health_percentage,
                'damage_count' => $this->damage_count,
                'images' => $this->images->pluck('image_url')->toArray(),
                'detail_url' => route('monitoring.detail-lokasi', $this->slug),
            ],
        ];
    }

    /**
     * Get geometry as GeoJSON
     */
    public function getGeometryAsGeoJson(): ?array
    {
        if (!$this->geometry) {
            // Fallback to point from lat/lng
            return [
                'type' => 'Point',
                'coordinates' => [(float) $this->longitude, (float) $this->latitude],
            ];
        }

        $geojson = DB::selectOne(
            "SELECT ST_AsGeoJSON(geometry) as geojson FROM mangrove_locations WHERE id = ?",
            [$this->id]
        );

        return $geojson ? json_decode($geojson->geojson, true) : null;
    }

    /**
     * Get geometry as WKT
     */
    public function getGeometryAsWkt(): ?string
    {
        if (!$this->geometry) {
            return null;
        }

        $result = DB::selectOne(
            "SELECT ST_AsText(geometry) as wkt FROM mangrove_locations WHERE id = ?",
            [$this->id]
        );

        return $result->wkt ?? null;
    }

    /**
     * Calculate area from geometry (in hectares)
     */
    public function calculateGeometryArea(): ?float
    {
        if (!$this->geometry) {
            return null;
        }

        $result = DB::selectOne(
            "SELECT ST_Area(ST_Transform(geometry, 3857)) / 10000 as area_ha FROM mangrove_locations WHERE id = ?",
            [$this->id]
        );

        return $result ? round($result->area_ha, 2) : null;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDensity($query, $density)
    {
        return $query->where('density', $density);
    }

    public function scopeByRegion($query, $region)
    {
        return $query->where('region', 'like', '%' . $region . '%');
    }

    /**
     * Scope: With geometry data
     */
    public function scopeWithGeometry($query)
    {
        return $query->whereNotNull('geometry');
    }

    /**
     * Scope: Within bounding box
     */
    public function scopeWithinBounds($query, float $minLat, float $minLng, float $maxLat, float $maxLng)
    {
        return $query->whereRaw(
            "ST_Intersects(geometry, ST_MakeEnvelope(?, ?, ?, ?, 4326))",
            [$minLng, $minLat, $maxLng, $maxLat]
        );
    }

    // Boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($location) {
            if (empty($location->slug)) {
                $location->slug = Str::slug($location->name);
            }
        });

        static::updating(function ($location) {
            if ($location->isDirty('name') && empty($location->slug)) {
                $location->slug = Str::slug($location->name);
            }
        });
    }

    /**
     * Static method to get GeoJSON FeatureCollection for all locations
     */
    public static function toGeoJsonFeatureCollection($query = null): array
    {
        $locations = $query ?? static::active()->get();

        $features = $locations->map(fn($location) => $location->geojson_feature)->toArray();

        return [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];
    }
}
