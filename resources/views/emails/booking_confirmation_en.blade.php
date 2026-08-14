<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation — GR Motors</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f6f8;
            color: #333;
            direction: ltr;
        }
        .email-wrapper {
            max-width: 620px;
            margin: 0 auto;
            background: #ffffff;
        }
        /* Header */
        .header {
            background: linear-gradient(135deg, #0b1a30 0%, #1a2f50 100%);
            padding: 35px 40px;
            text-align: center;
            border-bottom: 4px solid linear-gradient(180deg, #ED1C24 0%, #B1161C 32.21%, #8A1217 55.77%, #5A0D10 100%);
        }
        .header-logo {
            font-size: 28px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: 2px;
        }
        .header-logo span { color: #EE1E26; }
        .header-subtitle {
            color: rgba(255,255,255,0.7);
            font-size: 13px;
            margin-top: 6px;
        }
        /* Hero */
        .hero {
            background: linear-gradient(135deg, #EE1E26 0%, #c41520 100%);
            padding: 40px;
            text-align: center;
        }
        .hero-icon {
            font-size: 56px;
            margin-bottom: 15px;
        }
        .hero-title {
            color: #ffffff;
            font-size: 26px;
            font-weight: 800;
            margin-bottom: 8px;
        }
        .hero-subtitle {
            color: rgba(255,255,255,0.85);
            font-size: 15px;
            line-height: 1.6;
        }
        /* Content */
        .content {
            padding: 40px;
        }
        .greeting {
            font-size: 18px;
            color: #1a1a1a;
            font-weight: 700;
            margin-bottom: 15px;
        }
        .message-text {
            font-size: 15px;
            color: #555;
            line-height: 1.8;
            margin-bottom: 30px;
        }
        /* Order Summary Card */
        .summary-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 30px;
        }
        .summary-header {
            background: #0b1a30;
            color: #fff;
            padding: 14px 20px;
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .summary-header .order-num {
            margin-left: auto;
            background: linear-gradient(180deg, #ED1C24 0%, #B1161C 32.21%, #8A1217 55.77%, #5A0D10 100%);
            color: #fff;
            padding: 3px 12px;
            border-radius: 50px;
            font-size: 13px;
        }
        .summary-body { padding: 0; }
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 20px;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px;
        }
        .summary-row:last-child { border-bottom: none; }
        .summary-label { color: #666; font-weight: 600; }
        .summary-value { color: #1a1a1a; font-weight: 700; }
        .summary-value.highlight {
            color: #EE1E26;
            font-size: 17px;
        }
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 800;
            background: #fef3c7;
            color: #d97706;
        }
        /* CTA Button */
        .cta-wrapper {
            text-align: center;
            margin: 30px 0;
        }
        .cta-btn {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #EE1E26 0%, #c41520 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 50px;
            font-size: 15px;
            font-weight: 800;
            box-shadow: 0 8px 20px rgba(238,30,38,0.3);
        }
        /* Info box */
        .info-box {
            background: #e8f4fd;
            border: 1px solid #bee3f8;
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 25px;
        }
        .info-box p {
            font-size: 13px;
            color: #2c5282;
            line-height: 1.7;
        }
        /* Divider */
        .divider {
            height: 1px;
            background: #eee;
            margin: 25px 0;
        }
        /* Footer */
        .footer {
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            padding: 30px 40px;
            text-align: center;
        }
        .footer-brand {
            font-size: 18px;
            font-weight: 900;
            color: #0b1a30;
            margin-bottom: 8px;
        }
        .footer-brand span { color: #EE1E26; }
        .footer-contact {
            font-size: 13px;
            color: #777;
            line-height: 1.8;
        }
        .footer-disclaimer {
            font-size: 11px;
            color: #aaa;
            margin-top: 15px;
        }
        @media (max-width: 600px) {
            .content { padding: 25px 20px; }
            .hero { padding: 30px 20px; }
            .header { padding: 25px 20px; }
            .footer { padding: 25px 20px; }
        }
    </style>
</head>
<body>
<div class="email-wrapper">

    <!-- Header -->
    <div class="header">
        <div class="header-logo">GR <span>MOTORS</span></div>
        <div class="header-subtitle">Your trusted partner for all types of cars</div>
    </div>

    <!-- Hero -->
    <div class="hero">
        <div class="hero-icon">✅</div>
        <div class="hero-title">Your booking was received successfully!</div>
        <div class="hero-subtitle">One of our sales advisors will contact you as soon as possible</div>
    </div>

    <!-- Content -->
    <div class="content">

        <div class="greeting">Hello {{ $booking->client_name }},</div>
        <p class="message-text">
            Thank you for contacting us! We have received your booking and our dedicated team will review it and
            contact you as soon as possible. In this email, you'll find a full summary of your booking for reference.
        </p>

        <!-- Order Summary -->
        <div class="summary-card">
            <div class="summary-header">
                <span>📋 Order Summary</span>
                <span class="order-num">#{{ $booking->id }}</span>
            </div>
            <div class="summary-body">
                @if($booking->car)
                <div class="summary-row">
                    <span class="summary-label">🚗 Car</span>
                    <span class="summary-value">{{ $booking->car->name ?? '---' }}</span>
                </div>
                @if($booking->car->brand)
                <div class="summary-row">
                    <span class="summary-label">🏷️ Brand</span>
                    <span class="summary-value">{{ $booking->car->brand->name }}</span>
                </div>
                @endif
                @endif

                <div class="summary-row">
                    <span class="summary-label">📌 Order Type</span>
                    <span class="summary-value">{{ \App\Models\Booking::BOOKING_TYPES_LABELS_EN[$booking->booking_type] ?? $booking->booking_type }}</span>
                </div>

                @if($booking->down_payment)
                <div class="summary-row">
                    <span class="summary-label">💰 Down Payment</span>
                    <span class="summary-value">{{ number_format($booking->down_payment) }} SAR</span>
                </div>
                @endif

                @if($booking->duration_years)
                <div class="summary-row">
                    <span class="summary-label">📅 Financing Term</span>
                    <span class="summary-value">{{ $booking->duration_years }} {{ $booking->duration_years == 1 ? 'Year' : 'Years' }}</span>
                </div>
                @endif

                @if($booking->monthly_installment)
                <div class="summary-row">
                    <span class="summary-label">💳 Monthly Installment</span>
                    <span class="summary-value highlight">{{ number_format($booking->monthly_installment) }} SAR</span>
                </div>
                @endif

                @if($booking->total_price)
                <div class="summary-row">
                    <span class="summary-label">🏷️ Total</span>
                    <span class="summary-value">{{ number_format($booking->total_price) }} SAR</span>
                </div>
                @endif

                <div class="summary-row">
                    <span class="summary-label">📊 Order Status</span>
                    <span class="summary-value">
                        <span class="status-badge">{{ \App\Models\Booking::STATUSES_EN[$booking->status]['label'] ?? $booking->status }}</span>
                    </span>
                </div>

                <div class="summary-row">
                    <span class="summary-label">📆 Order Date</span>
                    <span class="summary-value">{{ $booking->created_at->format('Y/m/d') }}</span>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="info-box">
            <p>
                ℹ️ <strong>What happens next?</strong><br>
                Our sales team will review your booking and call you on
                <strong>{{ $booking->client_phone }}</strong>
                during working hours (Sat–Thu, 9 AM–9 PM).
            </p>
        </div>

        @if(auth()->check() || $booking->user_id)
        <!-- CTA -->
        <div class="cta-wrapper">
            <a href="{{ route('store.account.orders') }}" class="cta-btn">
                View My Orders
            </a>
        </div>
        @endif

    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-brand">GR <span>MOTORS</span></div>
        <div class="footer-contact">
            @php
                $siteName = \App\Models\Setting::where('key','site_name')->value('value');
                $phone    = \App\Models\Setting::where('key','contact_phone')->value('value');
                $email    = \App\Models\Setting::where('key','contact_email')->value('value');
            @endphp
            @if($phone) 📞 {{ is_array($phone) ? ($phone['value'] ?? '') : $phone }}<br> @endif
            @if($email) ✉️ {{ is_array($email) ? ($email['value'] ?? '') : $email }} @endif
        </div>
        <p class="footer-disclaimer">
            This email was sent automatically. Please do not reply directly.
        </p>
    </div>

</div>
</body>
</html>
