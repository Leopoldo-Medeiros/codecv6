<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'fullname' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'profile' => 'required|array',
            'profile.birth_date' => 'nullable|date',
            'profile.profession' => 'nullable|string',
            'profile.profile_image' => 'nullable|file|max:2048|mimes:jpeg,jpg,png',
            'role' => 'required|exists:roles,id',
        ];

        $userId = $this->route('user')?->id ?? 0;

        switch ($this->method()) {
            case 'PATCH':
            case 'PUT':
                $rules['email'] = 'required|email|unique:users,email,' . $userId;
                $rules['password'] = 'nullable|min:6|confirmed';
                break;

            case 'POST':
                // Nada adicional para POST
                break;
        }

        return $rules;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        if (is_null($this->password)) {
            $this->request->remove('password');
        }

        if (!is_array($this->profile)) {
            $this->merge(['profile' => []]);
        }
    }

    /**
     * After validation, modify the data.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated();

        // Hash da senha, se fornecida
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        // Garante que o perfil esteja presente como array
        $validated['profile'] = $validated['profile'] ?? [];

        return $validated;
    }
}
