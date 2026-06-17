# Backend Development Guide (codecv6)

Laravel 12 + PHP 8.4 patterns for the codecv6 backend.

## Architecture

```
HTTP Request
    ↓
Route (auth:sanctum, role:admin|consultant, throttle, ...)
    ↓
Controller (app/Http/Controllers/Api/) — thin
    ↓
FormRequest (app/Http/Requests/) — validation
    ↓
Service (app/Services/) — business logic
    ↓
Model (app/Models/)
    ↓
Resource (app/Http/Resources/) — response shape
```

## Controllers

Thin. Validate via FormRequest, delegate to Service, return Resource.

```php
namespace App\Http\Controllers\Api;

use App\Http\Requests\CourseRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;

class CourseController extends Controller
{
    public function __construct(private CourseService $courses) {}

    public function index(): JsonResponse
    {
        $courses = $this->courses->paginate(20);
        return CourseResource::collection($courses)->response();
    }

    public function store(CourseRequest $request): JsonResponse
    {
        $course = $this->courses->create($request->validated());

        return response()->json([
            'message' => 'Course created successfully',
            'course' => new CourseResource($course->load('creator')),
        ], 201);
    }

    public function show(Course $course): JsonResponse
    {
        return (new CourseResource($course->load('creator')))->response();
    }

    public function update(CourseRequest $request, Course $course): JsonResponse
    {
        $course = $this->courses->update($course, $request->validated());

        return response()->json([
            'message' => 'Course updated successfully',
            'course' => new CourseResource($course),
        ]);
    }

    public function destroy(Course $course): JsonResponse
    {
        $this->courses->delete($course, auth()->user());
        return response()->json(null, 204);
    }
}
```

### Invokable Controllers (Non-CRUD)

```php
namespace App\Http\Controllers\Api;

use App\Http\Requests\AssignPathRequest;
use App\Models\Path;
use App\Models\User;
use App\Services\MyClientsService;
use Illuminate\Http\JsonResponse;

class AssignPathToClientController extends Controller
{
    public function __construct(private MyClientsService $service) {}

    public function __invoke(AssignPathRequest $request, User $client): JsonResponse
    {
        $path = Path::findOrFail($request->validated('path_id'));

        $this->service->assignPath(
            consultant: auth()->user(),
            client: $client,
            path: $path,
        );

        return response()->json(['message' => 'Path assigned to client']);
    }
}
```

Registered as:
```php
Route::post('my-clients/{client}/paths', AssignPathToClientController::class);
```

## FormRequests

Validation + authorization + auto-injection.

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['admin', 'consultant']) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'is_published' => 'boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Inject the authenticated user as the course creator (only on create)
        if ($this->isMethod('POST')) {
            $this->merge(['user_id' => $this->user()?->id]);
        }
    }
}
```

### Patterns for `prepareForValidation()`

- `CourseRequest` → injects `user_id` (creator)
- `PlanRequest` → injects `consultant_id`
- `UserRequest` → removes null `password` so validation doesn't reject empty values on update

## Services

Single responsibility. Business logic + authorization rules + side effects.

```php
namespace App\Services;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CourseService
{
    public function __construct(private FileUploadService $files) {}

    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return Course::query()
            ->with('creator')
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Course
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['image']) && $data['image']) {
                $data['image_path'] = $this->files->upload($data['image'], 'courses');
                unset($data['image']);
            }

            return Course::create($data);
        });
    }

    public function update(Course $course, array $data): Course
    {
        return DB::transaction(function () use ($course, $data) {
            if (isset($data['image']) && $data['image']) {
                $data['image_path'] = $this->files->replace(
                    $data['image'],
                    'courses',
                    $course->image_path,
                );
                unset($data['image']);
            }

            $course->update($data);
            return $course->load('creator');
        });
    }

    public function delete(Course $course, User $actor): void
    {
        // Business-rule authorization — only the creator or admin can delete
        if ($course->user_id !== $actor->id && !$actor->hasRole('admin')) {
            throw new AuthorizationException(
                'You can only delete courses you created.'
            );
        }

        $course->delete();
    }
}
```

### When to Throw `AuthorizationException`

Throw it for **business-rule** authorization (data-dependent, not just role-based). Laravel returns 403 automatically.

Examples:
- "You cannot delete yourself" (`UserService::delete`)
- "Cannot delete the last admin" (`UserService::delete`)
- "You can only see your own consultant's clients" (`MyClientsService`)
- "You can only update progress on your own steps" (`UserStepProgressService`)

Don't throw it for **role**-based checks — those go on the route via `role:` middleware.

## Resources

Transform models for API responses. Guard relationships.

```php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image_url' => $this->image_path
                ? asset('storage/' . $this->image_path)
                : null,
            'is_published' => (bool) $this->is_published,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),

            'creator' => $this->when(
                $this->relationLoaded('creator'),
                fn() => new UserResource($this->creator),
            ),
        ];
    }
}
```

### Why `relationLoaded()` Matters

Without the guard, accessing `$this->creator` would lazy-load the relationship → N+1. With it, the field appears only when the controller eager-loaded the relation.

## Models

Lean models — no business logic.

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'image_path',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
```

### Self-Referential FK on `User`

```php
public function consultant()
{
    return $this->belongsTo(User::class, 'consultant_id');
}

public function clients()
{
    return $this->hasMany(User::class, 'consultant_id');
}
```

## Routes

Grouped by access level in `routes/api.php`.

```php
use Illuminate\Support\Facades\Route;

// Public (rate-limited 5/min)
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [PasswordController::class, 'forgot']);
    Route::post('/reset-password', [PasswordController::class, 'reset']);
});

// Stripe webhook (no auth, signature-verified inside controller)
Route::post('/webhooks/stripe', StripeWebhookController::class);

// All authenticated
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('courses', CourseController::class)->only(['index', 'show']);
    Route::apiResource('paths',   PathController::class)->only(['index', 'show']);
    Route::apiResource('jobs',    JobController::class)->only(['index', 'show']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markRead']);

    Route::post('/checkout/session', CreateCheckoutSessionController::class);
    Route::get('/checkout/status/{sessionId}', CheckoutStatusController::class);

    // Admin + Consultant
    Route::middleware('role:admin|consultant')->group(function () {
        Route::apiResource('courses', CourseController::class)
            ->except(['index', 'show']);
        Route::apiResource('paths', PathController::class)
            ->except(['index', 'show']);
    });

    // Consultant only
    Route::middleware('role:consultant')->group(function () {
        Route::get('/my-clients', [MyClientsController::class, 'index']);
        Route::post('/my-clients/{client}/paths', AssignPathToClientController::class);
    });

    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
    });
});

// Email verification (signed URL, throttled)
Route::middleware(['signed', 'throttle:6,1'])->group(function () {
    Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
        ->name('verification.verify');
});
```

## Common Conventions

### HTTP Status Codes
- `200` GET / PUT / PATCH success
- `201` POST creates a resource
- `204` DELETE success
- `401` not authenticated (Sanctum middleware)
- `403` not authorised (role middleware or `AuthorizationException`)
- `404` resource not found (route-model binding fail or explicit `findOrFail`)
- `409` conflict (e.g. resource already exists)
- `422` validation failed

### Response Envelopes

```php
// Single resource with custom message
return response()->json([
    'message' => 'Course created successfully',
    'course' => new CourseResource($course),
], 201);

// Resource Collection (handles pagination meta automatically)
return CourseResource::collection($courses)->response();

// Simple action result
return response()->json(['message' => 'Path assigned']);

// 204 No Content
return response()->json(null, 204);
```

### Pagination

```php
$courses = Course::query()->with('creator')->paginate(20);
return CourseResource::collection($courses)->response();
// Response: { data, links: { first, last, prev, next }, meta: { current_page, last_page, total, per_page } }
```

### Pivot Sync (Many-to-Many)

```php
// Replace the set
$plan->paths()->sync([1, 2, 3]);

// Append without removing existing
$plan->paths()->syncWithoutDetaching([4, 5]);

// Detach specific
$plan->paths()->detach([2]);
```

### Transactions

```php
DB::transaction(function () {
    $payment = Payment::create([...]);
    $user->update([...]);
    Notification::dispatch(...);
});
```

## Stripe Integration

```php
// app/Services/StripeService.php
namespace App\Services;

use App\Enums\PaymentTier;
use App\Models\Payment;
use App\Models\User;
use Stripe\Checkout\Session;
use Stripe\StripeClient;

class StripeService
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function createCheckoutSession(
        User $user,
        PaymentTier $tier,
        string $currency,
    ): Session {
        $pricing = config("pricing.tiers.{$tier->value}");
        $amount = $pricing['amount'][$currency]; // minor units (cents)

        $session = $this->stripe->checkout->sessions->create([
            'mode' => $tier->isSubscription() ? 'subscription' : 'payment',
            'customer_email' => $user->email,
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => ['name' => $pricing['name']],
                    'unit_amount' => $amount,
                ],
                'quantity' => 1,
            ]],
            'success_url' => config('app.frontend_url') . '/payment?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => config('app.frontend_url') . '/pricing',
            'metadata' => ['user_id' => $user->id, 'tier' => $tier->value],
        ]);

        Payment::create([
            'user_id' => $user->id,
            'stripe_session_id' => $session->id,
            'tier' => $tier->value,
            'amount_minor' => $amount,
            'currency' => $currency,
            'status' => 'pending',
        ]);

        return $session;
    }
}
```

## Notifications

Database channel for in-app notifications, mail channel for important events.

```php
// app/Notifications/PathAssigned.php
namespace App\Notifications;

use App\Models\Path;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PathAssigned extends Notification
{
    use Queueable;

    public function __construct(private Path $path) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'path_assigned',
            'path_id' => $this->path->id,
            'path_title' => $this->path->title,
            'message' => "A new path '{$this->path->title}' has been assigned to you.",
        ];
    }
}
```

Dispatch:
```php
$client->notify(new PathAssigned($path));
```

Read:
```php
// GET /api/notifications
$notifications = auth()->user()
    ->notifications()
    ->take(50)
    ->get();
```

## File Uploads

Use `FileUploadService` — validates, stores under `storage/app/public/{folder}`, returns the relative path.

```php
public function store(CourseRequest $request, FileUploadService $files)
{
    $data = $request->validated();
    if ($request->hasFile('image')) {
        $data['image_path'] = $files->upload($request->file('image'), 'courses');
    }
    return Course::create($data);
}
```

Front-end serves these via `asset('storage/' . $course->image_path)`.

## Code Style

```bash
ddev exec vendor/bin/pint --dirty   # format only changed files
```

PSR-12 + Laravel defaults. Type hints on all method parameters and return types (PHP 8.4 strict typing).

## See Also

- API endpoint design: `.claude/agents/api-design-agent.md`
- Testing: `docs/testing-guide.md`
- Migrations: `.claude/agents/migration-agent.md`
- Auth: `docs/authentication.md`
