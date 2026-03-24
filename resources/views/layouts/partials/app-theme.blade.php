<style>
    :root {
        --page: #101114;
        --page-alt: #18191e;
        --frame-ink: #f4efeb;
        --frame-muted: #b7afaa;
        --shell: #202228;
        --panel: #2b2e36;
        --panel-soft: #353942;
        --panel-border: rgba(255, 255, 255, 0.08);
        --card-ink: #f6f1ed;
        --card-muted: #b9b0aa;
        --soft-light: rgba(255, 255, 255, 0.05);
        --soft-line: rgba(255, 255, 255, 0.08);
        --accent: #c86b61;
        --accent-strong: #a74c46;
        --accent-soft: rgba(200, 107, 97, 0.16);
        --warm: #d6b36a;
        --warm-soft: rgba(214, 179, 106, 0.16);
        --success: #8fb8a0;
        --danger: #d88e83;
        --shadow: 0 24px 52px rgba(0, 0, 0, 0.24);
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
        background: radial-gradient(circle at top center, rgba(200, 107, 97, 0.08), transparent 24%), linear-gradient(180deg, var(--page) 0%, var(--page-alt) 100%);
        color: var(--frame-ink);
    }

    body.theme-login {
        background: radial-gradient(circle at top left, rgba(200, 107, 97, 0.1) 0%, transparent 28%), linear-gradient(180deg, #0d0e12 0%, var(--page) 54%, var(--page-alt) 100%);
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
        background: linear-gradient(180deg, var(--shell), #191b20);
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
        box-shadow: 0 14px 28px rgba(185, 75, 61, 0.24);
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
        color: #d9c9c2;
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
        background: linear-gradient(135deg, rgba(200, 107, 97, 0.24), rgba(214, 179, 106, 0.1));
        border-color: rgba(200, 107, 97, 0.34);
        color: #fff;
    }

    .sidebar-link.active::before {
        background: #fff;
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
        background: linear-gradient(180deg, var(--shell), #1b1d22);
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: var(--shadow);
    }

    .page-mini-card strong {
        display: block;
        font-size: 10px;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: #d9c9c2;
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
        border-color: rgba(200, 107, 97, 0.48);
        box-shadow: 0 0 0 4px rgba(200, 107, 97, 0.14);
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
        background: #24262d;
        color: #fff6f2;
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
        background: linear-gradient(135deg, var(--accent), var(--accent-strong));
        color: #fff;
        font: inherit;
        font-weight: 700;
        letter-spacing: 0.02em;
        cursor: pointer;
        box-shadow: 0 12px 24px rgba(167, 76, 70, 0.2);
        transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    }

    .button:hover,
    button:hover,
    .small-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 28px rgba(167, 76, 70, 0.24);
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
        border-color: rgba(255, 255, 255, 0.08);
        color: var(--card-ink);
        box-shadow: none;
    }

    .panel-link {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.02em;
        white-space: nowrap;
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
        background: rgba(255, 255, 255, 0.07);
        transform: none;
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
        background: rgba(207, 97, 80, 0.12);
        border: 1px solid rgba(207, 97, 80, 0.18);
        color: #f3d2cc;
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
        margin: -2px 0 0;
        color: var(--card-muted);
        font-size: 12px;
        line-height: 1.5;
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
        grid-template-columns: minmax(360px, 0.92fr) minmax(400px, 1.08fr);
        width: 100%;
        min-height: min(840px, calc(100vh - 56px));
        background: linear-gradient(180deg, #26292f, #1c1e24);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 34px;
        overflow: hidden;
        box-shadow: 0 34px 70px rgba(0, 0, 0, 0.32);
    }

    .login-panel {
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 22px;
        padding: clamp(30px, 5vw, 54px);
        background: linear-gradient(180deg, #202329, #1b1e23);
        color: var(--card-muted);
    }

    .login-panel h1 {
        margin: 0 0 10px;
        font-size: clamp(2.8rem, 5vw, 4.4rem);
        line-height: 0.92;
        letter-spacing: -0.06em;
    }

    .login-panel .lead {
        max-width: 480px;
        color: var(--card-muted);
        font-size: 17px;
        line-height: 1.65;
    }

    .login-form {
        max-width: 420px;
    }

    .theme-login label {
        color: #e9ddd7;
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
        border-color: rgba(200, 107, 97, 0.44);
        box-shadow: 0 0 0 4px rgba(200, 107, 97, 0.12);
    }

    .login-form button {
        width: 100%;
    }

    .login-support {
        max-width: 430px;
        padding-top: 18px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
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
        background: rgba(200, 107, 97, 0.12);
        border-color: rgba(200, 107, 97, 0.26);
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
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 28px;
        background: linear-gradient(180deg, #2b2e35, #21242a);
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
        background: radial-gradient(circle at top, rgba(200, 107, 97, 0.18), rgba(255, 255, 255, 0.03) 62%);
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
        background: linear-gradient(180deg, #f2d397, #de8d69);
    }

    .mini-bars span:nth-child(3n) {
        background: linear-gradient(180deg, #f5b8aa, #cf6150);
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

    @media (max-width: 1180px) {
        .workspace-shell {
            grid-template-columns: 1fr;
        }

        .central-sidebar,
        .tenant-sidebar {
            position: static;
            min-height: auto;
        }

        .sidebar-nav {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }
    }

    @media (max-width: 960px) {
        .login-stage,
        .preview-grid,
        .form-grid,
        .chart-grid,
        .two-column-layout,
        .role-switch-grid {
            grid-template-columns: 1fr;
        }

        .field-with-action {
            grid-template-columns: 1fr;
        }

        .login-art {
            order: -1;
        }

        .field-span-2 {
            grid-column: auto;
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

        .login-panel,
        .login-art {
            padding: 22px;
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
