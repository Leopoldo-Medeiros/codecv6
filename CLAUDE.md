# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Full-stack application: Laravel 11 (backend) + Nuxt 3 (frontend). Platform for managing courses, learning paths, job listings, and consultant-client relationships with role-based access control.

## Development Commands

### Lando (Local Environment)

```bash
lando start                    # Start environment
lando stop                     # Stop environment
lando ssh                      # SSH into appserver
```

**Environment:** PHP 8.3, MySQL 8.0, Nginx, Xdebug enabled
**URLs:** API at `http://codecv6.lndo.site/api`, Frontend proxied at `http://dashboard.codecv6.lndo.site:3000`

### Backend (Laravel)

```bash
composer install                                    # Install dependencies
lando artisan migrate                               # Run migrations
lando artisan migrate:fresh                         # Fresh database
lando artisan db:seed                               # Run all seeders
lando artisan db:seed --class=RoleSeeder            # Specific seeder
./vendor/bin/pint                                   # Code formatting
lando php vendor/bin/phpunit                        # Run all tests
lando php vendor/bin/phpunit --testsuite=Feature    # Feature tests only
lando php vendor/bin/phpunit tests/Feature/ExampleTest.php  # Single test
lando artisan cache:clear && lando artisan config:clear && lando artisan route:clear  # Clear caches
lando artisan storage:link                          # Link public storage
```

### Frontend (Nuxt 3)

```bash
cd frontend && npm install     # Install dependencies
npm run dev                    # Dev server at http://localhost:3000
npm run build                  # Production build
```

## Architecture

### Backend Service Layer Pattern

Controllers use dependency injection with Service classes that handle business logic:

```
Controller → Service → Model
```

- **Controllers** (`app/Http/Controllers/Api/`): Handle HTTP, validation, return Resources
- **Services** (`app/Services/`): Business logic, authorization checks, file uploads
- **Resources** (`app/Http/Resources/`): Transform models for API responses
- **Requests** (`app/Http/Requests/`): Form request validation

Example: `UserController` → `UserService` → `User` model

### Roles & Permissions

Uses Spatie Laravel Permission with `RoleEnum` (`app/Enums/RoleEnum.php`):
- `admin` (id: 1)
- `client` (id: 2)
- `consultant` (id: 3)

`RoleEnum::fromId(int $id)` provides type-safe role lookup. Middleware aliases: `role`, `permission`, `role_or_permission`.

### Core Domain Models

| Model | Key Relationships |
|-------|------------------|
| `User` | Has one `Profile`, has roles, optionally has `consultant_id` (FK to another User) |
| `Profile` | Belongs to User, stores social links (github, linkedin, instagram, facebook, website) |
| `Plan` | Belongs to consultant (User), has many clients (users), courses, paths via pivots |
| `Course` | Soft-deletable, belongs to User (creator) |
| `Path` | Learning paths, soft-deletable |
| `Job` | Job listings, soft-deletable |

`Course`, `Path`, `Job`, and `Plan` all use `SoftDeletes`. Services manage pivot relationships with `.sync()` (e.g., `PlanService` manages `plan_user` and `path_plan`).

### API Authentication

- **Sanctum token auth** for API endpoints
- CSRF cookie: call `/sanctum/csrf-cookie` before POST/PUT/DELETE (frontend does this automatically)
- Token stored in `localStorage` as `auth_token`; user object as `auth_user`
- Login response: `{ user, access_token, token_type }`

### API Response Conventions

Controllers return consistent envelopes:
```json
{ "message": "User created successfully", "user": { ... } }
```
Collections use Laravel API Resources with pagination metadata.

Resources use `$this->when($this->relationLoaded('relationship'), ...)` to avoid N+1 queries and circular includes.

### Form Requests

`prepareForValidation()` auto-injects authenticated user IDs:
- `CourseRequest` → injects `user_id`
- `PlanRequest` → injects `consultant_id`
- `UserRequest` → removes null passwords before validation

### Frontend Stack

- **UI Library:** `@nuxt/ui` v2 — use UCard, UButton, UTable, UBadge, UAvatar, UProgress, UIcon, UDropdown components
- **Dark mode:** enabled by default
- **Types:** defined in `frontend/types/models.ts` — `User`, `Profile`, `Course`, `Plan`, `Path`, `Job`, `Role`, pagination/API response types

### Frontend Composables

Located in `frontend/composables/`:

| Composable | Purpose |
|------------|---------|
| `useAuth` | Login/logout, user state, role checks (`isAdmin`, `isClient`) |
| `useApi` | Base `$fetch` wrapper with credentials and CSRF header |
| `useUsers` | CRUD operations for users |
| `useCourses`, `usePlans`, `useJobs`, `usePaths` | Domain-specific API operations |

Pattern: each returns `readonly()` reactive refs (`data`, `loading`, `error`) and async methods with try/catch error extraction.

### Frontend Layouts & Middleware

Layouts: `admin`, `auth`, `default`.

- `auth.ts` middleware — redirects unauthenticated users to login
- `guest.ts` middleware — redirects authenticated users to dashboard

### Database Seeding Order

1. `RoleSeeder` — creates admin, client, consultant roles
2. `UserSeeder` — creates initial users (runs `RoleSeeder` internally)
3. `CoursesTableSeeder` — seeds courses
4. Optional: `ClientsSeeder` — interactive prompt for fake clients

`UserFactory` uses `afterCreating()` to auto-create a `Profile` and assign the `client` role.

## Important Patterns

**Authorization in Services:** Services check permissions and throw `AuthorizationException` (e.g., preventing self-deletion, protecting last admin). Controllers catch it and return 403. `AuthenticationException` returns 401.

**File Uploads:** `FileUploadService` handles image storage/replacement. Profile images in `storage/app/public/profile_images/` (public disk). Max 2048KB, jpeg/jpg/png only.

**Testing:** Feature tests use `RefreshDatabase` and call `RoleSeeder` in `setUp()`. PHPUnit 11 with a separate test database config.

**Search:** `User` model uses Laravel Scout (Algolia) — `toSearchableArray()` indexes `id`, `fullname`, and `email`. Requires `SCOUT_QUEUE=true` in env.
