@php
    $layoutMode = 'dashboard';
    $approvedRequirements = $student->requirements->where('status', 'approved')->count();
    $approvedLogs = $student->hourLogs->where('status', 'approved')->count();
    $remainingHours = max(0, (float) $student->required_hours - (float) $student->completed_hours);
    $activeApplication = $student->applications->first(fn ($application) => in_array($application->status, ['pending', 'accepted', 'deployed'], true));
@endphp

@extends('layouts.tenant')

@section('content')
    <section class="page-head">
        <div>
            <h1>Student Dashboard</h1>
            <p>{{ $student->full_name }} &middot; {{ $student->partnerCompany?->name ?: 'No partner organization assigned yet' }}</p>
        </div>

        <div class="page-mini-stats">
            <div class="page-mini-card">
                <strong>Hours Left</strong>
                <span>{{ number_format($remainingHours, 0) }}</span>
            </div>
            <div class="page-mini-card">
                <strong>Applications</strong>
                <span>{{ $student->applications->count() }}</span>
            </div>
            <div class="page-mini-card">
                <strong>Forms & Requirements</strong>
                <span>{{ $approvedRequirements }}</span>
            </div>
            <div class="page-mini-card">
                <strong>Approved Hours</strong>
                <span>{{ $approvedLogs }}</span>
            </div>
        </div>
    </section>

    @if ($errors->any())
        <div class="error-panel">
            <strong>Some student actions did not complete.</strong>
            <ul style="margin:8px 0 0;padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif

    <section class="stack-sections">
        <article id="applications" class="section-card section-anchor">
            <div class="section-header">
                <h2>Internship Applications</h2>
                <span class="pill">{{ strtoupper($student->status) }}</span>
            </div>

            @if ($activeApplication)
                <div class="flash" style="margin-bottom:18px;">
                    Your current active application is <strong>{{ strtoupper($activeApplication->status) }}</strong> for {{ $activeApplication->partnerCompany?->name ?: 'your selected organization' }}.
                </div>
            @endif

            <div class="two-column-layout">
                <div class="section-card" style="margin:0;">
                    <h3>Apply for Internship</h3>
                    @if ($activeApplication)
                        <p>Your current application is still active. Wait for the internship coordinator to finish the review before sending another one.</p>
                        <div class="helper-note">
                            Active application: {{ $activeApplication->partnerCompany?->name ?: 'Selected organization' }} &middot; {{ strtoupper($activeApplication->status) }}
                        </div>
                    @else
                        <p>Browse partner organizations, pick a position, and attach your initial application documents.</p>
                        <form method="POST" action="{{ $studentApplicationAction }}" enctype="multipart/form-data">
                            @csrf
                            <label>
                                Partner Organization
                                <select name="partner_company_id" required>
                                    <option value="">Select an organization</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}" @selected((string) old('partner_company_id') === (string) $company->id)>{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label>Position Applied <input type="text" name="position_applied" value="{{ old('position_applied') }}" placeholder="IT Support, Lab Assistant, Accounting Intern" required></label>
                            <label>Student Notes <textarea name="student_notes" placeholder="Preferred schedule, availability, or application remarks">{{ old('student_notes') }}</textarea></label>
                            <label>Resume <input type="file" name="resume" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required></label>
                            <label>Endorsement Letter <input type="file" name="endorsement_letter" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></label>
                            <label>MOA <input type="file" name="moa" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></label>
                            <label>Clearance <input type="file" name="clearance" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></label>
                            <button type="submit" class="small-button">Submit Internship Application</button>
                        </form>
                    @endif
                </div>

                <div class="section-card" style="margin:0;">
                    <h3>Partner Organizations</h3>
                    @if ($companies->isEmpty())
                        <p>No partner organizations on file yet.</p>
                    @else
                        <ul class="soft-list">
                            @foreach ($companies as $company)
                                <li>
                                    <div class="split-line">
                                        <strong>{{ $company->name }}</strong>
                                        <span class="pill">{{ $company->intern_slot_limit }} slots</span>
                                    </div>
                                    <p style="margin:10px 0 0;">{{ $company->industry ?: 'No industry type set' }}</p>
                                    <p style="margin:10px 0 0;">Positions: {{ implode(', ', $company->availablePositionsList()) ?: 'No positions listed' }}</p>
                                    <p style="margin:10px 0 0;">Required docs: {{ implode(', ', $company->requiredDocumentsList()) ?: 'No required documents listed' }}</p>
                                    <p style="margin:10px 0 0;">Company Supervisor: {{ $company->supervisors->pluck('name')->implode(', ') ?: ($company->contact_person ?: 'No company supervisor listed') }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div style="margin-top:22px;">
                <h3>Your Application History</h3>
                @if ($student->applications->isEmpty())
                    <p>No internship applications yet.</p>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>Organization</th>
                                <th>Position</th>
                                <th>Status</th>
                                <th>Applied</th>
                                <th>Documents</th>
                                <th>Feedback</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($student->applications as $application)
                                <tr>
                                    <td>{{ $application->partnerCompany?->name ?: 'No organization' }}</td>
                                    <td>{{ $application->position_applied ?: 'Not set' }}</td>
                                    <td><span class="badge">{{ strtoupper($application->status) }}</span></td>
                                    <td>{{ $application->applied_at?->format('M d, Y') ?: 'Not set' }}</td>
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
                                    <td>{{ $application->admin_feedback ?: 'Waiting for coordinator review' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </article>

        <article id="requirements" class="section-card section-anchor">
            <div class="section-header">
                <h2>Forms & Requirements</h2>
                <span class="pill">{{ $approvedRequirements }} approved</span>
            </div>

            <div class="two-column-layout">
                <div class="section-card" style="margin:0;">
                    <h3>Upload Form or Requirement</h3>
                    <form method="POST" action="{{ $studentRequirementAction }}" enctype="multipart/form-data">
                        @csrf
                        <label>Requirement Name <input type="text" name="requirement_name" value="{{ old('requirement_name', 'Resume') }}" placeholder="Resume, MOA, Weekly Report, Monthly Report" required></label>
                        <label>Notes <textarea name="notes" placeholder="Optional context for the coordinator reviewer">{{ old('notes') }}</textarea></label>
                        <label>File <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required></label>
                        <button type="submit" class="small-button">Upload Document</button>
                    </form>
                </div>

                <div class="section-card" style="margin:0;">
                    <h3>Submission Queue</h3>
                    @if ($student->requirements->isEmpty())
                        <p>No forms or requirements submitted yet.</p>
                    @else
                        <ul class="soft-list">
                            @foreach ($student->requirements as $requirement)
                                <li>
                                    <div class="split-line">
                                        <strong>{{ $requirement->requirement_name }}</strong>
                                        <span class="badge">{{ strtoupper($requirement->status === 'revision' ? 'requires revision' : $requirement->status) }}</span>
                                    </div>
                                    @if ($requirement->file_path)
                                        <p style="margin:10px 0 0;"><a href="{{ asset($requirement->file_path) }}" target="_blank" rel="noopener">Open uploaded file</a></p>
                                    @endif
                                    @if ($requirement->feedback)
                                        <p style="margin:10px 0 0;">Feedback: {{ $requirement->feedback }}</p>
                                    @elseif ($requirement->notes)
                                        <p style="margin:10px 0 0;">Notes: {{ $requirement->notes }}</p>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </article>

        <article id="logs" class="section-card section-anchor">
            <div class="section-header">
                <h2>Progress & Hours</h2>
                <span class="pill">{{ $student->student_number }}</span>
            </div>

            @if ($student->hourLogs->isEmpty())
                <p>No progress or hour logs yet.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Hours</th>
                            <th>Status</th>
                            <th>Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($student->hourLogs as $log)
                            <tr>
                                <td>{{ $log->log_date?->format('M d, Y') }}</td>
                                <td>{{ $log->hours }}</td>
                                <td><span class="badge">{{ strtoupper($log->status) }}</span></td>
                                <td>{{ $log->activity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </article>
    </section>
@endsection
