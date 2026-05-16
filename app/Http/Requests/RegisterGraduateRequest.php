<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class RegisterGraduateRequest extends FormRequest
{
    public function authorize() { return true; }
    public function rules() {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
            'university_id' => 'required|string|unique:graduates,university_id',
            'phone' => 'nullable|string',
            'major_id' => 'required|exists:majors,id',
            'graduation_year' => 'required|digits:4',
        ];
    }
}
