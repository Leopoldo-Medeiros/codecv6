<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create(array $data)
    {
        return User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('user.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('user.edit', compact('user'));
    }

    public function update($request, $id)
    {
        $validateData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $user = User::find($id);
        $user->update($request->all());
        return redirect()->route('user.show', $user);
    }

    public function delete($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('user.index');
    }

    public function index()
    {
        $users = User::all();
        return view('user.index', compact('users'));
    }

}
