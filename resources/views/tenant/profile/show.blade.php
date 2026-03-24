@php
    $layoutMode = 'dashboard';
@endphp

@extends('layouts.tenant')

@section('content')
    <section class="page-head">
        <div>
            <h1>Profile</h1>
            <p>{{ ucfirst($profileRole) }} account for {{ $tenant->name }}</p>
        </div>
    </section>

    @if ($errors->any())
        <div class="error-panel">
            <strong>Some profile updates did not complete.</strong>
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

    <section class="profile-grid">
        <article class="section-card">
            <div class="section-header">
                <h2>Account Info</h2>
                <span class="pill">{{ strtoupper($profileRole) }}</span>
            </div>

            <form method="POST" action="{{ $profileUpdateAction }}" class="form-grid" style="margin-top:16px;">
                @csrf
                @method('PATCH')

                @if ($profileRole === 'student')
                    <label>Student Number <input type="text" value="{{ $profileUser->student_number }}" readonly></label>
                    <label>Status <input type="text" value="{{ ucfirst($profileUser->status) }}" readonly></label>
                    <label>First Name <input type="text" name="first_name" value="{{ old('first_name', $profileUser->first_name) }}" required></label>
                    <label>Last Name <input type="text" name="last_name" value="{{ old('last_name', $profileUser->last_name) }}" required></label>
                    <label class="field-span-2">Email <input type="email" name="email" value="{{ old('email', $profileUser->email) }}" required></label>
                    <label class="field-span-2">Program <input type="text" name="program" value="{{ old('program', $profileUser->program) }}"></label>
                    <label>Required Hours <input type="text" value="{{ $profileUser->required_hours }}" readonly></label>
                    <label>Completed Hours <input type="text" value="{{ $profileUser->completed_hours }}" readonly></label>
                @elseif ($profileRole === 'supervisor')
                    <label>Name <input type="text" name="name" value="{{ old('name', $profileUser->name) }}" required></label>
                    <label>Email <input type="email" name="email" value="{{ old('email', $profileUser->email) }}" required></label>
                    <label class="field-span-2">Position <input type="text" name="position" value="{{ old('position', $profileUser->position) }}"></label>
                    <label class="field-span-2">Company <input type="text" value="{{ $profileUser->partnerCompany?->name ?: 'Unassigned' }}" readonly></label>
                @else
                    <label>Name <input type="text" name="name" value="{{ old('name', $profileUser->name) }}" required></label>
                    <label>Email <input type="email" name="email" value="{{ old('email', $profileUser->email) }}" required></label>
                    <label class="field-span-2">Tenant <input type="text" value="{{ $tenant->name }}" readonly></label>
                @endif

                <div class="field-span-2">
                    <button type="submit">Save Profile</button>
                </div>
            </form>
        </article>

        <article class="section-card">
            <div class="section-header">
                <h2>Change Password</h2>
                <span class="pill">Secure</span>
            </div>

            <form method="POST" action="{{ $passwordUpdateAction }}" class="form-grid" style="margin-top:16px;">
                @csrf
                @method('PUT')
                <label class="field-span-2">Current Password <input type="password" name="current_password" required></label>
                <label>New Password <input type="password" name="password" required></label>
                <label>Confirm Password <input type="password" name="password_confirmation" required></label>
                <div class="field-span-2">
                    <button type="submit">Update Password</button>
                </div>
            </form>
        </article>
    </section>
@endsection
