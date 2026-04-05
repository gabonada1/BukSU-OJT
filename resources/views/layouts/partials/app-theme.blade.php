@php
    $hexPattern = '/^#[0-9A-Fa-f]{6}$/';
    $tenantAccent = isset($tenantAccent) && preg_match($hexPattern, (string) $tenantAccent)
        ? strtoupper($tenantAccent)
        : '#7B1C2E';
    $tenantSecondary = isset($tenantSecondary) && preg_match($hexPattern, (string) $tenantSecondary)
        ? strtoupper($tenantSecondary)
        : '#F5A623';

    $hexToRgb = static function (string $hex): array {
        $normalized = ltrim($hex, '#');

        if (strlen($normalized) !== 6) {
            $normalized = '7B1C2E';
        }

        return [
            hexdec(substr($normalized, 0, 2)),
            hexdec(substr($normalized, 2, 2)),
            hexdec(substr($normalized, 4, 2)),
        ];
    };

    $toRgba = static function (string $hex, float $alpha) use ($hexToRgb): string {
        [$red, $green, $blue] = $hexToRgb($hex);

        return sprintf('rgba(%d, %d, %d, %.2F)', $red, $green, $blue, $alpha);
    };

    $mixHex = static function (string $baseHex, string $targetHex, float $ratio) use ($hexToRgb): string {
        [$baseRed, $baseGreen, $baseBlue] = $hexToRgb($baseHex);
        [$targetRed, $targetGreen, $targetBlue] = $hexToRgb($targetHex);
        $ratio = max(0, min(1, $ratio));

        return sprintf(
            '#%02X%02X%02X',
            (int) round(($baseRed * (1 - $ratio)) + ($targetRed * $ratio)),
            (int) round(($baseGreen * (1 - $ratio)) + ($targetGreen * $ratio)),
            (int) round(($baseBlue * (1 - $ratio)) + ($targetBlue * $ratio)),
        );
    };

    $tenantAccentStrong = $mixHex($tenantAccent, '#000000', 0.24);
    $tenantAccentGlow = $toRgba($tenantAccent, 0.12);
    $tenantAccentMist = $toRgba($tenantAccent, 0.15);
    $tenantAccentSoft = $toRgba($tenantAccent, 0.18);
    $tenantAccentBorder = $toRgba($tenantAccent, 0.24);
    $tenantAccentActiveBorder = $toRgba($tenantAccent, 0.32);
    $tenantAccentFocusBorder = $toRgba($tenantAccent, 0.52);
    $tenantAccentFocusRing = $toRgba($tenantAccent, 0.16);
    $tenantAccentShadow = $toRgba($tenantAccent, 0.28);
    $tenantAccentShadowStrong = $toRgba($tenantAccent, 0.32);
    $tenantSecondarySoft = $toRgba($tenantSecondary, 0.16);
    $tenantSecondaryGlow = $toRgba($tenantSecondary, 0.06);
    $tenantSecondaryBorder = $toRgba($tenantSecondary, 0.14);
    $tenantSecondaryStrong = $toRgba($tenantSecondary, 0.75);
@endphp

<style>
    :root {
        --page: #13131f;
        --page-alt: #1a1a2e;
        --frame-ink: #f0edf5;
        --frame-muted: #b0a8c0;
        --shell: #1f1f38;
        --panel: #272745;
        --panel-soft: #31315a;
        --panel-border: rgba(255, 255, 255, 0.07);
        --card-ink: #f0ecf8;
        --card-muted: #a89ec0;
        --soft-light: rgba(255, 255, 255, 0.04);
        --soft-line: rgba(255, 255, 255, 0.07);
        --accent: {{ $tenantAccent }};
        --accent-strong: {{ $tenantAccentStrong }};
        --accent-soft: {{ $tenantAccentSoft }};
        --accent-glow: {{ $tenantAccentGlow }};
        --accent-mist: {{ $tenantAccentMist }};
        --accent-border: {{ $tenantAccentBorder }};
        --accent-active-border: {{ $tenantAccentActiveBorder }};
        --accent-focus-border: {{ $tenantAccentFocusBorder }};
        --accent-focus-ring: {{ $tenantAccentFocusRing }};
        --accent-shadow: {{ $tenantAccentShadow }};
        --accent-shadow-strong: {{ $tenantAccentShadowStrong }};
        --warm: {{ $tenantSecondary }};
        --warm-soft: {{ $tenantSecondarySoft }};
        --warm-glow: {{ $tenantSecondaryGlow }};
        --warm-border: {{ $tenantSecondaryBorder }};
        --warm-strong: {{ $tenantSecondaryStrong }};
        --success: #6db88a;
        --danger: #d07070;
        --shadow: 0 24px 52px rgba(0, 0, 0, 0.32);
    }

    * {
        box-sizing: border-box;
    }

    html,
    body {
        min-height: 100%;
    }

    body {
        margin: 0;
        font-family: "Bahnschrift", "Segoe UI", "Trebuchet MS", sans-serif;
        -webkit-font-smoothing: antialiased;
        text-rendering: optimizeLegibility;
    }

    body.theme-dashboard {
        background: radial-gradient(circle at top center, var(--accent-glow), transparent 28%), linear-gradient(180deg, var(--page) 0%, var(--page-alt) 100%);
        color: var(--frame-ink);
    }

    body.theme-login {
        background: radial-gradient(circle at top left, var(--accent-mist) 0%, transparent 30%), radial-gradient(circle at bottom right, var(--warm-glow) 0%, transparent 40%), linear-gradient(180deg, #0d0d1a 0%, var(--page) 54%, var(--page-alt) 100%);
        color: var(--frame-ink);
    }

    a {
        color: inherit;
        text-decoration: none;
    }

    h1,
    h2,
    h3,
    p {
        margin-top: 0;
    }

    strong {
        color: inherit;
    }

    .shell {
        width: calc(100% - 36px);
        max-width: none;
        margin: 18px 0 18px 18px;
        padding: 0 18px 24px 0;
        position: relative;
    }

    .shell-login {
        width: min(1280px, calc(100% - 44px));
        margin: 0 auto;
        padding: 28px 0;
        min-height: 100vh;
        display: flex;
        align-items: center;
    }

    .workspace-shell {
        display: grid;
        grid-template-columns: 252px minmax(0, 1fr);
        gap: 28px;
        align-items: start;
    }

    .central-sidebar,
    .tenant-sidebar {
        position: sticky;
        top: 18px;
        display: grid;
        gap: 18px;
        align-self: start;
        min-height: calc(100vh - 36px);
        padding: 22px 18px 18px;
        border-radius: 30px;
        background: linear-gradient(180deg, var(--shell), #161629);
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: var(--shadow);
    }

    .central-brand-panel,
    .tenant-brand-panel {
        padding: 6px 4px 18px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .central-brand,
    .tenant-brand {
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }

    .central-brand-mark,
    .tenant-brand-mark {
        width: 52px;
        height: 52px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        letter-spacing: 0.14em;
        color: #fff;
        background: linear-gradient(145deg, var(--accent-strong), var(--accent));
        box-shadow: 0 14px 28px var(--accent-shadow);
        overflow: hidden;
        padding: 0;
    }

    .brand-logo-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .central-brand strong,
    .tenant-brand strong,
    .central-meta strong,
    .tenant-meta strong {
        display: block;
        font-size: 10px;
        letter-spacing: 0.22em;
        text-transform: uppercase;
        color: #dccde6;
    }

    .central-brand span,
    .tenant-brand span,
    .central-meta span,
    .tenant-meta span {
        display: block;
        margin-top: 4px;
        color: var(--card-ink);
    }

    .central-brand span,
    .tenant-brand span {
        max-width: 132px;
        font-size: 15px;
        line-height: 1.3;
    }

    .brand-university-label {
        display: block;
        font-size: 9px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--warm);
        margin-top: 3px;
        opacity: 0.85;
    }

    .sidebar-nav {
        display: grid;
        gap: 10px;
    }

    .sidebar-link {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 18px 16px 36px;
        border-radius: 20px;
        border: 1px solid transparent;
        background: transparent;
        color: var(--card-muted);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease, transform 0.15s ease;
    }

    .sidebar-link::before {
        content: "";
        position: absolute;
        left: 18px;
        top: 50%;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: rgba(244, 239, 235, 0.28);
        transform: translateY(-50%);
    }

    .sidebar-link::after {
        content: "";
        position: absolute;
        right: 18px;
        top: 50%;
        width: 8px;
        height: 8px;
        border-top: 1.5px solid rgba(244, 239, 235, 0.28);
        border-right: 1.5px solid rgba(244, 239, 235, 0.28);
        transform: translateY(-50%) rotate(45deg);
    }

    .sidebar-link:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.06);
        color: var(--card-ink);
        transform: translateX(2px);
    }

    .sidebar-link.active {
        background: var(--accent-soft);
        border-color: var(--accent-active-border);
        color: #fff;
    }

    .sidebar-link.active::before {
        background: var(--accent);
    }

    .sidebar-link.active::after {
        border-top-color: #fff;
        border-right-color: #fff;
    }

    .central-meta-row,
    .tenant-meta-row {
        margin-top: auto;
        padding-top: 18px;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        display: grid;
        gap: 12px;
    }

    .central-meta,
    .tenant-meta {
        padding: 0;
        background: none;
        border: none;
    }

    .chrome-inline-form {
        display: block;
    }

    .workspace-main {
        min-width: 0;
        display: grid;
        gap: 18px;
        padding-top: 6px;
    }

    .page-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        padding: 24px 26px;
        border-radius: 28px;
        background: linear-gradient(135deg, var(--accent-soft), var(--warm-glow));
        border: 1px solid var(--warm-border);
        box-shadow: var(--shadow);
    }

    .page-head h1 {
        margin: 0;
        font-size: 40px;
        line-height: 1.05;
        letter-spacing: -0.05em;
        color: var(--frame-ink);
    }

    .page-head p {
        margin: 8px 0 0;
        color: var(--frame-muted);
        font-size: 16px;
        letter-spacing: 0.02em;
    }

    .page-mini-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .page-mini-card {
        min-width: 118px;
        padding: 16px 18px;
        border-radius: 20px;
        background: linear-gradient(180deg, var(--shell), #19192e);
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: var(--shadow);
    }

    .page-mini-card strong {
        display: block;
        font-size: 10px;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: #d8c7e4;
    }

    .page-mini-card span {
        display: block;
        margin-top: 6px;
        font-size: 28px;
        font-weight: 800;
        line-height: 1;
        color: var(--card-ink);
    }

    .card,
    .section-card {
        background: linear-gradient(180deg, var(--shell), var(--panel));
        border: 1px solid var(--panel-border);
        box-shadow: var(--shadow);
        border-radius: 30px;
    }

    .theme-dashboard .card,
    .section-card {
        padding: 24px;
    }

    .card h2,
    .card h3,
    .card strong,
    .section-card h2,
    .section-card h3,
    .section-card strong,
    .form-panel h3,
    .preview-widget h4,
    .preview-top h3,
    .login-panel h1,
    .login-support strong {
        color: var(--card-ink);
    }

    .card p,
    .card li,
    .card td,
    .card th,
    .card label,
    .card small,
    .section-card p,
    .section-card li,
    .section-card td,
    .section-card th,
    .section-card label,
    .section-card small,
    .form-panel p,
    .form-panel li,
    .form-panel label,
    .soft-list li,
    .legend,
    .legend li,
    .preview-widget p,
    .preview-list li,
    .login-panel p,
    .login-panel label,
    .login-art p {
        color: var(--card-muted);
    }

    .content-stack,
    .dashboard-stack,
    .stack-sections,
    .ring-grid,
    .chart-grid,
    .quick-grid,
    .forms-grid,
    .records-grid,
    .profile-grid,
    .stack-column {
        display: grid;
        gap: 18px;
    }

    .two-column-layout {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .chart-grid {
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    }

    .flash,
    .error-panel {
        padding: 15px 18px;
        border-radius: 20px;
        border: 1px solid;
    }

    .flash {
        background: rgba(155, 199, 163, 0.16);
        border-color: rgba(119, 173, 149, 0.24);
        color: #d9f1df;
    }

    .error-panel {
        background: rgba(210, 125, 115, 0.12);
        border-color: rgba(210, 125, 115, 0.22);
        color: #ffd8d0;
    }

    .theme-login .error-panel {
        background: rgba(210, 125, 115, 0.14);
        border-color: rgba(210, 125, 115, 0.22);
        color: #ffe1db;
    }

    form {
        display: grid;
        gap: 14px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px 16px;
    }

    .field-span-2 {
        grid-column: 1 / -1;
    }

    label {
        display: grid;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.01em;
    }

    input,
    select,
    textarea {
        width: 100%;
        border-radius: 18px;
        padding: 13px 14px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: var(--panel-soft);
        color: var(--card-ink);
        font: inherit;
        transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    }

    input::placeholder,
    textarea::placeholder {
        color: #a79d98;
    }

    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: var(--accent-focus-border);
        box-shadow: 0 0 0 4px var(--accent-focus-ring);
    }

    select {
        appearance: none;
        background-image: linear-gradient(45deg, transparent 50%, #d8cbc4 50%), linear-gradient(135deg, #d8cbc4 50%, transparent 50%);
        background-position: calc(100% - 22px) calc(50% - 3px), calc(100% - 14px) calc(50% - 3px);
        background-size: 8px 8px, 8px 8px;
        background-repeat: no-repeat;
        padding-right: 42px;
    }

    .theme-dashboard select option,
    .theme-login select option {
        background: #24243e;
        color: #f8f5ff;
    }

    textarea {
        min-height: 110px;
        resize: vertical;
    }

    input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin: 0;
        accent-color: var(--accent);
        box-shadow: none;
    }

    .checkline {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .button,
    button,
    .small-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px 18px;
        border-radius: 16px;
        border: 1px solid transparent;
        background: var(--accent);
        color: #fff;
        font: inherit;
        font-weight: 700;
        letter-spacing: 0.02em;
        cursor: pointer;
        box-shadow: 0 12px 24px var(--accent-shadow);
        transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease, border-color 0.15s ease;
    }

    .button:hover,
    button:hover,
    .small-button:hover {
        transform: translateY(-1px);
        background: var(--accent-strong);
        box-shadow: 0 14px 28px var(--accent-shadow-strong);
    }

    .button.secondary,
    .panel-link,
    .panel-close,
    .hero-action,
    .inline-add summary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 44px;
        padding: 10px 16px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.03);
        border-color: var(--accent-soft);
        color: var(--card-ink);
        box-shadow: none;
    }

    .panel-link {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.02em;
        white-space: nowrap;
        color: var(--warm);
    }

    .panel-close {
        width: 38px;
        min-width: 38px;
        min-height: 38px;
        padding: 0;
        border-radius: 12px;
    }

    .button.secondary:hover,
    .panel-link:hover,
    .panel-close:hover,
    .hero-action:hover,
    .inline-add summary:hover,
    .inline-add[open] summary {
        background: var(--accent-soft);
        transform: none;
    }

    .login-form-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 14px;
    }

    .login-form-actions .button {
        min-width: 180px;
    }

    .button.danger,
    button.danger,
    .small-button.danger {
        background: linear-gradient(135deg, #8f2d27, #6f201e);
        border-color: rgba(216, 142, 131, 0.28);
        color: #fff6f3;
        box-shadow: 0 12px 24px rgba(111, 32, 30, 0.24);
    }

    .button.danger:hover,
    button.danger:hover,
    .small-button.danger:hover {
        box-shadow: 0 16px 28px rgba(111, 32, 30, 0.3);
    }

    .central-sidebar .button.secondary,
    .tenant-sidebar .button.secondary {
        width: 100%;
        justify-content: center;
    }

    .panel-link.warning {
        background: rgba(231, 190, 113, 0.12);
        border-color: rgba(231, 190, 113, 0.22);
        color: #f4deaa;
    }

    .action-row,
    .section-header,
    .form-panel-header,
    .split-line {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }

    .action-row h2,
    .section-header h2,
    .form-panel-header h3 {
        margin: 0;
    }

    .action-row-actions,
    .actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .form-panel,
    .spotlight-copy,
    .metric-card,
    .mini-panel,
    .page-mini-panel {
        padding: 18px;
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .helper-note {
        padding: 14px 16px;
        border-radius: 18px;
        background: var(--accent-soft);
        border: 1px solid var(--accent-border);
        color: #f1d9df;
    }

    .marketing-shell {
        display: grid;
        gap: 24px;
        padding: 36px 28px 48px;
    }

    .marketing-hero {
        display: grid;
        grid-template-columns: minmax(0, 1.25fr) minmax(280px, 420px);
        gap: 24px;
        align-items: center;
    }

    .marketing-hero h1 {
        margin: 8px 0 0;
        font-size: clamp(36px, 5vw, 58px);
        line-height: 0.98;
    }

    .marketing-hero-copy {
        display: grid;
        gap: 16px;
    }

    .marketing-hero-copy .lead {
        max-width: 760px;
    }

    .marketing-hero-visual {
        display: grid;
        gap: 18px;
    }

    .marketing-showcase-frame {
        min-height: 280px;
    }

    .marketing-stat-grid,
    .benefit-grid,
    .pricing-grid {
        display: grid;
        gap: 18px;
    }

    .marketing-stat-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .benefit-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        margin-top: 18px;
    }

    .pricing-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .plan-showcase-card {
        min-height: 100%;
    }

    .field-with-action {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 10px;
        align-items: center;
    }

    .field-with-action input {
        min-width: 0;
    }

    .tiny-link {
        min-height: 46px;
        padding: 0 14px;
        font-size: 11px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .field-hint {
        display: block;
        margin-top: 4px;
        color: var(--card-muted);
        font-size: 11px;
        line-height: 1.4;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    th {
        text-align: left;
        padding: 14px 0;
        font-size: 10px;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: #cfc3bd;
        border-bottom: 1px solid rgba(255, 255, 255, 0.07);
    }

    td {
        padding: 16px 0;
        vertical-align: top;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .table-link-stack {
        display: grid;
        gap: 6px;
    }

    .table-link-stack a {
        color: #f0d8d1;
        text-decoration: underline;
        text-underline-offset: 3px;
    }

    code {
        display: inline-flex;
        align-items: center;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-family: "Consolas", "Courier New", monospace;
        background: rgba(255, 255, 255, 0.06);
        color: #f3ece6;
    }

    .badge,
    .pill,
    .eyebrow,
    .status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .badge,
    .pill,
    .eyebrow {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #ece5e0;
    }

    .status-pill {
        border: 1px solid transparent;
    }

    .status-pill.active {
        background: rgba(119, 173, 149, 0.16);
        color: #d3f1e2;
        border-color: rgba(119, 173, 149, 0.28);
    }

    .status-pill.suspended,
    .status-pill.expired {
        background: rgba(210, 125, 115, 0.18);
        color: #ffd7d2;
        border-color: rgba(210, 125, 115, 0.3);
    }

    .status-pill.scheduled {
        background: rgba(231, 190, 113, 0.16);
        color: #f6e1b1;
        border-color: rgba(231, 190, 113, 0.26);
    }

    .status-pill.status-active {
        background: rgba(119, 173, 149, 0.16);
        color: #d3f1e2;
        border-color: rgba(119, 173, 149, 0.28);
    }

    .status-pill.status-inactive {
        background: rgba(210, 125, 115, 0.18);
        color: #ffd7d2;
        border-color: rgba(210, 125, 115, 0.3);
    }

    .legend,
    .soft-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 12px;
    }

    .legend li,
    .soft-list li {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .soft-list li {
        padding: 16px 18px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .legend-label {
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .legend-swatch {
        width: 12px;
        height: 12px;
        border-radius: 999px;
    }

    .ring-grid {
        grid-template-columns: repeat(2, minmax(160px, 1fr));
        align-items: center;
    }

    .ring {
        --progress: 72;
        --ring-color: var(--accent);
        width: 148px;
        height: 148px;
        margin: 0 auto;
        border-radius: 50%;
        position: relative;
        background: radial-gradient(circle closest-side, var(--panel) 63%, transparent 64% 100%), conic-gradient(var(--ring-color) calc(var(--progress) * 1%), rgba(255, 255, 255, 0.08) 0);
    }

    .ring-content {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        text-align: center;
    }

    .ring-content strong {
        display: block;
        font-size: 34px;
        line-height: 1;
    }

    .ring-content span {
        display: block;
        margin-top: 6px;
        font-size: 10px;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: #beb4ae;
    }

    .donut {
        width: 206px;
        height: 206px;
        border-radius: 50%;
        position: relative;
        margin: 0 auto;
    }

    .donut::after {
        content: "";
        position: absolute;
        inset: 36px;
        border-radius: 50%;
        background: var(--panel);
    }

    .donut .ring-content {
        z-index: 1;
    }

    .progress-bar {
        height: 10px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.07);
        overflow: hidden;
        margin-top: 12px;
    }

    .progress-fill {
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, var(--accent-strong), var(--accent), var(--warm));
    }

    .small-button {
        padding: 10px 16px;
        border-radius: 14px;
    }

    .login-stage {
        display: grid;
        grid-template-columns: minmax(420px, 0.96fr) minmax(420px, 1.04fr);
        width: 100%;
        min-height: min(840px, calc(100vh - 56px));
        position: relative;
        isolation: isolate;
        background: linear-gradient(180deg, #1d1d33, #151526);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 34px;
        overflow: hidden;
        box-shadow: 0 34px 70px rgba(0, 0, 0, 0.32);
    }

    .login-panel {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 24px;
        padding: clamp(30px, 5vw, 54px);
        margin: 18px 0 18px 18px;
        border-radius: 28px;
        border: 1px solid var(--panel-border);
        border-top: 2px solid var(--accent);
        background: linear-gradient(180deg, rgba(39, 39, 69, 0.98), rgba(31, 31, 56, 0.98));
        box-shadow: 0 24px 52px rgba(0, 0, 0, 0.22);
        color: var(--card-muted);
    }

    .login-panel h1 {
        margin: 0 0 10px;
        font-size: clamp(2.9rem, 4.8vw, 4.5rem);
        line-height: 0.94;
        letter-spacing: -0.06em;
    }

    .login-panel .lead {
        max-width: 520px;
        color: var(--card-muted);
        font-size: 17px;
        line-height: 1.65;
    }

    .login-form {
        max-width: 460px;
        width: 100%;
        margin: 0 auto;
    }

    .theme-login label {
        color: #ede6f5;
    }

    .theme-login input,
    .theme-login select,
    .theme-login textarea {
        background: var(--panel-soft);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: var(--card-ink);
    }

    .theme-login input::placeholder,
    .theme-login textarea::placeholder {
        color: #a8a09b;
    }

    .theme-login input:focus,
    .theme-login select:focus,
    .theme-login textarea:focus {
        border-color: var(--accent-focus-border);
        box-shadow: 0 0 0 4px var(--accent-focus-ring);
    }

    .login-form button {
        width: 100%;
    }

    .login-form .button.secondary {
        width: 100%;
    }

    .login-support {
        max-width: 460px;
        width: 100%;
        margin: 0 auto;
        padding-top: 18px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
    }

    .login-panel-brand,
    .login-panel-copy,
    .role-switch-grid {
        width: 100%;
        max-width: 460px;
        margin: 0 auto;
    }

    .login-panel-brand {
        display: grid;
        justify-items: center;
        gap: 12px;
        text-align: center;
    }

    .login-panel-logo {
        width: 80px;
        height: 80px;
        border-radius: 24px;
        object-fit: cover;
        display: block;
        box-shadow: 0 16px 30px var(--accent-shadow);
    }

    .login-panel-copy {
        text-align: center;
    }

    .login-panel-subtitle {
        margin: 0 0 10px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: var(--warm);
    }

    .login-divider {
        width: min(220px, 100%);
        height: 1px;
        margin: 18px auto 0;
        background: linear-gradient(90deg, transparent, var(--warm-strong), transparent);
    }

    .login-university-tagline {
        margin: 12px 0 0;
        font-size: 11px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--frame-muted);
    }

    .role-switch-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .role-switch-card {
        display: grid;
        gap: 8px;
        padding: 18px;
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        transition: border-color 0.15s ease, background 0.15s ease, transform 0.15s ease;
    }

    .role-switch-card:hover,
    .role-switch-card.active {
        background: var(--accent-soft);
        border-color: var(--accent-active-border);
        transform: translateY(-1px);
    }

    .role-switch-card strong {
        color: var(--card-ink);
    }

    .role-switch-card span {
        color: var(--card-muted);
        font-size: 14px;
        line-height: 1.6;
    }

    .login-art {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 28px;
        margin: 18px 18px 18px 0;
        border-radius: 28px;
        background: linear-gradient(180deg, #252546, #1a1a31);
    }

    .login-preview {
        display: grid;
        grid-template-columns: 72px minmax(0, 1fr);
        gap: 18px;
        width: 100%;
        max-width: 640px;
        min-height: 520px;
    }

    .logo-showcase {
        width: 100%;
        max-width: 620px;
        min-height: 520px;
        display: grid;
        align-content: center;
        justify-items: center;
        gap: 26px;
        padding: 36px;
        border-radius: 30px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.04), rgba(255, 255, 255, 0.02));
        border: 1px solid rgba(255, 255, 255, 0.06);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
        text-align: center;
    }

    .logo-showcase-frame {
        width: min(280px, 70%);
        aspect-ratio: 1 / 1;
        border-radius: 34px;
        padding: 18px;
        background: radial-gradient(circle at top, var(--accent-border), var(--warm-glow) 62%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 22px 50px rgba(0, 0, 0, 0.24);
    }

    .logo-showcase-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.02);
    }

    .logo-showcase-copy {
        max-width: 420px;
        display: grid;
        justify-items: center;
        gap: 12px;
    }

    .logo-showcase-copy h3 {
        margin: 0;
        font-size: 34px;
        line-height: 1.05;
        letter-spacing: -0.05em;
        color: var(--card-ink);
    }

    .logo-showcase-copy p {
        margin: 0;
        font-size: 15px;
        line-height: 1.7;
        color: var(--card-muted);
    }

    .preview-rail {
        padding: 18px 14px;
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.07);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 14px;
    }

    .preview-logo {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--accent), var(--accent-strong));
        color: #fff;
        font-weight: 800;
        letter-spacing: 0.12em;
        overflow: hidden;
        padding: 0;
    }

    .preview-logo-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .preview-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.24);
    }

    .preview-dot.active {
        background: var(--accent);
    }

    .preview-content {
        display: grid;
        gap: 16px;
    }

    .preview-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
    }

    .preview-top h3 {
        margin: 8px 0 0;
        font-size: 26px;
        letter-spacing: -0.04em;
    }

    .preview-top p {
        margin: 8px 0 0;
    }

    .preview-chip {
        display: inline-flex;
        align-items: center;
        padding: 9px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.08);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #dfd5cf;
    }

    .preview-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .preview-widget {
        padding: 18px;
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.07);
    }

    .preview-widget h4 {
        margin: 0 0 6px;
        font-size: 16px;
    }

    .preview-metric {
        margin-top: 10px;
        font-size: 34px;
        font-weight: 800;
        line-height: 1;
        color: var(--card-ink);
    }

    .preview-wide {
        grid-column: 1 / -1;
    }

    .mini-bars {
        height: 72px;
        display: flex;
        align-items: flex-end;
        gap: 9px;
        margin-top: 14px;
    }

    .mini-bars span {
        flex: 1;
        border-radius: 999px 999px 10px 10px;
        background: linear-gradient(180deg, var(--warm), var(--accent));
    }

    .mini-bars span:nth-child(2n) {
        background: linear-gradient(180deg, var(--warm), var(--accent-strong));
    }

    .mini-bars span:nth-child(3n) {
        background: linear-gradient(180deg, var(--warm-soft), var(--accent));
    }

    .preview-ring {
        width: 124px;
        height: 124px;
        margin: 14px auto 0;
        border-radius: 50%;
        position: relative;
        background: radial-gradient(circle closest-side, var(--panel) 63%, transparent 64% 100%), conic-gradient(var(--accent) 0 74%, rgba(255, 255, 255, 0.08) 74% 100%);
    }

    .preview-ring.alt {
        background: radial-gradient(circle closest-side, var(--panel) 63%, transparent 64% 100%), conic-gradient(var(--warm) 0 64%, rgba(255, 255, 255, 0.08) 64% 100%);
    }

    .preview-ring .ring-content strong {
        font-size: 28px;
    }

    .preview-list {
        list-style: none;
        margin: 12px 0 0;
        padding: 0;
        display: grid;
        gap: 10px;
    }

    .preview-list li {
        padding: 12px 14px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .preview-list strong {
        color: var(--card-ink);
    }

    .section-anchor {
        scroll-margin-top: 24px;
    }

    .code-cell {
        font-family: "Consolas", "Courier New", monospace;
        font-size: 13px;
        letter-spacing: 0.06em;
        color: var(--warm);
    }

    .hours-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        background: var(--accent-soft);
        border: 1px solid var(--accent-border);
        color: var(--card-ink);
    }

    .inline-form-panel {
        margin: 16px 0;
        padding: 20px 22px;
        border-radius: 20px;
        background: var(--panel-soft);
        border: 1px solid var(--panel-border);
    }

    .inline-form-title {
        margin: 0 0 16px;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.14em;
        color: var(--card-muted);
    }

    .inline-edit-form {
        margin-top: 14px;
        padding: 16px 18px;
        border-radius: 16px;
        background: var(--panel);
        border: 1px solid var(--panel-border);
    }

    .compact-form label {
        font-size: 12px;
    }

    .empty-state-block {
        padding: 28px 0 8px;
        text-align: center;
    }

    .empty-label {
        color: var(--card-ink);
        font-weight: 700;
    }

    .empty-hint {
        font-size: 12px;
        color: var(--card-muted);
        max-width: 480px;
        margin: 6px auto 0;
        line-height: 1.6;
    }

    .table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .student-section-shell {
        display: grid;
        gap: 22px;
        align-content: start;
    }

    .student-section-head {
        align-items: flex-start;
    }

    .student-application-banner {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-radius: 22px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.04), var(--accent-soft));
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .student-application-banner p {
        margin: 8px 0 0;
        line-height: 1.7;
    }

    .student-dashboard-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr);
        gap: 18px;
        align-items: start;
    }

    .student-dashboard-grid-single {
        grid-template-columns: minmax(0, 1fr);
    }

    .student-subpanel,
    .student-history-panel {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.03), rgba(255, 255, 255, 0.015));
    }

    .student-history-panel {
        padding: 22px;
        border-radius: 26px;
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .student-upload-note {
        display: grid;
        align-content: center;
        gap: 8px;
        min-height: 100%;
        padding: 16px 18px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .student-upload-note p {
        margin: 0;
        line-height: 1.6;
    }

    .student-upload-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px 16px;
    }

    .student-uploaded-documents {
        display: grid;
        gap: 14px;
        padding: 18px;
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .student-uploaded-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px 14px;
    }

    .student-uploaded-item {
        display: grid;
        gap: 8px;
        padding: 14px 16px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .student-uploaded-item strong {
        color: var(--card-ink);
    }

    .student-uploaded-item a {
        color: var(--warm);
        font-weight: 700;
    }

    .student-uploaded-item span {
        color: var(--card-muted);
    }

    .student-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .student-card-head h3 {
        margin: 0;
    }

    .data-table {
        min-width: 760px;
    }

    .row-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 36px;
        padding: 8px 12px;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.04);
        color: var(--card-ink);
        font: inherit;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.02em;
        cursor: pointer;
        box-shadow: none;
    }

    .action-btn:hover {
        background: var(--accent-soft);
        border-color: var(--accent-active-border);
        transform: none;
        box-shadow: none;
    }

    .color-field-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .color-input {
        width: 58px;
        min-width: 58px;
        height: 46px;
        padding: 6px;
        border-radius: 14px;
        cursor: pointer;
    }

    .color-code {
        display: inline-flex;
        align-items: center;
        min-height: 46px;
        padding: 0 14px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: var(--card-ink);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .branding-preview-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 18px;
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .branding-preview-copy {
        display: grid;
        gap: 10px;
    }

    .branding-swatch-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .branding-swatch {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: var(--card-muted);
        font-size: 12px;
    }

    .branding-swatch-sample {
        width: 16px;
        height: 16px;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.14);
        background: var(--swatch-color, var(--accent));
        flex-shrink: 0;
    }

    .branding-logo-preview,
    .branding-logo-fallback {
        width: 86px;
        height: 86px;
        border-radius: 24px;
        flex-shrink: 0;
    }

    .branding-logo-preview {
        object-fit: cover;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.03);
    }

    .branding-logo-fallback {
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(145deg, var(--accent-strong), var(--accent));
        border: 1px solid var(--accent-active-border);
        color: #fff;
        font-size: 26px;
        font-weight: 800;
        letter-spacing: 0.12em;
        box-shadow: 0 16px 30px var(--accent-shadow);
    }

    .danger-btn {
        color: #ffd7d2;
        border-color: rgba(210, 125, 115, 0.24);
    }

    .muted-cell {
        color: var(--card-muted);
    }

    .hours-cell {
        white-space: nowrap;
    }

    .select-input {
        cursor: pointer;
    }

    .textarea-input {
        width: 100%;
        padding: 10px 14px;
        border-radius: 12px;
        background: var(--panel-soft);
        border: 1px solid var(--panel-border);
        color: var(--card-ink);
        font-size: 13px;
        resize: vertical;
        font-family: inherit;
        line-height: 1.5;
    }

    .section-hint {
        margin: 8px 0 0;
        font-size: 13px;
        color: var(--card-muted);
        line-height: 1.6;
    }

    .rbac-toolbar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
    }

    .rbac-toolbar-copy {
        min-width: 0;
    }

    .rbac-toolbar-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .rbac-toolbar-actions form {
        display: block;
    }

    .rbac-table {
        min-width: 860px;
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid rgba(255, 255, 255, 0.07);
        border-radius: 24px;
        overflow: hidden;
    }

    .rbac-table th,
    .rbac-table td {
        padding: 16px 14px;
        text-align: center;
        vertical-align: middle;
    }

    .rbac-table thead th {
        background: rgba(255, 255, 255, 0.04);
        white-space: nowrap;
    }

    .rbac-table tbody td {
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    .rbac-table tbody tr:hover td {
        background: rgba(255, 255, 255, 0.03);
    }

    .rbac-table th:first-child,
    .rbac-table td:first-child {
        min-width: 220px;
        text-align: left;
    }

    .rbac-permission-label {
        display: block;
        font-size: 18px;
        line-height: 1.1;
        text-transform: lowercase;
    }

    .rbac-permission-subject {
        display: block;
        margin-top: 4px;
        font-size: 13px;
        color: var(--card-muted);
        text-transform: lowercase;
    }

    .rbac-check-cell {
        min-width: 120px;
    }

    .rbac-checkbox {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
    }

    .rbac-checkbox input[type="checkbox"] {
        width: 20px;
        height: 20px;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        font-size: 14px;
        color: var(--card-ink);
    }

    .checkbox-group-card {
        margin: 0;
        padding: 16px 18px 18px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .checkbox-group-card legend {
        padding: 0 8px;
        font-size: 13px;
        font-weight: 700;
        color: var(--card-ink);
    }

    .checkbox-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px 16px;
        margin-top: 4px;
    }

    .checkbox-label input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: var(--accent);
        flex-shrink: 0;
    }

    .profile-summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px 16px;
        margin-top: 16px;
    }

    .summary-item {
        min-width: 0;
        padding: 16px 18px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .summary-label {
        display: block;
        margin-bottom: 10px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: #d8c7e4;
    }

    .summary-value {
        display: block;
        color: var(--card-ink);
        font-size: 15px;
        line-height: 1.6;
        word-break: break-word;
    }

    .summary-note {
        margin: 0;
        color: var(--card-muted);
        line-height: 1.7;
    }

    .summary-callout {
        padding: 18px;
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .summary-callout strong {
        display: block;
        color: var(--card-ink);
        margin-bottom: 8px;
    }

    .summary-callout p {
        margin: 0;
        color: var(--card-muted);
        line-height: 1.7;
    }

    body.modal-open {
        overflow: hidden;
    }

    .modal-shell[hidden] {
        display: none !important;
    }

    .modal-shell {
        position: fixed;
        inset: 0;
        z-index: 1400;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background: rgba(8, 8, 18, 0.74);
        backdrop-filter: blur(12px);
    }

    .modal-card {
        width: min(720px, 100%);
        max-height: calc(100vh - 48px);
        overflow: auto;
        padding: 24px;
        border-radius: 30px;
        background: linear-gradient(180deg, var(--shell), var(--panel));
        border: 1px solid var(--panel-border);
        box-shadow: var(--shadow);
    }

    .modal-card-wide {
        width: min(860px, 100%);
    }

    .modal-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .modal-head h3 {
        margin: 0;
        color: var(--card-ink);
        font-size: 24px;
        line-height: 1.1;
    }

    .modal-head .panel-close {
        font-size: 24px;
        line-height: 1;
    }

    .modal-copy {
        margin: 8px 0 0;
        color: var(--card-muted);
        font-size: 13px;
        line-height: 1.6;
    }

    .modal-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .central-admin-theme {
        --central-bg: #0d1730;
        --central-panel: rgba(21, 34, 65, 0.9);
        --central-border: rgba(120, 144, 196, 0.16);
        --central-ink: #f7faff;
        --central-muted: #9cb0d7;
        --central-primary: #2954e8;
        --central-primary-soft: rgba(41, 84, 232, 0.16);
        background:
            radial-gradient(circle at top left, rgba(41, 84, 232, 0.22), transparent 26%),
            radial-gradient(circle at bottom right, rgba(58, 94, 199, 0.18), transparent 32%),
            linear-gradient(180deg, #0a1429 0%, var(--central-bg) 56%, #0c1427 100%);
    }

    .console-shell {
        display: grid;
        grid-template-columns: 244px minmax(0, 1fr);
        gap: 0;
        min-height: calc(100vh - 36px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 26px;
        overflow: hidden;
        background: rgba(10, 18, 36, 0.94);
        box-shadow: 0 24px 48px rgba(2, 8, 24, 0.42);
    }

    .console-sidebar {
        display: grid;
        grid-template-rows: auto 1fr auto;
        gap: 16px;
        padding: 18px 0 12px;
        background: #0b1427;
        border-right: 1px solid rgba(255, 255, 255, 0.06);
    }

    .console-brand,
    .console-topbar,
    .console-content {
        padding-left: 18px;
        padding-right: 18px;
    }

    .console-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .console-brand-mark {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 56px;
        height: 56px;
        border-radius: 12px;
        background: transparent;
        flex-shrink: 0;
    }

    .console-brand-title {
        display: block;
        color: #f7faff;
        font-size: 20px;
        line-height: 1.1;
    }

    .console-brand-subtitle {
        display: block;
        margin-top: 4px;
        color: var(--central-muted);
        font-size: 13px;
    }

    .console-nav {
        display: grid;
        gap: 8px;
        padding: 0 8px;
    }

    .console-nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 14px;
        color: #dce5fb;
        border: 1px solid transparent;
        transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease;
        text-decoration: none;
    }

    .nav-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        font-size: 16px;
        flex-shrink: 0;
    }

    .nav-label {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
        flex: 1;
    }

    .nav-label > span:first-child {
        font-size: 13px;
        font-weight: 700;
        color: #dce5fb;
    }

    .nav-label > span:last-child {
        color: var(--central-muted);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.12em;
    }

    .console-nav-link:hover,
    .console-nav-link.active {
        background: rgba(41, 84, 232, 0.16);
        border-color: rgba(75, 110, 225, 0.28);
        color: #f7faff;
    }

    .console-nav-link.active .nav-icon {
        color: #4b6ee1;
    }

    .console-sidebar-footer {
        margin-top: auto;
        padding: 14px 18px 0;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    .console-logout-button {
        width: 100%;
        min-height: 42px;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.04);
        color: #f7faff;
        font: inherit;
        font-weight: 700;
        cursor: pointer;
    }

    .console-main {
        min-width: 0;
        display: grid;
        grid-template-rows: auto minmax(0, 1fr);
        background:
            radial-gradient(circle at top right, rgba(41, 84, 232, 0.14), transparent 26%),
            linear-gradient(180deg, #101a33 0%, #121d38 100%);
    }

    .console-topbar {
        min-height: 72px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 0 24px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .console-topbar-left,
    .console-topbar-user {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .console-topbar-left strong,
    .console-topbar-user strong {
        display: block;
        color: #f7faff;
        font-size: 15px;
    }

    .console-topbar-left span,
    .console-topbar-user span {
        color: var(--central-muted);
        font-size: 12px;
    }

    .console-topbar-back {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        color: #dce5fb;
        background: rgba(255, 255, 255, 0.04);
        font-size: 20px;
    }

    .console-avatar {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        background: rgba(41, 84, 232, 0.22);
        color: #fff;
        font-size: 13px;
        font-weight: 800;
        flex-shrink: 0;
    }

    .console-content {
        padding: 18px 24px 24px;
        display: grid;
        gap: 18px;
        align-content: start;
    }

    .central-shell {
        display: grid;
        grid-template-columns: 300px minmax(0, 1fr);
        gap: 24px;
        align-items: start;
    }

    .central-sidebar-v2 {
        position: sticky;
        top: 18px;
        display: grid;
        gap: 18px;
        min-height: calc(100vh - 36px);
        padding: 22px;
        border-radius: 28px;
        background: linear-gradient(180deg, rgba(13, 23, 48, 0.96), rgba(17, 29, 57, 0.94));
        border: 1px solid var(--central-border);
        box-shadow: 0 24px 48px rgba(2, 8, 24, 0.42);
    }

    .central-brand-panel-v2 {
        padding: 0 0 18px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .central-brand-v2 {
        align-items: center;
    }

    .central-brand-mark-v2 {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        background: linear-gradient(145deg, #1c3ca8, var(--central-primary));
        box-shadow: 0 16px 28px rgba(32, 69, 190, 0.28);
    }

    .central-side-group {
        display: grid;
        gap: 10px;
    }

    .central-side-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: rgba(156, 176, 215, 0.74);
    }

    .central-sidebar-nav {
        gap: 12px;
    }

    .central-sidebar-link {
        display: grid;
        justify-content: start;
        gap: 3px;
        min-height: 68px;
        padding: 16px 18px 16px 42px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid transparent;
    }

    .central-sidebar-link span,
    .central-sidebar-link small {
        display: block;
    }

    .central-sidebar-link span {
        font-size: 13px;
        letter-spacing: 0.02em;
        text-transform: none;
        color: #f4f7fd;
    }

    .central-sidebar-link small {
        font-size: 11px;
        color: var(--central-muted);
    }

    .central-sidebar-link.active {
        background: linear-gradient(180deg, rgba(41, 84, 232, 0.22), rgba(35, 56, 104, 0.32));
        border-color: rgba(75, 110, 225, 0.3);
    }

    .central-side-card {
        padding: 16px 18px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.05);
        display: grid;
        gap: 14px;
    }

    .central-side-note {
        margin: 0;
        color: var(--central-muted);
        line-height: 1.7;
        font-size: 13px;
    }

    .central-side-actions {
        margin-top: auto;
    }

    .central-main-v2 {
        min-width: 0;
        display: grid;
        gap: 18px;
        padding-top: 4px;
    }

    .admin-hero,
    .admin-glass-card,
    .admin-stat-card,
    .admin-mini-card,
    .admin-hero-panel,
    .admin-toolbar-note {
        border: 1px solid var(--central-border);
        background: var(--central-panel);
        backdrop-filter: blur(14px);
        box-shadow: 0 18px 40px rgba(4, 10, 28, 0.28);
    }

    .admin-hero {
        display: grid;
        grid-template-columns: minmax(0, 1.25fr) minmax(320px, 0.85fr);
        gap: 22px;
        padding: 28px;
        border-radius: 28px;
        background:
            radial-gradient(circle at top right, rgba(41, 84, 232, 0.28), transparent 30%),
            linear-gradient(180deg, rgba(18, 31, 62, 0.98), rgba(15, 24, 47, 0.94));
    }

    .admin-eyebrow {
        display: inline-flex;
        align-items: center;
        min-height: 28px;
        padding: 0 12px;
        border-radius: 999px;
        background: var(--central-primary-soft);
        color: #aecdff;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .admin-hero h1,
    .admin-section-head h2 {
        margin: 14px 0 0;
        color: var(--central-ink);
        letter-spacing: -0.04em;
    }

    .admin-hero h1 {
        font-size: clamp(34px, 4vw, 52px);
        line-height: 1.02;
        max-width: 12ch;
    }

    .admin-hero p {
        margin: 14px 0 0;
        color: var(--central-muted);
        line-height: 1.7;
        font-size: 15px;
    }

    .admin-action-row {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 22px;
    }

    .admin-hero-metrics {
        display: grid;
        gap: 14px;
    }

    .admin-hero-panel {
        padding: 18px 20px;
        border-radius: 22px;
    }

    .admin-hero-panel span,
    .admin-stat-card span {
        display: block;
        font-size: 12px;
        color: var(--central-muted);
    }

    .admin-hero-panel strong,
    .admin-stat-card strong {
        display: block;
        margin-top: 10px;
        font-size: 30px;
        color: var(--central-ink);
    }

    .admin-hero-panel small,
    .admin-stat-card small {
        display: block;
        margin-top: 8px;
        color: var(--central-muted);
        line-height: 1.6;
    }

    .admin-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .admin-tabset {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .admin-tab {
        display: inline-flex;
        align-items: center;
        min-height: 42px;
        padding: 0 16px;
        border-radius: 14px;
        border: 1px solid var(--central-border);
        background: rgba(255, 255, 255, 0.03);
        color: var(--central-muted);
        font-size: 13px;
        font-weight: 700;
    }

    .admin-tab.active {
        background: var(--central-primary-soft);
        color: #f8fbff;
        border-color: rgba(75, 110, 225, 0.34);
    }

    .admin-toolbar-note {
        padding: 12px 16px;
        border-radius: 16px;
        text-align: right;
    }

    .admin-toolbar-note span {
        display: block;
        font-size: 11px;
        color: var(--central-muted);
        text-transform: uppercase;
        letter-spacing: 0.14em;
    }

    .admin-toolbar-note strong {
        display: block;
        margin-top: 6px;
        color: var(--central-ink);
        font-size: 20px;
    }

    .admin-kpi-grid,
    .admin-grid-2 {
        display: grid;
        gap: 18px;
    }

    .admin-kpi-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .admin-grid-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .admin-grid-2.compact {
        gap: 14px;
    }

    .admin-stat-card,
    .admin-mini-card,
    .admin-glass-card {
        border-radius: 24px;
        padding: 22px;
    }

    .admin-section-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .admin-section-head h2 {
        font-size: 28px;
        line-height: 1.08;
    }

    .admin-mini-card strong {
        color: var(--central-ink);
        display: block;
        margin-bottom: 12px;
    }

    .admin-table thead th,
    .admin-table tbody td {
        border-color: rgba(255, 255, 255, 0.05);
    }

    .bandwidth-stack {
        display: grid;
        gap: 14px;
    }

    .bandwidth-item {
        padding: 16px 18px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .bandwidth-item-head,
    .bandwidth-item-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }

    .bandwidth-item-head strong,
    .bandwidth-item-meta strong {
        color: var(--central-ink);
    }

    .bandwidth-item-head small,
    .bandwidth-item-meta small,
    .bandwidth-item-meta-row span {
        color: var(--central-muted);
    }

    .bandwidth-item-meta {
        text-align: right;
    }

    .bandwidth-item-meta-row {
        margin-top: 12px;
        font-size: 12px;
    }

    .bandwidth-meter {
        width: 100%;
        height: 10px;
        margin-top: 14px;
        overflow: hidden;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.06);
    }

    .bandwidth-meter.slim {
        height: 8px;
    }

    .bandwidth-meter span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #3c72ff 0%, #2954e8 100%);
        box-shadow: 0 10px 24px rgba(41, 84, 232, 0.28);
    }

    .bandwidth-chart-card {
        padding: 18px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.05);
        margin-bottom: 16px;
    }

    .bandwidth-chart-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
    }

    .bandwidth-chart-head strong {
        display: block;
        color: var(--central-ink);
        font-size: 28px;
    }

    .bandwidth-chart-head small {
        color: var(--central-muted);
    }

    .bandwidth-chart-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        color: var(--central-muted);
        font-size: 12px;
    }

    .bandwidth-chart-legend span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .bandwidth-legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        display: inline-block;
    }

    .bandwidth-legend-dot.used {
        background: #3c72ff;
    }

    .bandwidth-legend-dot.limit {
        background: rgba(130, 154, 211, 0.35);
    }

    .bandwidth-bars {
        height: 240px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(72px, 1fr));
        gap: 16px;
        align-items: end;
    }

    .bandwidth-bar-group {
        display: grid;
        justify-items: center;
        gap: 8px;
    }

    .bandwidth-bar-track {
        width: 42px;
        height: 190px;
        position: relative;
        display: flex;
        align-items: flex-end;
        justify-content: center;
    }

    .bandwidth-bar-limit,
    .bandwidth-bar-used {
        position: absolute;
        bottom: 0;
        width: 100%;
        border-radius: 18px 18px 10px 10px;
    }

    .bandwidth-bar-limit {
        background: rgba(130, 154, 211, 0.22);
        border: 1px solid rgba(130, 154, 211, 0.12);
    }

    .bandwidth-bar-used {
        width: 28px;
        background: linear-gradient(180deg, #4b79ff 0%, #2954e8 100%);
        box-shadow: 0 12px 24px rgba(41, 84, 232, 0.26);
    }

    .bandwidth-bar-group strong {
        color: var(--central-ink);
        font-size: 12px;
    }

    .bandwidth-bar-group small {
        color: var(--central-muted);
        font-size: 11px;
    }

    .activity-feed {
        display: grid;
        gap: 16px;
    }

    .activity-item {
        display: grid;
        grid-template-columns: 12px minmax(0, 1fr);
        gap: 12px;
        align-items: start;
    }

    .activity-dot {
        width: 8px;
        height: 8px;
        margin-top: 6px;
        border-radius: 999px;
        background: #3c72ff;
        box-shadow: 0 0 0 6px rgba(60, 114, 255, 0.12);
    }

    .activity-item strong {
        display: block;
        color: var(--central-ink);
    }

    .activity-item small {
        display: block;
        margin-top: 4px;
        color: var(--central-muted);
    }

    .lovable-page-header h1 {
        margin: 0;
        color: var(--central-ink);
        font-size: 42px;
        line-height: 1.08;
        letter-spacing: -0.04em;
    }

    .lovable-page-header p {
        margin: 8px 0 0;
        color: var(--central-muted);
    }

    .lovable-metric-grid,
    .lovable-grid-2 {
        display: grid;
        gap: 14px;
    }

    .lovable-metric-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .lovable-grid-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .lovable-metric-card {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 22px;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.06);
        background: rgba(23, 35, 67, 0.96);
    }

    .lovable-metric-card span {
        display: block;
        color: var(--central-muted);
        font-size: 14px;
    }

    .lovable-metric-card strong {
        display: block;
        margin-top: 10px;
        color: var(--central-ink);
        font-size: 40px;
        line-height: 1;
    }

    .lovable-metric-card small {
        display: block;
        margin-top: 10px;
        color: var(--central-muted);
        font-size: 13px;
    }

    .lovable-metric-icon {
        width: 48px;
        height: 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: rgba(41, 84, 232, 0.18);
        color: #7ea2ff;
        font-size: 18px;
        font-weight: 800;
        flex-shrink: 0;
    }

    .lovable-landing {
        display: grid;
        gap: 22px;
        width: 100%;
    }

    .lovable-landing-hero {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(320px, 0.8fr);
        gap: 18px;
        padding: 28px;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.06);
        background: rgba(18, 30, 58, 0.96);
    }

    .lovable-landing-copy h1 {
        margin: 16px 0 0;
        color: var(--central-ink);
        font-size: clamp(38px, 5vw, 64px);
        line-height: 0.98;
        letter-spacing: -0.05em;
        max-width: 11ch;
    }

    .lovable-landing-copy p {
        margin: 16px 0 0;
        color: var(--central-muted);
        line-height: 1.7;
        max-width: 720px;
    }

    .lovable-landing-stats {
        display: grid;
        gap: 14px;
    }

    .lovable-auth-shell {
        width: 100%;
        display: flex;
        justify-content: center;
    }

    .lovable-auth-wide {
        justify-content: stretch;
    }

    .lovable-auth-card {
        width: min(420px, 100%);
        padding: 28px 24px;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.06);
        background: rgba(18, 30, 58, 0.96);
        box-shadow: 0 18px 40px rgba(4, 10, 28, 0.28);
    }

    .lovable-auth-card-wide {
        width: min(760px, 100%);
    }

    .lovable-auth-brand {
        display: grid;
        justify-items: center;
        text-align: center;
        gap: 10px;
        margin-bottom: 22px;
    }

    .lovable-auth-brand h1 {
        margin: 0;
        color: var(--central-ink);
        font-size: 38px;
        line-height: 1.05;
        letter-spacing: -0.04em;
    }

    .lovable-auth-brand p {
        margin: 0;
        color: var(--central-muted);
    }

    .lovable-auth-form {
        gap: 16px;
    }

    .lovable-auth-note {
        margin-top: 18px;
        padding: 16px 18px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .lovable-auth-note strong {
        display: block;
        color: var(--central-ink);
    }

    .lovable-auth-note p {
        margin: 8px 0 0;
        color: var(--central-muted);
        line-height: 1.6;
    }

    @media (max-width: 1180px) {
        .console-shell {
            grid-template-columns: 1fr;
        }

        .console-sidebar {
            border-right: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .workspace-shell {
            grid-template-columns: 1fr;
        }

        .central-shell,
        .admin-hero,
        .admin-kpi-grid,
        .admin-grid-2,
        .lovable-metric-grid,
        .lovable-grid-2,
        .lovable-landing-hero {
            grid-template-columns: 1fr;
        }

        .central-sidebar,
        .tenant-sidebar {
            position: static;
            min-height: auto;
        }

        .central-sidebar-v2 {
            position: static;
            min-height: auto;
        }

        .sidebar-nav,
        .console-nav {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }
    }

    @media (max-width: 960px) {
        .login-stage,
        .preview-grid,
        .form-grid,
        .chart-grid,
        .two-column-layout,
        .profile-summary-grid,
        .role-switch-grid,
        .marketing-hero,
        .benefit-grid,
        .pricing-grid,
        .marketing-stat-grid {
            grid-template-columns: 1fr;
        }

        .field-with-action {
            grid-template-columns: 1fr;
        }

        .branding-preview-card {
            flex-direction: column;
            align-items: flex-start;
        }

        .student-dashboard-grid {
            grid-template-columns: 1fr;
        }

        .student-upload-grid {
            grid-template-columns: 1fr;
        }

        .student-uploaded-grid {
            grid-template-columns: 1fr;
        }

        .checkbox-grid {
            grid-template-columns: 1fr;
        }

        .student-application-banner {
            flex-direction: column;
        }

        .login-art {
            order: -1;
        }

        .login-panel,
        .login-art {
            margin: 16px;
        }

        .field-span-2 {
            grid-column: auto;
        }

        .rbac-toolbar {
            flex-direction: column;
        }

        .admin-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .console-topbar {
            flex-direction: column;
            align-items: flex-start;
            padding-top: 14px;
            padding-bottom: 14px;
        }

        .bandwidth-item-head,
        .bandwidth-item-meta-row {
            flex-direction: column;
            align-items: flex-start;
        }

        .bandwidth-item-meta {
            text-align: left;
        }
    }

    @media (max-width: 760px) {
        .page-head {
            flex-direction: column;
        }

        .page-mini-stats {
            width: 100%;
        }

        .shell {
            width: calc(100% - 20px);
            margin-left: 10px;
            padding-right: 10px;
        }

        .shell-login {
            width: min(100% - 20px, 100%);
        }

        .marketing-shell {
            padding: 22px 14px 34px;
        }

        .login-panel,
        .login-art {
            padding: 22px;
            margin: 12px;
        }

        .modal-shell {
            padding: 14px;
        }

        .modal-card {
            padding: 20px;
            max-height: calc(100vh - 28px);
        }

        .login-preview {
            grid-template-columns: 1fr;
        }

        .logo-showcase {
            min-height: auto;
            padding: 28px 22px;
        }

        .preview-rail {
            display: none;
        }
    }
</style>
<script>
    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('[data-generate-password]');

        if (! trigger) {
            return;
        }

        const target = document.querySelector(trigger.getAttribute('data-target'));

        if (! target) {
            return;
        }

        const alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%';
        let password = '';

        for (let index = 0; index < 14; index += 1) {
            password += alphabet[Math.floor(Math.random() * alphabet.length)];
        }

        target.value = password;
        target.dispatchEvent(new Event('input', { bubbles: true }));
    });
</script>
