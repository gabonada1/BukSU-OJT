@php
    $embedded = $embedded ?? false;
    $showHeading = $showHeading ?? true;
    $mode = $mode ?? 'create';
    $isEditing = $mode === 'edit' && filled($editingRequirement ?? null);
    $requirementRecord = $editingRequirement ?? null;
    $action = $isEditing
        ? (request()->routeIs('tenant.domain.*')
            ? route('tenant.domain.admin.requirements.update', $requirementRecord)
            : route('tenant.admin.requirements.update', ['tenant' => $tenant, 'requirement' => $requirementRecord]))
        : $formActions['requirements'];
@endphp

@unless ($embedded)
<article class="card">
@endunless
    @if ($showHeading)
        <h2>{{ $isEditing ? 'Edit Requirement' : 'New Requirement' }}</h2>
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
                    <option value="{{ $student->id }}" @selected((string) old('student_id', $requirementRecord?->student_id) === (string) $student->id)>{{ $student->full_name }} ({{ $student->student_number }})</option>
                @endforeach
            </select>
        </label>
        <label>Requirement Name <input type="text" name="requirement_name" value="{{ old('requirement_name', $requirementRecord?->requirement_name ?? 'Resume') }}" required></label>
        <label>
            Status
            <select name="status" required>
                @foreach ($requirementStatuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $requirementRecord?->status ?? 'submitted') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </label>
        <label>Document File <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></label>
        <label>Notes <textarea name="notes" placeholder="Feedback, comments, or revision instructions">{{ old('notes', $requirementRecord?->notes) }}</textarea></label>
        <label>Admin Feedback <textarea name="feedback" placeholder="Approved, rejected, or requires revision notes">{{ old('feedback', $requirementRecord?->feedback) }}</textarea></label>
        <button type="submit" class="small-button">{{ $isEditing ? 'Save Changes' : 'Save Requirement' }}</button>
    </form>
@unless ($embedded)
</article>
@endunless
