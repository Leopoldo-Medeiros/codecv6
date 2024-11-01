<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // It verifies if the user has permission to make the request
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|max:255',
            'slug' => 'required|string|max:255|unique:courses,slug', //If the 'url' is not unique, the validation will fail
            'description' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
        ];

        // It verifies the HTTP method and adjusts the rules
        switch($this->method())
        {
            case 'POST':
                return $rules;
            case 'PUT':
            case 'PATCH':
                // In case of PUT/PATCH, we can ignore the current course
                $rules['slug'] = 'required|string|max:255|unique:courses,slug,' . $this->route('course')->id;
                return $rules;
            case 'GET':
                case 'DELETE':
                    return []; // No rules for GET/DELETE requests
            default:
                return [];
        }
    }

    /**
     * Prepare the data for validation.
     *
     * Notes for myself:
     *
     * The prepareForValidation method in Laravel form requests is used to modify or add data to the request before
     * the validation rules are applied.
     * This method allows you to manipulate the request data,
     * such as merging additional fields or transforming existing fields,
     * ensuring that the data is in the correct format for validation.
     */
    protected function prepareForValidation()
    {
        // Add the user_id of the authenticated user to the request data
        $this->merge([
            'user_id' => Auth::id(),
        ]);
    }
}
