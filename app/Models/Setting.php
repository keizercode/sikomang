<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Scope: Public settings only
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope: By group
     */
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Get setting value by key (with cache)
     */
    public static function get($key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();

            if (!$setting) {
                return $default;
            }

            // Parse value based on type
            return static::parseValue($setting->value, $setting->type);
        });
    }

    /**
     * Set setting value by key
     */
    public static function set($key, $value)
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        // Clear cache
        Cache::forget("setting_{$key}");
        Cache::forget('settings_all');

        return $setting;
    }

    /**
     * Get all settings as array (with cache)
     */
    public static function all($group = null)
    {
        $cacheKey = $group ? "settings_group_{$group}" : 'settings_all';

        return Cache::remember($cacheKey, 3600, function () use ($group) {
            $query = static::query();

            if ($group) {
                $query->where('group', $group);
            }

            $settings = $query->get();
            $result = [];

            foreach ($settings as $setting) {
                $result[$setting->key] = static::parseValue($setting->value, $setting->type);
            }

            return $result;
        });
    }

    /**
     * Parse value based on type
     */
    protected static function parseValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'number':
                return is_numeric($value) ? (float) $value : 0;
            case 'json':
                return json_decode($value, true) ?? [];
            default:
                return $value;
        }
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache()
    {
        $keys = static::pluck('key');

        foreach ($keys as $key) {
            Cache::forget("setting_{$key}");
        }

        Cache::forget('settings_all');

        // Clear group caches
        $groups = static::distinct()->pluck('group');
        foreach ($groups as $group) {
            Cache::forget("settings_group_{$group}");
        }
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when setting is updated or deleted
        static::saved(function ($setting) {
            Cache::forget("setting_{$setting->key}");
            Cache::forget('settings_all');
            Cache::forget("settings_group_{$setting->group}");
        });

        static::deleted(function ($setting) {
            Cache::forget("setting_{$setting->key}");
            Cache::forget('settings_all');
            Cache::forget("settings_group_{$setting->group}");
        });
    }
}
