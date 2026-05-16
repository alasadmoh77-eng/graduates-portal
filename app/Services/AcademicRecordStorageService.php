<?php

namespace App\Services;

use App\Models\GraduateAcademicRecord;
use App\Models\User;
use App\Support\AcademicSubjectCatalog;
use Illuminate\Support\Facades\DB;

class AcademicRecordStorageService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function sync(User $user, array $data): GraduateAcademicRecord
    {
        return DB::transaction(function () use ($user, $data) {
            $student = $data['student'] ?? [];

            $record = GraduateAcademicRecord::updateOrCreate(
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
                    'honors_rank' => $this->emptyToNull($student['honors'] ?? null),
                    'graduation_year_label' => $this->emptyToNull($student['gradYear'] ?? null),
                    'enrollment_year_label' => $this->emptyToNull($student['enrollmentYear'] ?? null),
                    'exam_session' => $this->emptyToNull($student['dora'] ?? null),
                ]
            );

            $record->levels()->delete();

            foreach ($data['levels'] as $lIdx => $levelRow) {
                $level = $record->levels()->create([
                    'sort_order' => $lIdx,
                    'name' => $levelRow['name'],
                    'academic_year' => $this->emptyToNull($levelRow['year'] ?? null),
                    'level_avg' => $this->emptyToNull($levelRow['avg'] ?? null),
                    'total_points' => $this->emptyToNull($levelRow['totalPoints'] ?? null),
                    'final_result' => $this->emptyToNull($levelRow['result'] ?? null),
                ]);

                $semesters = $levelRow['semesters'] ?? [];
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
