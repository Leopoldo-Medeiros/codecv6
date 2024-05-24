<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/home', function () {
    return view('home');
})->name('home');

// Comentadas para simplificar o exemplo
//Route::get('/about-us', function () {
//    return view('about-us');
//})->name('about-us');
//
//Route::get('/pricing', function () {
//    return view('pricing');
//})->name('pricing');
//
//Route::get('/faqs', function () {
//    return view('faqs');
//})->name('faqs');

Route::get('/admin/login', [AdminController::class, 'index'])->name('login');
Route::post('/admin/login', [AdminController::class, 'customLogin'])->name('admin.login.post');
Route::get('/admin/logout', [AdminController::class, 'signOut'])->name('admin.logout');

Route::get('/', function () {
    return view('welcome');
});

// Admin roles
// Esta rota garante que apenas usuários autenticados com o papel de admin
// possam acessar o dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
});
Route::get('/logout', [AdminController::class, 'logout'])->name('logout');
// Admin page
Route::get('/admin/page', function () {
})->name('admin.page');

// Client page
Route::get('/client/page', function () {
})->name('client.page');
