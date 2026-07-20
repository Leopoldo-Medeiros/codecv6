# ONBOARDING — start here

This is the orientation guide for **any developer or AI** picking up CODECV. It is
the "state of the world + what to watch out for + what to do next" layer.
For *how the code is structured*, read **`CLAUDE.md`** (root) and `frontend/CLAUDE.md` —
this file does not repeat them, it complements them.

Reading order for a cold start:
1. This file (10 min) — what the project is, how to run it, the traps.
2. `CLAUDE.md` (root) — architecture, conventions, domain model, feature-by-feature notes.
3. `frontend/CLAUDE.md` — frontend patterns in compact form.
4. `docs/roadmap.md` — what to build next (product + technical, one page).
5. `docs/architecture-review.md` — the detailed technical debt review + phased plan.
6. `docs/` — topic deep-dives (`authentication.md`, `testing-guide.md`, `troubleshooting.md`, …).
7. `DEPLOYMENT.md` — the production runbook (read before touching deploy).

---

## 1. What this is (60 seconds)

CODECV is a career-development SaaS for developers: users practice with **real-world
coding challenges** (run in a Judge0 sandbox), work through **learning paths**
(reading / lab / challenge / quiz / incident steps), earn **XP, streaks, badges and
certifications**, and can publish a **public skill profile**. Consultants build
**training plans** for clients. Monetization is Stripe (a self-serve Practice
subscription + one-time/recurring coaching tiers), with an AI-powered CV / LinkedIn
analyser (Google Gemini) as a lead feature.

The product is built around a **practice funnel** (F1–F6, all shipped):
anonymous teaser challenges (F2) → signup → gamified practice (F1) → public skill
profile (F3) → paid content gating (F4) → server-graded quizzes (F5) → coaching
upsell nudges (F6). A **waitlist** on the homepage and dashboards measures demand
for the next track to build (observability / AI-for-devs / AI-for-support).

**Stack:** Laravel 13 API (PHP 8.4) + Nuxt 4 frontend. MySQL, Redis (provisioned,
still idle), Sanctum auth, Spatie roles, Stripe, Judge0, Gemini. Local env is DDEV.

**Roles:** `admin` (1), `client` (2), `consultant` (3). See `app/Enums/RoleEnum.php`.

---

## 2. Run it locally

```bash
ddev start                          # brings up PHP 8.4, MySQL, Redis, nginx
ddev composer install
ddev artisan migrate:fresh --seed   # schema + users, courses, curriculum (via content:sync), badges
cd frontend && npm install && npm run dev   # frontend on the HOST, not in DDEV
```

- **API:** `http://codecv6.ddev.site`  ·  **Frontend:** `http://localhost:3001`
  (the dev server port is **3001**, set in `frontend/nuxt.config.ts`; CORS and
  Sanctum config must — and do — list the 3001 origins)
- Seeded logins (all password `password`): `admin@admin.com`, `consultant@consultant.com`, `client@client.com`
- Tests: `ddev artisan test --compact`  ·  Format: `ddev exec vendor/bin/pint --dirty`
- Curriculum sync: `ddev artisan content:sync [--dry-run]`

---

## 3. The most important things to know (read before touching code)

These are the non-obvious facts that will bite you. Ranked by how likely they are to
cause a wrong assumption.

1. **The curriculum lives in `database/content/`, not in code.** 34 challenges and
   8 paths (~62 steps) are versioned files (`meta.json` + Markdown with `---json`
   front-matter). `php artisan content:sync` upserts them idempotently (proven by
   `ContentSyncTest`). **Editing a lesson = editing a content file + re-syncing** —
   never author curriculum inside seeders (the old heredoc seeders were deleted),
   and never run `migrate:fresh` in production to update content (it wipes user
   progress, XP and payments — `content:sync` alone is the prod-safe path).

2. **Content policy (product rule):** every challenge and example must be a
   **real-world development scenario** — never Exercism-style fictional puzzles.
   This is a product decision, not a style preference. And content must be
   **deep, not thin**: the completeness bar is LabEx/Exercism — quality anatomy,
   exemplars and the authoring backlog are in `docs/content-strategy.md`.
   Deepening the catalog is the #1 product workstream.

3. **There are TWO parallel stacks.** The modern JSON API (`app/Http/Controllers/Api/`,
   `routes/api.php`) *and* a legacy server-rendered Blade stack
   (`AdminController`, `UsersController`, `CourseController`, `RegisterController`
   at controller root + `routes/web.php` + `resources/views/`). Both are live. The
   Blade stack duplicates users/courses with older patterns and is slated for
   deletion (roadmap Phase E). **Build new features in the API only.**

4. **AI = Gemini, never Anthropic.** `CvController` and `LinkedInController` call
   Google Gemini (`GEMINI_API_KEY`). There is no Anthropic integration; do not add
   an `ANTHROPIC_API_KEY`.

5. **Everything is synchronous.** No `app/Jobs`, zero `ShouldQueue`, Redis idle.
   CV/LinkedIn analysis blocks a PHP-FPM worker for up to ~75s (Jina 15s + Gemini
   60s, against a 90s client abort); Judge0 runs block up to 30s; every
   notification email sends inline. This is the scalability ceiling — don't add
   more synchronous external calls to hot paths. Fixing it is roadmap Phase B.

6. **Authorization is fragmented — no Laravel Policies yet.** Route middleware
   (`role:`), inline controller checks, service-layer throws
   (`EnsuresResourceOwnership` trait), and `EntitlementService` gating coexist.
   The known exploitable gaps (role self-escalation, ownership bypass,
   `consultant_id` spoofing) were **fixed in July 2026**, but the fragmentation
   that caused them remains. When adding an endpoint, copy the strictest neighbor.
   Consolidation into Policies is roadmap Phase C.

7. **Content gating means "has EVER paid".** `EntitlementService` grants practice
   access on any PAID payment in a granting tier — the Stripe webhook does not yet
   handle subscription cancellation/expiry (`customer.subscription.deleted`).
   A lapsed subscriber keeps access. Known limitation, on the roadmap.

8. **Tests can never hit live APIs.** The base `TestCase` calls
   `Http::preventStrayRequests()` — any outbound HTTP not covered by `Http::fake()`
   throws. Keep it that way; fake Judge0/Gemini/Jina/Stripe in every new test.

9. **DB enums have no PHP mirror.** `path_steps.type` / `.difficulty` and
   `user_step_progress.status` are magic strings (`'done'`, `'in_progress'`) across
   controllers. Only `Challenge.difficulty` and `Payment` have real PHP enums.
   Grep for the string, not a type. (Roadmap Phase D adds the enums.)

10. **Two frontend "sources of truth" for types.** `frontend/app/types/models.ts`
    AND per-composable local interfaces exist and have drifted. `PathStep` +
    `user_status` live in `composables/usePaths.ts`, not `models.ts`.

11. **The frontend dev server runs on port 3001** (not 3000 — that port was often
    occupied). All docs, `.env.example` CORS/Sanctum lines, and the local `.env`
    now agree on 3001. If you see CSRF/CORS failures, check that the origin you're
    browsing matches `FRONTEND_URL`. Production is different: the frontend
    container serves on 3000 behind a proxy (`DEPLOYMENT.md`).

12. **Language policy.** All code, comments, identifiers, commits, and docs in
    **English**. Explanations/communication may be Portuguese-BR or English.

---

## 4. Current state (as of 2026-07-19)

**Health:** 634 backend tests green (1,197 assertions), Pint clean, frontend builds.
CI (GitHub Actions) runs Pint + PHPUnit + a frontend build on every push/PR.

**What landed in July 2026** (details in `docs/architecture-review.md` §0):
- Security phase 0: role self-escalation closed, resource ownership enforced,
  `consultant_id` spoofing closed, expensive endpoints throttled, Stripe webhook
  idempotency.
- Test coverage for all previously-untested route groups (challenges, playground,
  CV, LinkedIn, notifications, onboarding) + `Http::preventStrayRequests()`.
- Content pipeline: curriculum extracted to `database/content/`, idempotent
  `content:sync`, heredoc seeders deleted, New-Relic-branded paths removed.
- Sprint 0 ops: CI pipeline, Sentry wiring (dormant until a DSN is set),
  production Docker images + `DEPLOYMENT.md` runbook.
- Practice funnel F1–F6 complete; observability track Phase A (7 incident readers,
  certification seal); "REST APIs & Auth" track; waitlist demand-sensing.

**Known gaps (the honest list):**
- No Laravel Policies; authorization still spans 4 mechanisms (see §3.6).
- Everything synchronous; Redis idle; no queue worker (see §3.5).
- Subscription lapse not handled by the webhook (see §3.7).
- Legacy Blade stack still deployed alongside the API (see §3.3).
- Zero frontend tests; CI has no typecheck step.
- Frontend carries ~6 styling systems with no shared token layer (recurring
  dark-mode bug source).
- God files: `UserController` (~480 lines), `pages/index.vue` (~1,600),
  `StepConceptView.vue` (~980).
- `LICENSE` file says MIT while README says proprietary — decide and align.
- Payments are live-tested only via Stripe CLI; no production deploy has happened
  yet (host not chosen — see `DEPLOYMENT.md` "Choosing a host").

---

## 5. What to build next

**`docs/roadmap.md`** is the single prioritized list (product + technical).
The technical detail behind each phase is in `docs/architecture-review.md`
(phases B–E with acceptance criteria per step).

Ground rules that keep the project safe to evolve:
- One commit per step, conventional commits; suite green (`ddev artisan test
  --compact`) + Pint clean before every commit. CI enforces both.
- Do not start a phase before the previous phase's gate is met.
- Product changes should be justified by the waitlist / dashboard demand data
  where possible — that instrumentation exists precisely to decide what to build.

---

## 6. Where things are

| You want… | Look in |
|-----------|---------|
| Architecture, conventions, domain model, feature notes | `CLAUDE.md` (root) |
| Frontend patterns (composables, components, layouts) | `frontend/CLAUDE.md` |
| The curriculum (challenges, paths, steps) | `database/content/` + `php artisan content:sync` |
| Content quality bar + authoring backlog | `docs/content-strategy.md` |
| Auth flow, Sanctum, Fortify, OAuth | `docs/authentication.md` |
| How to run/test/debug | `docs/development-workflow.md`, `docs/testing-guide.md`, `docs/troubleshooting.md` |
| Env vars and what they do | `docs/environment-variables.md`, `.env.example` |
| What to build next | `docs/roadmap.md` |
| Technical debt detail + phased fix plan | `docs/architecture-review.md` |
| Production deploy runbook | `DEPLOYMENT.md` |
| CI pipeline | `.github/workflows/ci.yml` |
| Pricing/tiers, coaching ladder, waitlist topics | `config/pricing.php`, `config/coaching.php`, `config/waitlist.php` |

Business/product rationale that is **not** derivable from the code (why a feature
exists, tier pricing strategy, target market) is not fully in the repo — the
product owner is Leo (leopoldo@abse.com.br). The short version: three-tier
IT-career-coaching SaaS aimed at the Dublin/EU market; the self-serve practice
tier funds validation of the coaching tiers; the waitlist decides the next track.
