<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PlanRequest extends FormRequest
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
            'price' => 'nullable|numeric|min:0',
            'consultant_id' => 'nullable|exists:users,id',
            'client_ids' => 'nullable|array',
            'client_ids.*' => 'exists:users,id',
            'path_ids' => 'nullable|array',
            'path_ids.*' => 'exists:paths,id',
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
