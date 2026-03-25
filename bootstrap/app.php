<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend([
            \App\Http\Middleware\InitializeTenant::class,
        ]);

        $middleware->web(prepend: [
            \App\Http\Middleware\ScopePortalSession::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'central/logout',
            'central/rbac',
            'central/rbac/reset',
            'central/applications/*/approve',
            'central/applications/*/reject',
            'central/tenants',
            'central/tenants/*',
            'courses',
            'courses/*',
        ]);

        $middleware->alias([
            'tenant' => \App\Http\Middleware\InitializeTenant::class,
            'central.domain' => \App\Http\Middleware\EnsureCentralDomain::class,
            'tenant.domain' => \App\Http\Middleware\EnsureTenantDomain::class,
            'tenant.account' => \App\Http\Middleware\EnsureTenantAccountIsActive::class,
        ]);

        $middleware->prependToPriorityList(Authenticate::class, \App\Http\Middleware\EnsureTenantDomain::class);
        $middleware->prependToPriorityList(Authenticate::class, \App\Http\Middleware\InitializeTenant::class);
        $middleware->prependToPriorityList(RedirectIfAuthenticated::class, \App\Http\Middleware\EnsureTenantDomain::class);
        $middleware->prependToPriorityList(RedirectIfAuthenticated::class, \App\Http\Middleware\InitializeTenant::class);

        $middleware->redirectGuestsTo(function (Request $request): string {
            $isCentralHost = in_array($request->getHost(), config('tenancy.central_domains', []), true);
            $path = trim($request->path(), '/');
            $isCentralPath = str_starts_with($path, 'central');

            if ($isCentralHost && $isCentralPath) {
                return '/central/login';
            }

            $role = match (true) {
                preg_match('#(^|/)student/#', $path) === 1 => 'student',
                preg_match('#(^|/)supervisor/#', $path) === 1 => 'supervisor',
                default => 'admin',
            };

            return $role === 'admin'
                ? '/login'
                : "/{$role}/login";
        });

        $middleware->redirectUsersTo(function (Request $request): string {
            $isCentralHost = in_array($request->getHost(), config('tenancy.central_domains', []), true);
            $path = trim($request->path(), '/');
            $isCentralPath = str_starts_with($path, 'central');

            if ($isCentralHost && $isCentralPath && auth('central_superadmin')->check()) {
                return '/central/dashboard';
            }

            return $isCentralHost && $isCentralPath
                ? '/central/dashboard'
                : '/login';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
