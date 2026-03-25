<?php

use App\Http\Controllers\Central\CentralAuthController;
use App\Http\Controllers\Central\CentralDashboardController;
use App\Http\Controllers\Central\CentralRbacController;
use App\Http\Controllers\Central\PlanApplicationController;
use App\Http\Controllers\Central\TenantProvisionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('central.domain')->group(function () {
    Route::post('/apply', [PlanApplicationController::class, 'store'])->name('central.plan-applications.store');
    Route::get('/apply/{application}/success', [PlanApplicationController::class, 'success'])->name('central.plan-applications.success');
    Route::get('/apply/{application}/cancel', [PlanApplicationController::class, 'cancel'])->name('central.plan-applications.cancel');

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
        Route::get('/central/rbac', [CentralRbacController::class, 'index'])->name('central.rbac.index');
        Route::post('/central/rbac', [CentralRbacController::class, 'update'])->name('central.rbac.update');
        Route::post('/central/rbac/reset', [CentralRbacController::class, 'reset'])->name('central.rbac.reset');
        Route::post('/central/applications/{application}/approve', [PlanApplicationController::class, 'approve'])->name('central.plan-applications.approve');
        Route::post('/central/applications/{application}/reject', [PlanApplicationController::class, 'reject'])->name('central.plan-applications.reject');
        Route::post('/central/tenants', [TenantProvisionController::class, 'store'])->name('central.tenants.store');
        Route::patch('/central/tenants/{tenant}', [TenantProvisionController::class, 'update'])->name('central.tenants.update');
        Route::patch('/central/tenants/{tenant}/status', [TenantProvisionController::class, 'updateStatus'])->name('central.tenants.status');
        Route::post('/central/tenants/{tenant}/notify', [TenantProvisionController::class, 'notify'])->name('central.tenants.notify');
        Route::delete('/central/tenants/{tenant}', [TenantProvisionController::class, 'destroy'])->name('central.tenants.destroy');
    });
});
