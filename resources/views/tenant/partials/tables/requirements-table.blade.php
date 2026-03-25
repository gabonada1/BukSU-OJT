@php
    $embedded = $embedded ?? false;
    $showHeading = $showHeading ?? true;
@endphp

@unless ($embedded)
<article class="card">
@endunless
    @if ($showHeading)
        <h2>Recent Forms & Requirements</h2>
    @endif
    @if ($requirements->isEmpty())
        <p>No practicum requirements submitted yet.</p>
    @else
        <table>
            <thead><tr><th>Student</th><th>Requirement</th><th>Document</th><th>Status</th><th>Feedback</th><th>Action</th></tr></thead>
            <tbody>
                @foreach ($requirements as $requirement)
                    <tr>
                        <td>{{ $requirement->student?->full_name ?: 'Unknown student' }}</td>
                        <td>{{ $requirement->requirement_name }}</td>
                        <td>
                            @if ($requirement->file_path)
                                <a href="{{ asset($requirement->file_path) }}" target="_blank" rel="noopener">Open file</a>
                            @else
                                No file
                            @endif
                        </td>
                        <td><span class="badge">{{ $requirement->status }}</span></td>
                        <td>{{ $requirement->feedback ?: ($requirement->notes ?: 'No feedback yet') }}</td>
                        <td><a class="panel-link" href="{{ $dashboardBaseUrl.'?section=requirements&edit='.$requirement->id }}">Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@unless ($embedded)
</article>
@endunless
