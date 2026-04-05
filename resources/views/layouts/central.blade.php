@php
    $layoutMode = $layoutMode ?? 'dashboard';
    $hideCentralHeader = $hideCentralHeader ?? ($layoutMode === 'login');
    $centralActor = auth('central_superadmin')->user();
    $systemLogo = asset('images/logos/logo.jpg');
    $centralCurrentSection = request()->query('section', 'overview');
    $centralNavigation = [
        ['label' => 'Dashboard', 'href' => route('central.dashboard').'?section=overview', 'active' => $centralCurrentSection === 'overview', 'meta' => 'Overview', 'icon' => '📊'],
        ['label' => 'Tenant Directory', 'href' => route('central.dashboard').'?section=directory', 'active' => $centralCurrentSection === 'directory', 'meta' => 'All colleges', 'icon' => '🏢'],
        ['label' => 'Register Tenant', 'href' => 'javascript:void(0)', 'active' => false, 'meta' => 'New college', 'icon' => '➕'],
        ['label' => 'Subscriptions', 'href' => 'javascript:void(0)', 'active' => false, 'meta' => 'Plans', 'icon' => '💳'],
        ['label' => 'Access Control', 'href' => 'javascript:void(0)', 'active' => false, 'meta' => 'Permissions', 'icon' => '🔐'],
        ['label' => 'Audit Logs', 'href' => 'javascript:void(0)', 'active' => false, 'meta' => 'History', 'icon' => '📋'],
    ];
    $activeCentralNav = collect($centralNavigation)->first(fn ($item) => $item['active'] ?? false);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $pageTitle ?? config('app.name', 'University Practicum') }}</title>
        @include('layouts.partials.app-theme')
    </head>
    <body class="theme-{{ $layoutMode }} central-admin-theme">
        <main class="shell {{ $layoutMode === 'login' ? 'shell-login' : '' }}">
            @if ($hideCentralHeader)
                @yield('content')
            @else
                <div class="console-shell">
                    <aside class="console-sidebar">
                        <div class="console-brand">
                            <div class="console-brand-mark">
                                <img src="{{ $systemLogo }}" alt="Bukidnon State University Logo" class="brand-logo-image">
                            </div>
                            <div>
                                <strong class="console-brand-title">BukSU Admin</strong>
                                <span class="console-brand-subtitle">Superadmin</span>
                            </div>
                        </div>

                        <nav class="console-nav" aria-label="University administration navigation">
                            @foreach ($centralNavigation as $item)
                                <a class="console-nav-link {{ ($item['active'] ?? false) ? 'active' : '' }}" href="{{ $item['href'] }}" title="{{ $item['meta'] }}">
                                    <span class="nav-icon">{{ $item['icon'] }}</span>
                                    <span class="nav-label">
                                        <span>{{ $item['label'] }}</span>
                                        <span>{{ $item['meta'] }}</span>
                                    </span>
                                </a>
                            @endforeach
                        </nav>

                        <div class="console-sidebar-footer">
                            <form method="POST" action="{{ route('central.logout') }}" class="chrome-inline-form">
                                @csrf
                                <button type="submit" class="console-logout-button">Logout</button>
                            </form>
                        </div>
                    </aside>

                    <div class="console-main">
                        <header class="console-topbar">
                            <div class="console-topbar-left">
                                <a class="console-topbar-back" href="{{ route('central.dashboard') }}">&lsaquo;</a>
                                <div>
                                    <strong>{{ $activeCentralNav['label'] ?? 'Superadmin' }}</strong>
                                    <span>Superadmin</span>
                                </div>
                            </div>
                            <div class="console-topbar-user">
                                <span class="console-avatar">{{ strtoupper(substr((string) ($centralActor?->name ?: 'SA'), 0, 1)) }}</span>
                                <div>
                                    <strong>{{ $centralActor?->name ?: 'System Administrator' }}</strong>
                                    <span>System Administrator</span>
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
