@php
    $embedded = $embedded ?? false;
    $showHeading = $showHeading ?? true;
@endphp

@unless ($embedded)
<article class="card">
@endunless
    @if ($showHeading)
        <h2>RBAC & User Management</h2>
    @endif
    <p class="section-hint" style="margin:0 0 16px;">Tenant admins manage student, supervisor, and coordinator access for this college portal.</p>
    @if ($userDirectory->isEmpty())
        <p>No college portal users yet.</p>
    @else
        <table>
            <thead><tr><th>Name</th><th>Role</th><th>Context</th><th>Verification</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
                @foreach ($userDirectory as $user)
                    <tr>
                        <td>{{ $user['name'] }}<br><small>{{ $user['email'] }}</small></td>
                        <td><span class="badge">{{ strtoupper($user['role'] === 'admin' ? 'internship coordinator' : ($user['role'] === 'supervisor' ? 'company supervisor' : $user['role'])) }}</span></td>
                        <td>{{ $user['context'] }}</td>
                        <td>
                            @if (array_key_exists('email_verified_at', $user))
                                <span class="status-pill {{ $user['email_verified_at'] ? 'active' : 'scheduled' }}">{{ $user['email_verified_at'] ? 'Verified' : 'Pending' }}</span>
                            @else
                                <span class="pill">Managed</span>
                            @endif
                        </td>
                        <td><span class="status-pill {{ $user['status'] === 'active' ? 'active' : ($user['status'] === 'pending verification' ? 'scheduled' : 'suspended') }}">{{ ucfirst($user['status']) }}</span></td>
                        <td><a class="panel-link" href="{{ $dashboardBaseUrl.'?section=users&edit='.$user['key'] }}">Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@unless ($embedded)
</article>
@endunless
