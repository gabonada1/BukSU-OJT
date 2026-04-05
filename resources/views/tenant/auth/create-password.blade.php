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
@endphp

@extends('layouts.tenant')

@section('content')
    <section class="login-stage">
        <article class="login-panel">
            <div class="login-panel-brand">
                <img src="{{ $systemLogo }}" alt="{{ $tenantPortalTitle }} Logo" class="login-panel-logo">
                <div class="eyebrow">First Login</div>
            </div>

            <div class="login-panel-copy">
                <h1>Create a New Password</h1>
                <p class="login-panel-subtitle">{{ $tenantPortalTitle }}</p>
                <p class="lead">
                    Your account was created with a temporary password. Create your personal password now before continuing to the university portal.
                </p>
                <div class="login-divider"></div>
                <p class="login-university-tagline">{{ $tenant->name }}</p>
            </div>

            @if ($errors->any())
                <div class="error-panel">
                    <strong>Password not updated.</strong>
                    <ul style="margin:8px 0 0;padding-left:18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ $passwordSetupAction }}" class="login-form">
                @csrf
                <label>
                    New Password
                    <input type="password" name="password" placeholder="Create a strong password" required>
                </label>
                <label>
                    Confirm Password
                    <input type="password" name="password_confirmation" placeholder="Confirm your new password" required>
                </label>
                <button type="submit">Save New Password</button>
            </form>
        </article>

        <article class="login-art">
            <div class="logo-showcase">
                <div class="logo-showcase-frame">
                    <img src="{{ $systemLogo }}" alt="{{ $tenantPortalTitle }} Logo" class="logo-showcase-image">
                </div>
                <div class="logo-showcase-copy">
                    <div class="eyebrow">{{ $tenantPortalTitle }}</div>
                    <h3>{{ $tenant->name }}</h3>
                    <p>After saving your new password, you will be sent directly to the tenant admin dashboard.</p>
                    <span class="preview-chip">Password Setup Required</span>
                </div>
            </div>
        </article>
    </section>
@endsection
