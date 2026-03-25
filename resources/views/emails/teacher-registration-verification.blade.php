<x-email-shell
    eyebrow="Company Supervisor Verification"
    title="Verify your company supervisor registration"
    subtitle="Confirm your email address to activate your BukSU Practicum Portal company supervisor account for {{ $tenant->name }}."
>
    <p>Hello {{ $teacher->name }},</p>

    <p>Please verify your email address to activate your company supervisor account.</p>

    <p style="margin:22px 0;">
        <a href="{{ $verificationUrl }}" style="display:inline-block;padding:12px 18px;background:#7B1C2E;color:#ffffff;text-decoration:none;border-radius:999px;font-weight:700;">Verify Email Address</a>
    </p>

    <p>If the button above does not open, copy and paste this link into your browser:</p>
    <p><a href="{{ $verificationUrl }}" style="color:#7B1C2E;">{{ $verificationUrl }}</a></p>

    <p>After verification, you can sign in here:</p>
    <p><a href="{{ $loginUrl }}" style="color:#7B1C2E;">{{ $loginUrl }}</a></p>
</x-email-shell>
