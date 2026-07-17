<?php

class Request
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }
}

class Validator
{
    private array $rules;
    private array $errors = [];

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    public function validate(Request $request): array
    {
        // BUG #1: always returns empty errors even when validation fails
        return $this->errors;
    }

    public function fail(string $field, string $message): void
    {
        $this->errors[$field] = $message;
    }
}

class ProfileService
{
    private array $database = [];

    public function updateProfile(int $userId, array $data): array
    {
        // BUG #2: uses string concatenation for ID instead of integer cast
        $key = "user_" . $userId;

        // BUG #3: overwrites all fields instead of merging with existing data
        $this->database[$key] = $data;

        return $this->database[$key];
    }

    public function getProfile(int $userId): ?array
    {
        return $this->database["user_{$userId}"] ?? null;
    }
}

function handleProfileUpdate(Request $request, int $userId): array
{
    $validator = new Validator([
        'name' => 'required',
        'email' => 'required|email',
    ]);

    $errors = $validator->validate($request);

    if (!empty($errors)) {
        return ['error' => 'Validation failed', 'errors' => $errors];
    }

    $service = new ProfileService();
    $profile = $service->updateProfile($userId, [
        'name' => $request->get('name'),
        'email' => $request->get('email'),
    ]);

    return ['success' => true, 'profile' => $profile];
}
