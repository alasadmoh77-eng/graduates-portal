<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'document_type_id' => [
                'required',
                Rule::exists('document_types', 'id')
                    ->whereIn('code', ['ACADEMIC_RECORD', 'GRADES_CERTIFICATE'])
            ],
            'language' => 'required|in:AR,EN',
            'purpose' => 'required|string|max:255',
            'delivery_type' => 'required|in:DIGITAL_PDF,PICKUP',
        ];

        $docType = null;
        if ($this->has('document_type_id')) {
            $docType = \App\Models\DocumentType::find($this->document_type_id);
        }

        if ($docType && $docType->payment_required) {
            $rules['payment_proof'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:5120';
        }

        return $rules;
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
            'payment_proof.required' => 'يرجى رفع إثبات الدفع.',
            'payment_proof.file' => 'يرجى رفع ملف صحيح.',
            'payment_proof.mimes' => 'صيغ الملفات المسموحة: jpg, jpeg, png, pdf.',
            'payment_proof.max' => 'الحد الأقصى لحجم الملف 5 ميجابايت.',
        ];
    }
}
