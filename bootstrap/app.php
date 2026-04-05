<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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
            'tenant.password.updated' => \App\Http\Middleware\EnsureTenantPasswordIsUpdated::class,
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

            if ($isCentralHost && $isCentralPath) {
                return '/central/dashboard';
            }

            return match (true) {
                auth('tenant_admin')->check() => '/admin/dashboard',
                auth('supervisor')->check() => '/supervisor/dashboard',
                auth('student')->check() => '/student/dashboard',
                default => '/login',
            };
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $redirectForbidden = function (string $message, Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            $fallbackPath = match (true) {
                auth('tenant_admin')->check() => '/admin/dashboard',
                auth('supervisor')->check() => '/supervisor/dashboard',
                auth('student')->check() => '/student/dashboard',
                auth('central_superadmin')->check() => '/central/dashboard',
                default => '/login',
            };

            $target = url()->previous();

            if (! $target || $target === $request->fullUrl()) {
                $target = $fallbackPath;
            }

            return redirect($target)->with('toast', [
                'type' => 'info',
                'message' => $message ?: 'You do not have permission to perform this action.',
            ]);
        };

        $exceptions->render(function (AuthorizationException $exception, Request $request) {
            return $redirectForbidden(
                $exception->getMessage() ?: 'You do not have permission to perform this action.',
                $request
            );
        });

        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) use ($redirectForbidden) {
            if ($exception->getStatusCode() !== 403) {
                return null;
            }

            return $redirectForbidden(
                $exception->getMessage() ?: 'You do not have permission to perform this action.',
                $request
            );
        });
    })->create();
