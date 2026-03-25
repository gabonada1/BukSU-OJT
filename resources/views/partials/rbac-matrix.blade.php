@php
    $oldPermissions = old('permissions', []);
@endphp

<section class="content-stack">
    <section class="page-head">
        <div>
            <h1>{{ $title }}</h1>
            <p>{{ $subtitle }}</p>
        </div>

        <div class="page-mini-stats">
            <div class="page-mini-card">
                <strong>Permissions</strong>
                <span>{{ count($definitions) }}</span>
            </div>
            <div class="page-mini-card">
                <strong>Roles</strong>
                <span>{{ count($roles) }}</span>
            </div>
        </div>
    </section>

    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="error-panel">
            <strong>Role permission changes were not saved.</strong>
            <ul style="margin:8px 0 0;padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (isset($persistenceReady) && ! $persistenceReady)
        <div class="error-panel">
            <strong>Database update required.</strong>
            <p style="margin:8px 0 0;">Run <code>php artisan migrate</code> to add the missing RBAC settings column for central superadmin persistence. Default permissions are shown below and still usable as reference.</p>
        </div>
    @endif

    <article class="section-card">
        <div class="rbac-toolbar">
            <div class="rbac-toolbar-copy">
                <h2>Role Permissions</h2>
                <p class="section-hint">{{ $description }}</p>
            </div>
            <div class="rbac-toolbar-actions">
                <form method="POST" action="{{ $resetAction }}">
                    @csrf
                    <button type="submit" class="button secondary">Reset Defaults</button>
                </form>
                <button type="submit" form="rbac-matrix-form">Save Changes</button>
            </div>
        </div>

        <form id="rbac-matrix-form" method="POST" action="{{ $saveAction }}" style="margin-top:18px;">
            @csrf
            <div class="table-wrap">
                <table class="data-table rbac-table">
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
                                <td>
                                    <strong class="rbac-permission-label">{{ $definition['label'] }}</strong>
                                    <small class="rbac-permission-subject">{{ $definition['subject'] }}</small>
                                </td>
                                @foreach ($roles as $roleKey => $roleLabel)
                                    <td class="rbac-check-cell">
                                        <input type="hidden" name="permissions[{{ $permissionKey }}][{{ $roleKey }}]" value="0">
                                        <label class="rbac-checkbox">
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
