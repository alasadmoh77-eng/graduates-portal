<?php

namespace App\Support;

/**
 * Localizes stored Arabic (or mixed) academic record strings for the English PDF only.
 */
class AcademicRecordEnglishPdf
{
    /** @var array<string, string> */
    private static array $levelNames = [
        'الأول' => 'First Level',
        'الاول' => 'First Level',
        'المستوى الأول' => 'First Level',
        'المستوى الاول' => 'First Level',
        'الثاني' => 'Second Level',
        'المستوى الثاني' => 'Second Level',
        'الثالث' => 'Third Level',
        'المستوى الثالث' => 'Third Level',
        'الرابع' => 'Fourth Level',
        'المستوى الرابع' => 'Fourth Level',
        'الخامس' => 'Fifth Level',
        'المستوى الخامس' => 'Fifth Level',
    ];

    /** @var array<string, string> */
    private static array $ratings = [
        'ممتاز' => 'Excellent',
        'جيد جداً' => 'Very Good',
        'جيد جدا' => 'Very Good',
        'جيد جدًا' => 'Very Good',
        'جيد' => 'Good',
        'مقبول' => 'Pass',
        'راسب' => 'Fail',
        '—' => '—',
        '-' => '—',
    ];

    /** @var array<string, string> */
    private static array $results = [
        'ناجح' => 'Pass',
        'نجاح' => 'Pass',
        'ناجحه' => 'Pass',
        'راسب' => 'Fail',
        'رسب' => 'Fail',
        'مكمل' => 'Conditional',
        '—' => '—',
    ];

    /** @var array<string, string> */
    private static array $honorsPhrases = [
        'مع مرتبة الشرف' => 'With Honours',
        'مع مرتبة الشرف الفخرية' => 'With Honours (Honoris Causa)',
        'مرتبة الشرف' => 'Honours',
        'بدون' => '—',
        'لا يوجد' => '—',
        '—' => '—',
    ];

    /** @var array<string, string> */
    private static array $degrees = [
        'بكالوريوس' => "Bachelor's Degree",
        'ماجستير' => "Master's Degree",
        'دكتوراه' => 'Doctorate',
    ];

    public static function levelName(string $storedName, int $zeroBasedIndex): string
    {
        $t = self::normalizeArabicSpaces(trim($storedName));
        if ($t === '') {
            return 'Level '.($zeroBasedIndex + 1);
        }
        if (isset(self::$levelNames[$t])) {
            return self::$levelNames[$t];
        }
        foreach (self::$levelNames as $ar => $en) {
            if (str_contains($t, $ar)) {
                return $en;
            }
        }

        return 'Level '.($zeroBasedIndex + 1);
    }

    public static function semesterName(int $sortOrder): string
    {
        return $sortOrder === 0 ? 'First Semester' : 'Second Semester';
    }

    public static function rating(?string $value): string
    {
        if ($value === null) {
            return '—';
        }
        $t = self::normalizeArabicSpaces(trim($value));
        if ($t === '' || $t === '—' || $t === '-') {
            return '—';
        }
        if (isset(self::$ratings[$t])) {
            return self::$ratings[$t];
        }

        return $t;
    }

    public static function result(?string $value): string
    {
        if ($value === null) {
            return '—';
        }
        $t = self::normalizeArabicSpaces(trim($value));
        if ($t === '' || $t === '—') {
            return '—';
        }
        if (isset(self::$results[$t])) {
            return self::$results[$t];
        }
        foreach (self::$results as $ar => $en) {
            if (str_contains($t, $ar)) {
                return $en;
            }
        }

        return $t;
    }

    public static function honors(?string $value): string
    {
        if ($value === null) {
            return '—';
        }
        $t = self::normalizeArabicSpaces(trim($value));
        if ($t === '' || $t === '—') {
            return '—';
        }
        if (isset(self::$honorsPhrases[$t])) {
            return self::$honorsPhrases[$t];
        }
        foreach (self::$honorsPhrases as $ar => $en) {
            if (str_contains($t, $ar)) {
                return $en;
            }
        }

        return $t;
    }

    public static function courseName(?string $catalogKey, ?string $storedName): string
    {
        $stored = trim((string) $storedName);
        if ($catalogKey !== null && $catalogKey !== '') {
            $row = AcademicSubjectCatalog::findByKey($catalogKey);
            if ($row !== null) {
                $en = trim((string) ($row['name_en'] ?? ''));
                if ($en !== '') {
                    return $en;
                }
            }
        }

        return $stored !== '' ? $stored : '—';
    }

    public static function degree(?string $degreeEn, ?string $degreeAr): string
    {
        $en = trim((string) $degreeEn);
        if ($en !== '') {
            return $en;
        }
        $ar = self::normalizeArabicSpaces(trim((string) $degreeAr));
        if ($ar !== '' && isset(self::$degrees[$ar])) {
            return self::$degrees[$ar];
        }
        foreach (self::$degrees as $a => $e) {
            if ($ar !== '' && str_contains($ar, $a)) {
                return $e;
            }
        }

        if ($ar !== '') {
            return '—';
        }

        return "Bachelor's Degree";
    }

    public static function majorName(?string $nameEn, ?string $nameAr): string
    {
        $en = trim((string) $nameEn);
        if ($en !== '') {
            return $en;
        }
        $ar = trim((string) $nameAr);

        return $ar !== '' ? $ar : '—';
    }

    private static function normalizeArabicSpaces(string $s): string
    {
        return preg_replace('/\s+/u', ' ', $s) ?? $s;
    }
}
