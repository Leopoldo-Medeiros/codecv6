<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/home', function () {
    return view('home');
})->name('home');

// USER CONTROLLERS
// Estas rotas estão protegidas pelo middleware de autenticação
Route::middleware(['auth'])->group(function () {
    Route::get('/user/{id}', [UserController::class, 'show'])->name('show');
    Route::post('/user/create', [UserController::class, 'create'])->name('create');
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('edit');
    Route::post('/user/{id}/update', [UserController::class, 'update'])->name('update');
    Route::delete('/user/{id}', [UserController::class, 'delete'])->name('delete');
    Route::get('/users', [UserController::class, 'index'])->name('index');
});

// AUTH CONTROLLERS
// Exibe o formulário de login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
// Processa o formulário de login
Route::post('/login', [AuthController::class, 'customLogin'])->name('login.post');
Route::get('/logout', [AuthController::class, 'signOut'])->name('logout');
Route::get('/admin', [AuthController::class, 'dashboard'])->name('admin');

// Client controllers
Route::get('/client', [UserController::class, 'index'])->name('client');

Route::get('/', function () {
    return view('welcome');
});

// Admin roles
// Esta rota garante que apenas usuários autenticados com o papel de admin
// possam acessar o dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard']);
});
