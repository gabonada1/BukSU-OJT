<?php

namespace App\Models;

use App\Models\Concerns\UsesTenantConnection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use Notifiable, UsesTenantConnection;

    protected $fillable = [
        'student_number',
        'first_name',
        'last_name',
        'email',
        'password',
        'program',
        'required_hours',
        'completed_hours',
        'status',
        'partner_company_id',
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
            'required_hours' => 'decimal:2',
            'completed_hours' => 'decimal:2',
            'is_active' => 'boolean',
            'suspended_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'verification_sent_at' => 'datetime',
            'registered_at' => 'datetime',
            'registered_via_self_service' => 'boolean',
        ];
    }

    public function partnerCompany(): BelongsTo
    {
        return $this->belongsTo(PartnerCompany::class);
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(StudentRequirement::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(InternshipApplication::class);
    }

    public function hourLogs(): HasMany
    {
        return $this->hasMany(OjtHourLog::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getNameAttribute(): string
    {
        return $this->full_name;
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
}
