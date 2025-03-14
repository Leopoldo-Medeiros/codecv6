<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/pricing', function () {
    return view('pricing');
})->name('pricing');

Route::get('/payment', function (\Illuminate\Http\Request $request) {
    $plan = $request->query('plan');
    $price = $request->query('price');

    return view('payment', ['plan' => $plan, 'price' => $price]);
})->name('payment');

Route::get('/about-us', function () {
    return view('about-us');
})->name('about-us');

Route::get('/faqs', function () {
    return view('faqs');
})->name('faqs');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Routes protected by authentication
Route::middleware(['auth'])->group(function () {
    // Panel of Admin (only for admin)
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // Routes Profile (Only for authenticated users)
    Route::get('/profile', [UsersController::class, 'profile'])->name('profile');
    Route::get('/profile/edit', [UsersController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [UsersController::class, 'update'])->name('profile.update');
    Route::get('/profile/{id}', [UsersController::class, 'show'])->name('profile.show');

    // Routes specific for Admins (only for admin)
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UsersController::class);
        Route::resource('courses', CourseController::class); // Moved inside the admin group
    });

    // Routes specific for clients (only for clients)
    Route::middleware(['role:client'])->group(function () {
        Route::get('/client/dashboard', function () {
            return 'Welcome to the client dashboard!';
        })->name('client.dashboard');
        // Add other client-specific routes here
    });

    // Route for both (Admin and client)
    Route::middleware(['role:client|admin'])->group(function () {
        // Add common routes here, if necessary
    });
});
