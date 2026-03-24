@php
    $embedded = $embedded ?? false;
    $showHeading = $showHeading ?? true;
    $mode = $mode ?? 'create';
    $isEditing = $mode === 'edit' && filled($editingStudent ?? null);
    $studentRecord = $editingStudent ?? null;
    $action = $isEditing
        ? (request()->routeIs('tenant.domain.*')
            ? route('tenant.domain.admin.students.update', $studentRecord)
            : route('tenant.admin.students.update', ['tenant' => $tenant, 'student' => $studentRecord]))
        : $formActions['students'];
    $passwordFieldId = $isEditing ? 'edit-student-password' : 'new-student-password';
@endphp

@unless ($embedded)
<article class="card">
@endunless
    @if ($showHeading)
        <h2>{{ $isEditing ? 'Edit Student' : 'New Student' }}</h2>
    @endif
    <form method="POST" action="{{ $action }}">
        @csrf
        @if ($isEditing)
            @method('PATCH')
        @endif
        <label>Student Number <input type="text" name="student_number" value="{{ old('student_number', $studentRecord?->student_number) }}" required></label>
        <label>First Name <input type="text" name="first_name" value="{{ old('first_name', $studentRecord?->first_name) }}" required></label>
        <label>Last Name <input type="text" name="last_name" value="{{ old('last_name', $studentRecord?->last_name) }}" required></label>
        <label>Email <input type="email" name="email" value="{{ old('email', $studentRecord?->email) }}" required></label>
        <label>
            Password
            <div class="field-with-action">
                <input id="{{ $passwordFieldId }}" type="password" name="password" placeholder="{{ $isEditing ? 'Leave blank to keep current password' : 'Leave blank to auto-generate' }}">
                <button type="button" class="panel-link tiny-link" data-generate-password data-target="#{{ $passwordFieldId }}">Randomize</button>
            </div>
        </label>
        <label>Program <input type="text" name="program" value="{{ old('program', $studentRecord?->program ?? 'BS Information Technology') }}"></label>
        <label>Required Hours <input type="number" name="required_hours" min="1" value="{{ old('required_hours', $studentRecord?->required_hours ?? 486) }}" required></label>
        @if ($isEditing)
            <label>Completed Hours <input type="number" step="0.5" min="0" name="completed_hours" value="{{ old('completed_hours', $studentRecord?->completed_hours ?? 0) }}" required></label>
        @endif
        <label>
            Status
            <select name="status" required>
                @foreach ($studentStatuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $studentRecord?->status ?? 'pending') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </label>
        <label>
            Assigned Company
            <select name="partner_company_id">
                <option value="">Not yet assigned</option>
                @foreach ($companies as $company)
                    <option value="{{ $company->id }}" @selected((string) old('partner_company_id', $studentRecord?->partner_company_id) === (string) $company->id)>{{ $company->name }}</option>
                @endforeach
            </select>
        </label>
        @if ($isEditing)
            <label>
                Email Verification
                <select name="email_verified" required>
                    <option value="1" @selected((string) old('email_verified', (int) filled($studentRecord?->email_verified_at)) === '1')>Verified</option>
                    <option value="0" @selected((string) old('email_verified', (int) filled($studentRecord?->email_verified_at)) === '0')>Pending verification</option>
                </select>
            </label>
            <label>
                Access
                <select name="is_active" required>
                    <option value="1" @selected((string) old('is_active', (int) ($studentRecord?->is_active ?? true)) === '1')>Active</option>
                    <option value="0" @selected((string) old('is_active', (int) ($studentRecord?->is_active ?? true)) === '0')>Suspended</option>
                </select>
            </label>
        @endif
        <p class="field-hint">{{ $isEditing ? 'Leave password blank if you do not want to change it.' : 'Leave the password blank to auto-generate it, or click Randomize. The final password will be emailed to the student automatically.' }}</p>
        <button type="submit" class="small-button">{{ $isEditing ? 'Save Changes' : 'Save Student' }}</button>
    </form>
@unless ($embedded)
</article>
@endunless
