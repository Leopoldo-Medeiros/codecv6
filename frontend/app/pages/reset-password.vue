<template>
  <div class="auth-page">
    <div class="auth-card">

    <div class="brand-panel">
      <div class="brand-panel__inner">
        <NuxtLink to="/" class="brand-logo">
          <img src="/images/Logo/codecv.png" alt="CODECV" />
        </NuxtLink>
        <div class="brand-content">
          <h1>Set a new<br/>password</h1>
          <p>Choose a strong password to keep your CODECV account secure.</p>
        </div>
        <p class="brand-copy">© {{ new Date().getFullYear() }} CODECV. All Rights Reserved</p>
      </div>
    </div>

    <div class="form-panel">
      <div class="form-panel__inner">

        <div v-if="!token || !email" class="error-state">
          <p>This reset link is invalid or missing. Please request a new one.</p>
          <NuxtLink to="/forgot-password" class="btn-primary" style="display:block;text-align:center;text-decoration:none;margin-top:1.25rem">Request New Link</NuxtLink>
        </div>

        <template v-else-if="!done">
          <div class="form-header">
            <h2>Set new password</h2>
            <p>Choose a strong password for your account.</p>
          </div>

          <form @submit.prevent="handleSubmit" novalidate>

            <div class="field">
              <label for="password">New password</label>
              <div class="field__wrap">
                <svg class="field__ico" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
                <input id="password" v-model="password" :type="showPwd ? 'text' : 'password'"
                  placeholder="Min. 8 characters" class="has-eye" :disabled="loading" required />
                <button type="button" class="eye-btn" @click="showPwd = !showPwd" tabindex="-1">
                  <svg v-if="!showPwd" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                  <svg v-else viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/><path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.064 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/></svg>
                </button>
              </div>
            </div>

            <div class="field">
              <label for="confirm">Confirm password</label>
              <div class="field__wrap">
                <svg class="field__ico" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
                <input id="confirm" v-model="confirm" :type="showPwd ? 'text' : 'password'"
                  placeholder="Repeat password" class="has-eye" :disabled="loading" required />
              </div>
            </div>

            <Transition name="err">
              <div v-if="error" class="alert" role="alert">
                <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                {{ error }}
              </div>
            </Transition>

            <button type="submit" class="btn-primary" :disabled="loading || !canSubmit">
              <span v-if="!loading">Reset Password</span>
              <span v-else class="spinner-row">
                <svg viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"
                    stroke-dasharray="40 20" stroke-linecap="round"
                    style="transform-origin:center;animation:spin .8s linear infinite"/>
                </svg>
                Resetting…
              </span>
            </button>

          </form>

          <p class="form-footer"><NuxtLink to="/login" class="link">← Back to sign in</NuxtLink></p>
        </template>

        <div v-else class="success">
          <div class="success__icon">
            <svg viewBox="0 0 24 24" fill="none">
              <circle cx="12" cy="12" r="10" stroke="#10b981" stroke-width="1.5"/>
              <path d="M8 12l3 3 5-5" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <h2>Password updated!</h2>
          <p>Your password has been reset. You can now sign in with your new password.</p>
          <NuxtLink to="/login" class="btn-primary" style="display:block;text-align:center;text-decoration:none;margin-top:1.5rem">Sign In</NuxtLink>
        </div>

      </div>
    </div>

    </div><!-- /auth-card -->
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: false, middleware: 'guest' })
useHead({ title: 'Reset Password — CODECV' })

const route  = useRoute()
const config = useRuntimeConfig()

const token    = computed(() => route.query.token as string | undefined)
const email    = computed(() => route.query.email as string | undefined)
const password = ref('')
const confirm  = ref('')
const showPwd  = ref(false)
const loading  = ref(false)
const error    = ref('')
const done     = ref(false)

const canSubmit = computed(() => password.value.length >= 8 && confirm.value.length >= 8)

async function handleSubmit() {
  if (!canSubmit.value) return
  if (password.value !== confirm.value) { error.value = 'Passwords do not match.'; return }
  loading.value = true
  error.value   = ''
  try {
    await $fetch('/api/reset-password', {
      baseURL: config.public.apiBase as string,
      method: 'POST',
      body: { token: token.value, email: email.value, password: password.value, password_confirmation: confirm.value },
      headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
    })
    done.value = true
  } catch (err: any) {
    error.value = err?.data?.message || 'Could not reset password. The link may have expired.'
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
  content: ''; position: absolute; bottom: -80px; right: -80px; width: 320px; height: 320px;
  background: radial-gradient(circle, rgba(99,102,241,0.25) 0%, transparent 70%); pointer-events: none;
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
.field__wrap input.has-eye { padding-right: 2.75rem; }
.eye-btn { position: absolute; right: 0.85rem; background: none; border: none; cursor: pointer; color: #9ca3af; padding: 0; display: flex; align-items: center; transition: color .15s; }
.eye-btn:hover { color: #6b7280; }
.eye-btn svg { width: 16px; height: 16px; }

.alert { display: flex; align-items: center; gap: 0.6rem; padding: 0.75rem 1rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; font-size: 0.875rem; color: var(--error); margin-bottom: 1rem; }
.alert svg { width: 15px; height: 15px; flex-shrink: 0; }
.err-enter-active { animation: errIn .3s var(--ease); }
@keyframes errIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }

.btn-primary { width: 100%; padding: 0.9rem; background: var(--accent); color: #fff; font-family: var(--font); font-size: 0.9375rem; font-weight: 700; border: none; border-radius: 10px; cursor: pointer; transition: background .2s, transform .15s, box-shadow .2s; }
.btn-primary:hover:not(:disabled) { background: #4f46e5; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,0.35); }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
.spinner-row { display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
.spinner-row svg { width: 16px; height: 16px; }
@keyframes spin { to { transform: rotate(360deg); } }

.form-footer { margin-top: 1.5rem; font-size: 0.875rem; color: #64748b; }
.link { color: var(--accent); font-weight: 600; text-decoration: none; transition: opacity .15s; }
.link:hover { opacity: 0.75; }

.success, .error-state { text-align: center; }
.success__icon { width: 64px; height: 64px; margin: 0 auto 1.25rem; }
.success__icon svg { width: 100%; height: 100%; }
.success h2 { font-size: 1.4rem; font-weight: 800; color: #0f172a; margin-bottom: 0.6rem; }
.success p, .error-state p { font-size: 0.875rem; color: #64748b; line-height: 1.6; }

@media (max-width: 768px) {
  .auth-card { flex-direction: column; }
  .brand-panel { width: 100%; min-height: 200px; padding: 2rem; }
  .brand-copy { display: none; }
  .form-panel { padding: 2rem 1.25rem; }
}
</style>
