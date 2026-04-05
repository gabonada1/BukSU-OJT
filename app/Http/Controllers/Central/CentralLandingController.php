<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantPlanApplication;
use App\Support\Billing\PlanCatalog;
use Illuminate\Contracts\View\View;

class CentralLandingController extends Controller
{
    public function __invoke(): View
    {
        $tenants = Tenant::query()->get();

        return view('central.landing', [
            'pageTitle' => config('app.name', 'University Practicum'),
            'plans' => PlanCatalog::all(),
            'benefits' => [
                'Separate per-university tenant database for cleaner practicum records and access control.',
                'Role-based portals for internship coordinators, students, and company supervisors.',
                'Built-in workflows for partner companies, applications, requirements, progress reports, evaluations, and OJT hours.',
                'Central approval and provisioning so each institution can launch a controlled, consistent practicum workspace.',
            ],
            'stats' => [
                'active_tenants' => $tenants->filter(fn (Tenant $tenant) => $tenant->canAccessTenantApp())->count(),
                'submitted_applications' => TenantPlanApplication::query()->whereIn('status', ['submitted', 'pending_approval'])->count(),
                'premium_tenants' => $tenants->where('plan', 'premium')->count(),
            ],
            'applyAction' => route('central.plan-applications.store'),
            'centralLoginUrl' => route('central.login'),
        ]);
    }
}
