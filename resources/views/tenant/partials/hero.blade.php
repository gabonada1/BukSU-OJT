<section class="hero">
    <div class="eyebrow">Centralized BukSU Platform | Active Tenant: {{ $tenant->code }}</div>
    <h1>{{ $tenant->name }} Practicum Placement System</h1>
    <p class="lead">
        The College of Technologies now runs on a centralized BukSU app with an isolated tenant database for
        its own companies, students, requirements, and OJT hour logs.
    </p>

    <div class="stats">
        @foreach ($stats as $label => $value)
            <div class="card stat">
                <strong>{{ str_replace('_', ' ', ucfirst($label)) }}</strong>
                <span>{{ $value }}</span>
            </div>
        @endforeach
    </div>

    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif
</section>
