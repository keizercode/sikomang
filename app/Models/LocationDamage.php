<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return $this->hasMany(LocationAction::class, 'location_damage_id');
    }
}
