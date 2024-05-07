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

Route::get('/admin/login', [AdminController::class, 'login'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'loginPost'])->name('admin.login.post');
