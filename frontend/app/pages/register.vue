<template>
  <TerminalShell title="guest@codecv: ~/register — zsh — 80×24" :mood="mood" :eye-shift="eyeShift" @body-click="focusCurrent">
    <p class="term-line term-line--dim term-in-1">Welcome to codecv v6.0.0 — account setup</p>

    <p class="term-line term-in-2">
      <span class="term-dollar">$</span><span class="term-cmd">codecv register</span><span class="term-cursor" aria-hidden="true" />
    </p>

    <form class="tui-box term-in-3" novalidate @submit.prevent="submit">
      <template v-for="(f, i) in FIELDS" :key="f.key">
        <TerminalPrompt
          :id="`term-${f.key}`"
          :ref="(el) => setPromptRef(f.key, el)"
          :label="f.label"
          :type="f.type"
          :placeholder="f.placeholder"
          :autocomplete="f.autocomplete"
          :enterkeyhint="f.enterkeyhint"
          :hint="f.hint"
          :revealable="f.revealable"
          :done="fieldValid[f.key]"
          :disabled="loading || done"
          :model-value="form[f.modelKey]"
          @focus="active = f.key"
          @update:model-value="(v: string) => { form[f.modelKey] = v; onTyped() }"
          @enter="advance(i)"
        />

        <!-- strength meter rides along under the password field -->
        <p v-if="f.key === 'password' && form.password" class="term-line">
          <span class="term-line--dim">strength </span>
          <span class="term-strength" :class="`term-strength--${pwdStrength}`">{{ '▰'.repeat(pwdStrength) + '▱'.repeat(4 - pwdStrength) }} {{ pwdLabel }}</span>
        </p>
      </template>

      <button type="submit" class="tui-btn tui-btn--primary" :disabled="loading || done || !canSubmit">
        <template v-if="loading"><span class="term-spinner">{{ spinner.frame.value }}</span> creating account…</template>
        <template v-else>Create account <kbd>⏎</kbd></template>
      </button>

      <div class="tui-sep"># or</div>

      <a :href="googleAuthUrl" class="tui-btn tui-btn--ghost">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
          <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
          <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
          <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
        </svg>
        Sign up with Google
      </a>
    </form>

    <!-- status output -->
    <Transition name="term-fade">
      <p v-if="error" class="term-line term-line--error" role="alert">
        <span class="term-glyph--err">✘</span> {{ error }}
      </p>
    </Transition>

    <template v-if="done">
      <p class="term-line term-line--ok"><span class="term-glyph">✔</span> account created for {{ form.email }}</p>
      <p class="term-line term-line--dim">→ opening <span class="term-flag">/onboarding</span>…</p>
    </template>

    <!-- helper "commands" -->
    <template v-if="!done">
      <p class="term-line term-in-4">&nbsp;</p>
      <p class="term-line term-comment term-in-4"># by continuing you agree to our <NuxtLink to="/terms">terms</NuxtLink> and <NuxtLink to="/privacy">privacy policy</NuxtLink></p>
      <p class="term-line term-comment term-in-5"># already have an account? <NuxtLink to="/login">sign in</NuxtLink></p>
    </template>
  </TerminalShell>
</template>

<script setup lang="ts">
definePageMeta({ layout: false, middleware: 'guest' })
useHead({ title: 'Create Account — CODECV' })

const { register, googleAuthUrl } = useAuth()
const spinner = useTerminalSpinner()

type FieldKey = 'fullname' | 'email' | 'password' | 'confirm'
type ModelKey = 'fullname' | 'email' | 'password' | 'password_confirmation'

interface FieldConfig {
  key: FieldKey
  modelKey: ModelKey
  label: string
  type: string
  placeholder: string
  autocomplete: string
  enterkeyhint: 'next' | 'go'
  hint: string
  revealable: boolean
}

const FIELDS: FieldConfig[] = [
  { key: 'fullname', modelKey: 'fullname', label: 'Full name', type: 'text', placeholder: 'Ada Lovelace', autocomplete: 'name', enterkeyhint: 'next', hint: '', revealable: false },
  { key: 'email', modelKey: 'email', label: 'Email', type: 'email', placeholder: 'you@example.com', autocomplete: 'email', enterkeyhint: 'next', hint: '', revealable: false },
  { key: 'password', modelKey: 'password', label: 'Password', type: 'password', placeholder: '••••••••', autocomplete: 'new-password', enterkeyhint: 'next', hint: 'min 8 chars', revealable: true },
  { key: 'confirm', modelKey: 'password_confirmation', label: 'Confirm password', type: 'password', placeholder: '••••••••', autocomplete: 'new-password', enterkeyhint: 'go', hint: '', revealable: true },
]

const active = ref<FieldKey>('fullname')
const error = ref('')
const loading = ref(false)
const done = ref(false)

const form = reactive<Record<ModelKey, string>>({
  fullname: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const fieldValid = computed<Record<FieldKey, boolean>>(() => ({
  fullname: form.fullname.trim() !== '',
  email: form.email.includes('@') && form.email.includes('.'),
  password: form.password.length >= 8,
  confirm: form.password_confirmation !== '' && form.password === form.password_confirmation,
}))

/* Prompt focus plumbing (imperative only — plain object, not reactive) */
type PromptHandle = { focus: () => void }
const promptRefs: Partial<Record<FieldKey, PromptHandle>> = {}
function setPromptRef(key: FieldKey, el: unknown) {
  promptRefs[key] = (el as PromptHandle) ?? undefined
}
function focusField(key: FieldKey) {
  if (loading.value || done.value) return
  promptRefs[key]?.focus()
}
function focusCurrent() {
  const firstInvalid = FIELDS.find(f => !fieldValid.value[f.key])
  focusField(firstInvalid?.key ?? 'fullname')
}

/* Mascot reactions */
const mood = computed(() => {
  if (done.value) return 'happy'
  if (loading.value) return 'think'
  if (error.value) return 'error'
  if (active.value === 'password' || active.value === 'confirm') return 'hide'
  return 'watch'
})
const eyeShift = computed(() => {
  const v = active.value === 'fullname' ? form.fullname : active.value === 'email' ? form.email : ''
  if (!v) return 0
  return Math.min(1, v.length / 28) * 2 - 1
})

// real keystrokes dismiss the error (and the mascot's X-eyes);
// programmatic field resets keep the error visible
function onTyped() { if (error.value) error.value = '' }

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
const pwdLabel = computed(() => (['', 'weak', 'fair', 'good', 'strong'] as const)[pwdStrength.value] ?? '')

const canSubmit = computed(() =>
  fieldValid.value.fullname
  && fieldValid.value.email
  && fieldValid.value.password
  && fieldValid.value.confirm,
)

/* Enter moves through the fields; on the last one it submits */
function advance(index: number) {
  if (index === FIELDS.length - 1) { submit(); return }
  focusField(FIELDS[index + 1]!.key)
}

let redirectTimer: ReturnType<typeof setTimeout> | null = null

async function submit() {
  if (loading.value || done.value) return
  error.value = ''
  if (!fieldValid.value.fullname) { error.value = 'full name required'; focusField('fullname'); return }
  if (!fieldValid.value.email) { error.value = `invalid email: "${form.email || ' '}" — expected user@domain`; focusField('email'); return }
  if (!fieldValid.value.password) { error.value = 'password too short — min 8 characters'; focusField('password'); return }
  if (form.password !== form.password_confirmation) {
    error.value = 'passwords do not match — try again'
    form.password_confirmation = ''
    nextTick(() => focusField('confirm'))
    return
  }

  loading.value = true
  spinner.start()
  const result = await register({ ...form })
  spinner.stop()
  loading.value = false

  if (result.success) {
    done.value = true
    redirectTimer = setTimeout(() => navigateTo('/onboarding'), 900)
  } else {
    error.value = `registration failed: ${result.error || 'unknown error'}`
    form.password = ''
    form.password_confirmation = ''
    nextTick(() => focusField('password'))
  }
}

onMounted(() => focusField('fullname'))
onUnmounted(() => {
  if (redirectTimer) clearTimeout(redirectTimer)
})
</script>
