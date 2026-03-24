<?php

namespace App\Models;

use App\Models\Concerns\UsesTenantConnection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class TenantAdmin extends Authenticatable
{
    use Notifiable, UsesTenantConnection;

    protected $table = 'tenant_admins';

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'suspended_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'suspended_at' => 'datetime',
        ];
    }

    public function canAccessPortal(): bool
    {
        return $this->is_active && ! $this->suspended_at;
    }

    public function accountStatusLabel(): string
    {
        return $this->canAccessPortal() ? 'active' : 'suspended';
    }
}
