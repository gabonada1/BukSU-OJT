@php
    $embedded = $embedded ?? false;
    $showHeading = $showHeading ?? true;
    $userRecord = $editingUser ?? null;
    $model = $userRecord['model'] ?? null;
    $action = filled($userRecord)
        ? (request()->routeIs('tenant.domain.*')
            ? route('tenant.domain.admin.users.update', ['type' => $userRecord['type'], 'id' => $userRecord['id']])
            : route('tenant.admin.users.update', ['tenant' => $tenant, 'type' => $userRecord['type'], 'id' => $userRecord['id']]))
        : '#';
@endphp

@unless ($embedded)
<article class="card">
@endunless
    @if ($showHeading)
        <h2>Edit User</h2>
    @endif

    @if ($userRecord)
        <form method="POST" action="{{ $action }}">
            @csrf
            @method('PATCH')
            <label>Name <input type="text" value="{{ $userRecord['name'] }}" readonly></label>
            <label>Email <input type="text" value="{{ $userRecord['email'] }}" readonly></label>
            <label>
                Role
                <select name="role" required>
                    @foreach ($userRoleOptions as $roleOption)
                        <option value="{{ $roleOption }}" @selected(old('role', $userRecord['role']) === $roleOption)>{{ $roleOption === 'supervisor' ? 'Teacher' : ucfirst($roleOption) }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                Access
                <select name="is_active" required>
                    <option value="1" @selected((string) old('is_active', (int) $userRecord['is_active']) === '1')>Active</option>
                    <option value="0" @selected((string) old('is_active', (int) $userRecord['is_active']) === '0')>Suspended</option>
                </select>
            </label>
            <p class="field-hint">
                Role changes move the account into the selected tenant role table while preserving the email and password.
            </p>
            <button type="submit" class="small-button">Save User</button>
        </form>
    @endif
@unless ($embedded)
</article>
@endunless
