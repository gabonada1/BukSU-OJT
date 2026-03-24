<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'tenant' => \App\Http\Middleware\InitializeTenant::class,
            'central.domain' => \App\Http\Middleware\EnsureCentralDomain::class,
            'tenant.domain' => \App\Http\Middleware\EnsureTenantDomain::class,
            'tenant.account' => \App\Http\Middleware\EnsureTenantAccountIsActive::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request): string {
            $isCentralHost = in_array($request->getHost(), config('tenancy.central_domains', []), true);
            $isCentralPath = str_starts_with(trim($request->path(), '/'), 'central');

            if ($isCentralHost && $isCentralPath) {
                return '/central/login';
            }

            $role = str_contains($request->path(), '/student/') ? 'student'
                : (str_contains($request->path(), '/supervisor/') ? 'supervisor' : 'admin');

            if (! $isCentralHost) {
                return $role === 'admin' ? '/login' : "/login/{$role}";
            }

            $tenant = $request->route('tenant') ?? config('tenancy.default_tenant_slug');

            return $role === 'admin'
                ? "/tenants/{$tenant}/login"
                : "/tenants/{$tenant}/login/{$role}";
        });

        $middleware->redirectUsersTo(function (Request $request): string {
            if (auth('central_superadmin')->check()) {
                return '/central/dashboard';
            }

            $isCentralHost = in_array($request->getHost(), config('tenancy.central_domains', []), true);
            $tenant = $request->route('tenant') ?? config('tenancy.default_tenant_slug');
            $prefix = $isCentralHost ? "/tenants/{$tenant}" : '';

            if (auth('tenant_admin')->check()) {
                return "{$prefix}/admin/dashboard";
            }

            if (auth('supervisor')->check()) {
                return "{$prefix}/supervisor/dashboard";
            }

            if (auth('student')->check()) {
                return "{$prefix}/student/dashboard";
            }

            return '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
