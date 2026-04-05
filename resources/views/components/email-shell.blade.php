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
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #0b1328;
            color: #d9e4ff;
            font-family: Arial, Helvetica, sans-serif;
        }

        table {
            border-collapse: collapse;
        }

        .email-shell {
            width: 100%;
            background:
                radial-gradient(circle at top left, rgba(93, 108, 250, 0.20), transparent 30%),
                linear-gradient(180deg, #0d1730 0%, #081121 100%);
        }

        .email-card {
            width: 100%;
            max-width: 640px;
            margin: 0 auto;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid rgba(132, 154, 215, 0.18);
            background: #121c36;
            box-shadow: 0 24px 60px rgba(3, 9, 24, 0.38);
        }

        .email-hero {
            padding: 36px 40px 28px;
            background:
                radial-gradient(circle at top right, rgba(111, 82, 186, 0.32), transparent 38%),
                linear-gradient(135deg, #16254d 0%, #101a33 55%, #17122d 100%);
        }

        .email-eyebrow {
            display: inline-block;
            margin: 0 0 18px;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid rgba(118, 142, 226, 0.28);
            background: rgba(55, 77, 146, 0.34);
            color: #dbe7ff;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.24em;
            text-transform: uppercase;
        }

        .email-title {
            margin: 0;
            color: #f7faff;
            font-size: 32px;
            line-height: 1.15;
            font-weight: 700;
        }

        .email-subtitle {
            margin: 14px 0 0;
            color: #b3c2e7;
            font-size: 15px;
            line-height: 1.7;
        }

        .email-body {
            padding: 32px 40px 18px;
            color: #d8e3fb;
            font-size: 15px;
            line-height: 1.75;
        }

        .email-body p {
            margin: 0 0 18px;
        }

        .email-body strong {
            color: #f7faff;
        }

        .email-body a {
            color: #8fb2ff;
        }

        .email-button-wrap {
            margin: 22px 0;
        }

        .email-button {
            display: inline-block;
            padding: 14px 22px;
            border-radius: 14px;
            background: linear-gradient(135deg, #5676ff 0%, #7b8fff 100%);
            color: #ffffff !important;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            box-shadow: 0 14px 32px rgba(86, 118, 255, 0.28);
        }

        .email-data-table {
            width: 100%;
            margin: 22px 0;
            border: 1px solid rgba(118, 142, 226, 0.18);
            border-radius: 18px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.03);
        }

        .email-data-table td {
            padding: 14px 18px;
            border-bottom: 1px solid rgba(118, 142, 226, 0.12);
            vertical-align: top;
            color: #d8e3fb;
            font-size: 14px;
            line-height: 1.6;
        }

        .email-data-table tr:last-child td {
            border-bottom: none;
        }

        .email-data-table td:first-child {
            width: 38%;
            color: #97abd5;
            font-weight: 700;
        }

        .email-note {
            margin: 22px 0;
            padding: 16px 18px;
            border-radius: 16px;
            border: 1px solid rgba(118, 142, 226, 0.16);
            background: rgba(255, 255, 255, 0.03);
            color: #cdd9f7;
        }

        .email-divider {
            height: 1px;
            margin: 28px 0 20px;
            background: rgba(118, 142, 226, 0.14);
        }

        .email-footer {
            padding: 0 40px 34px;
            color: #8698bf;
            font-size: 13px;
            line-height: 1.7;
        }

        .email-footer strong {
            display: block;
            margin-bottom: 6px;
            color: #dfe8ff;
            font-size: 13px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        @media only screen and (max-width: 640px) {
            .email-hero,
            .email-body,
            .email-footer {
                padding-left: 22px !important;
                padding-right: 22px !important;
            }

            .email-title {
                font-size: 27px !important;
            }

            .email-data-table td {
                display: block;
                width: 100% !important;
                padding-top: 10px !important;
                padding-bottom: 10px !important;
            }

            .email-data-table td:first-child {
                padding-bottom: 2px !important;
            }
        }
    </style>
</head>
<body>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" class="email-shell">
        <tr>
            <td style="padding: 32px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" class="email-card">
                    <tr>
                        <td class="email-hero">
                            <p class="email-eyebrow">{{ $eyebrow ?: 'University Practicum' }}</p>
                            <h1 class="email-title">{{ $title }}</h1>
                            @if ($subtitle)
                                <p class="email-subtitle">{{ $subtitle }}</p>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="email-body">
                            {{ $slot }}
                        </td>
                    </tr>
                    <tr>
                        <td class="email-footer">
                            <div class="email-divider"></div>
                            <strong>{{ config('app.name', 'University Practicum') }}</strong>
                            <div>This is an automated message from the practicum platform. Please keep this email for your records.</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
