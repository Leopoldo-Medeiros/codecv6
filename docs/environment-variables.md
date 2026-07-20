# Environment Variables (codecv6)

Reference for all environment variables used by the codecv6 backend (`/.env`) and frontend (`/frontend/.env`).

## Backend (`/.env`)

### App

| Variable | Default | Purpose |
|---|---|---|
| `APP_NAME` | `Laravel` | App display name |
| `APP_ENV` | `local` | Environment: `local`, `testing`, `production` |
| `APP_KEY` | — | Set via `ddev artisan key:generate` |
| `APP_DEBUG` | `true` | Set to `false` in production |
| `APP_URL` | `http://codecv6.ddev.site` | Backend URL (used by route helpers, queue jobs) |
| `APP_FRONTEND_URL` | `http://localhost:3001` | **Single canonical URL** used by backend redirects (OAuth callback, password reset emails). Must NOT be confused with `FRONTEND_URL` |

### Localisation

| Variable | Default | Purpose |
|---|---|---|
| `APP_LOCALE` | `en` | Default locale |
| `APP_TIMEZONE` | `UTC` | App timezone |

### Database (MySQL via DDEV)

| Variable | Default | Purpose |
|---|---|---|
| `DB_CONNECTION` | `mysql` | Driver |
| `DB_HOST` | `db` | DDEV-injected hostname |
| `DB_PORT` | `3306` | |
| `DB_DATABASE` | `db` | DDEV default |
| `DB_USERNAME` | `db` | DDEV default |
| `DB_PASSWORD` | `db` | DDEV default |

### Cache / Sessions / Queue (Redis via DDEV)

| Variable | Default | Purpose |
|---|---|---|
| `CACHE_STORE` | `redis` | |
| `SESSION_DRIVER` | `redis` | |
| `SESSION_LIFETIME` | `120` | Minutes |
| `SESSION_DOMAIN` | (unset) | Leave unset in dev across hosts |
| `SESSION_SECURE_COOKIE` | `false` in dev / `true` in prod | Required for HTTPS in prod |
| `QUEUE_CONNECTION` | `redis` | |
| `REDIS_HOST` | `redis` | DDEV-injected |
| `REDIS_PORT` | `6379` | |

### Sanctum / CORS

| Variable | Example | Purpose |
|---|---|---|
| `SANCTUM_STATEFUL_DOMAINS` | `localhost:3001,codecv6.ddev.site,192.168.1.39:3001` | Comma-separated stateful origins for the CSRF cookie flow |
| `FRONTEND_URL` | `http://localhost:3001,http://192.168.1.39:3001,http://codecv6.ddev.site` | **Comma-separated CORS allow-list** read by `config/cors.php` |

> WARNING: `FRONTEND_URL` is plural (multi-origin allow-list). `APP_FRONTEND_URL` is singular (one canonical URL for redirects). Don't confuse them.

### Mail

| Variable | Default | Purpose |
|---|---|---|
| `MAIL_MAILER` | `smtp` | `smtp`, `log`, `array` |
| `MAIL_HOST` | `mailpit` | DDEV's mailpit by default |
| `MAIL_PORT` | `1025` | |
| `MAIL_FROM_ADDRESS` | `hello@codecv.ie` | |
| `MAIL_FROM_NAME` | `CODECV` | |

DDEV runs Mailpit on `http://codecv6.ddev.site:8025` (port may vary) for catching dev mail.

### Stripe

| Variable | Example | Purpose |
|---|---|---|
| `STRIPE_KEY` | `pk_test_...` | Publishable key |
| `STRIPE_SECRET` | `sk_test_...` | Secret key |
| `STRIPE_WEBHOOK_SECRET` | `whsec_...` | Set from `stripe listen` output |

Local webhook forwarding:
```bash
stripe listen --forward-to codecv6.ddev.site/api/webhooks/stripe
```

Tier amounts (and currencies) are defined in `config/pricing.php` as **integer minor units** (cents for EUR, centavos for BRL). Don't store float currency amounts.

### Google OAuth

| Variable | Example | Purpose |
|---|---|---|
| `GOOGLE_CLIENT_ID` | `....apps.googleusercontent.com` | |
| `GOOGLE_CLIENT_SECRET` | `GOCSPX-...` | |
| `GOOGLE_REDIRECT_URI` | `http://codecv6.ddev.site/api/auth/google/callback` | Must match the URI registered in Google Cloud Console |

### AI Integrations

| Variable | Example | Purpose |
|---|---|---|
| `GEMINI_API_KEY` | — | CV analysis (via `CvController`) |
| `GEMINI_MODEL` | `gemini-flash-latest` | Optional override; see `config/services.php` |
| `ANTHROPIC_API_KEY` | — | LinkedIn analyser (via `LinkedInController`) |

### Judge0 (coding challenges)

| Variable | Default | Purpose |
|---|---|---|
| `JUDGE0_URL` | `https://judge0-ce.p.rapidapi.com` | API base |
| `JUDGE0_TOKEN` | — | RapidAPI key |
| `JUDGE0_LANGUAGE_ID` | `68` | 68 = PHP 7.4; set higher for PHP 8.x on self-hosted |

### Algolia (Scout)

| Variable | Example | Purpose |
|---|---|---|
| `SCOUT_DRIVER` | `algolia` (or `null` to disable) | |
| `SCOUT_QUEUE` | `true` | Recommended — indexes via queue |
| `ALGOLIA_APP_ID` | — | |
| `ALGOLIA_SECRET` | — | Admin API key |

### Logging

| Variable | Default | Purpose |
|---|---|---|
| `LOG_CHANNEL` | `stack` | |
| `LOG_LEVEL` | `debug` in local / `error` in prod | |

## Frontend (`/frontend/.env`)

| Variable | Example | Purpose |
|---|---|---|
| `NUXT_PUBLIC_API_BASE` | `http://codecv6.ddev.site` | API base URL. **Use HTTP in dev** — the DDEV TLS cert is self-signed; HTTPS will fail `Failed to fetch` on `/sanctum/csrf-cookie` unless you run `mkcert -install` |

The frontend `.env` is loaded automatically by `npm run dev` (host) or via `--dotenv ../.env` when running `ddev nuxt` (container).

## Tips

- **After editing `.env`** always run `ddev artisan config:clear`
- **Don't commit `.env`** — already gitignored
- **`*.local.*` is also gitignored** — safe place for personal overrides (e.g. `.env.local.example`)
- **HTTPS in dev**: only worth setting up if you really need it. Run `mkcert -install` once to trust the DDEV cert; otherwise stick with HTTP for the API base
