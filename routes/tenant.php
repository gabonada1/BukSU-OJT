<?php

use App\Http\Controllers\InternshipApplicationController;
use App\Http\Controllers\OjtHourLogController;
use App\Http\Controllers\PartnerCompanyController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TenantProfileController;
use App\Http\Controllers\TenantRegistrationController;
use App\Http\Controllers\TenantUserManagementController;
use App\Http\Controllers\StudentRequirementController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\SupervisorDashboardController;
use App\Http\Controllers\TenantAuthController;
use App\Http\Controllers\TenantDashboardController;
use Illuminate\Support\Facades\Route;

$loginRoles = ['student', 'supervisor'];

$registerTenantRoutes = function (string $namePrefix) use ($loginRoles): void {
    Route::middleware('guest:tenant_admin,supervisor,student')->group(function () use ($loginRoles, $namePrefix) {
        Route::get('/login', [TenantAuthController::class, 'admin'])->name("{$namePrefix}login.default");
        Route::post('/login', [TenantAuthController::class, 'storeAdmin'])->name("{$namePrefix}login.default.store");
        Route::get('/login/{role}', [TenantAuthController::class, 'create'])
            ->whereIn('role', $loginRoles)
            ->name("{$namePrefix}login");
        Route::post('/login/{role}', [TenantAuthController::class, 'store'])
            ->whereIn('role', $loginRoles)
            ->name("{$namePrefix}login.store");
        Route::get('/register', [TenantRegistrationController::class, 'create'])->name("{$namePrefix}register.create");
        Route::post('/register', [TenantRegistrationController::class, 'store'])->name("{$namePrefix}register.store");
        Route::get('/register/verify/{token}', [TenantRegistrationController::class, 'verify'])->name("{$namePrefix}register.verify");
    });

    Route::middleware('auth:tenant_admin,supervisor,student')->group(function () use ($namePrefix) {
        Route::post('/logout', [TenantAuthController::class, 'destroy'])->name("{$namePrefix}logout");
    });

    Route::middleware(['auth:tenant_admin,supervisor,student', 'tenant.account'])->group(function () use ($namePrefix) {
        Route::get('/profile', [TenantProfileController::class, 'show'])->name("{$namePrefix}profile.show");
        Route::patch('/profile', [TenantProfileController::class, 'update'])->name("{$namePrefix}profile.update");
        Route::put('/profile/password', [TenantProfileController::class, 'updatePassword'])->name("{$namePrefix}profile.password.update");
    });

    Route::middleware(['auth:tenant_admin', 'tenant.account'])->group(function () use ($namePrefix) {
        Route::get('/admin/dashboard', TenantDashboardController::class)->name("{$namePrefix}admin.dashboard");
        Route::post('/admin/companies', [PartnerCompanyController::class, 'store'])->name("{$namePrefix}admin.companies.store");
        Route::patch('/admin/companies/{company}', [PartnerCompanyController::class, 'update'])->name("{$namePrefix}admin.companies.update");
        Route::post('/admin/applications', [InternshipApplicationController::class, 'storeAdmin'])->name("{$namePrefix}admin.applications.store");
        Route::patch('/admin/applications/{application}', [InternshipApplicationController::class, 'updateAdmin'])->name("{$namePrefix}admin.applications.update");
        Route::post('/admin/students', [StudentController::class, 'store'])->name("{$namePrefix}admin.students.store");
        Route::patch('/admin/students/{student}', [StudentController::class, 'update'])->name("{$namePrefix}admin.students.update");
        Route::post('/admin/supervisors', [SupervisorController::class, 'store'])->name("{$namePrefix}admin.supervisors.store");
        Route::patch('/admin/supervisors/{supervisor}', [SupervisorController::class, 'update'])->name("{$namePrefix}admin.supervisors.update");
        Route::post('/admin/requirements', [StudentRequirementController::class, 'store'])->name("{$namePrefix}admin.requirements.store");
        Route::patch('/admin/requirements/{requirement}', [StudentRequirementController::class, 'update'])->name("{$namePrefix}admin.requirements.update");
        Route::post('/admin/hours', [OjtHourLogController::class, 'store'])->name("{$namePrefix}admin.hours.store");
        Route::patch('/admin/hours/{hour}', [OjtHourLogController::class, 'update'])->name("{$namePrefix}admin.hours.update");
        Route::patch('/admin/users/{type}/{id}', [TenantUserManagementController::class, 'update'])
            ->whereIn('type', ['admin', 'supervisor', 'student'])
            ->name("{$namePrefix}admin.users.update");
    });

    Route::middleware(['auth:supervisor', 'tenant.account'])->group(function () use ($namePrefix) {
        Route::get('/supervisor/dashboard', SupervisorDashboardController::class)->name("{$namePrefix}supervisor.dashboard");
    });

    Route::middleware(['auth:student', 'tenant.account'])->group(function () use ($namePrefix) {
        Route::get('/student/dashboard', StudentDashboardController::class)->name("{$namePrefix}student.dashboard");
        Route::post('/student/applications', [InternshipApplicationController::class, 'storeStudent'])->name("{$namePrefix}student.applications.store");
        Route::post('/student/requirements', [StudentRequirementController::class, 'storeStudent'])->name("{$namePrefix}student.requirements.store");
    });
};

Route::middleware('tenant')->prefix('/tenants/{tenant}')->group(function () use ($registerTenantRoutes) {
    Route::get('/', fn () => redirect()->route('tenant.login.default', [
        'tenant' => request()->route('tenant'),
    ]))->name('tenant.home');

    $registerTenantRoutes('tenant.');
});

Route::middleware(['tenant.domain', 'tenant'])->group(function () use ($registerTenantRoutes) {
    $registerTenantRoutes('tenant.domain.');
});
