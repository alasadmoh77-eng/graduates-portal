<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; direction: ltr; text-align: left; padding: 12px; color: #111; font-size: 10px; }
        .page-border { border: 2px double #1a237e; padding: 12px 14px; }
        .masthead-top { width: 100%; border-bottom: 1px solid #1a237e; padding-bottom: 6px; margin-bottom: 8px; }
        .masthead-top td { vertical-align: top; font-size: 8px; font-weight: bold; line-height: 1.35; }
        .masthead-brand-en { text-align: left; width: 50%; }
        .masthead-brand-ar { text-align: right; width: 50%; direction: rtl; font-family: 'DejaVu Sans', sans-serif; }
        .masthead-body { width: 100%; margin-bottom: 8px; }
        .masthead-body td { vertical-align: top; padding: 0; }
        .masthead-main { padding-right: 8px; }
        .masthead-side { width: 108px; text-align: center; vertical-align: top; padding-left: 4px; border-right: 1px solid #cbd5e1; }
        .doc-title-inline {
            font-size: 12px; font-weight: bold; color: #1a237e; text-align: center;
            border: 1px solid #1a237e; background: #f8fafc; padding: 4px 10px; margin-bottom: 6px;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .student-compact { width: 100%; border-collapse: collapse; font-size: 8px; margin-bottom: 4px; }
        .student-compact th, .student-compact td { border: 1px solid #cbd5e1; padding: 4px 6px; text-align: left; }
        .student-compact th { background: #f1f5f9; font-weight: bold; color: #0f172a; width: 18%; }
        .transcript-lead { font-size: 8px; font-weight: bold; color: #334155; margin: 6px 0 4px; }
        .detail-block { margin-bottom: 12px; page-break-inside: avoid; }
        .detail-level-title { font-size: 9px; font-weight: bold; color: #1a237e; margin: 6px 0 3px; }
        .detail-sem-title { font-size: 8px; font-weight: bold; margin: 4px 0 2px; color: #0f172a; }
        .detail-table { width: 100%; border-collapse: collapse; font-size: 8px; margin-bottom: 6px; }
        .detail-table th, .detail-table td { border: 1px solid #94a3b8; padding: 3px 5px; text-align: center; }
        .detail-table th { background: #f1f5f9; font-weight: bold; }
        .detail-table .col-subject { text-align: left; unicode-bidi: isolate; }
        .level-footer { font-size: 8px; font-weight: bold; color: #334155; margin-bottom: 4px; }
        .qr-mini img { width: 88px; height: auto; border: 1px solid #1a237e; padding: 2px; background: #fff; display: block; margin: 0 auto 4px; }
        .meta-mini { font-size: 7px; color: #475569; line-height: 1.45; text-align: left; font-family: 'DejaVu Sans Mono', monospace; }
        .meta-mini div { margin-bottom: 2px; }
        .verify-hint { font-size: 7px; font-weight: bold; color: #1a237e; margin-top: 4px; }
        .closing-sig { width: 100%; margin-top: 14px; padding-top: 10px; border-top: 1px solid #1a237e; page-break-inside: avoid; }
        .closing-sig td { text-align: center; font-size: 9px; font-weight: bold; color: #1a237e; width: 50%; vertical-align: bottom; padding: 4px 8px; }
        .sig-line { border-top: 1px dotted #1a237e; margin-top: 36px; font-size: 7px; font-weight: normal; color: #64748b; text-transform: uppercase; padding-top: 2px; }
        .footer-verify { text-align: center; font-size: 7px; color: #64748b; font-family: monospace; margin-top: 10px; padding-top: 6px; border-top: 1px dashed #cbd5e1; }
    </style>
</head>
@php
    function ar($text) { return \App\Helpers\ArabicReshaper::utf8Glyphs($text); }
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
                    {{ ar('الجمهورية اليمنية') }} — {{ ar('جامعة إقليم سبأ') }}<br>
                    {{ ar('نيابة شؤون الطلاب — الإدارة العامة للقبول والتسجيل') }}
                </td>
            </tr>
        </table>

        <table class="masthead-body">
            <tr>
                <td class="masthead-main">
                    <div class="doc-title-inline">Academic Record <span style="font-size:9px;font-weight:normal;">(Certified Summary)</span></div>
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

        @foreach($academic_record->levels as $level)
            <div class="detail-block">
                <div class="detail-level-title">
                    {{ \App\Support\AcademicRecordEnglishPdf::levelName($level->name ?? '', $loop->index) }}
                    @if($level->academic_year)
                        — Academic year: {{ $level->academic_year }}
                    @endif
                    @if($level->level_avg)
                        — Average %: {{ $level->level_avg }}
                    @endif
                </div>
                @foreach($level->semesters as $semester)
                    <div class="detail-sem-title">
                        {{ \App\Support\AcademicRecordEnglishPdf::semesterName((int) $semester->sort_order) }}
                    </div>
                    <table class="detail-table">
                        <thead>
                            <tr>
                                <th style="width:7%;">#</th>
                                <th class="col-subject">Course</th>
                                <th style="width:11%;">Cr.</th>
                                <th style="width:11%;">Score</th>
                                <th style="width:16%;">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($semester->subjects as $idx => $subject)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td class="col-subject" dir="auto">{{ \App\Support\AcademicRecordEnglishPdf::courseName($subject->catalog_key ?? null, $subject->name) }}</td>
                                    <td>{{ $subject->credit_hours ?? '—' }}</td>
                                    <td>{{ $subject->score ?? '—' }}</td>
                                    <td>{{ \App\Support\AcademicRecordEnglishPdf::rating($subject->rating ?? null) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5">No courses recorded.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @endforeach
                @if($level->total_points || $level->final_result)
                    <div class="level-footer">
                        Level total: {{ $level->total_points ?? '—' }}
                        &nbsp;|&nbsp;
                        Result: {{ \App\Support\AcademicRecordEnglishPdf::result($level->final_result ?? null) }}
                    </div>
                @endif
            </div>
        @endforeach

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
            Verify this document: {{ config('app.url') }}/verify/{{ $qr_token }}
        </div>
    </div>
</body>
</html>
