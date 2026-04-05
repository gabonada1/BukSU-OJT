<x-email-shell
    eyebrow="Student Verification"
    title="Verify your student registration"
    subtitle="Finish activating your University Practicum student account for {{ $tenant->name }} by confirming your email address."
>
    <p>Hello {{ $student->full_name }},</p>

    <p>Please verify your email address to activate your student login.</p>

    <p class="email-button-wrap">
        <a href="{{ $verificationUrl }}" class="email-button">Verify Email Address</a>
    </p>

    <p>If the button above does not open, copy and paste this link into your browser:</p>
    <p><a href="{{ $verificationUrl }}" >{{ $verificationUrl }}</a></p>

    <p>After verification, you can sign in here:</p>
    <p><a href="{{ $loginUrl }}" >{{ $loginUrl }}</a></p>
</x-email-shell>

