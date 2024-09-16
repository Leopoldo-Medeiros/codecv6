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
        $userId = $this->route('user'); // Get the User ID from the route

        return [
            'fullname' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'password' => 'nullable|min:6|confirmed',
            'profile.birth_date' => 'nullable|date',
            'profile.profession' => 'nullable|string',
            'role' => 'required|exists:roles,id',
        ];
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
