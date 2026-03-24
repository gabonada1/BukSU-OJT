<?php

namespace App\Providers;

use App\Models\Tenant;
use App\Support\Documentation\ProjectDocumentationWriter;
use App\Support\Tenancy\CurrentTenant;
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
        Tenant::saved(function (): void {
            rescue(fn () => app(ProjectDocumentationWriter::class)->write(), report: false);
        });

        Tenant::deleted(function (): void {
            rescue(fn () => app(ProjectDocumentationWriter::class)->write(), report: false);
        });
    }
}
