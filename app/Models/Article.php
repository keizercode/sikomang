<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'featured_image',
        'excerpt',
        'content',
        'status',
        'published_at',
        'views',
        'is_featured',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'views' => 'integer',
    ];

    protected $dates = [
        'published_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Relasi ke User (Author)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Auto generate slug from title
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }

            // Ensure unique slug
            $originalSlug = $article->slug;
            $count = 1;
            while (static::where('slug', $article->slug)->exists()) {
                $article->slug = $originalSlug . '-' . $count++;
            }
        });

        static::updating(function ($article) {
            if ($article->isDirty('title') && empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
        });
    }

    /**
     * Scope: Published articles only
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope: Featured articles
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: Draft articles
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Get excerpt or auto-generate from content
     */
    public function getExcerptAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        return Str::limit(strip_tags($this->content), 160);
    }

    /**
     * Get formatted content - CKEditor outputs clean, formatted HTML
     * No additional processing needed
     */
    public function getFormattedContentAttribute()
    {
        // CKEditor already outputs properly formatted HTML with:
        // - Proper paragraph tags
        // - Heading tags
        // - Lists, tables, images
        // - Text alignment
        // Just return the content as-is for frontend display
        return $this->content;
    }

    /**
     * Increment views count
     */
    public function incrementViews()
    {
        $this->increment('views');
    }

    /**
     * Check if article is published
     */
    public function isPublished()
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at <= now();
    }

    /**
     * Get URL/route
     */
    public function url()
    {
        return route('articles.show', $this->slug);
    }
}
