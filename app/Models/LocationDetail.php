<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'mangrove_location_id',
        'species_detail',
        'activities',
        'forest_utilization',
        'programs',
        'stakeholders',
    ];

    protected $casts = [
        'species_detail' => 'array',
        'activities' => 'array',
        'forest_utilization' => 'array',
        'programs' => 'array',
        'stakeholders' => 'array',
    ];

    public function location()
    {
        return $this->belongsTo(MangroveLocation::class, 'mangrove_location_id');
    }
}
