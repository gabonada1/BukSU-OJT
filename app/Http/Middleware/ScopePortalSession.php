<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ScopePortalSession
{
    public function handle(Request $request, Closure $next): Response
    {
        [$cookie, $path] = $this->sessionScope($request);

        config([
            'session.cookie' => $cookie,
            'session.path' => $path,
            'session.domain' => null,
        ]);

        return $next($request);
    }

    protected function sessionScope(Request $request): array
    {
        $baseCookie = Str::slug((string) config('app.name', 'laravel')).'-session';
        $host = $request->getHost();
        $path = trim($request->path(), '/');
        $isCentralHost = in_array($host, config('tenancy.central_domains', []), true);
        $isCentralPath = str_starts_with($path, 'central');

        if ($isCentralHost && $isCentralPath) {
            return [$baseCookie, '/'];
        }

        if ($isCentralHost) {
            return [$baseCookie, '/'];
        }

        return [$baseCookie, '/'];
    }
}
