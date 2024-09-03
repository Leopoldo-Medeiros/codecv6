<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect('dashboard')->withSuccess('Signed in');
        }

        return redirect('login')->withErrors('Login details are not valid');
    }

    public function loginForm() {
        return view('login');
    }

    public function logOut() {
        Auth::logout();
        return redirect()->route('login');
    }
}
