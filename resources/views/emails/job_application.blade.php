<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f4f5f8;
            color: #333333;
            margin: 0;
            padding: 0;
            direction: rtl;
            text-align: right;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border-top: 5px solid #E30613;
        }
        .header {
            background-color: #0f0f12;
            padding: 30px;
            text-align: center;
        }
        .header h2 {
            color: #ffffff;
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }
        .content {
            padding: 30px;
        }
        .info-group {
            margin-bottom: 20px;
            border-bottom: 1px solid #eeeeee;
            padding-bottom: 15px;
        }
        .info-label {
            font-size: 13px;
            color: #888888;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 16px;
            color: #0f0f12;
            font-weight: 700;
        }
        .cover-letter {
            background-color: #f9f9fc;
            border-right: 4px solid #E30613;
            padding: 15px;
            border-radius: 4px;
            font-size: 14px;
            line-height: 1.6;
            color: #555555;
            white-space: pre-line;
        }
        .footer {
            background-color: #f4f5f8;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777777;
            border-top: 1px solid #eeeeee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>طلب توظيف جديد من الموقع</h2>
        </div>
        <div class="content">
            <div class="info-group">
                <div class="info-label">اسم المتقدم بالكامل:</div>
                <div class="info-value">{{ $application->name }}</div>
            </div>
            
            <div class="info-group">
                <div class="info-label">البريد الإلكتروني:</div>
                <div class="info-value" dir="ltr" style="text-align: right;">{{ $application->email }}</div>
            </div>
            
            <div class="info-group">
                <div class="info-label">رقم الهاتف / الجوال:</div>
                <div class="info-value" dir="ltr" style="text-align: right;">{{ $application->phone }}</div>
            </div>
            
            <div class="info-group">
                <div class="info-label">الوظيفة المتقدم لها:</div>
                <div class="info-value">{{ $application->job_title }}</div>
            </div>

            @if($application->cover_letter)
            <div class="info-group">
                <div class="info-label">رسالة التغطية / نبذة:</div>
                <div class="cover-letter">{{ $application->cover_letter }}</div>
            </div>
            @endif

            <p style="font-size: 13px; color: #666; margin-top: 25px;">
                * تم إرفاق ملف السيرة الذاتية الخاصة بالمتقدم مع هذا البريد الإلكتروني.
            </p>
        </div>
        <div class="footer">
            هذا البريد مرسل تلقائياً من نظام التوظيف في GR Motors.
        </div>
    </div>
</body>
</html>
