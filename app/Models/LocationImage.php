<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'mangrove_location_id',
        'image_url',
        'caption',
        'order',
    ];

    public function location()
    {
        return $this->belongsTo(MangroveLocation::class, 'mangrove_location_id');
    }
}
