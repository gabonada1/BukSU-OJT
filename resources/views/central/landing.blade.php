@php
    $layoutMode = 'dashboard';
    $hideCentralHeader = true;
    $systemLogo = asset('images/logos/logo.jpg');
@endphp

@extends('layouts.central')

@section('content')
    <section class="lovable-landing">
        <section class="lovable-landing-hero">
            <div class="lovable-landing-copy">
                <span class="admin-eyebrow">BukSU Admin System</span>
                <h1>Multi-tenant practicum management for BukSU colleges.</h1>
                <p>Centralized provisioning, approvals, subscriptions, and tenant onboarding with a darker interface aligned to your Lovable design direction.</p>

                <div class="admin-action-row">
                    <a href="#apply" class="button">Apply for a Plan</a>
                    <a href="{{ $centralLoginUrl }}" class="button secondary">Central Admin Login</a>
                </div>
            </div>

            <div class="lovable-landing-stats">
                <article class="lovable-metric-card">
                    <div>
                        <span>Active Tenants</span>
                        <strong>{{ $stats['active_tenants'] }}</strong>
                        <small>Running university portals</small>
                    </div>
                    <div class="lovable-metric-icon">A</div>
                </article>
                <article class="lovable-metric-card">
                    <div>
                        <span>Pending Applications</span>
                        <strong>{{ $stats['submitted_applications'] }}</strong>
                        <small>Waiting for approval</small>
                    </div>
                    <div class="lovable-metric-icon">P</div>
                </article>
                <article class="lovable-metric-card">
                    <div>
                        <span>Premium Tenants</span>
                        <strong>{{ $stats['premium_tenants'] }}</strong>
                        <small>Advanced college workspaces</small>
                    </div>
                    <div class="lovable-metric-icon">M</div>
                </article>
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
                <article class="admin-glass-card plan-showcase-card">
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

        <article class="admin-glass-card">
            <div class="action-row">
                <h2>Why Universities Use It</h2>
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

        <article class="admin-glass-card" id="apply">
            <div class="action-row">
                <h2>Apply for a Plan</h2>
                <span class="pill">Stripe Test Payment</span>
            </div>
            <p class="section-hint">
                Fill up the university application below. After Stripe test payment, Bukidnon State University reviews your request and creates
                the tenant database, subdomain/domain record, and initial coordinator account.
            </p>

            <form method="POST" action="{{ $applyAction }}" class="form-grid" style="margin-top:16px;">
                @csrf
                <label class="field-span-2">
                    University Name
                    <input type="text" name="college_name" value="{{ old('college_name') }}" placeholder="Bukidnon State University - College of Technologies" required>
                </label>
                <label>
                    Contact Person
                    <input type="text" name="contact_name" value="{{ old('contact_name') }}" placeholder="Dean or Coordinator" required>
                </label>
                <label>
                    Contact Email
                    <input type="email" name="contact_email" value="{{ old('contact_email') }}" placeholder="dean@buksu.edu.ph" required>
                </label>
                <label>
                    Contact Phone
                    <input type="text" name="contact_phone" value="{{ old('contact_phone') }}" placeholder="09xxxxxxxxx">
                </label>
                <label>
                    Internship Coordinator Email
                    <input type="email" name="admin_email" value="{{ old('admin_email') }}" placeholder="coordinator@buksu.edu.ph" required>
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
                    <textarea name="notes" rows="4" class="textarea-input" placeholder="Tell Bukidnon State University about your rollout timeline, special requirements, or preferred go-live date.">{{ old('notes') }}</textarea>
                </label>
                <div class="field-span-2">
                    <button type="submit">Continue to Stripe Test Payment</button>
                </div>
            </form>
        </article>
    </section>
@endsection
