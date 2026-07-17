<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WaitlistRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Public endpoint — anyone may join a waitlist.
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Guests must give an email; a logged-in vote uses the account's email.
            'email' => [$this->user() ? 'nullable' : 'required', 'email:rfc', 'max:255'],
            // Only accept known "coming soon" tracks (config/waitlist.php).
            'topic' => ['required', 'string', Rule::in(array_keys(config('waitlist.topics')))],
            'source' => ['nullable', 'string', 'max:100'],
        ];
    }
}
