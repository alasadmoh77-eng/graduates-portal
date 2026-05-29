<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 5mm 10mm; }
        body { font-family: 'DejaVu Sans', sans-serif; direction: ltr; text-align: left; padding: 0; color: #000; font-size: 8px; line-height: 1.1; }
        .page-border { border: 2px solid #1a237e; padding: 2px 4px; }
        .masthead-top { width: 100%; border-bottom: 1px solid #1a237e; padding-bottom: 2px; margin-bottom: 4px; }
        .masthead-top td { vertical-align: top; font-size: 7px; font-weight: bold; line-height: 1.2; }
        .masthead-brand-en { text-align: left; width: 50%; }
        .masthead-brand-ar { text-align: right; width: 50%; direction: rtl; font-family: 'DejaVu Sans', sans-serif; }
        .masthead-body { width: 100%; margin-bottom: 4px; table-layout: fixed; }
        .masthead-body td { vertical-align: top; padding: 0; }
        .masthead-main { padding-right: 6px; width: 78%; }
        .masthead-side { width: 22%; text-align: center; vertical-align: top; border-left: 1px solid #cbd5e1; padding-left: 4px; }
        .doc-title-inline { font-size: 9px; font-weight: bold; color: #1a237e; text-align: center; border: 1px solid #1a237e; background: #f8fafc; padding: 2px; margin-bottom: 4px; text-transform: uppercase; }
        .student-compact { width: 100%; border-collapse: collapse; font-size: 7px; margin-bottom: 2px; table-layout: fixed; }
        .student-compact th, .student-compact td { border: 1px solid #cbd5e1; padding: 1px 2px; text-align: left; }
        .student-compact th { background: #f1f5f9; font-weight: bold; width: 18%; color: #0f172a; }
        .transcript-lead { font-size: 8px; font-weight: bold; color: #334155; margin: 2px 0 1px; text-align: center; text-decoration: underline; }
        .detail-block { margin-bottom: 2px; }
        .detail-level-title { font-size: 8px; font-weight: bold; color: #1a237e; margin: 1px 0; border-bottom: 1px solid #cbd5e1; padding-bottom: 1px; }
        .detail-sem-title { font-size: 7px; font-weight: bold; margin: 1px 0; color: #0f172a; }
        .detail-table { width: 100%; border-collapse: collapse; font-size: 6.5px; margin-bottom: 2px; table-layout: fixed; }
        .detail-table th, .detail-table td { border: 1px solid #94a3b8; padding: 1px; text-align: center; }
        .detail-table th { background: #f1f5f9; font-weight: bold; }
        .detail-table .col-subject { text-align: left; unicode-bidi: isolate; width: 50%; }
        .level-footer { font-size: 6.5px; font-weight: bold; color: #334155; margin-bottom: 2px; background: #f8fafc; border: 1px solid #e2e8f0; padding: 1px; text-align: center; }
        .qr-mini img { width: 55px; height: auto; border: 1px solid #1a237e; padding: 1px; background: #fff; display: block; margin: 0 auto 2px; }
        .meta-mini { font-size: 5.5px; color: #475569; line-height: 1.1; text-align: left; font-family: 'DejaVu Sans Mono', monospace; }
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
    $enName = trim((string) ($academic_record->student_name_en ?? '')) !== ''
        ? $academic_record->student_name_en
        : ($request->user->name ?? '—');
    $enUid = $academic_record->university_number ?: ($request->user->graduate->university_id ?? '—');
    $enDegree = \App\Support\AcademicRecordEnglishPdf::degree($academic_record->degree_en, $academic_record->degree_ar);
    $enMajor = \App\Support\AcademicRecordEnglishPdf::majorName(
        $request->user->graduate->major->name_en ?? null,
        $request->user->graduate->major->name_ar ?? null
    );
    $enGradY = $academic_record->graduation_year_label ?: ($request->user->graduate->graduation_year ?? '—');
    $enRating = \App\Support\AcademicRecordEnglishPdf::rating($academic_record->overall_rating);
    $enGpa = $academic_record->gpa ?: '—';
    $enTotal = $academic_record->total_marks ?: '—';
    $enHonors = \App\Support\AcademicRecordEnglishPdf::honors($academic_record->honors_rank);
@endphp
<body>
    <div class="page-border">
        <table class="masthead-top">
            <tr>
                <td class="masthead-brand-en">
                    Republic of Yemen — Saba Region University<br>
                    Student Affairs — Registration Gen. Dept.
                </td>
                <td class="masthead-brand-ar">
                    {{ ar('الجمهورية اليمنية — جامعة إقليم سبأ') }}<br>
                    {{ ar('نيابة شؤون الطلاب — الإدارة العامة للقبول والتسجيل') }}
                </td>
            </tr>
        </table>

        <table class="masthead-body">
            <tr>
                <td class="masthead-main">
                    <div class="doc-title-inline">Grades &amp; Estimates Certificate <span style="font-size:7.5px;font-weight:normal;">(Certified Summary)</span></div>
                    <table class="student-compact">
                        <tr>
                            <th>Graduate Name</th>
                            <td colspan="3" style="font-weight: bold; font-size: 9px;">{{ $enName }}</td>
                        </tr>
                        <tr>
                            <th>University ID</th>
                            <td style="font-family: monospace;">{{ $enUid }}</td>
                            <th>Degree</th>
                            <td style="font-weight: bold;">{{ $enDegree }}</td>
                        </tr>
                        <tr>
                            <th>Major / Dept</th>
                            <td colspan="3" style="font-weight: bold;">{{ $enMajor }}</td>
                        </tr>
                        <tr>
                            <th>Graduation Year</th>
                            <td>{{ $enGradY }}</td>
                            <th>Rating / GPA / Total</th>
                            <td style="font-weight: bold;">{{ $enRating }} — {{ $enGpa }} / {{ $enTotal }}</td>
                        </tr>
                        <tr>
                            <th>Honors</th>
                            <td colspan="3">{{ $enHonors }}</td>
                        </tr>
                    </table>
                </td>
                <td class="masthead-side">
                    <div class="qr-mini">
                        <img src="data:image/svg+xml;base64,{{ $qr_code }}" alt="QR">
                    </div>
                    <div class="meta-mini">
                        <div><strong>Serial:</strong> {{ $serial_number }}</div>
                        <div><strong>Tracking:</strong> {{ $request->tracking_code }}</div>
                        <div><strong>Issued:</strong> {{ $issue_date }}</div>
                    </div>
                    <div class="verify-hint">Scan to verify</div>
                </td>
            </tr>
        </table>

        <div class="transcript-lead">Course work and grades — official academic record</div>

        @if($academic_record && $academic_record->levels)
            @include('pdf.documents._transcript_tables')
        @else
            <p style="text-align: center; font-size: 14px; color: red;">
                No academic record data available.
            </p>
        @endif

        <table class="closing-sig">
            <tr>
                <td>
                    Dean of Faculty
                    <div class="sig-line">Stamp &amp; Signature</div>
                </td>
                <td>
                    General Registrar
                    <div class="sig-line">Stamp &amp; Signature</div>
                </td>
            </tr>
        </table>

        <div class="footer-verify">
            Verify this document: {{ route('verify.show', ['token' => $qr_token]) }}
        </div>
    </div>
</body>
</html>
