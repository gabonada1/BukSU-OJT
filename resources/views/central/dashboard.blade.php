@php
    $layoutMode = 'dashboard';
    $currentSection = request()->query('section', 'overview');
    $reviewId = (int) request()->query('review');
    $editId = (int) request()->query('edit');
    $dashboardBaseUrl = route('central.dashboard');
    $sections = ['overview' => 'Overview', 'applications' => 'Applications', 'directory' => 'College Directory'];

    if (! array_key_exists($currentSection, $sections)) {
        $currentSection = 'overview';
    }

    $reviewingApplication = $applications->firstWhere('id', $reviewId);
    $editingTenant = $tenants->firstWhere('id', $editId);
    $pendingApplications = $applications->whereIn('status', ['submitted', 'pending_approval']);
    $recentApplications = $applications->take(5);
    $recentTenants = $tenants->take(5);
    $premiumCount = $tenants->where('plan', 'premium')->count();
    $topBandwidthTenants = $tenantBandwidthProfiles->sortByDesc('utilization_pct')->take(5);
    $directoryTenants = $tenants->sortBy('name')->values();
    $bandwidthGraphProfiles = $tenantBandwidthProfiles->sortByDesc('used_gb')->take(6)->values();
    $bandwidthGraphMax = max(1, (float) $bandwidthGraphProfiles->max('limit_gb'));
    $recentActivity = collect()
        ->merge($tenants->take(4)->map(fn ($tenant) => [
            'title' => 'Created tenant: '.$tenant->name,
            'meta' => 'System Administrator · '.optional($tenant->created_at)->format('m/d/Y'),
        ]))
        ->merge($applications->take(4)->map(fn ($application) => [
            'title' => ucfirst(str_replace('_', ' ', $application->status)).' application: '.$application->college_name,
            'meta' => 'Central Admin · '.optional($application->created_at)->format('m/d/Y'),
        ]))
        ->take(5);
@endphp

@extends('layouts.central')

@section('content')
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

    <section class="admin-toolbar">
        <div class="admin-tabset">
            @foreach ($sections as $key => $label)
                <a class="admin-tab {{ $currentSection === $key ? 'active' : '' }}" href="{{ $dashboardBaseUrl.'?section='.$key }}">{{ $label }}</a>
            @endforeach
        </div>
        <div class="admin-toolbar-note">
            <span>Premium Tenants</span>
            <strong>{{ $premiumCount }}</strong>
        </div>
    </section>

    @if ($currentSection === 'overview')
        <section class="lovable-page-header">
            <div>
                <h1>Superadmin Dashboard</h1>
                <p>Overview of the entire university management system</p>
            </div>
        </section>

        <section class="lovable-metric-grid">
            <article class="lovable-metric-card">
                <div>
                    <span>Total Tenants</span>
                    <strong>{{ $tenants->count() }}</strong>
                    <small>{{ $premiumCount }} premium plans provisioned</small>
                </div>
                <div class="lovable-metric-icon">T</div>
            </article>
            <article class="lovable-metric-card">
                <div>
                    <span>Active Tenants</span>
                    <strong>{{ $stats['active_tenants'] }}</strong>
                    <small>Currently accessible college portals</small>
                </div>
                <div class="lovable-metric-icon">A</div>
            </article>
            <article class="lovable-metric-card">
                <div>
                    <span>Suspended</span>
                    <strong>{{ $stats['suspended_tenants'] }}</strong>
                    <small>Colleges blocked from access</small>
                </div>
                <div class="lovable-metric-icon">S</div>
            </article>
            <article class="lovable-metric-card">
                <div>
                    <span>Expiring Soon</span>
                    <strong>{{ $stats['expiring_tenants'] }}</strong>
                    <small>Within 30 days</small>
                </div>
                <div class="lovable-metric-icon">E</div>
            </article>
        </section>

        <section class="lovable-grid-2">
            <article class="admin-glass-card">
                <div class="admin-section-head">
                    <div>
                        <h2>Recent Tenants</h2>
                    </div>
                </div>

                @if ($recentTenants->isEmpty())
                    <p class="section-hint">No tenant records yet.</p>
                @else
                    <div class="table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Status</th>
                                    <th>Bandwidth</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentTenants as $tenant)
                                    @php $tenantProfile = $tenantBandwidthProfiles->get($tenant->id); @endphp
                                    <tr>
                                        <td><strong style="display:block;">{{ $tenant->name }}</strong></td>
                                        <td>{{ $tenant->code ?: 'N/A' }}</td>
                                        <td><span class="status-pill {{ $tenant->subscriptionStatus() }}">{{ ucfirst($tenant->subscriptionStatus()) }}</span></td>
                                        <td>{{ number_format((float) ($tenantProfile['used_gb'] ?? 0), 0) }} GB</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </article>

            <article class="admin-glass-card">
                <div class="admin-section-head">
                    <div>
                        <h2>Recent Activity</h2>
                    </div>
                </div>

                <div class="activity-feed">
                    @foreach ($recentActivity as $item)
                        <article class="activity-item">
                            <span class="activity-dot"></span>
                            <div>
                                <strong>{{ $item['title'] }}</strong>
                                <small>{{ $item['meta'] }}</small>
                            </div>
                        </article>
                    @endforeach
                </div>
            </article>
        </section>

        <section class="admin-glass-card">
            <div class="admin-section-head">
                <div>
                    <span class="admin-eyebrow">Bandwidth</span>
                    <h2>Tenant Bandwidth Usage</h2>
                </div>
                <span class="pill">{{ number_format($bandwidthTotals['utilization_pct'], 1) }}% used</span>
            </div>

            @if ($bandwidthGraphProfiles->isEmpty())
                <p class="section-hint">No tenants available yet.</p>
            @else
                <div class="bandwidth-chart-card">
                    <div class="bandwidth-chart-head">
                        <div>
                            <strong>{{ number_format($bandwidthTotals['used_gb'], 0) }} GB</strong>
                            <small>{{ number_format($bandwidthTotals['allocated_gb'], 0) }} GB total capacity</small>
                        </div>
                        <div class="bandwidth-chart-legend">
                            <span><i class="bandwidth-legend-dot used"></i>Used</span>
                            <span><i class="bandwidth-legend-dot limit"></i>Capacity</span>
                        </div>
                    </div>

                    <div class="bandwidth-bars">
                        @foreach ($bandwidthGraphProfiles as $profile)
                            <div class="bandwidth-bar-group">
                                <div class="bandwidth-bar-track">
                                    <span class="bandwidth-bar-limit" style="height: {{ max(16, ($profile['limit_gb'] / $bandwidthGraphMax) * 180) }}px;"></span>
                                    <span class="bandwidth-bar-used" style="height: {{ max(10, ($profile['used_gb'] / $bandwidthGraphMax) * 180) }}px;"></span>
                                </div>
                                <strong>{{ \Illuminate\Support\Str::limit($profile['tenant']->code ?: $profile['tenant']->name, 8, '') }}</strong>
                                <small>{{ number_format($profile['used_gb'], 0) }} / {{ number_format($profile['limit_gb'], 0) }} GB</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>
    @endif

    @if ($currentSection === 'applications')
        <section class="content-stack">
            @if ($reviewingApplication)
                @php
                    $suggestedSubdomain = \App\Http\Controllers\Central\PlanApplicationController::suggestedSubdomain($reviewingApplication->college_name);
                    $suggestedDatabase = \App\Http\Controllers\Central\PlanApplicationController::suggestedDatabaseName($reviewingApplication->college_name);
                    $suggestedBandwidth = $planBandwidthDefaults[$reviewingApplication->selected_plan] ?? 150;
                @endphp
                <article class="admin-glass-card">
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
                        <label class="field-span-2">College Database<input type="text" name="database" value="{{ old('database', $suggestedDatabase) }}" required></label>
                        <label>Subdomain<input type="text" name="subdomain" value="{{ old('subdomain', $reviewingApplication->preferred_subdomain ?: $suggestedSubdomain) }}"></label>
                        <label>Domain<input type="text" name="domain" value="{{ old('domain', $reviewingApplication->preferred_domain) }}" placeholder="Optional custom domain"></label>
                        <label>Subscription Starts<input type="date" name="subscription_starts_at" value="{{ old('subscription_starts_at', now()->toDateString()) }}" required></label>
                        <label>Subscription Expires<input type="date" name="subscription_expires_at" value="{{ old('subscription_expires_at', now()->addYear()->toDateString()) }}"></label>
                        <label>Bandwidth Allocation (GB)<input type="number" min="1" step="1" name="bandwidth_limit_gb" value="{{ old('bandwidth_limit_gb', $suggestedBandwidth) }}" required></label>
                        <label>Current Usage (GB)<input type="number" min="0" step="0.1" name="bandwidth_used_gb" value="{{ old('bandwidth_used_gb', 0) }}"></label>
                        <label class="field-span-2">
                            Coordinator Password
                            <input type="password" name="admin_password" placeholder="Leave blank to auto-generate">
                            <small class="field-hint">If left blank, the system creates a secure password and emails it to the coordinator.</small>
                        </label>
                        <label class="field-span-2">Approval Notes<textarea name="approval_notes" rows="3" class="textarea-input" placeholder="Optional notes for this approval">{{ old('approval_notes') }}</textarea></label>
                        <div class="field-span-2 action-row-actions">
                            <button type="submit">Approve and Provision Tenant</button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('central.plan-applications.reject', $reviewingApplication) }}" class="form-grid" style="margin-top:18px;">
                        @csrf
                        <label class="field-span-2">Rejection Reason<textarea name="rejection_reason" rows="3" class="textarea-input" placeholder="Explain why this application cannot be approved yet." required>{{ old('rejection_reason') }}</textarea></label>
                        <div class="field-span-2 action-row-actions">
                            <button type="submit" class="button danger">Reject Application</button>
                        </div>
                    </form>
                </article>
            @endif

            <article class="admin-glass-card">
                <div class="action-row">
                    <h2>Plan Applications</h2>
                    <span class="pill">{{ $applications->count() }} Total</span>
                </div>

                @if ($applications->isEmpty())
                    <p style="margin-top:16px;">No plan applications submitted yet.</p>
                @else
                    <div class="table-wrap">
                        <table class="admin-table" style="margin-top:16px;">
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
                                            <small>{{ $application->contact_name }} • {{ $application->admin_email }}</small>
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
                    </div>
                @endif
            </article>
        </section>
    @endif

    @if ($currentSection === 'directory')
        <section class="content-stack">
            <article class="admin-glass-card">
                <div class="action-row">
                    <h2>Tenant Directory Console</h2>
                    <span class="pill">Superadmin UI</span>
                </div>
                <div class="chart-grid" style="margin-top:16px;">
                    <div class="helper-note">
                        Superadmin actions:<br>
                        <strong>Approve paid applications, edit subscriptions, update bandwidth, activate or suspend access, manage approved domains, and send tenant emails.</strong>
                    </div>
                    <div class="helper-note">
                        Tenant admin actions:<br>
                        <strong>Create and manage students, supervisors, and coordinators only inside the tenant portal.</strong>
                    </div>
                </div>
            </article>

            @if ($editingTenant)
                @php
                    $editingContact = $tenantContacts[$editingTenant->id] ?? ['name' => null, 'email' => null];
                    $editingDomainHosts = $editingTenant->domains->where('is_active', true)->pluck('host')->implode(PHP_EOL);
                    $editingBandwidth = $tenantBandwidthProfiles->get($editingTenant->id);
                @endphp
                <article class="admin-glass-card">
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
                        <label>Bandwidth Allocation (GB) <input type="number" min="1" step="1" name="bandwidth_limit_gb" value="{{ old('bandwidth_limit_gb', $editingBandwidth['limit_gb'] ?? $planBandwidthDefaults[$editingTenant->plan] ?? 150) }}" required></label>
                        <label>Current Usage (GB) <input type="number" min="0" step="0.1" name="bandwidth_used_gb" value="{{ old('bandwidth_used_gb', $editingBandwidth['used_gb'] ?? 0) }}"></label>
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

            <article class="admin-glass-card">
                <div class="action-row">
                    <h2>College Directory</h2>
                    <div class="action-row-actions">
                        <span class="pill">{{ $tenants->count() }} Tenants</span>
                    </div>
                </div>

                @if ($tenants->isEmpty())
                    <p style="margin-top:16px;">No college tenants provisioned yet.</p>
                @else
                    <div class="table-wrap">
                        <table class="admin-table" style="margin-top:16px;">
                            <thead>
                                <tr>
                                    <th>College</th>
                                    <th>Code</th>
                                    <th>Plan</th>
                                    <th>Bandwidth</th>
                                    <th>Status</th>
                                    <th>Access</th>
                                    <th>Portal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tenants as $tenant)
                                    @php
                                        $tenantAccessPath = preg_replace('#^https?://#', '', app(\App\Support\Tenancy\TenantUrlGenerator::class)->loginUrl($tenant));
                                        $profile = $tenantBandwidthProfiles->get($tenant->id);
                                    @endphp
                                    <tr>
                                        <td><strong style="display:block;">{{ $tenant->name }}</strong><small>{{ $tenant->database }}</small></td>
                                        <td><code>{{ $tenant->code }}</code></td>
                                        <td><span class="badge">{{ strtoupper($tenant->plan) }}</span></td>
                                        <td><strong style="display:block;">{{ number_format($profile['used_gb'], 0) }} / {{ number_format($profile['limit_gb'], 0) }} GB</strong><small>{{ number_format($profile['available_gb'], 0) }} GB free</small></td>
                                        <td><span class="status-pill {{ $tenant->subscriptionStatus() }}">{{ ucfirst($tenant->subscriptionStatus()) }}</span></td>
                                        <td><strong style="display:block;">{{ $tenantAccessPath }}</strong><small>{{ $tenant->domains->pluck('host')->join(', ') ?: 'No approved domain records' }}</small></td>
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
                    </div>
                @endif
            </article>
        </section>
    @endif
@endsection
