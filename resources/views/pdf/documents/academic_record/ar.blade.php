<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 5mm 10mm; }
        body { font-family: 'DejaVu Sans', sans-serif; direction: rtl; text-align: right; padding: 0; color: #000; font-size: 8px; line-height: 1.1; }
        .page-border { border: 2px solid #1a237e; padding: 2px 4px; }
        .masthead-top { width: 100%; border-bottom: 1px solid #1a237e; padding-bottom: 2px; margin-bottom: 4px; }
        .masthead-top td { vertical-align: top; font-size: 7px; font-weight: bold; line-height: 1.2; }
        .masthead-brand-ar { text-align: right; width: 50%; }
        .masthead-brand-en { text-align: left; width: 50%; direction: ltr; }
        .masthead-body { width: 100%; margin-bottom: 4px; table-layout: fixed; }
        .masthead-body td { vertical-align: top; padding: 0; }
        .masthead-main { padding-inline-end: 6px; width: 78%; }
        .masthead-side { width: 22%; text-align: center; vertical-align: top; border-inline-start: 1px solid #cbd5e1; padding-inline-start: 4px; }
        .doc-title-inline { font-size: 9px; font-weight: bold; color: #1a237e; text-align: center; border: 1px solid #1a237e; background: #f8fafc; padding: 2px; margin-bottom: 4px; }
        .student-compact { width: 100%; border-collapse: collapse; font-size: 7px; margin-bottom: 2px; table-layout: fixed; }
        .student-compact th, .student-compact td { border: 1px solid #cbd5e1; padding: 1px 2px; text-align: right; }
        .student-compact th { background: #f1f5f9; font-weight: bold; width: 16%; color: #0f172a; }
        .transcript-lead { font-size: 8px; font-weight: bold; color: #334155; margin: 2px 0 1px; text-align: center; text-decoration: underline; }
        .detail-block { margin-bottom: 2px; }
        .detail-level-title { font-size: 8px; font-weight: bold; color: #1a237e; margin: 1px 0; border-bottom: 1px solid #cbd5e1; padding-bottom: 1px; }
        .detail-sem-title { font-size: 7px; font-weight: bold; margin: 1px 0; color: #0f172a; }
        .detail-table { width: 100%; border-collapse: collapse; font-size: 6.5px; margin-bottom: 2px; table-layout: fixed; }
        .detail-table th, .detail-table td { border: 1px solid #94a3b8; padding: 1px; text-align: center; }
        .detail-table th { background: #f1f5f9; font-weight: bold; }
        .detail-table .col-subject { text-align: right; width: 50%; }
        .level-footer { font-size: 6.5px; font-weight: bold; color: #334155; margin-bottom: 2px; background: #f8fafc; border: 1px solid #e2e8f0; padding: 1px; text-align: center; }
        .qr-mini img { width: 55px; height: auto; border: 1px solid #1a237e; padding: 1px; background: #fff; display: block; margin: 0 auto 2px; }
        .meta-mini { font-size: 5.5px; color: #475569; line-height: 1.1; text-align: right; font-family: 'DejaVu Sans Mono', monospace; }
        .meta-mini div { margin-bottom: 1px; }
        .verify-hint { font-size: 5.5px; font-weight: bold; color: #1a237e; margin-top: 1px; }
        .closing-sig { width: 100%; margin-top: 8px; padding-top: 4px; page-break-inside: avoid; table-layout: fixed; }
        .closing-sig td { text-align: center; font-size: 8px; font-weight: bold; color: #1a237e; width: 50%; vertical-align: bottom; }
        .sig-line { border-top: 1px dotted #1a237e; margin-top: 18px; font-size: 6px; font-weight: normal; color: #64748b; padding-top: 2px; width: 60%; margin-left: auto; margin-right: auto; }
        .footer-verify { text-align: center; font-size: 6px; color: #64748b; font-family: monospace; margin-top: 6px; padding-top: 3px; border-top: 1px dashed #cbd5e1; }
    </style>
</head>
@php
    if (!function_exists('ar')) {
        function ar($text) { return \App\Helpers\ArabicReshaper::utf8Glyphs($text); }
    }
    $arName = $academic_record->student_name_ar ?: $request->user->name;
    $arUid = $academic_record->university_number ?: ($request->user->graduate->university_id ?? '---');
    $arDegree = $academic_record->degree_ar ?: 'بكالوريوس';
    $arGradY = $academic_record->graduation_year_label ?: ($request->user->graduate->graduation_year ?? '---');
    $arRating = $academic_record->overall_rating ?: '---';
    $arGpa = $academic_record->gpa ?: '---';
    $arTotal = $academic_record->total_marks ?: '---';
    $arHonors = $academic_record->honors_rank ?: '—';
@endphp
<body>
    <div class="page-border">
        <table class="masthead-top">
            <tr>
                <td class="masthead-brand-ar">
                    {{ ar('الجمهورية اليمنية — جامعة إقليم سبأ') }}<br>
                    {{ ar('نيابة شؤون الطلاب — الإدارة العامة للقبول والتسجيل') }}
                </td>
                <td class="masthead-brand-en">
                    Republic of Yemen — Saba Region University<br>
                    Student Affairs — Registration Gen. Dept.
                </td>
            </tr>
        </table>

        <table class="masthead-body">
            <tr>
                <td class="masthead-main">
                    <div class="doc-title-inline">{{ ar('سجل أكاديمي (خلاصة معتمدة)') }}</div>
                    <table class="student-compact">
                        <tr>
                            <th>{{ ar('اسم الخريج') }}</th>
                            <td colspan="3" style="font-weight: bold; font-size: 9px;">{{ ar($arName) }}</td>
                        </tr>
                        <tr>
                            <th>{{ ar('الرقم الجامعي') }}</th>
                            <td style="font-family: monospace;">{{ $arUid }}</td>
                            <th>{{ ar('الدرجة') }}</th>
                            <td style="font-weight: bold;">{{ ar($arDegree) }}</td>
                        </tr>
                        <tr>
                            <th>{{ ar('التخصص') }}</th>
                            <td colspan="3" style="font-weight: bold;">{{ ar($request->user->graduate->major->name_ar ?? '---') }}</td>
                        </tr>
                        <tr>
                            <th>{{ ar('عام التخرج') }}</th>
                            <td>{{ $arGradY }}</td>
                            <th>{{ ar('التقدير / المعدل') }}</th>
                            <td style="font-weight: bold;">{{ ar($arRating) }} — {{ ar($arGpa) }} / {{ $arTotal }}</td>
                        </tr>
                        <tr>
                            <th>{{ ar('مرتبة الشرف') }}</th>
                            <td colspan="3">{{ ar($arHonors) }}</td>
                        </tr>
                    </table>
                </td>
                <td class="masthead-side">
                    <div class="qr-mini">
                        <img src="data:image/svg+xml;base64,{{ $qr_code }}" alt="QR">
                    </div>
                    <div class="meta-mini">
                        <div><strong>{{ ar('تسلسلي') }}:</strong> {{ $serial_number }}</div>
                        <div><strong>{{ ar('مرجعي') }}:</strong> {{ $request->tracking_code }}</div>
                        <div><strong>{{ ar('إصدار') }}:</strong> {{ $issue_date }}</div>
                    </div>
                    <div class="verify-hint">{{ ar('امسح للتحقق') }}</div>
                </td>
            </tr>
        </table>

        <div class="transcript-lead">{{ ar('جدول المقررات والدرجات — السجل الأكاديمي المعتمد') }}</div>

        @if($academic_record && $academic_record->levels)
            @include('pdf.documents._transcript_tables')
        @else
            <p style="text-align: center; font-size: 14px; color: red;">
                {{ ar('لا توجد بيانات السجل الأكاديمي') }}
            </p>
        @endif

        <table class="closing-sig">
            <tr>
                <td>
                    {{ ar('عميد الكلية') }}
                    <div class="sig-line">{{ ar('الختم والتوقيع') }}</div>
                </td>
                <td>
                    {{ ar('المسجل العام') }}
                    <div class="sig-line">{{ ar('الختم والتوقيع') }}</div>
                </td>
            </tr>
        </table>

        <div class="footer-verify">
            {{ ar('للتحقق من النسخة الأصلية:') }} {{ route('verify.show', ['token' => $qr_token]) }}
        </div>
    </div>
</body>
</html>
