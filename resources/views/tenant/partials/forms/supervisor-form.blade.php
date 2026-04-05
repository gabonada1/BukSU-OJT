@php
    $embedded = $embedded ?? false;
    $showHeading = $showHeading ?? true;
    $mode = $mode ?? 'create';
    $isEditing = $mode === 'edit' && filled($editingSupervisor ?? null);
    $supervisorRecord = $editingSupervisor ?? null;
    $action = $isEditing
        ? route('tenant.admin.supervisors.update', ['supervisor' => $supervisorRecord])
        : $formActions['supervisors'];
    $departmentOptions = collect($courses ?? [])->map(function ($course) {
        return [
            'value' => trim($course->code.' - '.$course->name),
            'label' => trim($course->code.' - '.$course->name),
        ];
    });
    $selectedDepartment = old('department', $supervisorRecord?->department ?? '');
    $hasDepartmentOptions = $departmentOptions->isNotEmpty();
    $selectedDepartmentMissing = filled($selectedDepartment)
        && ! $departmentOptions->contains(fn ($option) => $option['value'] === $selectedDepartment);
@endphp

@unless ($embedded)
<article >
@endunless
    @if ($showHeading)
        <h2>{{ $isEditing ? 'Edit Company Supervisor' : 'New Company Supervisor' }}</h2>
    @endif
    <form method="POST" action="{{ $action }}">
        @csrf
        @if ($isEditing)
            @method('PATCH')
        @endif
        <label>Name <input type="text" name="name" value="{{ old('name', $supervisorRecord?->name) }}" required></label>
        <label>Email <input type="email" name="email" value="{{ old('email', $supervisorRecord?->email) }}" required></label>
        <label>Position <input type="text" name="position" value="{{ old('position', $supervisorRecord?->position ?? 'Company Supervisor') }}"></label>
        @if ($hasDepartmentOptions)
            <label>
                Department / Unit
                <select name="department">
                    <option value="">Select course</option>
                    @if ($selectedDepartmentMissing)
                        <option value="{{ $selectedDepartment }}" selected>{{ $selectedDepartment }} (Current)</option>
                    @endif
                    @foreach ($departmentOptions as $option)
                        <option value="{{ $option['value'] }}" @selected($selectedDepartment === $option['value'])>{{ $option['label'] }}</option>
                    @endforeach
                </select>
            </label>
        @else
            <label>Department / Unit <input type="text" name="department" value="{{ old('department', $supervisorRecord?->department ?? $tenant->name) }}"></label>
        @endif
        <label>
            Organization
            <select name="partner_company_id">
                <option value="">Assign later</option>
                @foreach ($companies as $company)
                    <option value="{{ $company->id }}" @selected((string) old('partner_company_id', $supervisorRecord?->partner_company_id) === (string) $company->id)>{{ $company->name }}</option>
                @endforeach
            </select>
        </label>
        <label>Password <input type="password" name="password" {{ $isEditing ? '' : 'required' }}></label>
        @if ($isEditing)
            <label>
                Email Verification
                <select name="email_verified" required>
                    <option value="1" @selected((string) old('email_verified', (int) filled($supervisorRecord?->email_verified_at)) === '1')>Verified</option>
                    <option value="0" @selected((string) old('email_verified', (int) filled($supervisorRecord?->email_verified_at)) === '0')>Pending verification</option>
                </select>
            </label>
            <label>
                Access
                <select name="is_active" required>
                    <option value="1" @selected((string) old('is_active', (int) ($supervisorRecord?->is_active ?? true)) === '1')>Active</option>
                    <option value="0" @selected((string) old('is_active', (int) ($supervisorRecord?->is_active ?? true)) === '0')>Suspended</option>
                </select>
            </label>
        @endif
        <button type="submit" >{{ $isEditing ? 'Save Changes' : 'Save Company Supervisor' }}</button>
    </form>
@unless ($embedded)
</article>
@endunless

