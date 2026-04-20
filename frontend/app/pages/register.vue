<template>
  <div class="auth-page">
    <div class="auth-card">

    <!-- Left: form panel -->
    <div class="form-panel">
      <div class="form-panel__inner">

        <div class="form-header">
          <h2>Create an account</h2>
          <p>Join CODECV and accelerate your IT career</p>
        </div>

        <form @submit.prevent="handleRegister" novalidate>

          <div class="field">
            <label for="fullname">Full name</label>
            <div class="field__wrap">
              <svg class="field__ico" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
              </svg>
              <input id="fullname" v-model="form.fullname" type="text" placeholder="John Doe"
                autocomplete="name" :disabled="loading" required />
            </div>
          </div>

          <div class="field">
            <label for="email">Email address</label>
            <div class="field__wrap">
              <svg class="field__ico" viewBox="0 0 20 20" fill="currentColor">
                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
              </svg>
              <input id="email" v-model="form.email" type="email" placeholder="example@email.com"
                autocomplete="email" :disabled="loading" required />
            </div>
          </div>

          <div class="field">
            <label for="password">Password</label>
            <div class="field__wrap">
              <svg class="field__ico" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
              </svg>
              <input id="password" v-model="form.password" :type="showPwd ? 'text' : 'password'"
                placeholder="min 8 characters" autocomplete="new-password"
                class="has-eye" :disabled="loading" required />
              <button type="button" class="eye-btn" @click="showPwd = !showPwd" tabindex="-1">
                <svg v-if="!showPwd" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                <svg v-else viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/><path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.064 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/></svg>
              </button>
            </div>
            <div v-if="form.password" class="strength">
              <div class="strength__bars">
                <div v-for="i in 4" :key="i" class="strength__bar" :class="i <= pwdStrength ? `s${pwdStrength}` : ''" />
              </div>
              <span class="strength__label" :class="`s${pwdStrength}`">{{ pwdLabel }}</span>
            </div>
          </div>

          <div class="field">
            <label for="confirm">Confirm password</label>
            <div class="field__wrap">
              <svg class="field__ico" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <input id="confirm" v-model="form.password_confirmation"
                :type="showPwd ? 'text' : 'password'"
                placeholder="Repeat your password" autocomplete="new-password"
                :class="['has-eye', { mismatch: form.password_confirmation && form.password !== form.password_confirmation }]"
                :disabled="loading" required />
            </div>
          </div>

          <Transition name="err">
            <div v-if="error" class="alert" role="alert">
              <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
              {{ error }}
            </div>
          </Transition>

          <p class="terms">
            By clicking "Create Account", you agree to our
            <NuxtLink to="/terms" class="link">Terms</NuxtLink> and
            <NuxtLink to="/privacy" class="link">Privacy Policy</NuxtLink>.
          </p>

          <button type="submit" class="btn-primary" :disabled="loading || !canSubmit">
            <span v-if="!loading">Create Account</span>
            <span v-else class="spinner-row">
              <svg viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"
                  stroke-dasharray="40 20" stroke-linecap="round"
                  style="transform-origin:center;animation:spin .8s linear infinite"/>
              </svg>
              Creating account…
            </span>
          </button>

        </form>

        <div class="divider"><span>or</span></div>

        <a :href="googleAuthUrl" class="btn-google">
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
          </svg>
          Continue with Google
        </a>

        <p class="form-footer">Already have an account? <NuxtLink to="/login" class="link">Log in</NuxtLink></p>

      </div>
    </div>

    <!-- Right: brand panel -->
    <div class="brand-panel">
      <div class="brand-panel__inner">
        <p class="brand-tag">START FOR FREE</p>
        <div class="brand-content">
          <h1>Create<br/>an account</h1>
          <p>Join hundreds of IT professionals who have already accelerated their careers with CODECV coaching and learning paths.</p>
        </div>
        <NuxtLink to="/" class="brand-logo">
          <img src="/images/Logo/codecv.png" alt="CODECV" />
        </NuxtLink>
        <p class="brand-copy">© {{ new Date().getFullYear() }} CODECV. All Rights Reserved</p>
      </div>
    </div>

    </div><!-- /auth-card -->
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: false, middleware: 'guest' })
useHead({ title: 'Create Account — CODECV' })

const { register } = useAuth()
const config        = useRuntimeConfig()

const googleAuthUrl = computed(() => `${config.public.apiBase}/api/auth/google/redirect`)

const form = reactive({
  fullname: '',
  email: '',
  password: '',
  password_confirmation: '',
})
const showPwd = ref(false)
const error   = ref('')
const loading = ref(false)

const pwdStrength = computed(() => {
  const p = form.password
  if (!p) return 0
  let s = 0
  if (p.length >= 8) s++
  if (/[A-Z]/.test(p)) s++
  if (/[0-9]/.test(p)) s++
  if (/[^A-Za-z0-9]/.test(p)) s++
  return s
})
const pwdLabel = computed(() => ['', 'Weak', 'Fair', 'Good', 'Strong'][pwdStrength.value] ?? '')

const canSubmit = computed(() =>
  form.fullname.trim() &&
  form.email.trim() &&
  form.password.length >= 8 &&
  form.password === form.password_confirmation
)

async function handleRegister() {
  if (!canSubmit.value) {
    error.value = form.password !== form.password_confirmation
      ? 'Passwords do not match.'
      : 'Please fill in all fields correctly.'
    return
  }
  loading.value = true
  error.value   = ''
  const result = await register({ ...form })
  loading.value = false
  if (result.success) navigateTo('/onboarding')
  else {
    error.value = result.error || 'Registration failed.'
    form.password = ''
    form.password_confirmation = ''
  }
}
</script>

<style scoped>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.auth-page {
  --accent:  #0284c7;
  --accent2: #06b6d4;
  --glow:    rgba(2,132,199,0.2);
  --error:   #ef4444;
  --font:    'Inter', system-ui, sans-serif;
  --ease:    cubic-bezier(0.16,1,0.3,1);
  font-family: var(--font);
  min-height: 100dvh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
  background: #f1f5f9;
}
.auth-card {
  display: flex;
  width: 100%;
  max-width: 900px;
  min-height: 560px;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0,0,0,0.12), 0 4px 16px rgba(0,0,0,0.06);
}

/* ── Form panel ─────────────────────────────────── */
.form-panel {
  flex: 1;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2.5rem;
}
.form-panel__inner {
  width: 100%;
  max-width: 400px;
  animation: rise .6s var(--ease) both;
}
@keyframes rise {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}

.form-header { margin-bottom: 1.75rem; }
.form-header h2 { font-size: 1.6rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; margin-bottom: 0.35rem; }
.form-header p { font-size: 0.875rem; color: #64748b; }

/* Fields */
.field { display: flex; flex-direction: column; gap: 0.45rem; margin-bottom: 1rem; }
.field label { font-size: 0.8rem; font-weight: 600; color: #374151; }
.field__wrap { position: relative; display: flex; align-items: center; }
.field__ico { position: absolute; left: 0.9rem; width: 15px; height: 15px; color: #9ca3af; pointer-events: none; }
.field__wrap input {
  width: 100%; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 10px;
  padding: 0.8rem 0.9rem 0.8rem 2.6rem; font-family: var(--font); font-size: 0.9375rem;
  color: #0f172a; outline: none; transition: border-color .15s, box-shadow .15s, background .15s;
}
.field__wrap input::placeholder { color: #c0c9d4; }
.field__wrap input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--glow); background: #fff; }
.field__wrap input:disabled { opacity: 0.5; cursor: not-allowed; }
.field__wrap input.has-eye { padding-right: 2.75rem; }
.field__wrap input.mismatch { border-color: #fca5a5; }
.eye-btn { position: absolute; right: 0.85rem; background: none; border: none; cursor: pointer; color: #9ca3af; padding: 0; display: flex; align-items: center; transition: color .15s; }
.eye-btn:hover { color: #6b7280; }
.eye-btn svg { width: 16px; height: 16px; }

.strength { display: flex; align-items: center; gap: 0.5rem; margin-top: 0.25rem; }
.strength__bars { display: flex; gap: 3px; flex: 1; }
.strength__bar { flex: 1; height: 3px; border-radius: 9px; background: #e2e8f0; transition: background .3s; }
.strength__bar.s1 { background: #f87171; }
.strength__bar.s2 { background: #fbbf24; }
.strength__bar.s3 { background: #34d399; }
.strength__bar.s4 { background: #10b981; }
.strength__label { font-size: 0.68rem; font-weight: 700; min-width: 34px; text-align: right; }
.strength__label.s1 { color: #f87171; }
.strength__label.s2 { color: #fbbf24; }
.strength__label.s3 { color: #34d399; }
.strength__label.s4 { color: #10b981; }

.alert { display: flex; align-items: center; gap: 0.6rem; padding: 0.75rem 1rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; font-size: 0.875rem; color: var(--error); margin-bottom: 0.9rem; }
.alert svg { width: 15px; height: 15px; flex-shrink: 0; }
.err-enter-active { animation: errIn .3s var(--ease); }
@keyframes errIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }

.terms { font-size: 0.76rem; color: #94a3b8; line-height: 1.5; margin-bottom: 1rem; }

.btn-primary {
  width: 100%; padding: 0.9rem; background: var(--accent); color: #fff;
  font-family: var(--font); font-size: 0.9375rem; font-weight: 700;
  border: none; border-radius: 10px; cursor: pointer;
  transition: background .2s, transform .15s, box-shadow .2s; letter-spacing: 0.01em;
}
.btn-primary:hover:not(:disabled) { background: #4f46e5; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,0.35); }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
.spinner-row { display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
.spinner-row svg { width: 16px; height: 16px; }
@keyframes spin { to { transform: rotate(360deg); } }

.form-footer { margin-top: 1.5rem; text-align: center; font-size: 0.875rem; color: #64748b; }
.link { color: var(--accent); font-weight: 600; text-decoration: none; transition: opacity .15s; }
.link:hover { opacity: 0.75; }

/* ── Brand panel ────────────────────────────────── */
.brand-panel {
  width: 45%;
  background-image:
    radial-gradient(ellipse at 80% 60%, rgba(6,182,212,0.25) 0%, transparent 55%),
    radial-gradient(ellipse at 15% 15%, rgba(14,165,233,0.2) 0%, transparent 50%),
    linear-gradient(145deg, #0a1628 0%, #0d2137 50%, #0a3d62 100%);
  display: flex;
  flex-direction: column;
  padding: 2.5rem;
  position: relative;
  overflow: hidden;
}
.brand-panel::before {
  content: '';
  position: absolute;
  top: -80px; left: -80px;
  width: 320px; height: 320px;
  background: radial-gradient(circle, rgba(6,182,212,0.2) 0%, transparent 70%);
  pointer-events: none;
}
.brand-panel::after {
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
.brand-panel__inner {
  display: flex;
  flex-direction: column;
  height: 100%;
  position: relative;
  z-index: 1;
}
.brand-tag {
  font-size: 0.7rem;
  font-weight: 800;
  letter-spacing: 0.15em;
  color: rgba(255,255,255,0.5);
  margin-bottom: auto;
}
.brand-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 2rem 0;
}
.brand-content h1 {
  font-size: clamp(2rem, 3.5vw, 2.8rem);
  font-weight: 800;
  color: #fff;
  line-height: 1.1;
  letter-spacing: -0.025em;
  margin-bottom: 1.25rem;
}
.brand-content p {
  font-size: 0.9375rem;
  color: rgba(255,255,255,0.6);
  line-height: 1.7;
  max-width: 320px;
}
.brand-logo {
  display: inline-block;
  margin-bottom: 1.25rem;
}
.brand-logo img {
  height: 120px;
  width: auto;
  filter: brightness(0) invert(1);
  opacity: 0.95;
}
.brand-copy {
  font-size: 0.75rem;
  color: rgba(255,255,255,0.35);
}

.divider { display: flex; align-items: center; gap: 0.75rem; margin: 1.25rem 0; color: #cbd5e1; font-size: 0.75rem; }
.divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }

.btn-google {
  display: flex; align-items: center; justify-content: center; gap: 0.65rem;
  width: 100%; padding: 0.8rem 1rem;
  background: #fff; border: 1.5px solid #e2e8f0; border-radius: 10px;
  font-family: var(--font); font-size: 0.9375rem; font-weight: 600; color: #0f172a;
  text-decoration: none; cursor: pointer;
  transition: border-color .15s, box-shadow .15s, background .15s;
}
.btn-google:hover { border-color: #94a3b8; box-shadow: 0 2px 8px rgba(0,0,0,0.06); background: #f8fafc; }
.btn-google svg { width: 18px; height: 18px; flex-shrink: 0; }

/* Mobile */
@media (max-width: 768px) {
  .auth-card { flex-direction: column-reverse; }
  .brand-panel { width: 100%; min-height: 180px; padding: 2rem; }
  .brand-tag { display: none; }
  .brand-content { padding: 1rem 0; }
  .brand-copy { display: none; }
  .form-panel { padding: 2rem 1.25rem; }
}
</style>
