@php
    $embedded = $embedded ?? false;
    $showHeading = $showHeading ?? true;
@endphp

@unless ($embedded)
<article class="card">
@endunless
    @if ($showHeading)
        <h2>Recent Progress & Hour Logs</h2>
    @endif
    @if ($hourLogs->isEmpty())
        <p>No OJT hour logs yet.</p>
    @else
        <table>
            <thead><tr><th>Student</th><th>Date</th><th>Hours</th><th>Action</th></tr></thead>
            <tbody>
                @foreach ($hourLogs as $log)
                    <tr>
                        <td>{{ $log->student?->full_name ?: 'Unknown student' }}</td>
                        <td>{{ $log->log_date?->format('M d, Y') }}</td>
                        <td>{{ rtrim(rtrim(number_format($log->hours, 2), '0'), '.') }} <span class="badge">{{ $log->status }}</span></td>
                        <td><a class="panel-link" href="{{ $dashboardBaseUrl.'?section=hours&edit='.$log->id }}">Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@unless ($embedded)
</article>
@endunless
