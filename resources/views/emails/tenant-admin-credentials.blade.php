<x-email-shell
    eyebrow="Tenant Admin Account"
    title="Your tenant admin account is ready"
    subtitle="The central BukSU app has finished provisioning {{ $tenant->name }} and generated the first tenant admin credentials for you."
>
    <p>Hello {{ $adminName }},</p>

    <p>You can now sign in to the tenant workspace with the credentials below.</p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:20px 0;border-collapse:separate;border-spacing:0 10px;">
        <tr>
            <td style="width:180px;padding:12px 14px;background:#f8f3ef;border:1px solid #eadfd6;border-radius:12px 0 0 12px;"><strong>Tenant</strong></td>
            <td style="padding:12px 14px;background:#fffaf7;border:1px solid #eadfd6;border-left:0;border-radius:0 12px 12px 0;">{{ $tenant->name }}</td>
        </tr>
        <tr>
            <td style="padding:12px 14px;background:#f8f3ef;border:1px solid #eadfd6;border-radius:12px 0 0 12px;"><strong>Username / Email</strong></td>
            <td style="padding:12px 14px;background:#fffaf7;border:1px solid #eadfd6;border-left:0;border-radius:0 12px 12px 0;">{{ $email }}</td>
        </tr>
        <tr>
            <td style="padding:12px 14px;background:#f8f3ef;border:1px solid #eadfd6;border-radius:12px 0 0 12px;"><strong>Temporary Password</strong></td>
            <td style="padding:12px 14px;background:#fffaf7;border:1px solid #eadfd6;border-left:0;border-radius:0 12px 12px 0;">{{ $password }}</td>
        </tr>
        <tr>
            <td style="padding:12px 14px;background:#f8f3ef;border:1px solid #eadfd6;border-radius:12px 0 0 12px;"><strong>Login URL</strong></td>
            <td style="padding:12px 14px;background:#fffaf7;border:1px solid #eadfd6;border-left:0;border-radius:0 12px 12px 0;"><a href="{{ $loginUrl }}" style="color:#991b1b;">{{ $loginUrl }}</a></td>
        </tr>
    </table>

    <p style="margin:22px 0;">
        <a href="{{ $loginUrl }}" style="display:inline-block;padding:12px 18px;background:#991b1b;color:#ffffff;text-decoration:none;border-radius:999px;font-weight:700;">Open Tenant Login</a>
    </p>

    <p>Please sign in and change your password as soon as possible.</p>
</x-email-shell>
