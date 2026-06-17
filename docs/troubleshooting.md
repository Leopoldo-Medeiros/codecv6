# Troubleshooting (codecv6)

Common issues and fixes for the codecv6 (Laravel 12 + Nuxt 4 + DDEV) stack.

## Auth / API

### `Failed to fetch` on `/sanctum/csrf-cookie`
**Cause**: HTTPS dev cert is self-signed and not trusted by the browser.
**Fix**:
- Use HTTP for the API base: `NUXT_PUBLIC_API_BASE=http://codecv6.ddev.site`
- OR run `mkcert -install` once to trust DDEV's cert

### 401 Unauthorized after login
**Cause**: token missing or not being sent in the `Authorization: Bearer <token>` header.
**Fix**:
- Verify `localStorage.getItem('auth_token')` returns the token after login
- Check the failing request is using `useApi()` (not raw `$fetch`)
- Inspect the request in DevTools → Network → Headers

### 403 Forbidden on a route that should work
**Cause**: role mismatch — the user doesn't have the role required by the route's middleware.
**Fix**:
- Check the route's middleware: `Route::middleware('role:admin|consultant')`
- Check the user's roles in `/api/me` response
- If a Service threw `AuthorizationException`, read the message

### CORS error in browser console
```
Access to fetch at 'http://codecv6.ddev.site/api/...' from origin 'http://localhost:3000'
has been blocked by CORS policy
```
**Cause**: `FRONTEND_URL` env (comma-separated CORS allow-list) doesn't include the frontend origin.
**Fix**:
```env
FRONTEND_URL=http://localhost:3000,http://192.168.1.39:3000,http://codecv6.ddev.site
```
Then `ddev artisan config:clear`.

### OAuth redirect lands on the wrong page
**Cause**: `APP_FRONTEND_URL` (single canonical URL) vs `FRONTEND_URL` (multi-origin allow-list) confusion.
**Fix**: `APP_FRONTEND_URL` MUST be a single URL. Backend redirects use `config('app.frontend_url')`.

## Stripe

### Webhook returns 400 "Invalid signature"
**Cause**: `STRIPE_WEBHOOK_SECRET` doesn't match what `stripe listen` produced.
**Fix**:
```bash
stripe listen --forward-to codecv6.ddev.site/api/webhooks/stripe
# Copy the "whsec_..." it prints into .env as STRIPE_WEBHOOK_SECRET
ddev artisan config:clear
```

### Checkout amount is wrong (off by 100×)
**Cause**: amounts in `config/pricing.php` are **integer minor units** (cents/centavos), not float currency.
**Fix**: `19.90 EUR` → `1990`, not `19.90`.

### Webhook isn't received in DDEV
**Cause**: `stripe listen` not running, or DDEV not reachable on the public name.
**Fix**:
- Keep `stripe listen` running in another terminal
- Verify `codecv6.ddev.site` resolves locally (`ddev status`)

## Database / Migrations

### `Class "Spatie\\Permission\\Models\\Role" not found` in tests
**Cause**: `RoleSeeder` not seeded in test `setUp()`.
**Fix**:
```php
protected function setUp(): void
{
    parent::setUp();
    $this->seed(\Database\Seeders\RoleSeeder::class);
}
```

### `migrate:fresh --seed` fails with FK errors
**Cause**: tables being dropped while FKs still reference them, or seeder order wrong.
**Fix**:
- `ddev artisan migrate:fresh --seed` re-creates everything from scratch — usually fine
- If it still fails, drop the DB: `ddev exec mysql -uroot -proot -e "DROP DATABASE db; CREATE DATABASE db;"` then `migrate:fresh --seed` again

### "role does not exist" when calling `$user->syncRoles(['admin'])`
**Cause**: Spatie cache stale or role row missing.
**Fix**:
```bash
ddev artisan permission:cache-reset
ddev artisan db:seed --class=RoleSeeder
```

## DDEV

### `ddev start` fails to launch the web container
**Fix**:
```bash
ddev poweroff
ddev start
```
If that doesn't help: `ddev logs` to see the error, then check `.ddev/config.yaml`.

### `ddev nuxt` fails with `Cannot find module '@nuxt/...'`
**Cause**: container `node_modules` volume is empty or owned by root.
**Fix**:
```bash
ddev exec sudo chown -R $(id -u):$(id -g) /var/www/html/frontend/node_modules
ddev npm install
```

### Host `npm run dev` fails with native binary errors after using `ddev npm install`
**Cause**: the two `node_modules` (host arm64 vs container x64) should be isolated by the named volume but sometimes collide.
**Fix**:
```bash
rm -rf frontend/node_modules
cd frontend && npm install   # repopulate host
```

## Frontend / Nuxt

### Marketing page CSS tokens not applying
**Cause**: tokens defined as `:root { --token: ... }` inside a Vue `<style scoped>` block. Scoped CSS rewrites `:root` to `:root[data-v-xxx]` which doesn't match `<html>`.
**Fix**: move tokens to a section-level selector (e.g. `.mkt`, `.hero, .features, .testi`), or move them out of the scoped block.

### Navbar appears transparent / `CV` text appears purple on homepage
**Cause**: `isDarkHero = computed(() => route.path === '/')` activated the dark-hero variant (which uses purple).
**Fix**:
```ts
const isDarkHero = computed(() => false)
```
And make `.mkt-header` always have a white backdrop blur.

### Image at `/images/foo.png` returns 404
**Cause**: image placed in `/public/images/` (the Laravel backend) instead of `/frontend/public/images/` (Nuxt).
**Fix**: copy to the correct folder:
```bash
cp public/images/foo.png frontend/public/images/foo.png
```

### Tailwind classes don't apply on a marketing page
**Cause**: marketing pages use the `.mkt-*` design system, NOT `@nuxt/ui` / Tailwind utility classes.
**Fix**: use the `.mkt-*` classes defined in `app/layouts/marketing.vue`. If you really need Tailwind, scope it carefully — but the convention is custom CSS for marketing.

### Tailwind utilities work but components are broken
**Cause**: Tailwind v4 accidentally upgraded — incompatible with `@nuxt/ui` v2.
**Fix**: pin v3 in `frontend/package.json` `overrides`:
```json
"overrides": { "tailwindcss": "^3.4.0" }
```
Then `rm -rf node_modules package-lock.json && npm install`.

### `useSeoMeta` doesn't render meta tags
**Cause**: page is SPA (`ssr: false` in `routeRules`) — SEO meta only renders on prerendered/SSR pages.
**Fix**: marketing pages are `prerender: true`. Authenticated pages don't need SEO meta (they're behind auth).

### `@nuxtjs/sitemap` includes authenticated routes
**Cause**: `excludeAppSources: true` not set, or `sitemap.urls` allow-list not used.
**Fix**: in `nuxt.config.ts`:
```ts
sitemap: {
  urls: ['/', '/about', '/pricing', /* ... */],
  excludeAppSources: true,
}
```

## Tests

### Tests fail with "Database does not exist"
**Cause**: test DB connection not configured in `phpunit.xml`.
**Fix**: check `phpunit.xml` has `DB_CONNECTION=sqlite` + `DB_DATABASE=:memory:` (or the dedicated test MySQL DB).

### `assertJsonStructure` fails on pagination meta
**Cause**: Resource Collection wrapping not consistent.
**Fix**:
```php
return CourseResource::collection($query->paginate(20))->response();
// Returns: { data: [...], links: {...}, meta: {...} }
```

### Tests pass locally but fail in CI
**Cause**: env vars or seeded state differ.
**Fix**: ensure `phpunit.xml` overrides match local test env. `RoleSeeder` must run in `setUp()`.

## CV / LinkedIn AI

### `CvController` returns 500 from Gemini
**Cause**: invalid `GEMINI_API_KEY` or wrong model name.
**Fix**:
- Verify key in `.env`
- Try `GEMINI_MODEL=gemini-flash-latest` (default)
- Inspect `storage/logs/laravel.log`

### Jina AI page fetch times out
**Cause**: `https://r.jina.ai/{url}` rate-limited or the target page is slow.
**Fix**: `CvController` already caps content at 15 000 chars. Retry once; if persistent, log and surface error to user.

## Judge0 (Challenges)

### Submission stuck on `status.id = 1 (In Queue)` or `2 (Processing)`
**Cause**: Judge0 is processing — the controller uses `wait=true` so this shouldn't happen. If it does, network latency or Judge0 outage.
**Fix**: retry; if persistent, check `JUDGE0_TOKEN` quota.

### Submission returns `status.id = 6 (Compilation Error)`
**Cause**: bootstrap PHP syntax issue — usually means the test class structure changed and `ChallengeExecutionService` needs an update.
**Fix**: inspect the concatenated submission (log it temporarily); fix the bootstrap generation.

### TLE (`status.id = 5`)
**Cause**: user code exceeds Judge0's time limit.
**Fix**: handled explicitly by `ChallengeExecutionService` — should surface a clean error to the user. If raw 500 is returned, the handling regressed.

## General

### `ddev artisan config:clear` doesn't help
- Also try `ddev artisan cache:clear`, `ddev artisan route:clear`, `ddev artisan view:clear`
- Restart DDEV: `ddev restart`

### Mailpit not catching mail
- Check `MAIL_MAILER=smtp`, `MAIL_HOST=mailpit`, `MAIL_PORT=1025`
- Open Mailpit web UI: `ddev describe` shows the URL

### Frontend dev server uses the wrong port
- Default is `localhost:3000`, but Nuxt also listens on `0.0.0.0:3000` (LAN access)
- Check what port `npm run dev` prints

## Where to Look First

1. **`storage/logs/laravel.log`** — backend errors (most common source of truth)
2. **Browser DevTools → Network** — what status code? What payload?
3. **Browser DevTools → Console** — client-side exceptions
4. **`git log -10`** — recent changes in suspect files
5. **`.env`** — single most frequent cause of config-related bugs

## Helpful Commands

```bash
# Backend
ddev artisan pail                                    # live log tail
ddev artisan route:list --path=api                   # list API routes
ddev artisan tinker                                  # interactive shell
ddev artisan permission:cache-reset                  # reset Spatie cache
ddev artisan storage:link                            # link public storage

# DDEV
ddev status
ddev logs                                            # all containers
ddev logs -s db                                      # specific container
ddev restart
ddev poweroff && ddev start                          # nuclear option

# Frontend
cd frontend && npm run dev                           # host
ddev nuxt                                            # container
```
