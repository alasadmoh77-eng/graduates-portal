<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 15px 20px; }
        body { font-family: 'DejaVu Sans', sans-serif; direction: rtl; text-align: right; padding: 4px; color: #111; font-size: 10px; }
        .page-border { border: 2px double #1a237e; padding: 6px 10px; height: 97%; }
        .masthead-top { width: 100%; border-bottom: 1px solid #1a237e; padding-bottom: 4px; margin-bottom: 4px; }
        .masthead-top td { vertical-align: top; font-size: 8px; font-weight: bold; line-height: 1.25; }
        .masthead-brand-ar { text-align: right; width: 50%; }
        .masthead-brand-en { text-align: left; width: 50%; direction: ltr; }
        .masthead-body { width: 100%; margin-bottom: 4px; }
        .masthead-body td { vertical-align: top; padding: 0; }
        .masthead-main { padding-inline-end: 8px; }
        .masthead-side { width: 90px; text-align: center; vertical-align: top; padding-inline-start: 4px; border-inline-start: 1px solid #cbd5e1; }
        .doc-title-inline {
            font-size: 11px; font-weight: bold; color: #1a237e; text-align: center;
            border: 1px solid #1a237e; background: #f8fafc; padding: 3px 8px; margin-bottom: 4px;
        }
        .student-compact { width: 100%; border-collapse: collapse; font-size: 8px; margin-bottom: 2px; }
        .student-compact th, .student-compact td { border: 1px solid #cbd5e1; padding: 2px 4px; text-align: right; }
        .student-compact th { background: #f1f5f9; font-weight: bold; color: #0f172a; width: 18%; }
        .transcript-lead { font-size: 8px; font-weight: bold; color: #334155; margin: 4px 0 2px; }
        .detail-block { margin-bottom: 4px; page-break-inside: avoid; }
        .detail-level-title { font-size: 8px; font-weight: bold; color: #1a237e; margin: 2px 0 1px; }
        .detail-sem-title { font-size: 7px; font-weight: bold; margin: 1px 0; color: #0f172a; }
        .detail-table { width: 100%; border-collapse: collapse; font-size: 7px; margin-bottom: 2px; }
        .detail-table th, .detail-table td { border: 1px solid #94a3b8; padding: 1px 2px; text-align: center; }
        .detail-table th { background: #f1f5f9; font-weight: bold; }
        .detail-table .col-subject { text-align: right; }
        .level-footer { font-size: 7px; font-weight: bold; color: #334155; margin-bottom: 2px; }
        .qr-mini img { width: 72px; height: auto; border: 1px solid #1a237e; padding: 2px; background: #fff; display: block; margin: 0 auto 2px; }
        .meta-mini { font-size: 6.5px; color: #475569; line-height: 1.3; text-align: right; font-family: 'DejaVu Sans Mono', monospace; }
        .meta-mini div { margin-bottom: 1px; }
        .verify-hint { font-size: 6px; font-weight: bold; color: #1a237e; margin-top: 2px; }
        .closing-sig { width: 100%; margin-top: 8px; padding-top: 6px; border-top: 1px solid #1a237e; page-break-inside: avoid; }
        .closing-sig td { text-align: center; font-size: 8px; font-weight: bold; color: #1a237e; width: 50%; vertical-align: bottom; padding: 2px 6px; }
        .sig-line { border-top: 1px dotted #1a237e; margin-top: 24px; font-size: 6px; font-weight: normal; color: #64748b; padding-top: 2px; }
        .footer-verify { text-align: center; font-size: 6px; color: #64748b; font-family: monospace; margin-top: 6px; padding-top: 4px; border-top: 1px dashed #cbd5e1; }
    </style>
</head>
@php
    function ar($text) { return \App\Helpers\ArabicReshaper::utf8Glyphs($text); }
    $arName = $academic_record->student_name_ar ?: ($request->user->name ?? '---');
    $arUid = $academic_record->university_number ?: ($request->user->graduate->university_id ?? '---');
    $arDegree = $academic_record->degree_ar ?: 'بكالوريوس';
    $arMajor = $request->user->graduate->major->name_ar ?? '---';
    $arGradY = $academic_record->graduation_year_label ?: ($request->user->graduate->graduation_year ?? '---');
    $arRating = $academic_record->overall_rating ?: '---';
    $arGpa = $academic_record->gpa ?: '---';
    $arHonors = $academic_record->honors_rank ?? '';
    $honorsClause = $arHonors ? ar('، ') . ar($arHonors) : '';
    $arTotal = $academic_record->total_marks ?? '---';
@endphp
<body>
    <div class="page-border">
        <table class="masthead-top">
            <tr>
                <td class="masthead-brand-ar">
                    {{ ar('الجمهورية اليمنية') }} — {{ ar('جامعة إقليم سبأ') }}<br>
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
                    <div class="doc-title-inline">{{ ar('شهادة درجات وتقديرات') }}</div>
                    <div class="official-intro" style="font-size: 10px; line-height: 1.5; text-align: justify; margin-top: 6px; padding-inline-end: 15px;">
                        {{ ar('تشهد جامعة إقليم سبأ ممثلةً بنيابة شؤون الطلاب والقبول والتسجيل وكلية تكنولوجيا المعلومات وعلوم الحاسوب بأن الطالب/') }} <strong>{{ ar($arName) }}</strong>{{ ar('،') }}
                        {{ ar('الحامل للرقم الجامعي (') }}<span style="font-family: monospace;">{{ $arUid }}</span>{{ ar(')، من قسم ') }}<strong>{{ ar($arMajor) }}</strong>{{ ar('، قد أتم بنجاح جميع المتطلبات الأكاديمية المقررة لنيل درجة ') }}<strong>{{ ar($arDegree) }}</strong>{{ ar('،') }}
                        {{ ar('وتخرج في العام ') }}<strong>{{ $arGradY }}</strong>.
                        <br><br>
                        {{ ar('وبحسب السجل الأكاديمي المعتمد لدى الجامعة، فقد حصل على معدل تراكمي قدره ') }}<strong>{{ $arGpa }}</strong>{{ ar('،') }}
                        {{ ar('وبتقدير عام ') }}<strong>{{ ar($arRating) }}</strong>{!! $honorsClause !!}.
                        <br><br>
                        {{ ar('وقد أُعدت هذه الشهادة بناءً على السجلات الرسمية المعتمدة لدى الجامعة، وتُمنح له بناءً على طلبه للعمل بموجبها حيثما دعت الحاجة.') }}
                    </div>
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
            {{ ar('للتحقق من النسخة الأصلية:') }} {{ config('app.url') }}/verify/{{ $qr_token }}
        </div>
    </div>
</body>
</html>
