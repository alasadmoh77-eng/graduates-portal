<?php

namespace App\Imports;

use App\Models\ApprovedGraduate;
use App\Models\Major;
use App\Models\Faculty;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ApprovedGraduatesImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $universityId = $this->resolveKey($row, ['university_id', 'الرقم_الجامعي', 'الرقم الجامعي']);
        $majorName    = $this->resolveKey($row, ['major', 'التخصص', 'تخصص', 'major_name', 'اسم التخصص']);
        $collegeName  = $this->resolveCollege($row);
        $studentName  = $this->resolveKey($row, ['name', 'الاسم', 'اسم الطالب', 'student_name']);
        $email        = $this->resolveKey($row, ['email', 'البريد_الإلكتروني', 'البريد الإلكتروني', 'ايميل']);
        $gradYear     = $this->resolveKey($row, ['graduation_year', 'سنة_التخرج', 'سنة التخرج', 'grad_year']);

        if (!$universityId || !$majorName) {
            return null;
        }

        // Strict & deterministic major matching and dynamic creation
        $major = Major::where('name_ar', $majorName)
            ->orWhere('name_en', $majorName)
            ->first();

        if (!$major) {
            $facultyId = null;
            if ($collegeName) {
                $faculty = Faculty::firstOrCreate([
                    'name_ar' => $collegeName
                ], [
                    'name_en' => $collegeName,
                    'status' => 'active'
                ]);
                $facultyId = $faculty->id;
            }

            Major::create([
                'name_ar' => $majorName,
                'name_en' => $majorName,
                'faculty_id' => $facultyId,
                'degree_name_ar' => null,
                'degree_name_en' => null,
            ]);
        }

        // Sync with updateOrCreate behavior
        $approved = ApprovedGraduate::where('university_id', $universityId)->first();
        if ($approved) {
            $approved->fill([
                'name' => $studentName ?: trim($row['name'] ?? ''),
                'email' => $email ?: ($row['email'] ?? null),
                'college' => $collegeName,
                'major' => $majorName,
                'graduation_year' => intval($gradYear ?: ($row['graduation_year'] ?? 0)),
            ]);
            return $approved;
        }

        return new ApprovedGraduate([
            'university_id' => $universityId,
            'name' => $studentName ?: trim($row['name'] ?? ''),
            'email' => $email ?: ($row['email'] ?? null),
            'college' => $collegeName,
            'major' => $majorName,
            'graduation_year' => intval($gradYear ?: ($row['graduation_year'] ?? 0)),
        ]);
    }

    /**
     * Resolve a row key from multiple possible column header names.
     */
    private function resolveKey(array $row, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (isset($row[$key]) && $row[$key] !== null && trim((string) $row[$key]) !== '') {
                return trim((string) $row[$key]);
            }
        }
        return null;
    }

    /**
     * Resolve the college/faculty column from the row.
     */
    private function resolveCollege(array $row): ?string
    {
        $keys = [
            'college', 'faculty', 'college_name', 'faculty_name',
            'كلية', 'الكلية', 'اسم الكلية', 'اسم_الكلية',
            'college_ar', 'faculty_ar',
        ];

        $value = $this->resolveKey($row, $keys);
        if ($value !== null) {
            return $this->normalizeText($value);
        }

        return null;
    }

    /**
     * Normalize Arabic/English text: trim, remove excessive spaces.
     */
    private function normalizeText(string $value): string
    {
        return preg_replace('/\s+/u', ' ', trim($value));
    }

    /**
     * Normalize row keys before validation so Arabic/alternative headers
     * are mapped to the standard English keys expected by rules().
     */
    public function prepareForValidation(array $data): array
    {
        $keyMap = [
            'university_id' => ['university_id', 'الرقم_الجامعي', 'الرقم الجامعي', 'الرقم', 'student_id', 'academic_id'],
            'name'          => ['name', 'الاسم', 'اسم الطالب', 'student_name', 'الاسم_الكامل'],
            'email'         => ['email', 'البريد_الإلكتروني', 'البريد الإلكتروني', 'ايميل', 'البريد'],
            'college'       => ['college', 'faculty', 'college_name', 'faculty_name', 'كلية', 'الكلية', 'اسم الكلية', 'اسم_الكلية', 'college_ar', 'faculty_ar'],
            'major'         => ['major', 'التخصص', 'تخصص', 'major_name', 'اسم التخصص', 'اسم_التخصص'],
            'graduation_year' => ['graduation_year', 'سنة_التخرج', 'سنة التخرج', 'grad_year', 'عام_التخرج'],
        ];

        $normalized = [];
        foreach ($keyMap as $standardKey => $aliases) {
            foreach ($aliases as $alias) {
                if (array_key_exists($alias, $data) && $data[$alias] !== null) {
                    $normalized[$standardKey] = $data[$alias];
                    break;
                }
            }
        }

        // Preserve any remaining unmapped columns
        foreach ($data as $key => $value) {
            if (!array_key_exists($key, $normalized) && !in_array($key, ['college', 'university_id', 'name', 'email', 'major', 'graduation_year'])) {
                $normalized[$key] = $value;
            }
        }

        return $normalized;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'university_id'  => 'required',
            'name'           => 'required|string|max:255',
            'email'          => 'nullable|email',
            'college'        => 'nullable|string|max:255',
            'major'          => 'required|string|max:255',
            'graduation_year' => 'required|integer',
        ];
    }
}
