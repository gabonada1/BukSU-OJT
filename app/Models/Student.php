<?php

namespace App\Models;

use App\Models\Concerns\ScopesTenantUserRole;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends TenantUser
{
    use ScopesTenantUserRole;

    public function partnerCompany(): BelongsTo
    {
        return $this->belongsTo(PartnerCompany::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
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
        return parent::canAccessPortal();
    }

    public function accountStatusLabel(): string
    {
        return parent::accountStatusLabel();
    }

    protected static function tenantUserRole(): string
    {
        return 'student';
    }
}
