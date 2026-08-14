<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طلب حجز جديد — GR Motors CRM</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f2f5; color: #333; direction: rtl; }
        .email-wrapper { max-width: 640px; margin: 0 auto; background: #ffffff; }
        .header { background: linear-gradient(135deg, #0b1a30 0%, #1a2f50 100%); padding: 28px 40px; border-bottom: 4px solid linear-gradient(180deg, #ED1C24 0%, #B1161C 32.21%, #8A1217 55.77%, #5A0D10 100%); display: flex; justify-content: space-between; align-items: center; }
        .header-logo { font-size: 22px; font-weight: 900; color: #fff; }
        .header-logo span { color: #EE1E26; }
        .header-badge { background: linear-gradient(180deg, #ED1C24 0%, #B1161C 32.21%, #8A1217 55.77%, #5A0D10 100%); color: #fff; padding: 6px 16px; border-radius: 50px; font-size: 12px; font-weight: 800; }
        .alert-banner { background: linear-gradient(135deg, #EE1E26 0%, #c41520 100%); color: #fff; padding: 20px 40px; text-align: center; }
        .alert-banner h2 { font-size: 20px; font-weight: 800; }
        .alert-banner p { font-size: 13px; opacity: 0.9; margin-top: 5px; }
        .content { padding: 30px 40px; }
        .section-title { font-size: 13px; font-weight: 800; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 25px; }
        .info-item { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 10px; padding: 14px 16px; }
        .info-item .label { font-size: 11px; color: #888; font-weight: 700; text-transform: uppercase; margin-bottom: 5px; }
        .info-item .value { font-size: 15px; font-weight: 800; color: #1a1a1a; }
        .info-item.highlight { background: #fff5f5; border-color: #ffcdd2; }
        .info-item.highlight .value { color: #EE1E26; }
        .financial-card { background: #0b1a30; color: #fff; border-radius: 14px; padding: 20px 24px; margin-bottom: 25px; }
        .financial-card h4 { font-size: 13px; color: rgba(255,255,255,0.6); margin-bottom: 15px; font-weight: 700; }
        .financial-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.08); font-size: 14px; }
        .financial-row:last-child { border-bottom: none; padding-top: 12px; }
        .financial-row .f-label { color: rgba(255,255,255,0.7); }
        .financial-row .f-value { font-weight: 800; }
        .financial-row .f-value.big { color: #EE1E26; font-size: 18px; }
        .notes-box { background: #fffbf0; border: 1px solid #fde68a; border-radius: 10px; padding: 15px 18px; margin-bottom: 20px; font-size: 13px; color: #555; line-height: 1.7; }
        .cta-wrapper { text-align: center; margin: 20px 0; }
        .cta-btn { display: inline-block; padding: 14px 36px; background: #0b1a30; color: #fff; text-decoration: none; border-radius: 50px; font-size: 14px; font-weight: 800; }
        .footer { background: #f8f9fa; border-top: 1px solid #e9ecef; padding: 20px 40px; text-align: center; font-size: 12px; color: #888; }
        @media (max-width: 540px) { .info-grid { grid-template-columns: 1fr; } .content { padding: 20px; } }
    </style>
</head>
<body>
<div class="email-wrapper">

    <div class="header">
        <div class="header-logo">GR <span>MOTORS</span></div>
        <div class="header-badge">🔔 إشعار جديد</div>
    </div>

    <div class="alert-banner">
        <h2>🚀 طلب حجز جديد وصل!</h2>
        <p>الطلب رقم #{{ $booking->id }} — يرجى المراجعة والتواصل مع العميل</p>
    </div>

    <div class="content">

        <!-- Client Info -->
        <div class="section-title">بيانات العميل</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="label">الاسم</div>
                <div class="value">{{ $booking->client_name }}</div>
            </div>
            <div class="info-item">
                <div class="label">رقم الهاتف</div>
                <div class="value" dir="ltr">{{ $booking->client_phone }}</div>
            </div>
            @if($booking->client_email)
            <div class="info-item">
                <div class="label">البريد الإلكتروني</div>
                <div class="value">{{ $booking->client_email }}</div>
            </div>
            @endif
            @if($booking->location)
            <div class="info-item">
                <div class="label">الموقع</div>
                <div class="value">{{ $booking->location }}</div>
            </div>
            @endif
        </div>

        <!-- Car Info -->
        @if($booking->car)
        <div class="section-title">السيارة المطلوبة</div>
        <div class="info-grid">
            <div class="info-item highlight">
                <div class="label">اسم السيارة</div>
                <div class="value">{{ $booking->car->name }}</div>
            </div>
            <div class="info-item">
                <div class="label">نوع الطلب</div>
                <div class="value">{{ \App\Models\Booking::BOOKING_TYPES_LABELS[$booking->booking_type] ?? $booking->booking_type }}</div>
            </div>
            @if($booking->car->brand)
            <div class="info-item">
                <div class="label">الماركة</div>
                <div class="value">{{ $booking->car->brand->name }}</div>
            </div>
            @endif
            <div class="info-item">
                <div class="label">سعر النقدي</div>
                <div class="value">{{ number_format($booking->car->cash_price ?? 0) }} ريال</div>
            </div>
        </div>
        @endif

        <!-- Financial Details -->
        @if($booking->monthly_installment || $booking->down_payment)
        <div class="financial-card">
            <h4>💰 تفاصيل التمويل المقترح</h4>
            @if($booking->down_payment)
            <div class="financial-row">
                <span class="f-label">المقدم</span>
                <span class="f-value">{{ number_format($booking->down_payment) }} ريال</span>
            </div>
            @endif
            @if($booking->duration_years)
            <div class="financial-row">
                <span class="f-label">مدة التمويل</span>
                <span class="f-value">{{ $booking->duration_years }} سنة ({{ $booking->duration_years * 12 }} شهر)</span>
            </div>
            @endif
            @if($booking->interest_rate)
            <div class="financial-row">
                <span class="f-label">نسبة الفائدة</span>
                <span class="f-value">{{ $booking->interest_rate }}%</span>
            </div>
            @endif
            @if($booking->monthly_installment)
            <div class="financial-row">
                <span class="f-label">القسط الشهري</span>
                <span class="f-value big">{{ number_format($booking->monthly_installment) }} ريال</span>
            </div>
            @endif
            @if($booking->total_price)
            <div class="financial-row">
                <span class="f-label">الإجمالي</span>
                <span class="f-value">{{ number_format($booking->total_price) }} ريال</span>
            </div>
            @endif
        </div>
        @endif

        @if($booking->notes)
        <div class="notes-box">
            <strong>📝 ملاحظات العميل:</strong><br>
            {{ $booking->notes }}
        </div>
        @endif

        <!-- CTA to CRM -->
        <div class="cta-wrapper">
            <a href="{{ route('crm.bookings.show', $booking->id) }}" class="cta-btn">
                عرض الطلب في لوحة الإدارة →
            </a>
        </div>

        <p style="font-size:12px;color:#999;text-align:center;">
            وصل هذا الطلب بتاريخ {{ $booking->created_at->format('Y/m/d') }}
            الساعة {{ $booking->created_at->format('h:i A') }}
        </p>
    </div>

    <div class="footer">
        GR Motors — نظام إدارة الطلبات الداخلي<br>
        هذا إيميل تلقائي من النظام
    </div>

</div>
</body>
</html>
