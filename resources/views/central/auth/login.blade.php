@php
    $layoutMode = 'login';
    $hideCentralHeader = true;
    $systemLogo = asset('images/logos/logo.jpg');
@endphp

@extends('layouts.central')

@section('content')
    <section class="lovable-auth-shell">
        <article class="lovable-auth-card">
            <div class="lovable-auth-brand">
                <div class="console-brand-mark">
                    <img src="{{ $systemLogo }}" alt="Bukidnon State University Logo" class="brand-logo-image">
                </div>
                <h1>BukSU Admin System</h1>
                <p>University Administration Access</p>
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

            <form method="POST" action="{{ $loginAction }}" class="login-form lovable-auth-form">
                @csrf
                <label>
                    Email
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@buksu.edu.ph" required>
                </label>
                <label>
                    Password
                    <input type="password" name="password" placeholder="Enter your password" required>
                </label>
                <label class="checkline">
                    <input type="checkbox" name="remember" value="1">
                    Keep me signed in on this browser
                </label>
                <button type="submit">Sign In</button>
            </form>

            <div class="lovable-auth-note">
                <strong>University Registry</strong>
                <p>Manage college registrations, portal access, and subscription oversight from one superadmin workspace.</p>
            </div>
        </article>
    </section>
@endsection
