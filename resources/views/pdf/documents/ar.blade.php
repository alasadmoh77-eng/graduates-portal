<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page { margin: 10mm 15mm; }
        body { font-family: 'DejaVu Sans', sans-serif; direction: rtl; text-align: right; padding: 0; color: #0f172a; font-size: 10px; line-height: 1.5; }

        .page-border { border: 2px solid #0b2545; outline: 1px solid #b89047; outline-offset: -3px; padding: 15px; min-height: 250mm; position: relative; }
        .masthead-top { width: 100%; border-bottom: 2px solid #0b2545; padding-bottom: 6px; margin-bottom: 15px; }
        .masthead-top td { vertical-align: middle; font-size: 8.5px; font-weight: bold; line-height: 1.3; color: #0b2545; }
        .document-title { text-align: center; font-size: 18px; font-weight: bold; color: #0b2545; margin: 30px 0; text-decoration: underline; text-underline-offset: 4px; }
        .content { font-size: 12px; margin-bottom: 40px; }
        .field { margin-bottom: 12px; }
        .footer-sig-stamp { width: 100%; margin-top: 50px; table-layout: fixed; }
        .footer-sig-stamp td { text-align: center; vertical-align: top; }
        .sig-line { border-top: 1px dotted #b89047; margin-top: 40px; font-size: 8px; color: #64748b; padding-top: 4px; width: 60%; margin-left: auto; margin-right: auto; }
        .footer-verify { text-align: center; font-size: 7.5px; color: #64748b; font-family: 'Amiri', 'DejaVu Sans', sans-serif; position: absolute; bottom: 10px; width: 95%; left: 2.5%; border-top: 1px dashed #cbd5e1; padding-top: 6px; }
    </style>
</head>
@php
    if (!function_exists('ar')) {
        function ar($text) {
            if ($text === null || $text === '') return $text;
            return \App\Helpers\ArabicReshaper::utf8Glyphs($text);
        }
    }

    $arMajor = $request->user->graduate->major->name_ar ?? '---';
    $arFaculty = $request->user->graduate->major->faculty->name_ar ?? '---';
    $arDegree = $request->user->graduate->major->degree_name_ar ?? 'بكالوريوس';
@endphp
<body>
    <div class="page-border">
        <!-- 3-Column Premium Header with University Logo -->
        <table class="masthead-top">
            <tr>
                <td style="text-align: right; width: 38%;">
                    {{ ar('الجمهورية اليمنية') }}<br>
                    {{ ar('جامعة إقليم سبأ') }}<br>
                    {{ ar('نيابة شؤون الطلاب') }}<br>
                    {{ ar('الإدارة العامة للقبول والتسجيل') }}
                </td>
                <td style="text-align: center; width: 24%; vertical-align: middle;">
                    <img src="{{ public_path('assets/images/university-logo-pdf.png') }}" alt="SRU Logo" style="height: 55px; width: auto; display: block; margin: 0 auto;">
                </td>
                <td style="text-align: left; width: 38%; direction: ltr;">
                    Republic of Yemen<br>
                    Saba Region University<br>
                    Student Affairs<br>
                    Admission & Registration Dept.
                </td>
            </tr>
        </table>

        <div class="document-title">
            {{ ar($request->documentType->name_ar) }}
        </div>

        <div class="content">
            <div class="field">
                {{ ar('تشهد جامعة إقليم سبأ بأن الخريج / الخريجة:') }} <strong>{{ ar($request->user->name) }}</strong>
            </div>
            <div class="field">
                {{ ar('قد أكمل بنجاح متطلبات التخرج لنيل درجة') }} <strong>{{ ar($arDegree) }}</strong>
                {{ ar('في كلية:') }} <strong>{{ ar($arFaculty) }}</strong>
                {{ ar('تخصص:') }} <strong>{{ ar($arMajor) }}</strong>
            </div>
            <div class="field">
                {{ ar('دفعة عام:') }} <strong>{{ $request->user->graduate->graduation_year }}م</strong>
            </div>
            <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">
            <div class="field" style="font-size: 10px; color: #475569;">
                {{ ar('كود التتبع:') }} <span style="font-family: monospace; font-weight: bold;">{{ $request->tracking_code }}</span><br>
                {{ ar('الرقم التسلسلي:') }} <span style="font-family: monospace; font-weight: bold;">{{ $serial_number }}</span><br>
                {{ ar('تاريخ الإصدار:') }} {{ $issue_date }}
            </div>
        </div>

        <!-- Premium Stamp and Signature section -->
        <table class="footer-sig-stamp">
            <tr>
                <td style="width: 35%;">
                    {{ ar('ختم وتوقيع عميد الكلية') }}
                    <div class="sig-line">Dean of College Sign/Stamp</div>
                </td>
                <td style="width: 30%;">
                    <div style="border: 1.5px dashed #1a237e; border-radius: 6px; display: inline-block; padding: 10px 15px; margin-top: 10px;">
                        <span style="font-size: 7px; font-weight: bold; color: #1a237e;">{{ ar('الختم الرسمي للجامعة') }}</span><br>
                        <span style="font-size: 5.5px; color: #64748b;">OFFICIAL SEAL</span>
                    </div>
                </td>
                <td style="width: 35%;">
                    {{ ar('ختم وتوقيع مسجل العام للجامعة') }}
                    <div class="sig-line">General Registrar Sign/Stamp</div>
                </td>
            </tr>
        </table>

        <!-- QR Code section -->
        <div style="margin-top: 40px; text-align: left; padding-left: 20px;">
            <img src="data:image/svg+xml;base64,{{ $qr_code }}" alt="QR Code" style="width: 70px; height: 70px; border: 1px solid #1a237e; padding: 2px;">
            <p style="font-size: 6.5px; margin-top: 3px; color: #64748b;">{{ ar('امسح للتحقق من صحة المستند') }}</p>
        </div>


    </div>
</body>
</html>