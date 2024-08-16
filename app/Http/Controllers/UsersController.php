<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    // Other methods...

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('profile')->get();
        return view('users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::with('profile')->findOrFail($id);
        return view('users.show', compact('user'));
    }
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
            'password' => 'nullable|min:8',
            'birth_date' => 'nullable|date',
            'role' => 'nullable|string|max:255',
        ]);

        $user = User::findOrFail($id);

        // Update user fields
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = \Hash::make($validated['password']);
        }

        // Update profile fields if any
        if ($user->profile) {
            $user->profile->birth_date = $validated['birth_date'] ?? '';
            $user->profile->role = $validated['role'] ??  '';
            $user->profile->save();
        } else {
            // Create profile if it doesn't exist
            $user->profile()->create([
                'birth_date' => $validated['birth_date'] ?? '1970-01-01', // Default value
                'role' => $validated['role'] ?? '',
            ]);
        }

        $user->save();

        return redirect()->route('users.show', $user->id)->with('success', 'Profile updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
