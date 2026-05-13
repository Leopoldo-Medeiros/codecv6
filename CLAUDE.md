# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Full-stack application: Laravel 12 (backend) + Nuxt 4 (frontend). SaaS platform connecting consultants with clients to manage career development through courses, learning paths, job listings, and personalized training plans.

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
**URLs:** API at `http://codecv6.ddev.site` (HTTPS also works but the dev TLS cert needs to be trusted by the browser), Frontend at `http://192.168.1.39:3000` (or your LAN IP — see Frontend section)

### Backend (Laravel)

```bash
ddev composer install                                                              # Install dependencies
ddev artisan migrate                                                               # Run migrations
ddev artisan migrate:fresh --seed                                                  # Fresh database with seeds
ddev artisan db:seed --class=RoleSeeder                                            # Specific seeder
ddev exec vendor/bin/pint --dirty                                                  # Format changed PHP files
ddev artisan test --compact                                                        # Run all tests
ddev artisan test --compact tests/Feature/Api/UserApiTest.php                      # Single test file
ddev artisan test --compact --filter=testName                                      # Filter by test name
ddev artisan cache:clear && ddev artisan config:clear && ddev artisan route:clear  # Clear caches
ddev artisan storage:link                                                          # Link public storage
```

### Frontend (Nuxt 4)

The frontend runs **on the host** (macOS/Linux native node) by default. The DDEV container has a separate, isolated `node_modules` (via `.ddev/docker-compose.frontend.yaml` named volume) so both can coexist, but `ddev nuxt` requires populating that volume separately.

```bash
# Host (default — fast, native binaries, no Docker for the frontend)
cd frontend
npm install                    # Install macOS/host dependencies
npm run dev                    # Dev server at http://localhost:3000 / http://<LAN-IP>:3000
npm run generate               # Static build for deploy (outputs to frontend/dist/)

# DDEV (alternative — Linux container, separate node_modules)
ddev npm install               # Populates the named volume (Linux x64 binaries)
ddev nuxt                      # Dev server inside the container
```

**Frontend env:** `frontend/.env` is loaded automatically by `npm run dev`. Required at minimum:
```
NUXT_PUBLIC_API_BASE=http://codecv6.ddev.site
```

The `ddev nuxt` path loads the project-root `.env` instead via `--dotenv ../.env`.

**Why HTTP for the API base?** DDEV serves both HTTP and HTTPS, but the dev TLS cert is self-signed. Browsers running on the host won't trust it without `mkcert -install`, causing `Failed to fetch` errors on `/sanctum/csrf-cookie`. HTTP avoids this entirely in development.

**LAN IP vs localhost:** The dev server listens on `0.0.0.0:3000`. Use the LAN IP (e.g. `192.168.1.39:3000`) when testing from another device, or whatever `npm run dev` prints. `localhost:3000` may be occupied by another process — check first.

### Default Seeded Users

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@admin.com | password |
| Consultant | consultant@consultant.com | password |
| Client | client@client.com | password |

## Architecture

### Backend Service Layer Pattern

Controllers use dependency injection with Service classes that handle business logic:

```
Controller → Service → Model
```

- **Controllers** (`app/Http/Controllers/Api/`): Handle HTTP, validation, return Resources. Legacy web controllers exist at root but are not actively developed.
- **Services** (`app/Services/`): Business logic, authorization checks, file uploads. No abstract base — each is standalone.
- **Resources** (`app/Http/Resources/`): Transform models for API responses
- **Requests** (`app/Http/Requests/`): Form request validation

### Roles & Permissions

Uses Spatie Laravel Permission with `RoleEnum` (`app/Enums/RoleEnum.php`):
- `admin` (id: 1)
- `client` (id: 2)
- `consultant` (id: 3)

`RoleEnum::fromId(int $id)` provides type-safe role lookup. Middleware aliases: `role`, `permission`, `role_or_permission`.

### API Route Access Levels

Routes in `routes/api.php` are grouped by access:
- **No auth (signature-verified)**: `GET /email/verify/{id}/{hash}` — email verification link from Fortify emails; throttled 6/min; redirects to `APP_FRONTEND_URL/dashboard?verified=1`
- **No auth (Stripe signature)**: `POST /webhooks/stripe` — Stripe webhook; verified via `STRIPE_WEBHOOK_SECRET`, no CSRF
- **Public** (rate-limited 5/min): `/login`, `/register`, `/forgot-password`, `/reset-password`
- **All authenticated**: Read paths, courses, plans, jobs; update own profile; CV/LinkedIn analysis; Stripe checkout; challenges (`GET /challenges`, `GET /challenges/{slug}`, `POST /challenges/{slug}/run`); notifications (`GET /notifications`, `PATCH /notifications/{id}/read`, `PATCH /notifications/read-all`)
- **Admin + Consultant**: Create/edit/delete courses, plans, paths; manage path steps
- **Consultant only**: `/my-clients` — view clients, assign/remove paths
- **Admin only**: User CRUD, role management, consultant assignment, GDPR export/erase

### Core Domain Models

| Model | Key Relationships |
|-------|------------------|
| `User` | Has one `Profile`, has roles via Spatie, self-references via `consultant_id` (FK): `consultant()` BelongsTo + `clients()` HasMany |
| `Profile` | Belongs to User, stores social links (github, linkedin, instagram, facebook, website) |
| `Plan` | Belongs to consultant (User), has many clients (users) and paths via pivots (`plan_user`, `path_plan`) |
| `Course` | Soft-deletable, belongs to User (creator) |
| `Path` | Learning paths, soft-deletable; has ordered `PathStep`s |
| `PathStep` | Ordered steps within a Path; type can be `reading`, `lab`, or `challenge`; `challenge_slug` FK links to `Challenge`; tracked per-user via `UserStepProgress` |
| `Challenge` | Coding challenge with `slug` (unique), `boilerplate_code`, `tests_code`, `ChallengeDifficulty` enum; soft-deletable; `is_premium` + `price_eur` for monetization |
| `Job` | Job listings, soft-deletable |
| `Payment` | Stripe payment record; created `PENDING` on checkout, updated to `PAID` by webhook; belongs to `User` |
| `UserStepProgress` | Tracks per-user completion of individual `PathStep`s; `status` can be `not_started`, `in_progress`, `completed` |

`Course`, `Path`, `Job`, `Plan`, and `Challenge` all use `SoftDeletes`. Services manage pivot relationships with `.sync()` (e.g., `PlanService` manages `plan_user` and `path_plan`; uses `syncWithoutDetaching` to append paths).

### API Authentication

- **Sanctum token auth** for API endpoints
- CSRF cookie: call `/sanctum/csrf-cookie` before POST/PUT/DELETE (frontend does this automatically). Note that `/api/login` itself does NOT enforce CSRF (it's stateless + bearer-token), so the CSRF cookie is decorative for the login flow but required for cookie-based session endpoints
- Token stored in `localStorage` as `auth_token`; user object as `auth_user`
- Login response: `{ user, access_token, token_type }`
- `SANCTUM_STATEFUL_DOMAINS` must include the frontend host(s) and port variant(s)
- **CORS allow-list** is `FRONTEND_URL` in `.env` — comma-separated list of frontend origins (read by `config/cors.php`). Add any new origin here
- **Canonical frontend URL** is `APP_FRONTEND_URL` in `.env` — single, canonical URL used by backend redirects (OAuth callback, password reset emails). Must NOT be confused with `FRONTEND_URL`

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
- **UI Library:** `@nuxt/ui` v2 (emerald primary, slate gray) — use UCard, UButton, UTable, UBadge, UAvatar, UProgress, UIcon, UDropdown components in **authenticated/admin pages**. Marketing pages do NOT use `@nuxt/ui` — they use the custom `.mkt-*` design system (see Marketing Design System section)
- **Dark mode:** enabled by default for authenticated pages; marketing pages force light theme via `useColorMode()` in their layout
- **Types:** defined in `frontend/app/types/models.ts` — `User`, `Profile`, `Course`, `Plan`, `Path`, `Job`, `PathStep`, `Role`, `PaginatedResponse<T>`, `ApiResponse<T>`, auth types
- **SSR:** enabled globally (`ssr: true`) — authenticated app routes opt out via `ssr: false` in `routeRules` (SPA). Marketing/auth-public pages use `prerender: true` in `routeRules` (built once at deploy time, served as static HTML for SEO and social previews)
- **Tailwind:** pinned to v3 via `package.json` `overrides` because `@nuxt/ui` v2 is incompatible with Tailwind v4. Do not bump.

### Frontend Composables

Located in `frontend/app/composables/`:

| Composable | Purpose |
|------------|---------|
| `useAuth` | Login/logout, user state, role checks (`isAdmin`, `isClient`, `isConsultant`) |
| `useApi` | Base `$fetch` wrapper with credentials, CSRF header, Bearer token, 90s timeout |
| `useUsers` | CRUD operations for users |
| `useCourses`, `usePlans`, `useJobs`, `usePaths` | Domain-specific API operations |
| `useMyClients` | Consultant client management |
| `useNotifications` | Notification state management |
| `useAuthTheme` | Theme state management for auth/marketing pages |
| `useCheckout` | Stripe Checkout — `startCheckout(tier, currency)` → redirects to Stripe; `getStatus(sessionId)` polls payment status; `detectCurrency()` picks `eur`/`brl` from `navigator.language` |

Pattern: each returns `readonly()` reactive refs (`data`, `loading`, `error`) and async methods with try/catch error extraction.

### Frontend Pages of Note

Beyond standard CRUD pages, these require extra context:
- `onboarding.vue` — post-registration flow; guides new users through initial setup
- `payment.vue` — post-checkout landing, handles Stripe redirect
- `my-cv.vue` — triggers CV analysis via AI; calls the CV analysis API endpoint
- `linkedin-analyser.vue` — sends LinkedIn profile URL for AI scoring
- `pages/labs/` — experimental features area; not tied to production flows
- `auth/callback.vue` — OAuth redirect landing, exchanges code for token via `SocialAuthController`

### Frontend Layouts & Middleware

Layouts: `admin`, `auth`, `default`, `marketing`.

- `marketing.vue` — used by `/`, `/about`, `/pricing`, `/faqs`, `/terms`, `/privacy`, plus auth public pages (login/register/etc when wrapped explicitly). Inlines its own header (with SVG `{ }` logo) and footer — does NOT use the `Navbar.vue`/`Footer.vue` components (those serve `default`/`admin` layouts). Defines all `.mkt-*` design tokens at `:root`
- `auth.ts` middleware — redirects unauthenticated users to login
- `guest.ts` middleware — redirects authenticated users to dashboard

### Marketing Design System

Marketing pages (`pages/index.vue`, `about.vue`, `pricing.vue`, `faqs.vue`, `terms.vue`, `privacy.vue`) use a **custom CSS design system** rather than Tailwind or `@nuxt/ui`. Tokens live in `app/layouts/marketing.vue` `<style>` block under `.mkt`:

- **Color palette:** emerald — `--accent: #059669` (emerald-600), `--accent-hover: #047857`, `--bg-soft: #ECFDF5`. RGBA helpers: `--accent-light/-mid/-glow`. **Never use purple** (`#6b46e5`, `#7c3aed`) — was deliberately removed to avoid AmigosCode visual collision
- **Typography:** Plus Jakarta Sans (loaded via Google Fonts in the layout)
- **Class naming:** BEM-ish prefixed by section (`.hero__bg`, `.feat-card__icon`, `.fitem__num`, `.lh__title`)
- **Each marketing page** declares `definePageMeta({ layout: false })` and wraps content in `<NuxtLayout name="marketing">` explicitly (so the layout doesn't apply to other pages by accident)
- **Logo:** inline SVG with `{ }` text in monospace inside a folded-paper icon. Brand text is `CODE` + `<span class="mkt-logo__cv">CV</span>`

### Database Seeding Order

1. `RoleSeeder` — creates admin, client, consultant roles
2. `UserSeeder` — creates initial users (runs `RoleSeeder` internally)
3. `CoursesTableSeeder` — seeds courses
4. Optional: `ClientsSeeder` — interactive prompt for fake clients

`UserFactory` uses `afterCreating()` to auto-create a `Profile` and assign the `client` role.

## Important Patterns

**Authorization in Services:** Services throw `AuthorizationException` for business rule violations (self-deletion, protecting last admin, accessing another user's data). Controllers catch it and return 403. `AuthenticationException` returns 401.

**File Uploads:** `FileUploadService` handles image storage/replacement. Profile images in `storage/app/public/profile_images/` (public disk). Max 2048KB, jpeg/jpg/png only. Validation happens in the service layer, not middleware.

**Path Progress Tracking:** `UserStepProgress` tracks per-user completion of individual `PathStep`s. Steps are ordered via `.orderBy('order')` in the Path relationship. Users can only update their own progress (enforced in the controller).

**Testing:** Feature tests use `RefreshDatabase` and call `RoleSeeder` in `setUp()`. Tests live in `tests/Feature/Api/` (API tests) and `tests/Feature/Auth/`. PHPUnit 11 with a separate test database config.

**Search:** `User` model uses Laravel Scout (Algolia) — `toSearchableArray()` indexes `id`, `fullname`, and `email`. Requires `SCOUT_QUEUE=true` in env.

**Stripe Payments:** `StripeService` drives the checkout flow. Tiers and prices are defined in `config/pricing.php` — amounts are in **minor units** (cents for EUR, centavos for BRL). `PaymentTier` enum has `ACCELERATOR`, `BOOTCAMP` (one-time), and `MENTORSHIP` (recurring subscription). Flow: `POST /checkout/session` → `StripeService::createCheckoutSession()` creates a Stripe Checkout Session and records a `PENDING` `Payment` row → user is redirected to Stripe → `POST /webhooks/stripe` receives `checkout.session.completed` and marks the payment `PAID`. The webhook is excluded from CSRF middleware via `bootstrap/app.php`. Required env: `STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET`. For local testing use the Stripe CLI: `stripe listen --forward-to codecv6.ddev.site/api/webhooks/stripe`.

**Social Auth (Google OAuth):** `SocialAuthController` handles the provider redirect and callback. The frontend hits `/auth/google/redirect`, then the callback exchanges the code via `AuthService`. The `User` model has a `google_id` column. Requires `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, and `GOOGLE_REDIRECT_URI` in `.env`. The post-OAuth browser redirect uses `config('app.frontend_url')` (set via `APP_FRONTEND_URL` in `.env`) — must be a single canonical URL, NOT the comma-separated `FRONTEND_URL`.

**SEO:** `@nuxtjs/seo` is installed and configured in `nuxt.config.ts`. Provides:
- Auto-generated `/robots.txt` and `/sitemap.xml` (sitemap is restricted to public marketing/auth pages via `sitemap.urls` + `excludeAppSources: true` — never let it auto-discover authenticated routes)
- Canonical URLs and Schema.org JSON-LD per page
- Page meta via `useSeoMeta({ title, description, ogTitle, ogDescription, twitterCard, robots })` — used in all 6 marketing pages
- `og-image` module is **disabled** (`ogImage: { enabled: false }`) because it requires SSR, which is off. Provide a static `/public/og-image.png` (1200×630) for social previews
- Site config: `site.url = 'https://codecv.ie'`, `site.name = 'CODECV'`

**AI Integrations:** Two separate AI features, each with its own API key:
- CV Analysis — `CvController` calls Gemini API (`GEMINI_API_KEY`; model configurable via `services.gemini.model`, defaults to `gemini-flash-latest`). Accepts a PDF (max 5 MB) plus either `job_description` (text) or `job_url`. When `job_url` is provided, the controller fetches the page content via `https://r.jina.ai/{url}` (Jina AI reader) and caps it at 15 000 characters. Returns a JSON object with `score`, `matched_keywords`, `missing_keywords`, `strengths`, `improvements`, and `summary`.
- LinkedIn Analyser — `LinkedInController` calls Anthropic API (`ANTHROPIC_API_KEY`). Accepts a LinkedIn profile URL and scores it.

**DDEV Nuxt Config:** `frontend/nuxt.config.ts` uses `process.env.DDEV_HOSTNAME` (auto-injected by DDEV inside the container) with fallback to `codecv6.ddev.site`. The Vite HMR is configured for WSS through DDEV's HTTPS reverse proxy. When running on host (`npm run dev`), `DDEV_HOSTNAME` is unset → fallback applies.

**DDEV node_modules isolation:** `.ddev/docker-compose.frontend.yaml` mounts a named Docker volume on top of `frontend/node_modules` inside the web container. This isolates Linux x64 binaries (container) from macOS arm64 binaries (host) — they no longer collide. Each environment must be populated separately (`ddev npm install` for container, `npm install` for host). Note: the named volume is created as `root` by Docker, so the first `ddev npm install` after `ddev restart` may need `ddev exec sudo chown -R $(id -u):$(id -g) /var/www/html/frontend/node_modules` first.

**Coding Challenges (Judge0):** `ChallengeExecutionService` executes user-submitted PHP code in a Judge0 sandbox. Flow: `POST /challenges/{slug}/run` → service concatenates a minimal PHPUnit bootstrap + solution + test class + a reflection-based test runner → submits base64-encoded PHP to Judge0 as a single submission with `wait=true` → parses JSON output to determine per-test pass/fail. Judge0 status IDs 5 (TLE) and 6 (compilation error) are handled explicitly. Required env: `JUDGE0_URL` (defaults to `https://judge0-ce.p.rapidapi.com`), `JUDGE0_TOKEN`, `JUDGE0_LANGUAGE_ID` (defaults to 68 = PHP 7.4; set higher for PHP 8.x on self-hosted). A `PathStep` links to a `Challenge` via `challenge_slug` (FK on `slug`); the step's `type` field should be `challenge` in this case.

**Notifications:** The `notifications` table is the standard Laravel notifications table; `GET /notifications` returns unread + recent read notifications for the authenticated user.
- **Database-only:** `ClientAssigned` (consultant assigned a client), `PathAssigned` (path assigned to a client)
- **Email:** `NewClientOnboarded` (sent to consultant on new client payment), `ClientPathCompleted` / `ClientPathHalfway` / `ClientStartedLearning` (progress milestones), `WelcomeNotification`, `ResetPasswordNotification`

### Frontend Component Patterns

**MarkdownContent (`app/components/MarkdownContent.vue`):** Splits step descriptions into typed segments. Fenced code blocks with special language tags are extracted and rendered as interactive components:

| Block type | Renders as |
|---|---|
| ` ```mermaid ` | `<MermaidDiagram>` |
| ` ```lifecycle-diagram ` | `<LaravelLifecycleDiagramCard>` |
| Everything else | `renderMarkdown()` → `v-html` |

To embed a new interactive component in step content, add its block type to the `blockPattern` regex and the segment type union in `MarkdownContent.vue`.

**ChallengeEditor (`app/components/ChallengeEditor.vue`):** Full-screen fixed overlay rendered on top of the `admin` layout (see `pages/step/[step_id].vue`). The layout stays mounted underneath so navigation is preserved.

**DiagramCanvas / RoadmapFlow:** `DiagramCanvas.vue` uses `@vue-flow/core` for interactive flowcharts. `RoadmapFlow.vue` + `RoadmapStepNode.vue` are the learning path visualisation. Separate from `LaravelLifecycleDiagram.vue` which is plain HTML/SVG (no Vue Flow).

**PathStep page (`pages/step/[step_id].vue`):** Step `type` drives the rendered UI:
- `reading` (or no type) → two-column layout: `MarkdownContent` left, progress/resources sidebar right
- `challenge` with linked `Challenge` → `ChallengeEditor` full-screen overlay
- `challenge`/`lab` with no linked exercise → placeholder card
- anything else → fallback card

Progress update calls `updateStepProgress(stepId, status)` which catches `{ data: { blocking_step } }` errors and shows a blocking modal.

**Type co-location:** `PathStep.user_status` union type is defined in `frontend/app/composables/usePaths.ts`, not in `models.ts`.

> **Frontend-specific detail:** `frontend/CLAUDE.md` documents composable patterns, component internals, and layout rules in a compact reference. The root CLAUDE.md covers the same topics but at a higher level.