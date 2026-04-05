<?php

namespace App\Models;

use App\Models\Concerns\ScopesTenantUserRole;

class TenantAdmin extends TenantUser
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

    public function canManageTenantUsers(): bool
    {
        return $this->canAccessPortal();
    }

    protected static function tenantUserRole(): string
    {
        return 'admin';
    }
}
