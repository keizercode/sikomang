<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessMenu extends Model
{
    use HasFactory;

    protected $table = 'ms_access_menu';
    protected $primaryKey = 'MsAccessMenuId';

    protected $fillable = [
        'ms_group_id',
        'ms_menu_id',
        'module',
        'menu_group',
        'is_read',
        'is_create',
        'is_update',
        'is_delete',
        'is_download',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_create' => 'boolean',
        'is_update' => 'boolean',
        'is_delete' => 'boolean',
        'is_download' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class, 'ms_group_id', 'MsGroupId');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'ms_menu_id', 'MsMenuId');
    }
}
