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
| Backend | Laravel 13 (PHP 8.4) |
| Frontend | Nuxt 4 (Vue 3, TypeScript) |
| Database | MySQL 8.0 |
| Auth | Laravel Sanctum |
| Search | Algolia (Laravel Scout) |
| Dev Environment | DDEV |

## Getting Started

### Prerequisites

- Docker Desktop or [OrbStack](https://orbstack.dev/) (recommended for macOS)
- [DDEV](https://ddev.readthedocs.io/en/stable/) — install with:

```bash
# macOS (Homebrew)
brew install ddev/ddev/ddev

# Linux (install script)
curl -fsSL https://ddev.com/install.sh | bash

# Windows (Chocolatey)
choco install ddev
```

After installing, verify with `ddev version`.

### Installation

```bash
# Clone the repository
git clone https://github.com/Leopoldo-Medeiros/codecv6.git
cd codecv6

# Start the development environment
ddev start

# Install PHP dependencies
ddev composer install

# Copy environment file and generate key
cp .env.example .env
ddev artisan key:generate

# Run migrations and seed the database
ddev artisan migrate --seed

# Install frontend dependencies (host — default)
cd frontend && npm install && cd ..
```

### Running the Application

```bash
# Backend runs automatically via DDEV
# Access at: http://codecv6.ddev.site

# Start frontend development server on the host
cd frontend
npm run dev
# Access at: http://localhost:3000 (or your LAN IP — see frontend/CLAUDE.md)
```

The default frontend workflow runs on the **host** (native macOS/Linux node), loading `frontend/.env` automatically. `ddev nuxt` is also supported as an alternative for running inside the container — see `CLAUDE.md` for details.

### Default Users

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@admin.com | password |
| Consultant | consultant@consultant.com | password |
| Client | client@client.com | password |

## Development

### Backend Commands

```bash
ddev artisan migrate              # Run migrations
ddev artisan db:seed              # Seed database
ddev artisan test --compact       # Run tests
ddev exec vendor/bin/pint --dirty # Format changed PHP files
ddev artisan route:list           # List routes
ddev artisan config:clear         # Clear config cache
```

### Frontend Commands

```bash
# Host (default)
cd frontend
npm run dev        # Development server
npm run build      # Server build → frontend/.output/
npm run generate   # Static build → frontend/dist/
npm run preview    # Preview a built app locally
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

### Frontend (Nuxt 4)

```
frontend/
├── app/                     # Application source (Nuxt 4 convention)
│   ├── pages/               # File-based routing
│   ├── composables/         # Reusable API logic (useAuth, useUsers, etc.)
│   ├── components/          # Vue components
│   ├── layouts/             # Page layouts (default, admin, auth, marketing)
│   ├── middleware/          # Route guards (auth, guest)
│   ├── types/               # TypeScript definitions
│   └── assets/              # CSS and static assets processed by Vite
├── public/                  # Static files served directly
├── server/                  # Nitro server configuration
├── nuxt.config.ts           # Nuxt configuration
├── tailwind.config.js       # Tailwind CSS configuration
└── package.json             # Dependencies
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
ddev artisan test --compact

# Run specific test file
ddev artisan test --compact tests/Feature/Api/UserApiTest.php

# Filter by test name
ddev artisan test --compact --filter=testName
```

## License

This project is proprietary software.
