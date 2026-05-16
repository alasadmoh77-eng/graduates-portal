<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; }
        .header { text-align: center; margin-bottom: 50px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .content { margin: 20px; line-height: 1.6; }
        .box { border: 1px solid #ccc; padding: 15px; margin-top: 20px; }
        .qr-section { margin-top: 50px; text-align: right; }
        .serial { color: #888; font-size: 12px; }
    </style>
</head>
<body dir="{{ $request->language == 'ar' ? 'rtl' : 'ltr' }}">
    <div class="header">
        <h1>{{ $request->language == 'ar' ? 'جامعة إقليم سبأ' : 'Sabaa Region University' }}</h1>
        <h2>{{ $request->language == 'ar' ? $request->documentType->name_ar : $request->documentType->name_en }}</h2>
    </div>

    <div class="content">
        <p>
            {{ $request->language == 'ar' ? 'تشهد جامعة إقليم سبأ بأن الخريج:' : 'This is to certify that the graduate:' }}
            <strong>{{ $request->graduate->user->name }}</strong>
        </p>
        <p>
            {{ $request->language == 'ar' ? 'قد تخرج من قسم:' : 'Has graduated from the department of:' }}
            <strong>{{ $request->language == 'ar' ? $request->graduate->major->name_ar : $request->graduate->major->name_en }}</strong>
        </p>
        <p>
            {{ $request->language == 'ar' ? 'للعام الجامعي:' : 'For the academic year:' }}
            <strong>{{ $request->graduate->graduation_year }}</strong>
        </p>
    </div>

    <div class="qr-section">
        <p>{{ $request->language == 'ar' ? 'للتحقق من صحة الوثيقة، امسح الرمز أدناه:' : 'To verify the authenticity of this document, scan the QR code below:' }}</p>
        <img src="data:image/png;base64,{{ $qrCode }}" width="150">
        <br>
        <span class="serial">Serial: {{ $document->serial_number }}</span>
    </div>

    <div style="margin-top: 100px; text-align: center;">
        <p>_______________________</p>
        <p>{{ $request->language == 'ar' ? 'شؤون الخريجين' : 'Graduates Affairs' }}</p>
    </div>
</body>
</html>
