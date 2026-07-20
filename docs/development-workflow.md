# Development Workflow (codecv6)

End-to-end workflow for developing on codecv6 (Laravel 12 + Nuxt 4 + DDEV).

## Prerequisites

- **DDEV** (latest stable) — https://ddev.readthedocs.io/en/stable/
- **Git**
- **Node.js 24+** (for running the frontend on host; DDEV ships its own Node inside the container)
- **PHP 8.4+** is provided by the DDEV web container; you don't need it on the host

## Initial Setup

```bash
# 1. Clone
git clone <repo-url> codecv6
cd codecv6

# 2. Configure DDEV (config.yaml is already in .ddev/)
ddev start

# 3. Backend dependencies + app key
ddev composer install
cp .env.example .env
ddev artisan key:generate

# 4. Database
ddev artisan migrate:fresh --seed

# 5. Storage symlink (for profile images)
ddev artisan storage:link

# 6. Frontend dependencies (host — default for native binaries)
cd frontend
cp .env.example .env
npm install
npm run dev
```

You'll have:
- Backend API: `http://codecv6.ddev.site` (HTTPS works too but the dev TLS cert is self-signed — see `docs/troubleshooting.md`)
- Frontend: `http://localhost:3001` (or `http://<LAN-IP>:3001`)
- Mailpit: shown by `ddev describe`

## Default Seeded Users

| Role | Email | Password |
|---|---|---|
| Admin | `admin@admin.com` | `password` |
| Consultant | `consultant@consultant.com` | `password` |
| Client | `client@client.com` | `password` |

## Project Structure

```
codecv6/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/    # Thin controllers
│   │   ├── Requests/           # FormRequest validation
│   │   └── Resources/          # API response transformers
│   ├── Models/                 # Eloquent models
│   ├── Services/               # Business logic
│   ├── Enums/                  # PHP 8.1+ enums (RoleEnum, PaymentTier, ChallengeDifficulty)
│   └── Notifications/          # Database + email notifications
├── config/                     # Laravel config
├── database/
│   ├── migrations/
│   ├── seeders/                # RoleSeeder, UserSeeder, CoursesTableSeeder, ClientsSeeder
│   └── factories/
├── docs/                       # This documentation
├── frontend/
│   ├── app/                    # Nuxt 4 app dir
│   │   ├── components/         # Vue components
│   │   ├── composables/        # Composables (useAuth, useApi, useCourses, ...)
│   │   ├── layouts/            # admin, auth, default, marketing
│   │   ├── middleware/         # auth.ts, guest.ts
│   │   ├── pages/              # File-routed pages
│   │   ├── plugins/            # Nuxt plugins
│   │   └── types/              # TypeScript types (models.ts)
│   ├── public/                 # Static assets (PNG, OG image, favicons)
│   ├── nuxt.config.ts
│   └── package.json            # NB: Tailwind v3 pinned via overrides
├── routes/
│   ├── api.php                 # API routes (grouped by auth + role)
│   ├── web.php                 # Mostly empty — Nuxt handles the frontend
│   └── console.php
├── tests/
│   ├── Feature/Api/
│   └── Feature/Auth/
├── CLAUDE.md                   # Project guidance for Claude Code
├── CLAUDE.local.md             # Local-only (gitignored)
└── .ddev/                      # DDEV config
```

## Git Workflow

### Branching

- `main` — production-ready
- `feature/<name>` — new features
- `fix/<name>` — bug fixes
- `hotfix/<name>` — production hotfixes

```bash
git checkout main
git pull origin main
git checkout -b feature/path-progress-notifications
```

### Conventional Commits

Format: `<type>(<scope>): <subject>`

Types: `feat`, `fix`, `docs`, `style`, `refactor`, `perf`, `test`, `build`, `ci`, `chore`

Scopes commonly used in codecv6: `auth`, `api`, `payments`, `paths`, `challenges`, `notifications`, `marketing`, `frontend`, `seo`, `cv-analysis`, `linkedin-analyser`

```bash
git commit -m "feat(paths): notify consultant when client completes a path"
git commit -m "fix(auth): handle revoked Sanctum token on /me endpoint"
git commit -m "docs: add troubleshooting entry for Stripe webhook signature"
```

For complex commits with a body:
```bash
git commit -m "$(cat <<'EOF'
feat(payments): support Stripe subscription tier MENTORSHIP

Adds recurring subscription support to checkout flow. The MENTORSHIP
tier is created as a Stripe Subscription (not a one-time Checkout
Session). The webhook now handles invoice.payment_succeeded in
addition to checkout.session.completed.
EOF
)"
```

### Before Committing

1. `ddev exec vendor/bin/pint --dirty` — format PHP
2. `ddev artisan test --compact` — run backend tests
3. Manually exercise the affected UI in the browser
4. Review the diff: `git diff`

## Day-to-Day Commands

### Backend

```bash
ddev artisan migrate                              # Run pending migrations
ddev artisan migrate:fresh --seed                 # Reset DB with seed data
ddev artisan db:seed --class=RoleSeeder           # Run specific seeder
ddev artisan tinker                               # Interactive REPL
ddev artisan route:list --path=api                # Inspect API routes
ddev artisan pail                                 # Live log tail
ddev artisan cache:clear                          # After .env or config changes
ddev artisan config:clear
ddev artisan route:clear
ddev artisan storage:link                         # If storage symlink missing
ddev exec vendor/bin/pint --dirty                 # Format changed PHP files
ddev artisan test --compact                       # Run all tests
ddev artisan test --compact tests/Feature/Api/CourseApiTest.php
ddev artisan test --compact --filter=test_admin_can_create_course
```

### Frontend (Host — default)

```bash
cd frontend
npm install                                       # Install deps
npm run dev                                       # Dev server on localhost:3001
npm run build                                     # Production build
npm run generate                                  # Static export to dist/
```

### Frontend (Container — alternative)

```bash
ddev npm install                                  # Populates container node_modules volume
ddev nuxt                                         # Dev server inside container
```

### DDEV

```bash
ddev start
ddev stop
ddev restart
ddev ssh                                          # SSH into web container
ddev describe                                     # URLs, ports, container status
ddev logs
ddev logs -s db
ddev poweroff                                     # Stop all DDEV projects globally
```

### Stripe (local webhook forwarding)

```bash
stripe listen --forward-to codecv6.ddev.site/api/webhooks/stripe
# Copy the printed whsec_... into .env as STRIPE_WEBHOOK_SECRET
ddev artisan config:clear
```

## Feature Development Workflow

### Backend Feature

1. **Plan the data model** — migration first
2. **Migration + Model + Factory**:
   ```bash
   ddev artisan make:migration create_widgets_table
   ddev artisan make:model Widget --factory
   ddev artisan migrate
   ```
3. **FormRequest** for validation:
   ```bash
   ddev artisan make:request WidgetRequest
   ```
4. **Service** for business logic — put it in `app/Services/WidgetService.php`
5. **Resource** for API response:
   ```bash
   ddev artisan make:resource WidgetResource
   ```
6. **Controller** that orchestrates:
   ```bash
   ddev artisan make:controller Api/WidgetController --api
   ```
7. **Route** in `routes/api.php` under the correct middleware group
8. **Tests** in `tests/Feature/Api/WidgetApiTest.php`
9. Run tests: `ddev artisan test --compact`
10. Run Pint: `ddev exec vendor/bin/pint --dirty`

### Frontend Feature

1. **Add type** to `frontend/app/types/models.ts`
2. **Composable** in `frontend/app/composables/useWidgets.ts`:
   - `data`, `loading`, `error` reactive refs (returned as `readonly()`)
   - `fetchWidgets`, `createWidget`, `updateWidget`, `deleteWidget` methods
3. **Page** in `frontend/app/pages/widgets/index.vue`:
   - `definePageMeta({ middleware: 'auth' })` if auth-required
   - Use `@nuxt/ui` v2 components (`UCard`, `UTable`, ...)
4. **Manually test** the golden path + error states in the browser
5. **Check the Network tab** to verify API calls hit the right endpoint

## Code Review Checklist

Before opening a PR:

### Backend
- [ ] Route gated by correct middleware (`auth:sanctum`, `role:...`)
- [ ] FormRequest validates all inputs
- [ ] Service holds business logic (controller stays thin)
- [ ] Service throws `AuthorizationException` for business-rule violations
- [ ] Resource guards relationships with `$this->when($this->relationLoaded(...))`
- [ ] Eager-loading in controller / service prevents N+1
- [ ] Feature tests cover success + 401 + 403 + 422 cases
- [ ] `ddev exec vendor/bin/pint --dirty` clean

### Frontend
- [ ] No direct `$fetch` / `fetch` — always via a composable
- [ ] Loading + error states rendered
- [ ] TypeScript strict (no `any` without justification)
- [ ] Marketing pages use `.mkt-*` (not `@nuxt/ui`)
- [ ] No purple in marketing CSS
- [ ] Tested in browser, not just type-checked

## Deployment

(Add deployment-specific steps here when the production deploy pipeline is finalised. For now: `npm run generate` produces a static frontend export in `frontend/dist/`. Backend is deployed however the team chooses — Forge, custom VPS, etc.)

## Additional Resources

- Project guidance: `/CLAUDE.md`
- Local overrides: `/CLAUDE.local.md`
- Agent definitions: `/.claude/agents/`
- Frontend conventions: `/frontend/CLAUDE.md`
- Authentication: `/docs/authentication.md`
- Environment variables: `/docs/environment-variables.md`
- Testing: `/docs/testing-guide.md`
- Troubleshooting: `/docs/troubleshooting.md`
