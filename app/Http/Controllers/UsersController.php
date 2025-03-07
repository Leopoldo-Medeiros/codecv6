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
    public function index(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $user = Auth::user(); // Assuming you are using Laravel's Auth system
        $search = $request->input('search');
        $query = User::query();

        if ($search) {
            $query->where('fullname', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }

        $users = $query->paginate(10); // Adjust the number of items per page as needed

        return view('users.index', compact('user', 'users'));
    }

    public function show(User $user): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $user = $user->load('profile');
        return view('users.show', compact('user'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.form', compact('roles'));
    }

    public function store(UserRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();
        $user = User::create($validated);

        // Update individual profile fields
        $this->updateCreateProfile($user, $request);

        // update roles
        $this->updateRole($user, $validated);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $user = $user->load('profile');
        $roles = Role::all(); // Assuming you have roles to pass
        return view('users.form', compact('user', 'roles'));
    }

    public function update(UserRequest $request, User $user): \Illuminate\Http\RedirectResponse
    {
        \Log::info('Updating user: ' . $user->id);
        \Log::info('Request data: ' . json_encode($request->all()));

        $validated = $request->validated();
        $user->update($validated);

        $this->updateCreateProfile($user, $request);

        $this->updateRole($user, $validated);

        return redirect()->route('users.show', $user->id)->with('success', 'User updated successfully.');
    }
    public function destroy(User $user): \Illuminate\Http\RedirectResponse
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

    public function profile(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        $user = auth()->user()->load('profile');
        return view('profile', compact('user'));
    }

    public function updateRole(User $user, $validated): true|\Illuminate\Http\RedirectResponse
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

    private function updateCreateProfile(User $user, UserRequest $request): void
    {
        \Log::info('Updating profile for user: ' . $user->id);
        $validated = $request->validated();

        // Create profile data array
        $profileData = $validated['profile'] ?? [];

        if ($request->hasFile('profile_image')) {
            \Log::info('Profile image file detected');
            $file = $request->file('profile_image');

            // Log file details
            \Log::info('File details: ' . json_encode([
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType()
                ]));

            if ($user->profile && $user->profile->profile_image) {
                \Log::info('Deleting old profile image: ' . $user->profile->profile_image);
                try {
                    Storage::disk('public')->delete($user->profile->profile_image);
                } catch (\Exception $e) {
                    \Log::error('Error deleting old profile image: ' . $e->getMessage());
                }
            }

            try {
                $path = $file->store('profile_images', 'public');
                \Log::info('New profile image stored at: ' . $path);
                
                // Verify the file was actually stored
                if (Storage::disk('public')->exists($path)) {
                    \Log::info('File exists at path: ' . $path);
                    \Log::info('Full URL would be: ' . Storage::disk('public')->url($path));
                    $profileData['profile_image'] = $path;
                } else {
                    \Log::error('File does not exist at expected path: ' . $path);
                }
            } catch (\Exception $e) {
                \Log::error('Error storing profile image: ' . $e->getMessage());
            }
        } else {
            \Log::info('No profile image file in request');
        }

        $profile = $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        \Log::info('Profile updated: ' . json_encode($profile));
    }



}
