<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GraduateAcademicRecord extends Model
{
    protected $fillable = [
        'user_id',
        'student_name_ar',
        'student_name_en',
        'university_number',
        'degree_ar',
        'degree_en',
        'total_marks',
        'gpa',
        'overall_rating',
        'honors_rank',
        'graduation_year_label',
        'enrollment_year_label',
        'exam_session',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function levels(): HasMany
    {
        return $this->hasMany(GraduateAcademicLevel::class)->orderBy('sort_order');
    }

    /**
     * @return array<string, mixed>
     */
    public function toAlpinePayload(): array
    {
        $this->loadMissing(['levels.semesters.subjects']);

        $levels = $this->levels->map(function (GraduateAcademicLevel $level) {
            $semesters = $level->semesters->map(function (GraduateAcademicSemester $sem) {
                $subjects = $sem->subjects->map(fn (GraduateAcademicSubject $s) => [
                    'catalog_key' => $s->catalog_key ?? '',
                    'name' => $s->name,
                    'hours' => $s->credit_hours ?? '',
                    'score' => $s->score ?? '',
                    'rating' => $s->rating ?? '',
                ])->values()->all();

                return ['subjects' => $subjects];
            })->values()->all();

            while (count($semesters) < 2) {
                $semesters[] = ['subjects' => []];
            }

            return [
                'name' => $level->name,
                'year' => $level->academic_year ?? '',
                'avg' => $level->level_avg ?? '',
                'totalPoints' => $level->total_points ?? '',
                'result' => $level->final_result ?? '',
                'semesters' => $semesters,
            ];
        })->values()->all();

        return [
            'student' => [
                'name' => $this->student_name_ar ?? '',
                'name_en' => $this->student_name_en ?? '',
                'id' => $this->university_number ?? '',
                'degree' => $this->degree_ar ?? '',
                'degree_en' => $this->degree_en ?? '',
                'total' => $this->total_marks ?? '',
                'gpa' => $this->gpa ?? '',
                'rating' => $this->overall_rating ?? '',
                'honors' => $this->honors_rank ?? '',
                'gradYear' => $this->graduation_year_label ?? '',
                'enrollmentYear' => $this->enrollment_year_label ?? '',
                'dora' => $this->exam_session ?? '',
            ],
            'levels' => $levels,
        ];
    }
}
