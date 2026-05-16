<?php

namespace App\Support;

/**
 * Read-only helpers for config/academic_subject_catalog.php.
 */
class AcademicSubjectCatalog
{
    /**
     * @return array<int, array<int, list<array{key: string, name_ar: string, name_en: string, credit_hours: int}>>>
     */
    public static function levels(): array
    {
        return config('academic_subject_catalog.levels', []);
    }

    /**
     * @return list<array{key: string, name_ar: string, name_en: string, credit_hours: int}>
     */
    public static function forLevelSemester(int $level1to4, int $semester1to2): array
    {
        $levels = self::levels();

        return $levels[$level1to4][$semester1to2] ?? [];
    }

    /**
     * @return list<string>
     */
    public static function allKeys(): array
    {
        $keys = [];
        foreach (self::levels() as $semesters) {
            foreach ($semesters as $subjects) {
                foreach ($subjects as $row) {
                    if (! empty($row['key'])) {
                        $keys[] = $row['key'];
                    }
                }
            }
        }

        return array_values(array_unique($keys));
    }

    public static function isValidKey(?string $key): bool
    {
        if ($key === null || $key === '') {
            return true;
        }

        return in_array($key, self::allKeys(), true);
    }

    /**
     * @return array{key: string, name_ar: string, name_en: string, credit_hours: int}|null
     */
    public static function findByKey(string $key): ?array
    {
        foreach (self::levels() as $semesters) {
            foreach ($semesters as $subjects) {
                foreach ($subjects as $row) {
                    if (($row['key'] ?? '') === $key) {
                        return $row;
                    }
                }
            }
        }

        return null;
    }
}
