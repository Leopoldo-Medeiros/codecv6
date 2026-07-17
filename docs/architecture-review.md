# Architecture Review & Implementation Plan

> **Re-audit produced 2026-07-15** from a full-codebase pass (backend, data layer,
> frontend, infra) — supersedes the original 2026-07-08 review. This document is
> written so another model (or developer) can execute each step independently.
> **No step rewrites the project** — every phase is an incremental change with its
> own acceptance criteria.
>
> Ground rules for the executor:
> - Run `ddev artisan test --compact` after every step; a step is done only when green.
> - Run `ddev exec vendor/bin/pint --dirty` before every commit.
> - One commit per step, conventional commits.
> - Do not start a phase before the previous phase's **gate** is met.

---

## 0. What already landed (2026-07-08 → 2026-07-15)

The prior review's two highest phases are **complete**, verified against the current
tree. **Do not re-plan these:**

- **Role self-escalation closed** — non-admins can't set `role` (silently ignored in `UserService`).
- **Resource ownership enforced** — `EnsuresResourceOwnership` trait guards Plan/Path/Job/PathStep mutations (owner-or-admin).
- **`consultant_id` spoofing closed** — `AssignsConsultant` trait forces the actor's id for non-admins.
- **Expensive endpoints throttled** — Judge0 run, CV, LinkedIn, checkout all rate-limited.
- **Stripe webhook idempotency** — `processed_stripe_events` claim/release ledger; redelivery is a no-op.
- **The 6 uncovered route groups now have tests** — challenges, playground, CV, LinkedIn, notifications, onboarding + `Http::preventStrayRequests()`.

**Phase A (content pipeline, P1 + P3) — DONE 2026-07-15:**

- All curriculum extracted to versioned files under `database/content/` — 34
  challenges (`meta.json` + `description.md` + `boilerplate.php` + `tests.php`)
  and 8 paths with 62 steps (`meta.json` + `steps/NN-*.md`, a `---json`
  front-matter block + `concept_content` body).
- Idempotent `php artisan content:sync` (upsert by slug / name / `(path_id, order)`;
  preserves `created_by`/`consultant_id`; `--dry-run`). Proven idempotent:
  re-runs report every row unchanged, zero duplicates.
- The 6 heredoc seeders (`ChallengeSeeder`, `LearningPathsSeeder`, `PathStepSeeder`,
  `IncidentSeeder`, `RestApiTrackSeeder`, `ChallengeLinkSeeder`) collapsed into one
  thin `ContentSeeder` that calls `content:sync`. `pint.json` now excludes
  `database/content` so the linter never restyles authored content.
- `tests/Feature/ContentSyncTest.php` — 11 tests (idempotency, no-duplication,
  drift correction, author preservation, nested-JSON round-trip, no vendor content).
- **P3 was bigger than one path:** extraction revealed *three* New-Relic-coupled
  paths (APM with New Relic, OpenTelemetry in Practice, Full Stack Observability —
  ~50 mentions, several in lab/step titles teaching the NR agent + NRQL). Decision:
  **removed all three** (kept the vendor-neutral "Observability 101"); observability
  content to be rebuilt vendor-neutral later. Two incidental name-drops neutralized.

Everything below (the prior review's Phases 2, 4–8) remains untouched: no `app/Policies`,
no `app/Jobs` / zero `ShouldQueue`, no `.github` CI, zero `Cache::`, all 5 legacy web
controllers still present.

---

## 1. Overall assessment

The core is healthy and small — a consistent Controller → Service → Model layering,
a modern stack (Laravel 13, Sanctum, Spatie, Nuxt 4), and a genuinely strong backend
test suite (~500 methods). **This project does not need a rewrite.** It needs
consolidation in a handful of places where growth outpaced structure, plus a
production-hardening pass now that the goal is paying customers.

The debt clusters into three themes; every priority below is an instance of one:

1. **Content is code.** The curriculum — the thing customers pay for — lives as
   **7,849 lines of PHP heredoc inside seeders** (larger than the whole service
   layer). Two (`LearningPathsSeeder`, `PathStepSeeder`) use `::create`, so they
   **duplicate every path/step on re-run**. The only prod-safe update path today is
   `migrate:fresh --seed`, which wipes user progress, XP, streaks and payments.

2. **Everything is synchronous.** No queue infrastructure is in use — `app/Jobs`
   doesn't exist, zero `ShouldQueue`. Judge0, Gemini (CV + LinkedIn), Jina, Stripe,
   and every notification email run inline in the request. The CV-by-URL path chains
   Jina (15s) + Gemini (60s) ≈ **75s of blocking I/O** against a **90s client abort**;
   each call pins a PHP-FPM worker, so a few concurrent analyses can exhaust the pool.

3. **Structure fragments as it grows.** Authorization spans **5 mechanisms / ~30 call
   sites** with zero Policies; the frontend carries **6 styling systems with no shared
   token layer** (root cause of the recurring dark-mode + slate/neutral bugs); a few
   files are god objects (`UserController` 482, `index.vue` 1601, `StepConceptView.vue`
   982); `path_steps` is a 24-column single-table-inheritance table.

---

## 2. Priorities (highest impact → lowest)

Ranking = business impact × blast radius × cost-of-delay, for the goal of shipping
and selling content without it falling over.

| # | Priority | Sev | Why it ranks here |
|---|----------|-----|-------------------|
| P1 | Content pipeline | Critical | Content is the product. Can't update prod without wiping paying users; non-idempotent seeders duplicate on re-run. Blocks the core revenue loop. |
| P2 | Async & resilience | Critical | Every AI/Judge0/email call is synchronous. A few concurrent analyses starve PHP-FPM; the 75s CV path races the 90s client timeout. |
| P3 | Neutralize vendor-branded content | High | A public path named *"APM with New Relic"* ships vendor copy — a personal/employer risk and a contradiction of the vendor-neutral observability pivot. Cheap fix. |
| P4 | Authorization → Policies | High | 5 mechanisms, ~30 call sites, `'admin'` magic strings vs `RoleEnum`. The temp ownership trait is explicitly marked for replacement. |
| P5 | CI/CD gate | High | ~500 tests that never run automatically; zero frontend tests. Cheapest high-leverage safety net for a solo founder. |
| P6 | Frontend design tokens | Medium | 6 styling systems, no token layer; live slate↔neutral drift; 3 "brand emeralds." Already cost debugging time. |
| P7 | Backend consistency | Medium | Missing PHP enums (StepType/Status/Difficulty); fat `UserController`; duplicated Judge0 wrapper; inline validation; envelope inconsistency. |
| P8 | Frontend consolidation | Medium | God components, two diverged `renderMarkdown`, shadowed types, split state (local refs → refetch). |
| P9 | Retire legacy + housekeeping | Medium | 5 legacy web controllers duplicate the API; env port drift; no caching layer for read-heavy static content. |

---

## 3. Findings, with evidence

### P1 — Content pipeline (Critical)

**Why:** the product can't evolve safely. Editing a challenge/path means editing a
heredoc + re-seeding; re-running the two `::create` seeders duplicates everything.

```
database/seeders/LearningPathsSeeder.php   3348 lines  (Path::create → duplicates)
database/seeders/ChallengeSeeder.php       2894 lines  (updateOrCreate — idempotent)
database/seeders/PathStepSeeder.php         579 lines  (::create → duplicates)
                                    total  7849 lines
```

**Fix:** move content to versioned files under `database/content/`; add an idempotent
`php artisan content:sync` that `updateOrCreate`s by slug. Seeders become thin callers.

### P2 — Async & resilience (Critical)

**Why:** synchronous external I/O is the scalability ceiling and a live-outage risk.
Redis is provisioned but idle; `QUEUE_CONNECTION=database` is set but nothing dispatches.

```
CvController.php:106  Jina  Http::timeout(15) ─┐  ~75s chained, one request
CvController.php:62   Gemini Http::timeout(60) ┘
useApi.ts:48          client AbortController = 90_000ms
app/Jobs = (does not exist)   ShouldQueue = 0 classes
```

**Fix:** notifications + welcome/verify mailables `ShouldQueue`; queue+cache on Redis;
`->retry(2,250)` + named status constants on a shared Judge0 client. Keep CV/LinkedIn
sync for now; record the FPM math as an ADR.

### P3 — Neutralize the New Relic–branded path (High)

**Why:** a live public path teaching your employer's product is exactly the collision
already flagged, and it undercuts the vendor-neutral observability track.

```
LearningPathsSeeder.php:2184  path "APM with New Relic"
LearningPathsSeeder.php:2234  participant NR as "New Relic Agent"
LearningPathsSeeder.php:2115  logging step name-drops "New Relic, Datadog"
```

**Fix:** rename to a neutral title ("Application Performance Monitoring"), strip
vendor copy/diagram labels. Best done **as part of P1** so it's a content-file edit.

### P4 — Consolidate authorization into Policies (High)

**Why:** a 5-way split is why the earlier ownership gaps went unnoticed.

```
UserController inline hasRole() at lines 45,70,102,114,129,155,165,260,301,319
+ EnsuresResourceOwnership trait  + service role checks
+ EntitlementService gating       + route middleware
'admin' magic string vs RoleEnum inconsistent.  app/Policies = (empty)
```

**Fix:** Plan/Path/Job/Course/User/PathStep policies using `RoleEnum`; replace the
temp trait + inline checks with `$this->authorize()`. The global
`AuthorizationException`→403 handler already gives the uniform envelope.

### P5 — CI gate (High)

**Why:** ~500 backend tests never run on push; the Nuxt SPA is untested.

```
.github = (does not exist)   tests/Unit = ExampleTest.php only (stub)
frontend: no *.test.* / *.spec.*, no vitest/playwright config
```

**Fix:** GitHub Actions — job 1: `pint --test` + `php artisan test` (sqlite in-memory);
job 2: `npm ci` + `nuxt typecheck` + build. Block merge on red. Seed Vitest with one test.

### P6 — Frontend design tokens (Medium)

**Why:** six styling systems with no shared tokens is the root cause of the recurring
dark-mode + slate/neutral bugs.

```
.mkt-* (marketing.vue) · .term-* (TerminalShell) · @nuxt/ui · dark-theme.css
· StepConceptView --sc-* vars · FlowDiagram hardcoded hex
app.config gray:neutral BUT main.css + dark-theme.css use slate; 8 components ship slate-N
3 "brand emeralds": #059669 / #10b981 / emerald-700
```

**Fix:** one CSS custom-property token layer at `:root` (dark flip once); point all
palettes at it; pick one emerald; delete slate holdouts.

### P7 — Backend consistency (Medium)

**Fix:** PHP enums `StepType`/`StepProgressStatus`/`StepDifficulty` + casts, validation
from `Enum::values()`; extract a shared `Judge0Client` (byte-for-byte dup across the two
execution services) and a `GeminiClient` (thin the CV/LinkedIn controllers); thin the
482-line `UserController`; move inline validation (`stepRules`, 200+ lines) to
FormRequests; pick one success envelope.

### P8 — Frontend consolidation (Medium)

**Fix:** split `index.vue` (1601) + `StepConceptView.vue` (982) into section components;
collapse the two `renderMarkdown` into one; delete dead `MermaidDiagram.vue`; delete
composable-local model interfaces shadowing `types/models.ts`; move domain composables to
`useState`; add a `createCrudResource<T>` factory.

### P9 — Retire legacy + housekeeping (Medium)

**Fix:** delete `AdminController`, `UsersController`, root `CourseController`,
`RegisterController` + their `routes/web.php` entries + orphaned Blade (the SPA replaced
all of it); fix env port drift (`.env.example` :3000 vs `.env` :3001); add the first real
caches — challenge index (invalidate on save) + dashboard aggregates only.

---

## 4. Implementation plan

### Phase A — Content pipeline (P1 + P3) · ~5 steps

**Gate:** `content:sync` run twice → `assertDatabaseCount` unchanged, zero duplicates.

- **A.1** Create `database/content/challenges/<slug>/` (`meta.json`, `description.md`, `boilerplate.php`, `tests.php`). One-off script dumps current `ChallengeSeeder` arrays into files; verify round-trip equality vs DB.
- **A.2** Same for paths/steps: `paths/<slug>/meta.json` + `steps/NN-<slug>.md` with front-matter (type, difficulty, estimated_minutes, challenge_slug, resources) and tiered `concept_content` as body.
- **A.3** While extracting: rename the "APM with New Relic" path to a neutral title and strip vendor copy/diagram labels (P3, now a data edit).
- **A.4** `ContentSyncCommand` (`content:sync [--dry-run]`): reads `database/content/`, `updateOrCreate` by slug, reports created/updated/unchanged. Gut the `::create` bodies in `LearningPathsSeeder`/`PathStepSeeder` — seeders just call the command.
- **A.5** Feature test: sync twice → identical DB; edit a file → re-sync updates the row.

### Phase B — Async & resilience (P2) · ~5 steps

**Gate:** no user request blocks on email; Redis serving queue+cache; worker documented.

- **B.1** `QUEUE_CONNECTION=redis`, `CACHE_STORE=redis`; document `ddev artisan queue:work` (+ prod supervisor note).
- **B.2** All 8 notifications + welcome/verify mailables `implements ShouldQueue`. Tests: `Notification::fake()`/`Queue::fake()` at trigger points.
- **B.3** Extract `Services/Judge0/Judge0Client.php` (dedup the two services); add `->retry(2,250)`; status IDs 5/6 → named constants; one 30s timeout.
- **B.4** Extract `Services/Ai/GeminiClient.php` + thin `CvAnalysisService`/`LinkedInAnalysisService`; log upstream failures (502 branches log nothing today).
- **B.5** ADR: CV/LinkedIn stay sync for now; document FPM math + the queued+poll scale path.

### Phase C — Policies + CI (P4 + P5) · ~6 steps

**Gate:** `grep hasRole('` in Api controllers → 0; CI green on a PR.

- **C.1** Create Plan/Path/Job/Course/User/PathStep policies using `RoleEnum`; rules: update/delete = owner-or-admin, view = any auth, user = self-or-admin, erase = admin.
- **C.2** Replace the temp ownership trait + all inline `hasRole` checks with `$this->authorize()`. Delete the bespoke throw/catch choreography.
- **C.3** Sweep magic strings → `RoleEnum`; align `ChallengeDifficulty` with `path_steps.difficulty` or document the difference.
- **C.4** Move remaining inline `$request->validate()` into FormRequests (`PathStepRequest`, analyze/run/checkout requests).
- **C.5** GitHub Actions job 1: pint `--test` + `php artisan test`.
- **C.6** GitHub Actions job 2: `npm ci` + `nuxt typecheck` + build. Add Vitest + one composable test.

### Phase D — Frontend tokens + backend consistency (P6 + P7) · ~7 steps

**Gate:** one token layer drives all palettes; PHP enums cast on models; challenge index paginated.

- **D.1** Define the CSS custom-property token layer at `:root` (+ dark flip once); pick one emerald.
- **D.2** Point `.mkt-*`, `.term-*`, `@nuxt/ui` palette, and `--sc-*` at the tokens; delete slate holdouts + `dark-theme.css` hardcodes.
- **D.3** PHP enums `StepType`/`StepProgressStatus`/`StepDifficulty` + casts; build validation from `Enum::values()`.
- **D.4** Thin `UserController` — dashboard/activity/progress into services.
- **D.5** Paginate + trim heavy columns from `ChallengeController::index`; add index on `path_steps.challenge_slug`; batch the `reorder` UPDATE.
- **D.6** Pick one success envelope; align stragglers; make `UserResource` the single source of the `role` field.
- **D.7** First caches: challenge index (invalidate on save) + dashboard aggregates only.

### Phase E — Frontend consolidation + legacy retire (P8 + P9) · ~6 steps

**Gate:** no composable-local model interfaces; legacy web controllers gone; suite green.

- **E.1** `createCrudResource<T>` factory; rewrite the 5 CRUD composables as thin wrappers (public API identical).
- **E.2** Delete composable-local `Course/Plan/Path/Job` interfaces; import from `types/models.ts`; fix known drift.
- **E.3** Collapse the two `renderMarkdown` into one util; delete dead `MermaidDiagram.vue`.
- **E.4** Split `index.vue` and `StepConceptView.vue` into section components.
- **E.5** Move domain composables to `useState` (shared, not per-component refetch).
- **E.6** Delete the 5 legacy web controllers + their routes + orphaned Blade; fix env port drift.

---

## 5. Target structure (additions/moves only — no rewrite)

```
app/
├── Enums/                    + StepType, StepProgressStatus, StepDifficulty
├── Policies/                 NEW — the single authorization mechanism
├── Jobs/                     NEW — queued analysis; emails via ShouldQueue
├── Services/
│   ├── Judge0/Judge0Client.php   NEW — shared HTTP client (dedup 2 services)
│   ├── Ai/GeminiClient.php       NEW — endpoint + JSON extraction
│   └── Ai/{Cv,LinkedIn}AnalysisService.php
└── Http/Requests/            ALL validation lives here

database/
├── content/                  NEW — curriculum as versioned files
│   ├── challenges/<slug>/{meta.json, description.md, boilerplate.php, tests.php}
│   └── paths/<slug>/{meta.json, steps/NN-*.md}
└── seeders/                  → thin loaders over content:sync

frontend/app/
├── assets/css/tokens.css     NEW — one custom-property layer (light + dark)
├── composables/
│   ├── createCrudResource.ts NEW — generic factory (replaces 5 copies)
│   └── use{Courses,Jobs,…}.ts   thin wrappers, useState-backed
└── types/models.ts           SINGLE source of truth
```

---

## 6. Verification map (what proves each phase)

| Phase | Proof |
|---|---|
| A | `content:sync` run twice → `assertDatabaseCount` unchanged; "APM with New Relic" gone |
| B | `Queue::fake` assertions; `Services/Judge0/Judge0Client.php` exists; both execution services < 200 lines |
| C | `grep -rn "hasRole('" app/Http/Controllers/Api` → 0; `app/Policies` populated; CI green on a PR |
| D | Enum casts in models; challenge index paginated; one token layer drives all palettes |
| E | 5 domain composables < 30 lines each; legacy controllers gone; suite green |
