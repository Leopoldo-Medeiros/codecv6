<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class JobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'company' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'salary' => 'nullable|string|max:255',
            'consultant_id' => 'nullable|exists:users,id',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['title'] = 'sometimes|required|string|max:255';
            $rules['company'] = 'sometimes|required|string|max:255';
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        // Only default consultant_id on create — on update, an absent
        // consultant_id must leave the existing owner untouched rather than
        // silently reassigning the job to whoever is editing it.
        if ($this->isMethod('POST') && ! $this->has('consultant_id')) {
            $this->merge([
                'consultant_id' => Auth::id(),
            ]);
        }
    }
}
