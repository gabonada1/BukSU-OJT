@php
    $layoutMode = $layoutMode ?? 'dashboard';
    $hideTenantHeader = $hideTenantHeader ?? ($layoutMode === 'login');
    $tenantBranding = is_array($tenant->settings['branding'] ?? null) ? $tenant->settings['branding'] : [];
    $tenantPortalTitle = filled($tenantBranding['portal_title'] ?? null)
        ? $tenantBranding['portal_title']
        : config('app.name', 'BukSU Practicum Portal');
    $tenantAccent = preg_match('/^#[0-9A-Fa-f]{6}$/', (string) ($tenantBranding['accent'] ?? ''))
        ? strtoupper($tenantBranding['accent'])
        : '#7B1C2E';
    $tenantSecondary = preg_match('/^#[0-9A-Fa-f]{6}$/', (string) ($tenantBranding['secondary'] ?? ''))
        ? strtoupper($tenantBranding['secondary'])
        : '#F5A623';
    $systemLogo = filled($tenantBranding['logo_path'] ?? null)
        ? asset($tenantBranding['logo_path'])
        : asset('images/logos/logo.jpg');
    $tenantRole = $tenantRole ?? match (true) {
        auth('supervisor')->check() => 'supervisor',
        auth('student')->check() => 'student',
        auth('tenant_admin')->check() => 'admin',
        request()->routeIs('tenant*.supervisor.*') => 'supervisor',
        request()->routeIs('tenant*.student.*') => 'student',
        default => 'admin',
    };
    $tenantActor = match ($tenantRole) {
        'supervisor' => auth('supervisor')->user(),
        'student' => auth('student')->user(),
        default => auth('tenant_admin')->user(),
    };
    $tenantActorName = $tenantRole === 'student'
        ? optional($tenantActor)->full_name
        : optional($tenantActor)->name;
    $tenantRoleLabel = match ($tenantRole) {
        'supervisor' => 'Company Supervisor',
        'student' => 'Student',
        default => 'Internship Coordinator',
    };
    $tenantAccessLabel = preg_replace('#^https?://#', '', app(\App\Support\Tenancy\TenantUrlGenerator::class)->loginUrl($tenant));
    $tenantDashboardUrl = match ($tenantRole) {
        'supervisor' => route('tenant.supervisor.dashboard'),
        'student' => route('tenant.student.dashboard'),
        default => route('tenant.admin.dashboard'),
    };
    $tenantProfileUrl = match ($tenantRole) {
        'supervisor' => route('tenant.supervisor.profile.show'),
        'student' => route('tenant.student.profile.show'),
        default => route('tenant.admin.profile.show'),
    };
    $tenantLogoutAction = match ($tenantRole) {
        'supervisor' => route('tenant.supervisor.logout'),
        'student' => route('tenant.student.logout'),
        default => route('tenant.admin.logout'),
    };
    $tenantNavigation = match ($tenantRole) {
        'supervisor' => [
            ['label' => 'Students', 'href' => $tenantDashboardUrl.'#students'],
            ['label' => 'Progress & Hours', 'href' => $tenantDashboardUrl.'#logs'],
            ['label' => 'Profile', 'href' => $tenantProfileUrl, 'active' => request()->routeIs('tenant*.supervisor.profile.*')],
        ],
        'student' => [
            ['label' => 'Internship Applications', 'href' => $tenantDashboardUrl.'#applications'],
            ['label' => 'Forms & Requirements', 'href' => $tenantDashboardUrl.'#requirements'],
            ['label' => 'Progress & Hours', 'href' => $tenantDashboardUrl.'#logs'],
            ['label' => 'Profile', 'href' => $tenantProfileUrl, 'active' => request()->routeIs('tenant*.student.profile.*')],
        ],
        default => [
            ['label' => 'Organizations', 'href' => $tenantDashboardUrl.'?section=companies', 'key' => 'companies'],
            ['label' => 'Student Applications', 'href' => $tenantDashboardUrl.'?section=applications', 'key' => 'applications'],
            ['label' => 'Company Supervisors', 'href' => $tenantDashboardUrl.'?section=supervisors', 'key' => 'supervisors'],
            ['label' => 'Students', 'href' => $tenantDashboardUrl.'?section=students', 'key' => 'students'],
            ['label' => 'RBAC & Users', 'href' => $tenantDashboardUrl.'?section=users', 'key' => 'users'],
            ['label' => 'Role Permissions', 'href' => route('tenant.admin.rbac.index'), 'active' => request()->routeIs('tenant*.admin.rbac.*')],
            ['label' => 'Forms & Requirements', 'href' => $tenantDashboardUrl.'?section=requirements', 'key' => 'requirements'],
            ['label' => 'Progress & Hours', 'href' => $tenantDashboardUrl.'?section=hours', 'key' => 'hours'],
            ['label' => 'Profile', 'href' => $tenantProfileUrl, 'active' => request()->routeIs('tenant*.admin.profile.*')],
        ],
    };
    $tenantCurrentSection = request()->query('section', 'companies');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $pageTitle ?? ($tenant->name.' | '.$tenantPortalTitle) }}</title>
        @include('layouts.partials.app-theme')
    </head>
    <body class="theme-{{ $layoutMode }}">
        <main class="shell {{ $layoutMode === 'login' ? 'shell-login' : '' }}">
            @if ($hideTenantHeader)
                @yield('content')
            @else
                <div class="workspace-shell">
                    <aside class="tenant-sidebar">
                        <div class="tenant-brand-panel">
                            <div class="tenant-brand">
                                <div class="tenant-brand-mark">
                                    <img src="{{ $systemLogo }}" alt="{{ $tenantPortalTitle }} Logo" class="brand-logo-image">
                                </div>
                                <div>
                                    <strong>{{ $tenantRoleLabel }}</strong>
                                    <span>{{ $tenant->name }}</span>
                                    <small class="brand-university-label">{{ $tenantPortalTitle }}</small>
                                </div>
                            </div>
                        </div>

                        <nav class="sidebar-nav" aria-label="College portal navigation">
                            @foreach ($tenantNavigation as $item)
                                <a class="sidebar-link {{ (($item['key'] ?? null) === $tenantCurrentSection || ($item['active'] ?? false)) ? 'active' : '' }}" href="{{ $item['href'] }}">{{ $item['label'] }}</a>
                            @endforeach
                        </nav>

                        <div class="tenant-meta-row">
                            <div class="tenant-meta tenant-identity">
                                <strong>Signed In</strong>
                                <span>{{ $tenantActorName ?: $tenantRoleLabel }}</span>
                            </div>
                            <div class="tenant-meta">
                                <strong>{{ $tenantRole === 'admin' ? 'RBAC Scope' : 'College Portal' }}</strong>
                                <span>{{ $tenantRole === 'admin' ? 'Tenant users and portal records' : $tenantAccessLabel }}</span>
                            </div>
                            <form method="POST" action="{{ $tenantLogoutAction }}" class="chrome-inline-form">
                                @csrf
                                <button type="submit" class="button secondary">Logout</button>
                            </form>
                        </div>
                    </aside>

                    <section class="workspace-main">
                        @yield('content')
                    </section>
                </div>
            @endif
        </main>
    </body>
</html>
