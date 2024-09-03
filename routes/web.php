<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logOut'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/pricing', function () {
    return view('welcome');
})->name('pricing');

Route::get('/about-us', function () {
    return view('welcome');
})->name('about-us');

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UsersController::class)->except(['index', 'show']);
        Route::get('users', [UsersController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UsersController::class, 'show'])->name('users.show');
        Route::delete('users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');
        Route::get('users/create', [UsersController::class, 'create'])->name('users.create');
        Route::get('users/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
    });

    // Client routes
    Route::middleware(['role:client|admin'])->group(function () {
        // NO ROUTES FOR CLIENTS YET
    });
});
