<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

abstract class UserController
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
        $user = User::findOrFail($id); // Isso irá gerar uma ModelNotFoundException se o usuário não for encontrado
        return view('user.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('user.edit', compact('user'));
    }

    public function update($request, $id)
    {
        // Validação dos dados
        // Se não utilizar a validacao, estaria atualizando o usuário com todos os dados recebidos na requisição HTTP.
        // Isso poderia ser perigoso se a requisição tiver campos indesejados ou maliciosos.
        // Utilizei a validação para garantir que apenas os campos esperados sejam atualizados.

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
