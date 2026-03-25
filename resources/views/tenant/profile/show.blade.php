@php
    $layoutMode = 'dashboard';
    $courseFormContext = old('form_context');
    $editingCourseId = old('editing_course_id');
    $profileInfoFields = match ($profileRole) {
        'student' => ['first_name', 'last_name', 'email', 'program'],
        'supervisor' => ['name', 'email', 'position'],
        default => ['name', 'email'],
    };
    $profileInfoHasErrors = collect($profileInfoFields)->contains(fn ($field) => $errors->has($field));
    $passwordHasErrors = $errors->has('current_password')
        || $errors->has('password')
        || $errors->has('password_confirmation');
    $brandingHasErrors = $errors->has('portal_title')
        || $errors->has('accent_color')
        || $errors->has('secondary_color')
        || $errors->has('portal_logo');
    $ojtSettingsHasErrors = $errors->has('default_ojt_hours')
        || $errors->has('allow_student_hour_override')
        || $errors->has('ojt_hours_note');
    $courseCreateHasErrors = $courseFormContext === 'course-create';
    $courseEditHasErrors = $courseFormContext === 'course-edit' && filled($editingCourseId);
    $brandingPortalTitle = old('portal_title', $brandingSettings['portal_title']);
    $brandingAccent = old('accent_color', $brandingSettings['accent']);
    $brandingSecondary = old('secondary_color', $brandingSettings['secondary']);
@endphp

@extends('layouts.tenant')

@section('content')
    <section class="page-head">
        <div>
            <h1>Profile</h1>
            <p>{{ $profileRole === 'admin' ? 'Internship Coordinator' : ($profileRole === 'supervisor' ? 'Company Supervisor' : 'Student') }} account for {{ $tenant->name }}</p>
        </div>
    </section>

    @if ($errors->any())
        <div class="error-panel">
            <strong>Some profile updates did not complete.</strong>
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

    <section class="profile-grid">
        <article class="section-card">
            <div class="section-header">
                <div>
                    <h2>Account Info</h2>
                    <p class="section-hint">Review your current profile details, then open the modal when you need to update them.</p>
                </div>
                <div class="action-row-actions">
                    <span class="pill">{{ strtoupper($profileRole) }}</span>
                    <button type="button" class="panel-link" data-modal-open="profile-info-modal">Edit Info</button>
                </div>
            </div>

            <div class="profile-summary-grid">
                @if ($profileRole === 'student')
                    <div class="summary-item">
                        <span class="summary-label">Student Number</span>
                        <strong class="summary-value">{{ $profileUser->student_number }}</strong>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Status</span>
                        <strong class="summary-value">{{ ucfirst($profileUser->status) }}</strong>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Full Name</span>
                        <strong class="summary-value">{{ trim($profileUser->first_name.' '.$profileUser->last_name) }}</strong>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Email</span>
                        <strong class="summary-value">{{ $profileUser->email }}</strong>
                    </div>
                    <div class="summary-item field-span-2">
                        <span class="summary-label">Course / Program</span>
                        <strong class="summary-value">
                            @if ($profileUser->course_id && $profileUser->course)
                                {{ $profileUser->course->code }} - {{ $profileUser->course->name }}
                            @else
                                {{ $profileUser->program ?: 'Not set yet' }}
                            @endif
                        </strong>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Required OJT Hours</span>
                        <strong class="summary-value">{{ number_format($profileUser->required_hours, 0) }} hrs</strong>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Completed OJT Hours</span>
                        <strong class="summary-value">{{ number_format($profileUser->completed_hours, 0) }} hrs</strong>
                    </div>
                @elseif ($profileRole === 'supervisor')
                    <div class="summary-item">
                        <span class="summary-label">Name</span>
                        <strong class="summary-value">{{ $profileUser->name }}</strong>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Email</span>
                        <strong class="summary-value">{{ $profileUser->email }}</strong>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Position</span>
                        <strong class="summary-value">{{ $profileUser->position ?: 'Not set yet' }}</strong>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Organization</span>
                        <strong class="summary-value">{{ $profileUser->partnerCompany?->name ?: 'Unassigned' }}</strong>
                    </div>
                @else
                    <div class="summary-item">
                        <span class="summary-label">Name</span>
                        <strong class="summary-value">{{ $profileUser->name }}</strong>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Email</span>
                        <strong class="summary-value">{{ $profileUser->email }}</strong>
                    </div>
                    <div class="summary-item field-span-2">
                        <span class="summary-label">College</span>
                        <strong class="summary-value">{{ $tenant->name }}</strong>
                    </div>
                @endif
            </div>
        </article>

        <article class="section-card">
            <div class="section-header">
                <div>
                    <h2>Change Password</h2>
                    <p class="section-hint">Open the password modal when you want to secure your account with a new password.</p>
                </div>
                <div class="action-row-actions">
                    <span class="pill">Secure</span>
                    <button type="button" class="panel-link" data-modal-open="password-modal">Change Password</button>
                </div>
            </div>

            <div class="summary-callout" style="margin-top:16px;">
                <strong>Security Reminder</strong>
                <p>Use at least 8 characters and keep your login details private. You only need to open the form when you are ready to update your password.</p>
            </div>
        </article>

        @if ($profileRole === 'admin')
            <article class="section-card" id="portal-branding">
                <div class="section-header">
                    <div>
                        <h2>Portal Branding</h2>
                        <p class="section-hint">Keep the live look of your college portal here, then open the modal to change title, colors, and logo.</p>
                    </div>
                    <div class="action-row-actions">
                        <span class="pill">Customizable</span>
                        <button type="button" class="panel-link" data-modal-open="branding-modal">Customize UI</button>
                    </div>
                </div>

                <div class="branding-preview-card" style="margin-top:16px;">
                    <div class="branding-preview-copy">
                        <strong>{{ $brandingSettings['portal_title'] }}</strong>
                        <p class="section-hint" style="margin:0;">Current live branding preview for {{ $tenant->name }}.</p>
                        <div class="branding-swatch-row">
                            <span class="branding-swatch">
                                <span class="branding-swatch-sample" style="--swatch-color: {{ $brandingSettings['accent'] }};"></span>
                                Accent {{ strtoupper($brandingSettings['accent']) }}
                            </span>
                            <span class="branding-swatch">
                                <span class="branding-swatch-sample" style="--swatch-color: {{ $brandingSettings['secondary'] }};"></span>
                                Secondary {{ strtoupper($brandingSettings['secondary']) }}
                            </span>
                        </div>
                    </div>

                    @if ($brandingSettings['logo_path'])
                        <img src="{{ asset($brandingSettings['logo_path']) }}" alt="{{ $brandingSettings['portal_title'] }} Logo" class="branding-logo-preview">
                    @else
                        <div class="branding-logo-fallback">{{ strtoupper($tenant->code ?: 'BK') }}</div>
                    @endif
                </div>
            </article>

            <article class="section-card" id="ojt-settings">
                <div class="section-header">
                    <div>
                        <h2>OJT Hours Settings</h2>
                        <p class="section-hint">The active college-wide defaults are shown here. Open the modal if you need to change the fallback hour rules.</p>
                    </div>
                    <div class="action-row-actions">
                        <span class="pill">College-wide</span>
                        <button type="button" class="panel-link" data-modal-open="ojt-settings-modal">Edit Settings</button>
                    </div>
                </div>

                <div class="profile-summary-grid">
                    <div class="summary-item">
                        <span class="summary-label">Default Required OJT Hours</span>
                        <strong class="summary-value">{{ number_format($ojtSettings['default_ojt_hours'], 0) }} hrs</strong>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Manual Student Override</span>
                        <strong class="summary-value">{{ $ojtSettings['allow_student_hour_override'] ? 'Allowed' : 'Locked to course/default hours' }}</strong>
                    </div>
                    <div class="summary-item field-span-2">
                        <span class="summary-label">Policy Note</span>
                        <p class="summary-note">{{ $ojtSettings['ojt_hours_note'] ?: 'No OJT policy note saved yet.' }}</p>
                    </div>
                </div>
            </article>

            <article class="section-card" id="courses">
                <div class="action-row">
                    <div>
                        <h2>Courses & Programs</h2>
                        <p class="section-hint">Manage your official course list from here. Use the modal buttons to add or update courses without expanding the page.</p>
                    </div>
                    <div class="action-row-actions">
                        <button type="button" class="panel-link" data-modal-open="course-create-modal">+ Add Course</button>
                    </div>
                </div>

                @if ($courses->isEmpty())
                    <div class="empty-state-block">
                        <p class="empty-label">No courses defined yet. Add your first course from the button above.</p>
                        <p class="empty-hint">
                            Until courses are set up, students can still use the legacy free-text program field.
                            Once courses are defined, coordinators can assign them from this managed list.
                        </p>
                    </div>
                @else
                    <div class="table-wrap" style="margin-top:20px;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Course Name</th>
                                    <th>Required OJT Hours</th>
                                    <th>Students</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($courses as $course)
                                    <tr>
                                        <td><strong class="code-cell">{{ $course->code }}</strong></td>
                                        <td>{{ $course->name }}</td>
                                        <td class="hours-cell"><span class="hours-badge">{{ number_format($course->required_ojt_hours, 0) }} hrs</span></td>
                                        <td class="muted-cell">{{ $course->students_count }}</td>
                                        <td>
                                            <span class="status-pill {{ $course->is_active ? 'status-active' : 'status-inactive' }}">
                                                {{ $course->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="row-actions">
                                                <button type="button" class="action-btn" data-modal-open="course-edit-modal-{{ $course->id }}">Edit</button>

                                                @if ($course->students_count === 0)
                                                    <form method="POST" action="{{ $courseActions[$course->id]['destroy'] }}" class="chrome-inline-form" onsubmit="return confirm('Remove course {{ $course->code }}?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="action-btn danger-btn">Remove</button>
                                                    </form>
                                                @else
                                                    <span class="muted-cell" style="font-size:11px;">Has students</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </article>
        @endif
    </section>

    <div id="profile-info-modal" class="modal-shell" hidden aria-hidden="true">
        <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="profile-info-modal-title">
            <div class="modal-head">
                <div>
                    <h3 id="profile-info-modal-title">Edit Account Info</h3>
                    <p class="modal-copy">Update the fields that belong to your role, then save your profile changes.</p>
                </div>
                <button type="button" class="panel-close" data-modal-close aria-label="Close account info modal">&times;</button>
            </div>

            <form method="POST" action="{{ $profileUpdateAction }}" class="form-grid">
                @csrf
                @method('PATCH')

                @if ($profileRole === 'student')
                    <label>
                        First Name
                        <input type="text" name="first_name" value="{{ old('first_name', $profileUser->first_name) }}" required>
                    </label>
                    <label>
                        Last Name
                        <input type="text" name="last_name" value="{{ old('last_name', $profileUser->last_name) }}" required>
                    </label>
                    <label class="field-span-2">
                        Email
                        <input type="email" name="email" value="{{ old('email', $profileUser->email) }}" required>
                    </label>

                    @if ($profileUser->course_id && $profileUser->course)
                        <label class="field-span-2">
                            Course / Program
                            <input type="text" value="{{ $profileUser->course->code }} - {{ $profileUser->course->name }}" readonly>
                            <small class="field-hint">Your course is managed through coordinator enrollment settings.</small>
                        </label>
                    @else
                        <label class="field-span-2">
                            Program
                            <input type="text" name="program" value="{{ old('program', $profileUser->program) }}">
                        </label>
                    @endif
                @elseif ($profileRole === 'supervisor')
                    <label>
                        Name
                        <input type="text" name="name" value="{{ old('name', $profileUser->name) }}" required>
                    </label>
                    <label>
                        Email
                        <input type="email" name="email" value="{{ old('email', $profileUser->email) }}" required>
                    </label>
                    <label class="field-span-2">
                        Position
                        <input type="text" name="position" value="{{ old('position', $profileUser->position) }}">
                    </label>
                @else
                    <label>
                        Name
                        <input type="text" name="name" value="{{ old('name', $profileUser->name) }}" required>
                    </label>
                    <label>
                        Email
                        <input type="email" name="email" value="{{ old('email', $profileUser->email) }}" required>
                    </label>
                @endif

                <div class="field-span-2 modal-actions">
                    <button type="submit" class="button">Save Profile</button>
                    <button type="button" class="button secondary" data-modal-close>Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div id="password-modal" class="modal-shell" hidden aria-hidden="true">
        <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="password-modal-title">
            <div class="modal-head">
                <div>
                    <h3 id="password-modal-title">Change Password</h3>
                    <p class="modal-copy">Enter your current password first, then choose a new password for your account.</p>
                </div>
                <button type="button" class="panel-close" data-modal-close aria-label="Close password modal">&times;</button>
            </div>

            <form method="POST" action="{{ $passwordUpdateAction }}" class="form-grid">
                @csrf
                @method('PUT')

                <label class="field-span-2">
                    Current Password
                    <input type="password" name="current_password" required>
                </label>
                <label>
                    New Password
                    <input type="password" name="password" required>
                </label>
                <label>
                    Confirm Password
                    <input type="password" name="password_confirmation" required>
                </label>

                <div class="field-span-2 modal-actions">
                    <button type="submit" class="button">Update Password</button>
                    <button type="button" class="button secondary" data-modal-close>Cancel</button>
                </div>
            </form>
        </div>
    </div>

    @if ($profileRole === 'admin')
        <div id="branding-modal" class="modal-shell" hidden aria-hidden="true">
            <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="branding-modal-title">
                <div class="modal-head">
                    <div>
                        <h3 id="branding-modal-title">Customize Portal Branding</h3>
                        <p class="modal-copy">Update the title, accent colors, and logo used across your tenant portal screens.</p>
                    </div>
                    <button type="button" class="panel-close" data-modal-close aria-label="Close branding modal">&times;</button>
                </div>

                <form method="POST" action="{{ $brandingSettingsAction }}" enctype="multipart/form-data" class="form-grid">
                    @csrf

                    <label class="field-span-2">
                        Portal Title
                        <input type="text" name="portal_title" value="{{ $brandingPortalTitle }}" maxlength="120" required>
                        <small class="field-hint">Shown in the tenant sidebar, login screens, and portal preview labels.</small>
                    </label>

                    <label>
                        Accent Color
                        <div class="color-field-row">
                            <input type="color" name="accent_color" value="{{ $brandingAccent }}" class="color-input" required>
                            <span class="color-code">{{ strtoupper($brandingAccent) }}</span>
                        </div>
                        <small class="field-hint">Used for primary buttons, active navigation, and highlights.</small>
                    </label>

                    <label>
                        Secondary Color
                        <div class="color-field-row">
                            <input type="color" name="secondary_color" value="{{ $brandingSecondary }}" class="color-input" required>
                            <span class="color-code">{{ strtoupper($brandingSecondary) }}</span>
                        </div>
                        <small class="field-hint">Used for supporting accents like badges and divider highlights.</small>
                    </label>

                    <label class="field-span-2">
                        College Logo
                        <input type="file" name="portal_logo" accept="image/png,image/jpeg,image/webp">
                        <small class="field-hint">PNG, JPG, or WebP up to 2 MB. Leave empty to keep the current logo.</small>
                    </label>

                    <div class="field-span-2 branding-preview-card">
                        <div class="branding-preview-copy">
                            <strong>{{ $brandingPortalTitle }}</strong>
                            <p class="section-hint" style="margin:0;">Preview the values you are about to save.</p>
                            <div class="branding-swatch-row">
                                <span class="branding-swatch">
                                    <span class="branding-swatch-sample" style="--swatch-color: {{ $brandingAccent }};"></span>
                                    Accent {{ strtoupper($brandingAccent) }}
                                </span>
                                <span class="branding-swatch">
                                    <span class="branding-swatch-sample" style="--swatch-color: {{ $brandingSecondary }};"></span>
                                    Secondary {{ strtoupper($brandingSecondary) }}
                                </span>
                            </div>
                        </div>

                        @if ($brandingSettings['logo_path'])
                            <img src="{{ asset($brandingSettings['logo_path']) }}" alt="{{ $brandingPortalTitle }} Logo" class="branding-logo-preview">
                        @else
                            <div class="branding-logo-fallback">{{ strtoupper($tenant->code ?: 'BK') }}</div>
                        @endif
                    </div>

                    <div class="field-span-2 modal-actions">
                        <button type="submit" class="button">Save Branding</button>
                        <button type="button" class="button secondary" data-modal-close>Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="ojt-settings-modal" class="modal-shell" hidden aria-hidden="true">
            <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="ojt-settings-modal-title">
                <div class="modal-head">
                    <div>
                        <h3 id="ojt-settings-modal-title">Update OJT Hours Settings</h3>
                        <p class="modal-copy">Save the college-wide fallback hours and policy that apply when students are not using a course-specific requirement.</p>
                    </div>
                    <button type="button" class="panel-close" data-modal-close aria-label="Close OJT settings modal">&times;</button>
                </div>

                <form method="POST" action="{{ $ojtSettingsAction }}" class="form-grid">
                    @csrf

                    <label class="field-span-2">
                        Default Required OJT Hours
                        <input type="number" name="default_ojt_hours" value="{{ old('default_ojt_hours', $ojtSettings['default_ojt_hours']) }}" min="1" max="9999" step="0.5" required>
                        <small class="field-hint">Applied to students who do not have a course assigned.</small>
                    </label>

                    <label class="field-span-2 checkbox-label">
                        <input type="hidden" name="allow_student_hour_override" value="0">
                        <input type="checkbox" name="allow_student_hour_override" value="1" {{ old('allow_student_hour_override', $ojtSettings['allow_student_hour_override']) ? 'checked' : '' }}>
                        Allow coordinators to manually override individual student OJT hours
                    </label>

                    <label class="field-span-2">
                        OJT Hours Note / Policy
                        <textarea name="ojt_hours_note" class="textarea-input" rows="3" maxlength="500" placeholder="e.g. Students must complete all hours in a single company. Split deployment requires Dean approval.">{{ old('ojt_hours_note', $ojtSettings['ojt_hours_note']) }}</textarea>
                    </label>

                    <div class="field-span-2 modal-actions">
                        <button type="submit" class="button">Save OJT Settings</button>
                        <button type="button" class="button secondary" data-modal-close>Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="course-create-modal" class="modal-shell" hidden aria-hidden="true">
            <div class="modal-card modal-card-wide" role="dialog" aria-modal="true" aria-labelledby="course-create-modal-title">
                <div class="modal-head">
                    <div>
                        <h3 id="course-create-modal-title">Add New Course</h3>
                        <p class="modal-copy">Create an official program entry for this college so coordinators can assign it directly to students.</p>
                    </div>
                    <button type="button" class="panel-close" data-modal-close aria-label="Close add course modal">&times;</button>
                </div>

                <form method="POST" action="{{ $courseStoreAction }}" class="form-grid">
                    @csrf
                    <input type="hidden" name="form_context" value="course-create">

                    <label>
                        Course Code
                        <input type="text" name="code" value="{{ $courseCreateHasErrors ? old('code') : '' }}" placeholder="e.g. BSIT" maxlength="30" required>
                        <small class="field-hint">Short identifier such as BSIT, BSCS, or BSCpE.</small>
                    </label>

                    <label>
                        Required OJT Hours
                        <input type="number" name="required_ojt_hours" value="{{ $courseCreateHasErrors ? old('required_ojt_hours', $ojtSettings['default_ojt_hours']) : $ojtSettings['default_ojt_hours'] }}" min="1" max="9999" step="0.5" required>
                    </label>

                    <label class="field-span-2">
                        Full Course Name
                        <input type="text" name="name" value="{{ $courseCreateHasErrors ? old('name') : '' }}" placeholder="e.g. Bachelor of Science in Information Technology" maxlength="255" required>
                    </label>

                    <label>
                        Sort Order
                        <input type="number" name="sort_order" value="{{ $courseCreateHasErrors ? old('sort_order', 0) : 0 }}" min="0">
                        <small class="field-hint">Lower numbers appear first.</small>
                    </label>

                    <label class="checkbox-label" style="align-self:center; margin-top:20px;">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ (string) old('is_active', '1') === '1' ? 'checked' : '' }}>
                        Active and selectable for new students
                    </label>

                    <div class="field-span-2 modal-actions">
                        <button type="submit" class="button">Save Course</button>
                        <button type="button" class="button secondary" data-modal-close>Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        @foreach ($courses as $course)
            @php
                $isEditingCourse = $courseEditHasErrors && (string) $editingCourseId === (string) $course->id;
                $editCourseIsActive = $isEditingCourse ? old('is_active', (int) $course->is_active) : (int) $course->is_active;
            @endphp
            <div id="course-edit-modal-{{ $course->id }}" class="modal-shell" hidden aria-hidden="true">
                <div class="modal-card modal-card-wide" role="dialog" aria-modal="true" aria-labelledby="course-edit-modal-title-{{ $course->id }}">
                    <div class="modal-head">
                        <div>
                            <h3 id="course-edit-modal-title-{{ $course->id }}">Edit {{ $course->code }}</h3>
                            <p class="modal-copy">Update the course details and required OJT hours for this official program entry.</p>
                        </div>
                        <button type="button" class="panel-close" data-modal-close aria-label="Close edit course modal">&times;</button>
                    </div>

                    <form method="POST" action="{{ $courseActions[$course->id]['update'] }}" class="form-grid">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="form_context" value="course-edit">
                        <input type="hidden" name="editing_course_id" value="{{ $course->id }}">

                        <label>
                            Code
                            <input type="text" name="code" value="{{ $isEditingCourse ? old('code', $course->code) : $course->code }}" maxlength="30" required>
                        </label>

                        <label>
                            OJT Hours
                            <input type="number" name="required_ojt_hours" value="{{ $isEditingCourse ? old('required_ojt_hours', $course->required_ojt_hours) : $course->required_ojt_hours }}" min="1" max="9999" step="0.5" required>
                        </label>

                        <label class="field-span-2">
                            Full Name
                            <input type="text" name="name" value="{{ $isEditingCourse ? old('name', $course->name) : $course->name }}" maxlength="255" required>
                        </label>

                        <label>
                            Sort Order
                            <input type="number" name="sort_order" value="{{ $isEditingCourse ? old('sort_order', $course->sort_order) : $course->sort_order }}" min="0">
                        </label>

                        <label class="checkbox-label" style="align-self:center; margin-top:20px;">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ (string) $editCourseIsActive === '1' ? 'checked' : '' }}>
                            Active
                        </label>

                        <div class="field-span-2 modal-actions">
                            <button type="submit" class="button">Update Course</button>
                            <button type="button" class="button secondary" data-modal-close>Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @endif

    <script>
        function syncModalState() {
            const hasOpenModal = document.querySelector('.modal-shell:not([hidden])');
            document.body.classList.toggle('modal-open', Boolean(hasOpenModal));
        }

        function openProfileModal(id) {
            const modal = document.getElementById(id);

            if (! modal) {
                return;
            }

            modal.hidden = false;
            modal.setAttribute('aria-hidden', 'false');
            syncModalState();

            const focusTarget = modal.querySelector('input:not([type="hidden"]):not([readonly]), select, textarea, button');

            if (focusTarget) {
                setTimeout(function () {
                    focusTarget.focus();
                }, 0);
            }
        }

        function closeProfileModal(modal) {
            if (! modal) {
                return;
            }

            modal.hidden = true;
            modal.setAttribute('aria-hidden', 'true');
            syncModalState();
        }

        document.addEventListener('click', function (event) {
            const openTrigger = event.target.closest('[data-modal-open]');

            if (openTrigger) {
                event.preventDefault();
                openProfileModal(openTrigger.getAttribute('data-modal-open'));
                return;
            }

            const closeTrigger = event.target.closest('[data-modal-close]');

            if (closeTrigger) {
                event.preventDefault();
                closeProfileModal(closeTrigger.closest('.modal-shell'));
                return;
            }

            if (event.target.classList.contains('modal-shell')) {
                closeProfileModal(event.target);
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') {
                return;
            }

            const openModals = Array.from(document.querySelectorAll('.modal-shell:not([hidden])'));
            const activeModal = openModals.pop();

            if (activeModal) {
                closeProfileModal(activeModal);
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const hasProfileInfoErrors = @json($profileInfoHasErrors);
            const hasPasswordErrors = @json($passwordHasErrors);
            const hasBrandingErrors = @json($brandingHasErrors);
            const hasOjtSettingsErrors = @json($ojtSettingsHasErrors);
            const hasCourseCreateErrors = @json($courseCreateHasErrors);
            const hasCourseEditErrors = @json($courseEditHasErrors);
            const editingCourseId = @json($editingCourseId);

            if (hasProfileInfoErrors) {
                openProfileModal('profile-info-modal');
            }

            if (hasPasswordErrors) {
                openProfileModal('password-modal');
            }

            if (hasBrandingErrors) {
                window.location.hash = 'portal-branding';
                openProfileModal('branding-modal');
            }

            if (hasOjtSettingsErrors) {
                window.location.hash = 'ojt-settings';
                openProfileModal('ojt-settings-modal');
            }

            if (hasCourseCreateErrors) {
                window.location.hash = 'courses';
                openProfileModal('course-create-modal');
            }

            if (hasCourseEditErrors && editingCourseId) {
                window.location.hash = 'courses';
                openProfileModal('course-edit-modal-' + editingCourseId);
            }
        });
    </script>
@endsection
