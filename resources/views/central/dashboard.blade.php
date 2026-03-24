@php
    $layoutMode = 'dashboard';
    $premiumCount = $tenants->where('plan', 'premium')->count();
    $proCount = $tenants->where('plan', 'pro')->count();
    $basicCount = $tenants->where('plan', 'basic')->count();
    $totalTenants = max($tenants->count(), 1);
    $domainCoverage = (int) round(($tenants->whereNotNull('domain')->count() / $totalTenants) * 100);
    $premiumRate = (int) round(($premiumCount / $totalTenants) * 100);
    $basicStop = round(($basicCount / $totalTenants) * 100, 2);
    $proStop = round((($basicCount + $proCount) / $totalTenants) * 100, 2);
    $donutStyle = "background:conic-gradient(#e8dbcb 0 {$basicStop}%, #d6b36a {$basicStop}% {$proStop}%, #c86b61 {$proStop}% 100%);";
    $currentSection = request()->query('section', 'overview');
    $createSection = request()->query('create');
    $editSlug = request()->query('edit');
    $dashboardBaseUrl = route('central.dashboard');

    $sections = [
        'overview' => 'Overview',
        'provision' => 'Provision',
        'directory' => 'Directory',
    ];

    if (! array_key_exists($currentSection, $sections)) {
        $currentSection = 'overview';
    }

    $editingTenant = $tenants->firstWhere('slug', $editSlug);
    $showCreatePanel = $currentSection === 'directory' && $createSection === 'directory';
    $showEditPanel = $currentSection === 'directory' && filled($editingTenant);
@endphp

@extends('layouts.central')

@section('content')
    <section class="page-head">
        <div>
            <h1>Superadmin Dashboard</h1>
            <p>{{ $sections[$currentSection] }}</p>
        </div>

        <div class="page-mini-stats">
            <div class="page-mini-card">
                <strong>Tenants</strong>
                <span>{{ $stats['active_tenants'] }}</span>
            </div>
            <div class="page-mini-card">
                <strong>Domains</strong>
                <span>{{ $stats['tenant_domains'] }}</span>
            </div>
            <div class="page-mini-card">
                <strong>Premium</strong>
                <span>{{ $stats['premium_plans'] }}</span>
            </div>
        </div>
    </section>

    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="error-panel">
            <strong>Tenant provisioning did not complete.</strong>
            <ul style="margin:8px 0 0;padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($currentSection === 'overview')
        <section class="content-stack">
            <article class="section-card">
                <div class="action-row">
                    <h2>Registry Snapshot</h2>
                    <div class="action-row-actions">
                        <a class="panel-link" href="{{ $dashboardBaseUrl.'?section=provision' }}">Create Tenant</a>
                    </div>
                </div>

                <ul class="soft-list" style="margin-top:16px;">
                    <li>Base domain: <strong>{{ config('tenancy.base_domain', 'buksu.test') }}</strong></li>
                    <li>Central hosts: <strong>{{ implode(', ', config('tenancy.central_domains', ['127.0.0.1', 'localhost'])) }}</strong></li>
                    <li>Tenant databases: <strong>{{ $tenants->count() }}</strong></li>
                    <li>Premium plans: <strong>{{ $premiumCount }}</strong></li>
                </ul>
            </article>

            <section class="chart-grid">
                <article class="section-card">
                    <div class="action-row" style="margin-bottom:16px;">
                        <h2>Coverage</h2>
                        <span class="pill">Live</span>
                    </div>

                    <div class="ring-grid">
                        <div class="ring" style="--progress: {{ max($domainCoverage, 2) }}; --ring-color: #c86b61;">
                            <div class="ring-content">
                                <div>
                                    <strong>{{ $domainCoverage }}%</strong>
                                    <span>Domain Ready</span>
                                </div>
                            </div>
                        </div>
                        <div class="ring" style="--progress: {{ max($premiumRate, 2) }}; --ring-color: #d6b36a;">
                            <div class="ring-content">
                                <div>
                                    <strong>{{ $premiumRate }}%</strong>
                                    <span>Premium</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <ul class="legend" style="margin-top:18px;">
                        <li>
                            <span class="legend-label"><span class="legend-swatch" style="background:#c86b61;"></span>Domains configured</span>
                            <strong>{{ $tenants->whereNotNull('domain')->count() }}</strong>
                        </li>
                        <li>
                            <span class="legend-label"><span class="legend-swatch" style="background:#d6b36a;"></span>Premium tenants</span>
                            <strong>{{ $premiumCount }}</strong>
                        </li>
                    </ul>
                </article>

                <article class="section-card">
                    <div class="action-row" style="margin-bottom:16px;">
                        <h2>Plan Mix</h2>
                        <span class="pill">Tenants</span>
                    </div>

                    <div style="display:grid;grid-template-columns:minmax(180px,240px) minmax(0,1fr);gap:24px;align-items:center;">
                        <div class="donut" style="{{ $donutStyle }}">
                            <div class="ring-content">
                                <div>
                                    <strong>{{ $tenants->count() }}</strong>
                                    <span>Tenants</span>
                                </div>
                            </div>
                        </div>

                        <ul class="legend">
                            <li>
                                <span class="legend-label"><span class="legend-swatch" style="background:#e8dbcb;"></span>Basic</span>
                                <strong>{{ $basicCount }}</strong>
                            </li>
                            <li>
                                <span class="legend-label"><span class="legend-swatch" style="background:#d6b36a;"></span>Pro</span>
                                <strong>{{ $proCount }}</strong>
                            </li>
                            <li>
                                <span class="legend-label"><span class="legend-swatch" style="background:#c86b61;"></span>Premium</span>
                                <strong>{{ $premiumCount }}</strong>
                            </li>
                        </ul>
                    </div>
                </article>
            </section>
        </section>
    @endif

    @if ($currentSection === 'provision')
        <section class="content-stack">
            <article class="section-card">
                <div class="action-row">
                    <h2>Provision Tenant</h2>
                </div>

                <form method="POST" action="{{ $tenantCreateAction }}" class="form-grid" style="margin-top:16px;">
                    @csrf
                    <label class="field-span-2">Tenant Name <input type="text" name="name" value="{{ old('name') }}" placeholder="College of Technology - BSIT" required></label>
                    <label class="field-span-2">Tenant Database <input type="text" name="database" value="{{ old('database') }}" placeholder="buksu_bsit" required></label>
                    <div class="helper-note field-span-2">
                        The subscription starts date is required. Expiry is optional and can be used to monitor renewals.
                    </div>
                    <label>
                        Plan
                        <select name="plan" required>
                            <option value="basic" @selected(old('plan') === 'basic')>Basic</option>
                            <option value="pro" @selected(old('plan') === 'pro')>Pro</option>
                            <option value="premium" @selected(old('plan', 'premium') === 'premium')>Premium</option>
                        </select>
                    </label>
                    <label>Subscription Starts <input type="date" name="subscription_starts_at" value="{{ old('subscription_starts_at', now()->toDateString()) }}" required></label>
                    <label class="field-span-2">Subdomain <input type="text" name="subdomain" value="{{ old('subdomain') }}" placeholder="bsit" required></label>
                    <label>Subscription Expiry <input type="date" name="subscription_expires_at" value="{{ old('subscription_expires_at') }}"></label>
                    <label>Initial Tenant Admin Email <input type="email" name="admin_email" value="{{ old('admin_email') }}" placeholder="admin@bsit.buksu.test" required></label>
                    <label class="field-span-2">
                        Initial Tenant Admin Password
                        <div class="field-with-action">
                            <input id="provision-admin-password" type="password" name="admin_password" placeholder="Leave blank to auto-generate">
                            <button type="button" class="panel-link tiny-link" data-generate-password data-target="#provision-admin-password">Randomize</button>
                        </div>
                    </label>
                    <p class="field-hint field-span-2">Leave this blank to auto-generate a secure password, or click Randomize. The final password is emailed to the tenant admin automatically.</p>
                    <div class="field-span-2">
                        <button type="submit">Provision Tenant</button>
                    </div>
                </form>

                <ul class="soft-list" style="margin-top:18px;">
                    <li>Creates the tenant on the central registry.</li>
                    <li>Builds a separate tenant database.</li>
                    <li>Runs tenant migrations automatically.</li>
                    <li>Creates the first tenant admin account.</li>
                    <li>Stores subscription start and expiry dates on the tenant registry.</li>
                </ul>
            </article>
        </section>
    @endif

    @if ($currentSection === 'directory')
        <section class="content-stack">
            <article class="section-card">
                <div class="action-row">
                    <h2>Tenant Directory</h2>
                    <div class="action-row-actions">
                        <a class="panel-link" href="{{ $dashboardBaseUrl.'?section=directory&create=directory' }}">Add</a>
                    </div>
                </div>

                @if ($showCreatePanel)
                    <div class="form-panel" style="margin-top:16px;">
                        <div class="form-panel-header">
                            <h3>Create Tenant</h3>
                            <a class="panel-close" href="{{ $dashboardBaseUrl.'?section=directory' }}">&times;</a>
                        </div>

                        <form method="POST" action="{{ $tenantCreateAction }}" class="form-grid">
                            @csrf
                            <label class="field-span-2">Tenant Name <input type="text" name="name" value="{{ old('name') }}" placeholder="College of Technology - BSIT" required></label>
                            <label class="field-span-2">Tenant Database <input type="text" name="database" value="{{ old('database') }}" placeholder="buksu_bsit" required></label>
                            <div class="helper-note field-span-2">
                                Add the subscription dates now so the directory can show active and expiring tenants clearly.
                            </div>
                            <label>
                                Plan
                                <select name="plan" required>
                                    <option value="basic" @selected(old('plan') === 'basic')>Basic</option>
                                    <option value="pro" @selected(old('plan') === 'pro')>Pro</option>
                                    <option value="premium" @selected(old('plan', 'premium') === 'premium')>Premium</option>
                                </select>
                            </label>
                            <label>Subscription Starts <input type="date" name="subscription_starts_at" value="{{ old('subscription_starts_at', now()->toDateString()) }}" required></label>
                            <label class="field-span-2">Subdomain <input type="text" name="subdomain" value="{{ old('subdomain') }}" placeholder="bsit" required></label>
                            <label>Subscription Expiry <input type="date" name="subscription_expires_at" value="{{ old('subscription_expires_at') }}"></label>
                            <label>Initial Tenant Admin Email <input type="email" name="admin_email" value="{{ old('admin_email') }}" placeholder="admin@bsit.buksu.test" required></label>
                            <label class="field-span-2">
                                Initial Tenant Admin Password
                                <div class="field-with-action">
                                    <input id="directory-admin-password" type="password" name="admin_password" placeholder="Leave blank to auto-generate">
                                    <button type="button" class="panel-link tiny-link" data-generate-password data-target="#directory-admin-password">Randomize</button>
                                </div>
                            </label>
                            <p class="field-hint field-span-2">Leave blank to auto-generate, or click Randomize. This password is emailed to the tenant admin after provisioning.</p>
                            <div class="field-span-2">
                                <button type="submit">Provision Tenant</button>
                            </div>
                        </form>
                    </div>
                @endif

                @if ($tenants->isEmpty())
                    <p style="margin-top:16px;">No tenants found.</p>
                @else
                    @if ($showEditPanel)
                        <div class="form-panel" style="margin:16px 0 0;">
                            <div class="form-panel-header">
                                <h3>Edit Tenant</h3>
                                <a class="panel-close" href="{{ $dashboardBaseUrl.'?section=directory' }}">&times;</a>
                            </div>

                            <form method="POST" action="{{ route('central.tenants.update', $editingTenant) }}" class="form-grid">
                                @csrf
                                @method('PATCH')
                                <label class="field-span-2">Tenant Name <input type="text" name="name" value="{{ old('name', $editingTenant->name) }}" required></label>
                                <label>
                                    Plan
                                    <select name="plan" required>
                                        <option value="basic" @selected(old('plan', $editingTenant->plan) === 'basic')>Basic</option>
                                        <option value="pro" @selected(old('plan', $editingTenant->plan) === 'pro')>Pro</option>
                                        <option value="premium" @selected(old('plan', $editingTenant->plan) === 'premium')>Premium</option>
                                    </select>
                                </label>
                                <label>
                                    Access Status
                                    <select name="is_active" required>
                                        <option value="1" @selected((string) old('is_active', (int) $editingTenant->is_active) === '1')>Active</option>
                                        <option value="0" @selected((string) old('is_active', (int) $editingTenant->is_active) === '0')>Suspended</option>
                                    </select>
                                </label>
                                <label>Subscription Starts <input type="date" name="subscription_starts_at" value="{{ old('subscription_starts_at', $editingTenant->subscription_starts_at?->toDateString()) }}" required></label>
                                <label>Subscription Expiry <input type="date" name="subscription_expires_at" value="{{ old('subscription_expires_at', $editingTenant->subscription_expires_at?->toDateString()) }}"></label>
                                <label>Subdomain <input type="text" value="{{ $editingTenant->subdomain ?: 'n/a' }}" readonly></label>
                                <label>Tenant Domain <input type="text" value="{{ $editingTenant->domain ?: 'No domain assigned' }}" readonly></label>
                                <label class="field-span-2">Tenant Database <input type="text" value="{{ $editingTenant->database }}" readonly></label>
                                <div class="field-span-2">
                                    <button type="submit">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <table style="margin-top:16px;">
                        <thead>
                            <tr>
                                <th>Tenant</th>
                                <th>Plan</th>
                                <th>Status</th>
                                <th>Database</th>
                                <th>Subdomain</th>
                                <th>Domain</th>
                                <th>Subscription</th>
                                <th>Launch</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tenants as $tenant)
                                <tr>
                                    <td>
                                        <strong style="display:block;">{{ $tenant->name }}</strong>
                                        <small>{{ $tenant->slug }}</small>
                                    </td>
                                    <td><span class="badge">{{ strtoupper($tenant->plan) }}</span></td>
                                    <td><span class="status-pill {{ $tenant->subscriptionStatus() }}">{{ ucfirst($tenant->subscriptionStatus()) }}</span></td>
                                    <td><code>{{ $tenant->database }}</code></td>
                                    <td><code>{{ $tenant->subdomain ?: 'n/a' }}</code></td>
                                    <td>{{ $tenant->domain ?: 'No domain assigned' }}</td>
                                    <td>
                                        <strong style="display:block;">{{ $tenant->subscription_starts_at?->format('M d, Y') ?: 'Not set' }}</strong>
                                        <small>Expires: {{ $tenant->subscription_expires_at?->format('M d, Y') ?: 'Open-ended' }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $tenantDomainUrl = null;
                                            $localTenantUrl = null;

                                            if ($tenant->domain) {
                                                $tenantDomainUrl = request()->getScheme().'://'.$tenant->domain;

                                                if (! in_array(request()->getPort(), [80, 443], true)) {
                                                    $tenantDomainUrl .= ':'.request()->getPort();
                                                }

                                                $tenantDomainUrl .= '/login';
                                            }

                                            if ($tenant->subdomain) {
                                                $localTenantUrl = request()->getScheme().'://'.$tenant->subdomain.'.'.config('tenancy.local_domain_suffix', 'localhost');

                                                if (! in_array(request()->getPort(), [80, 443], true)) {
                                                    $localTenantUrl .= ':'.request()->getPort();
                                                }

                                                $localTenantUrl .= '/login';
                                            }
                                        @endphp
                                        <div class="actions">
                                            <a class="button secondary" href="{{ $dashboardBaseUrl.'?section=directory&edit='.$tenant->slug }}">Edit</a>
                                            <form method="POST" action="{{ route('central.tenants.destroy', $tenant) }}" onsubmit="return confirm('Delete {{ $tenant->name }} and permanently drop database {{ $tenant->database }}? This cannot be undone.');" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="button danger">Delete</button>
                                            </form>
                                            @if ($tenant->canAccessTenantApp())
                                                <a class="button" href="{{ route('tenant.login.default', $tenant) }}">Open Tenant App</a>
                                                @if ($localTenantUrl)
                                                    <a class="button secondary" href="{{ $localTenantUrl }}">Local Domain</a>
                                                @endif
                                                @if ($tenantDomainUrl)
                                                    <a class="button secondary" href="{{ $tenantDomainUrl }}">Tenant Domain</a>
                                                @endif
                                            @else
                                                <span class="panel-link warning">Access Blocked</span>
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
@endsection
