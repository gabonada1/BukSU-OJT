<x-email-shell
    eyebrow="College Subscription Updated"
    title="Your college subscription details were updated"
    subtitle="{{ $tenant->name }} has updated plan or renewal details from the BukSU central administration layer."
>
    <p>Hello {{ $recipientName }},</p>

    <p>The BukSU University Admin updated your college portal subscription settings.</p>

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
            <td style="padding:12px 14px;background:#f8f3ef;border:1px solid #eadfd6;border-radius:12px 0 0 12px;"><strong>Subscription Starts</strong></td>
            <td style="padding:12px 14px;background:#fffaf7;border:1px solid #eadfd6;border-left:0;border-radius:0 12px 12px 0;">{{ $tenant->subscription_starts_at?->format('F d, Y') ?: 'Not set' }}</td>
        </tr>
        <tr>
            <td style="padding:12px 14px;background:#f8f3ef;border:1px solid #eadfd6;border-radius:12px 0 0 12px;"><strong>Subscription Expires</strong></td>
            <td style="padding:12px 14px;background:#fffaf7;border:1px solid #eadfd6;border-left:0;border-radius:0 12px 12px 0;">{{ $tenant->subscription_expires_at?->format('F d, Y') ?: 'Open-ended' }}</td>
        </tr>
    </table>

    <p>Review these dates with your practicum team to avoid any interruption to portal access.</p>
</x-email-shell>
