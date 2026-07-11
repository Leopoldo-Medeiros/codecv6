<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\AssignsConsultant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PlanRequest extends FormRequest
{
    use AssignsConsultant;

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
        $this->normaliseConsultantId();
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(fn ($v) => $this->validateConsultantOwner($v));
    }
}
