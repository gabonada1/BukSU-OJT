@php
    $embedded = $embedded ?? false;
    $showHeading = $showHeading ?? true;
    $mode = $mode ?? 'create';
    $isEditing = $mode === 'edit' && filled($editingCompany ?? null);
    $company = $editingCompany ?? null;
    $documentOptions = ['Resume', 'Endorsement Letter', 'MOA', 'Clearance', 'Weekly Report', 'Monthly Report', 'Medical Certificate', 'Parent Consent'];
    $selectedDocuments = old(
        'required_documents',
        $company ? $company->requiredDocumentsList() : []
    );
    $action = $isEditing
        ? route('tenant.admin.companies.update', ['company' => $company])
        : $formActions['companies'];
@endphp

@unless ($embedded)
<article class="card">
@endunless
    @if ($showHeading)
        <h2>{{ $isEditing ? 'Edit Partner Organization' : 'New Partner Organization' }}</h2>
    @endif
    <form method="POST" action="{{ $action }}">
        @csrf
        @if ($isEditing)
            @method('PATCH')
        @endif
        <label>Organization Name <input type="text" name="name" value="{{ old('name', $company?->name) }}" required></label>
        <label>Industry / Type <input type="text" name="industry" value="{{ old('industry', $company?->industry) }}"></label>
        <label>Available Positions <textarea name="available_positions" placeholder="One position per line">{{ old('available_positions', $company?->available_positions) }}</textarea></label>
        <fieldset class="checkbox-group-card">
            <legend>Required Documents</legend>
            <div class="checkbox-grid">
                @foreach ($documentOptions as $documentOption)
                    <label class="checkbox-label">
                        <input type="checkbox" name="required_documents[]" value="{{ $documentOption }}" @checked(in_array($documentOption, $selectedDocuments, true))>
                        <span>{{ $documentOption }}</span>
                    </label>
                @endforeach
            </div>
        </fieldset>
        <label>Address <input type="text" name="address" value="{{ old('address', $company?->address) }}"></label>
        <label>Contact Person <input type="text" name="contact_person" value="{{ old('contact_person', $company?->contact_person) }}"></label>
        <label>Contact Email <input type="email" name="contact_email" value="{{ old('contact_email', $company?->contact_email) }}"></label>
        <label>Contact Phone <input type="text" name="contact_phone" value="{{ old('contact_phone', $company?->contact_phone) }}"></label>
        <label>OJT Slot Limit <input type="number" name="intern_slot_limit" min="1" value="{{ old('intern_slot_limit', $company?->intern_slot_limit ?? 10) }}" required></label>
        <button type="submit" class="small-button">{{ $isEditing ? 'Save Changes' : 'Save Organization' }}</button>
    </form>
@unless ($embedded)
</article>
@endunless
