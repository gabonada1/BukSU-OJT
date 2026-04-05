@php
    $layoutMode = 'dashboard';
    $validatedHours = (float) $hourLogs->where('status', 'approved')->sum('hours');
@endphp

@extends('layouts.tenant')

@section('content')
    <section class="admin-hero">
        <div class="admin-hero-copy">
            <span class="admin-eyebrow">Supervisor Workspace</span>
            <h1>Company Supervisor Dashboard</h1>
            <p>{{ $supervisor->name }} | {{ $company?->name ?: 'No partner organization assigned yet' }}</p>
        </div>

        <div class="admin-hero-metrics">
            <article class="admin-hero-panel">
                <span>Students</span>
                <strong>{{ $students->count() }}</strong>
                <small>Interns assigned to your company</small>
            </article>
            <article class="admin-hero-panel">
                <span>Hour Logs</span>
                <strong>{{ $hourLogs->count() }}</strong>
                <small>Recent submissions waiting for review</small>
            </article>
            <article class="admin-hero-panel">
                <span>Approved</span>
                <strong>{{ number_format($validatedHours, 0) }}</strong>
                <small>Hours already validated</small>
            </article>
        </div>
    </section>

    <section class="stack-sections">
        <article id="students" class="section-card section-anchor">
            <div class="section-header">
                <h2>Students</h2>
                <span class="pill">{{ $company?->name ?: 'Unassigned' }}</span>
            </div>

            @if ($students->isEmpty())
                <p>No students enrolled yet.</p>
            @else
                <ul class="soft-list">
                    @foreach ($students as $student)
                        <li>
                            <div class="split-line">
                                <strong>{{ $student->full_name }}</strong>
                                <span class="badge">{{ strtoupper($student->status) }}</span>
                            </div>
                            <p style="margin:8px 0 0;">{{ $student->completed_hours }} / {{ $student->required_hours }} hours</p>
                            @php
                                $studentProgress = $student->required_hours > 0 ? min(100, (int) round(($student->completed_hours / $student->required_hours) * 100)) : 0;
                            @endphp
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $studentProgress }}%;"></div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </article>

        <article id="logs" class="section-card section-anchor">
            <div class="section-header">
                <h2>Progress & Hour Logs</h2>
                <span class="pill">Recent</span>
            </div>

            @if ($hourLogs->isEmpty())
                <p>No recent hour logs yet.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Date</th>
                            <th>Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($hourLogs as $log)
                            <tr>
                                <td>{{ $log->student?->full_name ?: 'Unknown student' }}</td>
                                <td>{{ $log->log_date?->format('M d, Y') }}</td>
                                <td>{{ $log->hours }} <span class="badge">{{ strtoupper($log->status) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </article>
    </section>
@endsection
