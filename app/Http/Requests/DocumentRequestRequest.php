<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class DocumentRequestRequest extends FormRequest
{
    public function authorize() { return true; }
    public function rules() {
        return [
            'document_type_id' => 'required|exists:document_types,id',
            'language' => 'required|in:ar,en',
            'purpose' => 'nullable|string|max:255',
            'delivery_type' => 'required|in:digital,physical',
        ];
    }
}
