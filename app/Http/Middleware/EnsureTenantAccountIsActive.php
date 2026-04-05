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
        $tenantKey = method_exists($tenant, 'getRouteKey')
            ? (string) $tenant->getRouteKey()
            : (filled($tenant) ? (string) $tenant : null);

        foreach (['tenant_admin', 'supervisor', 'student'] as $guard) {
            $user = Auth::guard($guard)->user();

            if (! $user) {
                continue;
            }

            $sessionTenant = $request->session()->get("tenant_context.{$guard}");

            if ($tenantKey && $sessionTenant && $sessionTenant !== $tenantKey) {
                Auth::guard($guard)->logout();
                $request->session()->forget("tenant_context.{$guard}");
                $request->session()->regenerate();

                return redirect('/login')->withErrors([
                    'email' => 'Your session belonged to a different university portal. Please sign in again for this portal.',
                ]);
            }

            if (method_exists($user, 'canAccessPortal') && ! $user->canAccessPortal()) {
                Auth::guard($guard)->logout();
                $request->session()->forget("tenant_context.{$guard}");
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')->withErrors([
                    'email' => 'This account is no longer allowed to access the university portal.',
                ]);
            }
        }

        return $next($request);
    }
}
