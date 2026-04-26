<template>
  <div class="auth-page">
    <div class="auth-card">

    <div class="brand-panel">
      <div class="brand-panel__inner">
        <NuxtLink to="/" class="brand-logo" aria-label="CODECV — back to home">
          <img src="/images/Logo/codecv.png" alt="CODECV" />
        </NuxtLink>
        <div class="brand-content">
          <h1>Reset your<br/>password</h1>
          <p>Enter your email and we'll send you a link to get back into your account.</p>
        </div>
        <p class="brand-copy">© {{ new Date().getFullYear() }} CODECV. All Rights Reserved</p>
      </div>
    </div>

    <div class="form-panel">
      <div class="form-panel__inner">

        <template v-if="!sent">
          <div class="form-header">
            <h2>Forgot password?</h2>
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
                <input id="email" v-model="email" type="email" placeholder="example@email.com"
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

            <p class="form-footer"><NuxtLink to="/login" class="link">← Back to sign in</NuxtLink></p>
        </template>

        <template v-else>
          <div class="success">
            <div class="success__icon">
              <svg viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="#10b981" stroke-width="1.5"/>
                <path d="M8 12l3 3 5-5" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <h2>Check your email</h2>
            <p>If an account exists for <strong>{{ email }}</strong>, a reset link has been sent. Check your inbox and spam folder.</p>
            <NuxtLink to="/login" class="btn-primary" style="display:block;text-align:center;text-decoration:none;margin-top:1.5rem">Back to Sign In</NuxtLink>
          </div>
        </template>

      </div>
    </div>

    </div><!-- /auth-card -->
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

.auth-page {
  --accent: #0284c7; --glow: rgba(2,132,199,0.2); --error: #ef4444;
  --font: 'Inter', system-ui, sans-serif; --ease: cubic-bezier(0.16,1,0.3,1);
  font-family: var(--font); min-height: 100dvh; display: flex;
  align-items: center; justify-content: center; padding: 2rem 1rem; background: #f1f5f9;
}
.auth-card {
  display: flex; width: 100%; max-width: 860px; min-height: 520px;
  border-radius: 20px; overflow: hidden;
  box-shadow: 0 20px 60px rgba(0,0,0,0.12), 0 4px 16px rgba(0,0,0,0.06);
}
.brand-panel {
  width: 45%;
  background-image:
    radial-gradient(ellipse at 20% 60%, rgba(6,182,212,0.25) 0%, transparent 55%),
    radial-gradient(ellipse at 85% 15%, rgba(14,165,233,0.2) 0%, transparent 50%),
    linear-gradient(145deg, #0a1628 0%, #0d2137 50%, #0a3d62 100%);
  display: flex; flex-direction: column; padding: 2.5rem; overflow: hidden; position: relative;
}
.brand-panel::before {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 105%;
  height: 55%;
  background: url('/images/hero-15-img.png') no-repeat bottom center / contain;
  opacity: 0.14;
  pointer-events: none;
  z-index: 0;
}
.brand-panel::after {
  content: ''; position: absolute; bottom: -80px; right: -80px;
  width: 320px; height: 320px;
  background: radial-gradient(circle, rgba(99,102,241,0.25) 0%, transparent 70%);
  pointer-events: none;
}
.brand-panel__inner { display: flex; flex-direction: column; height: 100%; position: relative; z-index: 1; }
.brand-logo { display: inline-block; margin-bottom: auto; }
.brand-logo img { height: 120px; width: auto; filter: brightness(0) invert(1); opacity: 0.95; }
.brand-content { flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 3rem 0; }
.brand-content h1 { font-size: clamp(1.8rem, 3vw, 2.6rem); font-weight: 800; color: #fff; line-height: 1.15; letter-spacing: -0.02em; margin-bottom: 1.25rem; }
.brand-content p { font-size: 0.9375rem; color: rgba(255,255,255,0.65); line-height: 1.7; max-width: 320px; }
.brand-copy { font-size: 0.75rem; color: rgba(255,255,255,0.35); }

.form-panel { flex: 1; background: #fff; display: flex; align-items: center; justify-content: center; padding: 2.5rem; }
.form-panel__inner { width: 100%; max-width: 400px; animation: rise .6s var(--ease) both; }
@keyframes rise { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }

.form-header { margin-bottom: 2rem; }
.form-header h2 { font-size: 1.6rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; margin-bottom: 0.35rem; }
.form-header p { font-size: 0.875rem; color: #64748b; }

.field { display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1.1rem; }
.field label { font-size: 0.8rem; font-weight: 600; color: #374151; }
.field__wrap { position: relative; display: flex; align-items: center; }
.field__ico { position: absolute; left: 0.9rem; width: 15px; height: 15px; color: #9ca3af; pointer-events: none; }
.field__wrap input { width: 100%; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 0.8rem 0.9rem 0.8rem 2.6rem; font-family: var(--font); font-size: 0.9375rem; color: #0f172a; outline: none; transition: border-color .15s, box-shadow .15s; }
.field__wrap input::placeholder { color: #c0c9d4; }
.field__wrap input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--glow); background: #fff; }
.field__wrap input:disabled { opacity: 0.5; cursor: not-allowed; }

.alert { display: flex; align-items: center; gap: 0.6rem; padding: 0.75rem 1rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; font-size: 0.875rem; color: var(--error); margin-bottom: 1rem; }
.alert svg { width: 15px; height: 15px; flex-shrink: 0; }
.err-enter-active { animation: errIn .3s var(--ease); }
@keyframes errIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }

.btn-primary { width: 100%; padding: 0.9rem; background: var(--accent); color: #fff; font-family: var(--font); font-size: 0.9375rem; font-weight: 700; border: none; border-radius: 10px; cursor: pointer; transition: background .2s, transform .15s, box-shadow .2s; }
.btn-primary:hover:not(:disabled) { background: #047857; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,0.35); }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
.spinner-row { display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
.spinner-row svg { width: 16px; height: 16px; }
@keyframes spin { to { transform: rotate(360deg); } }

.form-footer { margin-top: 1.5rem; font-size: 0.875rem; color: #64748b; }
.link { color: var(--accent); font-weight: 600; text-decoration: none; transition: opacity .15s; }
.link:hover { opacity: 0.75; }

.success { text-align: center; }
.success__icon { width: 64px; height: 64px; margin: 0 auto 1.25rem; }
.success__icon svg { width: 100%; height: 100%; }
.success h2 { font-size: 1.4rem; font-weight: 800; color: #0f172a; margin-bottom: 0.6rem; }
.success p { font-size: 0.875rem; color: #64748b; line-height: 1.6; }
.success strong { color: #0f172a; }

@media (max-width: 768px) {
  .auth-card { flex-direction: column; }
  .brand-panel { width: 100%; min-height: 200px; padding: 2rem; }
  .brand-copy { display: none; }
  .form-panel { padding: 2rem 1.25rem; }
}
</style>
