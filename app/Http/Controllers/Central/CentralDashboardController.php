<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantPlanApplication;
use App\Support\Billing\PlanCatalog;
use App\Support\Tenancy\TenantAdminContactResolver;
use Illuminate\Contracts\View\View;

class CentralDashboardController extends Controller
{
    public function __construct(
        protected TenantAdminContactResolver $contactResolver,
    ) {
    }

    public function __invoke(): View
    {
        $tenants = Tenant::query()->with(['domains', 'primaryDomain'])->latest()->get();
        $applications = TenantPlanApplication::query()->with(['tenant.domains', 'tenant.primaryDomain'])->latest()->get();
        $tenantContacts = $tenants->mapWithKeys(function (Tenant $tenant): array {
            $contact = rescue(fn () => $this->contactResolver->contacts($tenant)->first(), report: false);

            return [
                $tenant->getKey() => [
                    'name' => $contact->name ?? null,
                    'email' => $contact->email ?? null,
                ],
            ];
        });

        return view('central.dashboard', [
            'pageTitle' => 'BukSU University Admin | '.config('app.name', 'BukSU Practicum Portal'),
            'tenants' => $tenants,
            'applications' => $applications,
            'plans' => PlanCatalog::all(),
            'centralResponsibilities' => [
                'Review incoming college plan applications and verify Stripe payments.',
                'Approve applications to provision a tenant database and coordinator account.',
                'Manage college registry metadata, subscriptions, and portal access from the central layer.',
            ],
            'tenantResponsibilities' => [
                'Authenticate internship coordinators, company supervisors, and students inside each college portal.',
                'Run practicum workflows using the college database, records, and dashboards.',
                'Store college-specific partner organizations, student applications, forms and requirements, progress reports, and evaluations.',
            ],
            'stats' => [
                'active_tenants' => $tenants->filter(fn (Tenant $tenant) => $tenant->canAccessTenantApp())->count(),
                'pending_applications' => $applications->whereIn('status', ['submitted', 'pending_approval'])->count(),
                'paid_applications' => $applications->where('payment_status', 'paid')->count(),
                'premium_plans' => $tenants->where('plan', 'premium')->count(),
            ],
            'tenantContacts' => $tenantContacts,
            'tenantCreateAction' => route('central.tenants.store'),
            'applicationReviewBaseUrl' => route('central.dashboard').'?section=applications',
            'logoutAction' => route('central.logout'),
        ]);
    }
}
