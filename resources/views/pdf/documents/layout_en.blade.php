<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    @include('pdf.documents._styles')
</head>
@php
    $enName = trim((string) ($academic_record->student_name_en ?? '')) !== ''
        ? $academic_record->student_name_en
        : ($request->user->name ?? '—');
    $enUid = $academic_record->university_number ?: ($request->user->graduate->university_id ?? '—');
    
    // Resolve dynamic degree name from majors database or helpers
    $enDegree = $academic_record->degree_en
        ?: ($request->user->graduate->major->degree_name_en
            ?: \App\Support\AcademicRecordEnglishPdf::degree($academic_record->degree_en, $academic_record->degree_ar));
            
    $enMajor = \App\Support\AcademicRecordEnglishPdf::majorName(
        $request->user->graduate->major->name_en ?? null,
        $request->user->graduate->major->name_ar ?? null
    );
    $enFaculty = $request->user->graduate->major->faculty->name_en
        ?? $request->user->graduate->major->faculty->name_ar
        ?? '—';
    $enGradY = $academic_record->graduation_year_label ?: ($request->user->graduate->graduation_year ?? '—');
    $enRating = \App\Support\AcademicRecordEnglishPdf::rating($academic_record->overall_rating);
    $enGpa = $academic_record->gpa ?: '—';
    $enTotal = $academic_record->total_marks ?: '—';
    $hasDisqualifying = \App\Helpers\AcademicHelper::hasHonorDisqualifyingGrade($academic_record);
    $rawHonors = ($academic_record && !$hasDisqualifying) ? $academic_record->honors_rank : null;
    $enHonors = \App\Support\AcademicRecordEnglishPdf::honors($rawHonors);
    $enEnrollY = $academic_record->enrollment_year_label ?: '—';
    $enExamSession = \App\Support\AcademicRecordEnglishPdf::examSession($academic_record->exam_session);
    $cleanGpa = rtrim($enGpa, '%');
@endphp
<body class="academic-page-content ltr">
    <div class="page-border"></div>
    <div class="page-wrap">
        {{-- LTR Header --}}
        <table class="header-table">
            <tr>
                <td class="header-left">
                    <table style="width: 100%;">
                        <tr>
                            <td style="vertical-align: top; width: auto;">
                                <div class="date-box-container">
                                    <table class="date-box-table">
                                        <tr>
                                            <td class="label">Date</td>
                                            <td class="val">{{ $issue_date }}</td>
                                        </tr>
                                        <tr>
                                            <td class="label">Serial</td>
                                            <td class="val" style="font-family: monospace; font-size: 5.5px;">{{ $serial_number }}</td>
                                        </tr>
                                        <tr>
                                            <td class="label">Ref</td>
                                            <td class="val" style="font-family: monospace; font-size: 5.5px;">{{ $request->tracking_code }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                            <td style="vertical-align: top; padding-left: 6px;">
                                <img src="data:image/svg+xml;base64,{{ $qr_code }}" class="qr-img-header">
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="header-center">
                    <img src="{{ public_path('assets/images/university-logo-pdf.png') }}" alt="SRU" class="uni-logo">
                    <div class="uni-name-under-logo">Saba Region University</div>
                    <div class="header-doc-title">@yield('title')</div>
                </td>
                <td class="header-right" style="text-align: left;">
                    Republic of Yemen<br>
                    Ministry of Higher Education & Scientific Research<br>
                    Saba Region University<br>
                    College of {{ $enFaculty }}<br>
                    Department of {{ $enMajor }}
                </td>
            </tr>
        </table>

        {{-- Student Information Panel --}}
        <table class="student-table">
            <tr>
                <td style="width: 37%;">
                    <div><strong class="important-label">Name:</strong> <span class="important-value">{{ $enName }}</span></div>
                </td>
                <td style="width: 38%;">
                    <table style="width: 100%; border-collapse: collapse; border: none;">
                        <tr style="border: none;">
                            <td style="border: none; padding: 0; text-align: left;"><strong class="important-label">Total:</strong> <span class="important-value">{{ $enTotal }}</span></td>
                            <td style="border: none; padding: 0; text-align: right;"><strong class="important-label">GPA:</strong> <span class="important-value">{{ $cleanGpa }}%</span></td>
                        </tr>
                    </table>
                </td>
                <td style="width: 25%;">
                    <div><strong class="important-label">Enrollment:</strong> <span class="important-value">{{ $enEnrollY }}</span></div>
                </td>
            </tr>
            <tr>
                <td>
                    <div><strong class="important-label">ID:</strong> <span class="important-value">{{ $enUid }}</span></div>
                </td>
                <td>
                    <table style="width: 100%; border-collapse: collapse; border: none;">
                        <tr style="border: none;">
                            <td style="border: none; padding: 0; text-align: left;"><strong class="important-label">Rating:</strong> <span class="important-value">{{ $enRating }}</span></td>
                            @if($enHonors && $enHonors !== '—')
                                <td style="border: none; padding: 0; text-align: center;"><strong class="important-label">With Honors</strong></td>
                            @endif
                            <td style="border: none; padding: 0; text-align: right;"><strong class="important-label">Session:</strong> <span class="important-value">{{ $enExamSession }}</span></td>
                        </tr>
                    </table>
                </td>
                <td>
                    <div><strong class="important-label">Graduation:</strong> <span class="important-value">{{ $enGradY }}</span></div>
                </td>
            </tr>
            <tr>
                <td>
                    <div><strong class="important-label">Degree:</strong> <span class="important-value">{{ $enDegree }}</span></div>
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>

        {{-- Optional Introductory Paragraph --}}
        @yield('intro')

        {{-- Side-by-side balanced Semester Tables --}}
        @if($academic_record && $academic_record->levels)
            @foreach($academic_record->levels as $idx => $level)
                @php
                    $hasSubjectsInLevel = false;
                    if (isset($level->semesters)) {
                        foreach($level->semesters as $sem) {
                            if (isset($sem->subjects) && count($sem->subjects) > 0) {
                                $hasSubjectsInLevel = true;
                                break;
                            }
                        }
                    }
                @endphp
                @if(!$hasSubjectsInLevel)
                    @continue
                @endif
                
                <div class="level-block">
                    @php
                        $levelEnName = \App\Support\AcademicRecordEnglishPdf::levelName($level->name, $idx);
                        $levelEnResult = $level->final_result ? \App\Support\AcademicRecordEnglishPdf::result($level->final_result) : '—';
                    @endphp
                    <table class="level-header-bar" style="direction: ltr;">
                        <tr>
                            <td style="width: 15%; text-align: center; background-color: #f1f5f9; font-weight: bold;">{{ $levelEnName }}</td>
                            <td style="width: 25%; text-align: left;"><strong>Academic Year:</strong> {{ $level->academic_year ?? '—' }}</td>
                            <td style="width: 20%; text-align: left;"><strong>Total:</strong> {{ $level->total_points ?? '—' }}</td>
                            <td style="width: 20%; text-align: left;"><strong>Average:</strong> {{ $level->level_avg ?? '—' }}%</td>
                            <td style="width: 20%; text-align: left;"><strong>Result:</strong> {{ $levelEnResult }}</td>
                        </tr>
                    </table>

                    <table class="sem-grid">
                        <tr>
                            @php
                                $sem1 = $level->semesters->firstWhere('sort_order', 0);
                                $sem2 = $level->semesters->firstWhere('sort_order', 1);
                                
                                $sem1Subjects = ($sem1 && isset($sem1->subjects)) ? $sem1->subjects : collect();
                                $sem2Subjects = ($sem2 && isset($sem2->subjects)) ? $sem2->subjects : collect();
                                
                                $maxRows = max($sem1Subjects->count(), $sem2Subjects->count());
                            @endphp

                            {{-- First Semester (Left in LTR) --}}
                            <td class="first-sem">
                                <div class="sem-title">First Semester</div>
                                <table class="subjects-table">
                                    <thead>
                                        <tr>
                                            <th style="width:5%;">No.</th>
                                            <th style="width:53%;">Course</th>
                                            <th style="width:11%;">Cr.Hr.</th>
                                            <th style="width:13%;">Score</th>
                                            <th style="width:18%;">Grade</th>
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
                                                    $score = is_numeric($subject->score) ? (float)$subject->score : null;
                                                    $hours = is_numeric($subject->credit_hours) ? (float)$subject->credit_hours : null;
                                                    if ($score !== null) {
                                                        $totalScore1 += $score;
                                                        if ($hours !== null) {
                                                            $weightedPoints1 += $score * $hours;
                                                            $totalHours1 += $hours;
                                                        }
                                                    }
                                                    $subName = \App\Support\AcademicRecordEnglishPdf::courseName($subject->catalog_key, $subject->name);
                                                    $subRating = \App\Support\AcademicRecordEnglishPdf::rating($subject->rating);
                                                @endphp
                                                <tr>
                                                    <td style="font-weight: bold; background-color: #f1f5f9;">{{ $i + 1 }}</td>
                                                    <td class="subj-name" style="text-align: left;">{{ $subName }}</td>
                                                    <td>{{ $subject->credit_hours ?? '—' }}</td>
                                                    <td>{{ $subject->score ?? '—' }}</td>
                                                    <td>{{ $subRating }}</td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td style="background-color: #f1f5f9;">&nbsp;</td>
                                                    <td class="subj-name">&nbsp;</td>
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>
                                                </tr>
                                            @endif
                                        @endfor
                                        @php
                                            $avgScore1 = $totalHours1 > 0 ? ($weightedPoints1 / $totalHours1) : 0;
                                            $rating1 = '';
                                            if ($totalHours1 > 0) {
                                                if ($avgScore1 >= 90) $rating1 = 'Excellent';
                                                elseif ($avgScore1 >= 80) $rating1 = 'Very Good';
                                                elseif ($avgScore1 >= 70) $rating1 = 'Good';
                                                elseif ($avgScore1 >= 60) $rating1 = 'Pass';
                                                else $rating1 = 'Fail';
                                            }
                                        @endphp
                                        <tr class="sem-footer-row">
                                            <td style="background-color: #f1f5f9;">&nbsp;</td>
                                            <td style="text-align:left; padding-left:4px;">Total</td>
                                            <td>{{ $totalHours1 > 0 ? $totalHours1 : '—' }}</td>
                                            <td>{{ $totalHours1 > 0 ? number_format($avgScore1, 2) : '—' }}</td>
                                            <td>{{ $rating1 ?: '—' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>

                            {{-- Second Semester (Right in LTR) --}}
                            <td class="second-sem">
                                <div class="sem-title">Second Semester</div>
                                <table class="subjects-table">
                                    <thead>
                                        <tr>
                                            <th style="width:5%;">No.</th>
                                            <th style="width:53%;">Course</th>
                                            <th style="width:11%;">Cr.Hr.</th>
                                            <th style="width:13%;">Score</th>
                                            <th style="width:18%;">Grade</th>
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
                                                    $score = is_numeric($subject->score) ? (float)$subject->score : null;
                                                    $hours = is_numeric($subject->credit_hours) ? (float)$subject->credit_hours : null;
                                                    if ($score !== null) {
                                                        $totalScore2 += $score;
                                                        if ($hours !== null) {
                                                            $weightedPoints2 += $score * $hours;
                                                            $totalHours2 += $hours;
                                                        }
                                                    }
                                                    $subName = \App\Support\AcademicRecordEnglishPdf::courseName($subject->catalog_key, $subject->name);
                                                    $subRating = \App\Support\AcademicRecordEnglishPdf::rating($subject->rating);
                                                @endphp
                                                <tr>
                                                    <td style="font-weight: bold; background-color: #f1f5f9;">{{ $i + 1 }}</td>
                                                    <td class="subj-name" style="text-align: left;">{{ $subName }}</td>
                                                    <td>{{ $subject->credit_hours ?? '—' }}</td>
                                                    <td>{{ $subject->score ?? '—' }}</td>
                                                    <td>{{ $subRating }}</td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td style="background-color: #f1f5f9;">&nbsp;</td>
                                                    <td class="subj-name">&nbsp;</td>
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>
                                                </tr>
                                            @endif
                                        @endfor
                                        @php
                                            $avgScore2 = $totalHours2 > 0 ? ($weightedPoints2 / $totalHours2) : 0;
                                            $rating2 = '';
                                            if ($totalHours2 > 0) {
                                                if ($avgScore2 >= 90) $rating2 = 'Excellent';
                                                elseif ($avgScore2 >= 80) $rating2 = 'Very Good';
                                                elseif ($avgScore2 >= 70) $rating2 = 'Good';
                                                elseif ($avgScore2 >= 60) $rating2 = 'Pass';
                                                else $rating2 = 'Fail';
                                            }
                                        @endphp
                                        <tr class="sem-footer-row">
                                            <td style="background-color: #f1f5f9;">&nbsp;</td>
                                            <td style="text-align:left; padding-left:4px;">Total</td>
                                            <td>{{ $totalHours2 > 0 ? $totalHours2 : '—' }}</td>
                                            <td>{{ $totalHours2 > 0 ? number_format($avgScore2, 2) : '—' }}</td>
                                            <td>{{ $rating2 ?: '—' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            @endforeach
        @else
            <p style="text-align: center; font-size: 10px; color: #000000; margin-top: 10px;">No certified academic record data available.</p>
        @endif

        {{-- Signatures + Footer (kept together on one page) --}}
            <div style="page-break-inside: avoid;">
            @php
                $docCode = strtolower($request->documentType->code ?? '');
                $isAcademicRecord = in_array($docCode, ['academic_record']);
                $isGradesCertificate = in_array($docCode, ['grades_certificate', 'grade_certificate', 'grades', 'certificate_grades']);

                $sigData = $signatures ?? collect();
            @endphp

            @if($isGradesCertificate)
                @php $signers = ['Vice President for Student Affairs', 'General Registrar', 'Dean of Faculty', 'College Registrar']; @endphp
                @php $signersAr = ['نائب رئيس الجامعة لشؤون الطلاب', 'المسجل العام', 'عميد الكلية', 'مسجل الكلية']; @endphp
                <table class="sig-table">
                    <tr>
                        @foreach($signers as $i => $role)
                            <td style="width: 25%;">
                                <div class="sig-title">{{ $role }}</div>
                                @php $sig = $sigData->get($signersAr[$i]); @endphp
                                @if($sig)
                                    @if($sig->user && ($sig->user->signature_base64 ?? false))
                                        <img src="data:image/png;base64,{{ $sig->user->signature_base64 }}" class="sig-img" alt="{{ $role }}">
                                    @endif
                                    <span class="sig-signer-name">{{ $sig->user->name }}</span>
                                    <span class="sig-date">{{ $sig->signed_at->format('Y-m-d') }}</span>
                                    <div class="sig-line"></div>
                                @else
                                    <div class="sig-pending">Pending...</div>
                                    <div class="sig-line"></div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </table>
            @elseif($isAcademicRecord)
                @php $signers = ['Dean of Faculty', 'College Registrar', 'Director of Graduate Affairs', 'Academic Specialist']; @endphp
                @php $signersAr = ['عميد الكلية', 'مسجل الكلية', 'مدير إدارة شؤون الخريجين', 'المختص الأكاديمي']; @endphp
                <table class="sig-table">
                    <tr>
                        @foreach($signers as $i => $role)
                            <td style="width: 25%;">
                                <div class="sig-title">{{ $role }}</div>
                                @php $sig = $sigData->get($signersAr[$i]); @endphp
                                @if($sig)
                                    @if($sig->user && ($sig->user->signature_base64 ?? false))
                                        <img src="data:image/png;base64,{{ $sig->user->signature_base64 }}" class="sig-img" alt="{{ $role }}">
                                    @endif
                                    <span class="sig-signer-name">{{ $sig->user->name }}</span>
                                    <span class="sig-date">{{ $sig->signed_at->format('Y-m-d') }}</span>
                                    <div class="sig-line"></div>
                                @else
                                    <div class="sig-pending">Pending...</div>
                                    <div class="sig-line"></div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </table>
            @else
                <table class="sig-table">
                    <tr>
                        <td style="width: 33%;">
                            <div class="sig-title">Faculty Registrar</div>
                            <div class="sig-line"></div>
                        </td>
                        <td style="width: 34%;">
                            <div style="border: 1px dashed #000000; display: inline-block; padding: 4px 10px; margin-top: 2px;">
                                <span style="font-size: 6px; font-weight: bold; color: #000000;">OFFICIAL SEAL</span><br>
                                <span style="font-size: 5px; color: #000000; font-weight: normal; letter-spacing: 0.5px;">SABA REGION UNIVERSITY</span>
                            </div>
                        </td>
                        <td style="width: 33%;">
                            <div class="sig-title">General Registrar</div>
                            <div class="sig-line"></div>
                        </td>
                    </tr>
                </table>
            @endif
            </div> {{-- end page-break-inside avoid --}}
    </div>
</body>
</html>
