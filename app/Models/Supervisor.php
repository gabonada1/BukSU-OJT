<?php

namespace App\Models;

use App\Models\Concerns\UsesTenantConnection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supervisor extends Authenticatable
{
    use Notifiable, UsesTenantConnection;

    protected $table = 'supervisors';

    protected $fillable = [
        'name',
        'email',
        'password',
        'partner_company_id',
        'position',
        'department',
        'is_active',
        'suspended_at',
        'email_verified_at',
        'email_verification_token',
        'verification_sent_at',
        'registered_at',
        'registered_via_self_service',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'suspended_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'verification_sent_at' => 'datetime',
            'registered_at' => 'datetime',
            'registered_via_self_service' => 'boolean',
        ];
    }

    public function canAccessPortal(): bool
    {
        return $this->is_active
            && ! $this->suspended_at
            && ! is_null($this->email_verified_at);
    }

    public function accountStatusLabel(): string
    {
        if ($this->suspended_at || ! $this->is_active) {
            return 'suspended';
        }

        if (! $this->email_verified_at) {
            return 'pending verification';
        }

        return 'active';
    }

    public function partnerCompany(): BelongsTo
    {
        return $this->belongsTo(PartnerCompany::class);
    }
}
