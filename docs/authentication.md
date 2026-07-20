# Authentication & Authorization (codecv6)

This guide covers the auth systems used by the codecv6 Laravel 12 + Nuxt 4 application.

## Overview

- **API authentication**: Laravel Sanctum — **token-based** (Bearer token in `Authorization` header), not session-based
- **Social login**: Google OAuth via Laravel Socialite (`GET /auth/google/redirect` → `GET /auth/google/callback`)
- **Authorization**: Spatie Laravel Permission with three roles — `admin`, `consultant`, `client`
- **Frontend state**: composable (`useAuth`) backed by `useState('user')` and `localStorage`

> NOTE: There is NO team-based multi-tenancy in this project. Authorisation is role-based.

## Architecture

```
Browser (Nuxt)
  ├── POST /api/login                        → access_token + user
  │   └── localStorage: auth_token, auth_user
  ├── GET /api/... (Authorization: Bearer <token>)
  └── POST /api/... (CSRF cookie + Bearer token)
                       ↓
                  Laravel Sanctum
                  (auth:sanctum middleware)
                       ↓
                  Role middleware
                  (role:admin|consultant)
                       ↓
                  Controller → Service → Resource
```

## Backend Setup

### Routes (`routes/api.php`)

```php
// Public — rate-limited 5/min
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [PasswordController::class, 'forgot']);
    Route::post('/reset-password', [PasswordController::class, 'reset']);
});

// Authenticated
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('courses', CourseController::class)->only(['index', 'show']);

    Route::middleware('role:admin|consultant')->group(function () {
        Route::apiResource('courses', CourseController::class)->except(['index', 'show']);
        Route::apiResource('paths', PathController::class);
    });

    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
    });
});

// Email verification (no auth, signature-verified)
Route::middleware(['signed', 'throttle:6,1'])->group(function () {
    Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
        ->name('verification.verify');
});

// Stripe webhook (no CSRF, signature-verified inside the controller)
Route::post('/webhooks/stripe', StripeWebhookController::class);
```

The Stripe webhook is excluded from CSRF in `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->validateCsrfTokens(except: ['api/webhooks/stripe']);
})
```

### Sanctum Configuration (`config/sanctum.php`)

```php
'stateful' => explode(',', env(
    'SANCTUM_STATEFUL_DOMAINS',
    'localhost:3001,codecv6.ddev.site'
)),

'guard' => ['web'],
'expiration' => null,
```

### Required Env Variables

```env
# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost:3001,codecv6.ddev.site,192.168.1.39:3001

# CORS (comma-separated allow-list, read by config/cors.php)
FRONTEND_URL=http://localhost:3001,http://192.168.1.39:3001,http://codecv6.ddev.site

# Backend redirects (OAuth callback, password reset emails) — single canonical URL
APP_FRONTEND_URL=http://localhost:3001

# Sessions — used by Sanctum for the CSRF cookie flow
SESSION_DRIVER=redis
SESSION_LIFETIME=500
SESSION_DOMAIN=null              # leave null in dev across hosts; set in prod

# Google OAuth
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI=http://codecv6.ddev.site/api/auth/google/callback
```

> WARNING: `FRONTEND_URL` (multi-origin allow-list) and `APP_FRONTEND_URL` (single canonical URL) are different. Don't confuse them.

### User Model (`app/Models/User.php`)

```php
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;

    public function consultant()
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }

    public function clients()
    {
        return $this->hasMany(User::class, 'consultant_id');
    }
}
```

## Login Flow

### Email/Password Login (`POST /api/login`)

```php
// app/Http/Controllers/Api/AuthController.php
public function login(LoginRequest $request): JsonResponse
{
    $user = User::where('email', $request->validated('email'))->first();

    if (!$user || !Hash::check($request->validated('password'), $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'user' => new UserResource($user->load('roles')),
        'access_token' => $token,
        'token_type' => 'Bearer',
    ]);
}
```

### Frontend Login (`useAuth.login()`)

```ts
// frontend/app/composables/useAuth.ts (extract)
async function login(email: string, password: string) {
  await useApi().get('/sanctum/csrf-cookie') // sets XSRF-TOKEN cookie

  const { user, access_token } = await useApi().post('/login', { email, password })

  localStorage.setItem('auth_token', access_token)
  localStorage.setItem('auth_user', JSON.stringify(user))
  userState.value = user

  await navigateTo('/dashboard')
}
```

### Google OAuth Flow

1. Frontend opens `${API_BASE}/auth/google/redirect` in the same window
2. Backend `SocialAuthController@redirect` calls `Socialite::driver('google')->redirect()`
3. Google authenticates, redirects to `GOOGLE_REDIRECT_URI` (`/api/auth/google/callback`)
4. `SocialAuthController@callback` exchanges the code, finds/creates a user (`google_id` column), and issues a Sanctum token
5. Backend redirects the browser to `${APP_FRONTEND_URL}/auth/callback?token=...&user=...`
6. Frontend `pages/auth/callback.vue` reads the query string and stores token/user

## Frontend Auth State

### `useAuth` Composable

State lives in `useState('user')` — shared across all calls in the same request.

```ts
export const useAuth = () => {
  const user = useState<User | null>('user', () => null)

  const isAuthenticated = computed(() => user.value !== null)
  const isAdmin = computed(() => user.value?.roles?.some(r => r.name === 'admin') ?? false)
  const isClient = computed(() => user.value?.roles?.some(r => r.name === 'client') ?? false)
  const isConsultant = computed(() => user.value?.roles?.some(r => r.name === 'consultant') ?? false)

  // login, logout, fetchUser, updateUser, ...

  return { user: readonly(user), isAuthenticated, isAdmin, isClient, isConsultant, /* ... */ }
}
```

### Middleware

- `app/middleware/auth.ts` — redirects unauthenticated users to `/login`
- `app/middleware/guest.ts` — redirects authenticated users to `/dashboard`

Apply per-page:
```vue
<script setup lang="ts">
definePageMeta({ middleware: 'auth' })
</script>
```

## Authorization

### Role Middleware (Routes)

Apply Spatie's role middleware on route groups:

```php
Route::middleware('role:admin')->group(function () { /* admin-only */ });
Route::middleware('role:admin|consultant')->group(function () { /* either */ });
Route::middleware('role:consultant')->group(function () { /* consultant-only */ });
```

### Business-Rule Authorization (Services)

For rules that depend on data (not just roles), services throw `AuthorizationException` — Laravel returns 403 automatically:

```php
// app/Services/UserService.php
public function delete(User $user, User $actor): void
{
    if ($user->id === $actor->id) {
        throw new AuthorizationException('You cannot delete yourself.');
    }

    if ($user->hasRole('admin') && User::role('admin')->count() === 1) {
        throw new AuthorizationException('Cannot delete the last admin.');
    }

    $user->delete();
}
```

### Frontend Role Checks

```vue
<script setup lang="ts">
const { isAdmin, isConsultant } = useAuth()
</script>

<template>
  <UButton v-if="isAdmin" @click="openAdminPanel">Admin panel</UButton>
  <NuxtLink v-if="isConsultant" to="/my-clients">My clients</NuxtLink>
</template>
```

## Handling Auth Errors

The `useApi()` wrapper handles 401 by clearing local auth state and redirecting to `/login`:

```ts
// frontend/app/composables/useApi.ts (extract)
export const useApi = () => {
  return $fetch.create({
    baseURL: useRuntimeConfig().public.apiBase,
    credentials: 'include',
    timeout: 90_000,
    onRequest({ options }) {
      const token = localStorage.getItem('auth_token')
      if (token) options.headers.set('Authorization', `Bearer ${token}`)
    },
    onResponseError({ response }) {
      if (response.status === 401) {
        localStorage.removeItem('auth_token')
        localStorage.removeItem('auth_user')
        navigateTo('/login')
      }
    },
  })
}
```

For `403`, surface the message in the UI (toast or alert). Don't auto-logout.

## Security Checklist

- ✅ All API routes (except public auth + Stripe webhook) protected by `auth:sanctum`
- ✅ Role middleware on writes (`role:admin`, `role:admin|consultant`)
- ✅ Business rules throw `AuthorizationException` (returns 403)
- ✅ Public routes rate-limited (5/min for login/register/forgot/reset)
- ✅ CSRF cookie flow for stateful endpoints; Bearer token for stateless
- ✅ Stripe webhook excluded from CSRF, verified via signature
- ✅ Sensitive fields stripped from API responses (`password`, `remember_token`, OAuth tokens)
- ✅ Tokens never logged

## Troubleshooting

### "Invalid signature" on Stripe webhook
- `STRIPE_WEBHOOK_SECRET` doesn't match the secret printed by `stripe listen`
- Re-run `stripe listen --forward-to codecv6.ddev.site/api/webhooks/stripe` and copy the new secret into `.env`
- `ddev artisan config:clear` after editing `.env`

### `Failed to fetch` on `/sanctum/csrf-cookie`
- HTTPS dev cert is self-signed and not trusted — use `http://codecv6.ddev.site` instead (set `NUXT_PUBLIC_API_BASE`)
- Or run `mkcert -install` once to trust DDEV's TLS cert

### 401 Unauthorized on API requests
- Token missing from `localStorage` (`auth_token`)
- `useApi()` not actually being used in the failing component
- Token expired (revoked, or user deleted)
- `SANCTUM_STATEFUL_DOMAINS` doesn't include the frontend origin (only matters for stateful flows)

### 403 Forbidden on API requests
- Route is gated by `role:` middleware and the user's role doesn't match
- Service threw `AuthorizationException` — read the message
- Check `user.roles` in the response from `/api/me`

### CORS error in browser console
```
Access to fetch at 'http://codecv6.ddev.site/api/...' from origin 'http://localhost:3001'
has been blocked by CORS policy
```
- `FRONTEND_URL` must include `http://localhost:3001` (comma-separated list)
- `ddev artisan config:clear`

### OAuth callback redirects to wrong URL
- Backend uses `config('app.frontend_url')` — read from `APP_FRONTEND_URL`, NOT `FRONTEND_URL`
- Verify `APP_FRONTEND_URL` is a single canonical URL (e.g. `http://localhost:3001`), not a list

## Additional Resources

- [Laravel Sanctum docs](https://laravel.com/docs/sanctum)
- [Spatie Permission docs](https://spatie.be/docs/laravel-permission)
- [Laravel Socialite docs](https://laravel.com/docs/socialite)
