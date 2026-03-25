@php
    $embedded = $embedded ?? false;
    $showHeading = $showHeading ?? true;
    $mode = $mode ?? 'create';
    $isEditing = $mode === 'edit' && filled($editingStudent ?? null);
    $studentRecord = $editingStudent ?? null;
    $action = $isEditing
        ? route('tenant.admin.students.update', ['student' => $studentRecord])
        : $formActions['students'];
    $passwordFieldId = $isEditing ? 'edit-student-password' : 'new-student-password';
    $activeCourses = collect($courses ?? []);
    $selectedCourseId = (string) old('course_id', $studentRecord?->course_id ?? '');
    $selectedInactiveCourse = $studentRecord?->course && ! $activeCourses->contains('id', $studentRecord->course->id)
        ? $studentRecord->course
        : null;
    $hasCourses = $activeCourses->isNotEmpty() || filled($selectedInactiveCourse);
    $allowHourOverride = (bool) ($ojtSettings['allow_student_hour_override'] ?? false);
    $defaultOjtHours = $ojtSettings['default_ojt_hours'] ?? 486;
@endphp

@unless ($embedded)
<article class="card">
@endunless
    @if ($showHeading)
        <h2>{{ $isEditing ? 'Edit Student' : 'New Student' }}</h2>
    @endif
    <form
        method="POST"
        action="{{ $action }}"
        @if ($hasCourses)
            data-course-hours-form="true"
            data-default-hours="{{ $defaultOjtHours }}"
            data-allow-override="{{ $allowHourOverride ? '1' : '0' }}"
        @endif
    >
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

        @if ($hasCourses)
            <label class="field-span-2">
                Course / Program
                <select name="course_id" class="select-input" data-course-select>
                    <option value="">- Select Course -</option>

                    @if ($selectedInactiveCourse)
                        <option value="{{ $selectedInactiveCourse->id }}" data-hours="{{ $selectedInactiveCourse->required_ojt_hours }}" @selected($selectedCourseId === (string) $selectedInactiveCourse->id)>
                            {{ $selectedInactiveCourse->code }} - {{ $selectedInactiveCourse->name }} (inactive)
                        </option>
                    @endif

                    @foreach ($activeCourses as $course)
                        <option value="{{ $course->id }}" data-hours="{{ $course->required_ojt_hours }}" @selected($selectedCourseId === (string) $course->id)>
                            {{ $course->code }} - {{ $course->name }} ({{ number_format($course->required_ojt_hours, 0) }} hrs)
                        </option>
                    @endforeach
                </select>
                <small class="field-hint">Selecting a course auto-fills the required OJT hours based on the course setting.</small>

                @if ($studentRecord?->program && ! $selectedCourseId)
                    <small class="field-hint">Legacy program on file: {{ $studentRecord->program }}</small>
                @endif
            </label>

            <label>
                Required OJT Hours
                <input type="number" name="required_hours" data-required-hours value="{{ old('required_hours', $studentRecord?->required_hours ?? $defaultOjtHours) }}" min="1" max="9999" step="0.5" {{ $allowHourOverride ? '' : 'readonly' }}>
                @if ($allowHourOverride)
                    <small class="field-hint">You can keep the course default or adjust this student individually.</small>
                @else
                    <small class="field-hint">This stays synced to the selected course or the college default.</small>
                @endif
            </label>
        @else
            <label class="field-span-2">
                Program / Course
                <input type="text" name="program" value="{{ old('program', $studentRecord?->program ?? '') }}" placeholder="e.g. BSIT, BSCpE, BSCS">
                <small class="field-hint">No active courses are configured yet. Set them up in Profile -> Courses & Programs.</small>
            </label>

            <label>
                Required OJT Hours
                <input type="number" name="required_hours" value="{{ old('required_hours', $studentRecord?->required_hours ?? $defaultOjtHours) }}" min="1" max="9999" step="0.5">
            </label>
        @endif

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
            Assigned Organization
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

    @if ($hasCourses)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('form[data-course-hours-form="true"]').forEach(function (form) {
                    const courseSelect = form.querySelector('[data-course-select]');
                    const requiredHoursField = form.querySelector('[data-required-hours]');
                    const allowOverride = form.dataset.allowOverride === '1';
                    const defaultHours = form.dataset.defaultHours;

                    if (! courseSelect || ! requiredHoursField) {
                        return;
                    }

                    courseSelect.addEventListener('change', function () {
                        const selectedOption = courseSelect.options[courseSelect.selectedIndex];
                        const selectedHours = selectedOption ? selectedOption.dataset.hours : null;

                        if (selectedHours) {
                            requiredHoursField.value = parseFloat(selectedHours);
                        } else if (defaultHours) {
                            requiredHoursField.value = parseFloat(defaultHours);
                        }

                        if (! allowOverride) {
                            requiredHoursField.setAttribute('readonly', 'readonly');
                        }
                    });
                });
            });
        </script>
    @endif
@unless ($embedded)
</article>
@endunless
