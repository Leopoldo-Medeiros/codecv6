<template>
  <div class="page">

    <NuxtLink to="/login" class="back-link">
      <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
      Back to sign in
    </NuxtLink>

    <div class="card">

      <div class="card__logo">
        <div class="logo-mark">
          <svg viewBox="0 0 32 32" fill="none">
            <rect width="32" height="32" rx="9" fill="url(#g1)"/>
            <path d="M9 16l4 4 10-10" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            <defs>
              <linearGradient id="g1" x1="0" y1="0" x2="32" y2="32" gradientUnits="userSpaceOnUse">
                <stop stop-color="#6366f1"/><stop offset="1" stop-color="#06b6d4"/>
              </linearGradient>
            </defs>
          </svg>
        </div>
        <span class="logo-name">CODECV</span>
      </div>

      <template v-if="!sent">
        <div class="card__header">
          <h1>Forgot password?</h1>
          <p>Enter your email and we'll send a reset link.</p>
        </div>

        <form @submit.prevent="handleSubmit" novalidate>
          <div class="field">
            <label for="email">Email address</label>
            <div class="field__wrap">
              <svg class="field__ico" viewBox="0 0 20 20" fill="currentColor">
                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
              </svg>
              <input id="email" v-model="email" type="email" placeholder="you@example.com"
                autocomplete="email" :disabled="loading" required />
            </div>
          </div>

          <Transition name="err">
            <div v-if="error" class="alert" role="alert">
              <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
              {{ error }}
            </div>
          </Transition>

          <button type="submit" class="btn-primary" :disabled="loading || !email">
            <span v-if="!loading">Send Reset Link</span>
            <span v-else class="spinner-row">
              <svg viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"
                  stroke-dasharray="40 20" stroke-linecap="round"
                  style="transform-origin:center;animation:spin .8s linear infinite"/>
              </svg>
              Sending…
            </span>
          </button>
        </form>
      </template>

      <template v-else>
        <div class="success">
          <div class="success__icon">
            <svg viewBox="0 0 24 24" fill="none">
              <circle cx="12" cy="12" r="10" stroke="#34d399" stroke-width="1.5"/>
              <path d="M8 12l3 3 5-5" stroke="#34d399" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <h2>Check your email</h2>
          <p>If an account exists for <strong>{{ email }}</strong>, a reset link has been sent. Check your inbox and spam folder.</p>
          <NuxtLink to="/login" class="btn-primary" style="display:block;text-align:center;text-decoration:none;margin-top:1.5rem">
            Back to Sign In
          </NuxtLink>
        </div>
      </template>

    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: false, middleware: 'guest' })
useHead({ title: 'Forgot Password — CODECV' })

const config  = useRuntimeConfig()
const email   = ref('')
const loading = ref(false)
const error   = ref('')
const sent    = ref(false)

async function handleSubmit() {
  if (!email.value) return
  loading.value = true
  error.value   = ''
  try {
    await $fetch('/api/forgot-password', {
      baseURL: config.public.apiBase as string,
      method: 'POST',
      body: { email: email.value },
      headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
    })
    sent.value = true
  } catch (err: any) {
    error.value = err?.data?.message || 'Something went wrong. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
.page {
  --accent: #6366f1; --accent2: #06b6d4; --glow: rgba(99,102,241,0.28);
  --text: #f0f4ff; --muted: #8ba4c8; --dim: #4a6080;
  --surface: rgba(255,255,255,0.05); --surface2: rgba(255,255,255,0.08);
  --border: rgba(255,255,255,0.1); --error: #f87171; --font: 'Outfit', system-ui, sans-serif;
  --ease: cubic-bezier(0.16,1,0.3,1);
  font-family: var(--font);
  min-height: 100dvh; display: flex; align-items: center; justify-content: center;
  padding: 2rem 1rem;
  background: linear-gradient(rgba(6,10,22,0.72), rgba(6,10,22,0.72)), url('/images/login_dark.jpg') center/cover no-repeat fixed;
  color: var(--text);
}
.back-link { position: fixed; top: 1.25rem; left: 1.5rem; display: flex; align-items: center; gap: 0.4rem; font-size: 0.8rem; font-weight: 500; color: var(--muted); text-decoration: none; transition: color .15s; z-index: 10; }
.back-link:hover { color: var(--text); }
.back-link svg { width: 14px; height: 14px; }
.card { width: 100%; max-width: 460px; background: rgba(10,15,30,0.75); backdrop-filter: blur(20px) saturate(1.4); border: 1px solid var(--border); border-radius: 20px; padding: 2.5rem; box-shadow: 0 0 0 1px rgba(99,102,241,0.08), 0 24px 64px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.05); animation: rise .7s var(--ease) both; }
@keyframes rise { from { opacity:0; transform:translateY(20px) scale(0.98); } to { opacity:1; transform:translateY(0) scale(1); } }
.card__logo { display: flex; align-items: center; gap: 0.7rem; margin-bottom: 1.75rem; }
.logo-mark { width: 38px; height: 38px; border-radius: 10px; overflow: hidden; flex-shrink: 0; box-shadow: 0 4px 16px rgba(99,102,241,0.35); }
.logo-mark svg { display: block; width: 100%; height: 100%; }
.logo-name { font-size: 1.15rem; font-weight: 800; letter-spacing: 0.1em; color: var(--text); }
.card__header { margin-bottom: 1.75rem; }
.card__header h1 { font-size: 1.6rem; font-weight: 800; letter-spacing: -0.015em; color: var(--text); margin-bottom: 0.3rem; }
.card__header p { font-size: 0.875rem; color: var(--muted); }
.field { display: flex; flex-direction: column; gap: 0.45rem; margin-bottom: 1rem; }
.field label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.09em; color: var(--muted); }
.field__wrap { position: relative; display: flex; align-items: center; }
.field__ico { position: absolute; left: 0.9rem; width: 15px; height: 15px; color: var(--dim); pointer-events: none; }
.field__wrap input { width: 100%; background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 0.85rem 0.9rem 0.85rem 2.65rem; font-family: var(--font); font-size: 0.9375rem; color: var(--text); outline: none; transition: border-color .18s, box-shadow .18s, background .18s; }
.field__wrap input::placeholder { color: var(--dim); }
.field__wrap input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--glow); background: var(--surface2); }
.field__wrap input:disabled { opacity: 0.45; cursor: not-allowed; }
.alert { display: flex; align-items: center; gap: 0.6rem; padding: 0.75rem 1rem; background: rgba(248,113,113,0.08); border: 1px solid rgba(248,113,113,0.2); border-radius: 10px; font-size: 0.875rem; color: var(--error); margin-bottom: 1rem; }
.alert svg { width: 15px; height: 15px; flex-shrink: 0; }
.err-enter-active { animation: errIn .3s var(--ease); }
@keyframes errIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }
.btn-primary { width: 100%; padding: 0.9rem; background: linear-gradient(135deg, var(--accent) 0%, #4f46e5 100%); color: #fff; font-family: var(--font); font-size: 0.9375rem; font-weight: 700; border: none; border-radius: 12px; cursor: pointer; margin-top: 0.25rem; transition: opacity .2s, transform .15s, box-shadow .2s; }
.btn-primary:hover:not(:disabled) { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 8px 32px rgba(99,102,241,0.45); }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
.spinner-row { display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
.spinner-row svg { width: 16px; height: 16px; }
@keyframes spin { to { transform: rotate(360deg); } }
.success { text-align: center; }
.success__icon { width: 64px; height: 64px; margin: 0 auto 1.25rem; }
.success__icon svg { width: 100%; height: 100%; }
.success h2 { font-size: 1.4rem; font-weight: 800; color: var(--text); margin-bottom: 0.6rem; }
.success p { font-size: 0.875rem; color: var(--muted); line-height: 1.6; }
.success strong { color: var(--text); }
</style>
