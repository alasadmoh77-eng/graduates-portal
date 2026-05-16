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
        <div class="detail-block">
            <div class="detail-level-title">
                @if(app()->getLocale() === 'ar')
                    {{ ar('المستوى') }} {{ ar($level->name) }}
                    @if($level->academic_year)
                        — {{ ar('العام الجامعي') }}: {{ ar($level->academic_year) }}
                    @endif
                    @if($level->level_avg)
                        — {{ ar('المعدل') }} %: {{ $level->level_avg }}
                    @endif
                @else
                    {{ \App\Support\AcademicRecordEnglishPdf::levelName($level->name ?? '', $loop->index) }}
                    @if($level->academic_year)
                        — Academic year: {{ $level->academic_year }}
                    @endif
                    @if($level->level_avg)
                        — Average %: {{ $level->level_avg }}
                    @endif
                @endif
            </div>
            @foreach($level->semesters as $semester)
                @if(!isset($semester->subjects) || count($semester->subjects) == 0)
                    @continue
                @endif
                <div class="detail-sem-title">
                    @if(app()->getLocale() === 'ar')
                        {{ $semester->sort_order === 0 ? ar('الفصل الدراسي الأول') : ar('الفصل الدراسي الثاني') }}
                    @else
                        {{ \App\Support\AcademicRecordEnglishPdf::semesterName((int) $semester->sort_order) }}
                    @endif
                </div>
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th style="width:7%;">#</th>
                            <th class="col-subject">
                                @if(app()->getLocale() === 'ar')
                                    {{ ar('المادة') }}
                                @else
                                    Course
                                @endif
                            </th>
                            <th style="width:11%;">
                                @if(app()->getLocale() === 'ar')
                                    {{ ar('الساعات') }}
                                @else
                                    Cr.
                                @endif
                            </th>
                            <th style="width:11%;">
                                @if(app()->getLocale() === 'ar')
                                    {{ ar('الدرجة') }}
                                @else
                                    Score
                                @endif
                            </th>
                            <th style="width:16%;">
                                @if(app()->getLocale() === 'ar')
                                    {{ ar('التقدير') }}
                                @else
                                    Grade
                                @endif
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($semester->subjects as $idx => $subject)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td class="col-subject" dir="auto">
                                    @if(app()->getLocale() === 'ar')
                                        {{ ar($subject->name) }}
                                    @else
                                        {{ \App\Support\AcademicRecordEnglishPdf::courseName($subject->catalog_key ?? null, $subject->name) }}
                                    @endif
                                </td>
                                <td>{{ $subject->credit_hours ?? '—' }}</td>
                                <td>{{ $subject->score ?? '—' }}</td>
                                <td>
                                    @if(app()->getLocale() === 'ar')
                                        {{ ar($subject->rating ?? '—') }}
                                    @else
                                        {{ \App\Support\AcademicRecordEnglishPdf::rating($subject->rating ?? null) }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5">
                                @if(app()->getLocale() === 'ar')
                                    {{ ar('لا توجد مقررات مسجلة') }}
                                @else
                                    No courses recorded.
                                @endif
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            @endforeach
            @if($level->total_points || $level->final_result)
                <div class="level-footer">
                    @if(app()->getLocale() === 'ar')
                        {{ ar('إجمالي المستوى') }}: {{ $level->total_points ?? '—' }}
                        &nbsp;|&nbsp;
                        {{ ar('النتيجة') }}: {{ ar($level->final_result ?? '—') }}
                    @else
                        Level total: {{ $level->total_points ?? '—' }}
                        &nbsp;|&nbsp;
                        Result: {{ \App\Support\AcademicRecordEnglishPdf::result($level->final_result ?? null) }}
                    @endif
                </div>
            @endif
        @endforeach
    </div>
@else
    <p style="text-align: center; font-size: 14px; color: red;">{{ app()->getLocale() === 'ar' ? ar('لا توجد بيانات السجل الأكاديمي') : 'No academic record data available.' }}</p>
@endif
