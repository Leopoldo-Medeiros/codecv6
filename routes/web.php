<?php

use App\Http\Controllers\AdminController;
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

// Client controllers
Route::get('/client', [UserController::class, 'index'])->name('client');

// Admin controllers
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/clients', [AdminController::class, 'showClients'])->name('clients');
    Route::get('/admin/courses', [AdminController::class, 'showCourses'])->name('courses');
    Route::get('/admin/paths', [AdminController::class, 'showPaths'])->name('paths');
    Route::get('/admin/steps', [AdminController::class, 'showSteps'])->name('steps');
});

Route::get('/', function () {
    return view('welcome');
});

// Admin roles
// Esta rota garante que apenas usuários autenticados com o papel de admin
// possam acessar o dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
});
