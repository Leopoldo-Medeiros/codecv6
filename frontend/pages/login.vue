<template>
  <div class="page">

    <!-- Back to site -->
    <NuxtLink to="/" class="back-link">
      <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
      Back to site
    </NuxtLink>

    <!-- Card -->
    <div class="card">

      <!-- Logo -->
      <div class="card__logo">
        <div class="logo-mark">
          <svg viewBox="0 0 32 32" fill="none">
            <rect width="32" height="32" rx="9" fill="url(#g1)"/>
            <path d="M9 16l4 4 10-10" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            <defs>
              <linearGradient id="g1" x1="0" y1="0" x2="32" y2="32" gradientUnits="userSpaceOnUse">
                <stop stop-color="#6366f1"/>
                <stop offset="1" stop-color="#06b6d4"/>
              </linearGradient>
            </defs>
          </svg>
        </div>
        <span class="logo-name">CODECV</span>
      </div>

      <!-- Header -->
      <div class="card__header">
        <h1>Welcome back</h1>
        <p>Sign in to your account to continue</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleLogin" novalidate>

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

        <div class="field">
          <label for="password">Password</label>
          <div class="field__wrap">
            <svg class="field__ico" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
            </svg>
            <input id="password" v-model="password" :type="showPwd ? 'text' : 'password'"
              placeholder="••••••••" autocomplete="current-password"
              class="has-eye" :disabled="loading" required />
            <button type="button" class="eye-btn" @click="showPwd = !showPwd" tabindex="-1">
              <svg v-if="!showPwd" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
              </svg>
              <svg v-else viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/>
                <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.064 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/>
              </svg>
            </button>
          </div>
        </div>

        <Transition name="err">
          <div v-if="error" class="alert" role="alert">
            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            {{ error }}
          </div>
        </Transition>

        <button type="submit" class="btn-primary" :disabled="loading">
          <span v-if="!loading">Sign In</span>
          <span v-else class="spinner-row">
            <svg viewBox="0 0 24 24" fill="none">
              <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"
                stroke-dasharray="40 20" stroke-linecap="round"
                style="transform-origin:center;animation:spin .8s linear infinite"/>
            </svg>
            Signing in…
          </span>
        </button>

      </form>

      <div class="card__footer">
        <p><NuxtLink to="/forgot-password" class="link">Forgot your password?</NuxtLink></p>
        <p>Don't have an account? <NuxtLink to="/register" class="link">Create one</NuxtLink></p>
      </div>

    </div>

  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: false, middleware: 'guest' })
useHead({
  title: 'Sign In — CODECV',
  link: [
    { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
    { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: '' },
    { rel: 'stylesheet', href: 'https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap' },
  ],
})

const { login } = useAuth()
const email    = ref('')
const password = ref('')
const showPwd  = ref(false)
const error    = ref('')
const loading  = ref(false)

async function handleLogin() {
  if (!email.value || !password.value) { error.value = 'Please enter your email and password.'; return }
  loading.value = true
  error.value   = ''
  const result = await login(email.value, password.value)
  loading.value = false
  if (result.success) navigateTo('/dashboard')
  else { error.value = result.error || 'Invalid credentials.'; password.value = '' }
}
</script>

<style scoped>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.page {
  --accent:   #6366f1;
  --accent2:  #06b6d4;
  --glow:     rgba(99,102,241,0.28);
  --text:     #f0f4ff;
  --muted:    #8ba4c8;
  --dim:      #4a6080;
  --surface:  rgba(255,255,255,0.05);
  --surface2: rgba(255,255,255,0.08);
  --border:   rgba(255,255,255,0.1);
  --error:    #f87171;
  --font:     'Outfit', system-ui, sans-serif;
  --ease:     cubic-bezier(0.16,1,0.3,1);

  font-family: var(--font);
  min-height: 100dvh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
  background:
    linear-gradient(rgba(6,10,22,0.72), rgba(6,10,22,0.72)),
    url('/images/login_dark.jpg') center/cover no-repeat fixed;
  color: var(--text);
}

/* Back link */
.back-link {
  position: fixed;
  top: 1.25rem;
  left: 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.8rem;
  font-weight: 500;
  color: var(--muted);
  text-decoration: none;
  transition: color .15s;
  z-index: 10;
}
.back-link:hover { color: var(--text); }
.back-link svg { width: 14px; height: 14px; }

/* Card */
.card {
  width: 100%;
  max-width: 460px;
  background: rgba(10,15,30,0.75);
  backdrop-filter: blur(20px) saturate(1.4);
  -webkit-backdrop-filter: blur(20px) saturate(1.4);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 2.5rem;
  box-shadow:
    0 0 0 1px rgba(99,102,241,0.08),
    0 24px 64px rgba(0,0,0,0.5),
    inset 0 1px 0 rgba(255,255,255,0.05);
  animation: rise .7s var(--ease) both;
}
@keyframes rise {
  from { opacity: 0; transform: translateY(20px) scale(0.98); }
  to   { opacity: 1; transform: translateY(0) scale(1); }
}

/* Logo */
.card__logo {
  display: flex;
  align-items: center;
  gap: 0.7rem;
  margin-bottom: 1.75rem;
}
.logo-mark {
  width: 38px; height: 38px;
  border-radius: 10px;
  overflow: hidden;
  flex-shrink: 0;
  box-shadow: 0 4px 16px rgba(99,102,241,0.35);
}
.logo-mark svg { display: block; width: 100%; height: 100%; }
.logo-name {
  font-size: 1.15rem;
  font-weight: 800;
  letter-spacing: 0.1em;
  color: var(--text);
}

/* Header */
.card__header { margin-bottom: 1.75rem; }
.card__header h1 {
  font-size: 1.6rem;
  font-weight: 800;
  letter-spacing: -0.015em;
  color: var(--text);
  margin-bottom: 0.3rem;
}
.card__header p {
  font-size: 0.875rem;
  color: var(--muted);
}

/* Fields */
.field { display: flex; flex-direction: column; gap: 0.45rem; margin-bottom: 1rem; }
.field label {
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.09em;
  color: var(--muted);
}
.field__wrap { position: relative; display: flex; align-items: center; }
.field__ico {
  position: absolute;
  left: 0.9rem;
  width: 15px; height: 15px;
  color: var(--dim);
  pointer-events: none;
}
.field__wrap input {
  width: 100%;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 0.85rem 0.9rem 0.85rem 2.65rem;
  font-family: var(--font);
  font-size: 0.9375rem;
  color: var(--text);
  outline: none;
  transition: border-color .18s, box-shadow .18s, background .18s;
  -webkit-appearance: none;
}
.field__wrap input::placeholder { color: var(--dim); }
.field__wrap input:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px var(--glow);
  background: var(--surface2);
}
.field__wrap input:disabled { opacity: 0.45; cursor: not-allowed; }
.field__wrap input.has-eye { padding-right: 2.75rem; }

.eye-btn {
  position: absolute;
  right: 0.85rem;
  background: none;
  border: none;
  cursor: pointer;
  color: var(--dim);
  padding: 0;
  display: flex;
  align-items: center;
  transition: color .15s;
}
.eye-btn:hover { color: var(--muted); }
.eye-btn svg { width: 15px; height: 15px; }

/* Alert */
.alert {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.75rem 1rem;
  background: rgba(248,113,113,0.08);
  border: 1px solid rgba(248,113,113,0.2);
  border-radius: 10px;
  font-size: 0.875rem;
  color: var(--error);
  margin-bottom: 1rem;
}
.alert svg { width: 15px; height: 15px; flex-shrink: 0; }

.err-enter-active { animation: errIn .3s var(--ease); }
@keyframes errIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }

/* Button */
.btn-primary {
  width: 100%;
  padding: 0.9rem;
  background: linear-gradient(135deg, var(--accent) 0%, #4f46e5 100%);
  color: #fff;
  font-family: var(--font);
  font-size: 0.9375rem;
  font-weight: 700;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  margin-top: 0.25rem;
  position: relative;
  overflow: hidden;
  transition: opacity .2s, transform .15s, box-shadow .2s;
  letter-spacing: 0.01em;
}
.btn-primary::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, transparent 40%, rgba(255,255,255,0.1));
  pointer-events: none;
}
.btn-primary:hover:not(:disabled) {
  opacity: 0.9;
  transform: translateY(-1px);
  box-shadow: 0 8px 32px rgba(99,102,241,0.45);
}
.btn-primary:active:not(:disabled) { transform: translateY(0); }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
.spinner-row {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}
.spinner-row svg { width: 16px; height: 16px; }

@keyframes spin { to { transform: rotate(360deg); } }

/* Footer */
.card__footer {
  margin-top: 1.5rem;
  text-align: center;
  font-size: 0.875rem;
  color: var(--muted);
}
.link {
  color: var(--accent2);
  font-weight: 600;
  text-decoration: none;
  margin-left: 0.2rem;
  transition: opacity .15s;
}
.link:hover { opacity: 0.8; }
</style>
