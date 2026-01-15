# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a full-stack application combining Laravel 11 (backend) with Nuxt 3 (frontend). The application provides a platform for managing courses, learning paths, job listings, and consultant-client relationships with role-based access control.

## Development Environment

### Local Development with Lando

This project uses Lando for local development environment:

```bash
# Start the development environment
lando start

# Stop the environment
lando stop

# Restart services
lando restart

# SSH into the appserver
lando ssh
```

**Environment specs:**
- PHP 8.3
- MySQL 8.0
- Nginx web server
- Xdebug enabled

### Backend (Laravel)

```bash
# Install dependencies
composer install

# Run migrations
lando artisan migrate

# Run migrations with fresh database
lando artisan migrate:fresh

# Run database seeders
lando artisan db:seed

# Run specific seeder
lando artisan db:seed --class=RoleSeeder

# Code formatting with Laravel Pint
./vendor/bin/pint

# Run tests
lando php vendor/bin/phpunit

# Run specific test suite
lando php vendor/bin/phpunit --testsuite=Feature

# Run single test file
lando php vendor/bin/phpunit tests/Feature/ExampleTest.php

# Clear cache
lando artisan cache:clear
lando artisan config:clear
lando artisan route:clear
lando artisan view:clear

# Generate application key
lando artisan key:generate
```

### Frontend (Nuxt 3)

The frontend is located in the `/frontend` directory:

```bash
# Install dependencies
cd frontend && npm install

# Run development server
npm run dev
# Frontend runs on http://localhost:3000

# Build for production
npm run build

# Preview production build
npm run preview
```

## Architecture

### Backend Structure

**Authentication & Authorization:**
- Uses Laravel Sanctum for API token authentication
- Spatie Laravel Permission package for role-based access control
- Three primary roles: `admin`, `client`, `consultant`
- Middleware aliases: `role`, `permission`, `role_or_permission`

**Core Domain Models:**
- `User` - Central user model with HasRoles trait and Scout searchable
- `Profile` - One-to-one with User, stores personal info and social links
- `Course` - Soft-deletable courses owned by users
- `Plan` - Subscription plans owned by consultants with many-to-many relationships to users (clients), courses, and paths
- `Path` - Learning paths
- `Job` - Job listings

**Key Relationships:**
- User has one Profile
- Courses belong to Users (creators)
- Plans belong to Users (consultants via `consultant_id`)
- Plans have many clients (users) via `plan_user` pivot
- Plans have many courses via `course_plan` pivot
- Plans have many paths via `path_plan` pivot

**API Routes (`routes/api.php`):**
- Public: `/api/login`, `/api/logout`
- Protected (auth:sanctum): `/api/users/*`, `/api/roles`
- All API routes are prefixed with `/api`

**Web Routes (`routes/web.php`):**
- Public pages: home, pricing, payment, about-us, faqs, register, login
- Authenticated: dashboard, profile management
- Role-protected: admin routes for user/course management, client dashboard

**Search Integration:**
- Laravel Scout configured with Algolia
- User model is searchable (fullname, email)

### Frontend Structure

**Framework:** Nuxt 3 with Vue 3

**Key Pages:**
- `/pages/index.vue` - Landing page
- `/pages/login.vue` - Authentication
- `/pages/dashboard.vue` - User dashboard
- `/pages/profile.vue` - User profile management
- `/pages/about.vue` - About page
- `/pages/pricing.vue` - Pricing information
- `/pages/payment.vue` - Payment flow
- `/pages/faqs.vue` - FAQ page
- `/pages/courses/*` - Course-related pages
- `/pages/users/*` - User management pages

**Configuration:**
- Dev server runs on `localhost:3000`
- File-based routing enabled via `pages: true`

### Database Seeding

The `DatabaseSeeder` runs in this order:
1. `RoleSeeder` - Creates admin, client, consultant roles
2. `UserSeeder` - Creates initial users
3. `CoursesTableSeeder` - Seeds courses
4. Optional: `ClientsSeeder` - Generates fake clients (interactive prompt)

### CORS Configuration

CORS is configured to allow all origins in `config/cors.php`:
- Paths: `api/*`, `sanctum/*`, `login`, `logout`
- Credentials support enabled for Sanctum authentication

## Important Notes

**Migration Considerations:**
- There's a migration to drop the `course_plan` table (`2026_01_15_211435_drop_course_plan_table.php`)
- Multiple migrations handle the `consultant_id` addition to users table
- LinkedIn column was added to profiles, replacing Twitter field

**Authentication Flow:**
- API uses Sanctum token authentication
- Web uses session-based authentication
- AuthController handles both API login (returns token) and web logout

**User Consultant Relationship:**
- Users can have a `consultant_id` field linking them to a consultant (another user)
- Plans are owned by consultants and subscribed to by clients

**Testing:**
- PHPUnit configured with separate Unit and Feature test suites
- Test environment uses array cache and sync queue
- Database connection for tests can be configured (currently commented out)
