<?php

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

$localSubdomainFromHost = function (string $host): ?string {
    $suffix = '.'.ltrim(config('tenancy.local_domain_suffix', 'localhost'), '.');

    if (! str_ends_with($host, $suffix)) {
        return null;
    }

    $subdomain = substr($host, 0, -strlen($suffix));

    return filled($subdomain) ? $subdomain : null;
};

Route::get('/', function (Request $request) use ($localSubdomainFromHost) {
    $host = $request->getHost();

    if (in_array($host, config('tenancy.central_domains', []), true)) {
        return Auth::guard('central_superadmin')->check()
            ? redirect()->route('central.dashboard')
            : redirect()->route('central.login');
    }

    $localSubdomain = $localSubdomainFromHost($host);

    $tenantExists = Tenant::query()
        ->where(function ($query) use ($host, $localSubdomain) {
            $query->where('domain', $host);

            if (filled($localSubdomain)) {
                $query->orWhere('subdomain', $localSubdomain);
            }
        })
        ->exists();

    abort_unless($tenantExists, 404);

    return redirect()->route('tenant.domain.login.default');
})->name('app.entry');

require __DIR__.'/central.php';
require __DIR__.'/tenant.php';
