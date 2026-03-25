<x-email-shell
    eyebrow="College Portal Suspended"
    title="College portal access has been suspended"
    subtitle="{{ $tenant->name }} can no longer access the college portal until the license is restored."
>
    <p>Hello {{ $recipientName }},</p>

    <p>Your college portal access is currently suspended.</p>

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
            <td style="padding:12px 14px;background:#fffaf7;border:1px solid #eadfd6;border-left:0;border-radius:0 12px 12px 0;">Suspended</td>
        </tr>
        <tr>
            <td style="padding:12px 14px;background:#f8f3ef;border:1px solid #eadfd6;border-radius:12px 0 0 12px;"><strong>License Expiry</strong></td>
            <td style="padding:12px 14px;background:#fffaf7;border:1px solid #eadfd6;border-left:0;border-radius:0 12px 12px 0;">{{ $tenant->subscription_expires_at?->format('F d, Y') ?: 'Open-ended' }}</td>
        </tr>
    </table>

    <p>Please contact the BukSU University Admin to reactivate the college portal.</p>
</x-email-shell>
