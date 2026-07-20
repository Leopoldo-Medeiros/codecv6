# CODECV

A career-development SaaS for developers: practice with real-world coding
challenges, work through learning paths, earn XP / streaks / badges /
certifications, publish a public skill profile — and get coached by a consultant
when you're ready.

> **New here (human or AI)? Start with [`ONBOARDING.md`](ONBOARDING.md)** — it is
> the orientation layer: state of the project, the traps, and what to build next.

## What's in the product

- **Coding challenges** — real-world PHP exercises executed in a Judge0 sandbox,
  with anonymous "teaser" challenges as the public funnel entry
- **Learning paths** — ordered steps: reading (tiered junior/mid/senior content),
  labs, challenges, server-graded quizzes, and *incident readers* (read curated
  traces/metrics/logs and diagnose the fault — the observability track)
- **Gamification** — XP, daily streaks, achievement badges, certification seals,
  a GitHub-style activity heatmap
- **Public skill profile** — opt-in `/u/{slug}` page with stats, badges and
  completed challenges
- **Monetization** — Stripe: a self-serve Practice subscription (content gating)
  plus one-time/recurring coaching tiers, with earned in-app upsell nudges
- **AI features** — CV analysis and LinkedIn profile scoring via Google Gemini
- **Consultant tooling** — training plans, client management, progress
  notifications; admin demand dashboard + a "coming soon" waitlist that measures
  which track to build next

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 13 (PHP 8.4), Sanctum, Spatie permissions |
| Frontend | Nuxt 4 (Vue 3, TypeScript), @nuxt/ui v2, Tailwind v3 |
| Database | MySQL 8.0 (Redis provisioned for the async phase) |
| Code execution | Judge0 (sandboxed PHP) |
| AI | Google Gemini |
| Payments | Stripe (Checkout + webhooks) |
| Search | Algolia via Laravel Scout (optional, off by default) |
| Monitoring | Sentry (backend + frontend, dormant until DSN set) |
| CI | GitHub Actions (Pint + PHPUnit + frontend build) |
| Dev environment | DDEV · Production: Docker images (see `DEPLOYMENT.md`) |

## Getting Started

### Prerequisites

- Docker Desktop or [OrbStack](https://orbstack.dev/) (recommended for macOS)
- [DDEV](https://ddev.readthedocs.io/en/stable/) — `brew install ddev/ddev/ddev`
  (macOS) · `curl -fsSL https://ddev.com/install.sh | bash` (Linux)

### Installation

```bash
git clone https://github.com/Leopoldo-Medeiros/codecv6.git
cd codecv6

ddev start                          # PHP 8.4, MySQL, Redis, nginx
ddev composer install
cp .env.example .env
ddev artisan key:generate
ddev artisan migrate --seed         # schema + users + curriculum (content:sync) + badges

cd frontend && npm install          # frontend deps on the HOST (not in DDEV)
```

### Running

```bash
# Backend runs automatically via DDEV → http://codecv6.ddev.site

cd frontend
npm run dev                         # → http://localhost:3001  (port 3001, not 3000)
```

The frontend dev server runs **on the host** and listens on **port 3001**
(`frontend/nuxt.config.ts` `devServer`); CORS and Sanctum config in
`.env.example` already allow the 3001 origins. `frontend/.env` needs at minimum
`NUXT_PUBLIC_API_BASE=http://codecv6.ddev.site`.

### Default Users

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@admin.com | password |
| Consultant | consultant@consultant.com | password |
| Client | client@client.com | password |

## Everyday Commands

```bash
# Backend
ddev artisan test --compact         # run the test suite (must be green before commit)
ddev exec vendor/bin/pint --dirty   # format changed PHP files (CI enforces style)
ddev artisan content:sync           # sync curriculum from database/content/ (--dry-run supported)
ddev artisan migrate                # run migrations

# Frontend (from frontend/)
npm run dev                         # dev server (port 3001)
npm run build                       # production build → .output/
```

## The Curriculum Is Data

All challenges and learning paths live as versioned files in
**`database/content/`** (34 challenges, 8 paths). Editing a lesson = editing a
content file + `ddev artisan content:sync` (idempotent — safe to re-run, safe in
production). Never author curriculum in seeders and never use `migrate:fresh` to
update content in production. Product rule: every challenge must be a
**real-world dev scenario**, never a fictional puzzle.

## Documentation Map

| Document | Purpose |
|----------|---------|
| [`ONBOARDING.md`](ONBOARDING.md) | **Start here** — state of the world, traps, what's next |
| [`CLAUDE.md`](CLAUDE.md) | Architecture, conventions, domain model, feature-by-feature notes |
| [`frontend/CLAUDE.md`](frontend/CLAUDE.md) | Frontend patterns in compact form |
| [`docs/roadmap.md`](docs/roadmap.md) | Prioritized next steps (product + technical) |
| [`docs/architecture-review.md`](docs/architecture-review.md) | Technical-debt review + phased plan with acceptance criteria |
| [`DEPLOYMENT.md`](DEPLOYMENT.md) | Production runbook (Docker images, env, rollback) |
| [`docs/`](docs/) | Topic deep-dives: auth, testing, env vars, troubleshooting, workflows |

## License

Proprietary — all rights reserved. (Note: the `LICENSE` file currently says MIT;
this contradiction is tracked in `docs/roadmap.md` item 0.6 — do not assume
open-source licensing until resolved.)
