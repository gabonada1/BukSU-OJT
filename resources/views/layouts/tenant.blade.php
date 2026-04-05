@php
    $layoutMode = $layoutMode ?? 'dashboard';
    $hideTenantHeader = $hideTenantHeader ?? ($layoutMode === 'login');
    $tenantBranding = is_array($tenant->settings['branding'] ?? null) ? $tenant->settings['branding'] : [];
    $tenantPortalTitle = filled($tenantBranding['portal_title'] ?? null)
        ? $tenantBranding['portal_title']
        : config('app.name', 'University Practicum');
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
            ['label' => 'Dashboard', 'href' => $tenantDashboardUrl.'#students', 'key' => 'students', 'meta' => 'Assigned students', 'icon' => '📊'],
            ['label' => 'Progress & Hours', 'href' => $tenantDashboardUrl.'#logs', 'key' => 'logs', 'meta' => 'Duty logs', 'icon' => '📈'],
            ['label' => 'Profile', 'href' => $tenantProfileUrl, 'active' => request()->routeIs('tenant*.supervisor.profile.*'), 'meta' => 'Account', 'icon' => '👤'],
        ],
        'student' => [
            ['label' => 'Dashboard', 'href' => $tenantDashboardUrl.'?section=applications', 'key' => 'applications', 'meta' => 'Applications', 'icon' => '📊'],
            ['label' => 'Requirements', 'href' => $tenantDashboardUrl.'?section=requirements', 'key' => 'requirements', 'meta' => 'Forms & files', 'icon' => '📋'],
            ['label' => 'Progress & Hours', 'href' => $tenantDashboardUrl.'?section=logs', 'key' => 'logs', 'meta' => 'Duty record', 'icon' => '📈'],
            ['label' => 'Profile', 'href' => $tenantProfileUrl, 'active' => request()->routeIs('tenant*.student.profile.*'), 'meta' => 'Account', 'icon' => '👤'],
        ],
        default => [
            ['label' => 'Dashboard', 'href' => $tenantDashboardUrl.'?section=companies', 'key' => 'companies', 'meta' => 'Overview', 'icon' => '📊'],
            ['label' => 'Supervisors', 'href' => $tenantDashboardUrl.'?section=supervisors', 'key' => 'supervisors', 'meta' => 'Company mentors', 'icon' => '👥'],
            ['label' => 'Students', 'href' => $tenantDashboardUrl.'?section=students', 'key' => 'students', 'meta' => 'Intern list', 'icon' => '🎓'],
            ['label' => 'Users', 'href' => $tenantDashboardUrl.'?section=users', 'key' => 'users', 'meta' => 'RBAC', 'icon' => '🔑'],
            ['label' => 'Permissions', 'href' => route('tenant.admin.rbac.index'), 'active' => request()->routeIs('tenant*.admin.rbac.*'), 'meta' => 'Role matrix', 'icon' => '🔐'],
            ['label' => 'Requirements', 'href' => $tenantDashboardUrl.'?section=requirements', 'key' => 'requirements', 'meta' => 'Documents', 'icon' => '📁'],
            ['label' => 'Profile', 'href' => $tenantProfileUrl, 'active' => request()->routeIs('tenant*.admin.profile.*'), 'meta' => 'Settings', 'icon' => '⚙️'],
        ],
    };
    $tenantCurrentSection = match ($tenantRole) {
        'student' => request()->query('section', 'applications'),
        'supervisor' => request()->query('section', 'students'),
        default => request()->query('section', 'companies'),
    };
    $activeTenantNav = collect($tenantNavigation)->first(fn ($item) => (($item['key'] ?? null) === $tenantCurrentSection) || ($item['active'] ?? false));
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $pageTitle ?? ($tenant->name.' | '.$tenantPortalTitle) }}</title>
        @include('layouts.partials.app-theme')
    </head>
    <body class="theme-{{ $layoutMode }} central-admin-theme">
        <main class="shell {{ $layoutMode === 'login' ? 'shell-login' : '' }}">
            @if ($hideTenantHeader)
                @yield('content')
            @else
                <div class="console-shell">
                    <aside class="console-sidebar">
                        <div class="console-brand">
                            <div class="console-brand-mark">
                                <img src="{{ $systemLogo }}" alt="{{ $tenantPortalTitle }} Logo" class="brand-logo-image">
                            </div>
                            <div>
                                <strong class="console-brand-title">{{ $tenantPortalTitle }}</strong>
                                <span class="console-brand-subtitle">{{ $tenantRoleLabel }}</span>
                            </div>
                        </div>

                        <nav class="console-nav" aria-label="Tenant portal navigation">
                            @foreach ($tenantNavigation as $item)
                                <a class="console-nav-link {{ (($item['key'] ?? null) === $tenantCurrentSection || ($item['active'] ?? false)) ? 'active' : '' }}" href="{{ $item['href'] }}" title="{{ $item['meta'] }}">
                                    <span class="nav-icon">{{ $item['icon'] }}</span>
                                    <span class="nav-label">
                                        <span>{{ $item['label'] }}</span>
                                        <span>{{ $item['meta'] }}</span>
                                    </span>
                                </a>
                            @endforeach
                        </nav>

                        <div class="console-sidebar-footer">
                            <form method="POST" action="{{ $tenantLogoutAction }}" class="chrome-inline-form">
                                @csrf
                                <button type="submit" class="console-logout-button">Logout</button>
                            </form>
                        </div>
                    </aside>

                    <div class="console-main">
                        <header class="console-topbar">
                            <div class="console-topbar-left">
                                <a class="console-topbar-back" href="{{ $tenantDashboardUrl }}">&lsaquo;</a>
                                <div>
                                    <strong>{{ $activeTenantNav['label'] ?? $tenantRoleLabel }}</strong>
                                    <span>{{ $tenant->name }}</span>
                                </div>
                            </div>
                            <div class="console-topbar-user">
                                <span class="console-avatar">{{ strtoupper(substr((string) ($tenantActorName ?: $tenantRoleLabel), 0, 1)) }}</span>
                                <div>
                                    <strong>{{ $tenantActorName ?: $tenantRoleLabel }}</strong>
                                    <span>{{ $tenantRole === 'admin' ? 'Tenant Access Control' : $tenantAccessLabel }}</span>
                                </div>
                            </div>
                        </header>

                        <section class="console-content">
                            @yield('content')
                        </section>
                    </div>
                </div>
            @endif
        </main>
    </body>
</html>
