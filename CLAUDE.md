# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Full-stack application: Laravel 11 (backend) + Nuxt 4 (frontend). Platform for managing courses, learning paths, job listings, and consultant-client relationships with role-based access control.

## Language Policy

**CRITICAL**: This project has specific language requirements:
- All **explanations and communication** must be in **Portuguese-BR** or/and **English**
- All **code, documentation, comments, identifiers, filenames, and commit messages** must be in **English**
- All **marketing messages, release notes, and internal announcements** must be in **English**

## Development Commands

### DDEV (Local Environment)

```bash
ddev start                     # Start environment
ddev stop                      # Stop environment
ddev ssh                       # SSH into web container
```

**Environment:** PHP 8.4, MySQL 8.0, Nginx, Node.js 24, Redis, Xdebug enabled
**URLs:** API at `https://codecv6.localhost.ddev.site/api`, Frontend at `https://codecv6.localhost.ddev.site:3000`

### Backend (Laravel)

```bash
ddev composer install                                   # Install dependencies
ddev artisan migrate                                    # Run migrations
ddev artisan migrate:fresh                              # Fresh database
ddev artisan db:seed                                    # Run all seeders
ddev artisan db:seed --class=RoleSeeder                 # Specific seeder
ddev exec ./vendor/bin/pint                             # Code formatting
ddev php vendor/bin/phpunit                             # Run all tests
ddev php vendor/bin/phpunit --testsuite=Feature         # Feature tests only
ddev php vendor/bin/phpunit tests/Feature/ExampleTest.php  # Single test
ddev artisan cache:clear && ddev artisan config:clear && ddev artisan route:clear  # Clear caches
ddev artisan storage:link                               # Link public storage
```

### Frontend (Nuxt 4)

```bash
ddev npm install               # Install dependencies
ddev nuxt                      # Dev server at https://codecv6.localhost.ddev.site:3000
ddev npm run build             # Production build
```

`ddev nuxt` loads `.env` from the project root via `--dotenv`, providing `NUXT_PUBLIC_API_BASE` and `DDEV_HOSTNAME` automatically.

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
- `SANCTUM_STATEFUL_DOMAINS` must include both the base domain and `:3000` port variant

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

### Frontend Stack (Nuxt 4)

- **Directory structure:** Application code lives in `frontend/app/` (Nuxt 4 convention)
- **UI Library:** `@nuxt/ui` v2 — use UCard, UButton, UTable, UBadge, UAvatar, UProgress, UIcon, UDropdown components
- **Dark mode:** enabled by default
- **Types:** defined in `frontend/app/types/models.ts` — `User`, `Profile`, `Course`, `Plan`, `Path`, `Job`, `Role`, pagination/API response types
- **SSR:** disabled (`ssr: false`) — runs as a client-side SPA

### Frontend Composables

Located in `frontend/app/composables/`:

| Composable | Purpose |
|------------|---------|
| `useAuth` | Login/logout, user state, role checks (`isAdmin`, `isClient`) |
| `useApi` | Base `$fetch` wrapper with credentials and CSRF header |
| `useUsers` | CRUD operations for users |
| `useCourses`, `usePlans`, `useJobs`, `usePaths` | Domain-specific API operations |
| `useMyClients` | Consultant client management |
| `useNotifications` | Notification state management |

Pattern: each returns `readonly()` reactive refs (`data`, `loading`, `error`) and async methods with try/catch error extraction.

### Frontend Layouts & Middleware

Layouts: `admin`, `auth`, `default`, `marketing`.

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

**DDEV Nuxt Config:** `frontend/nuxt.config.ts` uses `process.env.DDEV_HOSTNAME` (auto-injected by DDEV inside the container) with fallback to `codecv6.localhost.ddev.site`. The Vite HMR is configured for WSS through DDEV's HTTPS reverse proxy.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4
- laravel/fortify (FORTIFY) - v1
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v4
- laravel/scout (SCOUT) - v10
- laravel/socialite (SOCIALITE) - v5
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v11

## Skills Activation

This project has domain-specific skills available. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

- `fortify-development` — ACTIVATE when the user works on authentication in Laravel. This includes login, registration, password reset, email verification, two-factor authentication (2FA/TOTP/QR codes/recovery codes), profile updates, password confirmation, or any auth-related routes and controllers. Activate when the user mentions Fortify, auth, authentication, login, register, signup, forgot password, verify email, 2FA, or references app/Actions/Fortify/, CreateNewUser, UpdateUserProfileInformation, FortifyServiceProvider, config/fortify.php, or auth guards. Fortify is the frontend-agnostic authentication backend for Laravel that registers all auth routes and controllers. Also activate when building SPA or headless authentication, customizing login redirects, overriding response contracts like LoginResponse, or configuring login throttling. Do NOT activate for Laravel Passport (OAuth2 API tokens), Socialite (OAuth social login), or non-auth Laravel features.
- `laravel-best-practices` — Apply this skill whenever writing, reviewing, or refactoring Laravel PHP code. This includes creating or modifying controllers, models, migrations, form requests, policies, jobs, scheduled commands, service classes, and Eloquent queries. Triggers for N+1 and query performance issues, caching strategies, authorization and security patterns, validation, error handling, queue and job configuration, route definitions, and architectural decisions. Also use for Laravel code reviews and refactoring existing Laravel code to follow best practices. Covers any task involving Laravel backend PHP code patterns.
- `scout-development` — Develops full-text search with Laravel Scout. Activates when installing or configuring Scout; choosing a search engine (Algolia, Meilisearch, Typesense, Database, Collection); adding the Searchable trait to models; customizing toSearchableArray or searchableAs; importing or flushing search indexes; writing search queries with where clauses, pagination, or soft deletes; configuring index settings; troubleshooting search results; or when the user mentions Scout, full-text search, search indexing, or search engines in a Laravel project. Make sure to use this skill whenever the user works with search functionality in Laravel, even if they don't explicitly mention Scout.
- `socialite-development` — Manages OAuth social authentication with Laravel Socialite. Activate when adding social login providers; configuring OAuth redirect/callback flows; retrieving authenticated user details; customizing scopes or parameters; setting up community providers; testing with Socialite fakes; or when the user mentions social login, OAuth, Socialite, or third-party authentication.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.
- To check environment variables, read the `.env` file directly.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

## Laravel 12 Structure

- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app/Console/Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

</laravel-boost-guidelines>
