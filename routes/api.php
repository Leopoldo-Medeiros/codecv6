<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\PathController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All routes are prefixed with /api automatically.
|
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes (require authentication)
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);

    // Users
    Route::apiResource('users', UserController::class);
    Route::post('/users/{user}/avatar', [UserController::class, 'updateAvatar']);

    // Roles
    Route::get('/roles', [RoleController::class, 'index']);

    // Courses
    Route::apiResource('courses', CourseController::class);

    // Plans
    Route::apiResource('plans', PlanController::class);
    Route::post('/plans/{plan}/clients', [PlanController::class, 'attachClient']);
    Route::delete('/plans/{plan}/clients/{user}', [PlanController::class, 'detachClient']);

    // Paths
    Route::apiResource('paths', PathController::class);

    // Jobs
    Route::apiResource('jobs', JobController::class);
});
