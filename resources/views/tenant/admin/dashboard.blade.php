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
        'applications' => [
            'title' => 'Student Applications',
            'create_title' => 'Student Application',
            'empty' => 'No student applications yet.',
            'table' => 'tenant.partials.tables.applications-table',
            'form' => 'tenant.partials.forms.application-form',
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
            'empty' => 'No college portal users yet.',
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
    $editingApplication = $editing['applications'] ?? null;
    $editingSupervisor = $editing['supervisors'] ?? null;
    $editingStudent = $editing['students'] ?? null;
    $editingRequirement = $editing['requirements'] ?? null;
    $editingHour = $editing['hours'] ?? null;
    $editingUser = $editing['users'] ?? null;
    $showCreatePanel = ($createSection === $currentSection || $errors->any()) && $currentSection !== 'users';
    $showEditPanel = filled(match ($currentSection) {
        'companies' => $editingCompany,
        'applications' => $editingApplication,
        'supervisors' => $editingSupervisor,
        'students' => $editingStudent,
        'requirements' => $editingRequirement,
        'hours' => $editingHour,
        'users' => $editingUser,
        default => null,
    });
    $dashboardBaseUrl = route('tenant.admin.dashboard');
    $baseSectionUrl = $dashboardBaseUrl.'?section='.$currentSection;
@endphp

@extends('layouts.tenant')

@section('content')
    <section class="page-head">
        <div>
            <h1>{{ $tenant->name }}</h1>
            <p>Internship Coordinator Dashboard - {{ $sections[$currentSection]['title'] }}</p>
        </div>

        <div class="page-mini-stats">
            <div class="page-mini-card">
                <strong>Organizations</strong>
                <span>{{ $stats['companies'] }}</span>
            </div>
            <div class="page-mini-card">
                <strong>Applications</strong>
                <span>{{ $stats['applications'] }}</span>
            </div>
            <div class="page-mini-card">
                <strong>Students</strong>
                <span>{{ $stats['students'] }}</span>
            </div>
            <div class="page-mini-card">
                <strong>Users</strong>
                <span>{{ $stats['users'] }}</span>
            </div>
        </div>
    </section>

    @if ($errors->any())
        <div class="error-panel">
            <strong>Some college portal updates did not complete.</strong>
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
                    <li>Internship coordinators control tenant users only inside this college portal.</li>
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
                        <h3>New {{ $section['create_title'] }}</h3>
                        <a class="panel-close" href="{{ $baseSectionUrl }}">&times;</a>
                    </div>

                    @include($section['form'], ['embedded' => true, 'showHeading' => false])
                </div>
            @endif

            @if ($showEditPanel)
                <div class="form-panel">
                    <div class="form-panel-header">
                        <h3>Edit {{ $section['title'] === 'User Management' ? 'User' : $section['create_title'] }}</h3>
                        <a class="panel-close" href="{{ $baseSectionUrl }}">&times;</a>
                    </div>

                    @include($section['form'], ['embedded' => true, 'showHeading' => false, 'mode' => 'edit'])
                </div>
            @endif

            @include($section['table'], ['embedded' => true, 'showHeading' => false])
        </article>
    </section>
@endsection
