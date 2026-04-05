@php
    $layoutMode = 'dashboard';
    $validatedHours = (float) $hourLogs->where('status', 'approved')->sum('hours');
@endphp

@extends('layouts.tenant')

@section('content')
    <section class="dashboard-hero">
        <div>
            <span class="app-section-kicker">Supervisor Workspace</span>
            <h1>Company Supervisor Dashboard</h1>
            <p>{{ $supervisor->name }} · {{ $company?->name ?: 'No partner organization assigned yet' }}</p>
        </div>

        <div class="hero-metrics">
            <article class="hero-stat">
                <span>Students</span>
                <strong>{{ $students->count() }}</strong>
                <p>Interns assigned to your company.</p>
            </article>
            <article class="hero-stat">
                <span>Hour Logs</span>
                <strong>{{ $hourLogs->count() }}</strong>
                <p>Recent submissions waiting for review.</p>
            </article>
            <article class="hero-stat">
                <span>Approved</span>
                <strong>{{ number_format($validatedHours, 0) }}</strong>
                <p>Hours already validated.</p>
            </article>
        </div>
    </section>

    <section class="content-grid" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
        <article id="students" class="section-card">
            <div class="section-header">
                <div>
                    <h2>Students</h2>
                    <p>{{ $company?->name ?: 'Unassigned company' }}</p>
                </div>
            </div>

            @if ($students->isEmpty())
                <p>No students enrolled yet.</p>
            @else
                <ul class="clean-list">
                    @foreach ($students as $student)
                        @php
                            $studentProgress = $student->required_hours > 0 ? min(100, (int) round(($student->completed_hours / $student->required_hours) * 100)) : 0;
                        @endphp
                        <li>
                            <strong>{{ $student->full_name }}</strong>
                            <p><span class="table-badge">{{ strtoupper($student->status) }}</span></p>
                            <p>{{ $student->completed_hours }} / {{ $student->required_hours }} hours</p>
                            <div class="progress-track"><span style="width: {{ $studentProgress }}%"></span></div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </article>

        <article id="logs" class="section-card">
            <div class="section-header">
                <div>
                    <h2>Progress & Hour Logs</h2>
                    <p>Recent records submitted by assigned interns.</p>
                </div>
            </div>

            @if ($hourLogs->isEmpty())
                <p>No recent hour logs yet.</p>
            @else
                <div class="table-wrap">
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
                                    <td>{{ $log->hours }} <span class="table-badge">{{ strtoupper($log->status) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </article>
    </section>
@endsection
