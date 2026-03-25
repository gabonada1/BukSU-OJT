@php
    $layoutMode = 'dashboard';
    $currentSection = request()->query('section', 'overview');
    $reviewId = (int) request()->query('review');
    $editId = (int) request()->query('edit');
    $dashboardBaseUrl = route('central.dashboard');
    $sections = [
        'overview' => 'Overview',
        'applications' => 'Applications',
        'directory' => 'College Directory',
    ];

    if (! array_key_exists($currentSection, $sections)) {
        $currentSection = 'overview';
    }

    $reviewingApplication = $applications->firstWhere('id', $reviewId);
    $editingTenant = $tenants->firstWhere('id', $editId);
    $creatingTenant = $currentSection === 'directory' && request()->boolean('create');
    $pendingApplications = $applications->whereIn('status', ['submitted', 'pending_approval']);
    $recentApplications = $applications->take(5);
    $premiumCount = $tenants->where('plan', 'premium')->count();
@endphp

@extends('layouts.central')

@section('content')
    <section class="page-head">
        <div>
            <h1>University Admin Dashboard</h1>
            <p>BukSU central application for plan approvals, tenant provisioning, and college portal oversight.</p>
        </div>

        <div class="page-mini-stats">
            <div class="page-mini-card">
                <strong>Active Colleges</strong>
                <span>{{ $stats['active_tenants'] }}</span>
            </div>
            <div class="page-mini-card">
                <strong>Pending Applications</strong>
                <span>{{ $stats['pending_applications'] }}</span>
            </div>
            <div class="page-mini-card">
                <strong>Paid Applications</strong>
                <span>{{ $stats['paid_applications'] }}</span>
            </div>
        </div>
    </section>

    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="error-panel">
            <strong>Action not completed.</strong>
            <ul style="margin:8px 0 0;padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($currentSection === 'overview')
        <section class="content-stack">
            <section class="chart-grid">
                <article class="section-card">
                    <div class="action-row">
                        <h2>Superadmin RBAC</h2>
                        <span class="pill">Central Scope</span>
                    </div>

                    <ul class="soft-list" style="margin-top:16px;">
                        <li>Superadmin provisions tenants, activates or suspends portal access, and manages subscriptions.</li>
                        <li>Superadmin controls approved tenant hosts, coordinator contact email, and tenant notification delivery.</li>
                        <li>Tenant admins do not manage other tenants. Their authority stays inside one college portal.</li>
                    </ul>

                    <div class="action-row-actions" style="margin-top:18px;">
                        <a class="button secondary" href="{{ $dashboardBaseUrl.'?section=directory&create=1' }}">Register Tenant</a>
                        <a class="button secondary" href="{{ $dashboardBaseUrl.'?section=directory' }}">Open Tenant Directory</a>
                    </div>
                </article>

                <article class="section-card">
                    <div class="action-row">
                        <h2>Central Application</h2>
                        <span class="pill">University Layer</span>
                    </div>

                    <ul class="soft-list" style="margin-top:16px;">
                        @foreach ($centralResponsibilities as $responsibility)
                            <li>{{ $responsibility }}</li>
                        @endforeach
                    </ul>
                </article>

                <article class="section-card">
                    <div class="action-row">
                        <h2>Tenant Application</h2>
                        <span class="pill">College Layer</span>
                    </div>

                    <ul class="soft-list" style="margin-top:16px;">
                        @foreach ($tenantResponsibilities as $responsibility)
                            <li>{{ $responsibility }}</li>
                        @endforeach
                    </ul>
                </article>
            </section>

            <section class="chart-grid">
                <article class="metric-card">
                    <strong>{{ $pendingApplications->count() }}</strong>
                    <span>Applications waiting for BukSU approval</span>
                </article>
                <article class="metric-card">
                    <strong>{{ $tenants->count() }}</strong>
                    <span>College tenants recorded in the central database</span>
                </article>
                <article class="metric-card">
                    <strong>{{ $premiumCount }}</strong>
                    <span>Premium college plans currently provisioned</span>
                </article>
            </section>

            <article class="section-card">
                <div class="action-row">
                    <h2>Recent Plan Applications</h2>
                    <div class="action-row-actions">
                        <a class="panel-link" href="{{ $dashboardBaseUrl.'?section=applications' }}">Review Applications</a>
                    </div>
                </div>

                @if ($recentApplications->isEmpty())
                    <p style="margin-top:16px;">No plan applications yet.</p>
                @else
                    <table style="margin-top:16px;">
                        <thead>
                            <tr>
                                <th>College</th>
                                <th>Plan</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Submitted</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentApplications as $application)
                                <tr>
                                    <td>
                                        <strong style="display:block;">{{ $application->college_name }}</strong>
                                        <small>{{ $application->contact_name }} - {{ $application->contact_email }}</small>
                                    </td>
                                    <td><span class="badge">{{ strtoupper($application->selected_plan) }}</span></td>
                                    <td><span class="status-pill {{ $application->isPaid() ? 'active' : 'scheduled' }}">{{ strtoupper($application->payment_status) }}</span></td>
                                    <td><span class="status-pill {{ $application->status }}">{{ str_replace('_', ' ', strtoupper($application->status)) }}</span></td>
                                    <td>{{ $application->created_at?->format('M d, Y h:i A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </article>
        </section>
    @endif

    @if ($currentSection === 'applications')
        <section class="content-stack">
            @if ($reviewingApplication)
                @php
                    $suggestedSubdomain = \App\Http\Controllers\Central\PlanApplicationController::suggestedSubdomain($reviewingApplication->college_name);
                    $suggestedDatabase = \App\Http\Controllers\Central\PlanApplicationController::suggestedDatabaseName($reviewingApplication->college_name);
                @endphp
                <article class="section-card">
                    <div class="form-panel-header">
                        <h3>Review Application - {{ $reviewingApplication->college_name }}</h3>
                        <a class="panel-close" href="{{ $dashboardBaseUrl.'?section=applications' }}">&times;</a>
                    </div>

                    <div class="chart-grid" style="margin-top:16px;">
                        <div class="helper-note">
                            Contact: <strong>{{ $reviewingApplication->contact_name }}</strong><br>
                            Contact Email: <strong>{{ $reviewingApplication->contact_email }}</strong><br>
                            Coordinator Email: <strong>{{ $reviewingApplication->admin_email }}</strong>
                        </div>
                        <div class="helper-note">
                            Plan: <strong>{{ strtoupper($reviewingApplication->selected_plan) }}</strong><br>
                            Payment: <strong>{{ strtoupper($reviewingApplication->payment_status) }}</strong><br>
                            Preferred Access: <strong>{{ $reviewingApplication->preferred_subdomain ?: 'No subdomain requested' }}</strong>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('central.plan-applications.approve', $reviewingApplication) }}" class="form-grid" style="margin-top:16px;">
                        @csrf
                        <label class="field-span-2">
                            College Database
                            <input type="text" name="database" value="{{ old('database', $suggestedDatabase) }}" required>
                        </label>
                        <label>
                            Subdomain
                            <input type="text" name="subdomain" value="{{ old('subdomain', $reviewingApplication->preferred_subdomain ?: $suggestedSubdomain) }}">
                            <small class="field-hint">Optional. If provided, the approved host is stored in the central domain registry.</small>
                        </label>
                        <label>
                            Domain
                            <input type="text" name="domain" value="{{ old('domain', $reviewingApplication->preferred_domain) }}" placeholder="Optional custom domain">
                            <small class="field-hint">Optional custom host. Domains live in the central domain table, not on the tenant record.</small>
                        </label>
                        <label>
                            Subscription Starts
                            <input type="date" name="subscription_starts_at" value="{{ old('subscription_starts_at', now()->toDateString()) }}" required>
                        </label>
                        <label>
                            Subscription Expires
                            <input type="date" name="subscription_expires_at" value="{{ old('subscription_expires_at', now()->addYear()->toDateString()) }}">
                        </label>
                        <label class="field-span-2">
                            Coordinator Password
                            <input type="password" name="admin_password" placeholder="Leave blank to auto-generate">
                            <small class="field-hint">If left blank, the system creates a secure password and emails it to the coordinator.</small>
                        </label>
                        <label class="field-span-2">
                            Approval Notes
                            <textarea name="approval_notes" rows="3" class="textarea-input" placeholder="Optional notes for this approval">{{ old('approval_notes') }}</textarea>
                        </label>
                        <div class="field-span-2 action-row-actions">
                            <button type="submit">Approve and Provision Tenant</button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('central.plan-applications.reject', $reviewingApplication) }}" class="form-grid" style="margin-top:18px;">
                        @csrf
                        <label class="field-span-2">
                            Rejection Reason
                            <textarea name="rejection_reason" rows="3" class="textarea-input" placeholder="Explain why this application cannot be approved yet." required>{{ old('rejection_reason') }}</textarea>
                        </label>
                        <div class="field-span-2 action-row-actions">
                            <button type="submit" class="button danger">Reject Application</button>
                        </div>
                    </form>
                </article>
            @endif

            <article class="section-card">
                <div class="action-row">
                    <h2>Plan Applications</h2>
                    <span class="pill">{{ $applications->count() }} Total</span>
                </div>

                @if ($applications->isEmpty())
                    <p style="margin-top:16px;">No plan applications submitted yet.</p>
                @else
                    <table style="margin-top:16px;">
                        <thead>
                            <tr>
                                <th>College</th>
                                <th>Plan</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Requested Access</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($applications as $application)
                                <tr>
                                    <td>
                                        <strong style="display:block;">{{ $application->college_name }}</strong>
                                        <small>{{ $application->contact_name }} - {{ $application->admin_email }}</small>
                                    </td>
                                    <td><span class="badge">{{ strtoupper($application->selected_plan) }}</span></td>
                                    <td><span class="status-pill {{ $application->isPaid() ? 'active' : 'scheduled' }}">{{ strtoupper($application->payment_status) }}</span></td>
                                    <td><span class="status-pill {{ $application->status }}">{{ str_replace('_', ' ', strtoupper($application->status)) }}</span></td>
                                    <td>
                                        <strong style="display:block;">{{ $application->preferred_subdomain ?: 'No subdomain requested' }}</strong>
                                        <small>{{ $application->preferred_domain ?: 'No custom domain requested' }}</small>
                                    </td>
                                    <td>{{ $application->created_at?->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <div class="actions">
                                            <a class="button secondary" href="{{ $dashboardBaseUrl.'?section=applications&review='.$application->id }}">Review</a>
                                            @if ($application->tenant)
                                                <a class="button" href="{{ app(\App\Support\Tenancy\TenantUrlGenerator::class)->loginUrl($application->tenant) }}">Open Portal</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </article>
        </section>
    @endif

    @if ($currentSection === 'directory')
        <section class="content-stack">
            <article class="section-card">
                <div class="action-row">
                    <h2>Tenant Directory Console</h2>
                    <span class="pill">Superadmin UI</span>
                </div>
                <div class="chart-grid" style="margin-top:16px;">
                    <div class="helper-note">
                        Superadmin actions:<br>
                        <strong>Register tenants, edit subscriptions, activate or suspend access, manage approved domains, send tenant emails.</strong>
                    </div>
                    <div class="helper-note">
                        Tenant admin actions:<br>
                        <strong>Create and manage students, supervisors, and coordinators only inside the tenant portal.</strong>
                    </div>
                </div>
            </article>

            @if ($creatingTenant)
                <article class="section-card">
                    <div class="form-panel-header">
                        <h3>Register College Tenant</h3>
                        <a class="panel-close" href="{{ $dashboardBaseUrl.'?section=directory' }}">&times;</a>
                    </div>

                    <form method="POST" action="{{ $tenantCreateAction }}" class="form-grid" style="margin-top:16px;">
                        @csrf
                        <label class="field-span-2">College Name <input type="text" name="name" value="{{ old('name') }}" required></label>
                        <label>
                            License Tier
                            <select name="plan" required>
                                @foreach ($plans as $planKey => $plan)
                                    <option value="{{ $planKey }}" @selected(old('plan', 'basic') === $planKey)>{{ $plan['label'] }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>Tenant Database <input type="text" name="database" value="{{ old('database') }}" placeholder="buksu_college_name" required></label>
                        <label>Subdomain <input type="text" name="subdomain" value="{{ old('subdomain') }}" placeholder="college-code" required></label>
                        <label>Custom Domain <input type="text" name="domain" value="{{ old('domain') }}" placeholder="Optional custom domain"></label>
                        <label>Subscription Starts <input type="date" name="subscription_starts_at" value="{{ old('subscription_starts_at', now()->toDateString()) }}" required></label>
                        <label>Subscription Expires <input type="date" name="subscription_expires_at" value="{{ old('subscription_expires_at', now()->addYear()->toDateString()) }}"></label>
                        <label>Coordinator Name <input type="text" name="admin_name" value="{{ old('admin_name') }}" placeholder="Internship Coordinator"></label>
                        <label>Coordinator Email <input type="email" name="admin_email" value="{{ old('admin_email') }}" required></label>
                        <label class="field-span-2">
                            Coordinator Password
                            <input type="password" name="admin_password" placeholder="Leave blank to auto-generate">
                            <small class="field-hint">Manual tenant signup provisions the database, creates the first coordinator account, and emails the credentials automatically.</small>
                        </label>
                        <div class="field-span-2">
                            <button type="submit">Register Tenant</button>
                        </div>
                    </form>
                </article>
            @endif

            @if ($editingTenant)
                @php
                    $editingContact = $tenantContacts[$editingTenant->id] ?? ['name' => null, 'email' => null];
                    $editingDomainHosts = $editingTenant->domains
                        ->where('is_active', true)
                        ->pluck('host')
                        ->implode(PHP_EOL);
                @endphp
                <article class="section-card">
                    <div class="form-panel-header">
                        <h3>Edit College Tenant</h3>
                        <a class="panel-close" href="{{ $dashboardBaseUrl.'?section=directory' }}">&times;</a>
                    </div>

                    <form method="POST" action="{{ route('central.tenants.update', $editingTenant) }}" class="form-grid" style="margin-top:16px;">
                        @csrf
                        @method('PATCH')
                        <label class="field-span-2">College Name <input type="text" name="name" value="{{ old('name', $editingTenant->name) }}" required></label>
                        <label>
                            License Tier
                            <select name="plan" required>
                                @foreach ($plans as $planKey => $plan)
                                    <option value="{{ $planKey }}" @selected(old('plan', $editingTenant->plan) === $planKey)>{{ $plan['label'] }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            Portal Access
                            <select name="is_active" required>
                                <option value="1" @selected((string) old('is_active', (int) $editingTenant->is_active) === '1')>Active</option>
                                <option value="0" @selected((string) old('is_active', (int) $editingTenant->is_active) === '0')>Suspended</option>
                            </select>
                        </label>
                        <label>Subscription Starts <input type="date" name="subscription_starts_at" value="{{ old('subscription_starts_at', $editingTenant->subscription_starts_at?->toDateString()) }}" required></label>
                        <label>Subscription Expires <input type="date" name="subscription_expires_at" value="{{ old('subscription_expires_at', $editingTenant->subscription_expires_at?->toDateString()) }}"></label>
                        <label>Coordinator Name <input type="text" name="admin_name" value="{{ old('admin_name', $editingContact['name']) }}" placeholder="Internship Coordinator"></label>
                        <label>Coordinator Email <input type="email" name="admin_email" value="{{ old('admin_email', $editingContact['email']) }}" required></label>
                        <label>Tenant Code <input type="text" value="{{ $editingTenant->code }}" readonly></label>
                        <label>Tenant Database <input type="text" value="{{ $editingTenant->database }}" readonly></label>
                        <label class="field-span-2">
                            Approved Domain Hosts
                            <textarea name="domain_hosts" rows="4" class="textarea-input" placeholder="one-host-per-line.example.edu">{{ old('domain_hosts', $editingDomainHosts) }}</textarea>
                            <small class="field-hint">One host per line or comma-separated. The first host becomes the primary tenant domain.</small>
                        </label>
                        <div class="field-span-2">
                            <button type="submit">Save Changes</button>
                        </div>
                    </form>

                    <div class="action-row-actions" style="margin-top:18px;">
                        <form method="POST" action="{{ route('central.tenants.status', $editingTenant) }}" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="is_active" value="{{ $editingTenant->is_active ? '0' : '1' }}">
                            <button type="submit" class="button {{ $editingTenant->is_active ? 'danger' : '' }}">{{ $editingTenant->is_active ? 'Deactivate Tenant' : 'Activate Tenant' }}</button>
                        </form>
                        <form method="POST" action="{{ route('central.tenants.notify', $editingTenant) }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="notification" value="subscription">
                            <button type="submit" class="button secondary">Send Subscription Email</button>
                        </form>
                        <form method="POST" action="{{ route('central.tenants.notify', $editingTenant) }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="notification" value="{{ $editingTenant->is_active ? 'activation' : 'suspension' }}">
                            <button type="submit" class="button secondary">Send Status Email</button>
                        </form>
                    </div>
                </article>
            @endif

            <article class="section-card">
                <div class="action-row">
                    <h2>College Directory</h2>
                    <div class="action-row-actions">
                        <span class="pill">{{ $tenants->count() }} Tenants</span>
                        <a class="button secondary" href="{{ $dashboardBaseUrl.'?section=directory&create=1' }}">Register College</a>
                    </div>
                </div>

                @if ($tenants->isEmpty())
                    <p style="margin-top:16px;">No college tenants provisioned yet.</p>
                @else
                    <table style="margin-top:16px;">
                        <thead>
                            <tr>
                                <th>College</th>
                                <th>Code</th>
                                <th>Plan</th>
                                <th>Status</th>
                                <th>Database</th>
                                <th>Access</th>
                                <th>Portal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tenants as $tenant)
                                @php
                                    $tenantAccessPath = preg_replace('#^https?://#', '', app(\App\Support\Tenancy\TenantUrlGenerator::class)->loginUrl($tenant));
                                @endphp
                                <tr>
                                    <td><strong>{{ $tenant->name }}</strong></td>
                                    <td><code>{{ $tenant->code }}</code></td>
                                    <td><span class="badge">{{ strtoupper($tenant->plan) }}</span></td>
                                    <td><span class="status-pill {{ $tenant->subscriptionStatus() }}">{{ ucfirst($tenant->subscriptionStatus()) }}</span></td>
                                    <td><code>{{ $tenant->database }}</code></td>
                                    <td>
                                        <strong style="display:block;">{{ $tenantAccessPath }}</strong>
                                        <small>{{ $tenant->domains->pluck('host')->join(', ') ?: 'No approved domain records' }}</small>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a class="button" href="{{ app(\App\Support\Tenancy\TenantUrlGenerator::class)->loginUrl($tenant) }}">Open Portal</a>
                                            <a class="button secondary" href="{{ $dashboardBaseUrl.'?section=directory&edit='.$tenant->id }}">Edit</a>
                                            <form method="POST" action="{{ route('central.tenants.status', $tenant) }}" style="display:inline;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="is_active" value="{{ $tenant->is_active ? '0' : '1' }}">
                                                <button type="submit" class="button {{ $tenant->is_active ? 'danger' : 'secondary' }}">{{ $tenant->is_active ? 'Deactivate' : 'Activate' }}</button>
                                            </form>
                                            <form method="POST" action="{{ route('central.tenants.destroy', $tenant) }}" onsubmit="return confirm('Delete {{ $tenant->name }} and permanently drop database {{ $tenant->database }}? This cannot be undone.');" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="button danger">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </article>
        </section>
    @endif
@endsection
