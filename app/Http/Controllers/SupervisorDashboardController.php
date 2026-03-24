<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class SupervisorDashboardController extends Controller
{
    public function __invoke(): View
    {
        $supervisor = Auth::guard('supervisor')->user();

        abort_unless($supervisor, 403);

        $company = $supervisor->partnerCompany;
        $students = $company?->students()->latest()->get() ?? collect();
        $hourLogs = $students->isEmpty()
            ? collect()
            : $students->load('hourLogs')->pluck('hourLogs')->flatten()->sortByDesc('log_date')->take(10);

        return view('tenant.supervisor.dashboard', [
            'tenant' => app(\App\Support\Tenancy\CurrentTenant::class)->tenant(),
            'pageTitle' => 'Teacher Dashboard | '.config('app.name', 'BukSU Practicum'),
            'supervisor' => $supervisor,
            'company' => $company,
            'students' => $students,
            'hourLogs' => $hourLogs,
        ]);
    }
}
