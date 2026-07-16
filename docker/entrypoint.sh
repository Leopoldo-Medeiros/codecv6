#!/usr/bin/env bash
# Container entrypoint: prepare the app, then hand off to the CMD (supervisor).
# Runs on every boot; all steps are safe to repeat.
set -euo pipefail

cd /var/www/html

# Fail fast if the image was shipped without an app key.
if [ -z "${APP_KEY:-}" ]; then
  echo "FATAL: APP_KEY is not set. Generate one with 'php artisan key:generate --show'." >&2
  exit 1
fi

# Link public storage (idempotent).
php artisan storage:link || true

# Cache config/routes/views for production performance. Rebuilt each boot so a
# new release always reflects the current env.
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations. Set RUN_MIGRATIONS=false on replicas so only one instance
# migrates during a multi-instance rollout.
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  php artisan migrate --force
fi

exec "$@"
