@php
    $oldPermissions = old('permissions', []);
@endphp

<section class="section-card rbac-shell">
    <section class="section-header rbac-header">
        <div class="rbac-header-copy">
            <h1>{{ $title }}</h1>
            <p>{{ $subtitle }}</p>
            <span class="metric-pill rbac-header-pill">Changes apply immediately for tenant roles.</span>
        </div>
    </section>

    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="error-panel">
            <strong>Role permission changes were not saved.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (isset($persistenceReady) && ! $persistenceReady)
        <div class="helper-note">
            <strong>Database update required.</strong>
            <p>Run <code>php artisan migrate</code> to add the missing RBAC settings column for central superadmin persistence. Default permissions are shown below and still usable as reference.</p>
        </div>
    @endif

    <article class="dashboard-card dashboard-table-card rbac-card">
        <div class="table-header rbac-table-header">
            <div class="rbac-table-copy">
                <h2>Role Permissions</h2>
                <p>{{ $description }}</p>
            </div>
            <div class="hero-actions rbac-actions">
                <form method="POST" action="{{ $resetAction }}">
                    @csrf
                    <button type="submit" class="button secondary">Reset Defaults</button>
                </form>
                <button type="submit" form="rbac-matrix-form">Save Changes</button>
            </div>
        </div>

        <form id="rbac-matrix-form" method="POST" action="{{ $saveAction }}">
            @csrf
            <div class="table-wrap rbac-table-wrap">
                <table class="rbac-table">
                    <thead>
                        <tr>
                            <th>Permission</th>
                            @foreach ($roles as $roleKey => $roleLabel)
                                <th>{{ $roleLabel }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($definitions as $permissionKey => $definition)
                            <tr>
                                <td class="rbac-permission-cell">
                                    <strong>{{ ucfirst($definition['label']) }}</strong>
                                    <small>{{ ucfirst($definition['subject']) }}</small>
                                </td>
                                @foreach ($roles as $roleKey => $roleLabel)
                                    <td class="rbac-toggle-cell">
                                        <input type="hidden" name="permissions[{{ $permissionKey }}][{{ $roleKey }}]" value="0">
                                        <label class="checkline rbac-toggle">
                                            @php
                                                $checked = isset($oldPermissions[$permissionKey][$roleKey])
                                                    ? (bool) $oldPermissions[$permissionKey][$roleKey]
                                                    : (bool) ($matrix[$permissionKey][$roleKey] ?? false);
                                            @endphp
                                            <input
                                                type="checkbox"
                                                name="permissions[{{ $permissionKey }}][{{ $roleKey }}]"
                                                value="1"
                                                @checked($checked)
                                            >
                                        </label>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </article>
</section>
