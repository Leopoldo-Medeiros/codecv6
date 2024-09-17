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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'fullname' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'profile'  => 'required|array',
            'profile.birth_date' => 'nullable|date',
            'profile.profession' => 'nullable|string',
            'profile.profile_image' => 'nullable|file|size:2048|mimes:jpeg,jpg,png',
            'role' => 'required|exists:roles,id',
        ];

        $user = 0;
        if (!empty($this->route('user'))) {
            $user = $this->route('user')->id;
        }

        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST':
            {
                $rules = [
                    ...$rules,
                ];
            }
            case 'PATCH':
            case 'PUT':
            {
                $rules = [
                    ...$rules,
                    'email' => 'required|email|unique:users,email,'. $user,
                    'password' => 'nullable|min:6|confirmed',
                ];
            }
            default:break;
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        if (is_null($this->password)) {
            $this->request->remove('password');
        }
    }

    // After validation
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated();
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }
        return $validated;
    }
}
