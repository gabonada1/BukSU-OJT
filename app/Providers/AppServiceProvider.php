<?php

namespace App\Providers;

use App\Models\CentralSuperadmin;
use App\Models\TenantAdmin;
use App\Models\Tenant;
use App\Support\Documentation\ProjectDocumentationWriter;
use App\Support\Tenancy\CurrentTenant;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CurrentTenant::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('manage-tenants', fn (?CentralSuperadmin $user) => $user?->canManageTenants() ?? false);
        Gate::define('manage-tenant-users', fn (?TenantAdmin $user) => $user?->canManageTenantUsers() ?? false);

        Tenant::saved(function (): void {
            rescue(fn () => app(ProjectDocumentationWriter::class)->write(), report: false);
        });

        Tenant::deleted(function (): void {
            rescue(fn () => app(ProjectDocumentationWriter::class)->write(), report: false);
        });
    }
}
