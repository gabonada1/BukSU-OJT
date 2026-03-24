@php
    $layoutMode = $layoutMode ?? 'dashboard';
    $hideTenantHeader = $hideTenantHeader ?? ($layoutMode === 'login');
    $systemLogo = asset('images/logos/logo.jpg');
    $tenantRole = request()->routeIs('tenant*.supervisor.*')
        ? 'supervisor'
        : (request()->routeIs('tenant*.student.*') ? 'student' : 'admin');
    $tenantDomainRoute = request()->routeIs('tenant.domain.*');
    $tenantActor = match ($tenantRole) {
        'supervisor' => auth('supervisor')->user(),
        'student' => auth('student')->user(),
        default => auth('tenant_admin')->user(),
    };
    $tenantActorName = $tenantRole === 'student'
        ? optional($tenantActor)->full_name
        : optional($tenantActor)->name;
    $tenantRoleLabel = match ($tenantRole) {
        'supervisor' => 'Teacher',
        'student' => 'Student',
        default => 'College Admin',
    };
    $tenantDashboardUrl = match ($tenantRole) {
        'supervisor' => $tenantDomainRoute
            ? route('tenant.domain.supervisor.dashboard')
            : route('tenant.supervisor.dashboard', $tenant),
        'student' => $tenantDomainRoute
            ? route('tenant.domain.student.dashboard')
            : route('tenant.student.dashboard', $tenant),
        default => $tenantDomainRoute
            ? route('tenant.domain.admin.dashboard')
            : route('tenant.admin.dashboard', $tenant),
    };
    $tenantProfileUrl = $tenantDomainRoute
        ? route('tenant.domain.profile.show')
        : route('tenant.profile.show', $tenant);
    $tenantLogoutAction = $tenantDomainRoute
        ? route('tenant.domain.logout')
        : route('tenant.logout', $tenant);
    $tenantDomainLabel = $tenant->domain
        ?: ($tenant->subdomain ? $tenant->subdomain.'.'.config('tenancy.local_domain_suffix', 'localhost') : '/tenants/'.$tenant->slug);
    $tenantNavigation = match ($tenantRole) {
        'supervisor' => [
            ['label' => 'Students', 'href' => $tenantDashboardUrl.'#students'],
            ['label' => 'Logs', 'href' => $tenantDashboardUrl.'#logs'],
            ['label' => 'Profile', 'href' => $tenantProfileUrl, 'active' => request()->routeIs('tenant*.profile.*')],
        ],
        'student' => [
            ['label' => 'Applications', 'href' => $tenantDashboardUrl.'#applications'],
            ['label' => 'Requirements', 'href' => $tenantDashboardUrl.'#requirements'],
            ['label' => 'Logs', 'href' => $tenantDashboardUrl.'#logs'],
            ['label' => 'Profile', 'href' => $tenantProfileUrl, 'active' => request()->routeIs('tenant*.profile.*')],
        ],
        default => [
            ['label' => 'Companies', 'href' => $tenantDashboardUrl.'?section=companies', 'key' => 'companies'],
            ['label' => 'Applications', 'href' => $tenantDashboardUrl.'?section=applications', 'key' => 'applications'],
            ['label' => 'Supervisors', 'href' => $tenantDashboardUrl.'?section=supervisors', 'key' => 'supervisors'],
            ['label' => 'Students', 'href' => $tenantDashboardUrl.'?section=students', 'key' => 'students'],
            ['label' => 'User Management', 'href' => $tenantDashboardUrl.'?section=users', 'key' => 'users'],
            ['label' => 'Requirements', 'href' => $tenantDashboardUrl.'?section=requirements', 'key' => 'requirements'],
            ['label' => 'Hour Logs', 'href' => $tenantDashboardUrl.'?section=hours', 'key' => 'hours'],
            ['label' => 'Profile', 'href' => $tenantProfileUrl, 'active' => request()->routeIs('tenant*.profile.*')],
        ],
    };
    $tenantCurrentSection = request()->query('section', 'companies');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $pageTitle ?? ($tenant->name.' | '.config('app.name', 'BukSU Practicum')) }}</title>
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
                                    <img src="{{ $systemLogo }}" alt="BukSU Logo" class="brand-logo-image">
                                </div>
                                <div>
                                    <strong>{{ $tenantRoleLabel }}</strong>
                                    <span>{{ $tenant->name }}</span>
                                </div>
                            </div>
                        </div>

                        <nav class="sidebar-nav" aria-label="Tenant navigation">
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
                                <strong>Domain</strong>
                                <span>{{ $tenantDomainLabel }}</span>
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
