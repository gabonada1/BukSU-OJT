<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesTenantPermissions;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class SupervisorDashboardController extends Controller
{
    use AuthorizesTenantPermissions;

    public function __invoke(): View
    {
        $supervisor = Auth::guard('supervisor')->user();

        abort_unless($supervisor, 403);
        $this->authorizeTenantPermission('report.view');

        $company = $supervisor->partnerCompany;
        $students = $company?->students()->latest()->get() ?? collect();
        $hourLogs = $students->isEmpty()
            ? collect()
            : $students->load('hourLogs')->pluck('hourLogs')->flatten()->sortByDesc('log_date')->take(10);
        $tenant = app(\App\Support\Tenancy\CurrentTenant::class)->tenant();
        $portalTitle = data_get($tenant?->settings, 'branding.portal_title', config('app.name', 'University Practicum'));

        return view('tenant.supervisor.dashboard', [
            'tenant' => $tenant,
            'pageTitle' => 'Company Supervisor Dashboard | '.$portalTitle,
            'supervisor' => $supervisor,
            'company' => $company,
            'students' => $students,
            'hourLogs' => $hourLogs,
        ]);
    }
}
