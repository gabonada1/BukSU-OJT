<x-email-shell
    eyebrow="Internship Coordinator Account"
    title="Your university portal account is ready"
    subtitle="University Administration has finished registering {{ $tenant->name }} and generated the first Internship Coordinator credentials for you."
>
    <p>Hello {{ $adminName }},</p>

    <p>You can now sign in to the university portal with the temporary credentials below.</p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:20px 0;border-collapse:separate;border-spacing:0 10px;">
        <tr>
            <td style="width:180px;padding:12px 14px;background:#f8f3ef;border:1px solid #eadfd6;border-radius:12px 0 0 12px;"><strong>College</strong></td>
            <td style="padding:12px 14px;background:#fffaf7;border:1px solid #eadfd6;border-left:0;border-radius:0 12px 12px 0;">{{ $tenant->name }}</td>
        </tr>
        <tr>
            <td style="padding:12px 14px;background:#f8f3ef;border:1px solid #eadfd6;border-radius:12px 0 0 12px;"><strong>Coordinator Email</strong></td>
            <td style="padding:12px 14px;background:#fffaf7;border:1px solid #eadfd6;border-left:0;border-radius:0 12px 12px 0;">{{ $email }}</td>
        </tr>
        <tr>
            <td style="padding:12px 14px;background:#f8f3ef;border:1px solid #eadfd6;border-radius:12px 0 0 12px;"><strong>Temporary Password</strong></td>
            <td style="padding:12px 14px;background:#fffaf7;border:1px solid #eadfd6;border-left:0;border-radius:0 12px 12px 0;">{{ $password }}</td>
        </tr>
        <tr>
            <td style="padding:12px 14px;background:#f8f3ef;border:1px solid #eadfd6;border-radius:12px 0 0 12px;"><strong>Portal Login URL</strong></td>
            <td style="padding:12px 14px;background:#fffaf7;border:1px solid #eadfd6;border-left:0;border-radius:0 12px 12px 0;"><a href="{{ $loginUrl }}" style="color:#7B1C2E;">{{ $loginUrl }}</a></td>
        </tr>
    </table>

    <p style="margin:22px 0;">
        <a href="{{ $loginUrl }}" style="display:inline-block;padding:12px 18px;background:#7B1C2E;color:#ffffff;text-decoration:none;border-radius:999px;font-weight:700;">Open University Portal</a>
    </p>

    <p>After your first sign in, the system will require you to create a new password before you can continue.</p>
</x-email-shell>
