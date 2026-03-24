<?php

namespace App\Models\Concerns;

trait UsesTenantConnection
{
    public function getConnectionName(): ?string
    {
        return config('tenancy.tenant_connection', 'tenant');
    }
}
