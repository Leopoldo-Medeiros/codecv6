# Architecture Review & Implementation Plan

> Produced 2026-07-08 from a full-codebase audit (backend, data layer, security,
> tests, frontend, integrations). This document is written so another model (or
> developer) can execute each step independently. **No step rewrites the project** —
> every phase is an incremental change with its own acceptance criteria.
>
> Ground rules for the executor:
> - Run `ddev artisan test --compact` after every step; a step is done only when green.
> - Run `ddev exec vendor/bin/pint --dirty` before every commit.
> - One commit per step, conventional commits.
> - Do not start a phase before the previous phase's **gate** is met (phases are
>   ordered by risk: security first, then tests, then refactors that rely on those tests).

---

## 1. Overall assessment

The core is healthy and small: ~5,000 lines of backend app code with a consistent
Controller → Service → Model layering, a modern stack (Laravel 13, Sanctum, Spatie,
Nuxt 4), and a genuinely good test suite for the classic CRUD surface (306 test
methods, real authorization-boundary tests, proper Stripe webhook signature tests).
**This project does not need a rewrite.** It needs consolidation in six areas where
growth outpaced structure:

1. **Authorization is fragmented across 4 mechanisms** (route middleware, controller
   if-checks, service throws, nothing) with zero Laravel Policies — and this
   fragmentation has already produced real vulnerabilities (see Phase 0).
2. **Everything is synchronous.** No `app/Jobs`, no `ShouldQueue` notification,
   AI/Judge0 calls block PHP-FPM workers for up to ~75s, Redis is provisioned but idle,
   `Cache::` is used nowhere.
3. **The product's curriculum lives as ~5,200 lines of PHP heredoc inside seeders**
   (7,045 seeder lines total — larger than the entire service layer), with
   inconsistent idempotency (`LearningPathsSeeder` duplicates all paths on re-run).
4. **A legacy Blade web stack runs in parallel with the API** (5 root controllers
   routed in `routes/web.php`), duplicating user/course logic with divergent
   patterns, an unprotected `GET /profile/{id}`, and PII-dumping debug logs.
5. **Test coverage is inverted relative to risk**: the untested route groups
   (challenges, playground, CV, LinkedIn, notifications, onboarding) are exactly the
   product differentiator and the code that executes user-submitted code and calls
   paid external APIs. Frontend has zero tests.
6. **Frontend duplication**: ~500 lines of copy-pasted CRUD composable boilerplate,
   two diverging type definitions per model, no 401/419 handling, three coexisting
   error-UX patterns, 17 pages with no error UI at all.

Notable factual correction discovered during audit: **the LinkedIn analyser calls
Gemini, not Anthropic**. `config/services.php` defines an `anthropic` key that is
never used, and `.env.example` documents it incorrectly. CLAUDE.md also states this
incorrectly.

---

## 2. Priorities (highest impact → lowest)

| # | Priority | Why it ranks here |
|---|----------|-------------------|
| P0 | Security hotfixes | Exploitable today: a `client` can self-escalate to `consultant` via `PUT /users/{user}` with `role=3` (`UserService::syncRole` only blocks admin); any consultant can mutate/delete any other consultant's Plans/Paths/Jobs/PathSteps (only `role:` middleware, no ownership anywhere); `consultant_id` is spoofable via PlanRequest/PathRequest/JobRequest; Judge0/Gemini/Stripe endpoints have no throttle. Cheap to fix now, expensive after launch. |
| P1 | Test the uncovered surface | Challenges/playground/CV/LinkedIn/notifications/onboarding have zero tests and no `Http::fake` guards. Every later refactor touches these areas; without tests they can't be changed safely. This phase *enables* all subsequent phases. |
| P2 | Consolidate authorization into Policies | Single mechanism (Policies + FormRequest `authorize()`) prevents recurrence of P0-class bugs. The current 4-way split is why the ownership gaps went unnoticed. |
| P3 | Retire the legacy web stack | Live duplicated logic with its own security holes and PII logging; every future change to users/courses must be made twice or silently diverges. Deletion, not refactor — low effort, high clarity gain. |
| P4 | Async & resilience | Scalability ceiling: a handful of concurrent CV analyses (60s Gemini calls) exhausts PHP-FPM workers and takes the whole API down. Queue notifications/emails, put Redis to work, add retries/timeouts, extract shared HTTP clients. |
| P5 | Content pipeline | The curriculum (the actual product) is uneditable without touching PHP and re-seeding, non-versioned, and the seeder is non-idempotent. Move content to files + an idempotent `content:sync` command. |
| P6 | Backend consistency pass | Enums for step type/status/difficulty (DB enums currently have no PHP mirror and magic strings are spread across controllers), shared pagination/search, standard error envelope, unbounded queries fixed, missing index. Reduces friction on every future feature. |
| P7 | Frontend consolidation | CRUD composable factory, `useApi` interceptors (401 → logout), single source of truth for types, uniform error/loading UX. Big DX win but nothing is broken for users today. |
| P8 | Housekeeping | `.env.example` gaps (JUDGE0_*), wrong Anthropic docs, dead CSS from codecv5, unused `DebugHeaders` middleware, session-domain `.lndo.site` leftover, CI pipeline. |

---

## 3. Target structure (no rewrite — additions/moves only)

### Backend

```
app/
├── Enums/                    # + StepType, StepProgressStatus, StepDifficulty
├── Policies/                 # NEW — the single authorization mechanism
│   ├── PlanPolicy.php  PathPolicy.php  JobPolicy.php
│   ├── CoursePolicy.php  UserPolicy.php  PathStepPolicy.php
├── Jobs/                     # NEW — queued work (analysis, emails already via ShouldQueue)
├── Services/
│   ├── Judge0/Judge0Client.php        # NEW — shared HTTP client (extracted from the 2 services)
│   ├── Ai/GeminiClient.php            # NEW — endpoint building + JSON extraction (extracted from 2 controllers)
│   ├── Ai/CvAnalysisService.php
│   ├── Ai/LinkedInAnalysisService.php
│   └── (existing services unchanged)
├── Http/Requests/            # ALL validation lives here (incl. new PathStepRequest, AnalyzeCvRequest…)
database/
├── content/                  # NEW — curriculum as versioned files
│   ├── challenges/<slug>/{meta.json, description.md, boilerplate.php, tests.php}
│   └── paths/<slug>/{meta.json, steps/*.md}
└── seeders/                  # become thin loaders over database/content
app/Console/Commands/ContentSyncCommand.php   # idempotent import
```

### Frontend

```
frontend/app/
├── composables/
│   ├── useApi.ts             # $fetch.create + onResponseError interceptor (401/419)
│   ├── createCrudResource.ts # NEW — generic factory replacing 5 copy-pasted composables
│   └── use{Courses,Jobs,…}.ts  # 5-line wrappers over the factory
├── types/models.ts           # SINGLE source of truth (composable-local interfaces deleted)
└── components/ui/            # NEW — ErrorAlert.vue, LoadingSkeleton.vue (uniform states)
```

---

## 4. Implementation plan

### Phase 0 — Security hotfixes (P0) · ~6 small steps

**Gate to next phase:** all steps merged, full suite green, each fix has a regression test.

- **0.1 Block role self-escalation.**
  `app/Services/UserService.php::syncRole` (line ~173): only users with the admin role
  may change any role; for non-admin callers, strip/ignore the `role` field entirely
  (do not error — self-profile updates legitimately reuse `UserRequest`).
  Also make `role` `sometimes` instead of `required` on updates in `UserRequest`.
  *Tests:* client sends `role=3` on self-update → role unchanged, 200; admin changes
  role → works; client sends `role=1` → unchanged.

- **0.2 Stop `consultant_id` spoofing.**
  In `PlanRequest`, `PathRequest`, `JobRequest` `prepareForValidation()`: if the
  authenticated user is not admin, **force** `consultant_id = Auth::id()` (today it
  only defaults when absent). Admin may set it, but validate the target user has the
  consultant role (`Rule::exists` + role check).
  *Tests:* consultant A creates a plan with `consultant_id` of consultant B → stored
  as A; admin sets B → stored as B.

- **0.3 Ownership checks on Plans/Paths/Jobs mutations (temporary, replaced in Phase 2).**
  Minimal controller/service guard mirroring `CourseController` (owner-or-admin) in:
  `PlanController::update/destroy/attachClient/detachClient`,
  `PathController::update/destroy`, `JobController::update/destroy`,
  `PathStepController::store/update/destroy/reorder` (owner = path's consultant, or admin).
  Keep the guard in ONE place per resource (service preferred) so Phase 2 can swap it
  for a Policy call without hunting.
  *Tests:* consultant B mutating consultant A's plan/path/job/step → 403; owner → 200; admin → 200.

- **0.4 Throttle expensive endpoints** in `routes/api.php`:
  `/challenges/{slug}/run` → `throttle:20,1`; `/cv/analyze` → `throttle:5,1`;
  `/linkedin/analyze` → `throttle:5,1`; `/checkout/session` → `throttle:10,1`.
  *Tests:* 429 after limit (use `RateLimiter` assertions or loop).

- **0.5 Guard legacy `GET /profile/{id}`** (`UsersController::show`) with
  self-or-admin, and delete the 16 `\Log::info` PII debug lines in
  `UsersController` (logs full request bodies). Do not refactor anything else in the
  legacy stack yet — Phase 3 deletes it.

- **0.6 Webhook idempotency ledger.** Add a `processed_stripe_events` table
  (event id PK) or reuse cache with `Cache::add($eventId, …)`; skip already-seen
  event IDs in `StripeWebhookController` before dispatching to handlers. Also add the
  missing `isPaid()` early-return guard to `StripeService::markSessionFailed`.
  *Tests:* same event id delivered twice → second is a no-op 200.

### Phase 1 — Cover the untested surface (P1) · ~5 steps

**Gate:** the 6 uncovered route groups have feature tests; `Http::preventStrayRequests()` active.

- **1.1** Add `Http::preventStrayRequests()` in `tests/TestCase.php` so no test can
  ever hit a live API. Fix any fallout (there should be none — Stripe is mocked,
  webhook is synthetic).
- **1.2** `ChallengeApiTest`: index/show/run with `Http::fake` for Judge0 (fake the
  base64 submission response; cover pass, fail, TLE status 5, compile error status 6,
  Judge0 down → error shape). Assert the 65,535-char source limit.
- **1.3** `PlaygroundApiTest`: run with `Http::fake`; stdout/stderr passthrough;
  10,000-char limit; `language=php` only; throttle 30/min.
- **1.4** `CvAnalysisTest` + `LinkedInAnalysisTest`: `Http::fake` for Gemini and Jina;
  cover 503-when-unconfigured, 502-upstream-failure, JSON-in-markdown fallback parse,
  PDF validation (mime/size).
- **1.5** `NotificationApiTest` (index, mark read, mark all) and `OnboardingTest`
  (`PATCH /me/onboarding`).

### Phase 2 — Authorization consolidation with Policies (P2) · ~5 steps

**Gate:** zero inline `hasRole(...)` string checks left in Api controllers; one JSON 403 envelope.

- **2.1** Create `PlanPolicy`, `PathPolicy`, `JobPolicy`, `CoursePolicy`, `UserPolicy`
  (+ `PathStepPolicy` delegating to the parent Path). Rules: `update/delete` =
  owner-or-admin; `viewAny/view` = any authenticated; user policy = self-or-admin,
  `erase` admin-only. Use `RoleEnum` — never string literals.
- **2.2** Replace the Phase-0 temporary guards and ALL controller if-checks/service
  throws with `$this->authorize(...)` / `Gate` calls. Delete the custom
  `App\Exceptions\AuthorizationException` try/catch choreography (including
  `CourseController`'s throw-then-catch-two-lines-later pattern).
- **2.3** Standardize the error envelope in `bootstrap/app.php` exception handling:
  `AuthorizationException` → `{"message": …}` 403, `AuthenticationException` → 401,
  `ValidationException` → 422 default shape. Remove per-controller manual 403 JSON.
- **2.4** Move remaining inline `$request->validate()` into FormRequests:
  `PathStepRequest` (absorb `stepRules()`), `AnalyzeCvRequest`, `AnalyzeLinkedInRequest`,
  `RunPlaygroundRequest`, `RunChallengeRequest`, `CheckoutSessionRequest`,
  `AttachClientRequest`. API `CourseController` switches to the existing `CourseRequest`.
- **2.5** Sweep magic strings: replace `hasRole('admin')` etc. with `RoleEnum` values
  (13 occurrences in Api controllers); align `ChallengeDifficulty` (4 cases incl.
  `Expert`) with the `path_steps.difficulty` validation, or document why they differ.

### Phase 3 — Retire the legacy web stack (P3) · ~3 steps

**Gate:** `routes/web.php` contains no Blade CRUD; legacy controllers deleted.

- **3.1** Inventory: confirm no production traffic/bookmarks rely on `/dashboard`,
  `/profile*`, web `/users`, web `/courses`, web `/login|/register` (they duplicate
  the SPA). If the Blade dashboard is still wanted as an admin fallback, decide
  explicitly — default recommendation is delete.
- **3.2** Delete `AdminController`, `UsersController`, root `CourseController`,
  `RegisterController`, `Auth/LoginController`, their routes in `routes/web.php`,
  and orphaned Blade views. Keep `routes/web.php` minimal (health/redirects).
  Delete dead `RegisterController::verifyEmail` regardless.
- **3.3** Delete unregistered `app/Http/Middleware/DebugHeaders.php`; purge unused
  `public/css/` legacy assets (bootstrap.min.css, codecv5.css, theme files — nothing
  in `app/` references them).

### Phase 4 — Async & resilience (P4) · ~6 steps

**Gate:** no user-facing request blocks on email sending; Redis serving queue+cache; worker documented.

- **4.1** Switch queue/cache to Redis in env (`QUEUE_CONNECTION=redis`,
  `CACHE_STORE=redis`); document `ddev artisan queue:work` (or add a DDEV
  post-start hook / supervisor note for production).
- **4.2** Make all 8 notifications `implements ShouldQueue`; `Mail` welcome/verify
  mailables too. `SCOUT_QUEUE=true` becomes real.
  *Tests:* `Queue::fake()`/`Notification::fake()` assertions on the trigger points.
- **4.3** Extract `Services/Judge0/Judge0Client.php` — the constructor, `buildHeaders()`,
  submission POST and base64 decode blocks are byte-for-byte duplicated between
  `ChallengeExecutionService` and `PlaygroundExecutionService`. Both services keep
  their distinct wrappers (PHPUnit harness vs verbatim). Add `->retry(2, 250)` on
  transient failures and keep the 30s timeout in ONE place. Judge0 status IDs 5/6
  become named constants on the client.
- **4.4** Extract `Services/Ai/GeminiClient.php` (endpoint building, key handling —
  move the API key from query-string to header if supported —, `candidates.0…` text
  extraction, the regex JSON-fallback parse) + thin `CvAnalysisService` /
  `LinkedInAnalysisService`. Controllers become thin. Log upstream failures (today
  the 502 branches log nothing).
- **4.5** Decide sync-vs-queued for CV/LinkedIn analysis. Recommendation: keep
  synchronous for now (UX is "wait for score") but document the FPM math: with 60s
  upstream timeout and default FPM workers, N concurrent analyses starve the API —
  the Phase-0 throttle mitigates; a queued+poll design is the scale path. Record as ADR.
- **4.6** First real caches: challenge index (invalidated on challenge save) and the
  consultant dashboard aggregates if it appears in profiling. Do not cache
  speculatively beyond these.

### Phase 5 — Content pipeline (P5) · ~4 steps

**Gate:** `ddev artisan content:sync` is idempotent; seeders under ~100 lines each; re-running never duplicates.

- **5.1** Create `database/content/challenges/<slug>/` with `meta.json`
  (title, difficulty, is_premium, price_eur), `description.md`, `boilerplate.php`,
  `tests.php`. Write a one-off extraction script that dumps the current
  `ChallengeSeeder` arrays into those files (verify round-trip equality against DB).
- **5.2** Same for paths/steps: `database/content/paths/<slug>/meta.json` +
  `steps/NN-<slug>.md` with front-matter (type, difficulty, estimated_minutes,
  challenge_slug, resources…) and the tiered `concept_content` as the body.
- **5.3** `ContentSyncCommand` (`php artisan content:sync [--dry-run]`): reads
  `database/content/`, `updateOrCreate` by slug for challenges/paths/steps, reports
  created/updated/unchanged. Fix the `LearningPathsSeeder` duplication bug by
  deleting its `Path::create` body — seeders now just call the command.
- **5.4** Feature test: run sync twice → identical DB state, zero duplicates;
  editing a content file and re-syncing updates the row.

### Phase 6 — Backend consistency pass (P6) · ~5 steps

- **6.1** PHP enums `StepType`, `StepProgressStatus` (`not_started|in_progress|done`),
  `StepDifficulty` + model casts on `PathStep`/`UserStepProgress`; replace the magic
  strings across `PathStepController`/`UserController`; validation rules built from
  `Enum::values()` so DB/PHP can't drift.
- **6.2** Shared search+paginate: a small `FiltersAndPaginates` trait or scope used by
  the 5 services that currently copy the same 12-line block (User/Plan/Path/Job/Course).
- **6.3** Fix unbounded queries: paginate `ChallengeController::index` and exclude
  heavy longText columns from the list query (`select` without
  boilerplate/tests/description or a dedicated `ChallengeListResource`);
  paginate `UserController::consultants` and `NotificationController::index`.
- **6.4** Migration: add index on `path_steps.challenge_slug`. Wrap
  `PathStepController::reorder` in a transaction with a single bulk `CASE` update
  (or `upsert`) instead of one UPDATE per row.
- **6.5** Response consistency: pick ONE success envelope (`{message, data}`),
  document it, and align the stragglers (resource-key-per-controller today). Align
  `role` presence on user payloads (AuthService manually appends it; UserResource
  derives it — make UserResource the single source).

### Phase 7 — Frontend consolidation (P7) · ~6 steps

- **7.1** `useApi`: build a `$fetch.create` instance with `onRequest` (headers) and
  `onResponseError` — on 401 clear `auth_token`/`auth_user` and redirect to `/login`;
  on 419 refresh the CSRF cookie once and retry. Normalize errors to a single
  `{ message, errors? }` shape so composables stop guessing between `err.message`,
  `err.data.message`, and `err.response._data.message`.
- **7.2** `createCrudResource<T>(endpoint)` factory returning the standard refs +
  five methods; rewrite `useCourses/useJobs/useUsers/usePlans/usePaths` as thin
  wrappers (≈450–500 duplicated lines deleted). Keep public API identical so pages
  don't change.
- **7.3** Types: delete the composable-local `interface Course/Job/Plan/Path`
  duplicates; import everything from `types/models.ts`. Fix known drift: add
  `email_verified_at` to `User`; remove `Profile.id/user_id` (Resource omits them);
  remove `Challenge.creator` or add it to the Resource; document that
  `PathStep.user_status` is populated client-side, not by the Resource.
- **7.4** `components/ui/ErrorAlert.vue` + `LoadingSkeleton.vue`; adopt in the 17
  pages with no error UI (start with dashboard, plans/*, my-clients/*, my-paths).
- **7.5** Replace inline role checks (`user.value?.role === 'admin'` in dashboard.vue,
  admin.vue, courses/index.vue, plans/*.vue) with the `isAdmin/isClient/isConsultant`
  computeds already exported by `useAuth`.
- **7.6** Add Vitest + a first component/composable test (createCrudResource, useApi
  interceptors) and a `test` script in `frontend/package.json`. (E2E/Playwright is a
  separate later decision.)

### Phase 8 — Housekeeping (P8) · ~3 steps

- **8.1** `.env.example`: add `JUDGE0_URL/JUDGE0_TOKEN/JUDGE0_LANGUAGE_ID`; fix the
  comment claiming Anthropic powers CV/LinkedIn (it's Gemini); resolve
  `SCOUT_QUEUE=true` vs `SCOUT_DRIVER=null` contradiction; fix
  `config/session.php` default domain `.lndo.site` → env-driven; remove the unused
  `anthropic` block from `config/services.php` (or wire it intentionally).
- **8.2** Docs: correct CLAUDE.md (LinkedIn analyser = Gemini; devServer port is 3001
  in `nuxt.config.ts` vs 3000 documented — align one way); delete `.lando.yml` if
  Lando is truly retired.
- **8.3** CI (GitHub Actions): job 1 — pint `--test` + `php artisan test` (sqlite
  in-memory, as phpunit.xml already configures); job 2 — frontend `npm ci` +
  `nuxt typecheck` + `npm run build`. Block merge on red.

---

## 5. Verification map (what proves each phase)

| Phase | Proof |
|---|---|
| 0 | Regression tests for escalation/ownership/spoofing/throttle/idempotency all green |
| 1 | `grep -r "Http::fake" tests/` non-empty for Judge0+Gemini+Jina; 6 previously-uncovered groups have suites; stray requests blocked |
| 2 | `grep -rn "hasRole('" app/Http/Controllers/Api` → 0; `app/Policies` populated; single 403 shape asserted in AuthorizationTest |
| 3 | `routes/web.php` has no Blade CRUD; legacy controllers gone; suite green |
| 4 | `Queue::fake` assertions; `app/Services/Judge0/Judge0Client.php` exists; both execution services < 200 lines |
| 5 | `content:sync` run twice → `assertDatabaseCount` unchanged |
| 6 | Enum casts in models; challenge index paginated; reorder in one query |
| 7 | 5 domain composables < 30 lines each; 401 redirects to login (component test) |
| 8 | CI green on a PR |
