<?php

namespace App\Services;

use App\Models\GradesCertificate;
use App\Models\User;
use App\Support\AcademicSubjectCatalog;
use Illuminate\Support\Facades\DB;

class GradesCertificateStorageService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function sync(User $user, array $data): GradesCertificate
    {
        return DB::transaction(function () use ($user, $data) {
            $student = $data['student'] ?? [];

            // Disqualify honors if there is any numerical grade <= 64 in the request payload
            $hasUnder64Score = false;
            if (isset($data['levels']) && is_array($data['levels'])) {
                foreach ($data['levels'] as $levelRow) {
                    $semesters = $levelRow['semesters'] ?? [];
                    foreach ($semesters as $semPayload) {
                        $subjects = $semPayload['subjects'] ?? [];
                        foreach ($subjects as $sub) {
                            $scoreVal = trim((string) ($sub['score'] ?? ''));
                            if ($scoreVal !== '' && is_numeric($scoreVal) && floatval($scoreVal) <= 64) {
                                $hasUnder64Score = true;
                                break 3;
                            }
                        }
                    }
                }
            }

            $honorsVal = $this->emptyToNull($student['honors'] ?? null);
            if ($honorsVal === 'مستحق' || $honorsVal === 'مستحقة') {
                $honorsVal = 'مع مرتبة الشرف';
            }
            if ($hasUnder64Score) {
                $honorsVal = null;
            }

            $record = GradesCertificate::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'student_name_ar' => $this->emptyToNull($student['name'] ?? null),
                    'student_name_en' => $this->emptyToNull($student['name_en'] ?? null),
                    'university_number' => $this->emptyToNull($student['id'] ?? null),
                    'degree_ar' => $this->emptyToNull($student['degree'] ?? null),
                    'degree_en' => $this->emptyToNull($student['degree_en'] ?? null),
                    'total_marks' => $this->emptyToNull($student['total'] ?? null),
                    'gpa' => $this->emptyToNull($student['gpa'] ?? null),
                    'overall_rating' => $this->emptyToNull($student['rating'] ?? null),
                    'honors_rank' => $honorsVal,
                    'graduation_year_label' => $this->emptyToNull($student['gradYear'] ?? null),
                    'enrollment_year_label' => $this->emptyToNull($student['enrollmentYear'] ?? null),
                    'exam_session' => $this->emptyToNull($student['dora'] ?? null),
                ]
            );

            $record->levels()->delete();

            foreach ($data['levels'] as $lIdx => $levelRow) {
                $semesters = $levelRow['semesters'] ?? [];
                $lPoints = 0;
                $lHours = 0;
                $hasSubjects = false;
                $hasFailedSubject = false;
                
                foreach ([0, 1] as $sIdx) {
                    $semPayload = $semesters[$sIdx] ?? ['subjects' => []];
                    $subjects = $semPayload['subjects'] ?? [];
                    foreach ($subjects as $sub) {
                        $scoreVal = trim((string) ($sub['score'] ?? ''));
                        $hoursVal = trim((string) ($sub['hours'] ?? ''));
                        $catalogKey = $this->emptyToNull($sub['catalog_key'] ?? null);
                        if ($catalogKey !== null) {
                            $cat = AcademicSubjectCatalog::findByKey($catalogKey);
                            if ($cat !== null) {
                                if ($hoursVal === '') {
                                    $hoursVal = (string) $cat['credit_hours'];
                                }
                            }
                        }
                        
                        if ($scoreVal !== '' && $hoursVal !== '') {
                            $hasSubjects = true;
                            $score = floatval($scoreVal);
                            $hours = floatval($hoursVal);
                            if ($hours > 0) {
                                $lPoints += $score * $hours;
                                $lHours += $hours;
                            }
                            if ($score < 60) {
                                $hasFailedSubject = true;
                            }
                        }
                    }
                }
                
                $calculatedResult = null;
                if ($hasSubjects && $lHours > 0) {
                    $avg = $lPoints / $lHours;
                    if ($avg >= 60 && !$hasFailedSubject) {
                        $calculatedResult = 'ناجح';
                    } else {
                        $calculatedResult = 'راسب';
                    }
                }

                $level = $record->levels()->create([
                    'sort_order' => $lIdx,
                    'name' => $levelRow['name'],
                    'academic_year' => $this->emptyToNull($levelRow['year'] ?? null),
                    'level_avg' => $this->emptyToNull($levelRow['avg'] ?? null),
                    'final_result' => $calculatedResult,
                ]);

                foreach ([0, 1] as $sIdx) {
                    $semPayload = $semesters[$sIdx] ?? ['subjects' => []];
                    $semester = $level->semesters()->create(['sort_order' => $sIdx]);

                    $subjects = $semPayload['subjects'] ?? [];
                    $subOrder = 0;
                    foreach ($subjects as $sub) {
                        $catalogKey = $this->emptyToNull($sub['catalog_key'] ?? null);
                        $name = trim((string) ($sub['name'] ?? ''));
                        $hours = trim((string) ($sub['hours'] ?? ''));
                        $score = trim((string) ($sub['score'] ?? ''));
                        $rating = trim((string) ($sub['rating'] ?? ''));
                        if ($catalogKey !== null) {
                            $cat = AcademicSubjectCatalog::findByKey($catalogKey);
                            if ($cat !== null) {
                                if ($name === '') {
                                    $name = $cat['name_ar'];
                                }
                                if ($hours === '') {
                                    $hours = (string) $cat['credit_hours'];
                                }
                            }
                        }
                        if ($name === '' && $hours === '' && $score === '' && $rating === '' && $catalogKey === null) {
                            continue;
                        }
                        $semester->subjects()->create([
                            'sort_order' => $subOrder++,
                            'catalog_key' => $catalogKey,
                            'name' => $name !== '' ? $name : '—',
                            'credit_hours' => $this->emptyToNull($hours !== '' ? $hours : null),
                            'score' => $this->emptyToNull($score !== '' ? $score : null),
                            'rating' => $this->emptyToNull($rating !== '' ? $rating : null),
                        ]);
                    }
                }
            }

            return $record->load(['levels.semesters.subjects']);
        });
    }

    private function emptyToNull(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
