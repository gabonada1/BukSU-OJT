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

        return view('tenant.student.dashboard', [
            'tenant' => app(\App\Support\Tenancy\CurrentTenant::class)->tenant(),
            'pageTitle' => 'Student Dashboard | '.config('app.name', 'BukSU Practicum'),
            'student' => $student,
            'companies' => $companies,
            'studentApplicationAction' => request()->routeIs('tenant.domain.*')
                ? route('tenant.domain.student.applications.store')
                : route('tenant.student.applications.store', app(\App\Support\Tenancy\CurrentTenant::class)->tenant()),
            'studentRequirementAction' => request()->routeIs('tenant.domain.*')
                ? route('tenant.domain.student.requirements.store')
                : route('tenant.student.requirements.store', app(\App\Support\Tenancy\CurrentTenant::class)->tenant()),
        ]);
    }
}
