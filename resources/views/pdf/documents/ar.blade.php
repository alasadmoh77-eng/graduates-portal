<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            direction: rtl;
            text-align: right;
            padding: 50px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1a237e;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        .header h1 { color: #1a237e; margin-bottom: 5px; }
        .document-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 40px;
            text-decoration: underline;
        }
        .content { font-size: 18px; line-height: 1.8; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; width: 150px; display: inline-block; }
        .footer {
            margin-top: 50px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
            display: flex;
            justify-content: space-between;
        }
        .qr-section { text-align: left; position: absolute; bottom: 50px; left: 50px; }
        .qr-section img { width: 120px; }
        .signature-section { text-align: right; position: absolute; bottom: 50px; right: 50px; }
        .verification-info {
            position: absolute;
            bottom: 20px;
            width: 100%;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
@php
    function ar($text) {
        return \App\Helpers\ArabicReshaper::utf8Glyphs($text);
    }
@endphp
<body>
    <div class="header">
        <h1>{{ ar('جامعة إقليم سبأ') }}</h1>
        <h3>{{ ar('نيابة شؤون الطلاب - الإدارة العامة للقبول والتسجيل') }}</h3>
    </div>

    <div class="document-title">
        {{ ar($request->documentType->name_ar) }}
    </div>

    <div class="content">
        <div class="field">{{ ar('تشهد جامعة إقليم سبأ بأن الخريج/') }} <strong>{{ ar($request->user->name) }}</strong></div>
        <div class="field">{{ ar('قد أكمل متطلبات التخرج لنيل درجة البكالوريوس في:') }} <strong>{{ ar($request->user->graduate->major->name_ar) }}</strong></div>
        <div class="field">{{ ar('دفعة عام:') }} <strong>{{ $request->user->graduate->graduation_year }}</strong></div>
        <div class="field">{{ ar('كود التتبع:') }} {{ $request->tracking_code }}</div>
        <div class="field">{{ ar('الرقم التسلسلي:') }} {{ $serial_number }}</div>
        <div class="field">{{ ar('تاريخ الإصدار:') }} {{ $issue_date }}</div>
    </div>

    <div class="signature-section">
        <p>{{ ar('ختم وتوقيع مسجل العام') }}</p>
        <div style="height: 80px;"></div>
        <p>....................................</p>
    </div>

    <div class="qr-section">
        <img src="data:image/svg+xml;base64,{{ $qr_code }}" alt="QR Code">
        <p style="font-size: 10px; margin-top: 5px;">{{ ar('امسح للتحقق من صحة المستند') }}</p>
    </div>

    <div class="verification-info">
        {{ ar('يمكن التحقق من صحة هذا المستند عبر الرابط:') }} {{ config('app.url') }}/verify/{{ $qr_token }}
    </div>
</body>
</html>
