<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar_url',
        'role',
        'is_active',
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
            'is_active' => 'boolean',
        ];
    }

    // Role checks
    public function isSuperAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'officer']);
    }

    public function canManageSites(): bool
    {
        return in_array($this->role, ['admin', 'officer']);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Accessors
    public function getRoleNameAttribute(): string
    {
        $roles = [
            'admin' => 'Administrator',
            'officer' => 'Petugas Lapangan',
            'community' => 'Komunitas',
            'user' => 'Pengguna',
        ];

        return $roles[$this->role] ?? 'Pengguna';
    }

    public function getAvatarAttribute(): string
    {
        if ($this->avatar_url) {
            return $this->avatar_url;
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=009966&color=fff&size=128';
    }
}
