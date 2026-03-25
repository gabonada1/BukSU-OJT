@php
    $appUrl = config('app.url', request()->getSchemeAndHttpHost());
    $appParts = parse_url($appUrl);
    $centralDomains = config('tenancy.central_domains', []);
    $centralHost = collect($centralDomains)->first(fn ($domain) => $domain === 'localhost')
        ?? ($centralDomains[0] ?? ($appParts['host'] ?? request()->getHost()));
    $centralScheme = $appParts['scheme'] ?? request()->getScheme();
    $centralPort = isset($appParts['port']) ? ':'.$appParts['port'] : '';
    $centralPath = rtrim($appParts['path'] ?? '', '/');
    $renewUrl = $centralScheme.'://'.$centralHost.$centralPort.$centralPath.'/central/login';

    $plans = [
        'basic' => [
            'label' => 'Basic',
            'summary' => 'Designed for smaller colleges or focused practicum programs.',
            'features' => [
                'Up to 200 students per college portal',
                'Partner organization management',
                'Student OJT application submission',
                'Document uploads (MOA, resume, clearance, etc.)',
                'Basic OJT hour tracking',
                'Company supervisor evaluation forms',
                'Limited support and reports',
                'No advanced dashboards or analytics',
            ],
        ],
        'pro' => [
            'label' => 'Pro',
            'summary' => 'For medium-sized colleges with more active practicum operations.',
            'features' => [
                'Up to 500 students per college portal',
                'All Basic college license features',
                'Progress tracking dashboards for students and coordinators',
                'Commenting and revision requests on documents and reports',
                'Monthly summary reports for placements and evaluations',
                'Notifications for pending tasks and approvals',
            ],
        ],
        'premium' => [
            'label' => 'Premium',
            'summary' => 'For large colleges with full practicum oversight and reporting needs.',
            'features' => [
                'Unlimited students',
                'All Pro college license features',
                'Advanced analytics and reports (placement stats, OJT hour completion, evaluation summaries)',
                'Certificate generation for completed OJT deployments',
                'Custom branding per college (logo, theme)',
                'Priority support',
                'API access for integration with other college systems',
            ],
        ],
    ];

    $currentPlan = strtolower($tenant->plan ?? 'basic');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>College Portal Unavailable</title>
        <style>
            :root {
                --page: #13131f;
                --page-alt: #1a1a2e;
                --shell: #1f1f38;
                --panel: #272745;
                --panel-soft: #31315a;
                --card-ink: #f0ecf8;
                --card-muted: #a89ec0;
                --accent: #7B1C2E;
                --accent-strong: #5E1423;
                --warm: #F5A623;
                --danger: #d07070;
                --success: #6db88a;
                --shadow: 0 26px 56px rgba(0, 0, 0, 0.32);
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                padding: 32px 24px;
                font-family: "Bahnschrift", "Segoe UI", "Trebuchet MS", sans-serif;
                background: radial-gradient(circle at top center, rgba(123, 28, 46, 0.12), transparent 28%), linear-gradient(180deg, var(--page), var(--page-alt));
                color: #f0edf5;
            }

            .wrap {
                width: min(1180px, 100%);
                margin: 0 auto;
                display: grid;
                gap: 22px;
            }

            .panel {
                padding: 34px;
                border-radius: 30px;
                background: linear-gradient(180deg, var(--shell), var(--panel));
                border: 1px solid rgba(255, 255, 255, 0.08);
                box-shadow: var(--shadow);
            }

            .eyebrow {
                display: inline-flex;
                align-items: center;
                padding: 7px 12px;
                border-radius: 999px;
                font-size: 11px;
                font-weight: 700;
                letter-spacing: 0.14em;
                text-transform: uppercase;
                color: #ece5f4;
                background: rgba(255, 255, 255, 0.06);
                border: 1px solid rgba(255, 255, 255, 0.08);
            }

            h1,
            h2,
            h3,
            p {
                margin-top: 0;
            }

            h1 {
                margin: 16px 0 10px;
                font-size: 40px;
                letter-spacing: -0.04em;
                color: var(--card-ink);
            }

            p {
                margin: 0;
                line-height: 1.7;
                color: var(--card-muted);
            }

            .top {
                display: grid;
                grid-template-columns: minmax(0, 1.2fr) minmax(320px, 0.8fr);
                gap: 18px;
            }

            .meta {
                display: grid;
                gap: 12px;
            }

            .meta div {
                padding: 14px 16px;
                border-radius: 18px;
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.06);
                color: var(--card-muted);
            }

            strong {
                display: block;
                margin-bottom: 4px;
                color: var(--card-ink);
            }

            .status {
                color: #f3dde3;
                background: rgba(123, 28, 46, 0.16);
                border: 1px solid rgba(123, 28, 46, 0.24);
            }

            .cta-card {
                display: grid;
                gap: 16px;
                align-content: start;
            }

            .button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                min-height: 48px;
                padding: 12px 18px;
                border-radius: 16px;
                border: 1px solid transparent;
                background: linear-gradient(135deg, var(--accent), var(--accent-strong));
                color: #fff;
                font: inherit;
                font-weight: 700;
                letter-spacing: 0.02em;
                text-decoration: none;
                box-shadow: 0 12px 24px rgba(94, 20, 35, 0.24);
            }

            .button.secondary {
                background: rgba(255, 255, 255, 0.04);
                border-color: rgba(255, 255, 255, 0.08);
                color: var(--card-ink);
                box-shadow: none;
            }

            .helper {
                padding: 16px 18px;
                border-radius: 18px;
                background: rgba(255, 255, 255, 0.04);
                border: 1px solid rgba(255, 255, 255, 0.06);
            }

            .plans-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 18px;
            }

            .plan-card {
                padding: 24px 22px;
                border-radius: 26px;
                background: linear-gradient(180deg, #252546, #1b1b31);
                border: 1px solid rgba(255, 255, 255, 0.06);
                box-shadow: var(--shadow);
                display: grid;
                gap: 16px;
            }

            .plan-card.active {
                border-color: rgba(123, 28, 46, 0.34);
                box-shadow: 0 22px 40px rgba(94, 20, 35, 0.18);
            }

            .plan-top {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 12px;
            }

            .plan-top h2 {
                margin: 0;
                font-size: 24px;
                letter-spacing: -0.03em;
                color: var(--card-ink);
            }

            .plan-badge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 7px 12px;
                border-radius: 999px;
                font-size: 11px;
                font-weight: 700;
                letter-spacing: 0.14em;
                text-transform: uppercase;
                background: rgba(255, 255, 255, 0.06);
                border: 1px solid rgba(255, 255, 255, 0.08);
                color: #ece5f4;
            }

            .plan-card.active .plan-badge {
                background: rgba(123, 28, 46, 0.16);
                border-color: rgba(123, 28, 46, 0.24);
                color: #ffd9e2;
            }

            .plan-summary {
                color: var(--card-muted);
                min-height: 50px;
            }

            .plan-card ul {
                margin: 0;
                padding: 0;
                list-style: none;
                display: grid;
                gap: 10px;
            }

            .plan-card li {
                position: relative;
                padding-left: 18px;
                color: var(--card-muted);
                line-height: 1.6;
            }

            .plan-card li::before {
                content: "";
                position: absolute;
                left: 0;
                top: 10px;
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: var(--warm);
            }

            .plan-card.active li::before {
                background: var(--accent);
            }

            @media (max-width: 980px) {
                .top,
                .plans-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
        <main class="wrap">
            <section class="top">
                <article class="panel">
                    <span class="eyebrow">College Portal Access Paused</span>
                    <h1>{{ $tenant->name }}</h1>
                    <p>{{ $message }}</p>

                    <div class="meta" style="margin-top:22px;">
                        <div class="status">
                            <strong>Status</strong>
                            <span>{{ ucfirst($tenant->subscriptionStatus()) }}</span>
                        </div>
                        <div>
                            <strong>License Starts</strong>
                            <span>{{ $tenant->subscription_starts_at?->format('M d, Y') ?: 'Not set' }}</span>
                        </div>
                        <div>
                            <strong>License Expires</strong>
                            <span>{{ $tenant->subscription_expires_at?->format('M d, Y') ?: 'Open-ended' }}</span>
                        </div>
                    </div>
                </article>

                <aside class="panel cta-card">
                    <div>
                        <span class="eyebrow">College License</span>
                        <h2 style="margin:16px 0 8px;font-size:28px;letter-spacing:-0.03em;color:var(--card-ink);">Restore Access</h2>
                        <p>Open University Administration to renew this college license tier and reactivate the portal.</p>
                    </div>

                    <a class="button" href="{{ $renewUrl }}">Open University Administration</a>

                    <div class="helper">
                        <strong>Current License</strong>
                        <span style="color:var(--card-muted);">{{ strtoupper($tenant->plan) }}</span>
                    </div>
                </aside>
            </section>

            <section class="plans-grid">
                @foreach ($plans as $key => $plan)
                    <article class="plan-card {{ $currentPlan === $key ? 'active' : '' }}">
                        <div class="plan-top">
                            <div>
                                <h2>{{ $plan['label'] }}</h2>
                                <p class="plan-summary">{{ $plan['summary'] }}</p>
                            </div>
                            <span class="plan-badge">{{ $currentPlan === $key ? 'Current' : 'Plan' }}</span>
                        </div>

                        <ul>
                            @foreach ($plan['features'] as $feature)
                                <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                    </article>
                @endforeach
            </section>
        </main>
    </body>
</html>
