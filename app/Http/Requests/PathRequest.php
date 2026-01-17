<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PathRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'consultant_id' => 'nullable|exists:users,id',
            'plan_ids' => 'nullable|array',
            'plan_ids.*' => 'exists:plans,id',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['name'] = 'sometimes|required|string|max:255';
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('consultant_id')) {
            $this->merge([
                'consultant_id' => Auth::id(),
            ]);
        }
    }
}
