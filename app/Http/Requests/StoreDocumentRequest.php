<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'document_type_id' => [
                'required', 
                \Illuminate\Validation\Rule::exists('document_types', 'id')
                    ->whereIn('code', ['ACADEMIC_RECORD', 'GRADES_CERTIFICATE'])
            ],
            'language' => 'required|in:AR,EN',
            'purpose' => 'required|string|max:255',
            'delivery_type' => 'required|in:DIGITAL_PDF,PICKUP',
        ];
    }

    public function messages(): array
    {
        return [
            'document_type_id.required' => 'يرجى اختيار نوع المستند.',
            'document_type_id.exists' => 'نوع المستند المختار غير صالح.',
            'language.required' => 'يرجى اختيار اللغة.',
            'language.in' => 'اللغة المختارة غير صالحة.',
            'purpose.required' => 'يرجى إدخال الغرض من الطلب.',
            'purpose.max' => 'يجب ألا يتجاوز الغرض 255 حرفاً.',
            'delivery_type.required' => 'يرجى اختيار طريقة التسليم.',
            'delivery_type.in' => 'طريقة التسليم المختارة غير صالحة.',
        ];
    }
}
