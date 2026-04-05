@php
    $layoutMode = 'dashboard';
    $currentSection = request()->query('section', 'companies');
    $createSection = request()->query('create');

    $sections = [
        'companies' => [
            'title' => 'Partner Organizations',
            'create_title' => 'Partner Organization',
            'empty' => 'No partner organizations on file yet.',
            'table' => 'tenant.partials.tables.partner-companies-table',
            'form' => 'tenant.partials.forms.partner-company-form',
        ],
        'supervisors' => [
            'title' => 'Company Supervisors',
            'create_title' => 'Company Supervisor',
            'empty' => 'No company supervisors registered yet.',
            'table' => 'tenant.partials.tables.supervisors-table',
            'form' => 'tenant.partials.forms.supervisor-form',
        ],
        'students' => [
            'title' => 'Students',
            'create_title' => 'Student',
            'empty' => 'No students yet.',
            'table' => 'tenant.partials.tables.students-table',
            'form' => 'tenant.partials.forms.student-form',
        ],
        'users' => [
            'title' => 'RBAC & User Management',
            'empty' => 'No university portal users yet.',
            'table' => 'tenant.partials.tables.users-table',
            'form' => 'tenant.partials.forms.user-management-form',
        ],
        'requirements' => [
            'title' => 'Forms & Requirements',
            'create_title' => 'Form / Requirement',
            'empty' => 'No forms or requirements submitted yet.',
            'table' => 'tenant.partials.tables.requirements-table',
            'form' => 'tenant.partials.forms.requirement-form',
        ],
        'hours' => [
            'title' => 'Progress & Hour Logs',
            'create_title' => 'Progress / Hour Log',
            'empty' => 'No progress or hour logs yet.',
            'table' => 'tenant.partials.tables.hour-logs-table',
            'form' => 'tenant.partials.forms.hour-log-form',
        ],
    ];

    if (! array_key_exists($currentSection, $sections)) {
        $currentSection = 'companies';
    }

    $section = $sections[$currentSection];
    $editingCompany = $editing['companies'] ?? null;
    $editingSupervisor = $editing['supervisors'] ?? null;
    $editingStudent = $editing['students'] ?? null;
    $editingRequirement = $editing['requirements'] ?? null;
    $editingHour = $editing['hours'] ?? null;
    $editingUser = $editing['users'] ?? null;
    $showCreatePanel = ($createSection === $currentSection || $errors->any()) && $currentSection !== 'users';
    $showEditPanel = filled(match ($currentSection) {
        'companies' => $editingCompany,
        'supervisors' => $editingSupervisor,
        'students' => $editingStudent,
        'requirements' => $editingRequirement,
        'hours' => $editingHour,
        'users' => $editingUser,
        default => null,
    });
    $dashboardBaseUrl = route('tenant.admin.dashboard');
    $baseSectionUrl = $dashboardBaseUrl.'?section='.$currentSection;
    $sectionCreateTitle = $section['create_title'] ?? \Illuminate\Support\Str::singular($section['title']);
@endphp

@extends('layouts.tenant')

@section('content')
    <section class="admin-hero">
        <div class="admin-hero-copy">
            <span class="admin-eyebrow">Tenant Workspace</span>
            <h1>{{ $tenant->name }}</h1>
            <p>Internship Coordinator Dashboard for {{ $sections[$currentSection]['title'] }}.</p>
            <div class="admin-action-row">
                @if ($currentSection === 'users')
                    <a class="button" href="{{ $dashboardBaseUrl.'?section=students&create=students' }}">Create Student</a>
                    <a class="button secondary" href="{{ $dashboardBaseUrl.'?section=supervisors&create=supervisors' }}">Create Supervisor</a>
                @else
                    <a class="button" href="{{ $dashboardBaseUrl.'?section='.$currentSection.'&create='.$currentSection }}">Add Record</a>
                    <a class="button secondary" href="{{ route('tenant.admin.profile.show') }}">Open Profile</a>
                @endif
            </div>
        </div>

        <div class="admin-hero-metrics">
            <article class="admin-hero-panel">
                <span>Organizations</span>
                <strong>{{ $stats['companies'] }}</strong>
                <small>Partner companies in this tenant portal</small>
            </article>
            <article class="admin-hero-panel">
                <span>Students</span>
                <strong>{{ $stats['students'] }}</strong>
                <small>Intern records currently tracked</small>
            </article>
            <article class="admin-hero-panel">
                <span>Users</span>
                <strong>{{ $stats['users'] }}</strong>
                <small>Coordinators, supervisors, and students with access</small>
            </article>
        </div>
    </section>

    <section class="admin-kpi-grid">
        <article class="admin-stat-card">
            <span>Applications</span>
            <strong>{{ $stats['applications'] }}</strong>
            <small>Internship submissions across this tenant</small>
        </article>
        <article class="admin-stat-card">
            <span>Supervisors</span>
            <strong>{{ $stats['supervisors'] }}</strong>
            <small>Partner-company supervisors available</small>
        </article>
        <article class="admin-stat-card">
            <span>Approved Requirements</span>
            <strong>{{ $stats['approved_requirements'] }}</strong>
            <small>Documents cleared by coordinators</small>
        </article>
        <article class="admin-stat-card">
            <span>Approved Hours</span>
            <strong>{{ number_format($stats['approved_hours'], 0) }}</strong>
            <small>Validated practicum hours recorded</small>
        </article>
    </section>

    @if ($errors->any())
        <div class="error-panel">
            <strong>Some university portal updates did not complete.</strong>
            <ul style="margin:8px 0 0;padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif

    @if ($currentSection === 'users')
        <section class="chart-grid" style="margin-bottom:20px;">
            <article class="section-card">
                <div class="action-row">
                    <h2>Tenant Admin RBAC</h2>
                    <span class="pill">Tenant Scope</span>
                </div>
                <ul class="soft-list" style="margin-top:16px;">
                    <li>Internship coordinators control tenant users only inside this university portal.</li>
                    <li>You can activate, suspend, and reassign users between student, supervisor, and coordinator roles.</li>
                    <li>Central superadmin still controls tenant creation, subscription, activation, and approved domains.</li>
                </ul>
            </article>

            <article class="section-card">
                <div class="action-row">
                    <h2>Quick Access</h2>
                    <span class="pill">RBAC UI</span>
                </div>
                <div class="action-row-actions" style="margin-top:16px;">
                    <a class="button secondary" href="{{ $dashboardBaseUrl.'?section=students&create=students' }}">Create Student</a>
                    <a class="button secondary" href="{{ $dashboardBaseUrl.'?section=supervisors&create=supervisors' }}">Create Supervisor</a>
                    <a class="button secondary" href="{{ $dashboardBaseUrl.'?section=users' }}">Manage Roles</a>
                </div>
            </article>
        </section>
    @endif

    <section id="{{ $currentSection }}" class="content-stack section-anchor">
        <article class="section-card">
            <div class="action-row">
                <h2>{{ $section['title'] }}</h2>
                <div class="action-row-actions">
                    @if ($currentSection === 'users')
                        <a class="panel-link" href="{{ $dashboardBaseUrl.'?section=students&create=students' }}">Add Student</a>
                    @else
                        <a class="panel-link" href="{{ $dashboardBaseUrl.'?section='.$currentSection.'&create='.$currentSection }}">Add Record</a>
                    @endif
                </div>
            </div>

            @if ($showCreatePanel)
                <div class="form-panel">
                    <div class="form-panel-header">
                        <h3>New {{ $sectionCreateTitle }}</h3>
                        <a class="panel-close" href="{{ $baseSectionUrl }}">&times;</a>
                    </div>

                    @include($section['form'], ['embedded' => true, 'showHeading' => false])
                </div>
            @endif

            @if ($showEditPanel)
                <div class="form-panel">
                    <div class="form-panel-header">
                        <h3>Edit {{ $section['title'] === 'User Management' ? 'User' : $sectionCreateTitle }}</h3>
                        <a class="panel-close" href="{{ $baseSectionUrl }}">&times;</a>
                    </div>

                    @include($section['form'], ['embedded' => true, 'showHeading' => false, 'mode' => 'edit'])
                </div>
            @endif

            @include($section['table'], ['embedded' => true, 'showHeading' => false])

            @if ($currentSection === 'students' && $selectedStudentForApplications)
                <div class="form-panel">
                    <div class="form-panel-header">
                        <h3>Applications for {{ $selectedStudentForApplications->full_name }}</h3>
                        <a class="panel-close" href="{{ $baseSectionUrl }}">&times;</a>
                    </div>

                    @include('tenant.partials.tables.applications-table', [
                        'embedded' => true,
                        'showHeading' => false,
                        'applications' => $selectedStudentApplications,
                        'applicationSection' => 'students',
                    ])
                </div>
            @endif
        </article>
    </section>
@endsection
