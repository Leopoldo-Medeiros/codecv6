<?php

use App\Http\Controllers\UsersController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logOut'])->middleware('auth:sanctum');


// Protected routes (require authentication)
Route::middleware(['auth:sanctum'])->group(function () {
    // User management API
    Route::get('/users', [UsersController::class, 'index']);
    Route::post('/users', [UsersController::class, 'store']);
    Route::get('/users/{user}', [UsersController::class, 'show']);
    Route::put('/users/{user}', [UsersController::class, 'update']);
    Route::delete('/users/{user}', [UsersController::class, 'destroy']);
    
    // Get available roles
    Route::get('/roles', [UsersController::class, 'getRoles']);
});
