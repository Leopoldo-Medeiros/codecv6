# Deployment Runbook

How CODECV ships to production. Until now the only documented environment was
local DDEV; this is the repeatable, host-agnostic path. Companion to
`docs/architecture-review.md`.

## Topology

Two stateless app containers plus two managed backing services:

| Component | Image / service | Port | Notes |
|-----------|-----------------|------|-------|
| API (Laravel) | `Dockerfile` (php-fpm + nginx) | 8080 | Stateless; scale horizontally |
| Frontend (Nuxt) | `frontend/Dockerfile` (Nitro Node) | 3000 | Stateless; SSR + prerendered marketing |
| Database | MySQL 8 (managed) | 3306 | The one stateful store |
| Cache / queue | Redis (managed) | 6379 | Idle today; used from Phase B (async) |

Object storage (S3-compatible) is recommended for user uploads in production so
the API containers stay stateless — see **Storage** below.

## Required environment

Set these as secrets on the host (never commit them). Minimum viable set:

```
APP_NAME=CODECV
APP_ENV=production
APP_KEY=                      # php artisan key:generate --show
APP_DEBUG=false
APP_URL=https://api.codecv.ie
APP_FRONTEND_URL=https://codecv.ie          # single canonical URL (redirects)
FRONTEND_URL=https://codecv.ie              # CORS allow-list (comma-separated)

DB_CONNECTION=mysql
DB_HOST=... DB_PORT=3306 DB_DATABASE=... DB_USERNAME=... DB_PASSWORD=...

CACHE_STORE=redis                            # Phase B; "file" works until then
QUEUE_CONNECTION=sync                        # switch to "redis" in Phase B
REDIS_HOST=... REDIS_PASSWORD=... REDIS_PORT=6379

SANCTUM_STATEFUL_DOMAINS=codecv.ie
SESSION_DOMAIN=.codecv.ie

# Integrations (see .env.example for the full annotated list)
STRIPE_KEY= STRIPE_SECRET= STRIPE_WEBHOOK_SECRET=
GEMINI_API_KEY=
JUDGE0_URL= JUDGE0_TOKEN= JUDGE0_LANGUAGE_ID=
GOOGLE_CLIENT_ID= GOOGLE_CLIENT_SECRET= GOOGLE_REDIRECT_URI=
MAIL_MAILER=resend RESEND_KEY=

# Error monitoring (optional but recommended — see below)
SENTRY_LARAVEL_DSN=
SENTRY_TRACES_SAMPLE_RATE=0.2
SENTRY_RELEASE=                              # set to the git SHA per deploy
```

Frontend image env (public runtime config, injected at run time):

```
NUXT_PUBLIC_API_BASE=https://api.codecv.ie
NUXT_PUBLIC_SENTRY_DSN=
```

## Build

```bash
# API
docker build -t codecv-api:$(git rev-parse --short HEAD) .

# Frontend
docker build -t codecv-web:$(git rev-parse --short HEAD) ./frontend
```

Both images are immutable per commit — tag with the git SHA and promote the
same artifact through environments (config comes from env, not the image).

## Run

```bash
# API — the entrypoint links storage, caches config/routes/views, and runs
# migrations before starting php-fpm + nginx.
docker run -d --name codecv-api -p 8080:8080 --env-file .env.production codecv-api:<sha>

# Frontend
docker run -d --name codecv-web -p 3000:3000 \
  -e NUXT_PUBLIC_API_BASE=https://api.codecv.ie \
  codecv-web:<sha>
```

Put a TLS-terminating load balancer / reverse proxy in front of both.

## First deploy

1. Provision MySQL + Redis; create the database.
2. Set all required env (above); generate `APP_KEY` once and store it as a secret.
3. Deploy the API image — migrations run automatically via the entrypoint.
4. Seed the baseline roles (idempotent):
   ```bash
   docker exec codecv-api php artisan db:seed --class=RoleSeeder --force
   ```
   Seed demo/content data only in non-production, or via the content pipeline
   once it lands (`php artisan content:sync`).
5. Point `GOOGLE_REDIRECT_URI` and the Stripe webhook endpoint
   (`/api/webhooks/stripe`) at the production API URL.

## Ongoing deploys & rollback

- **Deploy:** build the new SHA image, roll it out. On a multi-instance rollout
  set `RUN_MIGRATIONS=false` on all but one instance so migrations run once.
- **Rollback:** re-deploy the previous SHA image. Because migrations run forward
  only, prefer expand/contract (backward-compatible) migrations so a rollback of
  the app doesn't require a DB rollback.
- **Zero-downtime:** health-check `/up` (already configured) before shifting
  traffic.

## Queue worker & scheduler (Phase B)

Today `QUEUE_CONNECTION=sync` — no worker needed. When Phase B lands:

- Set `QUEUE_CONNECTION=redis` and enable the `queue` program in
  `docker/supervisord.conf` (`autostart=true`), or run a dedicated worker
  container: `php artisan queue:work`.
- Run the scheduler once per minute (cron or a sidecar):
  `php artisan schedule:run`.

## Storage

`FileUploadService` writes to the `public` disk (`storage/app/public`). In a
containerised, multi-instance setup local disk is ephemeral and per-instance —
switch `FILESYSTEM_DISK=s3` and configure an S3-compatible bucket so uploads
survive restarts and are shared across instances.

## Error monitoring (Sentry)

Sentry is wired but dormant until a DSN is set (see `.env.example`).

1. Create backend + frontend projects at sentry.io.
2. Set `SENTRY_LARAVEL_DSN` (API) and `NUXT_PUBLIC_SENTRY_DSN` (frontend).
3. Set `SENTRY_RELEASE` to the git SHA on each deploy to tie errors to releases.

## Choosing a host (open decision)

The images run anywhere. Reasonable options, simplest → most control:

- **Fly.io / Render** — push a container, managed MySQL + Redis add-ons. Fastest
  to production; good default for this stage.
- **Laravel Cloud / Forge (+ a VPS)** — Laravel-native tooling, less container
  plumbing.
- **AWS ECS/Fargate or Kubernetes** — most control and scale, most ops overhead;
  only worth it once traffic justifies it.

Recommendation for now: **Fly.io or Render** — lowest operational burden while
the priority is customers, not infrastructure.
