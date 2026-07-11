<template>
  <TerminalShell title="guest@codecv: ~/login — zsh — 80×24" :mood="mood" :eye-shift="eyeShift" @body-click="focusCurrent">
    <p class="term-line term-line--dim term-in-1">Last login: just now on ttys001</p>

    <p class="term-line term-in-2">
      <span class="term-dollar">$</span><span class="term-cmd">codecv login</span><span class="term-cursor" aria-hidden="true" />
    </p>

    <form class="tui-box term-in-3" novalidate @submit.prevent="submit">
      <TerminalPrompt
        id="term-email"
        ref="emailPrompt"
        :model-value="email"
        label="Email"
        type="email"
        placeholder="you@example.com"
        autocomplete="email"
        enterkeyhint="next"
        :done="emailValid"
        :disabled="loading || done"
        @focus="active = 'email'"
        @update:model-value="(v: string) => { email = v; onTyped() }"
        @enter="confirmEmail"
      />
      <TerminalPrompt
        id="term-password"
        ref="passwordPrompt"
        :model-value="password"
        label="Password"
        type="password"
        placeholder="••••••••"
        autocomplete="current-password"
        enterkeyhint="go"
        revealable
        :disabled="loading || done"
        @focus="active = 'password'"
        @update:model-value="(v: string) => { password = v; onTyped() }"
        @enter="submit"
      />

      <button type="submit" class="tui-btn tui-btn--primary" :disabled="loading || done || !email || !password">
        <template v-if="loading"><span class="term-spinner">{{ spinner.frame.value }}</span> authenticating…</template>
        <template v-else>Sign in <kbd>⏎</kbd></template>
      </button>

      <div class="tui-sep"># or</div>

      <a :href="googleAuthUrl" class="tui-btn tui-btn--ghost">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
          <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
          <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
          <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
        </svg>
        Continue with Google
      </a>
    </form>

    <!-- status output -->
    <Transition name="term-fade">
      <p v-if="error" class="term-line term-line--error" role="alert">
        <span class="term-glyph--err">✘</span> {{ error }}
      </p>
    </Transition>

    <template v-if="done">
      <p class="term-line term-line--ok"><span class="term-glyph">✔</span> authenticated as {{ email }}</p>
      <p class="term-line term-line--dim">→ opening <span class="term-flag">/dashboard</span>…</p>
    </template>

    <!-- helper "commands" -->
    <template v-if="!done">
      <p class="term-line term-in-4">&nbsp;</p>
      <p class="term-line term-comment term-in-4"># new here? <NuxtLink to="/register">create an account</NuxtLink></p>
      <p class="term-line term-comment term-in-5"># <NuxtLink to="/forgot-password">forgot your password?</NuxtLink></p>
    </template>
  </TerminalShell>
</template>

<script setup lang="ts">
definePageMeta({ layout: false, middleware: 'guest' })
useHead({ title: 'Sign In — CODECV' })

const { login, googleAuthUrl } = useAuth()
const spinner = useTerminalSpinner()

const active = ref<'email' | 'password'>('email')
const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)
const done = ref(false)

const emailPrompt = ref<{ focus: () => void } | null>(null)
const passwordPrompt = ref<{ focus: () => void } | null>(null)

const emailValid = computed(() => email.value.includes('@') && email.value.includes('.'))

/* Mascot reactions */
const mood = computed(() => {
  if (done.value) return 'happy'
  if (loading.value) return 'think'
  if (error.value) return 'error'
  if (active.value === 'password') return 'hide'
  return 'watch'
})
const eyeShift = computed(() => {
  if (active.value !== 'email' || !email.value) return 0
  return Math.min(1, email.value.length / 28) * 2 - 1
})

// real keystrokes dismiss the error (and the mascot's X-eyes);
// programmatic field resets keep the error visible
function onTyped() { if (error.value) error.value = '' }

function focusCurrent() {
  if (loading.value || done.value) return
  if (!email.value || !emailValid.value) emailPrompt.value?.focus()
  else if (active.value === 'password') passwordPrompt.value?.focus()
  else emailPrompt.value?.focus()
}

function confirmEmail() {
  error.value = ''
  if (!emailValid.value) {
    error.value = `invalid email: "${email.value || ' '}" — expected user@domain`
    return
  }
  passwordPrompt.value?.focus()
}

let redirectTimer: ReturnType<typeof setTimeout> | null = null

async function submit() {
  if (loading.value || done.value) return
  error.value = ''
  if (!emailValid.value) {
    error.value = 'invalid email — fix it and try again'
    emailPrompt.value?.focus()
    return
  }
  if (!password.value) {
    error.value = 'password required'
    passwordPrompt.value?.focus()
    return
  }

  loading.value = true
  spinner.start()
  const result = await login(email.value, password.value)
  spinner.stop()
  loading.value = false

  if (result.success) {
    done.value = true
    redirectTimer = setTimeout(() => navigateTo('/dashboard'), 900)
  } else {
    error.value = `auth failed: ${result.error || 'invalid credentials'}`
    password.value = ''
    nextTick(() => passwordPrompt.value?.focus())
  }
}

onMounted(() => emailPrompt.value?.focus())
onUnmounted(() => {
  if (redirectTimer) clearTimeout(redirectTimer)
})
</script>
