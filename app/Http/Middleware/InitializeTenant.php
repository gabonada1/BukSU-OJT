<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Support\Tenancy\CurrentTenant;
use App\Support\Tenancy\TenantDatabaseManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenant
{
    public function __construct(
        protected TenantDatabaseManager $databaseManager,
        protected CurrentTenant $currentTenant,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $request->route('tenant');
        $isCentralHost = in_array($request->getHost(), config('tenancy.central_domains', []), true);

        if ($isCentralHost && ! $tenant) {
            return $next($request);
        }

        if (! $tenant instanceof Tenant) {
            if (filled($tenant)) {
                $tenant = Tenant::query()->whereKey($tenant)->firstOrFail();
            } else {
                $tenantDomain = TenantDomain::query()
                    ->active()
                    ->whereRaw('LOWER(host) = ?', [strtolower($request->getHost())])
                    ->first();

                if (! $tenantDomain) {
                    return $next($request);
                }

                $tenant = $tenantDomain->tenant;
            }
        }

        if (! $tenant->canAccessTenantApp()) {
            return response()->view('tenant.unavailable', [
                'tenant' => $tenant,
                'message' => $tenant->subscriptionBlockMessage(),
            ], 403);
        }

        $this->databaseManager->connect($tenant);
        $this->currentTenant->set($tenant);

        return $next($request);
    }
}
