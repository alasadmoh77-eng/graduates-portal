<?php

namespace App\Support;

/**
 * Read-only helpers for config/academic_subject_catalog.php.
 */
class AcademicSubjectCatalog
{
    /**
     * Get major slug from major name
     */
    public static function getMajorSlug(?string $nameEn): string
    {
        if (!$nameEn) {
            return 'computer_science';
        }
        $slug = strtolower(trim($nameEn));
        $slug = str_replace([' ', '-'], '_', $slug);
        
        // Map similar majors to fallback catalogs
        if (in_array($slug, ['software_engineering', 'information_systems'])) {
            return 'computer_science';
        }
        
        $majors = config('academic_subject_catalog.majors', []);
        if (isset($majors[$slug])) {
            return $slug;
        }
        
        return 'computer_science'; // Default fallback
    }

    /**
     * @return array
     */
    public static function levels(?string $majorSlug = null): array
    {
        $majors = config('academic_subject_catalog.majors', []);
        $slug = self::getMajorSlug($majorSlug);
        return $majors[$slug]['levels'] ?? [];
    }

    /**
     * @return array
     */
    public static function forLevelSemester(int $level1to4, int $semester1to2, ?string $majorSlug = null): array
    {
        $levels = self::levels($majorSlug);
        return $levels[$level1to4][$semester1to2] ?? [];
    }

    /**
     * @return list<string>
     */
    public static function allKeys(): array
    {
        $keys = [];
        $majors = config('academic_subject_catalog.majors', []);
        foreach ($majors as $mSlug => $mConfig) {
            $levels = $mConfig['levels'] ?? [];
            foreach ($levels as $semesters) {
                foreach ($semesters as $subjects) {
                    foreach ($subjects as $row) {
                        if (! empty($row['key'])) {
                            $keys[] = $row['key'];
                        }
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
     * @return array|null
     */
    public static function findByKey(string $key): ?array
    {
        $majors = config('academic_subject_catalog.majors', []);
        foreach ($majors as $mSlug => $mConfig) {
            $levels = $mConfig['levels'] ?? [];
            foreach ($levels as $semesters) {
                foreach ($semesters as $subjects) {
                    foreach ($subjects as $row) {
                        if (($row['key'] ?? '') === $key) {
                            return $row;
                        }
                    }
                }
            }
        }

        return null;
    }
}
