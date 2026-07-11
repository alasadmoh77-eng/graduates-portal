<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class RegisterGraduateRequest extends FormRequest
{
    public function authorize() { return true; }
    public function rules() {
        return [
            'email' => [
                'required',
                'email',
                'unique:users,email',
                function ($attribute, $value, $fail) {
                    $universityId = $this->input('university_id');
                    if ($universityId) {
                        $approved = \App\Models\ApprovedGraduate::where('university_id', $universityId)->first();
                        if ($approved && !empty($approved->email) && $approved->email !== $value) {
                            $fail(app()->getLocale() == 'ar'
                                ? 'البريد الإلكتروني المدخل لا يطابق البريد المعتمد لهذا الرقم الجامعي.'
                                : 'The input email does not match the approved email for this university ID.');
                        }
                    }
                }
            ],
            'password' => 'required|confirmed|min:8',
            'university_id' => 'required|regex:/^[0-9]+$/|unique:graduates,university_id|exists:approved_graduates,university_id',
            'phone' => 'nullable|regex:/^[0-9]+$/',
            'name' => 'nullable|string|max:255',
            'major_id' => 'nullable',
            'graduation_year' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'university_id.required' => 'الرقم الجامعي مطلوب.',
            'university_id.regex' => 'الرقم الأكاديمي يجب أن يحتوي على أرقام فقط.',
            'university_id.unique' => 'الرقم الجامعي مسجل بالفعل.',
            'university_id.exists' => 'الرقم الجامعي غير موجود في سجل الخريجين المعتمدين.',
            'phone.regex' => 'رقم الهاتف يجب أن يحتوي على أرقام فقط.',
        ];
    }
}
