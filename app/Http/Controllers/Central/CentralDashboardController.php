<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Contracts\View\View;

class CentralDashboardController extends Controller
{
    public function __invoke(): View
    {
        $tenants = Tenant::query()->latest()->get();

        return view('central.dashboard', [
            'pageTitle' => 'Central Application | '.config('app.name', 'BukSU Practicum'),
            'tenants' => $tenants,
            'centralResponsibilities' => [
                'Create and register new college tenants.',
                'Manage central tenant metadata, plans, and domains.',
                'Launch each tenant application from one BukSU control layer.',
            ],
            'tenantResponsibilities' => [
                'Authenticate admins, supervisors, and students inside tenant context.',
                'Run practicum workflows using the tenant database, records, and dashboards.',
                'Store tenant-specific partner companies, students, requirements, and OJT activity.',
            ],
            'stats' => [
                'active_tenants' => $tenants->filter(fn (Tenant $tenant) => $tenant->canAccessTenantApp())->count(),
                'tenant_domains' => $tenants->whereNotNull('domain')->count(),
                'premium_plans' => $tenants->where('plan', 'premium')->count(),
            ],
            'tenantCreateAction' => route('central.tenants.store'),
            'logoutAction' => route('central.logout'),
        ]);
    }
}
