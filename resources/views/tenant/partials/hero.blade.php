@php
    $tenantBranding = is_array($tenant->settings['branding'] ?? null) ? $tenant->settings['branding'] : [];
    $tenantPortalTitle = filled($tenantBranding['portal_title'] ?? null)
        ? $tenantBranding['portal_title']
        : config('app.name', 'University Practicum');
@endphp

<section >
    <div >{{ $tenantPortalTitle }} | Active College: {{ $tenant->code }}</div>
    <h1>{{ $tenant->name }} OJT Portal</h1>
    <p >
        {{ $tenant->name }} runs on {{ $tenantPortalTitle }} with an isolated university database for its own
        partner organizations, students, practicum requirements, and OJT hour logs.
    </p>

    <div >
        @foreach ($stats as $label => $value)
            <div >
                <strong>{{ str_replace('_', ' ', ucfirst($label)) }}</strong>
                <span>{{ $value }}</span>
            </div>
        @endforeach
    </div>

    @if (session('status'))
        <div >{{ session('status') }}</div>
    @endif
</section>

