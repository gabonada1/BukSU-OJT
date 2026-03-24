@php
    $embedded = $embedded ?? false;
    $showHeading = $showHeading ?? true;
    $mode = $mode ?? 'create';
    $isEditing = $mode === 'edit' && filled($editingApplication ?? null);
    $applicationRecord = $editingApplication ?? null;
    $action = $isEditing
        ? (request()->routeIs('tenant.domain.*')
            ? route('tenant.domain.admin.applications.update', $applicationRecord)
            : route('tenant.admin.applications.update', ['tenant' => $tenant, 'application' => $applicationRecord]))
        : $formActions['applications'];
@endphp

@unless ($embedded)
<article class="card">
@endunless
    @if ($showHeading)
        <h2>{{ $isEditing ? 'Edit Application' : 'New Application' }}</h2>
    @endif
    <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
        @csrf
        @if ($isEditing)
            @method('PATCH')
        @endif
        <label>
            Student
            <select name="student_id" required>
                @foreach ($students as $student)
                    <option value="{{ $student->id }}" @selected((string) old('student_id', $applicationRecord?->student_id) === (string) $student->id)>{{ $student->full_name }} ({{ $student->student_number }})</option>
                @endforeach
            </select>
        </label>
        <label>
            Partner Company
            <select name="partner_company_id" required>
                @foreach ($companies as $company)
                    <option value="{{ $company->id }}" @selected((string) old('partner_company_id', $applicationRecord?->partner_company_id) === (string) $company->id)>{{ $company->name }}</option>
                @endforeach
            </select>
        </label>
        <label>Position Applied <input type="text" name="position_applied" value="{{ old('position_applied', $applicationRecord?->position_applied) }}" required></label>
        <label>
            Status
            <select name="status" required>
                @foreach ($applicationStatuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $applicationRecord?->status ?? 'pending') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </label>
        <label>Student Notes <textarea name="student_notes" placeholder="Application notes, preferred schedule, or placement context">{{ old('student_notes', $applicationRecord?->student_notes) }}</textarea></label>
        <label>Admin Feedback <textarea name="admin_feedback" placeholder="Approval notes, rejection reason, or deployment remarks">{{ old('admin_feedback', $applicationRecord?->admin_feedback) }}</textarea></label>
        <label>Resume <input type="file" name="resume" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></label>
        <label>Endorsement Letter <input type="file" name="endorsement_letter" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></label>
        <label>MOA <input type="file" name="moa" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></label>
        <label>Clearance <input type="file" name="clearance" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></label>
        <button type="submit" class="small-button">{{ $isEditing ? 'Save Changes' : 'Save Application' }}</button>
    </form>
@unless ($embedded)
</article>
@endunless
