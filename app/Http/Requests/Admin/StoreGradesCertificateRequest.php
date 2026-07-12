<?php

namespace App\Http\Requests\Admin;

use App\Support\AcademicSubjectCatalog;
use Illuminate\Foundation\Http\FormRequest;

class StoreGradesCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['admin', 'super_admin', 'academic_admin']);
    }

    protected function prepareForValidation(): void
    {
        $data = $this->all();

        if (isset($data['student']['total']) && $data['student']['total'] === '') {
            $data['student']['total'] = null;
        }

        if (isset($data['levels']) && is_array($data['levels'])) {
            foreach ($data['levels'] as $lIdx => &$level) {
                if (isset($level['avg']) && $level['avg'] === '') {
                    $level['avg'] = null;
                }
                if (isset($level['semesters']) && is_array($level['semesters'])) {
                    foreach ($level['semesters'] as $sIdx => &$sem) {
                        if (isset($sem['subjects']) && is_array($sem['subjects'])) {
                            foreach ($sem['subjects'] as $subIdx => &$sub) {
                                if (isset($sub['hours']) && $sub['hours'] === '') {
                                    $sub['hours'] = null;
                                }
                                if (isset($sub['score']) && $sub['score'] === '') {
                                    $sub['score'] = null;
                                }
                            }
                            unset($sub);
                        }
                    }
                    unset($sem);
                }
            }
            unset($level);
        }

        $this->merge($data);
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
            'student.total' => ['nullable', 'numeric'],
            'student.gpa' => ['nullable', 'string', 'max:32'],
            'student.rating' => ['nullable', 'string', 'max:64'],
            'student.honors' => ['nullable', 'string', 'max:128'],
            'student.gradYear' => ['nullable', 'string', 'max:64'],
            'student.enrollmentYear' => ['nullable', 'string', 'max:64'],
            'student.dora' => ['nullable', 'string', 'max:64'],

            'levels' => ['required', 'array', 'min:1'],
            'levels.*.name' => ['required', 'string', 'max:64'],
            'levels.*.year' => ['nullable', 'string', 'max:64'],
            'levels.*.avg' => ['nullable', 'numeric'],
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
            'levels.*.semesters.*.subjects.*.hours' => ['nullable', 'numeric', 'min:0', 'max:30'],
            'levels.*.semesters.*.subjects.*.score' => ['nullable', 'numeric', 'between:0,100'],
            'levels.*.semesters.*.subjects.*.rating' => ['nullable', 'string', 'max:64'],
        ];
    }

    public function messages(): array
    {
        return [
            'student.total.numeric' => 'مجموع النقاط يجب أن يكون رقماً.',
            'levels.*.avg.numeric' => 'معدل المستوى يجب أن يكون رقماً.',
            'levels.*.semesters.*.subjects.*.hours.numeric' => 'ساعات المادة يجب أن تكون رقماً.',
            'levels.*.semesters.*.subjects.*.hours.min' => 'ساعات المادة لا يمكن أن تكون أقل من 0.',
            'levels.*.semesters.*.subjects.*.hours.max' => 'ساعات المادة لا يمكن أن تتجاوز 30.',
            'levels.*.semesters.*.subjects.*.score.numeric' => 'درجة المادة يجب أن تكون رقماً.',
            'levels.*.semesters.*.subjects.*.score.min' => 'درجة المادة لا يمكن أن تكون أقل من 0.',
            'levels.*.semesters.*.subjects.*.score.max' => 'درجة المادة لا يمكن أن تتجاوز 100.',
            'levels.*.semesters.*.subjects.*.score.between' => 'يجب أن تكون درجة المقرر بين 0 و100.',
        ];
    }
}
