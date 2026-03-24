<?php

namespace App\Support\Tenancy;

use App\Models\Tenant;
use App\Models\TenantAdmin;
use Illuminate\Support\Collection;

class TenantAdminContactResolver
{
    public function __construct(
        protected TenantDatabaseManager $databaseManager,
    ) {
    }

    public function contacts(Tenant $tenant): Collection
    {
        $this->databaseManager->connect($tenant);

        return TenantAdmin::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['name', 'email']);
    }
}
