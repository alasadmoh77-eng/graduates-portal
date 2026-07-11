<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 10mm 15mm; }
        body { font-family: 'DejaVu Sans', sans-serif; direction: ltr; text-align: left; padding: 0; color: #0f172a; font-size: 10px; line-height: 1.5; }
        .page-border { border: 3px double #1a237e; padding: 15px; min-height: 250mm; position: relative; }
        .masthead-top { width: 100%; border-bottom: 2px solid #1a237e; padding-bottom: 6px; margin-bottom: 15px; }
        .masthead-top td { vertical-align: middle; font-size: 8.5px; font-weight: bold; line-height: 1.3; }
        .masthead-brand-en { text-align: left; width: 38%; }
        .masthead-brand-ar { text-align: right; width: 38%; direction: rtl; }
        .document-title { text-align: center; font-size: 18px; font-weight: bold; color: #1a237e; margin: 30px 0; text-transform: uppercase; letter-spacing: 0.5px; }
        .content { font-size: 12px; margin-bottom: 40px; }
        .field { margin-bottom: 12px; }
        .footer-sig-stamp { width: 100%; margin-top: 50px; table-layout: fixed; }
        .footer-sig-stamp td { text-align: center; vertical-align: top; }
        .sig-line { border-top: 1px dotted #1a237e; margin-top: 40px; font-size: 8px; color: #64748b; padding-top: 4px; width: 60%; margin-left: auto; margin-right: auto; }
        .footer-verify { text-align: center; font-size: 7.5px; color: #64748b; font-family: monospace; position: absolute; bottom: 10px; width: 95%; left: 2.5%; border-top: 1px dashed #cbd5e1; padding-top: 6px; }
    </style>
</head>
@php
    if (!function_exists('ar')) {
        function ar($text) {
            if ($text === null || $text === '') return $text;
            return \App\Helpers\ArabicReshaper::utf8Glyphs($text);
        }
    }

    $enMajor = $request->user->graduate->major->name_en ?? '---';
    $enFaculty = $request->user->graduate->major->faculty->name_en ?? '---';
    $enDegree = $request->user->graduate->major->degree_name_en ?? "Bachelor's Degree";
@endphp
<body>
    <div class="page-border">
        <!-- 3-Column Premium Header with University Logo -->
        <table class="masthead-top">
            <tr>
                <td class="masthead-brand-en" style="width: 38%;">
                    Republic of Yemen<br>
                    Saba Region University<br>
                    Student Affairs<br>
                    Admission & Registration Dept.
                </td>
                <td style="text-align: center; width: 24%; vertical-align: middle;">
                    <img src="{{ public_path('assets/images/university-logo-pdf.png') }}" alt="SRU Logo" style="height: 55px; width: auto; display: block; margin: 0 auto;">
                </td>
                <td class="masthead-brand-ar" style="width: 38%; font-size: 7.5px;">
                    {{ ar('الجمهورية اليمنية') }}<br>
                    {{ ar('جامعة إقليم سبأ') }}<br>
                    {{ ar('نيابة شؤون الطلاب') }}<br>
                    {{ ar('الإدارة العامة للقبول والتسجيل') }}
                </td>
            </tr>
        </table>

        <div class="document-title">
            {{ $request->documentType->name_en }}
        </div>

        <div class="content">
            <div class="field">
                This is to certify that Mr/Ms: <strong>{{ $request->user->name }}</strong>
            </div>
            <div class="field">
                Has successfully completed the requirements for graduation to be awarded the degree of: <strong>{{ $enDegree }}</strong>
                in the major of: <strong>{{ $enMajor }}</strong>, Faculty of: <strong>{{ $enFaculty }}</strong>.
            </div>
            <div class="field">
                Graduation Class of: <strong>{{ $request->user->graduate->graduation_year }}</strong>
            </div>
            <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">
            <div class="field" style="font-size: 10px; color: #475569;">
                Tracking Code: <span style="font-family: monospace; font-weight: bold;">{{ $request->tracking_code }}</span><br>
                Serial Number: <span style="font-family: monospace; font-weight: bold;">{{ $serial_number }}</span><br>
                Date of Issue: {{ $issue_date }}
            </div>
        </div>

        <!-- Premium Stamp and Signature section -->
        <table class="footer-sig-stamp">
            <tr>
                <td style="width: 35%;">
                    Dean of Faculty Signature/Stamp
                    <div class="sig-line">Dean of College Sign/Stamp</div>
                </td>
                <td style="width: 30%;">
                    <div style="border: 1.5px dashed #1a237e; border-radius: 6px; display: inline-block; padding: 10px 15px; margin-top: 10px;">
                        <span style="font-size: 7px; font-weight: bold; color: #1a237e;">OFFICIAL SEAL</span><br>
                        <span style="font-size: 5.5px; color: #64748b;">SABA REGION UNIVERSITY</span>
                    </div>
                </td>
                <td style="width: 35%;">
                    General Registrar Signature/Stamp
                    <div class="sig-line">General Registrar Sign/Stamp</div>
                </td>
            </tr>
        </table>

        <!-- QR Code section -->
        <div style="margin-top: 40px; text-align: right; padding-right: 20px;">
            <img src="data:image/svg+xml;base64,{{ $qr_code }}" alt="QR Code" style="width: 70px; height: 70px; border: 1px solid #1a237e; padding: 2px;">
            <p style="font-size: 6.5px; margin-top: 3px; color: #64748b;">Scan to verify document validity</p>
        </div>


    </div>
</body>
</html>
