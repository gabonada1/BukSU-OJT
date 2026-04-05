@props([
    'eyebrow' => null,
    'title' => '',
    'subtitle' => null,
])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?: config('app.name', 'University Practicum') }}</title>
</head>
<body style="margin:0;background:#f2eff4;padding:32px 16px;font-family:Arial, Helvetica, sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;margin:0 auto;">
        <tr>
            <td style="padding:0;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#ffffff;border:1px solid #e4d7dd;border-radius:20px;overflow:hidden;">
                    <tr>
                        <td style="padding:24px 28px;background:linear-gradient(135deg, #5E1423, #7B1C2E);color:#fff7ed;">
                            <p style="margin:0 0 10px;font-size:11px;letter-spacing:0.24em;text-transform:uppercase;opacity:0.9;">{{ $eyebrow ?: 'University Practicum' }}</p>
                            <h1 style="margin:0;font-size:28px;line-height:1.2;font-weight:700;">{{ $title }}</h1>
                            @if ($subtitle)
                                <p style="margin:12px 0 0;font-size:15px;line-height:1.6;color:#f7dca1;">{{ $subtitle }}</p>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;background:#ffffff;">
                            <div style="font-size:15px;line-height:1.7;color:#334155;">
                                {{ $slot }}
                            </div>
                            <p style="margin:28px 0 0 0;font-size:13px;line-height:1.6;color:#6b7280;">University Practicum</p>
                            <p style="margin:4px 0 0;font-size:13px;line-height:1.6;color:#6b7280;">Bukidnon State University - Office of Academic and Industrial Coordination</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
