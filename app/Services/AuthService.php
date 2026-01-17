<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Exceptions\AuthenticationException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login(array $credentials): array
    {
        if (! Auth::attempt($credentials)) {
            throw new AuthenticationException('Invalid credentials');
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $this->formatUserResponse($user),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    public function register(array $data): array
    {
        $user = User::create([
            'fullname' => $data['fullname'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $role = $data['role'] ?? RoleEnum::CLIENT->value;
        $user->assignRole($role);

        if (isset($data['profile'])) {
            $user->profile()->create($data['profile']);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $this->formatUserResponse($user),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function refresh(User $user): array
    {
        $user->currentAccessToken()->delete();
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $this->formatUserResponse($user),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    private function formatUserResponse(User $user): array
    {
        $user->load('roles', 'profile');

        $userData = $user->toArray();
        $userData['role'] = $user->roles->first()?->name;

        return $userData;
    }
}
