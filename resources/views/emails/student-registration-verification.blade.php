<x-email-shell
    eyebrow="Student Verification"
    title="Verify your student registration"
    subtitle="Finish activating your practicum student account for {{ $tenant->name }} by confirming your email address."
>
    <p>Hello {{ $student->full_name }},</p>

    <p>Please verify your email address to activate your student login.</p>

    <p style="margin:22px 0;">
        <a href="{{ $verificationUrl }}" style="display:inline-block;padding:12px 18px;background:#991b1b;color:#ffffff;text-decoration:none;border-radius:999px;font-weight:700;">Verify Email Address</a>
    </p>

    <p>If the button above does not open, copy and paste this link into your browser:</p>
    <p><a href="{{ $verificationUrl }}" style="color:#991b1b;">{{ $verificationUrl }}</a></p>

    <p>After verification, you can sign in here:</p>
    <p><a href="{{ $loginUrl }}" style="color:#991b1b;">{{ $loginUrl }}</a></p>
</x-email-shell>
