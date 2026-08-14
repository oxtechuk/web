@php
    $locale = app()->getLocale();
    $isAr = ($locale === 'ar');
    $dir = $isAr ? 'rtl' : 'ltr';
    $textAlign = $isAr ? 'right' : 'left';

    $fontPlain = public_path('assets/fonts/Bahij_TheSansArabic-Plain.ttf');
    $fontSemiBold = public_path('assets/fonts/Bahij_TheSansArabic-SemiBold.ttf');

    // Helper closure to format & reshape text if Arabic
    $fmt = function(?string $text) use ($isAr) {
        if (empty($text)) return '';
        return $isAr ? \App\Helpers\PdfHelper::ar($text) : $text;
    };

    // Translations helper
    $tr = function(string $ar, string $en) use ($isAr, $fmt) {
        return $isAr ? $fmt($ar) : $en;
    };

    $carNameTrans = method_exists($car, 'getTranslation') ? ($car->getTranslation('name', $locale, false) ?: $car->name) : $car->name;
    $brandNameTrans = $car->brand ? (method_exists($car->brand, 'getTranslation') ? ($car->brand->getTranslation('name', $locale, false) ?: $car->brand->name) : $car->brand->name) : '';
    $catNameTrans = $car->category ? (method_exists($car->category, 'getTranslation') ? ($car->category->getTranslation('name', $locale, false) ?: $car->category->name) : $car->category->name) : '';
    $logoBase64 = $pdfService->getCleanImagePath('assets/images/logo.png');

    // Construct full car title matching template
    $yearVal = $car->year ?: 2026;
    $brandVal = $brandNameTrans ?: 'Toyota';
    $modelVal = $car->model ?: 'RAV4';
    $trimVal = isset($car->specs['trim']) ? $car->specs['trim'] : ($car->type ? ucfirst($car->type) : 'Adventure');
    $engineVal = isset($car->specs['engine']) ? $car->specs['engine'] : (isset($car->specs['hp']) ? $car->specs['hp'] : '2.5L AT');
    $fuelVal = isset($car->specs['fuel']) ? $car->specs['fuel'] : (isset($car->specs['fuel_type']) ? $car->specs['fuel_type'] : 'Hybrid');
    $colorVal = $car->color ?: (isset($car->colors[0]['name']) ? $car->colors[0]['name'] : 'Gray-Black');

    if (!empty($carNameTrans) && strlen($carNameTrans) > 12) {
        $headerTitleStr = $carNameTrans;
    } else {
        $headerTitleStr = "{$yearVal} {$brandVal} {$modelVal} {$trimVal} {$engineVal} {$fuelVal} ({$colorVal})";
    }

    // Key Specs list for Page 1 table
    $keySpecs = [
        ['label' => $tr('الحالة', 'Condition'), 'value' => !empty($car->availability_status) ? ($isAr ? $fmt($car->availability_status) : ucfirst($car->availability_status)) : $tr('جديد', 'New')],
        ['label' => $tr('نوع الهيكل', 'Body'), 'value' => $catNameTrans ? $fmt($catNameTrans) : ($car->type ? ucfirst($car->type) : 'SUV')],
        ['label' => $tr('الماركة', 'Make'), 'value' => $brandNameTrans ? $fmt($brandNameTrans) : 'Toyota'],
        ['label' => $tr('الموديل', 'Model'), 'value' => $car->model ?: 'RAV4'],
        ['label' => $tr('ممشى السيارة', 'Mileage'), 'value' => isset($car->specs['mileage']) ? $car->specs['mileage'] : (isset($car->specs['odometer']) ? $car->specs['odometer'] : '0 KM')],
        ['label' => $tr('نوع الوقود', 'Fuel type'), 'value' => isset($car->specs['fuel']) ? $fmt($car->specs['fuel']) : (isset($car->specs['fuel_type']) ? $fmt($car->specs['fuel_type']) : $tr('هجين', 'Hybrid'))],
        ['label' => $tr('المحرك', 'Engine'), 'value' => isset($car->specs['engine']) ? $car->specs['engine'] : (isset($car->specs['hp']) ? $car->specs['hp'] : '2.5')],
        ['label' => $tr('سنة الصنع', 'Year'), 'value' => (string)($car->year ?: 2026)],
        ['label' => $tr('اللون الخارجي', 'Exterior Color'), 'value' => $car->color ? $fmt($car->color) : (isset($car->colors[0]['name']) ? $fmt($car->colors[0]['name']) : $tr('رمادي', 'Grey'))],
        ['label' => $tr('اللون الداخلي', 'Interior Color'), 'value' => isset($car->specs['interior_color']) ? $fmt($car->specs['interior_color']) : (isset($car->specs['interior']) ? $fmt($car->specs['interior']) : $tr('أسود', 'Black'))],
        ['label' => $tr('ناقل الحركة', 'Transmission'), 'value' => isset($car->specs['gearbox']) ? $fmt($car->specs['gearbox']) : (isset($car->specs['transmission']) ? $fmt($car->specs['transmission']) : $tr('أوتوماتيك', 'Automatic'))],
        ['label' => $tr('نظام الدفع', 'Drive'), 'value' => isset($car->specs['drive']) ? $car->specs['drive'] : (isset($car->specs['drive_type']) ? $car->specs['drive_type'] : 'AWD')],
        ['label' => $tr('الفئة', 'Trim'), 'value' => isset($car->specs['trim']) ? $car->specs['trim'] : ($car->type ? ucfirst($car->type) : 'Adventure')],
    ];

    if (!empty($car->specs) && is_array($car->specs)) {
        foreach ($car->specs as $spKey => $spVal) {
            if (is_array($spVal) && isset($spVal['label']) && isset($spVal['value'])) {
                $exists = false;
                foreach ($keySpecs as $ex) {
                    if (strcasecmp($ex['label'], $spVal['label']) === 0) { $exists = true; break; }
                }
                if (!$exists) {
                    $keySpecs[] = ['label' => $tr($spVal['label'], $spVal['label']), 'value' => $tr($spVal['value'], $spVal['value'])];
                }
            }
        }
    }

    // Categorized features setup
    $allFeatureItems = [];

    if ($car->features_list && $car->features_list->count() > 0) {
        foreach ($car->features_list as $feat) {
            $name = method_exists($feat, 'getTranslation') ? ($feat->getTranslation('name', $locale, false) ?: $feat->name) : $feat->name;
            if (!empty($name)) $allFeatureItems[] = trim($name);
        }
    }

    if ($car->specifications && $car->specifications->count() > 0) {
        foreach ($car->specifications as $spec) {
            $name = method_exists($spec, 'getTranslation') ? ($spec->getTranslation('name', $locale, false) ?: $spec->name) : $spec->name;
            if (!empty($name)) $allFeatureItems[] = trim($name);
        }
    }

    if (!empty($car->features)) {
        $featText = is_array($car->features) ? implode(',', $car->features) : (string)$car->features;
        $splitFeats = explode(',', $featText);
        foreach ($splitFeats as $fItem) {
            $fItem = trim($fItem);
            if (!empty($fItem) && !in_array($fItem, $allFeatureItems)) {
                $allFeatureItems[] = $fItem;
            }
        }
    }

    $categorizedFeatures = [
        'interior' => [
            'title' => $tr('الميزات الداخلية', 'Interior Features'),
            'items' => [],
        ],
        'exterior' => [
            'title' => $tr('الميزات الخارجية', 'Exterior Features'),
            'items' => [],
        ],
        'safety' => [
            'title' => $tr('أنظمة الأمان', 'Safety Features'),
            'items' => [],
        ],
        'extra' => [
            'title' => $tr('ميزات إضافية', 'Extra Features'),
            'items' => [],
        ],
    ];

    $interiorKeywords = ['air', 'conditioner', 'am/fm', 'radio', 'android', 'bluetooth', 'center console', 'cooling', 'cup holder', 'electric seat', 'glove', 'infotainment', 'keyless', 'leather', 'manual-adjustable', 'navigation', 'power seat', 'power window', 'push start', 'usb', 'steering', 'interior', 'تكييف', 'مقاعد', 'شاشة', 'بلوتوث', 'راديو', 'جلد', 'كونسول', 'ملاحة', 'زجاج'];
    $exteriorKeywords = ['alloy', 'wheels', 'led', 'headlight', 'head light', 'tail light', 'mirror', 'spoiler', 'roof', 'sunroof', 'door', 'body', 'exterior', 'جنوط', 'كشافات', 'مرايا', 'جناح', 'سقف', 'فتحة', 'أنوار'];
    $safetyKeywords = ['abs', 'cruise', 'airbag', 'anti-lock', 'blind spot', 'driver', 'passenger', 'brakeforce', 'ebd', 'esc', 'lane', 'parking', 'sensor', 'radar', 'camera', 'proximity', 'traction', 'tcs', 'safety', 'أمان', 'وسائد', 'فرامل', 'حساسات', 'كاميرا', 'رادار', 'توازن', 'مثبت'];

    if (!empty($allFeatureItems)) {
        foreach ($allFeatureItems as $item) {
            $lower = mb_strtolower($item);
            $matched = false;

            foreach ($interiorKeywords as $kw) {
                if ($kw !== '' && mb_stripos($lower, $kw) !== false) {
                    $categorizedFeatures['interior']['items'][] = $item;
                    $matched = true;
                    break;
                }
            }
            if ($matched) continue;

            foreach ($exteriorKeywords as $kw) {
                if ($kw !== '' && mb_stripos($lower, $kw) !== false) {
                    $categorizedFeatures['exterior']['items'][] = $item;
                    $matched = true;
                    break;
                }
            }
            if ($matched) continue;

            foreach ($safetyKeywords as $kw) {
                if ($kw !== '' && mb_stripos($lower, $kw) !== false) {
                    $categorizedFeatures['safety']['items'][] = $item;
                    $matched = true;
                    break;
                }
            }
            if ($matched) continue;

            $categorizedFeatures['extra']['items'][] = $item;
        }
    }

    $hasAnyFeatures = false;
    foreach ($categorizedFeatures as $cKey => $cVal) {
        if (!empty($cVal['items'])) {
            $hasAnyFeatures = true;
            break;
        }
    }

    if (!$hasAnyFeatures) {
        $categorizedFeatures['interior']['items'] = [
            $tr('تكييف هواء', 'Air Conditioner'), $tr('مشغل AM/FM', 'AM/FM Player'), 'Android Auto', 'Bluetooth',
            $tr('كونسول وسطي', 'Center Console'), $tr('مقاعد تبريد', 'Cooling Seats'), $tr('مثبت السرعة', 'Cruiser Control'), $tr('حامل أكواب', 'Cup Holder'),
            $tr('مقعد كهربائي', 'Electric Seat'), $tr('درج الرف', 'Glove Box'), $tr('شاشة معلومات وترفيه', 'Infotainment Screen'), $tr('دخول بدون مفتاح', 'Keyless Entry'),
            $tr('مقاعد جلدية', 'Leather Seats'), $tr('مقاعد قابلة للتعديل يدوياً', 'Manual-Adjustable Seats'), $tr('نظام الملاحة', 'Navigation'), $tr('مقاعد كهربائية', 'Power seats'),
            $tr('زجاج كهربائي', 'Power Windows'), $tr('زر تشغيل المحرك', 'Push Start'), 'USB/ Type C'
        ];
        $categorizedFeatures['exterior']['items'] = [
            $tr('جنوط ألومنيوم', 'Alloy Wheels'), $tr('مصابيح LED بالكامل', 'Full LED headlights'), $tr('مصابيح أمامية LED', 'LED Head Light'), $tr('مصابيح خلفية LED', 'LED Tail Light'),
            $tr('مرايا كهربائية', 'Power Mirrors'), $tr('جناح خلفي', 'Rear Spoiler'), $tr('عوارض سقف', 'Roof Rails'), $tr('فتحة سقف', 'Sunroof'),
            $tr('عجلة قيادة تلسكوبية', 'Telescopic Steering Wheel')
        ];
        $categorizedFeatures['safety']['items'] = [
            'ABS', $tr('مثبت سرعة متكيف', 'Adaptive Cruise Control'), $tr('وسائد هوائية', 'Airbags'), $tr('نظام منع إغلاق المكابح', 'Anti-Lock Braking System (ABS)'),
            $tr('مراقبة النقطة العمياء', 'Blind Spot Monitoring'), $tr('وسائد هوائية أمامية متقدمة', 'Driver and front-passenger advanced airbags'),
            $tr('توزيع قوة الفرامل إلكترونياً (EBD)', 'Electronic Brakeforce Distribution (EBD)'), $tr('نظام الثبات الإلكتروني (ESC)', 'Electronic Stability Control (ESC)'),
            $tr('رادار تحذير المغادرة من المسار', 'Lane Departure Warning Radar'), $tr('نظام الحفاظ على المسار', 'Lane Keeping Assist radar-based adaptive systems'),
            $tr('حساسات ركن مع كاميرا خلفية', 'Parking Sensor Rear Camera'), $tr('حساسات تقارب', 'Proximity sensors'), $tr('نظام التحكم في السحب (TCS)', 'Traction Control System (TCS)')
        ];
        $categorizedFeatures['extra']['items'] = [
            $tr('منفذ طاقة 12 فولت', '12V Power Outlet'), 'Apple CarPlay', $tr('فرملة الطوارئ التلقائية', 'Automatic Emergency Braking'), $tr('قفل مركزي', 'Central locking'),
            $tr('قفل حماية الأطفال', 'Child Lock'), $tr('إضاءة نهارية', 'Daytime Running Light'), $tr('عدادات رقمية', 'Digital Instrumental Cluster'), $tr('نظام المساعدة في النزول', 'Hill Descent Control'),
            $tr('أنماط قيادة متعددة', 'Multiple Drive Modes'), $tr('قفل كهربائي', 'Power Lock'), $tr('قابل للتعديل كهربائياً', 'Power-Adjustable'), $tr('دخول سلس', 'Seamless Entry')
        ];
    }
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $headerTitleStr }} - Spec Sheet</title>
    <style>
        @font-face {
            font-family: 'BahijTheSans';
            src: url('{{ $fontPlain }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'BahijTheSans';
            src: url('{{ $fontSemiBold }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        @page {
            margin: 0px;
            size: A4 portrait;
        }
        body {
            font-family: 'BahijTheSans', 'DejaVu Sans', 'Helvetica Neue', 'Arial', sans-serif;
            color: #1e293b;
            line-height: 1.4;
            margin: 0px;
            padding: 0px;
            background-color: #ffffff;
            direction: ltr;
            text-align: {{ $textAlign }};
        }
        .page {
            position: relative;
            width: 100%;
            height: 297mm;
            page-break-after: always;
            box-sizing: border-box;
        }
        .page:last-child {
            page-break-after: avoid;
        }

        /* Full Width Header Bar */
        .header-bar-table {
            width: 100%;
            background-color: #0b1a30;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
        }
        .header-bar-table td {
            padding: 10px 24px;
            vertical-align: middle;
        }
        .header-logo-cell-left {
            width: 25%;
            text-align: left;
        }
        .header-title-cell-right {
            width: 75%;
            text-align: right;
            color: #ffffff;
            font-size: 13.5px;
            font-weight: bold;
            letter-spacing: 0.2px;
        }
        .header-title-cell-left {
            width: 75%;
            text-align: left;
            color: #ffffff;
            font-size: 13.5px;
            font-weight: bold;
            letter-spacing: 0.2px;
        }
        .header-logo-cell-right {
            width: 25%;
            text-align: right;
        }
        .header-logo-img {
            height: 24px;
            width: auto;
            display: inline-block;
        }

        /* Page Content Area */
        .content {
            padding: 20px 35px;
        }

        /* Title Box */
        .title-box-wrapper {
            text-align: center;
            margin-bottom: 20px;
        }
        .title-box {
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 18px;
            display: inline-block;
            width: 90%;
            margin: 0 auto;
        }
        .title-box h1 {
            font-size: 18px;
            margin: 0;
            color: #0f172a;
            font-weight: bold;
            line-height: 1.3;
        }

        /* Main Image */
        .main-image-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .main-image {
            width: 100%;
            max-width: 530px;
            height: 275px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
        }

        /* Key Specs Table */
        .specs-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .specs-table tr {
            border-bottom: 1px solid #e2e8f0;
        }
        .specs-table td {
            padding: 8px 10px;
            font-size: 12.5px;
        }
        .specs-col-left {
            width: 50%;
            text-align: left;
        }
        .specs-col-right {
            width: 50%;
            text-align: right;
        }

        /* Categorized Features Sections (Page 2) */
        .category-block {
            margin-bottom: 22px;
            page-break-inside: avoid;
        }
        .category-title {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 4px;
            text-align: {{ $textAlign }};
        }
        .category-divider {
            border-bottom: 1px solid #cbd5e1;
            margin-bottom: 10px;
            width: 100%;
        }
        .features-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .features-grid td {
            width: 25%;
            padding: 5px 6px;
            font-size: 11.5px;
            color: #334155;
            vertical-align: top;
            text-align: {{ $textAlign }};
        }

        /* Gallery Page Vertical Stack */
        .pdf-gallery-vertical {
            width: 100%;
            margin-top: 10px;
        }
        .gallery-vertical-item {
            text-align: center;
            margin-bottom: 20px;
            height: 370px;
            overflow: hidden;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
        }
        .gallery-vertical-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body>

    <!-- PAGE 1: Key Specs & Main Photo -->
    <div class="page">
        <!-- Dark Top Header Bar -->
        <table class="header-bar-table">
            <tr>
                @if($isAr)
                    <td class="header-logo-cell-left">
                        @if($logoBase64)
                            <img src="{{ $logoBase64 }}" class="header-logo-img" alt="Logo">
                        @endif
                    </td>
                    <td class="header-title-cell-right">
                        {{ $fmt($headerTitleStr) }}
                    </td>
                @else
                    <td class="header-title-cell-left">
                        {{ $headerTitleStr }}
                    </td>
                    <td class="header-logo-cell-right">
                        @if($logoBase64)
                            <img src="{{ $logoBase64 }}" class="header-logo-img" alt="Logo">
                        @endif
                    </td>
                @endif
            </tr>
        </table>

        <div class="content">
            <!-- Centered Header Box -->
            <div class="title-box-wrapper">
                <div class="title-box">
                    <h1>{{ $fmt($headerTitleStr) }}</h1>
                </div>
            </div>

            <!-- Main Car Image -->
            @php
                $thumbnailAbsolute = $pdfService->getCleanImagePath((string)$car->thumbnail);
            @endphp
            @if($thumbnailAbsolute)
                <div class="main-image-container">
                    <img src="{{ $thumbnailAbsolute }}" class="main-image" alt="Car Thumbnail" />
                </div>
            @endif

            <!-- 2-Column Key Specs Table -->
            <table class="specs-table">
                @foreach($keySpecs as $spec)
                    @if(!empty($spec['value']))
                    <tr>
                        @if($isAr)
                            {{-- Arabic Layout: Left = Value, Right = Label --}}
                            <td class="specs-col-left" style="color: #334155;">{{ $fmt($spec['value']) }}</td>
                            <td class="specs-col-right" style="font-weight: bold; color: #0f172a;">{{ $fmt($spec['label']) }}</td>
                        @else
                            {{-- English Layout: Left = Label, Right = Value --}}
                            <td class="specs-col-left" style="font-weight: bold; color: #0f172a;">{{ $spec['label'] }}</td>
                            <td class="specs-col-right" style="color: #334155;">{{ $spec['value'] }}</td>
                        @endif
                    </tr>
                    @endif
                @endforeach
            </table>
        </div>
    </div>

    <!-- PAGE 2: Categorized Features -->
    <div class="page">
        <!-- Dark Top Header Bar -->
        <table class="header-bar-table">
            <tr>
                @if($isAr)
                    <td class="header-logo-cell-left">
                        @if($logoBase64)
                            <img src="{{ $logoBase64 }}" class="header-logo-img" alt="Logo">
                        @endif
                    </td>
                    <td class="header-title-cell-right">
                        {{ $fmt($headerTitleStr) }}
                    </td>
                @else
                    <td class="header-title-cell-left">
                        {{ $headerTitleStr }}
                    </td>
                    <td class="header-logo-cell-right">
                        @if($logoBase64)
                            <img src="{{ $logoBase64 }}" class="header-logo-img" alt="Logo">
                        @endif
                    </td>
                @endif
            </tr>
        </table>

        <div class="content" style="padding-top: 25px;">
            @foreach($categorizedFeatures as $cKey => $cData)
                @if(!empty($cData['items']))
                <div class="category-block">
                    <div class="category-title">{{ $fmt($cData['title']) }}</div>
                    <div class="category-divider"></div>
                    <table class="features-grid">
                        @foreach(array_chunk($cData['items'], 4) as $chunk)
                            @php
                                $rowItems = array_pad($chunk, 4, '');
                                if ($isAr) {
                                    $rowItems = array_reverse($rowItems);
                                }
                            @endphp
                            <tr>
                                @foreach($rowItems as $featItem)
                                    <td>{{ $fmt($featItem) }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </table>
                </div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- GALLERY PAGES (If Car Has Extra Images) -->
    @php
        $exteriorImages = $car->images->filter(function($img) {
            return $img->type !== 'interior';
        });
        $interiorImages = $car->images->where('type', 'interior');
    @endphp

    {{-- Exterior Images --}}
    @if($exteriorImages->count() > 0)
        @foreach($exteriorImages->chunk(2) as $index => $chunk)
            <div class="page">
                <table class="header-bar-table">
                    <tr>
                        @if($isAr)
                            <td class="header-logo-cell-left">
                                @if($logoBase64)
                                    <img src="{{ $logoBase64 }}" class="header-logo-img" alt="Logo">
                                @endif
                            </td>
                            <td class="header-title-cell-right">
                                {{ $fmt($headerTitleStr) }}
                            </td>
                        @else
                            <td class="header-title-cell-left">
                                {{ $headerTitleStr }}
                            </td>
                            <td class="header-logo-cell-right">
                                @if($logoBase64)
                                    <img src="{{ $logoBase64 }}" class="header-logo-img" alt="Logo">
                                @endif
                            </td>
                        @endif
                    </tr>
                </table>

                <div class="content" style="padding: 20px 35px 10px;">
                    <div class="pdf-gallery-vertical">
                        @foreach($chunk as $img)
                            @php
                                $imgAbsolute = $pdfService->getCleanImagePath((string)$img->image_path);
                            @endphp
                            @if($imgAbsolute)
                                <div class="gallery-vertical-item">
                                    <img src="{{ $imgAbsolute }}" class="gallery-vertical-img" alt="Exterior Image" />
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    {{-- Interior Images --}}
    @if($interiorImages->count() > 0)
        @foreach($interiorImages->chunk(2) as $index => $chunk)
            <div class="page">
                <table class="header-bar-table">
                    <tr>
                        @if($isAr)
                            <td class="header-logo-cell-left">
                                @if($logoBase64)
                                    <img src="{{ $logoBase64 }}" class="header-logo-img" alt="Logo">
                                @endif
                            </td>
                            <td class="header-title-cell-right">
                                {{ $fmt($headerTitleStr) }}
                            </td>
                        @else
                            <td class="header-title-cell-left">
                                {{ $headerTitleStr }}
                            </td>
                            <td class="header-logo-cell-right">
                                @if($logoBase64)
                                    <img src="{{ $logoBase64 }}" class="header-logo-img" alt="Logo">
                                @endif
                            </td>
                        @endif
                    </tr>
                </table>

                <div class="content" style="padding: 20px 35px 10px;">
                    <div class="pdf-gallery-vertical">
                        @foreach($chunk as $img)
                            @php
                                $imgAbsolute = $pdfService->getCleanImagePath((string)$img->image_path);
                            @endphp
                            @if($imgAbsolute)
                                <div class="gallery-vertical-item">
                                    <img src="{{ $imgAbsolute }}" class="gallery-vertical-img" alt="Interior Image" />
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @endif

</body>
</html>
