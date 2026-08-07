<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify your email | VeriFact AI</title>
</head>
<body style="margin:0;padding:0;background-color:#F0F7FF;font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F0F7FF;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #E2E8F0;box-shadow:0 12px 40px rgba(30,58,138,0.12);">
                    <tr>
                        <td align="center" style="padding:28px 32px 12px;background:linear-gradient(180deg,#EFF6FF 0%,#ffffff 100%);">
                            <img src="{{ $logoUrl }}" alt="VeriFact AI" width="160" style="display:block;max-width:160px;height:auto;border:0;">
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:8px 32px 0;">
                            <div style="width:64px;height:64px;border-radius:999px;background:#EFF6FF;line-height:64px;font-size:28px;">✉️</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 36px 8px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;line-height:1.3;color:#0F172A;font-weight:700;">Verify your email</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:8px 36px 24px;text-align:center;">
                            <p style="margin:0;font-size:15px;line-height:1.6;color:#64748B;">
                                Hi {{ $userName }}, thanks for joining <strong style="color:#1E3A8A;">VeriFact AI</strong>.
                                Please confirm your email address to activate your account.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:0 36px 28px;">
                            <a href="{{ $url }}"
                               style="display:inline-block;background:linear-gradient(135deg,#1E3A8A 0%,#2563EB 100%);color:#ffffff;text-decoration:none;font-weight:600;font-size:14px;padding:14px 28px;border-radius:10px;">
                                Verify Email Address
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 36px 28px;text-align:center;">
                            <p style="margin:0;font-size:13px;line-height:1.6;color:#94A3B8;">
                                If you did not create a VeriFact AI account, you can ignore this email.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 36px;background:#F8FAFC;border-top:1px solid #E2E8F0;text-align:center;">
                            <p style="margin:0 0 8px;font-size:12px;color:#64748B;">
                                If the button does not work, copy and paste this link:
                            </p>
                            <p style="margin:0;font-size:12px;line-height:1.5;word-break:break-all;">
                                <a href="{{ $url }}" style="color:#009AA4;text-decoration:underline;">{{ $url }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
                <p style="margin:20px 0 0;font-size:12px;color:#94A3B8;">
                    © {{ date('Y') }} VeriFact AI. Detect falsehoods with confidence.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
