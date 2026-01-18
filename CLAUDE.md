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

Middleware aliases: `role`, `permission`, `role_or_permission`

### Core Domain Models

| Model | Key Relationships |
|-------|------------------|
| `User` | Has one `Profile`, has roles, optionally has `consultant_id` (FK to another User) |
| `Profile` | Belongs to User, stores social links (github, linkedin, etc.) |
| `Plan` | Belongs to consultant (User), has many clients (users), courses, paths via pivots |
| `Course` | Soft-deletable, belongs to User (creator) |
| `Path` | Learning paths |
| `Job` | Job listings |

### API Authentication

- **Sanctum token auth** for API endpoints
- Base URL: `http://codecv6.lndo.site/api`
- CSRF cookie endpoint: `/sanctum/csrf-cookie` (called before POST/PUT/DELETE)
- Token stored in localStorage as `auth_token`

### Frontend Composables

Located in `frontend/composables/`:

| Composable | Purpose |
|------------|---------|
| `useAuth` | Login/logout, user state, role checks (`isAdmin`, `isClient`) |
| `useApi` | Base API client wrapping `$fetch` with credentials |
| `useUsers` | CRUD operations for users |
| `useCourses`, `usePlans`, `useJobs`, `usePaths` | Domain-specific API operations |

Pattern: Each composable returns reactive refs (`users`, `loading`, `error`) and async methods.

### Frontend Middleware

- `auth.ts` - Protects authenticated routes
- `guest.ts` - Redirects authenticated users away from login/register

### Database Seeding Order

1. `RoleSeeder` - Creates admin, client, consultant roles
2. `UserSeeder` - Creates initial users
3. `CoursesTableSeeder` - Seeds courses
4. Optional: `ClientsSeeder` - Interactive prompt for fake clients

## Important Patterns

**Authorization in Services:** Services check permissions and throw `AuthorizationException` (e.g., preventing self-deletion, protecting last admin).

**File Uploads:** `FileUploadService` handles image storage/replacement. Profile images stored in `storage/app/public/profile_images/`.

**API Responses:** Use Laravel API Resources for consistent JSON structure. Collections include pagination meta.
