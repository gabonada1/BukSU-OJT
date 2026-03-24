<?php

use App\Http\Controllers\Central\CentralAuthController;
use App\Http\Controllers\Central\CentralDashboardController;
use App\Http\Controllers\Central\TenantProvisionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('central.domain')->group(function () {
    Route::get('/central', function () {
        return Auth::guard('central_superadmin')->check()
            ? redirect()->route('central.dashboard')
            : redirect()->route('central.login');
    })->name('central.home');

    Route::middleware('guest:central_superadmin')->group(function () {
        Route::get('/central/login', [CentralAuthController::class, 'create'])->name('central.login');
        Route::post('/central/login', [CentralAuthController::class, 'store'])->name('central.login.store');
    });

    Route::post('/central/logout', [CentralAuthController::class, 'destroy'])
        ->middleware('auth:central_superadmin')
        ->name('central.logout');

    Route::middleware('auth:central_superadmin')->group(function () {
        Route::get('/central/dashboard', CentralDashboardController::class)->name('central.dashboard');
        Route::post('/central/tenants', [TenantProvisionController::class, 'store'])->name('central.tenants.store');
        Route::patch('/central/tenants/{tenant}', [TenantProvisionController::class, 'update'])->name('central.tenants.update');
        Route::delete('/central/tenants/{tenant}', [TenantProvisionController::class, 'destroy'])->name('central.tenants.destroy');
    });
});
