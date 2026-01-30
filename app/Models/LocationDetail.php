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

class LocationDamage extends Model
{
    use HasFactory;

    protected $fillable = [
        'mangrove_location_id',
        'title',
        'description',
        'priority',
        'status',
    ];

    public function location()
    {
        return $this->belongsTo(MangroveLocation::class, 'mangrove_location_id');
    }

    public function actions()
    {
        return $this->hasMany(LocationAction::class);
    }
}

class LocationAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_damage_id',
        'action_description',
        'action_date',
    ];

    protected $casts = [
        'action_date' => 'date',
    ];

    public function damage()
    {
        return $this->belongsTo(LocationDamage::class, 'location_damage_id');
    }
}
