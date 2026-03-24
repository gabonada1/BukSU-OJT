<?php

namespace App\Support\Tenancy;

use App\Models\Tenant;

class CurrentTenant
{
    protected ?Tenant $tenant = null;

    public function set(Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function tenant(): ?Tenant
    {
        return $this->tenant;
    }
}
