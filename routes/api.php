<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChallengeController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\CvController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\LinkedInController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\PathController;
use App\Http\Controllers\Api\PathStepController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\PlaygroundController;
use App\Http\Controllers\Api\PublicChallengeController;
use App\Http\Controllers\Api\PublicProfileController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\StripeWebhookController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VerifyEmailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All routes are prefixed with /api automatically.
|
*/

// Google OAuth
Route::get('/auth/google/redirect', [SocialAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

// Stripe webhook (signature-verified, no auth, no CSRF)
Route::post('/webhooks/stripe', [StripeWebhookController::class, 'handle']);

// Email verification (signed URL, no session auth required)
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('api.email.verify');

// Public routes — rate limited to prevent brute force
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
});

// Public practice teaser (no account) — practice funnel stage 1 / F2.
// IP-throttled (no user_id to key on): the run endpoint executes real code.
Route::get('/public/challenges/teaser', [PublicChallengeController::class, 'teaser'])
    ->middleware('throttle:30,1');
Route::post('/public/challenges/{challenge:slug}/run', [PublicChallengeController::class, 'run'])
    ->middleware('throttle:5,1');

// Public skill profile (opt-in, shareable) — practice funnel stage 5 / F3.
Route::get('/public/profile/{slug}', [PublicProfileController::class, 'show'])
    ->middleware('throttle:30,1');

// Protected routes (require authentication)
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);

    // Read-only access (all authenticated roles)
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/{course}', [CourseController::class, 'show']);
    Route::get('/plans', [PlanController::class, 'index']);
    Route::get('/plans/{plan}', [PlanController::class, 'show']);
    Route::get('/paths', [PathController::class, 'index']);
    Route::get('/my-paths', [PathController::class, 'myPaths']);
    Route::get('/paths/{path}', [PathController::class, 'show']);
    Route::get('/paths/{path}/steps', [PathStepController::class, 'index']);
    Route::get('/path-steps/{step}', [PathStepController::class, 'show']);

    // Challenges
    Route::get('/challenges', [ChallengeController::class, 'index']);
    Route::get('/challenges/{challenge:slug}', [ChallengeController::class, 'show']);
    // Throttled per-user: each run is a Judge0 sandbox submission.
    Route::post('/challenges/{challenge:slug}/run', [ChallengeController::class, 'run'])->middleware('throttle:20,1');

    // Scratch playground — runs arbitrary PHP via Judge0 (no tests). Lightly
    // throttled per-user to keep Judge0 calls bounded; the front-end shows
    // the response as plain stdout/stderr.
    Route::post('/playground/run', [PlaygroundController::class, 'run'])->middleware('throttle:30,1');

    // Step progress (all authenticated users update their own progress)
    Route::put('/path-steps/{step}/progress', [PathStepController::class, 'updateProgress']);
    // Quiz grading (server-side; F4-gated) — practice funnel F5
    Route::post('/path-steps/{step}/quiz', [PathStepController::class, 'submitQuiz']);
    Route::get('/jobs', [JobController::class, 'index']);
    Route::get('/jobs/{job}', [JobController::class, 'show']);

    // CV & LinkedIn analysis (all authenticated users). Throttled: each call
    // is a paid Gemini request (plus a Jina fetch for CV job-url analysis).
    Route::post('/cv/analyze', [CvController::class, 'analyze'])->middleware('throttle:5,1');
    Route::post('/linkedin/analyze', [LinkedInController::class, 'analyze'])->middleware('throttle:5,1');

    // Stripe Checkout (all authenticated users)
    Route::post('/checkout/session', [CheckoutController::class, 'createSession'])->middleware('throttle:10,1');
    Route::get('/checkout/{sessionId}/status', [CheckoutController::class, 'status']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markRead']);
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllRead']);

    // Onboarding (authenticated user completes their own profile)
    Route::patch('/me/onboarding', [UserController::class, 'completeOnboarding']);

    // Practice funnel — gamification snapshot (XP, streak, badges)
    Route::get('/me/progress', [UserController::class, 'progress']);

    // Practice funnel — coaching upsell recommendation (F6)
    Route::get('/me/coaching-recommendation', [UserController::class, 'coachingRecommendation']);

    // Daily practice activity for the contribution heatmap
    Route::get('/me/activity', [UserController::class, 'activity']);

    // Practice funnel — public skill profile visibility (owner only)
    Route::patch('/me/public-profile', [PublicProfileController::class, 'update']);

    // User self-service (admin or resource owner — enforced in controller)
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::patch('/users/{user}', [UserController::class, 'update']);
    Route::post('/users/{user}/avatar', [UserController::class, 'updateAvatar']);

    // GDPR — data portability (own data) and erasure (admin only)
    Route::get('/users/{user}/export', [UserController::class, 'exportData']);
    Route::delete('/users/{user}/erase', [UserController::class, 'eraseData']);

    // Consultant — my clients
    Route::middleware(['role:admin|consultant'])->group(function () {
        Route::get('/my-clients', [UserController::class, 'myClients']);
        Route::get('/my-clients/{client}', [UserController::class, 'clientDetail']);
        Route::post('/my-clients/{client}/paths', [UserController::class, 'assignPath']);
        Route::delete('/my-clients/{client}/paths/{path}', [UserController::class, 'removePath']);
    });

    // Admin + Consultant
    Route::middleware(['role:admin|consultant'])->group(function () {
        Route::post('/courses', [CourseController::class, 'store']);
        Route::put('/courses/{course}', [CourseController::class, 'update']);
        Route::patch('/courses/{course}', [CourseController::class, 'update']);
        Route::delete('/courses/{course}', [CourseController::class, 'destroy']);

        Route::post('/plans', [PlanController::class, 'store']);
        Route::put('/plans/{plan}', [PlanController::class, 'update']);
        Route::patch('/plans/{plan}', [PlanController::class, 'update']);
        Route::delete('/plans/{plan}', [PlanController::class, 'destroy']);
        Route::post('/plans/{plan}/clients', [PlanController::class, 'attachClient']);
        Route::delete('/plans/{plan}/clients/{user}', [PlanController::class, 'detachClient']);

        Route::post('/paths', [PathController::class, 'store']);
        Route::put('/paths/{path}', [PathController::class, 'update']);
        Route::patch('/paths/{path}', [PathController::class, 'update']);
        Route::delete('/paths/{path}', [PathController::class, 'destroy']);

        // Path step management (admin/consultant only)
        Route::post('/paths/{path}/steps', [PathStepController::class, 'store']);
        Route::put('/paths/{path}/steps/{step}', [PathStepController::class, 'update']);
        Route::delete('/paths/{path}/steps/{step}', [PathStepController::class, 'destroy']);
        Route::post('/paths/{path}/steps/reorder', [PathStepController::class, 'reorder']);

        Route::post('/jobs', [JobController::class, 'store']);
        Route::put('/jobs/{job}', [JobController::class, 'update']);
        Route::patch('/jobs/{job}', [JobController::class, 'update']);
        Route::delete('/jobs/{job}', [JobController::class, 'destroy']);
    });

    // Admin only
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
        Route::get('/consultants', [UserController::class, 'consultants']);
        Route::patch('/users/{user}/consultant', [UserController::class, 'assignConsultant']);
        Route::get('/roles', [RoleController::class, 'index']);
    });
});
