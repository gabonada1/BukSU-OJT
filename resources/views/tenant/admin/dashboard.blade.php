@php
    $layoutMode = 'dashboard';
    $currentSection = request()->query('section', 'companies');
    $createSection = request()->query('create');

    $sections = [
        'companies' => [
            'title' => 'Companies',
            'create_title' => 'Company',
            'empty' => 'No partner companies yet.',
            'table' => 'tenant.partials.tables.partner-companies-table',
            'form' => 'tenant.partials.forms.partner-company-form',
        ],
        'applications' => [
            'title' => 'Applications',
            'create_title' => 'Application',
            'empty' => 'No internship applications yet.',
            'table' => 'tenant.partials.tables.applications-table',
            'form' => 'tenant.partials.forms.application-form',
        ],
        'supervisors' => [
            'title' => 'Supervisors',
            'create_title' => 'Supervisor',
            'empty' => 'No supervisors yet.',
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
            'title' => 'User Management',
            'empty' => 'No tenant users yet.',
            'table' => 'tenant.partials.tables.users-table',
            'form' => 'tenant.partials.forms.user-management-form',
        ],
        'requirements' => [
            'title' => 'Requirements',
            'create_title' => 'Requirement',
            'empty' => 'No requirements submitted yet.',
            'table' => 'tenant.partials.tables.requirements-table',
            'form' => 'tenant.partials.forms.requirement-form',
        ],
        'hours' => [
            'title' => 'Hour Logs',
            'create_title' => 'Hour Log',
            'empty' => 'No hour logs yet.',
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
    $dashboardBaseUrl = request()->routeIs('tenant.domain.*')
        ? route('tenant.domain.admin.dashboard')
        : route('tenant.admin.dashboard', $tenant);
    $baseSectionUrl = $dashboardBaseUrl.'?section='.$currentSection;
@endphp

@extends('layouts.tenant')

@section('content')
    <section class="page-head">
        <div>
            <h1>Admin Dashboard</h1>
            <p>{{ $tenant->name }}</p>
        </div>

        <div class="page-mini-stats">
            <div class="page-mini-card">
                <strong>Companies</strong>
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
            <strong>Some admin actions did not complete.</strong>
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

    <section id="{{ $currentSection }}" class="content-stack section-anchor">
        <article class="section-card">
            <div class="action-row">
                <h2>{{ $section['title'] }}</h2>
                <div class="action-row-actions">
                    @if ($currentSection === 'users')
                        <a class="panel-link" href="{{ $dashboardBaseUrl.'?section=students&create=students' }}">Add Student</a>
                    @else
                        <a class="panel-link" href="{{ $dashboardBaseUrl.'?section='.$currentSection.'&create='.$currentSection }}">Add</a>
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
