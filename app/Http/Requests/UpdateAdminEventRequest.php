<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('seats') && $this->input('seats') === '') {
            $this->merge(['seats' => null]);
        }
    }

    public function rules(): array
    {
        return [
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'description_ar' => ['required', 'string'],
            'description_en' => ['required', 'string'],
            'start_at' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'seats' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'in:upcoming,completed,cancelled'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $event = $this->route('event');
            if (! $event || ! $this->filled('seats')) {
                return;
            }
            $min = $event->registrations()->count();
            if ((int) $this->input('seats') < $min) {
                $validator->errors()->add(
                    'seats',
                    __('app.event_seats_below_registrations', ['count' => $min])
                );
            }
        });
    }
}
