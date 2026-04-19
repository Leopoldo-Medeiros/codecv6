<template>
  <div class="onboarding-page">
    <div class="onboarding-card">

      <!-- Header -->
      <div class="card-header">
        <NuxtLink to="/" class="logo-link">
          <img src="/images/codecv.png" alt="CODECV" class="logo" onerror="this.style.display='none'" />
        </NuxtLink>
        <h1>Welcome, {{ user?.fullname?.split(' ')[0] }}!</h1>
        <p>Help us personalise your experience — takes 60 seconds.</p>
      </div>

      <!-- Step indicator -->
      <div class="steps">
        <div v-for="i in 3" :key="i" class="step-item">
          <div class="step-circle" :class="stepClass(i)">
            <UIcon v-if="i < step" name="i-heroicons-check" class="h-3.5 w-3.5" />
            <span v-else>{{ i }}</span>
          </div>
          <div v-if="i < 3" class="step-line" :class="i < step ? 'step-line--done' : ''" />
        </div>
      </div>

      <!-- ── STEP 1 — Your profile ──────────────────────────── -->
      <Transition :name="transitionName" mode="out-in">
        <div v-if="step === 1" key="1" class="step-body">
          <h2 class="step-title">Your profile</h2>

          <div class="field">
            <label>Current role</label>
            <div class="field__wrap">
              <UIcon name="i-heroicons-briefcase" class="field__ico" />
              <select v-model="form.profession" required>
                <option value="" disabled>Select your role…</option>
                <optgroup label="Development">
                  <option>Frontend Developer</option>
                  <option>Backend Developer</option>
                  <option>Full-Stack Developer</option>
                  <option>Mobile Developer</option>
                  <option>DevOps / Platform Engineer</option>
                  <option>Data Engineer</option>
                  <option>Machine Learning Engineer</option>
                </optgroup>
                <optgroup label="Other IT">
                  <option>QA / Test Engineer</option>
                  <option>Cloud Architect</option>
                  <option>Security Engineer</option>
                  <option>IT Manager / Tech Lead</option>
                  <option>Product Manager</option>
                  <option>UX / UI Designer</option>
                </optgroup>
                <optgroup label="Status">
                  <option>Student / Bootcamp</option>
                  <option>Career Changer</option>
                  <option>Freelancer / Consultant</option>
                  <option>Other</option>
                </optgroup>
              </select>
            </div>
          </div>

          <div class="field">
            <label>Experience level</label>
            <div class="card-grid">
              <button
                v-for="opt in levelOptions" :key="opt.value" type="button"
                class="option-card" :class="form.level === opt.value ? 'option-card--active' : ''"
                @click="form.level = opt.value"
              >
                <span class="option-card__icon">{{ opt.icon }}</span>
                <span class="option-card__label">{{ opt.label }}</span>
                <span class="option-card__sub">{{ opt.sub }}</span>
              </button>
            </div>
          </div>

          <div class="step-actions">
            <button class="btn-primary" :disabled="!step1Valid" @click="next">
              Continue <UIcon name="i-heroicons-arrow-right" class="h-4 w-4" />
            </button>
          </div>
        </div>
      </Transition>

      <!-- ── STEP 2 — What you're looking for ──────────────── -->
      <Transition :name="transitionName" mode="out-in">
        <div v-if="step === 2" key="2" class="step-body">
          <h2 class="step-title">What are you looking for?</h2>

          <div class="field">
            <label>Choose the product that fits your goal</label>
            <div class="product-grid">
              <button
                v-for="opt in productOptions" :key="opt.value" type="button"
                class="product-card" :class="form.product_interest === opt.value ? 'product-card--active' : ''"
                @click="form.product_interest = opt.value"
              >
                <span class="product-card__icon">{{ opt.icon }}</span>
                <p class="product-card__label">{{ opt.label }}</p>
                <p class="product-card__desc">{{ opt.desc }}</p>
                <p class="product-card__price">{{ opt.price }}</p>
              </button>
            </div>
          </div>

          <div class="field">
            <label>When do you want to see results?</label>
            <div class="tag-row">
              <button
                v-for="opt in timelineOptions" :key="opt.value" type="button"
                class="tag-btn" :class="form.timeline === opt.value ? 'tag-btn--active' : ''"
                @click="form.timeline = opt.value"
              >{{ opt.label }}</button>
            </div>
          </div>

          <div class="step-actions">
            <button class="btn-ghost" @click="prev">
              <UIcon name="i-heroicons-arrow-left" class="h-4 w-4" /> Back
            </button>
            <button class="btn-primary" :disabled="!step2Valid" @click="next">
              Continue <UIcon name="i-heroicons-arrow-right" class="h-4 w-4" />
            </button>
          </div>
        </div>
      </Transition>

      <!-- ── STEP 3 — Your situation ───────────────────────── -->
      <Transition :name="transitionName" mode="out-in">
        <div v-if="step === 3" key="3" class="step-body">
          <h2 class="step-title">Your situation</h2>

          <div class="field">
            <label>Main stack <span class="label-hint">(select all that apply)</span></label>
            <div class="tag-row tag-row--wrap">
              <button
                v-for="s in stackOptions" :key="s" type="button"
                class="tag-btn" :class="form.stack.includes(s) ? 'tag-btn--active' : ''"
                @click="toggleStack(s)"
              >{{ s }}</button>
            </div>
          </div>

          <div class="field">
            <label>Hours available per week</label>
            <div class="tag-row">
              <button
                v-for="opt in availabilityOptions" :key="opt.value" type="button"
                class="tag-btn" :class="form.availability_hours === opt.value ? 'tag-btn--active' : ''"
                @click="form.availability_hours = opt.value"
              >{{ opt.label }}</button>
            </div>
          </div>

          <div class="field">
            <label>Anything else you'd like us to know? <span class="label-hint">(optional)</span></label>
            <div class="field__wrap field__wrap--textarea">
              <textarea
                v-model="form.goal" rows="3" maxlength="500"
                placeholder="Your current situation, specific challenges, what you've tried before…"
              ></textarea>
              <span class="char-count">{{ form.goal.length }}/500</span>
            </div>
          </div>

          <Transition name="err">
            <div v-if="error" class="alert" role="alert">
              <UIcon name="i-heroicons-exclamation-circle" class="alert__ico" />
              {{ error }}
            </div>
          </Transition>

          <div class="step-actions">
            <button class="btn-ghost" @click="prev">
              <UIcon name="i-heroicons-arrow-left" class="h-4 w-4" /> Back
            </button>
            <button class="btn-primary" :disabled="loading || !step3Valid" @click="handleSubmit">
              <span v-if="!loading">Complete setup</span>
              <span v-else class="spinner-row">
                <svg viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"
                    stroke-dasharray="40 20" stroke-linecap="round"
                    style="transform-origin:center;animation:spin .8s linear infinite"/>
                </svg>
                Saving…
              </span>
            </button>
          </div>

          <button type="button" class="btn-skip" @click="navigateTo('/dashboard')">
            Skip for now
          </button>
        </div>
      </Transition>

    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: false, middleware: 'auth' })
useHead({ title: 'Welcome — CODECV' })

const { user, updateUser } = useAuth()
const { patch } = useApi()

const step           = ref(1)
const transitionName = ref('slide-left')
const loading        = ref(false)
const error          = ref('')

const form = reactive({
  profession:         '',
  level:              '' as string,
  stack:              [] as string[],
  product_interest:   '' as string,
  availability_hours: null as number | null,
  timeline:           '' as string,
  goal:               '',
})

// ── Options ────────────────────────────────────────────────
const levelOptions = [
  { value: 'junior',  icon: '🌱', label: 'Júnior',  sub: '0–2 years' },
  { value: 'mid',     icon: '⚡', label: 'Pleno',   sub: '2–5 years' },
  { value: 'senior',  icon: '🚀', label: 'Sênior',  sub: '5+ years'  },
  { value: 'manager', icon: '🎯', label: 'Manager', sub: 'Tech Lead / Manager' },
]

const productOptions = [
  {
    value: 'self-serve',
    icon:  '📄',
    label: 'Career Accelerator',
    desc:  'CV review, LinkedIn optimisation, interview prep',
    price: 'From €99/month',
  },
  {
    value: 'bootcamp',
    icon:  '🧑‍💻',
    label: 'Laravel Bootcamp',
    desc:  'Laravel + New Relic · 12–16 weeks · cohort-based',
    price: '€2,500–5,000',
  },
  {
    value: 'mentorship',
    icon:  '🎓',
    label: '1-on-1 Mentorship',
    desc:  'Bi-weekly sessions, personalised roadmap',
    price: '€800–1,500/month',
  },
]

const timelineOptions = [
  { value: '1-3m',     label: '1–3 months'  },
  { value: '3-6m',     label: '3–6 months'  },
  { value: '6-12m',    label: '6–12 months' },
  { value: 'flexible', label: 'No rush'     },
]

const stackOptions = [
  'Laravel/PHP', 'React', 'Vue.js', 'Node.js',
  'TypeScript', 'Python', 'DevOps/Cloud', 'Mobile',
  'Java/Spring', 'Other',
]

const availabilityOptions = [
  { value: 5,  label: '~5h/week'  },
  { value: 10, label: '~10h/week' },
  { value: 20, label: '~20h/week' },
  { value: 40, label: '40h+ week' },
]

// ── Validation ─────────────────────────────────────────────
const step1Valid = computed(() => form.profession && form.level)
const step2Valid = computed(() => form.product_interest && form.timeline)
const step3Valid = computed(() => form.stack.length > 0 && form.availability_hours !== null)

// ── Navigation ─────────────────────────────────────────────
function next() {
  transitionName.value = 'slide-left'
  step.value++
}
function prev() {
  transitionName.value = 'slide-right'
  step.value--
}

function stepClass(i: number) {
  if (i < step.value)  return 'step-circle--done'
  if (i === step.value) return 'step-circle--active'
  return ''
}

function toggleStack(s: string) {
  const idx = form.stack.indexOf(s)
  if (idx === -1) form.stack.push(s)
  else form.stack.splice(idx, 1)
}

// ── Submit ──────────────────────────────────────────────────
async function handleSubmit() {
  loading.value = true
  error.value   = ''
  try {
    const res = await patch<{ message: string; user: any }>('/me/onboarding', { ...form })
    updateUser(res.user)
    navigateTo('/dashboard')
  } catch (e: any) {
    error.value = e?.data?.message || 'Something went wrong. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.onboarding-page {
  --accent:  #6366f1;
  --glow:    rgba(99,102,241,0.18);
  --error:   #ef4444;
  --font:    'Inter', system-ui, sans-serif;
  --ease:    cubic-bezier(0.16,1,0.3,1);
  font-family: var(--font);
  min-height: 100dvh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
  background: linear-gradient(135deg, #f0f4ff 0%, #e8f4f8 100%);
}

.onboarding-card {
  width: 100%;
  max-width: 580px;
  background: #fff;
  border-radius: 20px;
  padding: 2.5rem;
  box-shadow: 0 20px 60px rgba(0,0,0,0.1), 0 4px 16px rgba(0,0,0,0.05);
}

/* Header */
.card-header { text-align: center; margin-bottom: 1.75rem; }
.logo-link { display: inline-block; margin-bottom: 1rem; }
.logo { height: 44px; width: auto; mix-blend-mode: multiply; }
.card-header h1 { font-size: 1.5rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; margin-bottom: 0.35rem; }
.card-header p  { font-size: 0.875rem; color: #64748b; }

/* Step indicator */
.steps {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0;
  margin-bottom: 2rem;
}
.step-item { display: flex; align-items: center; }
.step-circle {
  width: 28px; height: 28px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 0.75rem; font-weight: 700;
  background: #f1f5f9; color: #94a3b8;
  border: 2px solid #e2e8f0;
  transition: all .25s var(--ease);
}
.step-circle--active {
  background: var(--accent); color: #fff; border-color: var(--accent);
  box-shadow: 0 0 0 4px rgba(99,102,241,0.15);
}
.step-circle--done { background: #10b981; color: #fff; border-color: #10b981; }
.step-line {
  width: 48px; height: 2px;
  background: #e2e8f0; transition: background .25s;
}
.step-line--done { background: #10b981; }

/* Step body */
.step-body { animation: rise .35s var(--ease) both; }
@keyframes rise { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

.step-title {
  font-size: 1.1rem; font-weight: 700; color: #0f172a;
  margin-bottom: 1.5rem;
}

/* Fields */
.field { margin-bottom: 1.5rem; }
.field label {
  display: block; font-size: 0.82rem; font-weight: 600;
  color: #374151; margin-bottom: 0.6rem;
}
.label-hint { font-weight: 400; color: #94a3b8; }
.field__wrap { position: relative; }
.field__ico {
  position: absolute; left: 0.9rem; top: 50%; transform: translateY(-50%);
  width: 16px; height: 16px; color: #9ca3af; pointer-events: none;
}

select {
  width: 100%; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 10px;
  padding: 0.8rem 0.9rem 0.8rem 2.6rem; font-family: var(--font); font-size: 0.9375rem;
  color: #0f172a; outline: none; cursor: pointer; appearance: none;
  transition: border-color .15s, box-shadow .15s;
}
select:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--glow); background: #fff; }

/* Level cards */
.card-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.6rem; }
.option-card {
  display: flex; flex-direction: column; align-items: center; gap: 0.2rem;
  padding: 0.9rem 0.5rem; border-radius: 10px; border: 1.5px solid #e2e8f0;
  background: #f8fafc; cursor: pointer; transition: all .15s;
  font-family: var(--font);
}
.option-card:hover { border-color: #a5b4fc; background: #eef2ff; }
.option-card--active { border-color: var(--accent); background: #eef2ff; box-shadow: 0 0 0 3px var(--glow); }
.option-card__icon  { font-size: 1.4rem; }
.option-card__label { font-size: 0.8rem; font-weight: 700; color: #0f172a; }
.option-card__sub   { font-size: 0.65rem; color: #94a3b8; text-align: center; }

/* Product cards */
.product-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; }
.product-card {
  display: flex; flex-direction: column; align-items: flex-start; gap: 0.3rem;
  padding: 1rem; border-radius: 12px; border: 1.5px solid #e2e8f0;
  background: #f8fafc; cursor: pointer; transition: all .15s;
  font-family: var(--font); text-align: left;
}
.product-card:hover { border-color: #a5b4fc; background: #eef2ff; }
.product-card--active { border-color: var(--accent); background: #eef2ff; box-shadow: 0 0 0 3px var(--glow); }
.product-card__icon  { font-size: 1.5rem; }
.product-card__label { font-size: 0.82rem; font-weight: 700; color: #0f172a; }
.product-card__desc  { font-size: 0.7rem; color: #64748b; line-height: 1.4; }
.product-card__price { font-size: 0.7rem; font-weight: 600; color: var(--accent); margin-top: auto; padding-top: 0.4rem; }

/* Tag buttons */
.tag-row { display: flex; flex-wrap: wrap; gap: 0.5rem; }
.tag-row--wrap { }
.tag-btn {
  padding: 0.45rem 0.9rem; border-radius: 99px; border: 1.5px solid #e2e8f0;
  background: #f8fafc; font-family: var(--font); font-size: 0.82rem;
  font-weight: 500; color: #475569; cursor: pointer; transition: all .15s;
}
.tag-btn:hover { border-color: #a5b4fc; color: var(--accent); }
.tag-btn--active { border-color: var(--accent); background: #eef2ff; color: var(--accent); font-weight: 600; }

/* Textarea */
.field__wrap--textarea { display: flex; flex-direction: column; }
textarea {
  width: 100%; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 10px;
  padding: 0.8rem; font-family: var(--font); font-size: 0.9375rem;
  color: #0f172a; outline: none; resize: vertical; min-height: 90px;
  transition: border-color .15s, box-shadow .15s;
}
textarea:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--glow); background: #fff; }
textarea::placeholder { color: #c0c9d4; }
.char-count { align-self: flex-end; font-size: 0.7rem; color: #94a3b8; margin-top: 0.25rem; }

/* Actions */
.step-actions {
  display: flex; justify-content: flex-end; gap: 0.75rem;
  margin-top: 1.75rem;
}
.btn-primary {
  display: flex; align-items: center; gap: 0.4rem;
  padding: 0.75rem 1.5rem; background: var(--accent); color: #fff;
  font-family: var(--font); font-size: 0.9375rem; font-weight: 700;
  border: none; border-radius: 10px; cursor: pointer;
  transition: background .2s, transform .15s, box-shadow .2s;
}
.btn-primary:hover:not(:disabled) {
  background: #4f46e5; transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(99,102,241,0.35);
}
.btn-primary:disabled { opacity: 0.45; cursor: not-allowed; }
.btn-ghost {
  display: flex; align-items: center; gap: 0.4rem;
  padding: 0.75rem 1rem; background: none;
  font-family: var(--font); font-size: 0.875rem; font-weight: 500;
  color: #64748b; border: 1.5px solid #e2e8f0; border-radius: 10px;
  cursor: pointer; transition: all .15s;
}
.btn-ghost:hover { border-color: #94a3b8; color: #374151; }
.btn-skip {
  display: block; width: 100%; margin-top: 0.75rem;
  padding: 0.6rem; background: none; color: #94a3b8;
  font-family: var(--font); font-size: 0.8rem;
  border: none; cursor: pointer; transition: color .15s;
}
.btn-skip:hover { color: #64748b; }

/* Alert */
.alert {
  display: flex; align-items: center; gap: 0.6rem;
  padding: 0.75rem 1rem; background: #fef2f2; border: 1px solid #fecaca;
  border-radius: 10px; font-size: 0.875rem; color: var(--error);
  margin-bottom: 1rem;
}
.alert__ico { width: 15px; height: 15px; flex-shrink: 0; }
.err-enter-active { animation: errIn .3s var(--ease); }
@keyframes errIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }

/* Slide transitions */
.slide-left-enter-active,
.slide-left-leave-active,
.slide-right-enter-active,
.slide-right-leave-active { transition: all .25s var(--ease); }

.slide-left-enter-from  { opacity: 0; transform: translateX(30px); }
.slide-left-leave-to    { opacity: 0; transform: translateX(-30px); }
.slide-right-enter-from { opacity: 0; transform: translateX(-30px); }
.slide-right-leave-to   { opacity: 0; transform: translateX(30px); }

.spinner-row { display: flex; align-items: center; gap: 0.5rem; }
.spinner-row svg { width: 16px; height: 16px; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Responsive */
@media (max-width: 500px) {
  .onboarding-card { padding: 1.75rem 1.25rem; }
  .card-grid { grid-template-columns: repeat(2, 1fr); }
  .product-grid { grid-template-columns: 1fr; }
}
</style>
