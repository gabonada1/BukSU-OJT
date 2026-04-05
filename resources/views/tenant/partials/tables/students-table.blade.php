@php
    $embedded = $embedded ?? false;
    $showHeading = $showHeading ?? true;
@endphp

@unless ($embedded)
<article class="card">
@endunless
    @if ($showHeading)
        <h2>Students</h2>
    @endif
    @if ($students->isEmpty())
        <p>No students enrolled yet.</p>
    @else
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Verification</th><th>Status</th><th>Hours</th><th>Action</th></tr></thead>
            <tbody>
                @foreach ($students as $student)
                    <tr>
                        <td>{{ $student->full_name }}<br><small>{{ $student->partnerCompany?->name ?: 'Unassigned organization' }}</small></td>
                        <td>{{ $student->email }}</td>
                        <td><span class="status-pill {{ $student->email_verified_at ? 'active' : 'scheduled' }}">{{ $student->email_verified_at ? 'Verified' : 'Pending' }}</span></td>
                        <td><span class="badge">{{ $student->status }}</span></td>
                        <td>{{ $student->completed_hours }} / {{ $student->required_hours }}</td>
                        <td>
                            <div class="row-actions">
                                <a class="panel-link" href="{{ $dashboardBaseUrl.'?section=students&student_applications='.$student->id }}">Applications</a>
                                <a class="panel-link" href="{{ $dashboardBaseUrl.'?section=students&edit='.$student->id }}">Edit</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@unless ($embedded)
</article>
@endunless
