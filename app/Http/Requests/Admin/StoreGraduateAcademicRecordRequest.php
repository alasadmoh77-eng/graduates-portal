<?php

namespace App\Http\Requests\Admin;

use App\Support\AcademicSubjectCatalog;
use Illuminate\Foundation\Http\FormRequest;

class StoreGraduateAcademicRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'student' => ['required', 'array'],
            'student.name' => ['nullable', 'string', 'max:255'],
            'student.name_en' => ['nullable', 'string', 'max:255'],
            'student.id' => ['nullable', 'string', 'max:64'],
            'student.degree' => ['nullable', 'string', 'max:128'],
            'student.degree_en' => ['nullable', 'string', 'max:128'],
            'student.total' => ['nullable', 'string', 'max:32'],
            'student.gpa' => ['nullable', 'string', 'max:32'],
            'student.rating' => ['nullable', 'string', 'max:64'],
            'student.honors' => ['nullable', 'string', 'max:128'],
            'student.gradYear' => ['nullable', 'string', 'max:64'],
            'student.enrollmentYear' => ['nullable', 'string', 'max:64'],
            'student.dora' => ['nullable', 'string', 'max:64'],

            'levels' => ['required', 'array', 'min:1'],
            'levels.*.name' => ['required', 'string', 'max:64'],
            'levels.*.year' => ['nullable', 'string', 'max:64'],
            'levels.*.avg' => ['nullable', 'string', 'max:32'],
            'levels.*.totalPoints' => ['nullable', 'string', 'max:32'],
            'levels.*.result' => ['nullable', 'string', 'max:64'],
            'levels.*.semesters' => ['required', 'array', 'size:2'],
            'levels.*.semesters.*.subjects' => ['present', 'array'],
            'levels.*.semesters.*.subjects.*.catalog_key' => [
                'nullable',
                'string',
                'max:64',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value === null || $value === '') {
                        return;
                    }
                    if (! AcademicSubjectCatalog::isValidKey($value)) {
                        $fail(__('validation.in', ['attribute' => $attribute]));
                    }
                },
            ],
            'levels.*.semesters.*.subjects.*.name' => ['nullable', 'string', 'max:512'],
            'levels.*.semesters.*.subjects.*.hours' => ['nullable', 'string', 'max:16'],
            'levels.*.semesters.*.subjects.*.score' => ['nullable', 'string', 'max:32'],
            'levels.*.semesters.*.subjects.*.rating' => ['nullable', 'string', 'max:64'],
        ];
    }
}
