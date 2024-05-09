<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/home', function () {
    return view('home');
})->name('home');
//
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
Route::get('/dashboard', function () {
    // Only authenticated users may enter...
    return view('admin.dashboard');
})->middleware('auth')->name('dashboard');

Route::get('/', function () {
    return view('welcome');
});
