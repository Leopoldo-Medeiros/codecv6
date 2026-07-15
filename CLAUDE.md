# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Full-stack application: Laravel 13 (backend) + Nuxt 4 (frontend). SaaS platform connecting consultants with clients to manage career development through courses, learning paths, job listings, and personalized training plans.

> **Deep-dive docs:** `docs/` contains topic-specific guides (`authentication.md`, `development-workflow.md`, `environment-variables.md`, `testing-guide.md`, `troubleshooting.md`, plus `docs/backend/` and `docs/frontend/`). Read these for the full surface area; this file covers the architecture-level "big picture".

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
ddev artisan test --coverage                                                       # Coverage report (Xdebug enabled in DDEV)
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
npm run build                  # Server build (outputs to frontend/.output/)
npm run generate               # Static build for deploy (outputs to frontend/dist/)
npm run preview                # Preview a built app locally

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
- **No auth (Stripe signature)**: `POST /webhooks/stripe` — Stripe webhook; verified via `STRIPE_WEBHOOK_SECRET`, no CSRF. Idempotent: the event id is claimed in a `processed_stripe_events` table before dispatch, so a Stripe-redelivered event is a 200 no-op on the second delivery (see Stripe Payments below)
- **Public** (rate-limited 5/min): `/login`, `/register`, `/forgot-password`, `/reset-password`
- **Public — practice teaser** (no account; practice funnel stage 1 / F2): `GET /public/challenges/teaser` (`throttle:30,1`, curated `is_teaser=true` challenges only, via `TeaserChallengeResource` — deliberately excludes `tests_code`/`is_premium`/`created_by`); `POST /public/challenges/{slug}/run` (`throttle:5,1` by IP, 404s for a non-teaser slug, no XP/progress recorded — there's no user to attach it to)
- **Public — skill profile** (practice funnel stage 5 / F3): `GET /public/profile/{slug}` (`throttle:30,1`) — opt-in only (`profiles.is_public`, off by default; 404 for private or unknown slugs). Payload is an explicit allow-list built by `PublicProfileService` (fullname, profession/level/stack/goal, github/linkedin/website links, XP/streak stats, badges, completed challenges) — email, birth_date, and onboarding answers never leave the API. Owner toggles via `PATCH /me/public-profile`; the slug is minted on first opt-in and stays stable across toggles so shared links survive re-enabling
- **All authenticated**: Read paths (`GET /paths`, `GET /my-paths`), courses, plans, jobs; view/update own user + avatar (`/users/{user}`, ownership enforced in controller/service — role changes are admin-only, silently ignored for other callers, see Authorization below); `PATCH /me/onboarding`; `GET /me/progress` (XP/streak/badges snapshot, see Gamification below); `GET /me/coaching-recommendation` (F6 coaching upsell nudge, see below); `GET /me/activity` (daily practice counts for the dashboard contribution heatmap — derived from `UserChallengeCompletion.completed_at` + incident-step completions, no activity log); CV/LinkedIn analysis (`throttle:5,1` — Gemini + optional Jina fetch); Stripe checkout (`POST /checkout/session`, `throttle:10,1`); challenges (`GET /challenges`, `GET /challenges/{slug}`, `POST /challenges/{slug}/run` — `throttle:20,1`, Judge0 sandbox run); playground (`POST /playground/run`, throttled 30/min); step progress (`PUT /path-steps/{step}/progress`); quiz grading (`POST /path-steps/{step}/quiz` — server-side, F4-gated, see Quizzes below); notifications (`GET /notifications`, `PATCH /notifications/{id}/read`, `PATCH /notifications/read-all`); GDPR export (`GET /users/{user}/export` — self or admin, checked in controller)
- **Admin + Consultant** (`role:admin|consultant`): Create/edit/delete courses, plans, paths, jobs; manage path steps (`POST/PUT/DELETE/reorder /paths/{path}/steps`); `/my-clients` — view clients, assign/remove paths. Mutations (update/delete, plus Plan's attach/detachClient and PathStep's store/update/destroy/reorder) are further gated to the resource's own consultant or an admin — see Authorization below
- **Admin only** (`role:admin`): `GET /admin/dashboard` (platform-wide aggregates for the admin dashboard — all-user practice activity, daily signups, PAID-EUR revenue series); User list/create/delete, role management (`GET /roles`), consultant listing + assignment. GDPR erase (`DELETE /users/{user}/erase`) is admin-only too, but enforced in the controller rather than route middleware

### Core Domain Models

| Model | Key Relationships |
|-------|------------------|
| `User` | Has one `Profile`, has roles via Spatie, self-references via `consultant_id` (FK): `consultant()` BelongsTo + `clients()` HasMany |
| `Profile` | Belongs to User, stores social links (github, linkedin, instagram, facebook, website) |
| `Plan` | Belongs to consultant (User), has many clients (users) and paths via pivots (`plan_user`, `path_plan`) |
| `Course` | Soft-deletable, belongs to User (creator) |
| `Path` | Learning paths, soft-deletable; has ordered `PathStep`s |
| `PathStep` | Ordered steps within a Path; type can be `reading`, `lab`, `challenge`, or `quiz`; `challenge_slug` FK links to `Challenge`; tracked per-user via `UserStepProgress` |
| `Challenge` | Coding challenge with `slug` (unique), `boilerplate_code`, `tests_code`, `ChallengeDifficulty` enum; soft-deletable; `is_premium` + `price_eur` for monetization; `is_teaser` marks it playable anonymously (practice funnel F2) |
| `Job` | Job listings, soft-deletable |
| `Payment` | Stripe payment record; created `PENDING` on checkout, updated to `PAID` by webhook; belongs to `User` |
| `UserStepProgress` | Tracks per-user completion of individual `PathStep`s; `status` can be `not_started`, `in_progress`, `completed` |
| `UserProgressStats` | One row per user (`hasOne`): cumulative `xp_points`, `current_streak`, `longest_streak`, `last_practiced_at`. Owned by `GamificationService` |
| `UserChallengeCompletion` | First-pass record per (user, challenge) — `unique(user_id, challenge_id)`, no timestamps (only `completed_at`); makes challenge XP idempotent and is the practice-history source for the future public skill profile |
| `Badge` / pivot `user_badges` | Milestone achievements (`belongsToMany` on `User` with `earned_at` pivot); seeded by `BadgesSeeder` |

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
- `PlanRequest`, `PathRequest`, `JobRequest` → use the shared `AssignsConsultant` trait (`app/Http/Requests/Concerns/`): non-admins always have `consultant_id` forced to their own id (ignores any client-supplied value); admins may set it explicitly but the target must have the consultant or admin role (validated via `withValidator`'s after-hook)
- `UserRequest` → removes null passwords before validation; `role` is `sometimes` on updates (role changes are enforced admin-only in `UserService`, not here)

### Frontend Stack (Nuxt 4)

- **Directory structure:** Application code lives in `frontend/app/` (Nuxt 4 convention)
- **UI Library:** `@nuxt/ui` v2 (emerald primary, slate gray) — use UCard, UButton, UTable, UBadge, UAvatar, UProgress, UIcon, UDropdown components in **authenticated/admin pages**. Marketing pages do NOT use `@nuxt/ui` — they use the custom `.mkt-*` design system (see Marketing Design System section)
- **Dark mode:** enabled by default for authenticated pages; marketing pages force light theme via `useColorMode()` in their layout. The authenticated app's dark surfaces use **`neutral` (near-black)** — `neutral-950` page bg, `neutral-900` cards, `neutral-800` borders (a "Lotech"-style analytics look; the `admin` layout adds a subtle emerald ambient glow, top-right). Use `neutral`, not `slate`, for new authenticated dark surfaces. **Gotcha:** Vue scoped-style dark overrides written as `:global(.dark) .x :deep(y)` tie on specificity with their light counterparts and lose on source order — dark text/code stayed light. Fix pattern (see `StepConceptView.vue`): drive content colors with CSS variables and flip them in a **non-scoped** `<style>` block via `html.dark .x { --var: … }`, which out-specifies the scoped defaults. Prefer Tailwind `dark:` utilities for component colors where possible (reliable, e.g. `FlowDiagram.vue`)
- **Dashboard (`pages/dashboard.vue`):** client view has a KPI row (XP/streak/steps/progress, each with a mini viz — `AreaChart`/`MiniBars`/progress bar), a GitHub-style `ActivityHeatmap` (from `GET /me/activity`), a `Practice over time` `AreaChart`, `Continue learning` cards, and the right rail (`ProgressWidget`, `CoachingNudge`, quick actions). The admin/staff view mirrors it from `GET /admin/dashboard`: KPIs (Total Users signups sparkline, Revenue-EUR area), a platform-wide `ActivityHeatmap`, a `Revenue over time` `AreaChart`, and the recent-users table. Each `StatCard` carries its own `series`/`bars`/`ratio` so KPI mini-viz is data-driven. Dashboard chart components: `ActivityHeatmap.vue` (contribution grid), `AreaChart.vue` (fills parent height — set a height on the wrapper), `MiniBars.vue`
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
| `useCoaching` | F6 coaching upsell — `fetchRecommendation()` → `GET /me/coaching-recommendation`; exposes `recommendation` (`null` when unearned) |

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

- `marketing.vue` — used by `/`, `/about`, `/pricing`, `/faqs`, `/terms`, `/privacy`. Inlines its own top bar, header (with SVG `{ }` logo) and footer — does NOT use the `Navbar.vue`/`Footer.vue` components (those serve `default`/`admin` layouts). Defines all `.mkt-*` design tokens on the `.mkt` wrapper class (NOT `:root` — components rendered outside `.mkt` can't see them)
- **Login/register do NOT use any layout** — they declare `definePageMeta({ layout: false })` and render the standalone `TerminalShell` component (terminal-style auth, see Frontend Component Patterns). The `auth` layout serves the remaining auth pages (forgot/reset password)
- `auth.ts` middleware — redirects unauthenticated users to login
- `guest.ts` middleware — redirects authenticated users to dashboard

### Marketing Design System

Marketing pages (`pages/index.vue`, `about.vue`, `pricing.vue`, `faqs.vue`, `terms.vue`, `privacy.vue`) use a **custom CSS design system** rather than Tailwind or `@nuxt/ui`. Tokens live in `app/layouts/marketing.vue` `<style>` block under `.mkt`:

- **Aesthetic:** corporate consulting (NorthWest-style) — white surfaces (`--bg: #ffffff`), dark ink top bar (`--ink: #111A22`), kicker + bold heading + short 54px emerald underline as the section-header motif, white cards with soft shadows (`--shadow-sm/-md`), rectangular buttons (`--radius-btn: 3px`, uppercase bold labels), full-bleed emerald bands (counters, testimonials) with dot patterns
- **Color palette:** emerald — `--accent: #059669` (emerald-600), `--accent-hover: #047857`. RGBA helpers: `--accent-light/-mid/-glow`. **Never use purple** (`#6b46e5`, `#7c3aed`) — was deliberately removed to avoid AmigosCode visual collision
- **Typography:** Poppins only (400–800), loaded via Google Fonts in the layout; single token `--ff`
- **Class naming:** BEM-ish prefixed by section (`.scard__icon`, `.igrid__item`, `.counters__grid`, `.tband__quote`)
- **Each marketing page** declares `definePageMeta({ layout: false })` and wraps content in `<NuxtLayout name="marketing">` explicitly (so the layout doesn't apply to other pages by accident)
- **Known drift:** `pages/index.vue` re-declares its own scoped token set (`--em`, `--ink`, `.btn` ≈ `.mkt-btn`) on its section roots — values already diverge slightly from the layout tokens; consolidating onto the `.mkt` tokens is a pending cleanup
- **Logo:** inline SVG with `{ }` text in monospace inside a folded-paper icon. Brand text is `CODE` + `<span class="mkt-logo__cv">CV</span>`

### Terminal Auth Pages

`login.vue` and `register.vue` render an iTerm-style window (`TerminalShell.vue`, unscoped `.term-*` design system + JetBrains Mono) with prompt-style fields (`TerminalPrompt.vue` — real visible inputs styled as terminal text, so IME/autofill/password managers work) and an animated SVG mascot (`TerminalMascot.vue`) with 3D shading + perspective tilt that follows the cursor with its eyes and reacts to form state via a `mood` prop: watches typing, covers its eyes during password entry, shakes with red X-eyes on auth failure, celebrates on success. Shared spinner logic lives in `composables/useTerminalSpinner.ts`; `useAuth()` exposes `googleAuthUrl` for the OAuth "command" links.

### Database Seeding Order

`DatabaseSeeder` calls these in order:

1. `RoleSeeder` — creates admin, client, consultant roles
2. `UserSeeder` — creates initial users (runs `RoleSeeder` internally)
3. `CoursesTableSeeder` — seeds courses
4. `ChallengeSeeder` — seeds coding challenges with boilerplate + test code
5. `LearningPathsSeeder` — seeds `Path` records
6. `PathStepSeeder` — seeds `PathStep` records (links steps to paths and challenges)
6b. `IncidentSeeder` — seeds the "Observability 101" path + the N+1 incident-reader step (observability track, Phase A)
7. Optional: `ClientsSeeder` — interactive prompt for fake clients

`UserFactory` uses `afterCreating()` to auto-create a `Profile` and assign the `client` role.

## Important Patterns

**Authorization in Services:** Services throw `AuthorizationException` for business rule violations (self-deletion, protecting last admin, role-escalation attempts, accessing another user's data). `bootstrap/app.php` registers a global render callback for it — any JSON request gets a `{"message": ...}` 403 automatically, so services/controllers just throw instead of each hand-rolling try/catch (this is intentionally the minimal slice of the eventual full error-envelope pass, not a Policy migration). `AuthenticationException` returns 401.
- **Resource ownership:** `Plan`, `Path`, `Job`, and `PathStep` mutations are guarded by the `EnsuresResourceOwnership` trait (`app/Services/Concerns/`) — `ensureOwnerOrAdmin($ownerId, $resourceName)` throws unless the actor is the resource's consultant or an admin. Used in `PlanService`/`PathService`/`JobService` (update/delete, plus Plan's attach/detachClient) and directly in `PathStepController` (owner = the parent `Path`'s consultant). No Policies yet — this is a temporary Phase 0 guard; Phase 2 of `docs/architecture-review.md` replaces it with proper Policies
- **Role changes:** only admins may change a user's role (`UserService::update`) — the `role` field is silently ignored (not rejected) for non-admin callers, since self-profile updates legitimately reuse `UserRequest`

**File Uploads:** `FileUploadService` handles image storage/replacement. Profile images in `storage/app/public/profile_images/` (public disk). Max 2048KB, jpeg/jpg/png only. Validation happens in the service layer, not middleware.

**Path Progress Tracking:** `UserStepProgress` tracks per-user completion of individual `PathStep`s. Steps are ordered via `.orderBy('order')` in the Path relationship. Users can only update their own progress (enforced in the controller).

**Content gating (practice funnel F4):** `EntitlementService` is the single source of truth for what practice content a user may access. Free tier = every teaser + every `beginner`-difficulty challenge, plus the first 2 steps (order 0/1) of any path. Everything else needs practice access, granted by role (admin/consultant always) or a PAID `Payment` in a practice-granting tier (`PRACTICE`, `BOOTCAMP`, `MENTORSHIP` — not `ACCELERATOR`). Enforced in `ChallengeController::show/run` and `PathStepController::show` (403 via `AuthorizationException`); list endpoints stay open but add a `locked` flag (and `ChallengeResource` nulls `boilerplate_code`/`tests_code` when locked so gated content can't be scraped off the list). The public teaser endpoints are unaffected — teasers are free by definition. **KNOWN LIMITATION:** access means "has ever paid for a granting tier" — the webhook doesn't yet handle subscription cancellation/expiry, so lapse handling (needs `customer.subscription.deleted` etc.) is a separate follow-up.

**Coaching upsell nudges (practice funnel F6 — "Get Coached"):** `CoachingRecommendationService` recommends the single most relevant coaching tier for a practicing user, bridging self-serve practice into the high-touch tiers. Philosophy is *earned* nudges — a recommendation only surfaces once the user has proven something (finished a path, built a streak); a brand-new user gets `null` (that stage belongs to F1's ProgressWidget). Signals: `xp_points`, `longest_streak`, challenges completed (`UserChallengeCompletion` count), and paths completed (all steps `done`). The ladder is priority-ordered and fully config-driven in `config/coaching.php` (`priority` + per-tier `thresholds` with OR semantics + copy): mentorship (streak ≥ 7 or XP ≥ 300) → accelerator (≥ 1 path or ≥ 5 challenges) → bootcamp (≥ 3 challenges). The first qualifying tier the user hasn't already PAID for wins (owned-tier suppression reads `Payment` PAID rows, mirroring `EntitlementService`). Exposed at `GET /me/coaching-recommendation` (`{recommendation: {...}|null}`). Frontend: `CoachingNudge.vue` renders on the dashboard right-rail (clients only) and inside a path-completion celebration modal on `/step/[id]` (the highest-intent moment, triggered when the `path_completed` badge lands in the progress response). CTA links to `/pricing#plans`.

**Observability track — incident reader (Phase A):** A `PathStep` of `type = 'incident'` teaches production debugging by making the learner read curated telemetry and diagnose a fault. The telemetry lives in the `evidence` JSON column (`{ scenario, trace: { root, spans[] }, metrics[], logs[] }`) and is rendered entirely client-side — **no live environment, no per-user compute** (that's the deliberate cheap-to-run scope of Phase A). The diagnostic questions reuse the F5 `quiz` column and grading wholesale: `submitQuiz` accepts both `quiz` and `incident` types, so grading, XP, and the F4 gate are unchanged. Frontend renderers: `TraceWaterfall.vue` (span waterfall with per-service colors + `repeat`/N+1 collapsing + click-to-inspect), `MetricChart.vue` (inline-SVG line/area with threshold), `LogStream.vue` (level-colored, correlation-id filter), composed by `IncidentRunner.vue` (scenario + evidence + reused `<QuizRunner>`). Seeded content: a 7-incident library in `IncidentSeeder` under the "Observability 101" path — N+1, missing index, slow downstream, bad deploy, cache stampede, pool exhaustion, memory leak — each teaching a distinct signal (trace-led, metric-correlation, saturation, sawtooth) with cross-incident distractors in the diagnostic questions. This is the first slice of the larger observability track; live-environment phases (learner instruments a running service) are deliberately deferred until Phase A validates demand.

**Quizzes (practice funnel F5):** A `PathStep` of `type = 'quiz'` stores its questions in the `quiz` JSON column — an array of `{ id, question, options[], correct_index, explanation }`. `PathStepResource` strips `correct_index`/`explanation` before serialising, so the answer key never reaches the client. Grading is server-side only: `POST /path-steps/{step}/quiz` (`PathStepController::submitQuiz`, F4-gated by `EntitlementService::canAccessStep`) validates `answers` (a `present|array` map of `questionId => optionIndex`), compares against the key, and returns `{ score, total, passed, results[] }` where each result carries `correct`/`correct_index`/`explanation` for post-submission review. `passed` is true only on a perfect score. The frontend renderer is `QuizRunner.vue`; on `passed` the step page marks the step `done` (which routes through the normal gamification XP path). Seeded example: the "Checkpoint: Modern PHP Reflexes" step in `PathStepSeeder`.

**Gamification (practice funnel F1):** `GamificationService` awards XP, tracks a daily practice streak, and unlocks milestone badges — called from `ChallengeController::run` (on a passing submission) and `PathStepController::updateProgress` (on a step's first transition to `done`; the controller pre-checks the previous status so repeat completions award nothing). Challenge XP is idempotent via `UserChallengeCompletion` (first pass only); step XP trusts the controller's freshness check rather than re-deriving it, mirroring the existing `$hadAnyProgress` pattern used for progress notifications. XP scales by difficulty (`beginner`/`intermediate`/`advanced`/`expert` for challenges, `beginner`/`intermediate`/`advanced` for steps). 5 badges exist today, seeded by `BadgesSeeder`: four `category = 'achievement'` (`first_challenge`, `streak_7`, `path_completed`, `incident_solved` — awarded on a user's first solved `incident` step) plus one `category = 'certification'` (`observability_certified`). Both mutation endpoints return a `progress` key (`null` when nothing was freshly completed); `GET /me/progress` reads the full snapshot.

**Certification seals:** A `Path` may confer a badge on full completion via its nullable `paths.badge_key` column — when `GamificationService` detects every step of a path is `done`, it awards `path_completed` (generic) plus the path's `badge_key` if set. Badges carry a `category` (`achievement` vs `certification`); the public skill profile (F3) renders certifications as a prominent **seal** (distinct emerald card, "Certified by CODECV"), separate from achievement chips. The Observability track ("Observability 101", seeded by `IncidentSeeder` with `badge_key = 'observability_certified'`) is the first certification — it's the employer-facing proof the track is designed to produce.

**Testing:** Feature tests use `RefreshDatabase` and call `RoleSeeder` in `setUp()`. Tests live in `tests/Feature/Api/` (API tests) and `tests/Feature/Auth/`. PHPUnit 12 with a separate test database config. The base `Tests\TestCase` calls `Http::preventStrayRequests()` in `setUp()` — any outbound HTTP call not covered by `Http::fake()` throws instead of silently reaching a live API (Judge0, Gemini, Jina, Stripe). All 6 previously-untested route groups now have coverage: `ChallengeApiTest`/`PlaygroundApiTest` (Judge0 outcomes: pass/fail/TLE/compile-error/down), `CvAnalysisTest`/`LinkedInAnalysisTest` (Gemini + Jina, including the markdown-fenced JSON fallback parse), `NotificationApiTest`, `OnboardingTest`.

**Search:** `User` model uses Laravel Scout (Algolia) — `toSearchableArray()` indexes `id`, `fullname`, and `email`. Requires `SCOUT_QUEUE=true` in env.

**Stripe Payments:** `StripeService` drives the checkout flow. Tiers and prices are defined in `config/pricing.php` — amounts are in **minor units** (cents for EUR, centavos for BRL). `PaymentTier` enum has `PRACTICE` (recurring, practice funnel F4 — the cheap €12/mo self-serve tier), `ACCELERATOR`, `BOOTCAMP` (one-time), and `MENTORSHIP` (recurring). `isRecurring()` returns true for `PRACTICE` and `MENTORSHIP`. Adding a tier = enum case + `isRecurring()` + a `config/pricing.php` block; `StripeService` reads name/description/interval/prices from config, so nothing else changes. Flow: `POST /checkout/session` (`throttle:10,1`) → `StripeService::createCheckoutSession()` creates a Stripe Checkout Session and records a `PENDING` `Payment` row → user is redirected to Stripe → `POST /webhooks/stripe` receives `checkout.session.completed` and marks the payment `PAID`. The webhook is excluded from CSRF middleware via `bootstrap/app.php`. **Idempotency:** `StripeWebhookController` claims the event id in `processed_stripe_events` (`insertOrIgnore`) before calling `handleEvent()` — a redelivered event returns `{"received": true, "duplicate": true}` without re-running the handler; if the handler throws, the claim is released so a genuine retry can reprocess it. `markSessionPaid`/`markSessionFailed` additionally no-op via `Payment::isPaid()` so a settled payment is never overwritten either way. Required env: `STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET`. For local testing use the Stripe CLI: `stripe listen --forward-to codecv6.ddev.site/api/webhooks/stripe`.

**Social Auth (Google OAuth):** `SocialAuthController` handles the provider redirect and callback. The frontend hits `/auth/google/redirect`, then the callback exchanges the code via `AuthService`. The `User` model has a `google_id` column. Requires `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, and `GOOGLE_REDIRECT_URI` in `.env`. The post-OAuth browser redirect uses `config('app.frontend_url')` (set via `APP_FRONTEND_URL` in `.env`) — must be a single canonical URL, NOT the comma-separated `FRONTEND_URL`.

**SEO:** `@nuxtjs/seo` is installed and configured in `nuxt.config.ts`. Provides:
- Auto-generated `/robots.txt` and `/sitemap.xml` (sitemap is restricted to public marketing/auth pages via `sitemap.urls` + `excludeAppSources: true` — never let it auto-discover authenticated routes)
- Canonical URLs and Schema.org JSON-LD per page
- Page meta via `useSeoMeta({ title, description, ogTitle, ogDescription, twitterCard, robots })` — used in all 6 marketing pages
- `og-image` module is **disabled** (`ogImage: { enabled: false }`) because it requires SSR, which is off. Provide a static `/public/og-image.png` (1200×630) for social previews
- Site config: `site.url = 'https://codecv.ie'`, `site.name = 'CODECV'`

**AI Integrations:** Two AI features, **both backed by Google Gemini** (single `GEMINI_API_KEY`; model configurable via `services.gemini.model`, defaults to `gemini-flash-latest`). There is no Anthropic integration in the codebase despite the historical name — do not reintroduce an `ANTHROPIC_API_KEY`.
- CV Analysis — `CvController` calls Gemini. Accepts a PDF (max 5 MB) plus either `job_description` (text) or `job_url`. When `job_url` is provided, the controller fetches the page content via `https://r.jina.ai/{url}` (Jina AI reader) and caps it at 15 000 characters. Returns a JSON object with `score`, `matched_keywords`, `missing_keywords`, `strengths`, `improvements`, and `summary`.
- LinkedIn Analyser — `LinkedInController` also calls Gemini (same key/model). Accepts a LinkedIn profile PDF and scores it.

**DDEV Nuxt Config:** `frontend/nuxt.config.ts` uses `process.env.DDEV_HOSTNAME` (auto-injected by DDEV inside the container) with fallback to `codecv6.ddev.site`. The Vite HMR is configured for WSS through DDEV's HTTPS reverse proxy. When running on host (`npm run dev`), `DDEV_HOSTNAME` is unset → fallback applies.

**DDEV node_modules isolation:** `.ddev/docker-compose.frontend.yaml` mounts a named Docker volume on top of `frontend/node_modules` inside the web container. This isolates Linux x64 binaries (container) from macOS arm64 binaries (host) — they no longer collide. Each environment must be populated separately (`ddev npm install` for container, `npm install` for host). Note: the named volume is created as `root` by Docker, so the first `ddev npm install` after `ddev restart` may need `ddev exec sudo chown -R $(id -u):$(id -g) /var/www/html/frontend/node_modules` first.

**Coding Challenges (Judge0):** `ChallengeExecutionService` executes user-submitted PHP code in a Judge0 sandbox. Flow: `POST /challenges/{slug}/run` → service concatenates a minimal PHPUnit bootstrap + solution + test class + a reflection-based test runner → submits base64-encoded PHP to Judge0 as a single submission with `wait=true` → parses JSON output to determine per-test pass/fail. Judge0 status IDs 5 (TLE) and 6 (compilation error) are handled explicitly. Required env: `JUDGE0_URL` (defaults to `https://judge0-ce.p.rapidapi.com`), `JUDGE0_TOKEN`, `JUDGE0_LANGUAGE_ID` (defaults to 68 = PHP 7.4; set higher for PHP 8.x on self-hosted). A `PathStep` links to a `Challenge` via `challenge_slug` (FK on `slug`); the step's `type` field should be `challenge` in this case.

**Scratch Playground (Judge0):** `PlaygroundExecutionService` is the sibling of `ChallengeExecutionService` for the in-step "Try this in the playground" feature. Unlike challenges there is no PHPUnit wrapper — the submitted PHP runs verbatim and stdout/stderr are returned as-is. Route: `POST /playground/run` (throttled 30/min per user). Shares the same `services.judge0.*` config (URL/token/language ID). Use it for free-form exploration inside step content; use the challenge flow only when you need pass/fail tests.

**Public Practice Teaser (practice funnel F2):** `PublicChallengeController` is the app's only unauthenticated code-execution surface, kept deliberately separate from `ChallengeController` so its access rule (`is_teaser=true` only) lives in one small, auditable file. Reuses `ChallengeExecutionService` as-is — same Judge0 config, same 65,535-char limit. No `GamificationService` call (no user to award XP/streaks/badges to). Which challenges are teasers is a data decision (`challenges.is_teaser`, set in `ChallengeSeeder`), not application logic.

**Notifications:** The `notifications` table is the standard Laravel notifications table; `GET /notifications` returns unread + recent read notifications for the authenticated user.
- **Database-only:** `ClientAssigned` (consultant assigned a client), `PathAssigned` (path assigned to a client)
- **Email:** `NewClientOnboarded` (sent to consultant on new client payment), `ClientPathCompleted` / `ClientPathHalfway` / `ClientStartedLearning` (progress milestones), `WelcomeNotification`, `ResetPasswordNotification`

### Frontend Component Patterns

**MarkdownContent (`app/components/MarkdownContent.vue`):** Splits step descriptions into typed segments. Fenced code blocks with special language tags are extracted and rendered as interactive components:

| Block type | Renders as |
|---|---|
| ` ```mermaid ` | `<FlowDiagram>` — parses the simple mermaid flowchart subset (nodes + `-->` chains + `:::class` colors) into a modern, readable pipeline (numbered cards + connectors), TD=vertical / LR=horizontal. Replaced the old `MermaidDiagram` raw render; `StepConceptView` routes its `mermaid` fences to it too |
| ` ```lifecycle-diagram ` | `<LaravelLifecycleDiagramCard>` |
| Everything else | `renderMarkdown()` → `v-html` |

To embed a new interactive component in step content, add its block type to the `blockPattern` regex and the segment type union in `MarkdownContent.vue`.

**ChallengeEditor (`app/components/ChallengeEditor.vue`):** Full-screen fixed overlay rendered on top of the `admin` layout (see `pages/step/[step_id].vue`). The layout stays mounted underneath so navigation is preserved. Code editing uses Monaco via `@guolao/vue-monaco-editor`.

**DiagramCanvas / RoadmapFlow:** `DiagramCanvas.vue` uses `@vue-flow/core` for interactive flowcharts. `RoadmapFlow.vue` + `RoadmapStepNode.vue` are the learning path visualisation — node positions are computed with `@dagrejs/dagre` auto-layout, not hand-placed. Separate from `LaravelLifecycleDiagram.vue` which is plain HTML/SVG (no Vue Flow).

**StepConceptView (`app/components/StepConceptView.vue`):** The rich `reading`-step renderer used by `pages/step/[step_id].vue`. Owns the two-column layout (concept content + sidebar), the TL;DR card, and the **tiered content sections** — "Core / Deeper dive / Senior" — for seniority filtering. It also hosts the per-task "Try this in the playground" entry points that call `POST /playground/run`.

**PathStep full schema** (all columns across migrations):

| Field | Type | Purpose |
|-------|------|---------|
| `path_id` | FK | Parent path |
| `course_id` | FK nullable | Optional linked course |
| `title` | string | Step title |
| `description` | text | Short summary |
| `tldr` | string(500) | One-liner shown in the TL;DR card |
| `concept_content` | longText | Main Markdown body (see tiered content below) |
| `resources` | json | `[{ label, url }]` — sidebar links |
| `order` | smallint | Display order within the path |
| `type` | string | `reading` / `lab` / `challenge` / `quiz` / `incident` (plain string, not a DB enum — allowed values enforced in `stepRules`) |
| `lab_url` | string | External lab URL (type=lab) |
| `instructions` | json | `[{ id, text }]` — lab instructions |
| `challenge_prompt` | text | (deprecated — use linked Challenge) |
| `challenge_slug` | string FK | Links to `challenges.slug` (type=challenge) |
| `quiz` | json | `[{ id, question, options[], correct_index, explanation }]` (type=quiz **and** type=incident — incidents reuse the quiz grading); answer key stripped by the Resource — see F5 note above |
| `evidence` | json | `{ scenario, trace: { root, spans[] }, metrics[], logs[] }` (type=incident) — curated telemetry rendered client-side; display-only, no answer key — see Observability track below |
| `estimated_minutes` | smallint | Sidebar time estimate |
| `difficulty` | enum | `beginner` / `intermediate` / `advanced` |
| `prerequisites` | json | `[{ id, title }]` — prerequisite steps |
| `concepts` | json | `['string', ...]` — concept tags |
| `has_playground` | bool | Shows in-step playground |
| `playground_starter_code` | text | Starter code for the playground |

**Tiered content convention:** `concept_content` is Markdown. When H2 headings begin with `"Core"`, `"Deeper dive"`, or `"Senior"`, `StepConceptView` splits the content into three seniority tiers. Users filter with "Junior / Mid / Senior" pills. Steps without these heading prefixes render as flat content (legacy fallback). Example structure:

```markdown
## Core — What it is
…junior-friendly explanation…

## Deeper dive — How it works
…intermediate concepts…

## Senior insights — When and why
…architectural tradeoffs…
```

**PathStep page (`pages/step/[step_id].vue`):** Step `type` drives the rendered UI:
- `reading` (or no type) → `StepConceptView` two-column layout: concept/tiered content left, progress/resources sidebar right
- `challenge` with linked `Challenge` → `ChallengeEditor` full-screen overlay
- `challenge`/`lab` with no linked exercise → placeholder card
- `quiz` with `quiz[]` questions → `QuizRunner` (F5); marks the step `done` on a perfect score
- `incident` with `evidence` → `IncidentRunner` (observability track): renders the telemetry + reuses `QuizRunner` for diagnosis; marks `done` on a perfect score
- anything else → fallback card

Progress update calls `updateStepProgress(stepId, status)` which catches `{ data: { blocking_step } }` errors and shows a blocking modal.

**Type co-location:** `PathStep.user_status` union type is defined in `frontend/app/composables/usePaths.ts`, not in `models.ts`.

> **Frontend-specific detail:** `frontend/CLAUDE.md` documents composable patterns, component internals, and layout rules in a compact reference. The root CLAUDE.md covers the same topics but at a higher level.