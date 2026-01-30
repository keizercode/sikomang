<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'ms_menu';
    protected $primaryKey = 'MsMenuId';

    protected $fillable = [
        'parent_id',
        'title',
        'url',
        'module',
        'menu_type',
        'menu_icons',
        'ordering',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'ordering' => 'integer',
    ];

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id', 'MsMenuId');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id', 'MsMenuId')->orderBy('ordering');
    }

    public function accessMenus()
    {
        return $this->hasMany(AccessMenu::class, 'ms_menu_id', 'MsMenuId');
    }

    public static function getMenuTree($type = 'sidebar', $status = true)
    {
        return self::where('menu_type', $type)
            ->where('parent_id', 0)
            ->where('status', $status)
            ->with(['children' => function ($query) use ($status) {
                $query->where('status', $status)->orderBy('ordering');
            }])
            ->orderBy('ordering')
            ->get();
    }
}
