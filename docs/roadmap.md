# Roadmap — what to build next

> Written 2026-07-19. One page, prioritized. Product and technical work
> interleaved on purpose: the project's stated goal is **paying customers**, so
> launch-blocking work outranks internal refactors. Technical phases reference
> `docs/architecture-review.md` (which has per-step acceptance criteria).
>
> Rule of thumb when priorities conflict: *does this get a paying user, or
> protect one?* If neither, it waits.

## Horizon 0 — Launch (days, not weeks)

The product is feature-complete for a first launch (practice funnel F1–F6,
two tracks, payments, CI, deploy images). What's missing is operational:

| # | Item | Notes |
|---|------|-------|
| 0.1 | Pick a host and deploy | `DEPLOYMENT.md` runbook is ready; recommendation there is Fly.io or Render. First deploy checklist is in the runbook. |
| 0.2 | Go live on Stripe | Swap test keys for live keys; register the production webhook endpoint; run one real €-payment end-to-end. |
| 0.3 | Activate Sentry | Create the two projects, set `SENTRY_LARAVEL_DSN` + `NUXT_PUBLIC_SENTRY_DSN`; wiring is already in place. |
| 0.4 | Production email | Switch `MAIL_MAILER=resend` and verify the sending domain (`codecv.ie`). |
| 0.5 | Static OG image | `frontend/public/og-image.png` (1200×630) — og-image module is off by design. |
| 0.6 | Resolve the license contradiction | `LICENSE` is MIT; README says proprietary. Decide one. |

**Gate:** a stranger can sign up, pay, and practice on the production URL; errors
land in Sentry; a payment shows in the Stripe dashboard.

## Horizon 1 — Protect the launch (first weeks after)

| # | Item | Why |
|---|------|-----|
| 1.1 | **Subscription lapse handling** | Today "has ever paid" = permanent access. Handle `customer.subscription.deleted`/`invoice.payment_failed` in `StripeWebhookController`, add an `expires_at`/status to entitlement checks in `EntitlementService`. This is revenue leakage once real subscribers exist. |
| 1.2 | **Async & resilience (Phase B)** | Queue notifications/emails on Redis, extract `Judge0Client`/`GeminiClient` with retries. Detail: architecture-review Phase B (5 steps). Prevents the first traffic spike from exhausting PHP-FPM. |
| 1.3 | **Uploads to S3** | Local disk is per-instance and ephemeral in containers (`DEPLOYMENT.md` → Storage). Needed before scaling past one API instance. |
| 1.4 | **Backups** | Automated MySQL backups + a tested restore. Not in the repo at all today. |

**Gate:** no user request blocks on email; a killed API container loses nothing.

## Horizon 2 — Structural consolidation (ongoing, one phase at a time)

These are the remaining phases of `docs/architecture-review.md`, in order:

| Phase | Summary | Gate |
|-------|---------|------|
| C | Authorization → Policies; magic role strings → `RoleEnum`; inline validation → FormRequests; add `nuxt typecheck` + Vitest to CI | `grep hasRole('` in Api controllers → 0; CI runs typecheck |
| D | Frontend design-token layer (kills the recurring dark-mode bugs); PHP enums for step type/status/difficulty; thin `UserController`; paginate challenge index; first caches | One token layer drives all palettes; enum casts on models |
| E | Frontend consolidation (`createCrudResource` factory, single `renderMarkdown`, split god components); **delete the legacy Blade stack** | Legacy web controllers gone; suite green |

## Product horizon — decided by data, not opinion

**#1 — Content depth (product owner's explicit direction, 2026-07-19):** the
catalog is too thin next to LabEx / Exercism / the courses on Class Central —
matching their completeness is the goal. The full gap analysis, quality bar
(canonical exemplars), and prioritized authoring backlog live in
**`docs/content-strategy.md`**. This is the main product workstream and runs in
parallel with Horizon 0 — content authoring never blocks, and is never blocked
by, launch ops. Start with the backlog's item 1 (bring all 34 challenge
descriptions to the anatomy bar).

The rest of the demand-sensing instrumentation exists; use it:

- **Next track = the waitlist winner.** `GET /admin/waitlist` (admin dashboard
  card) counts votes for Observability / AI for Developers / AI for IT Support.
  Build the track that pulls hardest. Adding a candidate = one entry in
  `config/waitlist.php` + a card.
- **Observability track Phase B** (live environments where the learner instruments
  a running service) is deliberately deferred until Phase A (incident readers)
  shows demand — check completion rates on the "Observability 101" path and the
  waitlist before investing.
- **More Option-A tracks** ("full-stack credibility": REST APIs & Auth shipped
  first) — authoring is now cheap: content files + `content:sync`, no code.
- **Coaching-tier conversion** — `CoachingNudge` (F6) is instrumented by design;
  watch which nudge converts before changing the ladder in `config/coaching.php`.
- **Content policy reminder:** every new challenge/example must be a real-world
  dev scenario (product rule; see ONBOARDING §3.2).

## Standing rules for any executor (human or AI)

1. Suite green + Pint clean before every commit (`ddev artisan test --compact`,
   `ddev exec vendor/bin/pint --dirty`). CI enforces both on PRs.
2. One commit per step, conventional commits, English everywhere in code/docs.
3. New endpoints copy the strictest neighboring authorization pattern (until
   Phase C lands Policies).
4. Fake all outbound HTTP in tests (`Http::preventStrayRequests()` is global).
5. Curriculum changes go through `database/content/` + `content:sync` — never
   seeders, never `migrate:fresh` in production.
6. Update `CLAUDE.md` / `ONBOARDING.md` when a change makes them wrong — these
   files are the next maintainer's onboarding.
