<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/pricing', function () {
    return view('welcome');
})->name('pricing');
Route::get('/about-us', function () {
    return view('welcome');
})->name('about-us');

// Public routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rotas protegidas (usuários autenticados)
Route::middleware(['auth'])->group(function () {
    // Painel de administração (para todos os usuários autenticados)
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // Rotas de perfil (para qualquer usuário autenticado)
    Route::get('/profile', [UsersController::class, 'profile'])->name('profile');
    Route::put('/profile/{id}', [UsersController::class, 'update'])->name('update');
    Route::get('/profile/edit/{id}', [UsersController::class, 'edit'])->name('edit');
    Route::get('/profile/{id}', [UsersController::class, 'show'])->name('show');

    // Rotas específicas para administradores
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UsersController::class);
    });

    // Rotas específicas para clientes
    Route::middleware(['role:client'])->group(function () {
        Route::get('/client/dashboard', function () {
            return 'Bem-vindo ao painel do cliente';
        })->name('client.dashboard');
        // Adicione outras rotas específicas de cliente aqui
    });

    // Rotas para ambos (admin e client)
    Route::middleware(['role:client|admin'])->group(function () {
        // NO ROUTES FOR CLIENTS YET - Adicione suas rotas comuns aqui
    });
});
