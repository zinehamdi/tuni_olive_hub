<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light only">
    <meta name="supported-color-schemes" content="light only">
    <title>{{ $subjectTitle }}</title>
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #F3F4F6 !important; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        @media screen and (max-width: 600px) {
            .email-container { width: 100% !important; }
            .padding-mobile { padding-left: 20px !important; padding-right: 20px !important; }
            .hero-title { font-size: 20px !important; }
        }
            @media (prefers-color-scheme: dark) {
            body { background-color: #F3F4F6 !important; }
            .email-container { background-color: #FFFFFF !important; }
            td, p, h1, h2, h3, h4, span, li, a { color: inherit !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #F3F4F6;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F3F4F6; padding: 30px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="600" class="email-container" style="background-color: #FFFFFF; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.06); border: 1px solid #E5E7EB;">

                    <!-- HEADER -->
                    <tr>
                        <td align="center" style="background: linear-gradient(135deg, #111827 0%, #0F291E 100%); padding: 32px 24px; border-bottom: 3px solid #6A8F3B;">
                            <a href="{{ url('/') }}" target="_blank" style="text-decoration: none;">
                                <img src="https://zintoop.com/images/zintoop-logo.png" alt="ZinToop Logo" width="64" height="64" style="display: block; width: 64px; height: 64px; border-radius: 16px; border: 2px solid rgba(255,255,255,0.2); margin: 0 auto 12px auto;">
                            </a>
                            <h1 style="color: #FFFFFF; font-size: 24px; font-weight: 800; margin: 0 0 4px 0;">منصة زيت الزيتون التونسي / زينتوب</h1>
                            <p style="color: #A7F3D0; font-size: 13px; font-weight: 600; margin: 0; letter-spacing: 1px;">ZinToop | السوق التونسي الأول لزيت الزيتون والخدمات الفلاحية</p>
                        </td>
                    </tr>

                    <!-- SUBJECT HERO -->
                    <tr>
                        <td class="padding-mobile" style="padding: 36px 32px 28px 32px; text-align: right;" dir="rtl">
                            <h2 class="hero-title" style="color: #111827; font-size: 23px; font-weight: 900; margin: 0 0 20px 0; line-height: 1.4; border-bottom: 2px solid #E5E7EB; padding-bottom: 16px;">
                                {{ $subjectTitle }}
                            </h2>
                        </td>
                    </tr>

                    <!-- DYNAMIC BODY -->
                    <tr>
                        <td class="padding-mobile" style="padding: 0 32px 32px 32px; text-align: right; color: #374151; font-size: 15px; line-height: 1.8;" dir="rtl">
                            {!! $messageBody !!}
                        </td>
                    </tr>

                    <!-- DIVIDER -->
                    <tr>
                        <td style="padding: 0 32px 28px 32px;">
                            <hr style="border: none; border-top: 1px solid #E5E7EB; margin: 0;">
                        </td>
                    </tr>

                    <!-- CTA -->
                    <tr>
                        <td align="center" style="padding: 0 32px 36px 32px;">
                            <a href="{{ url('/') }}" target="_blank" style="display: inline-block; background-color: #6A8F3B; color: #FFFFFF; font-size: 15px; font-weight: 800; text-decoration: none; padding: 14px 32px; border-radius: 14px; box-shadow: 0 8px 20px rgba(106,143,59,0.35);">
                                🫒 زيارة منصة زينتوب
                            </a>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td align="center" style="background-color: #111827; padding: 28px 24px; border-top: 1px solid #1F2937; color: #9CA3AF; font-size: 12px; line-height: 1.6;" dir="rtl">
                            <p style="color: #D1D5DB; font-weight: 700; font-size: 14px; margin: 0 0 6px 0;">منصة زيت الزيتون التونسي / زينتوب | ZinToop</p>
                            <p style="margin: 0 0 12px 0; color: #6B7280;">تونس | جميع الحقوق محفوظة © {{ date('Y') }}</p>
                            <p style="margin: 0; font-size: 11px; color: #4B5563;">تلقيت هذا البريد لأنك مشترك في نشرات منصة زينتوب.</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
