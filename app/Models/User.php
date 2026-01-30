<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'ms_group_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'ms_group_id', 'MsGroupId');
    }

    public function isSuperAdmin(): bool
    {
        return $this->group && $this->group->alias === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return $this->group && in_array($this->group->alias, ['superadmin', 'admin']);
    }

    public function hasAccess(string $module, string $permission = 'is_read'): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return AccessMenu::where('ms_group_id', $this->ms_group_id)
            ->where('module', 'LIKE', $module . '%')
            ->where($permission, true)
            ->exists();
    }
}
