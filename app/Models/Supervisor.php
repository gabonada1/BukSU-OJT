<?php

namespace App\Models;

use App\Models\Concerns\ScopesTenantUserRole;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supervisor extends TenantUser
{
    use ScopesTenantUserRole;

    public function canAccessPortal(): bool
    {
        return parent::canAccessPortal();
    }

    public function accountStatusLabel(): string
    {
        return parent::accountStatusLabel();
    }

    public function partnerCompany(): BelongsTo
    {
        return $this->belongsTo(PartnerCompany::class);
    }

    protected static function tenantUserRole(): string
    {
        return 'supervisor';
    }
}
