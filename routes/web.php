<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'customLogin'])->name('login.post');
Route::get('/logout', [AuthController::class, 'signOut'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/pricing', function () {
    return view('welcome');
})->name('pricing');

Route::get('/about-us', function () {
    return view('welcome');
})->name('about-us');

// Estas rotas estão protegidas pelo middleware de autenticação
// routes/web.php

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UsersController::class);
        Route::delete('users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');
        Route::get('users/create', [UsersController::class, 'create'])->name('users.create');
    });

    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
});
