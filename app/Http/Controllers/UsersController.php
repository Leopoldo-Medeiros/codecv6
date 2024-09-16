<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::paginate(10); // Adjust the number as needed
        return view('users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('profile');
        return view('users.show', compact('user'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.form', compact('roles'));
    }

    public function store(UserRequest $request)
    {
        $user = User::create([
            'fullname' => $request->fullname,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $profileData = $request->only(['profile.birth_date', 'profile.profession']);
        if ($request->hasFile('profile_image')) {
            $profileData['profile_image'] = $request->file('profile_image')->store('profile_images', 'public');
        }

        $user->profile()->create($profileData);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function update(UserRequest $request, $id)
    {
        $user = User::findOrFail($id); // Ensure $user is an object
        $user->update([
            'fullname' => $request->fullname,
            'email' => $request->email,
        ]);

        $profileData = $request->only(['profile.birth_date', 'profile.profession']);
        if ($request->hasFile('profile_image')) {
            $profileData['profile_image'] = $request->file('profile_image')->store('profile_images', 'public');
        }

        // Update individual profile fields
        $user->profile()->update([
            'birth_date' => $profileData['profile.birth_date'] ?? null,
            'profession' => $profileData['profile.profession'] ?? null,
            'profile_image' => $profileData['profile_image'] ?? $user->profile->profile_image,
        ]);

        return redirect()->route('users.show', $user->id)->with('success', 'User updated successfully.');
    }
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all(); // Assuming you have roles to pass
        return view('users.form', compact('user', 'roles'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('profile', compact('user'));
    }

    public function destroy(User $user)
    {
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

    public function updateRole(User $user, $validated)
    {
        $role = Role::findById($validated['role']);

        if ($validated['role'] === 1) {
            if (Auth::user()->hasRole('admin')) {
                $user->assignRole($role); // Assign the role to the user
            } else {
                return redirect()->route('users.index')->with('error', 'You cannot create an admin user.');
            }
        } else {
            $user->syncRoles($role->name); // Sync roles to remove old role and add only the last one
        }

        // Debugging statement
        if ($user->hasRole($role->name)) {
            Log::info('Role assigned successfully.');
        } else {
            Log::error('Role assignment failed.');
        }

        return true;
    }

    private function updateCreateProfile(User $user, $validated)
    {
        // Update or create profile
        $user->profile()->updateOrCreate(
            [
                'user_id' => $user->id
            ],
            $validated
        );
    }
}
