<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/home', function () {
    return view('home');
})->name('home');

// Admin controllers
Route::get('/admin/login', [AuthController::class, 'index'])->name('login');
Route::post('/admin/login', [AuthController::class, 'customLogin'])->name('login.post');
Route::get('/admin/logout', [AuthController::class, 'signOut'])->name('logout');

// User controllers
Route::get('/user/{$id}', [UserController::class, 'show'])->name('show');
Route::post('/user/create', [UserController::class, 'create'])->name('create');
Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('edit');
Route::post('/user/{id}/update', [UserController::class, 'update'])->name('update');
Route::delete('/user/{id}', [UserController::class, 'delete'])->name('delete');
Route::get('/users', [UserController::class, 'index'])->name('index');

Route::get('/', function () {
    return view('welcome');
});

// Admin roles
// Esta rota garante que apenas usuários autenticados com o papel de admin
// possam acessar o dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard']);
});
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
// Admin page
Route::get('/admin/page', function () {
})->name('admin.page');

// Client page
Route::get('/client/page', function () {
})->name('client.page');
