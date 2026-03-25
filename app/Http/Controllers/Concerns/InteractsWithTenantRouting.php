<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait InteractsWithTenantRouting
{
    protected function tenantRoute(Tenant $tenant, string $name, array $parameters = []): string
    {
        return route("tenant.{$name}", $parameters);
    }

    protected function redirectToTenantRoute(Request $request, Tenant $tenant, string $name, array $parameters = [], ?string $status = null): RedirectResponse
    {
        $response = redirect()->to($this->tenantRoute($tenant, $name, $parameters));

        if ($status) {
            $response->with('status', $status);
        }

        return $response;
    }
}
