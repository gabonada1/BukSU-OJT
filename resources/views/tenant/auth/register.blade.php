@php
    $layoutMode = 'login';
    $hideTenantHeader = true;
    $systemLogo = asset('images/logos/logo.jpg');
@endphp

@extends('layouts.tenant')

@section('content')
    <section class="login-stage">
        <article class="login-panel">
            <div>
                <div class="eyebrow">Tenant Registration</div>
                <h1>Create Your Practicum Access</h1>
                <p class="lead">
                    Register under {{ $tenant->name }} and choose whether you are joining as a student or teacher. Once you verify your email, your account will appear in tenant user management and you can sign in to the correct workspace.
                </p>
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
                    <span>Apply for internship placements and upload practicum requirements.</span>
                </a>
                <a href="{{ $registerPageUrl }}?role=teacher" class="role-switch-card {{ $selectedRole === 'teacher' ? 'active' : '' }}">
                    <strong>Teacher</strong>
                    <span>Register as a faculty/supervisor reviewer for student practicum activity.</span>
                </a>
            </div>

            @if ($selectedRole === 'student')
                <form method="POST" action="{{ $registerAction }}" class="login-form">
                    @csrf
                    <input type="hidden" name="role" value="student">
                    <label>Student Number <input type="text" name="student_number" value="{{ old('student_number') }}" required></label>
                    <label>First Name <input type="text" name="first_name" value="{{ old('first_name') }}" required></label>
                    <label>Last Name <input type="text" name="last_name" value="{{ old('last_name') }}" required></label>
                    <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label>
                    <label>Program <input type="text" name="program" value="{{ old('program', 'BS Information Technology') }}"></label>
                    <label>Password <input type="password" name="password" required></label>
                    <label>Confirm Password <input type="password" name="password_confirmation" required></label>
                    <button type="submit">Register Student</button>
                    <a href="{{ $loginUrl }}" class="button secondary">Back to Login</a>
                </form>
            @elseif ($selectedRole === 'teacher')
                <form method="POST" action="{{ $registerAction }}" class="login-form">
                    @csrf
                    <input type="hidden" name="role" value="teacher">
                    <label>Full Name <input type="text" name="name" value="{{ old('name') }}" required></label>
                    <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label>
                    <label>Department / Unit <input type="text" name="department" value="{{ old('department', 'College of Technology') }}" required></label>
                    <label>Position / Title <input type="text" name="position" value="{{ old('position', 'Faculty Supervisor') }}" required></label>
                    <label>Password <input type="password" name="password" required></label>
                    <label>Confirm Password <input type="password" name="password_confirmation" required></label>
                    <button type="submit">Register Teacher</button>
                    <a href="{{ $loginUrl }}" class="button secondary">Back to Login</a>
                </form>
            @else
                <div class="login-support" style="margin-top:0;">
                    <strong>Choose a role to continue.</strong>
                    <p>Select Student or Teacher above to open the right registration form for your practicum access.</p>
                </div>
            @endif
        </article>

        <article class="login-art">
            <div class="logo-showcase">
                <div class="logo-showcase-frame">
                    <img src="{{ $systemLogo }}" alt="BukSU Logo" class="logo-showcase-image">
                </div>
                <div class="logo-showcase-copy">
                    <div class="eyebrow">Role-Based Access</div>
                    <h3>{{ $tenant->name }}</h3>
                    <p>Students can apply for placements and upload documents. Teachers can review practicum activity after verification.</p>
                    <span class="preview-chip">{{ $tenant->domain ?: '/tenants/'.$tenant->slug }}</span>
                </div>
            </div>
        </article>
    </section>
@endsection
