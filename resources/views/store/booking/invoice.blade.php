<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $booking->id }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 portrait;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            background: #ffffff;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
        }

        /* ===== A4 SINGLE PAGE CONTAINER ===== */
        .page-container {
            width: 210mm;
            height: 297mm;
            padding: 0;
            position: relative;
            background: #ffffff;
            overflow: hidden;
        }

        /* ===== HEADER ===== */
        .invoice-header {
            background: #0b1a30;
            color: #ffffff;
            padding: 24px 35px 18px;
            border-bottom: 4px solid linear-gradient(180deg, #ED1C24 0%, #B1161C 32.21%, #8A1217 55.77%, #5A0D10 100%);
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .brand-name {
            font-size: 26px;
            font-weight: 900;
            letter-spacing: 2px;
            color: #ffffff;
        }
        .brand-name span { color: #EE1E26; }
        .brand-sub {
            font-size: 9px;
            color: rgba(255,255,255,0.6);
            margin-top: 2px;
            letter-spacing: 1px;
        }
        .inv-title {
            font-size: 18px;
            font-weight: 900;
            color: #EE1E26;
            text-align: right;
        }
        .inv-meta {
            font-size: 10px;
            color: rgba(255,255,255,0.7);
            text-align: right;
            margin-top: 3px;
        }

        /* ===== MAIN CONTENT ===== */
        .invoice-body {
            padding: 22px 35px;
        }

        /* Two Column Info Section */
        .info-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 14px 0;
            margin-left: -14px;
            margin-right: -14px;
            margin-bottom: 18px;
        }
        .info-box {
            width: 50%;
            vertical-align: top;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 16px;
        }
        .box-title {
            font-size: 10px;
            font-weight: 900;
            color: #EE1E26;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding-bottom: 5px;
            margin-bottom: 8px;
            border-bottom: 2px solid linear-gradient(180deg, #ED1C24 0%, #B1161C 32.21%, #8A1217 55.77%, #5A0D10 100%);
        }
        .info-row {
            margin-bottom: 5px;
        }
        .info-label {
            font-size: 9px;
            color: #64748b;
            font-weight: 700;
        }
        .info-val {
            font-size: 11px;
            font-weight: 800;
            color: #0f172a;
        }

        /* Car Section */
        .car-card {
            background: linear-gradient(135deg, #0b1a30 0%, #1e293b 100%);
            color: #ffffff;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 18px;
        }
        .car-title-tag {
            font-size: 9px;
            font-weight: 900;
            color: #EE1E26;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .car-name {
            font-size: 16px;
            font-weight: 900;
            color: #ffffff;
            margin-bottom: 10px;
        }
        .car-stats-table {
            width: 100%;
            border-collapse: collapse;
            border-top: 1px solid rgba(255,255,255,0.12);
            padding-top: 8px;
        }
        .car-stat-td {
            width: 33.33%;
            vertical-align: top;
            padding-top: 8px;
        }
        .car-stat-label {
            font-size: 8px;
            color: rgba(255,255,255,0.55);
            text-transform: uppercase;
        }
        .car-stat-val {
            font-size: 12px;
            font-weight: 800;
            color: #ffffff;
            margin-top: 1px;
        }

        /* Financial Section */
        .section-heading {
            font-size: 11px;
            font-weight: 900;
            color: #0b1a30;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 2px solid linear-gradient(180deg, #ED1C24 0%, #B1161C 32.21%, #8A1217 55.77%, #5A0D10 100%);
        }
        .fin-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .fin-table th {
            background: #f1f5f9;
            padding: 8px 12px;
            font-size: 9px;
            font-weight: 900;
            color: #475569;
            text-transform: uppercase;
            border-bottom: 1px solid #cbd5e1;
        }
        .fin-table td {
            padding: 9px 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 11px;
            color: #334155;
        }
        .fin-table .val-col {
            font-weight: 800;
            color: #0f172a;
        }
        .fin-table tr.highlight-monthly td {
            background: #fef2f2;
            color: #EE1E26;
            font-weight: 900;
        }
        .fin-table tr.total-row td {
            background: #0b1a30;
            color: #ffffff;
            font-weight: 900;
            font-size: 12px;
        }

        /* Notes Box */
        .notes-card {
            background: #fffbe6;
            border: 1px solid #ffe58f;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 15px;
            font-size: 10px;
            color: #73510d;
        }

        /* FOOTER (PINNED AT BOTTOM) */
        .invoice-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: #f8fafc;
            border-top: 3px solid linear-gradient(180deg, #ED1C24 0%, #B1161C 32.21%, #8A1217 55.77%, #5A0D10 100%);
            padding: 16px 35px;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-brand {
            font-size: 14px;
            font-weight: 900;
            color: #0b1a30;
        }
        .footer-brand span { color: #EE1E26; }
        .footer-info {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
        }
        .stamp-box {
            text-align: right;
        }
        .stamp-circle {
            display: inline-block;
            width: 62px;
            height: 62px;
            border: 2px solid #EE1E26;
            border-radius: 50%;
            text-align: center;
            padding: 10px 2px;
            color: #EE1E26;
            font-size: 8px;
            font-weight: 900;
            line-height: 1.3;
        }
        .disclaimer {
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            margin-top: 10px;
        }
    </style>
</head>
<body>

@php
    use App\Helpers\PdfHelper;

    $locale = App::getLocale();
    $isAr   = ($locale === 'ar');
    $ar     = fn($txt) => $isAr ? PdfHelper::ar($txt) : $txt;

    // Text Strings
    $txtBrandSub      = $isAr ? $ar('وكيلك الموثوق لجميع أنواع السيارات') : 'YOUR TRUSTED CAR PARTNER';
    $txtInvoice       = $ar($isAr ? 'فـاتـورة حـجـز' : 'BOOKING INVOICE');
    $txtOrderNo       = $ar($isAr ? 'رقم الطلب:' : 'Order No:');
    $txtDate          = $ar($isAr ? 'تاريخ الإصدار:' : 'Date:');

    $txtClientHeader  = $ar($isAr ? 'بيانات العميل' : 'CLIENT INFORMATION');
    $txtCompanyHeader = $ar($isAr ? 'بيانات الشركة' : 'COMPANY INFORMATION');

    $lblClientName    = $ar($isAr ? 'الاسم:' : 'Name:');
    $lblClientPhone   = $ar($isAr ? 'رقم الهاتف:' : 'Phone:');
    $lblClientEmail   = $ar($isAr ? 'البريد الإلكتروني:' : 'Email:');
    $lblClientLoc     = $ar($isAr ? 'الموقع:' : 'Location:');

    $lblCompName      = $ar($isAr ? 'اسم الشركة:' : 'Company:');
    $lblCompPhone     = $ar($isAr ? 'رقم الهاتف:' : 'Phone:');
    $lblCompEmail     = $ar($isAr ? 'البريد الإلكتروني:' : 'Email:');
    $lblCompAddr      = $ar($isAr ? 'العنوان:' : 'Address:');

    $txtCarHeader     = $ar($isAr ? 'السيارة المطلوبة' : 'REQUESTED VEHICLE');
    $lblCashPrice     = $ar($isAr ? 'السعر النقدي' : 'Cash Price');
    $lblBookingType   = $ar($isAr ? 'نوع الطلب' : 'Booking Type');
    $lblBookingDate   = $ar($isAr ? 'تاريخ الطلب' : 'Booking Date');

    $txtFinHeader     = $ar($isAr ? 'تفاصيل التمويل والأقساط' : 'FINANCIAL BREAKDOWN');
    $thItem           = $ar($isAr ? 'البيان' : 'Item Description');
    $thValue          = $ar($isAr ? 'القيمة' : 'Amount');

    $lblCarBasePrice  = $ar($isAr ? 'سعر السيارة الأساسي' : 'Vehicle Base Price');
    $lblDownPayment   = $ar($isAr ? 'الدفعة المقدمة' : 'Down Payment');
    $lblFinDuration   = $ar($isAr ? 'مدة التمويل' : 'Financing Duration');
    $lblInterestRate  = $ar($isAr ? 'نسبة الفائدة السنوية' : 'Annual Interest Rate');
    $lblMonthlyInst   = $ar($isAr ? 'القسط الشهري' : 'Monthly Installment');
    $lblTotalPrice    = $ar($isAr ? 'الإجمالي الكلي' : 'Total Amount');

    $txtYears         = $isAr ? $ar('سنة') : 'Years';
    $txtMonths        = $isAr ? $ar('شهر') : 'Months';
    $currency         = $ar($isAr ? 'ر.س' : 'SAR');

    // Values
    $siteNameSetting  = \App\Models\Setting::where('key','site_name')->value('value');
    $compPhoneSetting = \App\Models\Setting::where('key','contact_phone')->value('value');
    $compEmailSetting = \App\Models\Setting::where('key','contact_email')->value('value');
    $compAddrSetting  = \App\Models\Setting::where('key','contact_address')->value('value');

    $cleanSetting = function($v) use ($isAr) {
        if (is_array($v)) {
            return $v[$isAr ? 'ar' : 'en'] ?? $v['value'] ?? array_values($v)[0] ?? '';
        }
        return (string)$v;
    };

    $cName    = $ar($cleanSetting($siteNameSetting) ?: 'GR Motors');
    $cPhone   = $cleanSetting($compPhoneSetting) ?: '+9660549088126';
    $cEmail   = $cleanSetting($compEmailSetting) ?: 'sales@grmotors.com';
    $cAddr    = $ar($cleanSetting($compAddrSetting) ?: 'Riyadh, Saudi Arabia');

    $clientName  = $ar($booking->client_name);
    $clientPhone = $booking->client_phone;
    $clientEmail = $booking->client_email;
    $clientLoc   = $ar($booking->location);

    $carName     = $booking->car ? ($isAr ? $ar($booking->car->name) : $booking->car->name) : '---';
    $brandName   = $booking->car?->brand ? ($isAr ? $ar($booking->car->brand->name) : $booking->car->brand->name) : '';
    $bookingType = $ar(\App\Models\Booking::BOOKING_TYPES_LABELS[$booking->booking_type] ?? $booking->booking_type);
    $statusText  = $ar($booking->status_label);
@endphp

<div class="page-container">

    <!-- HEADER -->
    <div class="invoice-header">
        <table class="header-table">
            <tr>
                <td style="vertical-align: middle;">
                    <div class="brand-name">GR <span>MOTORS</span></div>
                    <div class="brand-sub">{{ $txtBrandSub }}</div>
                </td>
                <td style="vertical-align: middle; text-align: right;">
                    <div class="inv-title">{{ $txtInvoice }}</div>
                    <div class="inv-meta">
                        {{ $txtOrderNo }} #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }} | {{ $txtDate }} {{ $booking->created_at->format('Y/m/d') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- BODY -->
    <div class="invoice-body">

        <!-- Client & Company 2-Col Table -->
        <table class="info-table">
            <tr>
                <!-- Client Info -->
                <td class="info-box">
                    <div class="box-title">{{ $txtClientHeader }}</div>
                    <div class="info-row">
                        <div class="info-label">{{ $lblClientName }}</div>
                        <div class="info-val">{{ $clientName }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">{{ $lblClientPhone }}</div>
                        <div class="info-val">{{ $clientPhone }}</div>
                    </div>
                    @if($clientEmail)
                    <div class="info-row">
                        <div class="info-label">{{ $lblClientEmail }}</div>
                        <div class="info-val">{{ $clientEmail }}</div>
                    </div>
                    @endif
                    @if($clientLoc)
                    <div class="info-row">
                        <div class="info-label">{{ $lblClientLoc }}</div>
                        <div class="info-val">{{ $clientLoc }}</div>
                    </div>
                    @endif
                </td>

                <!-- Company Info -->
                <td class="info-box">
                    <div class="box-title">{{ $txtCompanyHeader }}</div>
                    <div class="info-row">
                        <div class="info-label">{{ $lblCompName }}</div>
                        <div class="info-val">{{ $cName }}</div>
                    </div>
                    @if($cPhone)
                    <div class="info-row">
                        <div class="info-label">{{ $lblCompPhone }}</div>
                        <div class="info-val">{{ $cPhone }}</div>
                    </div>
                    @endif
                    @if($cEmail)
                    <div class="info-row">
                        <div class="info-label">{{ $lblCompEmail }}</div>
                        <div class="info-val">{{ $cEmail }}</div>
                    </div>
                    @endif
                    @if($cAddr)
                    <div class="info-row">
                        <div class="info-label">{{ $lblCompAddr }}</div>
                        <div class="info-val">{{ $cAddr }}</div>
                    </div>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Car Card -->
        @if($booking->car)
        <div class="car-card">
            <div class="car-title-tag">{{ $txtCarHeader }}</div>
            <div class="car-name">{{ $brandName }} {{ $carName }} @if($booking->car->year) ({{ $booking->car->year }}) @endif</div>
            <table class="car-stats-table">
                <tr>
                    @if($booking->car->cash_price)
                    <td class="car-stat-td">
                        <div class="car-stat-label">{{ $lblCashPrice }}</div>
                        <div class="car-stat-val">{{ number_format($booking->car->cash_price) }} {{ $currency }}</div>
                    </td>
                    @endif
                    <td class="car-stat-td">
                        <div class="car-stat-label">{{ $lblBookingType }}</div>
                        <div class="car-stat-val">{{ $bookingType }}</div>
                    </td>
                    <td class="car-stat-td">
                        <div class="car-stat-label">{{ $lblBookingDate }}</div>
                        <div class="car-stat-val">{{ $booking->created_at->format('Y/m/d') }}</div>
                    </td>
                </tr>
            </table>
        </div>
        @endif

        <!-- Financial Breakdown -->
        <div class="section-heading">{{ $txtFinHeader }}</div>
        <table class="fin-table">
            <thead>
                <tr>
                    <th style="text-align: left;">{{ $thItem }}</th>
                    <th style="text-align: right;">{{ $thValue }}</th>
                </tr>
            </thead>
            <tbody>
                @if($booking->car && $booking->car->cash_price)
                <tr>
                    <td>{{ $lblCarBasePrice }}</td>
                    <td class="val-col" style="text-align: right;">{{ number_format($booking->car->cash_price) }} {{ $currency }}</td>
                </tr>
                @endif

                @if($booking->down_payment !== null)
                <tr>
                    <td>{{ $lblDownPayment }}</td>
                    <td class="val-col" style="text-align: right;">{{ number_format($booking->down_payment) }} {{ $currency }}</td>
                </tr>
                @endif

                @if($booking->duration_years)
                <tr>
                    <td>{{ $lblFinDuration }}</td>
                    <td style="text-align: right;">{{ $booking->duration_years }} {{ $txtYears }} ({{ $booking->duration_years * 12 }} {{ $txtMonths }})</td>
                </tr>
                @endif

                @if($booking->interest_rate)
                <tr>
                    <td>{{ $lblInterestRate }}</td>
                    <td style="text-align: right;">{{ $booking->interest_rate }}%</td>
                </tr>
                @endif

                @if($booking->monthly_installment)
                <tr class="highlight-monthly">
                    <td>💳 {{ $lblMonthlyInst }}</td>
                    <td style="text-align: right;">{{ number_format($booking->monthly_installment) }} {{ $currency }}</td>
                </tr>
                @endif

                @if($booking->total_price)
                <tr class="total-row">
                    <td>🏷️ {{ $lblTotalPrice }}</td>
                    <td style="text-align: right;">{{ number_format($booking->total_price) }} {{ $currency }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        @if($booking->notes)
        <div class="notes-card">
            <strong>{{ $ar($isAr ? 'ملاحظات العميل:' : 'Client Notes:') }}</strong> {{ $ar($booking->notes) }}
        </div>
        @endif

    </div>

    <!-- FOOTER -->
    <div class="invoice-footer">
        <table class="footer-table">
            <tr>
                <td style="vertical-align: middle;">
                    <div class="footer-brand">GR <span>MOTORS</span></div>
                    <div class="footer-info">
                        {{ $cPhone }} | {{ $cEmail }} | {{ $cAddr }}
                    </div>
                </td>
                <td style="vertical-align: middle; text-align: right;" class="stamp-box">
                    <div class="stamp-circle">
                        GR<br>MOTORS<br>✓
                    </div>
                </td>
            </tr>
        </table>
        <div class="disclaimer">
            {{ $ar($isAr ? 'وثيقة إلكترونية رسمية صادرة من النظام — رقم الطلب' : 'Official Electronic Document — Order') }} #{{ $booking->id }} | {{ now()->format('Y/m/d H:i') }}
        </div>
    </div>

</div>

</body>
</html>
