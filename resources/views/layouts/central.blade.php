@php
    $layoutMode = $layoutMode ?? 'dashboard';
    $hideCentralHeader = $hideCentralHeader ?? ($layoutMode === 'login');
    $centralActor = auth('central_superadmin')->user();
    $systemLogo = asset('images/logos/logo.jpg');
    $centralCurrentSection = request()->query('section', 'overview');
    $centralNavigation = [
        ['label' => 'Overview', 'href' => route('central.dashboard').'?section=overview', 'key' => 'overview'],
        ['label' => 'Provision', 'href' => route('central.dashboard').'?section=provision', 'key' => 'provision'],
        ['label' => 'Directory', 'href' => route('central.dashboard').'?section=directory', 'key' => 'directory'],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $pageTitle ?? config('app.name', 'BukSU Practicum') }}</title>
        @include('layouts.partials.app-theme')
    </head>
    <body class="theme-{{ $layoutMode }}">
        <main class="shell {{ $layoutMode === 'login' ? 'shell-login' : '' }}">
            @if ($hideCentralHeader)
                @yield('content')
            @else
                <div class="workspace-shell">
                    <aside class="central-sidebar">
                        <div class="central-brand-panel">
                            <div class="central-brand">
                                <div class="central-brand-mark">
                                    <img src="{{ $systemLogo }}" alt="BukSU Logo" class="brand-logo-image">
                                </div>
                                <div>
                                    <strong>Central Application</strong>
                                    <span>BukSU Control Layer</span>
                                </div>
                            </div>
                        </div>

                        <nav class="sidebar-nav" aria-label="Central navigation">
                            @foreach ($centralNavigation as $item)
                                <a class="sidebar-link {{ $item['key'] === $centralCurrentSection ? 'active' : '' }}" href="{{ $item['href'] }}">{{ $item['label'] }}</a>
                            @endforeach
                        </nav>

                        <div class="central-meta-row">
                            <div class="central-meta">
                                <strong>Signed In</strong>
                                <span>{{ $centralActor?->name ?: 'Superadmin' }}</span>
                            </div>
                            <div class="central-meta">
                                <strong>Workspace</strong>
                                <span>Tenant Registry</span>
                            </div>
                            <form method="POST" action="{{ route('central.logout') }}" class="chrome-inline-form">
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
