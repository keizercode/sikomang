<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'area' => 'decimal:2',
        'health_percentage' => 'decimal:2',
        'is_active' => 'boolean',
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
}
