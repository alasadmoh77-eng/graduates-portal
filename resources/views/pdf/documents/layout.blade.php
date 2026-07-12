<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    @include('pdf.documents._styles')
</head>
@php
    if (!function_exists('ar')) {
        function ar($text)
        {
            if ($text === null || $text === '') {
                return $text;
            }
            return \App\Helpers\ArabicReshaper::utf8Glyphs($text);
        }
    }

    $arName = $academic_record->student_name_ar ?: ($request->user->name ?? '---');
    $arUid = $academic_record->university_number ?: ($request->user->graduate->university_id ?? '---');
    $arDegree = $academic_record->degree_ar ?: ($request->user->graduate->major->degree_name_ar ?? 'بكالوريوس');
    $arGradY = $academic_record->graduation_year_label ?: ($request->user->graduate->graduation_year ?? '---');
    $arRating = $academic_record->overall_rating ?: '---';
    $arGpa = $academic_record->gpa ?: '---';
    $arTotal = $academic_record->total_marks ?: '---';
    $hasDisqualifying = \App\Helpers\AcademicHelper::hasHonorDisqualifyingGrade($academic_record);
    $arHonors = ($academic_record && !$hasDisqualifying) ? ($academic_record->honors_rank ?: '—') : '—';
    $arMajor = $request->user->graduate->major->name_ar ?? '---';
    $arFaculty = $request->user->graduate->major->faculty->name_ar ?? '---';
    $arEnrollY = $academic_record->enrollment_year_label ?: '---';
    $arExamSession = $academic_record->exam_session ?: '---';

    $cleanGpa = rtrim($arGpa, '%');
    $honorsStr = ($arHonors && $arHonors !== '—' && $arHonors !== 'بدون' && $arHonors !== 'لا يوجد') ? " " . $arHonors : "";
@endphp

<body class="academic-page-content">
    <div class="page-wrap">
        <div class="doc-body-container">
            <div class="doc-content-wrapper">
                {{-- Official 3-Column Header --}}
                <table class="header-table">
                    <tr>
                        <td class="header-left">
                            <table style="width: 100%; border-collapse: collapse; direction: ltr; table-layout: fixed;">
                                <tr>
                                    <td class="pdf-header-qr" style="vertical-align: top; width: 72px;">
                                        <div class="pdf-header-qr-box" style="display: inline-block;">
                                            <img src="data:image/svg+xml;base64,{{ $qr_code }}" class="qr-img-header" alt="QR">
                                        </div>
                                    </td>
                                    <td style="width: 8px;"></td>
                                    <td class="pdf-header-meta-box" style="vertical-align: top; width: 196px;">
                                        <table class="doc-meta-box" dir="rtl" style="margin-left: auto; margin-right: 0; direction: rtl; width: 196px;">
                                            <tr>
                                                <td class="meta-label" style="text-align: right;">{{ ar('تاريخ') }}</td>
                                                <td class="meta-value" dir="ltr" style="text-align: left;">{{ $issue_date }}</td>
                                            </tr>
                                            <tr>
                                                <td class="meta-label" style="text-align: right;">{{ ar('رقم') }}</td>
                                                <td class="meta-value" dir="ltr" style="text-align: left;">{{ $serial_number }}</td>
                                            </tr>
                                            <tr>
                                                <td class="meta-label" style="text-align: right;">{{ ar('مرجع') }}</td>
                                                <td class="meta-value" dir="ltr" style="text-align: left;">{{ $request->tracking_code }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td class="header-center">
                            <img src="{{ public_path('assets/images/university-logo-pdf.png') }}" alt="SRU"
                                class="uni-logo">
                            <div class="uni-name-under-logo">{{ ar('جامعة إقليم سبأ') }}</div>
                            <div class="header-doc-title">@yield('title')</div>
                        </td>
                        <td class="header-right">
                            {{ ar('الجمهورية اليمنية') }}<br>
                            {{ ar('وزارة التعليم العالي والبحث العلمي') }}<br>
                            {{ ar('جامعة إقليم سبأ') }}<br>
                            {{ ar($arFaculty) }}<br>
                            {{ ar('قسم : ' . $arMajor) }}
                        </td>
                    </tr>
                </table>

                {{-- Student Information Panel --}}
                <table class="student-table" dir="ltr">
                    <tr>
                        <td style="text-align: right; width: 25%;">
                            <div><span class="important-value">{{ ar($arEnrollY) }}</span> <strong
                                    class="important-label">{{ ar(' : عام الالتحاق') }}</strong></div>
                        </td>
                        <td style="width: 38%;">
                            <table style="width: 100%; border-collapse: collapse; border: none;" dir="ltr">
                                <tr style="border: none;">
                                    <td style="border: none; padding: 0; text-align: right;"><span
                                            class="important-value">{{ $cleanGpa }}%</span>
                                        <strong class="important-label">{{ ar(' : المعدل') }}</strong>
                                    </td>
                                    <td style="border: none; padding: 0; text-align: right;"><span
                                            class="important-value">{{ $arTotal }}</span>
                                        <strong class="important-label">{{ ar(' : مجموع الدرجات') }}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 37%; text-align: right;">
                            <div><span class="important-value">{{ ar($arName) }}</span> <strong
                                    class="important-label">{{ ar(' : الاسم') }}</strong></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">
                            <div><span class="important-value">{{ ar($arGradY) }}</span> <strong
                                    class="important-label">{{ ar(' : عام التخرج') }}</strong></div>
                        </td>
                        <td>
                            <table style="width: 100%; border-collapse: collapse; border: none;" dir="ltr">
                                <tr style="border: none;">
                                    <td style="border: none; padding: 0; text-align: right;"><span
                                            class="important-value">{{ ar($arExamSession) }}</span>
                                        <strong class="important-label">{{ ar(' : الدور') }}</strong>
                                    </td>
                                    @if($honorsStr)
                                        <td style="border: none; padding: 0; text-align: right;">
                                            <strong class="important-label">{{ ar('مع مرتبة الشرف') }}</strong>
                                        </td>
                                    @endif
                                    <td style="border: none; padding: 0; text-align: right;"><span
                                            class="important-value">{{ ar($arRating) }}</span>
                                        <strong class="important-label">{{ ar(' : التقدير') }}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="text-align: right;">
                            <div><span class="important-value" style="font-family: monospace;">{{ $arUid }}</span>
                                <strong class="important-label">{{ ar(' : الرقم') }}</strong>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td style="text-align: right;">
                            <div><span class="important-value">{{ ar($arDegree) }}</span> <strong
                                    class="important-label">{{ ar(' : الدرجة') }}</strong></div>
                        </td>
                    </tr>
                </table>

                {{-- Optional Introductory Paragraph --}}
                @yield('intro')

                {{-- Side-by-side balanced Semester Tables --}}
                @if($academic_record && $academic_record->levels)
                    @foreach($academic_record->levels as $level)
                        @php
                            $hasSubjectsInLevel = false;
                            $levelTotal = 0;
                            $hasScores = false;
                            if (isset($level->semesters)) {
                                foreach ($level->semesters as $sem) {
                                    if (isset($sem->subjects) && count($sem->subjects) > 0) {
                                        $hasSubjectsInLevel = true;
                                    }
                                    if (isset($sem->subjects)) {
                                        foreach ($sem->subjects as $subject) {
                                            if (isset($subject->score) && is_numeric($subject->score)) {
                                                $levelTotal += (float) $subject->score;
                                                $hasScores = true;
                                            }
                                        }
                                    }
                                }
                            }
                        @endphp
                        @if(!$hasSubjectsInLevel)
                            @continue
                        @endif

                        <div class="level-block">
                            <table class="level-header-bar">
                                <tr>
                                    <td style="width: 20%; text-align: right;">{{ ar($level->final_result ?? '—') }}
                                        <strong>{{ ar(' : التقدير') }}</strong>
                                    </td>
                                    <td style="width: 20%; text-align: right;">{{ $level->level_avg ?? '—' }}%
                                        <strong>{{ ar(' : المعدل') }}</strong>
                                    </td>
                                    <td style="width: 20%; text-align: right;">
                                        {{ $hasScores ? rtrim(rtrim(number_format($levelTotal, 2), '0'), '.') : '—' }}
                                        <strong>{{ ar(' : المجموع') }}</strong>
                                    </td>
                                    <td style="width: 25%; text-align: right;">{{ $level->academic_year ?? '—' }}
                                        <strong>{{ ar(' : العام الجامعي') }}</strong>
                                    </td>
                                    <td style="width: 15%; text-align: center; background-color: #f1f5f9; font-weight: bold;">
                                        {{ ar('المستوى ' . $level->name) }}
                                    </td>
                                </tr>
                            </table>

                            <table class="sem-grid" dir="ltr">
                                <tr>
                                    @php
                                        $semesters = $level->semesters->sortBy('sort_order')->values();
                                        $sem1 = $semesters->first(function ($semester) {
                                            $name = $semester->name_ar ?? $semester->name ?? $semester->title ?? '';
                                            return str_contains($name, 'أول') || str_contains($name, 'اول');
                                        }) ?? $semesters->get(0);

                                        $sem2 = $semesters->first(function ($semester) use ($sem1) {
                                            $name = $semester->name_ar ?? $semester->name ?? $semester->title ?? '';
                                            return (!$sem1 || $semester->id !== $sem1->id)
                                                && (str_contains($name, 'ثاني') || str_contains($name, 'ثانى'));
                                        }) ?? $semesters->filter(function ($semester) use ($sem1) {
                                            return !$sem1 || $semester->id !== $sem1->id;
                                        })->values()->get(0);

                                        $sem1Subjects = ($sem1 && isset($sem1->subjects)) ? $sem1->subjects : collect();
                                        $sem2Subjects = ($sem2 && isset($sem2->subjects)) ? $sem2->subjects : collect();

                                        $maxRows = max($sem1Subjects->count(), $sem2Subjects->count());
                                    @endphp

                                    {{-- Second Semester (Left in LTR) --}}
                                    <td class="sem-cell second-sem">
                                        <div class="sem-title">{{ ar('الفصل الدراسي الثاني') }}</div>
                                        <table class="subjects-table">
                                            <thead>
                                                <tr>
                                                    <th style="width:18%;">{{ ar('التقدير') }}</th>
                                                    <th style="width:13%;">{{ ar('الدرجة') }}</th>
                                                    <th style="width:11%;">{{ ar('س.م') }}</th>
                                                    <th style="width:53%;">{{ ar('اسم المادة') }}</th>
                                                    <th style="width:5%;">{{ ar('م') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $totalScore2 = 0;
                                                    $totalHours2 = 0;
                                                    $weightedPoints2 = 0;
                                                @endphp
                                                @for($i = 0; $i < $maxRows; $i++)
                                                    @php
                                                        $subject = $sem2Subjects->get($i);
                                                    @endphp
                                                    @if($subject)
                                                        @php
                                                            $score = is_numeric($subject->score) ? (float) $subject->score : null;
                                                            $hours = is_numeric($subject->credit_hours) ? (float) $subject->credit_hours : null;
                                                            if ($score !== null) {
                                                                $totalScore2 += $score;
                                                                if ($hours !== null) {
                                                                    $weightedPoints2 += $score * $hours;
                                                                    $totalHours2 += $hours;
                                                                }
                                                            }
                                                        @endphp
                                                        <tr>
                                                            <td>{{ ar($subject->rating ?? '—') }}</td>
                                                            <td>{{ $subject->score ?? '—' }}</td>
                                                            <td>{{ $subject->credit_hours ?? '—' }}</td>
                                                            <td class="subj-name">{{ ar($subject->name) }}</td>
                                                            <td style="font-weight: bold; background-color: #f1f5f9;">{{ $i + 1 }}</td>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td class="subj-name">&nbsp;</td>
                                                            <td style="background-color: #f1f5f9;">&nbsp;</td>
                                                        </tr>
                                                    @endif
                                                @endfor
                                                @php
                                                    $avgScore2 = $totalHours2 > 0 ? ($weightedPoints2 / $totalHours2) : 0;
                                                    $rating2 = '';
                                                    if ($totalHours2 > 0) {
                                                        if ($avgScore2 >= 90)
                                                            $rating2 = 'ممتاز';
                                                        elseif ($avgScore2 >= 80)
                                                            $rating2 = 'جيد جداً';
                                                        elseif ($avgScore2 >= 70)
                                                            $rating2 = 'جيد';
                                                        elseif ($avgScore2 >= 60)
                                                            $rating2 = 'مقبول';
                                                        else
                                                            $rating2 = 'راسب';
                                                    }
                                                @endphp
                                                <tr class="sem-footer-row">
                                                    <td>{{ $rating2 ? ar($rating2) : '—' }}</td>
                                                    <td>{{ $totalHours2 > 0 ? number_format($avgScore2, 2) : '—' }}</td>
                                                    <td>{{ $totalHours2 > 0 ? $totalHours2 : '—' }}</td>
                                                    <td style="text-align:right; padding-right:4px;">{{ ar('المجموع') }}</td>
                                                    <td style="background-color: #f1f5f9;">&nbsp;</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>

                                    {{-- First Semester (Right in LTR) --}}
                                    <td class="sem-cell first-sem">
                                        <div class="sem-title">{{ ar('الفصل الدراسي الأول') }}</div>
                                        <table class="subjects-table">
                                            <thead>
                                                <tr>
                                                    <th style="width:18%;">{{ ar('التقدير') }}</th>
                                                    <th style="width:13%;">{{ ar('الدرجة') }}</th>
                                                    <th style="width:11%;">{{ ar('س.م') }}</th>
                                                    <th style="width:53%;">{{ ar('اسم المادة') }}</th>
                                                    <th style="width:5%;">{{ ar('م') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $totalScore1 = 0;
                                                    $totalHours1 = 0;
                                                    $weightedPoints1 = 0;
                                                @endphp
                                                @for($i = 0; $i < $maxRows; $i++)
                                                    @php
                                                        $subject = $sem1Subjects->get($i);
                                                    @endphp
                                                    @if($subject)
                                                        @php
                                                            $score = is_numeric($subject->score) ? (float) $subject->score : null;
                                                            $hours = is_numeric($subject->credit_hours) ? (float) $subject->credit_hours : null;
                                                            if ($score !== null) {
                                                                $totalScore1 += $score;
                                                                if ($hours !== null) {
                                                                    $weightedPoints1 += $score * $hours;
                                                                    $totalHours1 += $hours;
                                                                }
                                                            }
                                                        @endphp
                                                        <tr>
                                                            <td>{{ ar($subject->rating ?? '—') }}</td>
                                                            <td>{{ $subject->score ?? '—' }}</td>
                                                            <td>{{ $subject->credit_hours ?? '—' }}</td>
                                                            <td class="subj-name">{{ ar($subject->name) }}</td>
                                                            <td style="font-weight: bold; background-color: #f1f5f9;">{{ $i + 1 }}</td>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td class="subj-name">&nbsp;</td>
                                                            <td style="background-color: #f1f5f9;">&nbsp;</td>
                                                        </tr>
                                                    @endif
                                                @endfor
                                                @php
                                                    $avgScore1 = $totalHours1 > 0 ? ($weightedPoints1 / $totalHours1) : 0;
                                                    $rating1 = '';
                                                    if ($totalHours1 > 0) {
                                                        if ($avgScore1 >= 90)
                                                            $rating1 = 'ممتاز';
                                                        elseif ($avgScore1 >= 80)
                                                            $rating1 = 'جيد جداً';
                                                        elseif ($avgScore1 >= 70)
                                                            $rating1 = 'جيد';
                                                        elseif ($avgScore1 >= 60)
                                                            $rating1 = 'مقبول';
                                                        else
                                                            $rating1 = 'راسب';
                                                    }
                                                @endphp
                                                <tr class="sem-footer-row">
                                                    <td>{{ $rating1 ? ar($rating1) : '—' }}</td>
                                                    <td>{{ $totalHours1 > 0 ? number_format($avgScore1, 2) : '—' }}</td>
                                                    <td>{{ $totalHours1 > 0 ? $totalHours1 : '—' }}</td>
                                                    <td style="text-align:right; padding-right:4px;">{{ ar('المجموع') }}</td>
                                                    <td style="background-color: #f1f5f9;">&nbsp;</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </table>


                        </div>
                    @endforeach
                @else
                    <p style="text-align: center; font-size: 10px; color: #000000; margin-top: 10px;">
                        {{ ar('لا توجد بيانات السجل الأكاديمي المعتمد') }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Signatures + Footer (kept together on one page) --}}
        <div style="page-break-inside: avoid;">
            @php
                $docCode = strtolower($request->documentType->code ?? '');
                $isAcademicRecord = in_array($docCode, ['academic_record']);
                $isGradesCertificate = in_array($docCode, ['grades_certificate', 'grade_certificate', 'grades', 'certificate_grades']);

                $sigData = $signatures ?? collect();
            @endphp

            @if($isGradesCertificate)
                @php $signers = ['نائب رئيس الجامعة لشؤون الطلاب', 'المسجل العام', 'عميد الكلية', 'مسجل الكلية']; @endphp
                <table class="sig-table" dir="ltr">
                    <tr>
                        @foreach($signers as $role)
                            <td style="width: 25%;">
                                <div class="sig-title">{{ ar($role) }}</div>
                                @php $sig = $sigData->get($role); @endphp
                                @if($sig)
                                    @if($sig->user && ($sig->user->signature_base64 ?? false))
                                        <img src="data:image/png;base64,{{ $sig->user->signature_base64 }}" class="sig-img"
                                            alt="{{ ar($role) }}">
                                    @endif
                                    <span class="sig-signer-name">{{ ar($sig->user->name) }}</span>
                                    <span class="sig-date">{{ $sig->signed_at->format('Y-m-d') }}</span>
                                    <div class="sig-line"></div>
                                @else
                                    <div class="sig-pending">{{ ar('قيد التوقيع...') }}</div>
                                    <div class="sig-line"></div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </table>
            @elseif($isAcademicRecord)
                @php $signers = ['عميد الكلية', 'مسجل الكلية', 'مدير إدارة شؤون الخريجين', 'المختص الأكاديمي']; @endphp
                <table class="sig-table" dir="ltr">
                    <tr>
                        @foreach($signers as $role)
                            <td style="width: 25%;">
                                <div class="sig-title">{{ ar($role) }}</div>
                                @php $sig = $sigData->get($role); @endphp
                                @if($sig)
                                    @if($sig->user && ($sig->user->signature_base64 ?? false))
                                        <img src="data:image/png;base64,{{ $sig->user->signature_base64 }}" class="sig-img"
                                            alt="{{ ar($role) }}">
                                    @endif
                                    <span class="sig-signer-name">{{ ar($sig->user->name) }}</span>
                                    <span class="sig-date">{{ $sig->signed_at->format('Y-m-d') }}</span>
                                    <div class="sig-line"></div>
                                @else
                                    <div class="sig-pending">{{ ar('قيد التوقيع...') }}</div>
                                    <div class="sig-line"></div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </table>
            @else
                @php $signers = ['المسجل العام', 'عميد الكلية']; @endphp
                <table class="sig-table" dir="ltr">
                    <tr>
                        @foreach($signers as $role)
                            <td style="width: 50%;">
                                <div class="sig-title">{{ ar($role) }}</div>
                                @php $sig = $sigData->get($role); @endphp
                                @if($sig)
                                    @if($sig->user && ($sig->user->signature_base64 ?? false))
                                        <img src="data:image/png;base64,{{ $sig->user->signature_base64 }}" class="sig-img"
                                            alt="{{ ar($role) }}">
                                    @endif
                                    <span class="sig-signer-name">{{ ar($sig->user->name) }}</span>
                                    <span class="sig-date">{{ $sig->signed_at->format('Y-m-d') }}</span>
                                    <div class="sig-line"></div>
                                @else
                                    <div class="sig-pending">{{ ar('قيد التوقيع...') }}</div>
                                    <div class="sig-line"></div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </table>
            @endif
        </div> {{-- end page-break-inside avoid --}}
    </div>
</body>

</html>