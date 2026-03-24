@php
    $layoutMode = 'login';
    $hideTenantHeader = true;
    $systemLogo = asset('images/logos/logo.jpg');
    $emailPlaceholder = 'you@'.($tenant->domain ?: 'tenant-domain.test');
@endphp

@extends('layouts.tenant')

@section('content')
    <section class="login-stage">
        <article class="login-panel">
            <div>
                <div class="eyebrow">Tenant Access</div>
                <h1>Practicum Workspace Login</h1>
                <p class="lead">
                    Sign in with your tenant email and the system will route you to the correct admin, teacher, or
                    student workspace for this practicum environment.
                </p>
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
                <p>Use your tenant email to access the practicum workspace, or register as a student or teacher first.</p>
                <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:14px;">
                    <span class="pill">{{ $tenant->code ?: strtoupper($tenant->slug) }}</span>
                    <span class="pill">{{ strtoupper($tenant->plan) }} Plan</span>
                </div>
            </div>
        </article>

        <article class="login-art">
            <div class="logo-showcase">
                <div class="logo-showcase-frame">
                    <img src="{{ $systemLogo }}" alt="BukSU Logo" class="logo-showcase-image">
                </div>
                <div class="logo-showcase-copy">
                    <div class="eyebrow">BukSU Practicum</div>
                    <h3>{{ $tenant->name }}</h3>
                    <p>Focused access for practicum records, student progress, and deployment workflows.</p>
                    <span class="preview-chip">{{ $tenant->domain ?: '/tenants/'.$tenant->slug }}</span>
                </div>
            </div>
        </article>
    </section>
@endsection
