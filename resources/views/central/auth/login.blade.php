@php
    $layoutMode = 'login';
    $hideCentralHeader = true;
    $systemLogo = asset('images/logos/logo.jpg');
@endphp

@extends('layouts.central')

@section('content')
    <section class="login-stage">
        <article class="login-panel">
            <div>
                <div class="eyebrow">Central Access</div>
                <h1>BukSU Superadmin Portal</h1>
                <p class="lead">
                    Sign in to manage tenant subscriptions, create new college workspaces, and control access across the
                    multitenant practicum platform.
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

            <form method="POST" action="{{ $loginAction }}" class="login-form">
                @csrf
                <label>
                    Email
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="superadmin@buksu.test" required>
                </label>
                <label>
                    Password
                    <input type="password" name="password" placeholder="Enter your superadmin password" required>
                </label>
                <label class="checkline">
                    <input type="checkbox" name="remember" value="1">
                    Keep me signed in on this browser
                </label>
                <button type="submit">Sign In to Central</button>
            </form>

            <div class="login-support">
                <strong>BukSU Central</strong>
                <p>Manage tenants, subscriptions, and access from one central workspace.</p>
                <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:14px;">
                    <span class="pill">Provisioning</span>
                    <span class="pill">Subscriptions</span>
                    <span class="pill">Tenant Directory</span>
                </div>
            </div>
        </article>

        <article class="login-art">
            <div class="logo-showcase">
                <div class="logo-showcase-frame">
                    <img src="{{ $systemLogo }}" alt="BukSU Logo" class="logo-showcase-image">
                </div>
                <div class="logo-showcase-copy">
                    <div class="eyebrow">Central Control</div>
                    <h3>BukSU Practicum</h3>
                    <p>Tenant provisioning, subscription management, and domain access in one central hub.</p>
                    <span class="preview-chip">Central Application</span>
                </div>
            </div>
        </article>
    </section>
@endsection
