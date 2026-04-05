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
    $emailPlaceholder = 'you@buksu.edu.ph';
    $selectedLoginRole = $selectedLoginRole ?? null;
    $tenantAccessLabel = preg_replace('#^https?://#', '', app(\App\Support\Tenancy\TenantUrlGenerator::class)->loginUrl($tenant));
@endphp

@extends('layouts.tenant')

@section('content')
    <section class="lovable-auth-shell">
        <article class="lovable-auth-card">
            <div class="lovable-auth-brand">
                <div class="console-brand-mark">
                    <img src="{{ $systemLogo }}" alt="{{ $tenantPortalTitle }} Logo" class="brand-logo-image">
                </div>
                <h1>{{ $tenant->name }}</h1>
                <p>{{ $tenantPortalTitle }}</p>
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

            <form method="POST" action="{{ $loginAction }}" class="login-form lovable-auth-form">
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
            </form>

            <div class="login-form-actions">
                <a href="{{ $registerUrl }}" class="button secondary">Register</a>
            </div>

            <div class="lovable-auth-note">
                <strong>{{ $tenant->code ?: 'UNIVERSITY PORTAL' }}</strong>
                <p>Use your university portal email to access coordinator, student, or supervisor workspaces.</p>
            </div>
        </article>
    </section>
@endsection
