<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تحديث حالة طلبك — GR Motors</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6f8; color: #333; direction: rtl; }
        .email-wrapper { max-width: 620px; margin: 0 auto; background: #ffffff; }
        .header { background: linear-gradient(135deg, #0b1a30 0%, #1a2f50 100%); padding: 30px 40px; text-align: center; border-bottom: 4px solid linear-gradient(180deg, #ED1C24 0%, #B1161C 32.21%, #8A1217 55.77%, #5A0D10 100%); }
        .header-logo { font-size: 26px; font-weight: 900; color: #ffffff; letter-spacing: 2px; }
        .header-logo span { color: #EE1E26; }
        .hero { padding: 35px 40px; background: #0b1a30; text-align: center; }
        .hero-icon { font-size: 52px; margin-bottom: 12px; }
        .hero-title { color: #ffffff; font-size: 22px; font-weight: 800; margin-bottom: 6px; }
        .hero-subtitle { color: rgba(255,255,255,0.7); font-size: 14px; }
        .content { padding: 35px 40px; }
        .greeting { font-size: 17px; font-weight: 700; color: #1a1a1a; margin-bottom: 12px; }
        .status-change-box {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            padding: 25px;
            margin: 25px 0;
        }
        .status-pill {
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 800;
        }
        .status-old { background: #e9ecef; color: #555; text-decoration: line-through; }
        .status-new { background: linear-gradient(135deg, #EE1E26, #c41520); color: #fff; }
        .arrow { font-size: 24px; color: #EE1E26; }
        .summary-card { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 16px; overflow: hidden; margin: 25px 0; }
        .summary-header { background: #0b1a30; color: #fff; padding: 12px 20px; font-size: 14px; font-weight: 700; }
        .summary-row { display: flex; justify-content: space-between; padding: 12px 20px; border-bottom: 1px solid #e9ecef; font-size: 14px; }
        .summary-row:last-child { border-bottom: none; }
        .summary-label { color: #666; }
        .summary-value { font-weight: 700; color: #1a1a1a; }
        .cta-wrapper { text-align: center; margin: 25px 0; }
        .cta-btn { display: inline-block; padding: 14px 36px; background: linear-gradient(135deg, #EE1E26, #c41520); color: #fff; text-decoration: none; border-radius: 50px; font-size: 14px; font-weight: 800; }
        .footer { background: #f8f9fa; border-top: 1px solid #e9ecef; padding: 25px 40px; text-align: center; }
        .footer-brand { font-size: 16px; font-weight: 900; color: #0b1a30; }
        .footer-brand span { color: #EE1E26; }
        .footer-contact { font-size: 12px; color: #777; margin-top: 6px; line-height: 1.7; }
        .footer-disclaimer { font-size: 11px; color: #aaa; margin-top: 12px; }
    </style>
</head>
<body>
<div class="email-wrapper">

    <div class="header">
        <div class="header-logo">GR <span>MOTORS</span></div>
    </div>

    <div class="hero">
        <div class="hero-icon">🔄</div>
        <div class="hero-title">تحديث حالة طلبك</div>
        <div class="hero-subtitle">طلب رقم #{{ $booking->id }}</div>
    </div>

    <div class="content">
        <div class="greeting">مرحباً {{ $booking->client_name }}،</div>

        <p style="font-size:15px;color:#555;line-height:1.8;margin-bottom:20px;">
            نود إعلامك بأنه تم تحديث حالة طلبك كما يلي:
        </p>

        <!-- Status Change Visual -->
        <div class="status-change-box">
            <span class="status-pill status-old">
                {{ \App\Models\Booking::STATUSES[$oldStatus]['label'] ?? $oldStatus }}
            </span>
            <span class="arrow">←</span>
            <span class="status-pill status-new">
                {{ \App\Models\Booking::STATUSES[$newStatus]['label'] ?? $newStatus }}
            </span>
        </div>

        <!-- Booking Summary -->
        <div class="summary-card">
            <div class="summary-header">📋 تفاصيل الطلب</div>
            @if($booking->car)
            <div class="summary-row">
                <span class="summary-label">السيارة</span>
                <span class="summary-value">{{ $booking->car->name }}</span>
            </div>
            @endif
            <div class="summary-row">
                <span class="summary-label">نوع الطلب</span>
                <span class="summary-value">{{ \App\Models\Booking::BOOKING_TYPES_LABELS[$booking->booking_type] ?? $booking->booking_type }}</span>
            </div>
            @if($booking->monthly_installment)
            <div class="summary-row">
                <span class="summary-label">القسط الشهري</span>
                <span class="summary-value">{{ number_format($booking->monthly_installment) }} ريال</span>
            </div>
            @endif
            <div class="summary-row">
                <span class="summary-label">الحالة الجديدة</span>
                <span class="summary-value" style="color: #EE1E26;">
                    {{ \App\Models\Booking::STATUSES[$newStatus]['label'] ?? $newStatus }}
                </span>
            </div>
        </div>

        @if(auth()->check() || $booking->user_id)
        <div class="cta-wrapper">
            <a href="{{ route('store.account.orders') }}" class="cta-btn">عرض تفاصيل طلبي</a>
        </div>
        @endif

        <p style="font-size:14px;color:#777;line-height:1.8;">
            في حال كان لديك أي استفسار، لا تتردد في التواصل مع فريقنا مباشرةً.
        </p>
    </div>

    <div class="footer">
        <div class="footer-brand">GR <span>MOTORS</span></div>
        <div class="footer-contact">
            @php
                $phone = \App\Models\Setting::where('key','contact_phone')->value('value');
                $email = \App\Models\Setting::where('key','contact_email')->value('value');
            @endphp
            @if($phone) 📞 {{ is_array($phone) ? ($phone['value'] ?? '') : $phone }}<br> @endif
            @if($email) ✉️ {{ is_array($email) ? ($email['value'] ?? '') : $email }} @endif
        </div>
        <p class="footer-disclaimer">هذا الإيميل تم إرساله تلقائياً. يرجى عدم الرد عليه مباشرةً.</p>
    </div>

</div>
</body>
</html>
