<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantPasswordIsUpdated
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = Auth::guard('tenant_admin')->user();

        if (! $admin || ! $admin->must_change_password) {
            return $next($request);
        }

        if ($request->routeIs('tenant.admin.password.setup.*', 'tenant.admin.logout')) {
            return $next($request);
        }

        return redirect()->route('tenant.admin.password.setup.show');
    }
}
