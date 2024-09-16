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

    Route::get('/profile', [UsersController::class, 'profile'])->name('profile');
    Route::put('/profile/{id}', [UsersController::class, 'update'])->name('profile.update');
    Route::get('/profile/edit/{id}', [UsersController::class, 'edit'])->name('profile.edit');

    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UsersController::class);
        Route::get('/users/{user}', [UsersController::class, 'show'])->name('users.show');
        Route::put('/users/{user}', [UsersController::class, 'update'])->name('users.update'); // Add this line
    });

    // Client routes
    Route::middleware(['role:client|admin'])->group(function () {
        // NO ROUTES FOR CLIENTS YET
    });
});
