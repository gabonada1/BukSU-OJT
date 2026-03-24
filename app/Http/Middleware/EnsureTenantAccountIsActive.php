<?php

namespace App\Http\Middleware;

use App\Support\Tenancy\CurrentTenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = app(CurrentTenant::class)->tenant() ?? $request->route('tenant');

        foreach (['tenant_admin', 'supervisor', 'student'] as $guard) {
            $user = Auth::guard($guard)->user();

            if (! $user) {
                continue;
            }

            $sessionTenant = $request->session()->get("tenant_context.{$guard}");

            if ($tenant && $sessionTenant && $sessionTenant !== $tenant->slug) {
                Auth::guard($guard)->logout();
                $request->session()->forget("tenant_context.{$guard}");
                $request->session()->regenerate();

                $path = ! in_array($request->getHost(), config('tenancy.central_domains', []), true)
                    ? '/login'
                    : '/tenants/'.($tenant->slug ?? config('tenancy.default_tenant_slug')).'/login';

                return redirect($path)->withErrors([
                    'email' => 'Your session belonged to a different tenant. Please sign in again for this tenant.',
                ]);
            }

            if (method_exists($user, 'canAccessPortal') && ! $user->canAccessPortal()) {
                Auth::guard($guard)->logout();
                $request->session()->forget("tenant_context.{$guard}");
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $path = ! in_array($request->getHost(), config('tenancy.central_domains', []), true)
                    ? '/login'
                    : '/tenants/'.($tenant?->slug ?? config('tenancy.default_tenant_slug')).'/login';

                return redirect($path)->withErrors([
                    'email' => 'This account is no longer allowed to access the tenant workspace.',
                ]);
            }
        }

        return $next($request);
    }
}
