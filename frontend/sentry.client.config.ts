import * as Sentry from '@sentry/nuxt'

// Browser-side error monitoring. Initialises only when a DSN is provided via
// NUXT_PUBLIC_SENTRY_DSN (runtimeConfig.public.sentry.dsn) — with no DSN this
// is a no-op, so it's safe to ship disabled in local/CI/preview.
const dsn = useRuntimeConfig().public.sentry?.dsn

if (dsn) {
  Sentry.init({
    dsn,
    // Sample 20% of transactions for performance tracing.
    tracesSampleRate: 0.2,
    // Keep noise low: don't send default PII.
    sendDefaultPii: false,
  })
}
