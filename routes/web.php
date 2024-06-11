<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'customLogin'])->name('login.post');
Route::get('/logout', [AuthController::class, 'signOut'])->name('logout');

// Estas rotas estão protegidas pelo middleware de autenticação
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [AuthController::class, 'dashboard']);

    // Group middleware role admin
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UsersController::class);
        Route::resource('courses', CoursesController::class);
        //  Route::resource('paths', PathsController::class);
        //  Route::resource('steps', StepsController::class);
    });

    Route::get('/users/{user}/courses', [CoursesController::class, 'index'])->name('users.courses');
});
