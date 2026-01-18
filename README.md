# CODECV

A SaaS platform for career development that helps users track their learning paths, manage training roadmaps, and discover job opportunities.

## Overview

CODECV connects consultants with clients to provide structured career development through:

- **Learning Paths** - Curated sequences of courses to achieve career goals
- **Training Plans** - Personalized roadmaps created by consultants for their clients
- **Job Board** - Career opportunities posted by consultants for their network
- **Progress Tracking** - Visual tracking of course completion and learning milestones

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 11 (PHP 8.3) |
| Frontend | Nuxt 3 (Vue 3, TypeScript) |
| Database | MySQL 8.0 |
| Auth | Laravel Sanctum |
| Search | Algolia (Laravel Scout) |
| Dev Environment | Lando |

## Getting Started

### Prerequisites

- [Lando](https://lando.dev/) (Docker-based local development)
- Node.js 18+
- Composer

### Installation

```bash
# Clone the repository
git clone https://github.com/Leopoldo-Medeiros/codecv6.git
cd codecv6

# Start the development environment
lando start

# Install PHP dependencies
composer install

# Copy environment file and generate key
cp .env.example .env
lando artisan key:generate

# Run migrations and seed the database
lando artisan migrate --seed

# Install frontend dependencies
cd frontend && npm install
```

### Running the Application

```bash
# Backend runs automatically via Lando
# Access at: http://codecv6.lndo.site

# Start frontend development server
cd frontend && npm run dev
# Access at: http://localhost:3000
```

### Default Users

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@admin.com | password |
| Consultant | consultant@consultant.com | password |
| Client | client@client.com | password |

## Development

### Backend Commands

```bash
lando artisan migrate              # Run migrations
lando artisan db:seed              # Seed database
lando artisan test                 # Run tests
./vendor/bin/pint                  # Format code
lando artisan route:list           # List routes
```

### Frontend Commands

```bash
cd frontend
npm run dev                        # Development server
npm run build                      # Production build
npm run preview                    # Preview production build
```

## Architecture

### Backend (Laravel)

```
app/
├── Http/Controllers/Api/    # API controllers (JSON responses)
├── Http/Controllers/        # Web controllers (Blade views)
├── Http/Requests/           # Form validation
├── Http/Resources/          # API response transformers
├── Models/                  # Eloquent models
├── Services/                # Business logic layer
└── Enums/                   # Role definitions
```

### Frontend (Nuxt)

```
frontend/
├── pages/                   # File-based routing
├── composables/             # Reusable API logic (useAuth, useUsers, etc.)
├── components/              # Vue components
├── layouts/                 # Page layouts (default, admin, auth)
├── middleware/              # Route guards (auth, guest)
└── types/                   # TypeScript definitions
```

### User Roles

| Role | Permissions |
|------|-------------|
| **Admin** | Full system access, user management |
| **Consultant** | Create plans, paths, courses, job postings; manage clients |
| **Client** | View assigned plans, track progress, browse jobs |

## API Endpoints

### Authentication
- `POST /api/login` - Login
- `POST /api/register` - Register
- `POST /api/logout` - Logout
- `GET /api/me` - Current user

### Resources (CRUD)
- `/api/users` - User management
- `/api/courses` - Course management
- `/api/plans` - Training plans
- `/api/paths` - Learning paths
- `/api/jobs` - Job listings
- `/api/roles` - Available roles

## Testing

```bash
# Run all tests
lando artisan test

# Run specific test file
lando php vendor/bin/phpunit tests/Feature/LoginTest.php

# Run with coverage
lando php vendor/bin/phpunit --coverage-html coverage
```

## License

This project is proprietary software.
