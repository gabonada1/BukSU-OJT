@php
    $layoutMode = 'login';
    $hideTenantHeader = true;
    $tenantBranding = is_array($tenant->settings['branding'] ?? null) ? $tenant->settings['branding'] : [];
    $tenantPortalTitle = filled($tenantBranding['portal_title'] ?? null)
        ? $tenantBranding['portal_title']
        : config('app.name', 'BukSU Practicum Portal');
    $systemLogo = filled($tenantBranding['logo_path'] ?? null)
        ? asset($tenantBranding['logo_path'])
        : asset('images/logos/logo.jpg');
    $emailPlaceholder = 'you@college-portal.local';
    $selectedLoginRole = $selectedLoginRole ?? null;
    $tenantAccessLabel = preg_replace('#^https?://#', '', app(\App\Support\Tenancy\TenantUrlGenerator::class)->loginUrl($tenant));
@endphp

@extends('layouts.tenant')

@section('content')
    <section class="login-stage">
        <article class="login-panel">
            <div class="login-panel-brand">
                <img src="{{ $systemLogo }}" alt="{{ $tenantPortalTitle }} Logo" class="login-panel-logo">
                <div class="eyebrow">College Access</div>
            </div>

            <div class="login-panel-copy">
                <h1>{{ $tenant->name }}</h1>
                <p class="login-panel-subtitle">{{ $tenantPortalTitle }}</p>
                <p class="lead">
                    Sign in with your college portal account to continue to the correct internship coordinator, company supervisor, or
                    student workspace.
                </p>
                <div class="login-divider"></div>
                <p class="login-university-tagline">Bukidnon State University</p>
            </div>

            @if ($errors->any())
                <div class="error-panel">
                    <strong>Login failed.</strong>
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

            <form method="POST" action="{{ $loginAction }}" class="login-form">
                @csrf
                @if ($selectedLoginRole)
                    <input type="hidden" name="role" value="{{ $selectedLoginRole }}">
                @endif
                <label>
                    Email
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ $emailPlaceholder }}" required>
                </label>
                <label>
                    Password
                    <input type="password" name="password" placeholder="Enter your password" required>
                </label>
                <label class="checkline">
                    <input type="checkbox" name="remember" value="1">
                    Keep me signed in on this device
                </label>
                <button type="submit">Sign In</button>
                <a href="{{ $registerUrl }}" class="button secondary">Register</a>
            </form>

            <div class="login-support">
                <strong>{{ $tenant->name }}</strong>
                <p>Use your college portal email to access internship records, or register as a student or company supervisor first.</p>
                <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:14px;">
                    <span class="pill">{{ $tenant->code ?: 'COLLEGE PORTAL' }}</span>
                    <span class="pill">{{ strtoupper($tenant->plan) }} License</span>
                </div>
            </div>
        </article>

        <article class="login-art">
            <div class="logo-showcase">
                <div class="logo-showcase-frame">
                    <img src="{{ $systemLogo }}" alt="{{ $tenantPortalTitle }} Logo" class="logo-showcase-image">
                </div>
                <div class="logo-showcase-copy">
                    <div class="eyebrow">{{ $tenantPortalTitle }}</div>
                    <h3>{{ $tenant->name }}</h3>
                    <p>Focused access for partner companies, student applications, progress reports, and evaluation workflows.</p>
                    <span class="preview-chip">{{ $tenantAccessLabel }}</span>
                </div>
            </div>
        </article>
    </section>
@endsection
