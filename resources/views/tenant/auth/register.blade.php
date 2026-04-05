@php
    $layoutMode = 'login';
    $hideTenantHeader = true;
    $tenantBranding = is_array($tenant->settings['branding'] ?? null) ? $tenant->settings['branding'] : [];
    $tenantPortalTitle = filled($tenantBranding['portal_title'] ?? null)
        ? $tenantBranding['portal_title']
        : config('app.name', 'University Practicum');
    $systemLogo = filled($tenantBranding['logo_path'] ?? null)
        ? asset($tenantBranding['logo_path'])
        : asset('images/logos/logo.jpg');
    $hasCourses = $courses->isNotEmpty();
    $departmentOptions = $courses->map(function ($course) {
        return trim($course->code.' - '.$course->name);
    });
    $tenantAccessLabel = preg_replace('#^https?://#', '', app(\App\Support\Tenancy\TenantUrlGenerator::class)->loginUrl($tenant));
@endphp

@extends('layouts.tenant')

@section('content')
    <section class="lovable-auth-shell lovable-auth-wide">
        <article class="lovable-auth-card lovable-auth-card-wide">
            <div class="lovable-auth-brand">
                <div class="console-brand-mark">
                    <img src="{{ $systemLogo }}" alt="{{ $tenantPortalTitle }} Logo" class="brand-logo-image">
                </div>
                <h1>Register for {{ $tenant->name }}</h1>
                <p>{{ $tenantPortalTitle }}</p>
            </div>

            @if ($errors->any())
                <div class="error-panel">
                    <strong>Registration failed.</strong>
                    <ul style="margin:8px 0 0;padding-left:18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="role-switch-grid">
                <a href="{{ $registerPageUrl }}?role=student" class="role-switch-card {{ $selectedRole === 'student' ? 'active' : '' }}">
                    <strong>Student</strong>
                    <span>View partner companies, apply for internship slots, and upload requirements and reports.</span>
                </a>
                <a href="{{ $registerPageUrl }}?role=teacher" class="role-switch-card {{ $selectedRole === 'teacher' ? 'active' : '' }}">
                    <strong>Company Supervisor</strong>
                    <span>Accept assigned students, validate reports, log hours, and submit evaluations.</span>
                </a>
            </div>

            @if ($selectedRole === 'student')
                <form method="POST" action="{{ $registerAction }}" class="login-form lovable-auth-form">
                    @csrf
                    <input type="hidden" name="role" value="student">
                    <label>Student Number <input type="text" name="student_number" value="{{ old('student_number') }}" required></label>
                    <label>First Name <input type="text" name="first_name" value="{{ old('first_name') }}" required></label>
                    <label>Last Name <input type="text" name="last_name" value="{{ old('last_name') }}" required></label>
                    <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label>

                    @if ($hasCourses)
                        <label>
                            Course / Program
                            <select name="course_id" class="select-input">
                                <option value="">- Select Course -</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}" @selected((string) old('course_id') === (string) $course->id)>
                                        {{ $course->code }} - {{ $course->name }} ({{ number_format($course->required_ojt_hours, 0) }} hrs)
                                    </option>
                                @endforeach
                            </select>
                            <small class="field-hint">Your required OJT hours will follow the selected course.</small>
                        </label>
                    @else
                        <label>
                            Program
                            <input type="text" name="program" value="{{ old('program', 'BS Information Technology') }}">
                        </label>
                    @endif

                    <label>Password <input type="password" name="password" required></label>
                    <label>Confirm Password <input type="password" name="password_confirmation" required></label>
                    <button type="submit">Register Student</button>
                    <a href="{{ $loginUrl }}" class="button secondary">Back to Login</a>
                </form>
            @elseif ($selectedRole === 'teacher')
                <form method="POST" action="{{ $registerAction }}" class="login-form lovable-auth-form">
                    @csrf
                    <input type="hidden" name="role" value="teacher">
                    <label>Full Name <input type="text" name="name" value="{{ old('name') }}" required></label>
                    <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label>
                    @if ($departmentOptions->isNotEmpty())
                        <label>
                            Department / Unit
                            <select name="department" class="select-input" required>
                                <option value="">- Select Course -</option>
                                @foreach ($departmentOptions as $option)
                                    <option value="{{ $option }}" @selected(old('department') === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                            <small class="field-hint">Choose the course or program this company supervisor will handle.</small>
                        </label>
                    @else
                        <label>Department / Unit <input type="text" name="department" value="{{ old('department', $tenant->name) }}" required></label>
                    @endif
                    <label>Position / Title <input type="text" name="position" value="{{ old('position', 'Company Supervisor') }}" required></label>
                    <label>Password <input type="password" name="password" required></label>
                    <label>Confirm Password <input type="password" name="password_confirmation" required></label>
                    <button type="submit">Register Company Supervisor</button>
                    <a href="{{ $loginUrl }}" class="button secondary">Back to Login</a>
                </form>
            @else
                <div class="lovable-auth-note" style="margin-top:0;">
                    <strong>Choose a role to continue.</strong>
                    <p>Select Student or Company Supervisor above to open the right registration form for your practicum access.</p>
                </div>
            @endif
        </article>
    </section>
@endsection
