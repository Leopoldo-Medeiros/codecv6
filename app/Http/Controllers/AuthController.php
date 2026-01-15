<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth-token')->plainTextToken;

            // Load user's roles
            $user->load('roles');

            // Add the primary role name to the user object
            $userData = $user->toArray();
            $userData['role'] = $user->roles->first()?->name;

            return response()->json([
                'user' => $userData,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function logOut() {
        Auth::logout();
        return redirect()->route('login');
    }
}
