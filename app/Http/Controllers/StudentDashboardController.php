<?php

namespace App\Http\Controllers;

use App\Models\PartnerCompany;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function __invoke(): View
    {
        $student = Auth::guard('student')->user();

        abort_unless($student, 403);

        $student->load([
            'partnerCompany',
            'requirements',
            'hourLogs',
            'applications.partnerCompany',
        ]);

        $companies = PartnerCompany::query()
            ->with(['supervisors', 'students'])
            ->where('is_active', true)
            ->latest()
            ->get();

        $tenant = app(\App\Support\Tenancy\CurrentTenant::class)->tenant();
        $portalTitle = data_get($tenant?->settings, 'branding.portal_title', config('app.name', 'BukSU Practicum Portal'));

        return view('tenant.student.dashboard', [
            'tenant' => $tenant,
            'pageTitle' => 'Student Dashboard | '.$portalTitle,
            'student' => $student,
            'companies' => $companies,
            'studentApplicationAction' => route('tenant.student.applications.store'),
            'studentRequirementAction' => route('tenant.student.requirements.store'),
        ]);
    }
}
