<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        $frontendUrl = rtrim(config('app.frontend_url', 'http://localhost:3000'), '/');

        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect("{$frontendUrl}/login?error=google_auth_failed");
        }

        $user = User::where('google_id', $googleUser->getId())->first()
            ?? User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            if (! $user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }
        } else {
            $user = User::create([
                'fullname'          => $googleUser->getName() ?? $googleUser->getEmail(),
                'email'             => $googleUser->getEmail(),
                'google_id'         => $googleUser->getId(),
                'password'          => Str::random(32),
                'email_verified_at' => now(),
            ]);
            $user->assignRole('client');
            $user->notify(new WelcomeNotification());
        }

        $token = $user->createToken('google-auth')->plainTextToken;
        $user->load('profile', 'roles');

        $userData = urlencode(json_encode((new UserResource($user))->resolve()));

        return redirect("{$frontendUrl}/auth/callback?token={$token}&user={$userData}");
    }
}
