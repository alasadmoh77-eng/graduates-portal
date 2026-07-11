<?php

namespace App\Services;

use App\Imports\RawImport;
use App\Models\Faculty;
use App\Models\Graduate;
use App\Models\GraduateAcademicRecord;
use App\Models\Major;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AcademicRecordExcelImportService
{
    /**
     * Import academic records from Excel.
     *
     * @param UploadedFile $file
     * @param bool $updateStudentProfile
     * @return array
     */
    public function import(UploadedFile $file, bool $updateStudentProfile = false): array
    {
        $sheets = Excel::toArray(new RawImport, $file);
        $rows = $sheets[0] ?? [];

        if (empty($rows)) {
            throw new \Exception("الملف المرفوع فارغ.");
        }

        // 1. Map headers
        $headers = array_map(fn($h) => strtolower(trim((string)$h)), $rows[0]);
        $requiredColumns = [
            'university_id', 'student_name', 'college', 'department', 'degree',
            'admission_year', 'graduation_year', 'level', 'academic_year',
            'semester', 'subject_name', 'credit_hours', 'score', 'grade'
        ];

        $colIndexes = [];
        foreach ($requiredColumns as $col) {
            $idx = array_search($col, $headers);
            if ($idx === false) {
                throw new \Exception("الملف المرفوع لا يحتوي على العمود المطلوب: {$col}");
            }
            $colIndexes[$col] = $idx;
        }

        $studentRows = [];
        $errors = [];
        $successCount = 0;
        $newRecords = 0;
        $studentErrorCount = 0;

        // 2. Read and validate row contents
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            
            // Check if row is completely empty
            if (empty(array_filter($row, fn($val) => !is_null($val) && trim((string)$val) !== ''))) {
                continue;
            }

            $rowNum = $i + 1;

            $universityId = isset($row[$colIndexes['university_id']]) ? trim((string)$row[$colIndexes['university_id']]) : '';
            $studentName = isset($row[$colIndexes['student_name']]) ? trim((string)$row[$colIndexes['student_name']]) : '';
            $college = isset($row[$colIndexes['college']]) ? trim((string)$row[$colIndexes['college']]) : '';
            $department = isset($row[$colIndexes['department']]) ? trim((string)$row[$colIndexes['department']]) : '';
            $degree = isset($row[$colIndexes['degree']]) ? trim((string)$row[$colIndexes['degree']]) : '';
            $admissionYear = isset($row[$colIndexes['admission_year']]) ? trim((string)$row[$colIndexes['admission_year']]) : '';
            $graduationYear = isset($row[$colIndexes['graduation_year']]) ? trim((string)$row[$colIndexes['graduation_year']]) : '';
            $levelName = isset($row[$colIndexes['level']]) ? trim((string)$row[$colIndexes['level']]) : '';
            $academicYear = isset($row[$colIndexes['academic_year']]) ? trim((string)$row[$colIndexes['academic_year']]) : '';
            $semesterName = isset($row[$colIndexes['semester']]) ? trim((string)$row[$colIndexes['semester']]) : '';
            $subjectName = isset($row[$colIndexes['subject_name']]) ? trim((string)$row[$colIndexes['subject_name']]) : '';
            $creditHours = isset($row[$colIndexes['credit_hours']]) ? trim((string)$row[$colIndexes['credit_hours']]) : '';
            $score = isset($row[$colIndexes['score']]) ? trim((string)$row[$colIndexes['score']]) : '';
            $grade = isset($row[$colIndexes['grade']]) ? trim((string)$row[$colIndexes['grade']]) : '';

            if ($universityId === '') {
                $errors[] = "السطر {$rowNum}: الرقم الجامعي مطلوب.";
                continue;
            }

            if ($subjectName === '') {
                $errors[] = "السطر {$rowNum}: اسم المقرر مطلوب للرقم الجامعي {$universityId}.";
                continue;
            }

            if ($creditHours === '' || !is_numeric($creditHours) || $creditHours <= 0) {
                $errors[] = "السطر {$rowNum}: ساعات المقرر {$subjectName} للرقم الجامعي {$universityId} يجب أن تكون قيمة عددية أكبر من الصفر.";
                continue;
            }

            if ($score === '' || !is_numeric($score) || $score < 0 || $score > 100) {
                $errors[] = "السطر {$rowNum}: درجة المقرر {$subjectName} للرقم الجامعي {$universityId} يجب أن تكون قيمة عددية بين 0 و 100.";
                continue;
            }

            if (!isset($studentRows[$universityId])) {
                $studentRows[$universityId] = [
                    'student_name' => $studentName,
                    'college' => $college,
                    'department' => $department,
                    'degree' => $degree,
                    'admission_year' => $admissionYear,
                    'graduation_year' => $graduationYear,
                    'grades' => []
                ];
            }

            $studentRows[$universityId]['grades'][] = [
                'row_num' => $rowNum,
                'level' => $levelName,
                'academic_year' => $academicYear,
                'semester' => $semesterName,
                'subject_name' => $subjectName,
                'credit_hours' => (int)$creditHours,
                'score' => (float)$score,
                'grade' => $grade
            ];
        }

        // 3. Process grouped student academic records
        foreach ($studentRows as $universityId => $studentData) {
            DB::beginTransaction();
            try {
                $graduate = Graduate::where('university_id', $universityId)->first();
                
                if (!$graduate) {
                    $errors[] = "رقم الطالب {$universityId}: الطالب غير موجود في النظام.";
                    $studentErrorCount++;
                    DB::rollBack();
                    continue;
                }

                $user = $graduate->user;

                // Update Profile if allowed or if fields are empty
                if ($updateStudentProfile) {
                    if (!empty($studentData['student_name'])) {
                        $user->update(['name' => $studentData['student_name']]);
                    }
                    if (!empty($studentData['graduation_year']) && is_numeric($studentData['graduation_year'])) {
                        $graduate->update(['graduation_year' => (int)$studentData['graduation_year']]);
                    }

                    if (!empty($studentData['college'])) {
                        $faculty = Faculty::firstOrCreate([
                            'name_ar' => $studentData['college']
                        ], [
                            'name_en' => $studentData['college'],
                            'status' => 'active'
                        ]);

                        if (!empty($studentData['department'])) {
                            $major = Major::firstOrCreate([
                                'name_ar' => $studentData['department']
                            ], [
                                'name_en' => $studentData['department'],
                                'faculty_id' => $faculty->id,
                                'degree_name_ar' => $studentData['degree'] ?: 'بكالوريوس'
                            ]);

                            $graduate->update(['major_id' => $major->id]);
                        }
                    }
                } else {
                    // Update only if currently empty
                    if (empty($user->name) && !empty($studentData['student_name'])) {
                        $user->update(['name' => $studentData['student_name']]);
                    }
                    if (empty($graduate->graduation_year) && !empty($studentData['graduation_year']) && is_numeric($studentData['graduation_year'])) {
                        $graduate->update(['graduation_year' => (int)$studentData['graduation_year']]);
                    }

                    if (empty($graduate->major_id) && !empty($studentData['department'])) {
                        $faculty = null;
                        if (!empty($studentData['college'])) {
                            $faculty = Faculty::firstOrCreate([
                                'name_ar' => $studentData['college']
                            ], [
                                'name_en' => $studentData['college'],
                                'status' => 'active'
                            ]);
                        }
                        $major = Major::firstOrCreate([
                            'name_ar' => $studentData['department']
                        ], [
                            'name_en' => $studentData['department'],
                            'faculty_id' => $faculty ? $faculty->id : null,
                            'degree_name_ar' => $studentData['degree'] ?: 'بكالوريوس'
                        ]);
                        $graduate->update(['major_id' => $major->id]);
                    }
                }

                // Delete existing academic record for the student to prevent duplicate subjects
                $record = GraduateAcademicRecord::where('user_id', $user->id)->first();
                if ($record) {
                    foreach ($record->levels as $level) {
                        foreach ($level->semesters as $sem) {
                            $sem->subjects()->delete();
                            $sem->delete();
                        }
                        $level->delete();
                    }
                    $record->delete();
                    $newRecords++;
                } else {
                    $newRecords++;
                }

                // Create master record
                $record = GraduateAcademicRecord::create([
                    'user_id' => $user->id,
                    'student_name_ar' => $studentData['student_name'] ?: $user->name,
                    'university_number' => $universityId,
                    'degree_ar' => $studentData['degree'] ?: ($graduate->major->degree_name_ar ?? 'بكالوريوس'),
                    'graduation_year_label' => $studentData['graduation_year'] ?: (string)$graduate->graduation_year,
                    'enrollment_year_label' => $studentData['admission_year'] ?: '',
                ]);

                // Group by levels and semesters
                $groupedGrades = [];
                foreach ($studentData['grades'] as $gradeRow) {
                    $lvl = $gradeRow['level'] ?: 'الأول';
                    $sem = $gradeRow['semester'] ?: 'الفصل الأول';
                    $acadYr = $gradeRow['academic_year'] ?: '';
                    
                    if (!isset($groupedGrades[$lvl])) {
                        $groupedGrades[$lvl] = [
                            'academic_year' => $acadYr,
                            'semesters' => []
                        ];
                    }
                    if (!isset($groupedGrades[$lvl]['semesters'][$sem])) {
                        $groupedGrades[$lvl]['semesters'][$sem] = [];
                    }
                    $groupedGrades[$lvl]['semesters'][$sem][] = $gradeRow;
                }

                // Sort levels based on standard order
                uksort($groupedGrades, function($a, $b) {
                    return $this->getLevelOrder($a) <=> $this->getLevelOrder($b);
                });

                $totalPointsAll = 0;
                $totalHoursAll = 0;
                $levelOrder = 1;

                foreach ($groupedGrades as $lvlName => $lvlData) {
                    $semesters = $lvlData['semesters'];
                    $lPoints = 0;
                    $lHours = 0;
                    $hasSubjects = false;
                    $hasFailedSubject = false;

                    foreach ($semesters as $semName => $subjectsList) {
                        foreach ($subjectsList as $subData) {
                            $hasSubjects = true;
                            $score = $subData['score'];
                            $hours = $subData['credit_hours'];
                            if ($hours > 0) {
                                $lPoints += $score * $hours;
                                $lHours += $hours;
                                $totalPointsAll += $score * $hours;
                                $totalHoursAll += $hours;
                            }
                            if ($score < 60) {
                                $hasFailedSubject = true;
                            }
                        }
                    }

                    $calculatedResult = null;
                    $levelAvg = null;
                    if ($hasSubjects && $lHours > 0) {
                        $levelAvg = round($lPoints / $lHours, 2);
                        if ($levelAvg >= 60 && !$hasFailedSubject) {
                            $calculatedResult = 'ناجح';
                        } else {
                            $calculatedResult = 'راسب';
                        }
                    }

                    $level = $record->levels()->create([
                        'sort_order' => $levelOrder++,
                        'name' => $lvlName,
                        'academic_year' => $lvlData['academic_year'],
                        'level_avg' => $levelAvg,
                        'final_result' => $calculatedResult,
                    ]);

                    $semOrder = 1;
                    foreach ($semesters as $semName => $subjectsList) {
                        $isSecondSemester = false;
                        $semLower = strtolower($semName);
                        if (str_contains($semLower, 'ثاني') || str_contains($semLower, '2') || str_contains($semLower, 'second') || str_contains($semLower, 'ii')) {
                            $isSecondSemester = true;
                        }
                        $sIdx = $isSecondSemester ? 1 : 0;

                        $semester = $level->semesters()->create(['sort_order' => $sIdx]);

                        $subOrder = 1;
                        foreach ($subjectsList as $subData) {
                            $semester->subjects()->create([
                                'sort_order' => $subOrder++,
                                'name' => $subData['subject_name'],
                                'credit_hours' => $subData['credit_hours'],
                                'score' => $subData['score'],
                                'rating' => $subData['grade'] ?: $this->getSubjectRating($subData['score']),
                            ]);
                        }
                    }
                }

                $cumulativeGpa = $totalHoursAll > 0 ? round($totalPointsAll / $totalHoursAll, 2) : null;
                $overallRating = $cumulativeGpa !== null ? $this->getOverallRating($cumulativeGpa) : null;

                $record->update([
                    'total_marks' => $totalPointsAll ?: null,
                    'gpa' => $cumulativeGpa ?: null,
                    'overall_rating' => $overallRating ?: null,
                ]);

                DB::commit();
                $successCount++;
            } catch (\Exception $e) {
                DB::rollBack();
                $errors[] = "رقم الطالب {$universityId}: خطأ أثناء الحفظ - " . $e->getMessage();
                $studentErrorCount++;
            }
        }

        return [
            'success_count' => $successCount,
            'new_records' => $newRecords,
            'error_count' => count($errors),
            'errors' => $errors
        ];
    }

    /**
     * Map level name to standard sort order.
     */
    private function getLevelOrder(string $levelName): int
    {
        $levelName = strtolower(trim($levelName));
        $order = [
            'الأول' => 1, 'الأولى' => 1, '1' => 1, 'first' => 1, 'level 1' => 1, 'level1' => 1,
            'الثاني' => 2, 'الثانية' => 2, '2' => 2, 'second' => 2, 'level 2' => 2, 'level2' => 2,
            'الثالث' => 3, 'الثالثة' => 3, '3' => 3, 'third' => 3, 'level 3' => 3, 'level3' => 3,
            'الرابع' => 4, 'الرابعة' => 4, '4' => 4, 'fourth' => 4, 'level 4' => 4, 'level4' => 4,
            'الخامس' => 5, 'الخامسة' => 5, '5' => 5, 'fifth' => 5, 'level 5' => 5, 'level5' => 5,
        ];
        return $order[$levelName] ?? 99;
    }

    /**
     * Get subject rating text from score.
     */
    private function getSubjectRating(float $score): string
    {
        if ($score >= 90) return 'ممتاز';
        if ($score >= 80) return 'جيد جداً';
        if ($score >= 70) return 'جيد';
        if ($score >= 65) return 'مقبول مرتفع';
        if ($score >= 60) return 'مقبول';
        return 'ضعيف';
    }

    /**
     * Get overall rating text from cumulative GPA average.
     */
    private function getOverallRating(float $gpa): string
    {
        if ($gpa >= 90) return 'ممتاز';
        if ($gpa >= 80) return 'جيد جداً';
        if ($gpa >= 70) return 'جيد';
        if ($gpa >= 60) return 'مقبول';
        return 'ضعيف';
    }
}
