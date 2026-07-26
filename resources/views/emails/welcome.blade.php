<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light only">
    <meta name="supported-color-schemes" content="light only">
    <title>مرحباً بك في زينتوب | Welcome to ZinToop</title>
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #F3F4F6 !important; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        @media screen and (max-width: 600px) {
            .email-container { width: 100% !important; margin: auto !important; }
            .padding-mobile { padding-left: 20px !important; padding-right: 20px !important; }
            .hero-title { font-size: 22px !important; }
            .card-padding { padding: 18px !important; }
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

                    <!-- WELCOME HERO -->
                    <tr>
                        <td class="padding-mobile" style="padding: 36px 32px 20px 32px; text-align: right;" dir="rtl">
                            <span style="display: inline-block; background-color: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; font-size: 12px; font-weight: 700; padding: 6px 14px; border-radius: 50px; margin-bottom: 16px;">
                                🎉 حساب جديد | Nouveau Compte
                            </span>
                            <h2 class="hero-title" style="color: #111827; font-size: 25px; font-weight: 900; margin: 0 0 12px 0; line-height: 1.4;">
                                أهلاً وسهلاً بك يا <span style="color: #6A8F3B;">{{ $user->name }}</span>! 🫒
                            </h2>
                            <p style="color: #4B5563; font-size: 15px; line-height: 1.7; margin: 0 0 8px 0;">
                                لقد أصبحت الآن جزءاً من <strong>أكبر شبكة رقمية</strong> تجمع فلاحي الزيتون، أصحاب المعاصر، الناقلين، وتجار زيت الزيتون في تونس.
                            </p>
                            <p style="color: #6B7280; font-size: 13px; line-height: 1.6; margin: 0 0 24px 0;">
                                Bienvenue ! Votre compte a été activé avec succès sur ZinToop, le premier marché numérique de l'huile d'olive en Tunisie.
                            </p>
                        </td>
                    </tr>

                    <!-- FEATURES -->
                    <!-- Feature 1 -->
                    <tr>
                        <td class="padding-mobile" style="padding: 0 32px 16px 32px;" dir="rtl">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="card-padding" style="background-color: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 18px; padding: 20px;">
                                <tr>
                                    <td width="50" valign="top" style="padding-left: 14px;">
                                        <div style="width: 48px; height: 48px; background-color: #6A8F3B; border-radius: 14px; text-align: center; line-height: 48px; font-size: 24px; color: #FFFFFF;">🛒</div>
                                    </td>
                                    <td valign="top" style="text-align: right;">
                                        <h3 style="color: #111827; font-size: 16px; font-weight: 800; margin: 0 0 6px 0;">أضف عروضك وابدأ البيع الآن</h3>
                                        <p style="color: #4B5563; font-size: 13px; line-height: 1.6; margin: 0;">نشر عروض البيع والطلب مجاناً — زيت الزيتون، الزيتون، والمواد الفلاحية. تواصل مباشر مع المشترين بدون وسطاء.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Feature 2 -->
                    <tr>
                        <td class="padding-mobile" style="padding: 0 32px 16px 32px;" dir="rtl">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="card-padding" style="background-color: #FEFCE8; border: 1px solid #FEF08A; border-radius: 18px; padding: 20px;">
                                <tr>
                                    <td width="50" valign="top" style="padding-left: 14px;">
                                        <div style="width: 48px; height: 48px; background-color: #C8A356; border-radius: 14px; text-align: center; line-height: 48px; font-size: 24px; color: #FFFFFF;">📈</div>
                                    </td>
                                    <td valign="top" style="text-align: right;">
                                        <h3 style="color: #854D0E; font-size: 16px; font-weight: 800; margin: 0 0 6px 0;">تابع أسعار السوق يومياً</h3>
                                        <p style="color: #713F12; font-size: 13px; line-height: 1.6; margin: 0;">أسعار بورصة زيت الزيتون في تونس والأسواق العالمية محدّثة يومياً — ابقَ على اطلاع دائم لاتخاذ أفضل القرارات التجارية.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Feature 3 -->
                    <tr>
                        <td class="padding-mobile" style="padding: 0 32px 28px 32px;" dir="rtl">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="card-padding" style="background-color: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 18px; padding: 20px;">
                                <tr>
                                    <td width="50" valign="top" style="padding-left: 14px;">
                                        <div style="width: 48px; height: 48px; background-color: #16A34A; border-radius: 14px; text-align: center; line-height: 48px; font-size: 24px; color: #FFFFFF;">💼</div>
                                    </td>
                                    <td valign="top" style="text-align: right;">
                                        <h3 style="color: #14532D; font-size: 16px; font-weight: 800; margin: 0 0 6px 0;">دليل الخدمات الفلاحية والتجارية</h3>
                                        <p style="color: #166534; font-size: 13px; line-height: 1.6; margin: 0;">ابحث عن المعاصر، الناقلين، وحدات التعبئة، المخلصين الجمركيين، والمكاتب الإدارية — كل ما تحتاجه في مكان واحد.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- CTA -->
                    <tr>
                        <td align="center" class="padding-mobile" style="padding: 0 32px 36px 32px;">
                            <a href="{{ route('dashboard') }}" target="_blank" style="display: inline-block; background-color: #6A8F3B; color: #FFFFFF; font-size: 16px; font-weight: 800; text-decoration: none; padding: 16px 36px; border-radius: 14px; box-shadow: 0 8px 20px rgba(106,143,59,0.35); border: 1px solid #5a7a2f;">
                                🚀 ابدأ الاستكشاف — لوحة التحكم
                            </a>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td align="center" style="background-color: #111827; padding: 28px 24px; border-top: 1px solid #1F2937; color: #9CA3AF; font-size: 12px; line-height: 1.6;" dir="rtl">
                            <p style="color: #D1D5DB; font-weight: 700; font-size: 14px; margin: 0 0 6px 0;">منصة زيت الزيتون التونسي / زينتوب | ZinToop</p>
                            <p style="margin: 0 0 12px 0; color: #6B7280;">تونس | جميع الحقوق محفوظة © {{ date('Y') }}</p>
                            <p style="margin: 0; font-size: 11px; color: #4B5563;">لتغيير إعدادات الإشعارات، يمكنك تحديث حسابك في المنصة.</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
