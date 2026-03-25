@php
    $layoutMode = 'dashboard';
    $hideCentralHeader = true;
    $systemLogo = asset('images/logos/logo.jpg');
@endphp

@extends('layouts.central')

@section('content')
    <section class="marketing-shell">
        <section class="marketing-hero section-card">
            <div class="marketing-hero-copy">
                <div class="eyebrow">BukSU Practicum Portal</div>
                <h1>Central landing for college plan applications, approvals, and tenant onboarding.</h1>
                <p class="lead">
                    This is the central application. Colleges can review plans, see the benefits of the system, and apply for a
                    practicum portal. After Stripe test payment, the BukSU admin approves the request and provisions the tenant database.
                </p>

                <div class="action-row-actions" style="margin-top:18px;">
                    <a href="#apply" class="button">Apply for a Plan</a>
                    <a href="{{ $centralLoginUrl }}" class="button secondary">Central Admin Login</a>
                </div>

                <div class="marketing-stat-grid">
                    <article class="metric-card">
                        <strong>{{ $stats['active_tenants'] }}</strong>
                        <span>Active college tenants</span>
                    </article>
                    <article class="metric-card">
                        <strong>{{ $stats['submitted_applications'] }}</strong>
                        <span>Applications waiting for approval</span>
                    </article>
                    <article class="metric-card">
                        <strong>{{ $stats['premium_tenants'] }}</strong>
                        <span>Premium colleges already using the system</span>
                    </article>
                </div>
            </div>

            <div class="marketing-hero-visual">
                <div class="logo-showcase-frame marketing-showcase-frame">
                    <img src="{{ $systemLogo }}" alt="BukSU Logo" class="logo-showcase-image">
                </div>
                <div class="mini-panel">
                    <strong>What the system includes</strong>
                    <ul class="soft-list" style="margin-top:12px;">
                        <li>Partner company management</li>
                        <li>Student applications and requirements</li>
                        <li>Progress reports and OJT hour tracking</li>
                        <li>Supervisor evaluation workflows</li>
                    </ul>
                </div>
            </div>
        </section>

        @if (session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="error-panel">
                <strong>Application not submitted.</strong>
                <ul style="margin:8px 0 0;padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="pricing-grid">
            @foreach ($plans as $plan)
                <article class="section-card plan-showcase-card">
                    <div class="action-row">
                        <h2>{{ $plan['label'] }}</h2>
                        <span class="pill">PHP {{ number_format($plan['amount'] / 100, 2) }}</span>
                    </div>
                    <p class="section-hint">{{ $plan['summary'] }}</p>
                    <ul class="soft-list" style="margin-top:16px;">
                        @foreach ($plan['features'] as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    </ul>
                </article>
            @endforeach
        </section>

        <article class="section-card">
            <div class="action-row">
                <h2>Why Colleges Use It</h2>
                <span class="pill">Benefits</span>
            </div>

            <div class="benefit-grid">
                @foreach ($benefits as $benefit)
                    <article class="mini-panel">
                        <p>{{ $benefit }}</p>
                    </article>
                @endforeach
            </div>
        </article>

        <article class="section-card" id="apply">
            <div class="action-row">
                <h2>Apply for a Plan</h2>
                <span class="pill">Stripe Test Payment</span>
            </div>
            <p class="section-hint">
                Fill up the college application below. After Stripe test payment, the BukSU admin reviews your request and creates
                the tenant database, subdomain/domain record, and initial coordinator account.
            </p>

            <form method="POST" action="{{ $applyAction }}" class="form-grid" style="margin-top:16px;">
                @csrf
                <label class="field-span-2">
                    College Name
                    <input type="text" name="college_name" value="{{ old('college_name') }}" placeholder="College of Nursing" required>
                </label>
                <label>
                    Contact Person
                    <input type="text" name="contact_name" value="{{ old('contact_name') }}" placeholder="Dean or Coordinator" required>
                </label>
                <label>
                    Contact Email
                    <input type="email" name="contact_email" value="{{ old('contact_email') }}" placeholder="dean@college.edu" required>
                </label>
                <label>
                    Contact Phone
                    <input type="text" name="contact_phone" value="{{ old('contact_phone') }}" placeholder="09xxxxxxxxx">
                </label>
                <label>
                    Internship Coordinator Email
                    <input type="email" name="admin_email" value="{{ old('admin_email') }}" placeholder="coordinator@college.edu" required>
                </label>
                <label>
                    Selected Plan
                    <select name="selected_plan" class="select-input" required>
                        @foreach ($plans as $planKey => $plan)
                            <option value="{{ $planKey }}" @selected(old('selected_plan', 'premium') === $planKey)>
                                {{ $plan['label'] }} - PHP {{ number_format($plan['amount'] / 100, 2) }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Preferred Subdomain
                    <input type="text" name="preferred_subdomain" value="{{ old('preferred_subdomain') }}" placeholder="nursing">
                    <small class="field-hint">Optional. Stored in the central database and reviewed on approval.</small>
                </label>
                <label class="field-span-2">
                    Preferred Domain
                    <input type="text" name="preferred_domain" value="{{ old('preferred_domain') }}" placeholder="Optional custom domain">
                    <small class="field-hint">Optional. Local development can still run through `php artisan serve` on localhost.</small>
                </label>
                <label class="field-span-2">
                    Notes
                    <textarea name="notes" rows="4" class="textarea-input" placeholder="Tell BukSU admin about your rollout timeline, special requirements, or preferred go-live date.">{{ old('notes') }}</textarea>
                </label>
                <div class="field-span-2">
                    <button type="submit">Continue to Stripe Test Payment</button>
                </div>
            </form>
        </article>
    </section>
@endsection
