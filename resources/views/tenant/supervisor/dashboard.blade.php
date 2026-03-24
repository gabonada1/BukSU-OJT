@php
    $layoutMode = 'dashboard';
    $validatedHours = (float) $hourLogs->where('status', 'approved')->sum('hours');
@endphp

@extends('layouts.tenant')

@section('content')
    <section class="page-head">
        <div>
            <h1>Teacher Dashboard</h1>
            <p>{{ $supervisor->name }} &middot; {{ $company?->name ?: 'No company assigned yet' }}</p>
        </div>

        <div class="page-mini-stats">
            <div class="page-mini-card">
                <strong>Students</strong>
                <span>{{ $students->count() }}</span>
            </div>
            <div class="page-mini-card">
                <strong>Logs</strong>
                <span>{{ $hourLogs->count() }}</span>
            </div>
            <div class="page-mini-card">
                <strong>Approved</strong>
                <span>{{ number_format($validatedHours, 0) }}</span>
            </div>
        </div>
    </section>

    <section class="stack-sections">
        <article id="students" class="section-card section-anchor">
            <div class="section-header">
                <h2>Students</h2>
                <span class="pill">{{ $company?->name ?: 'Unassigned' }}</span>
            </div>

            @if ($students->isEmpty())
                <p>No students assigned yet.</p>
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
                <h2>Logs</h2>
                <span class="pill">Recent</span>
            </div>

            @if ($hourLogs->isEmpty())
                <p>No recent logs yet.</p>
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
