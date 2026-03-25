<?php

use App\Http\Controllers\Central\CentralLandingController;
use App\Models\TenantDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    if (in_array($request->getHost(), config('tenancy.central_domains', []), true)) {
        return app(CentralLandingController::class)();
    }

    abort_unless(
        TenantDomain::query()
            ->active()
            ->whereRaw('LOWER(host) = ?', [strtolower($request->getHost())])
            ->exists(),
        404
    );

    return redirect('/login');
})->name('app.entry');

require __DIR__.'/central.php';
require __DIR__.'/tenant.php';
