<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gallery extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'image_path',
        'thumbnail_path',
        'category',
        'location_id',
        'date_taken',
        'photographer',
        'is_featured',
        'is_active',
        'order',
    ];

    protected $casts = [
        'date_taken' => 'date',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    protected $dates = [
        'date_taken',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Relasi ke User (Uploader)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke MangroveLocation
     */
    public function location()
    {
        return $this->belongsTo(MangroveLocation::class, 'location_id');
    }

    /**
     * Scope: Active galleries only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Featured galleries
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: By category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Ordered
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('created_at', 'desc');
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail_path) {
            return asset('storage/' . $this->thumbnail_path);
        }

        return $this->image_url;
    }

    /**
     * Get category label
     */
    public function getCategoryLabelAttribute()
    {
        $labels = [
            'mangrove' => 'Mangrove',
            'kegiatan' => 'Kegiatan',
            'lokasi' => 'Lokasi',
            'flora' => 'Flora',
            'fauna' => 'Fauna',
            'lainnya' => 'Lainnya',
        ];

        return $labels[$this->category] ?? $this->category;
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Auto set order
        static::creating(function ($gallery) {
            if ($gallery->order === 0) {
                $maxOrder = static::max('order') ?? 0;
                $gallery->order = $maxOrder + 1;
            }
        });
    }
}
