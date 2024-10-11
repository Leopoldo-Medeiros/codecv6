<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AuthAdminController;
use Illuminate\Support\Facades\Route;

// Rota inicial
Route::get('/', function () {
    return view('welcome');
});

// Rotas de autenticação
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logOut'])->name('logout');

// Rotas de registro
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/verify-email/{token}', [RegisterController::class, 'verifyEmail'])->name('verify.email');

// Rotas de OAuth Google (para clientes)
Route::get('auth/google', [RegisterController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [RegisterController::class, 'handleGoogleCallback']);

// Rotas de OAuth Google (para administradores)
Route::get('admin/auth/google', [AuthAdminController::class, 'redirectToGoogle'])->name('admin.auth.google');
Route::get('admin/auth/google/callback', [AuthAdminController::class, 'handleGoogleCallback']);

// Rotas públicas (exemplo: preços e sobre nós)
Route::get('/pricing', function () {
    return view('welcome');
})->name('pricing');

Route::get('/about-us', function () {
    return view('welcome');
})->name('about-us');

// Rotas protegidas (usuários autenticados)
Route::middleware(['auth'])->group(function () {

    // Painel de administração (para todos os usuários autenticados)
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

// Rotas de perfil (para qualquer usuário autenticado)
    Route::get('/profile', [UsersController::class, 'profile'])->name('profile');
    Route::put('/profile/{id}', [UsersController::class, 'update'])->name('update');
    Route::get('/profile/edit/{id}', [UsersController::class, 'edit'])->name('edit');

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
