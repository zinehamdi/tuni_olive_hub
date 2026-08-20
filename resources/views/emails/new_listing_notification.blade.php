<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light only">
    <meta name="supported-color-schemes" content="light only">
    <title>عرض جديد في السوق | ZinToop</title>
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #F3F4F6 !important; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color-scheme: light only; -webkit-color-scheme: light; }
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

                    <!-- HERO -->
                    <tr>
                        <td class="padding-mobile" style="padding: 36px 32px 20px 32px; text-align: right;" dir="rtl">
                            <span style="display: inline-block; background-color: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; font-size: 12px; font-weight: 700; padding: 6px 14px; border-radius: 50px; margin-bottom: 16px;">
                                🆕 عرض جديد في السوق | Nouvelle annonce sur le marché
                            </span>
                            <h2 class="hero-title" style="color: #111827; font-size: 24px; font-weight: 900; margin: 0 0 12px 0; line-height: 1.4;">
                                {{ $listing->product->type === 'oil' ? '🫙 زيت زيتون' : '🫒 زيتون' }} — {{ $listing->product->variety ?? 'منتج جديد' }} متاح الآن!
                            </h2>
                            <p style="color: #4B5563; font-size: 15px; line-height: 1.7; margin: 0 0 24px 0;">
                                تم نشر عرض جديد للبيع على منصة <strong>زينتوب (ZinToop)</strong>. تواصل مع البائع مباشرة بدون وسيط أو عمولات.
                            </p>
                        </td>
                    </tr>

                    <!-- LISTING DETAILS CARD -->
                    <tr>
                        <td class="padding-mobile" style="padding: 0 32px 24px 32px;" dir="rtl">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background: linear-gradient(135deg, #F0FDF4, #ECFDF5); border: 2px solid #6A8F3B; border-radius: 18px;">
                                <tr>
                                    <td style="padding: 24px;" dir="rtl">
                                        <h3 style="color: #14532D; font-size: 15px; font-weight: 800; margin: 0 0 16px 0; border-bottom: 1px solid #A7F3D0; padding-bottom: 10px;">
                                            📋 تفاصيل العرض
                                        </h3>
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding: 7px 0; font-size: 14px; font-weight: 700; color: #374151; text-align: right; width: 40%;">المنتج:</td>
                                                <td style="padding: 7px 0; font-size: 14px; font-weight: 800; color: #6A8F3B; text-align: left;">
                                                    {{ $listing->product->variety ?? '—' }}
                                                    ({{ $listing->product->type === 'oil' ? 'زيت زيتون' : 'زيتون' }})
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 7px 0; font-size: 14px; font-weight: 700; color: #374151; text-align: right;">الكمية:</td>
                                                <td style="padding: 7px 0; font-size: 14px; font-weight: 800; color: #111827; text-align: left;">
                                                    {{ number_format($listing->quantity, 0) }} {{ $listing->unit }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 7px 0; font-size: 14px; font-weight: 700; color: #374151; text-align: right;">السعر:</td>
                                                <td style="padding: 7px 0; font-size: 15px; font-weight: 900; color: #C8A356; text-align: left;">
                                                    @if($listing->price > 0)
                                                        {{ number_format($listing->price, 2) }} {{ $listing->currency }}
                                                    @else
                                                        <span style="color: #6B7280;">عند الطلب</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @if($listing->governorate)
                                            <tr>
                                                <td style="padding: 7px 0; font-size: 14px; font-weight: 700; color: #374151; text-align: right;">المنطقة:</td>
                                                <td style="padding: 7px 0; font-size: 14px; font-weight: 800; color: #111827; text-align: left;">
                                                    📍 {{ $listing->governorate }}
                                                </td>
                                            </tr>
                                            @endif
                                            @if($listing->seller)
                                            <tr>
                                                <td style="padding: 7px 0; font-size: 14px; font-weight: 700; color: #374151; text-align: right;">البائع:</td>
                                                <td style="padding: 7px 0; font-size: 14px; font-weight: 800; color: #111827; text-align: left;">
                                                    {{ $listing->seller->name }}
                                                </td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- LISTING IMAGE (if exists) -->
                    @if(!empty($listing->media) && is_array($listing->media) && count($listing->media) > 0)
                    <tr>
                        <td class="padding-mobile" style="padding: 0 32px 24px 32px;">
                            <img src="{{ url(Storage::url($listing->media[0])) }}" alt="صورة المنتج" style="width: 100%; max-height: 280px; object-fit: cover; border-radius: 16px; border: 1px solid #E5E7EB; display: block;">
                        </td>
                    </tr>
                    @endif

                    <!-- DESCRIPTION (if exists) -->
                    @if($listing->description)
                    <tr>
                        <td class="padding-mobile" style="padding: 0 32px 24px 32px;" dir="rtl">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #FEFCE8; border: 1px solid #FEF08A; border-radius: 18px;">
                                <tr>
                                    <td style="padding: 18px 20px;" dir="rtl">
                                        <p style="color: #854D0E; font-size: 13px; font-weight: 700; margin: 0 0 6px 0;">📝 وصف العرض</p>
                                        <p style="color: #713F12; font-size: 13px; line-height: 1.7; margin: 0;">{{ Str::limit($listing->description, 200) }}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif

                    <!-- CTA -->
                    <tr>
                        <td align="center" class="padding-mobile" style="padding: 0 32px 36px 32px;">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <a href="{{ route('listings.show', ['locale' => app()->getLocale() ?: 'ar', 'listing' => $listing->id]) }}" target="_blank" style="display: inline-block; background-color: #6A8F3B; color: #FFFFFF; font-size: 16px; font-weight: 800; text-decoration: none; padding: 16px 36px; border-radius: 14px; box-shadow: 0 8px 20px rgba(106,143,59,0.35); border: 1px solid #5a7a2f;">
                                            👁 مشاهدة العرض والتواصل مع البائع
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-top: 14px;">
                                        <a href="{{ url((app()->getLocale() ?: 'ar') . '/#products') }}" target="_blank" style="color: #6A8F3B; font-size: 13px; font-weight: 700; text-decoration: underline;">
                                            🫒 تصفح جميع العروض في السوق
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td align="center" style="background-color: #111827; padding: 28px 24px; border-top: 1px solid #1F2937; color: #9CA3AF; font-size: 12px; line-height: 1.6;" dir="rtl">
                            <p style="color: #D1D5DB; font-weight: 700; font-size: 14px; margin: 0 0 6px 0;">منصة زيت الزيتون التونسي / زينتوب | ZinToop</p>
                            <p style="margin: 0 0 12px 0; color: #6B7280;">تونس | جميع الحقوق محفوظة © {{ date('Y') }}</p>
                            <p style="margin: 0; font-size: 11px; color: #4B5563;">تلقيت هذا البريد لأنك مشترك في تنبيهات السوق على منصة زينتوب.</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
