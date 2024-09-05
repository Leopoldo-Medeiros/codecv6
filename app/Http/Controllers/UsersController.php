<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('profile')->simplePaginate(5);
        return view('users.index', compact('users'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $user = $user->load('profile');
        return view('users.show', compact('user'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.form', compact('roles'));
    }

    public function store(UserRequest $request)
    {
        $validated = $request->validated();
        $user = User::create($validated);
        $this->updateCreateProfile($user, $validated['profile']);

        if (!empty($validated['role'])) {
            $this->updateRole($user, $request->validated());
        }

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $user = $user->load('profile');
        $roles = Role::all();
        return view('users.form', compact('user', 'roles'));
    }

    public function update(UserRequest $request, User $user)
    {
        $validated = $request->validated();
        $user->update($validated);
        $this->updateCreateProfile($user, $validated['profile']);

        if (!empty($validated['role'])) {
            $this->updateRole($user, $request->validated());
        }

        return redirect()->route('users.show', $user->id)->with('success', 'Profile updated successfully.');
    }

    /**
     * Remove user from the system
     */
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
