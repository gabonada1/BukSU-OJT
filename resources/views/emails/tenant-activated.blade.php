<x-email-shell
    eyebrow="College Portal Activated"
    title="College portal access has been restored"
    subtitle="{{ $tenant->name }} is active again and its coordinator accounts can sign in to the portal."
>
    <p>Hello {{ $recipientName }},</p>

    <p>The BukSU University Admin has reactivated your college portal.</p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:20px 0;border-collapse:separate;border-spacing:0 10px;">
        <tr>
            <td style="width:180px;padding:12px 14px;background:#f8f3ef;border:1px solid #eadfd6;border-radius:12px 0 0 12px;"><strong>College</strong></td>
            <td style="padding:12px 14px;background:#fffaf7;border:1px solid #eadfd6;border-left:0;border-radius:0 12px 12px 0;">{{ $tenant->name }}</td>
        </tr>
        <tr>
            <td style="padding:12px 14px;background:#f8f3ef;border:1px solid #eadfd6;border-radius:12px 0 0 12px;"><strong>License Tier</strong></td>
            <td style="padding:12px 14px;background:#fffaf7;border:1px solid #eadfd6;border-left:0;border-radius:0 12px 12px 0;">{{ strtoupper($tenant->plan) }}</td>
        </tr>
        <tr>
            <td style="padding:12px 14px;background:#f8f3ef;border:1px solid #eadfd6;border-radius:12px 0 0 12px;"><strong>Status</strong></td>
            <td style="padding:12px 14px;background:#fffaf7;border:1px solid #eadfd6;border-left:0;border-radius:0 12px 12px 0;">Active</td>
        </tr>
        <tr>
            <td style="padding:12px 14px;background:#f8f3ef;border:1px solid #eadfd6;border-radius:12px 0 0 12px;"><strong>Subscription Expires</strong></td>
            <td style="padding:12px 14px;background:#fffaf7;border:1px solid #eadfd6;border-left:0;border-radius:0 12px 12px 0;">{{ $tenant->subscription_expires_at?->format('F d, Y') ?: 'Open-ended' }}</td>
        </tr>
    </table>

    <p>You may sign in again and continue managing your practicum portal.</p>
</x-email-shell>
