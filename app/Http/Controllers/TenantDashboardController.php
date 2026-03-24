<?php

namespace App\Http\Controllers;

use App\Models\InternshipApplication;
use App\Models\OjtHourLog;
use App\Models\PartnerCompany;
use App\Models\Student;
use App\Models\StudentRequirement;
use App\Models\Supervisor;
use App\Models\TenantAdmin;
use App\Support\Tenancy\CurrentTenant;
use Illuminate\View\View;

class TenantDashboardController extends Controller
{
    public function __invoke(CurrentTenant $currentTenant): View
    {
        $tenant = $currentTenant->tenant();

        abort_unless($tenant, 404);

        $companies = PartnerCompany::query()->with('supervisors')->latest()->get();
        $students = Student::query()->with(['partnerCompany', 'applications'])->latest()->get();
        $applications = InternshipApplication::query()->with(['student', 'partnerCompany'])->latest()->get();
        $supervisors = Supervisor::query()->with('partnerCompany')->latest()->get();
        $requirements = StudentRequirement::query()->with('student')->latest()->get();
        $hourLogs = OjtHourLog::query()->with('student')->latest('log_date')->get();
        $userDirectory = $this->userDirectory($students, $supervisors);
        $currentSection = request()->query('section', 'companies');
        $editKey = request()->query('edit');

        return view('tenant.admin.dashboard', [
            'tenant' => $tenant,
            'pageTitle' => 'Admin Dashboard | '.config('app.name', 'BukSU Practicum'),
            'roles' => [
                'College Admin / Internship Coordinator',
                'Students',
                'Company Supervisor',
                'Practicum Head / Dean',
            ],
            'modules' => [
                'Partner company management',
                'Student internship applications',
                'Requirement and report submissions',
                'OJT hour tracking and validation',
                'Supervisor evaluation workflows',
                'College-wide dashboards and reports',
            ],
            'databaseStrategy' => [
                'Central app database stores tenant registry, plans, platform settings, and shared authentication hooks.',
                'Each tenant gets its own dedicated database for students, partner companies, documents, OJT logs, reports, and evaluations.',
                'The current deployment is focused on College of Technologies as the first active tenant.',
            ],
            'stats' => [
                'companies' => $companies->count(),
                'students' => $students->count(),
                'applications' => $applications->count(),
                'supervisors' => $supervisors->count(),
                'users' => $userDirectory->count(),
                'approved_requirements' => StudentRequirement::query()->where('status', 'approved')->count(),
                'approved_hours' => round((float) OjtHourLog::query()->where('status', 'approved')->sum('hours'), 2),
            ],
            'companies' => $companies,
            'students' => $students,
            'applications' => $applications,
            'supervisors' => $supervisors,
            'requirements' => $requirements,
            'hourLogs' => $hourLogs,
            'userDirectory' => $userDirectory,
            'editing' => [
                'companies' => $currentSection === 'companies' ? $companies->firstWhere('id', (int) $editKey) : null,
                'applications' => $currentSection === 'applications' ? $applications->firstWhere('id', (int) $editKey) : null,
                'supervisors' => $currentSection === 'supervisors' ? $supervisors->firstWhere('id', (int) $editKey) : null,
                'students' => $currentSection === 'students' ? $students->firstWhere('id', (int) $editKey) : null,
                'requirements' => $currentSection === 'requirements' ? $requirements->firstWhere('id', (int) $editKey) : null,
                'hours' => $currentSection === 'hours' ? $hourLogs->firstWhere('id', (int) $editKey) : null,
                'users' => $currentSection === 'users' ? $userDirectory->firstWhere('key', $editKey) : null,
            ],
            'requirementStatuses' => ['submitted', 'approved', 'revision', 'rejected'],
            'hourStatuses' => ['pending', 'approved', 'rejected'],
            'studentStatuses' => ['pending', 'accepted', 'deployed', 'completed'],
            'applicationStatuses' => ['pending', 'accepted', 'rejected', 'deployed'],
            'userRoleOptions' => ['admin', 'supervisor', 'student'],
            'formActions' => $this->formActions($tenant),
        ]);
    }

    protected function formActions($tenant): array
    {
        if (request()->routeIs('tenant.domain.*')) {
            return [
                'companies' => route('tenant.domain.admin.companies.store'),
                'applications' => route('tenant.domain.admin.applications.store'),
                'students' => route('tenant.domain.admin.students.store'),
                'supervisors' => route('tenant.domain.admin.supervisors.store'),
                'requirements' => route('tenant.domain.admin.requirements.store'),
                'hours' => route('tenant.domain.admin.hours.store'),
            ];
        }

        return [
            'companies' => route('tenant.admin.companies.store', $tenant),
            'applications' => route('tenant.admin.applications.store', $tenant),
            'students' => route('tenant.admin.students.store', $tenant),
            'supervisors' => route('tenant.admin.supervisors.store', $tenant),
            'requirements' => route('tenant.admin.requirements.store', $tenant),
            'hours' => route('tenant.admin.hours.store', $tenant),
        ];
    }

    protected function userDirectory($students, $supervisors)
    {
        $admins = TenantAdmin::query()->latest()->get()->map(function (TenantAdmin $admin) {
            return [
                'key' => 'admin:'.$admin->getKey(),
                'type' => 'admin',
                'id' => $admin->getKey(),
                'role' => 'admin',
                'name' => $admin->name,
                'email' => $admin->email,
                'status' => $admin->accountStatusLabel(),
                'is_active' => $admin->is_active,
                'email_verified_at' => now(),
                'context' => 'Tenant admin',
                'model' => $admin,
            ];
        });

        $supervisorItems = $supervisors->map(function (Supervisor $supervisor) {
            return [
                'key' => 'supervisor:'.$supervisor->getKey(),
                'type' => 'supervisor',
                'id' => $supervisor->getKey(),
                'role' => 'supervisor',
                'name' => $supervisor->name,
                'email' => $supervisor->email,
                'status' => $supervisor->accountStatusLabel(),
                'is_active' => $supervisor->is_active,
                'email_verified_at' => $supervisor->email_verified_at,
                'context' => $supervisor->department ?: ($supervisor->partnerCompany?->name ?: 'Teacher / Supervisor'),
                'model' => $supervisor,
            ];
        });

        $studentItems = $students->map(function (Student $student) {
            return [
                'key' => 'student:'.$student->getKey(),
                'type' => 'student',
                'id' => $student->getKey(),
                'role' => 'student',
                'name' => $student->full_name,
                'email' => $student->email,
                'status' => $student->accountStatusLabel(),
                'is_active' => $student->is_active,
                'email_verified_at' => $student->email_verified_at,
                'context' => $student->program ?: 'No program set',
                'model' => $student,
            ];
        });

        return $admins
            ->concat($supervisorItems)
            ->concat($studentItems)
            ->sortBy('name')
            ->values();
    }
}
