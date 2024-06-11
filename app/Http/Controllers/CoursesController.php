<?php

namespace App\Http\Controllers;

use App\Models\User;

class CoursesController extends Controller
{
    // Aqui estou tentando exibir cursos para um user específico
    public function index(USer $user)
    {
        return view('users.courses', ['courses' => $user->courses]);
    }
}
