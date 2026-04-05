@php
    $embedded = $embedded ?? false;
    $showHeading = $showHeading ?? true;
    $applicationSection = $applicationSection ?? 'applications';
    $isPaginated = $applications instanceof \Illuminate\Contracts\Pagination\Paginator
        || $applications instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator
        || $applications instanceof \Illuminate\Contracts\Pagination\CursorPaginator;
@endphp

@unless ($embedded)
<article>
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
                        <td><span class="table-badge">{{ strtoupper($application->status) }}</span></td>
                        <td>
                            <div class="link-row">
                                @if ($application->resume_path)
                                    <a class="action-icon-button" href="{{ asset($application->resume_path) }}" target="_blank" rel="noopener" title="Open resume" aria-label="Open resume">
                                        <i class="fa-solid fa-file-lines"></i>
                                        <span class="sr-only">Resume</span>
                                    </a>
                                @endif
                                @if ($application->endorsement_letter_path)
                                    <a class="action-icon-button" href="{{ asset($application->endorsement_letter_path) }}" target="_blank" rel="noopener" title="Open endorsement letter" aria-label="Open endorsement letter">
                                        <i class="fa-solid fa-file-signature"></i>
                                        <span class="sr-only">Endorsement</span>
                                    </a>
                                @endif
                                @if ($application->moa_path)
                                    <a class="action-icon-button" href="{{ asset($application->moa_path) }}" target="_blank" rel="noopener" title="Open MOA" aria-label="Open MOA">
                                        <i class="fa-solid fa-file-circle-check"></i>
                                        <span class="sr-only">MOA</span>
                                    </a>
                                @endif
                                @if ($application->clearance_path)
                                    <a class="action-icon-button" href="{{ asset($application->clearance_path) }}" target="_blank" rel="noopener" title="Open clearance" aria-label="Open clearance">
                                        <i class="fa-solid fa-file-shield"></i>
                                        <span class="sr-only">Clearance</span>
                                    </a>
                                @endif
                            </div>
                        </td>
                        <td>
                            <a class="action-icon-button action-icon-button-secondary" href="{{ $dashboardBaseUrl.'?section='.$applicationSection.'&edit='.$application->id }}" title="Edit application" aria-label="Edit application">
                                <i class="fa-solid fa-pen-to-square"></i>
                                <span class="sr-only">Edit</span>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($isPaginated && $applications->hasPages())
            <div class="pagination">
                {{ $applications->links() }}
            </div>
        @endif
    @endif
@unless ($embedded)
</article>
@endunless
