<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        // Always return success to prevent email enumeration
        if (! $user) {
            return response()->json(['message' => 'If that email exists, a reset link has been sent.']);
        }

        $token = Password::broker()->createToken($user);
        $user->notify(new ResetPasswordNotification($token));

        return response()->json(['message' => 'If that email exists, a reset link has been sent.']);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])
                    ->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'message' => match ($status) {
                    Password::INVALID_TOKEN => 'This reset link is invalid or has expired.',
                    Password::INVALID_USER => 'No account found with that email.',
                    default => 'Could not reset password. Please try again.',
                },
            ], 422);
        }

        return response()->json(['message' => 'Password reset successfully. You can now sign in.']);
    }
}
