<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function index(Request $request): View|Factory|Application
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

    public function show(int $id): View|Factory|Application
    {
        if ($id !== Auth::id() && ! Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $user = User::with('profile')->findOrFail($id);

        return view('users.show', compact('user'));
    }

    public function create()
    {
        $roles = Role::all();

        return view('users.form', compact('roles'));
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = User::create($validated);

        // Update individual profile fields
        $this->updateCreateProfile($user, $request);

        // update roles
        $this->updateRole($user, $validated);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user): View|Factory|Application
    {
        $user = $user->load('profile');
        $roles = Role::all(); // Assuming you have roles to pass

        return view('users.form', compact('user', 'roles'));
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();
        $user->update($validated);

        $this->updateCreateProfile($user, $request);

        $this->updateRole($user, $validated);

        return redirect()->route('users.show', $user->id)->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
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

    public function profile(): View|Factory|Application
    {
        $user = auth()->user()->load('profile');

        return view('profile', compact('user'));
    }

    public function updateRole(User $user, $validated): true|RedirectResponse
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

        return true;
    }

    private function updateCreateProfile(User $user, UserRequest $request): void
    {
        $validated = $request->validated();

        // Create profile data array
        $profileData = $validated['profile'] ?? [];

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');

            if ($user->profile && $user->profile->profile_image) {
                try {
                    Storage::disk('public')->delete($user->profile->profile_image);
                } catch (\Exception $e) {
                    // Ignored: a failed delete of the old image must not block
                    // saving the new one.
                }
            }

            try {
                $path = $file->store('profile_images', 'public');

                if (Storage::disk('public')->exists($path)) {
                    $profileData['profile_image'] = $path;
                }
            } catch (\Exception $e) {
                // Ignored: profile update proceeds without the new image.
            }
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );
    }
}
