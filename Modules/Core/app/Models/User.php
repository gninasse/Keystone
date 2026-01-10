<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Modules\Core\Traits\HasModulePermissions;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasModulePermissions; 

    // protected $table = 'cores_users'; // Removed to use default 'users' table

    protected $fillable = [
        'name',
        'last_name',
        'user_name',
        'email',
        'service',
        'password',
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

    // Accessor pour le nom complet
    public function getFullNameAttribute(): string
    {
        return "{$this->name} {$this->last_name}";
    }
}