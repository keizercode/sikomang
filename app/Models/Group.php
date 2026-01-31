<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $table = 'ms_group';
    protected $primaryKey = 'MsGroupId';

    protected $fillable = [
        'name',
        'alias',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'ms_group_id', 'MsGroupId');
    }

    public function accessMenus()
    {
        return $this->hasMany(AccessMenu::class, 'ms_group_id', 'MsGroupId');
    }
}
