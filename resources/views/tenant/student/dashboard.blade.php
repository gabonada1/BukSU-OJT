@php
    $layoutMode = 'dashboard';
    $currentSection = $currentSection ?? 'applications';
    $requirementOptions = ['Resume', 'MOA', 'Endorsement Letter', 'Weekly Report', 'Monthly Report', 'Clearance'];
    $approvedRequirements = $student->requirements->where('status', 'approved')->count();
    $approvedLogs = $student->hourLogs->where('status', 'approved')->count();
    $remainingHours = max(0, (float) $student->required_hours - (float) $student->completed_hours);
    $activeApplication = $student->applications->first(fn ($application) => in_array($application->status, ['pending', 'accepted', 'deployed'], true));
    $latestApplicationForDocuments = $activeApplication ?: $student->applications->sortByDesc(fn ($application) => $application->applied_at?->timestamp ?? 0)->first();
    $sectionMeta = [
        'applications' => [
            'title' => 'Internship Applications',
            'description' => 'Choose a partner organization, review active submissions, and keep your internship application documents in one place.',
            'pill' => strtoupper($student->status),
        ],
        'requirements' => [
            'title' => 'Forms & Requirements',
            'description' => 'Upload your school requirements and track coordinator feedback without mixing them into your internship application workspace.',
            'pill' => $approvedRequirements.' approved',
        ],
        'logs' => [
            'title' => 'Progress & Hours',
            'description' => 'Monitor approved duty hours and review your progress log history in a dedicated section.',
            'pill' => $student->student_number,
        ],
    ];
    $activeMeta = $sectionMeta[$currentSection] ?? $sectionMeta['applications'];
@endphp

@extends('layouts.tenant')

@section('content')
    <section class="admin-hero">
        <div class="admin-hero-copy">
            <span class="admin-eyebrow">Student Portal</span>
            <h1>Student Dashboard</h1>
            <p>{{ $student->full_name }} | {{ $student->partnerCompany?->name ?: 'No partner organization assigned yet' }}</p>
        </div>

        <div class="admin-hero-metrics">
            <article class="admin-hero-panel">
                <span>Hours Left</span>
                <strong>{{ number_format($remainingHours, 0) }}</strong>
                <small>Remaining duty hours before completion</small>
            </article>
            <article class="admin-hero-panel">
                <span>Applications</span>
                <strong>{{ $student->applications->count() }}</strong>
                <small>Internship applications on record</small>
            </article>
            <article class="admin-hero-panel">
                <span>Approved Hours</span>
                <strong>{{ $approvedLogs }}</strong>
                <small>Logs already validated by the coordinator</small>
            </article>
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

    <section class="section-card student-section-shell">
        <div class="section-header student-section-head">
            <div>
                <h2>{{ $activeMeta['title'] }}</h2>
                <p class="section-hint">{{ $activeMeta['description'] }}</p>
            </div>
            <span class="pill">{{ $activeMeta['pill'] }}</span>
        </div>

        @if ($currentSection === 'applications')
            <div class="student-application-banner">
                <div>
                    <strong>Application Workspace</strong>
                    <p>{{ $activeApplication ? 'You already have an active application under review, so the submission form is locked until the coordinator finishes that cycle.' : 'Submit your preferred internship placement here, then review partner organizations and your application history below.' }}</p>
                </div>
                <span class="badge">{{ $activeApplication ? strtoupper($activeApplication->status) : 'READY TO APPLY' }}</span>
            </div>

            @if ($activeApplication)
                <div class="flash" style="margin-bottom:4px;">
                    Your current active application is <strong>{{ strtoupper($activeApplication->status) }}</strong> for {{ $activeApplication->partnerCompany?->name ?: 'your selected organization' }}.
                </div>
            @endif

            <div class="student-dashboard-grid student-dashboard-grid-single">
                <div class="section-card student-subpanel" style="margin:0;">
                    <div class="student-card-head">
                        <h3>Apply for Internship</h3>
                        <span class="badge">{{ $activeApplication ? 'Locked' : 'Open' }}</span>
                    </div>

                    @if ($activeApplication)
                        <p>Your current application is still active. Wait for the internship coordinator to finish the review before sending another one.</p>
                        <div class="helper-note">
                            Active application: {{ $activeApplication->partnerCompany?->name ?: 'Selected organization' }} &middot; {{ strtoupper($activeApplication->status) }}
                        </div>
                        <div class="student-uploaded-documents">
                            <div class="student-card-head">
                                <h3>Your Uploaded Documents</h3>
                                <span class="badge">Current</span>
                            </div>
                            <div class="student-uploaded-grid">
                                <div class="student-uploaded-item">
                                    <strong>Resume</strong>
                                    @if ($activeApplication->resume_path)
                                        <a href="{{ asset($activeApplication->resume_path) }}" target="_blank" rel="noopener">Open uploaded file</a>
                                    @else
                                        <span>Not uploaded yet</span>
                                    @endif
                                </div>
                                <div class="student-uploaded-item">
                                    <strong>Endorsement Letter</strong>
                                    @if ($activeApplication->endorsement_letter_path)
                                        <a href="{{ asset($activeApplication->endorsement_letter_path) }}" target="_blank" rel="noopener">Open uploaded file</a>
                                    @else
                                        <span>Not uploaded yet</span>
                                    @endif
                                </div>
                                <div class="student-uploaded-item">
                                    <strong>MOA</strong>
                                    @if ($activeApplication->moa_path)
                                        <a href="{{ asset($activeApplication->moa_path) }}" target="_blank" rel="noopener">Open uploaded file</a>
                                    @else
                                        <span>Not uploaded yet</span>
                                    @endif
                                </div>
                                <div class="student-uploaded-item">
                                    <strong>Clearance</strong>
                                    @if ($activeApplication->clearance_path)
                                        <a href="{{ asset($activeApplication->clearance_path) }}" target="_blank" rel="noopener">Open uploaded file</a>
                                    @else
                                        <span>Not uploaded yet</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <p>Browse partner organizations, pick a position, and attach your initial application documents.</p>
                        <form method="POST" action="{{ $studentApplicationAction }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-grid">
                                <label class="field-span-2">
                                    Partner Organization
                                    <select name="partner_company_id" required>
                                        <option value="">Select an organization</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}" @selected((string) old('partner_company_id') === (string) $company->id)>{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </label>
                                <label>Position Applied <input type="text" name="position_applied" value="{{ old('position_applied') }}" placeholder="IT Support, Lab Assistant, Accounting Intern" required></label>
                                <div class="student-upload-note">
                                    <strong>Required Uploads</strong>
                                    <p>Attach your application documents here so the coordinator can review everything in one submission.</p>
                                </div>
                                <label class="field-span-2">Student Notes <textarea name="student_notes" placeholder="Preferred schedule, availability, or application remarks">{{ old('student_notes') }}</textarea></label>
                            </div>

                            <div class="student-upload-grid">
                                <label>Resume <input type="file" name="resume" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required></label>
                                <label>Endorsement Letter <input type="file" name="endorsement_letter" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></label>
                                <label>MOA <input type="file" name="moa" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></label>
                                <label>Clearance <input type="file" name="clearance" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></label>
                            </div>

                            @if ($latestApplicationForDocuments)
                                <div class="student-uploaded-documents">
                                    <div class="student-card-head">
                                        <h3>Your Last Uploaded Documents</h3>
                                        <span class="badge">{{ strtoupper($latestApplicationForDocuments->status) }}</span>
                                    </div>
                                    <div class="student-uploaded-grid">
                                        <div class="student-uploaded-item">
                                            <strong>Resume</strong>
                                            @if ($latestApplicationForDocuments->resume_path)
                                                <a href="{{ asset($latestApplicationForDocuments->resume_path) }}" target="_blank" rel="noopener">Open uploaded file</a>
                                            @else
                                                <span>Not uploaded yet</span>
                                            @endif
                                        </div>
                                        <div class="student-uploaded-item">
                                            <strong>Endorsement Letter</strong>
                                            @if ($latestApplicationForDocuments->endorsement_letter_path)
                                                <a href="{{ asset($latestApplicationForDocuments->endorsement_letter_path) }}" target="_blank" rel="noopener">Open uploaded file</a>
                                            @else
                                                <span>Not uploaded yet</span>
                                            @endif
                                        </div>
                                        <div class="student-uploaded-item">
                                            <strong>MOA</strong>
                                            @if ($latestApplicationForDocuments->moa_path)
                                                <a href="{{ asset($latestApplicationForDocuments->moa_path) }}" target="_blank" rel="noopener">Open uploaded file</a>
                                            @else
                                                <span>Not uploaded yet</span>
                                            @endif
                                        </div>
                                        <div class="student-uploaded-item">
                                            <strong>Clearance</strong>
                                            @if ($latestApplicationForDocuments->clearance_path)
                                                <a href="{{ asset($latestApplicationForDocuments->clearance_path) }}" target="_blank" rel="noopener">Open uploaded file</a>
                                            @else
                                                <span>Not uploaded yet</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <button type="submit" class="small-button">Submit Internship Application</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="student-history-panel">
                <div class="student-card-head">
                    <h3>Your Application History</h3>
                    <span class="badge">{{ $student->applications->count() }} total</span>
                </div>

                @if ($student->applications->isEmpty())
                    <p>No internship applications yet.</p>
                @else
                    <div class="table-wrap">
                        <table class="data-table">
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
                    </div>
                @endif
            </div>
        @elseif ($currentSection === 'requirements')
            <div class="student-dashboard-grid">
                <div class="section-card student-subpanel" style="margin:0;">
                    <div class="student-card-head">
                        <h3>Upload Form or Requirement</h3>
                        <span class="badge">Upload</span>
                    </div>

                    <form method="POST" action="{{ $studentRequirementAction }}" enctype="multipart/form-data">
                        @csrf
                        <label>
                            Requirement Name
                            <select name="requirement_name" required>
                                @foreach ($requirementOptions as $requirementOption)
                                    <option value="{{ $requirementOption }}" @selected(old('requirement_name', 'Resume') === $requirementOption)>{{ $requirementOption }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>Notes <textarea name="notes" placeholder="Optional context for the coordinator reviewer">{{ old('notes') }}</textarea></label>
                        <label>File <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required></label>
                        <button type="submit" class="small-button">Upload Document</button>
                    </form>
                </div>

                <div class="section-card student-subpanel" style="margin:0;">
                    <div class="student-card-head">
                        <h3>Submission Queue</h3>
                        <span class="badge">{{ $student->requirements->count() }} files</span>
                    </div>

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
        @else
            @if ($student->hourLogs->isEmpty())
                <p>No progress or hour logs yet.</p>
            @else
                <div class="table-wrap">
                    <table class="data-table">
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
                </div>
            @endif
        @endif
    </section>
@endsection
