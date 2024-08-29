<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function customLogin(Request $request)
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

    public function showLoginForm() {
        return view('login');
    }

    public function dashboard()
    {
        if (Auth::check()) {
            $users = \App\Models\User::with('profile')->get();
            $totalUsers = $users->count(); // Contando o total de usuários
            $totalAdmins = \App\Models\User::where('role', 'admin')->count(); // Contando o total de administradores

            return view('admin.dashboard', compact('users', 'totalUsers', 'totalAdmins')); // Passando a variável totalAdmins
        }
        return redirect('login')->withErrors('You are not allowed to access');
    }

    public function signOut() {
        Auth::logout();
        return redirect()->route('login');
    }
}
