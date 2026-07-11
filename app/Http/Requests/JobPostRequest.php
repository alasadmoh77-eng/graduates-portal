<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class JobPostRequest extends FormRequest
{
    public function authorize() { return true; }
    public function rules() {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'deadline' => 'required|date|after:today',
            'location' => 'nullable|string',
            'job_type' => 'required|string',
        ];
    }
}
