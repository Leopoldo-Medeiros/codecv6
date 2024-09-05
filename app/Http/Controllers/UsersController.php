<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::with('profile')->simplePaginate(5);
        return view('users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::with('profile')->findOrFail($id);
        return view('users.show', compact('user'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.form', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fullname' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'fullname' => $validated['fullname'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        if ($validated['role'] === 1) {
            if (Auth::user()->hasRole('admin')) {
                $role = Role::findById($validated['role']);
                $user->assignRole($role); // Assign the role to the user
            } else {
                return redirect()->route('users.index')->with('error', 'You cannot create an admin user.');
            }
        } else {
            $role = Role::findOrFail($validated['role']);
            $user->assignRole($role->name); // Assign the role to the user
        }

        // Debugging statement
        if ($user->hasRole($role->name)) {
            Log::info('Role assigned successfully.');
        } else {
            Log::error('Role assignment failed.');
        }

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('users.form', compact('user', 'roles'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'fullname' => 'required|max:255',
            'email' => 'required|email',
            'password' => 'nullable|min:8',
        ]);

        $user = User::findOrFail($id);

        $user->fullname = $validated['fullname'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if (!empty($validated['role'])) {
            if ($validated['role'] === 1) {
                if (Auth::user()->hasRole('admin')) {
                    $role = Role::findById($validated['role']);
                    $user->syncRoles($role->name);
                } else {
                    return redirect()->route('users.index')->with('error', 'You cannot create an admin user.');
                }
            }
        }

        $user->save();

        return redirect()->route('users.show', $user->id)->with('success', 'Profile updated successfully.');
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete yourself.');
        }

        if ($user->hasRole('admin')) {
            $adminCount = User::role('admin')->count();

            if ($adminCount <= 1) {
                return redirect()->route('users.index')->with('error', 'You cannot delete the only admin.');
            }
        }

        if ($user->profile) {
            $user->profile->delete();
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
