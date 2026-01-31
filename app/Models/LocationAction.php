<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
