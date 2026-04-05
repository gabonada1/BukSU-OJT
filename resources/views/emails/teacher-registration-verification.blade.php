<x-email-shell
    eyebrow="Company Supervisor Verification"
    title="Verify your company supervisor registration"
    subtitle="Confirm your email address to activate your University Practicum company supervisor account for {{ $tenant->name }}."
>
    <p>Hello {{ $teacher->name }},</p>

    <p>Please verify your email address to activate your company supervisor account.</p>

    <p class="email-button-wrap">
        <a href="{{ $verificationUrl }}" class="email-button">Verify Email Address</a>
    </p>

    <p>If the button above does not open, copy and paste this link into your browser:</p>
    <p><a href="{{ $verificationUrl }}" >{{ $verificationUrl }}</a></p>

    <p>After verification, you can sign in here:</p>
    <p><a href="{{ $loginUrl }}" >{{ $loginUrl }}</a></p>
</x-email-shell>

