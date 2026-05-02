<template>
  <NuxtLayout name="marketing">
    <section class="psucc">
      <div class="mkt-container psucc__inner">
        <!-- LOADING / PENDING -->
        <div v-if="state === 'loading' || state === 'pending'" class="psucc__card">
          <div class="psucc__spinner" />
          <h1 class="psucc__title">Confirming your payment…</h1>
          <p class="psucc__sub">This usually takes a few seconds. Please don't close this page.</p>
        </div>

        <!-- PAID -->
        <div v-else-if="state === 'paid'" class="psucc__card">
          <div class="psucc__icon psucc__icon--ok">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12" />
            </svg>
          </div>
          <span class="section-badge">Payment confirmed</span>
          <h1 class="psucc__title">Thank you, {{ user?.fullname?.split(' ')[0] || 'there' }}!</h1>
          <p class="psucc__sub">
            Your <strong>{{ tierLabel }}</strong> purchase is confirmed. We'll be in touch on WhatsApp within one business day to start your onboarding.
          </p>
          <div v-if="payment" class="psucc__receipt">
            <div class="psucc__receipt-row"><span>Plan</span><span>{{ tierLabel }}</span></div>
            <div class="psucc__receipt-row"><span>Amount</span><span>{{ formattedAmount }}</span></div>
            <div class="psucc__receipt-row"><span>Reference</span><span class="psucc__ref">{{ shortSessionId }}</span></div>
          </div>
          <div class="psucc__cta-row">
            <NuxtLink to="/dashboard" class="mkt-btn mkt-btn--primary mkt-btn--lg">Go to dashboard</NuxtLink>
            <NuxtLink to="/" class="mkt-btn mkt-btn--outline mkt-btn--lg">Back to home</NuxtLink>
          </div>
        </div>

        <!-- FAILED -->
        <div v-else-if="state === 'failed'" class="psucc__card">
          <div class="psucc__icon psucc__icon--err">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </div>
          <h1 class="psucc__title">Payment didn't go through</h1>
          <p class="psucc__sub">No charge was made to your card. You can try again or contact us if the problem persists.</p>
          <div class="psucc__cta-row">
            <NuxtLink to="/pricing" class="mkt-btn mkt-btn--primary mkt-btn--lg">Choose a plan</NuxtLink>
          </div>
        </div>

        <!-- ERROR (network / not found) -->
        <div v-else class="psucc__card">
          <div class="psucc__icon psucc__icon--err">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10" />
              <line x1="12" y1="8" x2="12" y2="12" />
              <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
          </div>
          <h1 class="psucc__title">We couldn't find this payment</h1>
          <p class="psucc__sub">{{ errorMessage || 'The reference is invalid or the session expired.' }}</p>
          <div class="psucc__cta-row">
            <NuxtLink to="/pricing" class="mkt-btn mkt-btn--primary mkt-btn--lg">Back to pricing</NuxtLink>
          </div>
        </div>
      </div>
    </section>
  </NuxtLayout>
</template>

<script setup lang="ts">
import type { CheckoutStatusResponse } from '~/composables/useCheckout'

definePageMeta({ layout: false })

useSeoMeta({
  title: 'Payment',
  robots: 'noindex, nofollow',
})

const route = useRoute()
const router = useRouter()
const { user, isAuthenticated } = useAuth()
const { getStatus } = useCheckout()

type State = 'loading' | 'pending' | 'paid' | 'failed' | 'error'
const state = ref<State>('loading')
const payment = ref<CheckoutStatusResponse | null>(null)
const errorMessage = ref<string | null>(null)

const sessionId = computed(() => (route.query.session_id as string) || '')

const tierLabel = computed(() => {
  if (!payment.value) return ''
  return {
    accelerator: 'Career Accelerator',
    bootcamp: 'Laravel + NR Bootcamp',
    mentorship: '1-on-1 Mentorship',
  }[payment.value.tier]
})

const formattedAmount = computed(() => {
  if (!payment.value) return ''
  const major = payment.value.amount / 100
  const symbol = payment.value.currency === 'eur' ? '€' : 'R$'
  return `${symbol}${major.toLocaleString(payment.value.currency === 'brl' ? 'pt-BR' : 'en-IE', { minimumFractionDigits: 2 })}`
})

const shortSessionId = computed(() => {
  if (!payment.value) return ''
  const id = sessionId.value
  return id.length > 14 ? `${id.slice(0, 8)}…${id.slice(-4)}` : id
})

const POLL_DELAYS = [0, 1500, 3000, 5000, 8000]

const fetchStatus = async (): Promise<void> => {
  if (!sessionId.value) {
    state.value = 'error'
    errorMessage.value = 'Missing session reference.'
    return
  }

  if (!isAuthenticated.value) {
    await router.push({ path: '/login', query: { next: route.fullPath } })
    return
  }

  for (const delay of POLL_DELAYS) {
    if (delay > 0) await new Promise((r) => setTimeout(r, delay))

    try {
      const data = await getStatus(sessionId.value)
      payment.value = data
      if (data.status === 'paid') {
        state.value = 'paid'
        return
      }
      if (data.status === 'failed') {
        state.value = 'failed'
        return
      }
      state.value = 'pending'
    } catch (err) {
      const status = (err as { status?: number; statusCode?: number })?.status
        ?? (err as { statusCode?: number })?.statusCode
      if (status === 404) {
        state.value = 'error'
        errorMessage.value = 'Payment reference not found.'
        return
      }
      errorMessage.value = (err as { message?: string })?.message || 'Network error.'
      state.value = 'error'
      return
    }
  }
}

onMounted(() => { fetchStatus() })
</script>

<style scoped>
.psucc {
  min-height: calc(100vh - 200px);
  padding: 120px 0 80px;
  display: flex;
  align-items: center;
}
.psucc__inner { display: flex; justify-content: center; }
.psucc__card {
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 22px;
  box-shadow: var(--shadow-md);
  padding: 48px 40px;
  max-width: 520px;
  width: 100%;
  text-align: center;
}
.psucc__spinner {
  width: 44px; height: 44px;
  margin: 0 auto 24px;
  border: 3px solid var(--bg-soft);
  border-top-color: var(--accent);
  border-radius: 50%;
  animation: spin 0.9s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.psucc__icon {
  width: 64px; height: 64px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center; justify-content: center;
  margin-bottom: 20px;
}
.psucc__icon--ok { background: #ecfdf5; color: var(--accent); }
.psucc__icon--err { background: #fef2f2; color: #dc2626; }
.psucc__title {
  font-size: clamp(24px, 3.5vw, 32px);
  font-weight: 800;
  color: var(--text);
  letter-spacing: -0.03em;
  margin: 8px 0 12px;
}
.psucc__sub { font-size: 15px; color: var(--muted); line-height: 1.7; margin-bottom: 24px; }
.psucc__receipt {
  background: var(--bg-soft);
  border-radius: 12px;
  padding: 18px 20px;
  margin-bottom: 28px;
  text-align: left;
}
.psucc__receipt-row {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
  padding: 6px 0;
  color: var(--text-body);
}
.psucc__receipt-row:not(:last-child) { border-bottom: 1px solid var(--border); }
.psucc__receipt-row > span:first-child { color: var(--muted); }
.psucc__receipt-row > span:last-child { font-weight: 600; color: var(--text); }
.psucc__ref { font-family: ui-monospace, "SF Mono", Menlo, monospace; font-size: 12px; }
.psucc__cta-row {
  display: flex;
  gap: 12px;
  justify-content: center;
  flex-wrap: wrap;
}
@media (max-width: 600px) {
  .psucc__card { padding: 36px 24px; }
  .psucc__cta-row .mkt-btn { width: 100%; }
}
</style>
