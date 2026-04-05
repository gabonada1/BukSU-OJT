<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Tenant;
use App\Support\Security\RbacMatrix;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

trait AuthorizesTenantPermissions
{
    protected function authorizeTenantPermission(string $permission, ?Tenant $tenant = null): void
    {
        $tenant ??= app(\App\Support\Tenancy\CurrentTenant::class)->tenant();

        abort_unless($tenant, 404);

        [$role, $user] = $this->currentTenantActor();

        if (! $user || ! RbacMatrix::tenantAllows($tenant, $role, $permission)) {
            throw new AuthorizationException('This account does not have permission to perform that action.');
        }
    }

    protected function currentTenantActor(): array
    {
        if (request()->routeIs('tenant*.admin.*') && ($user = Auth::guard('tenant_admin')->user())) {
            return [RbacMatrix::TENANT_ADMIN_ROLE, $user];
        }

        if (request()->routeIs('tenant*.supervisor.*') && ($user = Auth::guard('supervisor')->user())) {
            return ['supervisor', $user];
        }

        if (request()->routeIs('tenant*.student.*') && ($user = Auth::guard('student')->user())) {
            return ['student', $user];
        }

        if ($user = Auth::guard('tenant_admin')->user()) {
            return [RbacMatrix::TENANT_ADMIN_ROLE, $user];
        }

        if ($user = Auth::guard('supervisor')->user()) {
            return ['supervisor', $user];
        }

        if ($user = Auth::guard('student')->user()) {
            return ['student', $user];
        }

        return [null, null];
    }
}
