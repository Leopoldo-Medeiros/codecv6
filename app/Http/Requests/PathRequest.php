<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\AssignsConsultant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PathRequest extends FormRequest
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
        $this->normaliseConsultantId();
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(fn ($v) => $this->validateConsultantOwner($v));
    }
}
