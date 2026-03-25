@php
    $embedded = $embedded ?? false;
    $showHeading = $showHeading ?? true;
@endphp

@unless ($embedded)
<article class="card">
@endunless
    @if ($showHeading)
        <h2>Student Applications</h2>
    @endif
    @if ($applications->isEmpty())
        <p>No student applications yet.</p>
    @else
        <table>
            <thead><tr><th>Student</th><th>Organization</th><th>Position</th><th>Status</th><th>Documents</th><th>Action</th></tr></thead>
            <tbody>
                @foreach ($applications as $application)
                    <tr>
                        <td>{{ $application->student?->full_name ?: 'Unknown student' }}</td>
                        <td>{{ $application->partnerCompany?->name ?: 'No organization' }}</td>
                        <td>{{ $application->position_applied ?: 'Not set' }}</td>
                        <td><span class="badge">{{ strtoupper($application->status) }}</span></td>
                        <td>
                            <div class="table-link-stack">
                                @if ($application->resume_path)
                                    <a href="{{ asset($application->resume_path) }}" target="_blank" rel="noopener">Resume</a>
                                @endif
                                @if ($application->endorsement_letter_path)
                                    <a href="{{ asset($application->endorsement_letter_path) }}" target="_blank" rel="noopener">Endorsement</a>
                                @endif
                                @if ($application->moa_path)
                                    <a href="{{ asset($application->moa_path) }}" target="_blank" rel="noopener">MOA</a>
                                @endif
                                @if ($application->clearance_path)
                                    <a href="{{ asset($application->clearance_path) }}" target="_blank" rel="noopener">Clearance</a>
                                @endif
                            </div>
                        </td>
                        <td><a class="panel-link" href="{{ $dashboardBaseUrl.'?section=applications&edit='.$application->id }}">Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@unless ($embedded)
</article>
@endunless
