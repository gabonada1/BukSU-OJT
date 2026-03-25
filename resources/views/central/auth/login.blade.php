@php
    $layoutMode = 'login';
    $hideCentralHeader = true;
    $systemLogo = asset('images/logos/logo.jpg');
@endphp

@extends('layouts.central')

@section('content')
    <section class="login-stage">
        <article class="login-panel">
            <div class="login-panel-brand">
                <img src="{{ $systemLogo }}" alt="BukSU Logo" class="login-panel-logo">
                <div class="eyebrow">University Access</div>
            </div>

            <div class="login-panel-copy">
                <h1>BukSU Practicum Portal</h1>
                <p class="login-panel-subtitle">University Administration Access</p>
                <p class="lead">
                    Sign in to manage college registrations, monitor portal access, and oversee practicum operations
                    across Bukidnon State University.
                </p>
                <div class="login-divider"></div>
                <p class="login-university-tagline">Bukidnon State University - Office of Practicum Affairs</p>
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

            <form method="POST" action="{{ $loginAction }}" class="login-form">
                @csrf
                <label>
                    Email
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="university-admin@lvh.me" required>
                </label>
                <label>
                    Password
                    <input type="password" name="password" placeholder="Enter your University Administration password" required>
                </label>
                <label class="checkline">
                    <input type="checkbox" name="remember" value="1">
                    Keep me signed in on this browser
                </label>
                <button type="submit">Sign In to University Administration</button>
            </form>

            <div class="login-support">
                <strong>BukSU University Admin</strong>
                <p>Manage college portals, license periods, and access from one institutional administration workspace.</p>
                <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:14px;">
                    <span class="pill">College Registry</span>
                    <span class="pill">Portal Access</span>
                    <span class="pill">License Tracking</span>
                </div>
            </div>
        </article>

        <article class="login-art">
            <div class="logo-showcase">
                <div class="logo-showcase-frame">
                    <img src="{{ $systemLogo }}" alt="BukSU Logo" class="logo-showcase-image">
                </div>
                <div class="logo-showcase-copy">
                    <div class="eyebrow">University Administration</div>
                    <h3>BukSU Practicum Portal</h3>
                    <p>College registration, portal lifecycle management, and practicum oversight in one BukSU system.</p>
                    <span class="preview-chip">University Administration</span>
                </div>
            </div>
        </article>
    </section>
@endsection
