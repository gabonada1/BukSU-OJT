<x-email-shell
    eyebrow="Student Account"
    title="Your student account is ready"
    subtitle="A tenant administrator created your practicum account in {{ $tenant->name }} and issued a temporary password."
>
    <p>Hello {{ $student->full_name }},</p>

    <p>You can sign in right away using the credentials below.</p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:20px 0;border-collapse:separate;border-spacing:0 10px;">
        <tr>
            <td style="width:180px;padding:12px 14px;background:#f8f3ef;border:1px solid #eadfd6;border-radius:12px 0 0 12px;"><strong>Tenant</strong></td>
            <td style="padding:12px 14px;background:#fffaf7;border:1px solid #eadfd6;border-left:0;border-radius:0 12px 12px 0;">{{ $tenant->name }}</td>
        </tr>
        <tr>
            <td style="padding:12px 14px;background:#f8f3ef;border:1px solid #eadfd6;border-radius:12px 0 0 12px;"><strong>Email / Username</strong></td>
            <td style="padding:12px 14px;background:#fffaf7;border:1px solid #eadfd6;border-left:0;border-radius:0 12px 12px 0;">{{ $student->email }}</td>
        </tr>
        <tr>
            <td style="padding:12px 14px;background:#f8f3ef;border:1px solid #eadfd6;border-radius:12px 0 0 12px;"><strong>Temporary Password</strong></td>
            <td style="padding:12px 14px;background:#fffaf7;border:1px solid #eadfd6;border-left:0;border-radius:0 12px 12px 0;">{{ $plainPassword }}</td>
        </tr>
        <tr>
            <td style="padding:12px 14px;background:#f8f3ef;border:1px solid #eadfd6;border-radius:12px 0 0 12px;"><strong>Login URL</strong></td>
            <td style="padding:12px 14px;background:#fffaf7;border:1px solid #eadfd6;border-left:0;border-radius:0 12px 12px 0;"><a href="{{ $loginUrl }}" style="color:#991b1b;">{{ $loginUrl }}</a></td>
        </tr>
    </table>

    <p style="margin:22px 0;">
        <a href="{{ $loginUrl }}" style="display:inline-block;padding:12px 18px;background:#991b1b;color:#ffffff;text-decoration:none;border-radius:999px;font-weight:700;">Open Student Login</a>
    </p>

    <p>Your account has already been verified by the tenant admin, so you can sign in immediately.</p>
</x-email-shell>
