<?php

namespace App\Helpers;

class AcademicHelper
{
    /**
     * Checks if the student has any numerical score of 64 or less.
     *
     * @param mixed $record GraduateAcademicRecord or GradesCertificate
     * @return bool
     */
    public static function hasHonorDisqualifyingGrade($record): bool
    {
        if (!$record) {
            return false;
        }

        // Load levels relationship if not loaded
        if (!$record->relationLoaded('levels')) {
            $record->load('levels.semesters.subjects');
        }

        foreach ($record->levels as $level) {
            if ($level->semesters) {
                foreach ($level->semesters as $semester) {
                    if ($semester->subjects) {
                        foreach ($semester->subjects as $subject) {
                            $score = $subject->score;
                            if ($score !== null && $score !== '' && is_numeric($score) && (float)$score <= 64) {
                                return true;
                            }
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Formats the Arabic session name cleanly to avoid repetitions like "دور الدور الأول".
     *
     * @param string|null $session
     * @return string
     */
    public static function formatArabicSession(?string $session): string
    {
        if ($session === null || trim($session) === '') {
            return 'دور —';
        }

        $trimmed = trim($session);

        // If it already starts with "دور " or "الدور" (case insensitive/arabic safe)
        if (str_starts_with($trimmed, 'دور ') || str_starts_with($trimmed, 'الدور')) {
            return $trimmed;
        }

        return 'دور ' . $trimmed;
    }
}
