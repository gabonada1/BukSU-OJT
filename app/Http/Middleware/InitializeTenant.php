<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
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

        if (! $tenant instanceof Tenant) {
            $tenant = Tenant::query()
                ->when(
                    filled($tenant),
                    fn ($query) => $query->where('slug', $tenant),
                    function ($query) use ($request) {
                        $host = $request->getHost();
                        $localHost = $this->localSubdomainFromHost($host);

                        $query->where(function ($inner) use ($host, $localHost) {
                            $inner->where('domain', $host);

                            if (filled($localHost)) {
                                $inner->orWhere('subdomain', $localHost);
                            }
                        });
                    }
                )
                ->firstOrFail();
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

    protected function localSubdomainFromHost(string $host): ?string
    {
        $suffix = '.'.ltrim(config('tenancy.local_domain_suffix', 'localhost'), '.');

        if (! str_ends_with($host, $suffix)) {
            return null;
        }

        $subdomain = substr($host, 0, -strlen($suffix));

        return filled($subdomain) ? $subdomain : null;
    }
}
