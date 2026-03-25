<x-email-shell
    eyebrow="Plan Application Received"
    title="Payment received, waiting for approval"
    subtitle="BukSU has recorded the Stripe test payment for {{ $application->college_name }}, but the tenant portal is not active yet."
>
    <p>Hello {{ $application->contact_name }},</p>

    <p>Your college plan application has been paid successfully and is now waiting for BukSU central admin approval.</p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:20px 0;border-collapse:separate;border-spacing:0 10px;">
        <tr>
            <td style="width:180px;padding:12px 14px;background:#f8f3ef;border:1px solid #eadfd6;border-radius:12px 0 0 12px;"><strong>College</strong></td>
            <td style="padding:12px 14px;background:#fffaf7;border:1px solid #eadfd6;border-left:0;border-radius:0 12px 12px 0;">{{ $application->college_name }}</td>
        </tr>
        <tr>
            <td style="padding:12px 14px;background:#f8f3ef;border:1px solid #eadfd6;border-radius:12px 0 0 12px;"><strong>Selected Plan</strong></td>
            <td style="padding:12px 14px;background:#fffaf7;border:1px solid #eadfd6;border-left:0;border-radius:0 12px 12px 0;">{{ strtoupper($application->selected_plan) }}</td>
        </tr>
        <tr>
            <td style="padding:12px 14px;background:#f8f3ef;border:1px solid #eadfd6;border-radius:12px 0 0 12px;"><strong>Payment Status</strong></td>
            <td style="padding:12px 14px;background:#fffaf7;border:1px solid #eadfd6;border-left:0;border-radius:0 12px 12px 0;">{{ strtoupper($application->payment_status) }}</td>
        </tr>
        <tr>
            <td style="padding:12px 14px;background:#f8f3ef;border:1px solid #eadfd6;border-radius:12px 0 0 12px;"><strong>Coordinator Email</strong></td>
            <td style="padding:12px 14px;background:#fffaf7;border:1px solid #eadfd6;border-left:0;border-radius:0 12px 12px 0;">{{ $application->admin_email }}</td>
        </tr>
    </table>

    <p>
        Important:
        the tenant database, college portal, and coordinator login credentials are <strong>not created yet</strong>.
        They are only created after BukSU central admin approves your request.
    </p>

    <p>Once approved, the coordinator credentials will be emailed automatically.</p>
</x-email-shell>
