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
            'summary' => 'Designed for small colleges or limited internship programs.',
            'features' => [
                'Up to 200 students per tenant',
                'Partner company management',
                'Student application submission',
                'Document uploads (MOA, resume, clearance, etc.)',
                'Basic OJT hours tracking',
                'Supervisor evaluation forms',
                'Limited support and reports',
                'No advanced dashboards or analytics',
            ],
        ],
        'pro' => [
            'label' => 'Pro',
            'summary' => 'For medium-sized colleges with more active programs.',
            'features' => [
                'Up to 500 students per tenant',
                'All Basic plan features',
                'Progress tracking dashboards for students and admin',
                'Commenting and revision requests on documents and reports',
                'Monthly summary reports for placements and evaluations',
                'Notifications for pending tasks and approvals',
            ],
        ],
        'premium' => [
            'label' => 'Premium',
            'summary' => 'For large colleges or multi-college setups with full program oversight.',
            'features' => [
                'Unlimited students',
                'All Pro plan features',
                'Advanced analytics and reports (placement stats, OJT hour completion, evaluation summaries)',
                'Certificate generation for completed internships',
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
        <title>Tenant Unavailable</title>
        <style>
            :root {
                --page: #101114;
                --page-alt: #18191e;
                --shell: #202228;
                --panel: #2b2e36;
                --panel-soft: #343842;
                --card-ink: #f6f1ed;
                --card-muted: #b9b0aa;
                --accent: #c86b61;
                --accent-strong: #a74c46;
                --warm: #d6b36a;
                --danger: #d88e83;
                --success: #8fb8a0;
                --shadow: 0 26px 56px rgba(0, 0, 0, 0.28);
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                padding: 32px 24px;
                font-family: "Bahnschrift", "Segoe UI", "Trebuchet MS", sans-serif;
                background: radial-gradient(circle at top center, rgba(200, 107, 97, 0.08), transparent 24%), linear-gradient(180deg, var(--page), var(--page-alt));
                color: #f4efeb;
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
                color: #ece5e0;
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
                color: #f6e1dd;
                background: rgba(216, 142, 131, 0.16);
                border: 1px solid rgba(216, 142, 131, 0.24);
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
                box-shadow: 0 12px 24px rgba(167, 76, 70, 0.24);
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
                background: linear-gradient(180deg, #252830, #1f2127);
                border: 1px solid rgba(255, 255, 255, 0.06);
                box-shadow: var(--shadow);
                display: grid;
                gap: 16px;
            }

            .plan-card.active {
                border-color: rgba(200, 107, 97, 0.34);
                box-shadow: 0 22px 40px rgba(167, 76, 70, 0.14);
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
                color: #ece5e0;
            }

            .plan-card.active .plan-badge {
                background: rgba(200, 107, 97, 0.16);
                border-color: rgba(200, 107, 97, 0.24);
                color: #ffd9d4;
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
                    <span class="eyebrow">Tenant Access Blocked</span>
                    <h1>{{ $tenant->name }}</h1>
                    <p>{{ $message }}</p>

                    <div class="meta" style="margin-top:22px;">
                        <div class="status">
                            <strong>Status</strong>
                            <span>{{ ucfirst($tenant->subscriptionStatus()) }}</span>
                        </div>
                        <div>
                            <strong>Subscription Starts</strong>
                            <span>{{ $tenant->subscription_starts_at?->format('M d, Y') ?: 'Not set' }}</span>
                        </div>
                        <div>
                            <strong>Subscription Expires</strong>
                            <span>{{ $tenant->subscription_expires_at?->format('M d, Y') ?: 'Open-ended' }}</span>
                        </div>
                    </div>
                </article>

                <aside class="panel cta-card">
                    <div>
                        <span class="eyebrow">Subscription</span>
                        <h2 style="margin:16px 0 8px;font-size:28px;letter-spacing:-0.03em;color:var(--card-ink);">Restore Access</h2>
                        <p>Open the central subscription side to renew this tenant plan and reactivate the domain.</p>
                    </div>

                    <a class="button" href="{{ $renewUrl }}">Subscribe Again</a>

                    <div class="helper">
                        <strong>Current Plan</strong>
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
