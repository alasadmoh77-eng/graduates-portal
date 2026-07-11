@if($academic_record && $academic_record->levels)
    @foreach($academic_record->levels as $level)
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
            <div class="level-header-text">
                @php
                    $levelTotal = 0;
                    $hasScores = false;
                    if (isset($level->semesters)) {
                        foreach ($level->semesters as $sem) {
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
                    $levelText = 'المستوى ' . $level->name;
                    if ($level->academic_year) {
                        $levelText .= ' — العام الجامعي: ' . $level->academic_year;
                    }
                    if ($hasScores) {
                        $levelText .= ' — المجموع: ' . rtrim(rtrim(number_format($levelTotal, 2), '0'), '.');
                    }
                    if ($level->level_avg) {
                        $levelText .= ' — المعدل: ' . $level->level_avg . '%';
                    }
                @endphp
                {{ ar($levelText) }}
            </div>

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

                        $hasSem1 = $sem1 && isset($sem1->subjects) && count($sem1->subjects) > 0;
                        $hasSem2 = $sem2 && isset($sem2->subjects) && count($sem2->subjects) > 0;
                    @endphp

                    {{-- Second Semester (Left in LTR) --}}
                    <td class="sem-cell second-sem">
                        @if($hasSem2)
                            <div class="sem-title">{{ ar('الفصل الدراسي الثاني') }}</div>
                            <table class="subjects-table">
                                <thead>
                                    <tr>
                                        <th style="width:48%;">{{ ar('المقرر') }}</th>
                                        <th style="width:13%;">{{ ar('الساعات') }}</th>
                                        <th style="width:16%;">{{ ar('الدرجة') }}</th>
                                        <th style="width:23%;">{{ ar('التقدير') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalScore = 0;
                                        $totalHours = 0;
                                        $weightedPoints = 0;
                                    @endphp
                                    @foreach($sem2->subjects as $idx => $subject)
                                        @php
                                            $score = is_numeric($subject->score) ? (float)$subject->score : null;
                                            $hours = is_numeric($subject->credit_hours) ? (float)$subject->credit_hours : null;
                                            if ($score !== null) {
                                                $totalScore += $score;
                                                if ($hours !== null) {
                                                    $weightedPoints += $score * $hours;
                                                    $totalHours += $hours;
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            <td class="subj-name">{{ ar($subject->name) }}</td>
                                            <td>{{ $subject->credit_hours ?? '—' }}</td>
                                            <td>{{ $subject->score ?? '—' }}</td>
                                            <td>{{ ar($subject->rating ?? '—') }}</td>
                                        </tr>
                                    @endforeach
                                    @php
                                        $avgScore = $totalHours > 0 ? ($weightedPoints / $totalHours) : 0;
                                        $rating = '';
                                        if ($totalHours > 0) {
                                            if ($avgScore >= 90) $rating = 'ممتاز';
                                            elseif ($avgScore >= 80) $rating = 'جيد جداً';
                                            elseif ($avgScore >= 70) $rating = 'جيد';
                                            elseif ($avgScore >= 60) $rating = 'مقبول';
                                            else $rating = 'راسب';
                                        }
                                    @endphp
                                    <tr class="sem-footer-row">
                                        <td style="text-align:right; padding-right:4px;">{{ ar('المجموع') }}</td>
                                        <td>{{ $totalHours > 0 ? $totalHours : '—' }}</td>
                                        <td>{{ $totalHours > 0 ? number_format($avgScore, 2) : '—' }}</td>
                                        <td>{{ $rating ? ar($rating) : '—' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        @endif
                    </td>

                    {{-- First Semester (Right in LTR) --}}
                    <td class="sem-cell first-sem">
                        @if($hasSem1)
                            <div class="sem-title">{{ ar('الفصل الدراسي الأول') }}</div>
                            <table class="subjects-table">
                                <thead>
                                    <tr>
                                        <th style="width:48%;">{{ ar('المقرر') }}</th>
                                        <th style="width:13%;">{{ ar('الساعات') }}</th>
                                        <th style="width:16%;">{{ ar('الدرجة') }}</th>
                                        <th style="width:23%;">{{ ar('التقدير') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalScore = 0;
                                        $totalHours = 0;
                                        $weightedPoints = 0;
                                    @endphp
                                    @foreach($sem1->subjects as $idx => $subject)
                                        @php
                                            $score = is_numeric($subject->score) ? (float)$subject->score : null;
                                            $hours = is_numeric($subject->credit_hours) ? (float)$subject->credit_hours : null;
                                            if ($score !== null) {
                                                $totalScore += $score;
                                                if ($hours !== null) {
                                                    $weightedPoints += $score * $hours;
                                                    $totalHours += $hours;
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            <td class="subj-name">{{ ar($subject->name) }}</td>
                                            <td>{{ $subject->credit_hours ?? '—' }}</td>
                                            <td>{{ $subject->score ?? '—' }}</td>
                                            <td>{{ ar($subject->rating ?? '—') }}</td>
                                        </tr>
                                    @endforeach
                                    @php
                                        $avgScore = $totalHours > 0 ? ($weightedPoints / $totalHours) : 0;
                                        $rating = '';
                                        if ($totalHours > 0) {
                                            if ($avgScore >= 90) $rating = 'ممتاز';
                                            elseif ($avgScore >= 80) $rating = 'جيد جداً';
                                            elseif ($avgScore >= 70) $rating = 'جيد';
                                            elseif ($avgScore >= 60) $rating = 'مقبول';
                                            else $rating = 'راسب';
                                        }
                                    @endphp
                                    <tr class="sem-footer-row">
                                        <td style="text-align:right; padding-right:4px;">{{ ar('المجموع') }}</td>
                                        <td>{{ $totalHours > 0 ? $totalHours : '—' }}</td>
                                        <td>{{ $totalHours > 0 ? number_format($avgScore, 2) : '—' }}</td>
                                        <td>{{ $rating ? ar($rating) : '—' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        @endif
                    </td>
                </tr>
            </table>

            @if($level->final_result)
                <div class="level-result">
                    {{ ar('النتيجة: ' . ($level->final_result ?? '—')) }}
                </div>
            @endif
        </div>
    @endforeach
@else
    <p style="text-align: center; font-size: 12px; color: #000;">{{ ar('لا توجد بيانات السجل الأكاديمي') }}</p>
@endif
