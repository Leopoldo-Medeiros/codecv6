<?php

// app/Http/Controllers/UsersController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

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
        $roles = Role::all(); // Carregar todas as roles disponíveis
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    // app/Http/Controllers/UsersController.php

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
            'password' => 'nullable|min:8',
            'birth_date' => 'nullable|date',
            'role' => 'nullable|exists:roles,id',
        ]);

        $user = User::findOrFail($id);

        // Update user fields
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // Update or create profile
        $user->profile()->updateOrCreate(['user_id' => $user->id], [
            'birth_date' => $validated['birth_date'] ?? null,
            'profession' => $validated['profession'] ?? null,
        ]);

        // Update user role
        if (!empty($validated['role'])) {
            $role = Role::findById($validated['role']);
            $user->syncRoles($role);
        }

        // Assign the role to the user
//        $user->assignRole($validated['role']);

        $user->save();

        return redirect()->route('users.show', $user->id)->with('success', 'Profile updated successfully.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Nessa parte evita de auto deletar o user
        // Nesse if statement ele ta verificando se o user é o mesmo que está logado [Auth::id()]
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete yourself.');
        }

        // Aqui verifica se o user é admin ou não
        if ($user->hasRole('admin')) {
            // Conta quantos admins existem
            $adminCount = User::role('admin')->count();

            // Evita deletar se for o único admin
            if ($adminCount <= 1) {
                return redirect()->route('users.index')->with('error', 'You cannot delete the only admin.');
            }
        }
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
