# ONBOARDING — start here

This is the orientation guide for **any developer or AI** picking up CODECV. It is
the "state of the world + what to watch out for + what to do next" layer.
For *how the code is structured*, read **`CLAUDE.md`** (root) and `frontend/CLAUDE.md` —
this file does not repeat them, it complements them.

Reading order for a cold start:
1. This file (10 min) — what the project is, how to run it, the traps.
2. `CLAUDE.md` (root) — architecture, conventions, domain model.
3. `docs/` — topic deep-dives (`authentication.md`, `testing-guide.md`, `troubleshooting.md`, …).
4. `docs/architecture-review.md` — the prioritized technical roadmap (what to build next).

---

## 1. What this is (30 seconds)

CODECV is a career-development SaaS: consultants build **learning paths** (ordered
steps: reading / lab / coding challenge / quiz) and **training plans** for **clients**,
who work through them while progress is tracked. It monetizes via Stripe (one-time
tiers + a subscription), runs user-submitted PHP in a Judge0 sandbox for coding
challenges, and uses Google Gemini for CV and LinkedIn analysis.

**Stack:** Laravel 13 API (PHP 8.4) + Nuxt 4 SPA/SSG frontend. MySQL, Redis
(provisioned), Sanctum auth, Spatie roles. Local env is DDEV.

**Roles:** `admin` (1), `client` (2), `consultant` (3). See `app/Enums/RoleEnum.php`.

---

## 2. Run it locally

```bash
ddev start                          # brings up PHP 8.4, MySQL, Redis, nginx
ddev composer install
ddev artisan migrate:fresh --seed   # schema + seeded users, courses, challenges, paths
cd frontend && npm install && npm run dev   # frontend on the HOST, not in DDEV
```

- **API:** `http://codecv6.ddev.site`  ·  **Frontend:** `http://<LAN-IP>:3000`
- Seeded logins (all password `password`): `admin@admin.com`, `consultant@consultant.com`, `client@client.com`
- Tests: `ddev artisan test --compact`  ·  Format: `ddev exec vendor/bin/pint --dirty`

---

## 3. The most important things to know (read before touching code)

These are the non-obvious facts that will bite you. Ranked by how likely they are to
cause a wrong assumption.

1. **There are TWO parallel stacks.** A modern JSON API (`app/Http/Controllers/Api/`,
   `routes/api.php`) *and* a legacy server-rendered Blade stack (`app/Http/Controllers/`
   root, `routes/web.php`, `resources/views/`). Both are live. The Blade stack
   duplicates users/courses with different patterns and is slated for deletion
   (roadmap Phase 3). **Build new features in the API only.** The `public/css/*.css`
   theme files exist *solely* to serve those Blade views — they are not dead until the
   Blade stack is removed.

2. **AI = Gemini, never Anthropic.** Both `CvController` and `LinkedInController` call
   Google Gemini (`GEMINI_API_KEY`). Older docs said "Anthropic" — that was wrong and
   has been corrected. There is no Anthropic integration; do not add `ANTHROPIC_API_KEY`.

3. **Authorization lives in four different places** — route middleware (`role:`),
   inline controller `if`-checks, service-layer `throw`, and (for some routes)
   nothing beyond `auth:sanctum`. There are **no Laravel Policies yet**. This
   fragmentation has produced real security gaps (see §4). When adding an endpoint,
   check the authorization pattern of its neighbors and prefer the strictest.

4. **Everything is synchronous.** No `app/Jobs`, no queued notifications. CV/LinkedIn
   analysis blocks a PHP-FPM worker for up to ~75s; Judge0 runs block for up to 30s.
   Redis is provisioned but idle (cache/queue/session all use database/file). This is
   the scalability ceiling — don't add more synchronous external calls to hot paths.

5. **The curriculum is code.** All challenge and learning-path content lives as ~5,200
   lines of PHP heredoc inside `database/seeders/` (`ChallengeSeeder`,
   `LearningPathsSeeder`). Editing a lesson means editing PHP and re-seeding.
   `LearningPathsSeeder` is **not idempotent** — re-running it duplicates paths.
   `ChallengeSeeder`/`PathStepSeeder` use `updateOrCreate`/`firstOrCreate` and are safe.

6. **Content policy (product rule):** every challenge and example must be a
   **real-world development scenario** — never Exercism-style fictional puzzles.
   This is a product decision, not a style preference.

7. **DB enums have no PHP mirror.** `path_steps.type`, `path_steps.difficulty`, and
   `user_step_progress.status` are enforced as MySQL enums but represented as **magic
   strings** across controllers (e.g. `'done'`, `'in_progress'`). Only
   `Challenge.difficulty` and `Payment` have real PHP enums. Grep for the string, not a type.

8. **Two frontend "sources of truth" for types.** `frontend/app/types/models.ts` AND
   per-composable local interfaces exist and have already drifted. `PathStep` +
   `user_status` are defined in `composables/usePaths.ts`, not `models.ts`.

9. **Port drift gotcha.** `frontend/nuxt.config.ts` sets `devServer.port: 3001`, but
   CORS (`FRONTEND_URL`), Sanctum stateful domains, and all docs assume `3000`. If the
   dev server comes up on 3001 you'll get CSRF/CORS failures. Check what `npm run dev`
   actually prints and make CORS match. (Unreconciled — decide one port.)

10. **Language policy.** All code, comments, identifiers, commits, and docs in
    **English**. Explanations/communication may be Portuguese-BR or English.

---

## 4. Current state & known issues (as of 2026-07-08)

A full audit was done across backend, data, security, tests, frontend, and
integrations. The findings and the fix plan are in **`docs/architecture-review.md`**.
The headline items a newcomer must be aware of:

**Security — not yet fixed, exploitable (roadmap Phase 0 addresses these):**
- A `client` can self-escalate to `consultant` via `PUT /users/{user}` with `role=3`
  (`UserService::syncRole` only blocks escalation to admin).
- No ownership checks on Plans / Paths / Jobs / PathSteps — any consultant can mutate
  any other consultant's records (only `role:` middleware guards them).
- `consultant_id` is spoofable via PlanRequest/PathRequest/JobRequest.
- No rate limiting on the expensive endpoints (`/cv/analyze`, `/linkedin/analyze`,
  `/challenges/{slug}/run`, `/checkout/session`) — cost-abuse vector on your API keys.

**Testing:** 306 tests cover the CRUD surface well (including real authorization-boundary
and Stripe-webhook-signature tests), but **6 route groups have zero tests**: challenges,
playground, cv, linkedin, notifications, onboarding — exactly the product differentiators.
There are no frontend tests. `Http::fake` is used nowhere, and there is no
`Http::preventStrayRequests()` guard, so a future test hitting Gemini/Judge0 would make
a live call.

**Ops:** No CI pipeline, no error tracking, no deploy/backup story in the repo.
The `LICENSE` is MIT — confirm that is intended for a proprietary SaaS.

---

## 5. What to build next

The prioritized, step-by-step plan is **`docs/architecture-review.md`** — 9 phases,
~43 small steps, each with acceptance criteria and a regression test, written so any
dev or AI can execute one step at a time (one commit per step, suite green = done).

Recommended execution order (security first, then the CI/tests that make everything
else safe to change):

| Order | Phase | Why first |
|-------|-------|-----------|
| 1 | **Phase 0** — security hotfixes | Exploitable today; cheap now, expensive after launch |
| 2 | **CI** (step 8.3, pulled forward) | Protects every change that follows |
| 3 | **Phase 1** — test the uncovered surface | Nothing downstream is safe to refactor without it |
| 4 | **Phase 2** — Policies | Kills the whole class of §4 authorization bugs |
| 5 | **Phase 3** — retire the legacy Blade stack | Removes duplicated logic + its own security holes |
| 6+ | Phases 4–8 | Async/Redis, content pipeline, consistency, frontend, housekeeping |

**Do not start a phase before the previous phase's tests are green.** The phases are
ordered by risk on purpose.

---

## 6. Where things are

| You want… | Look in |
|-----------|---------|
| Architecture, conventions, domain model | `CLAUDE.md` (root) |
| Frontend patterns (composables, components, layouts) | `frontend/CLAUDE.md` |
| Auth flow, Sanctum, Fortify, OAuth | `docs/authentication.md` |
| How to run/test/debug | `docs/development-workflow.md`, `docs/testing-guide.md`, `docs/troubleshooting.md` |
| Env vars and what they do | `docs/environment-variables.md`, `.env.example` |
| The technical roadmap | `docs/architecture-review.md` |
| Business context / product direction | product owner (Leo) — not fully in-repo |

Business/product rationale that is **not** derivable from the code (why a feature
exists, tier pricing strategy, target market) is not in the repo — ask the product owner.
