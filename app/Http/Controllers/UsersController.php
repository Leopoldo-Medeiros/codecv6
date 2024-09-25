<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user(); // Assuming you are using Laravel's Auth system
        $search = $request->input('search');
        $query = User::query();

        if ($search) {
            $query->where('fullname', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }

        $users = $query->simplePaginate(10); // Adjust the number of items per page as needed

        return view('users.index', compact('user', 'users'));
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
        $validated = $request->validated();
        $user = User::create($validated);

        // Update individual profile fields
        $this->updateCreateProfile($user, $request);

        // update roles
        $this->updateRole($user, $validated);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $user = $user->load('profile');
        $roles = Role::all(); // Assuming you have roles to pass
        return view('users.form', compact('user', 'roles'));
    }

    public function update(UserRequest $request, User $user)
    {
        $validated = $request->validated();
        $user->update($validated);

        // Update individual profile fields
        $this->updateCreateProfile($user, $request);

        // update roles
        $this->updateRole($user, $validated);

        return redirect()->route('users.show', $user->id)->with('success', 'User updated successfully.');
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

    public function profile()
    {
        $user = Auth::user();
        return view('profile', compact('user'));
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

    private function updateCreateProfile(User $user, UserRequest $request)
    {
        $validated = $request->validated();
        if ($request->hasFile('profile_image')) {
            if($user->profile && $user->profile->profile_image) {
                if(Storage::exists($user->profile->profile_image)) {
                    Storage::delete($user->profile->profile_image);
                }
            }
            $validated['profile']['profile_image'] = $request->file('profile_image')->store('profile_images', config('filesystems.default'));
        }

        // Update or create profile
        $user->profile()->updateOrCreate(
            [
                'user_id' => $user->id
            ],
            $validated['profile']
        );
    }
}
