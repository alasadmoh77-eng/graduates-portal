<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

class ApplyJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cover_letter' => ['nullable', 'string', 'max:10000'],
            'cv_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->has('cv_file')) {
                return;
            }

            if ($this->hasFile('cv_file')) {
                return;
            }

            $path = $this->user()->graduate?->cvRelativePath();
            if (! $path || ! Storage::disk('public')->exists($path)) {
                $validator->errors()->add('cv_file', __('app.cv_required_for_application'));
            }
        });
    }
}
