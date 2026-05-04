<template>
  <NuxtLayout name="default">
    <div class="relative -mx-4 -mt-4 min-h-screen overflow-hidden bg-gray-950 px-4 py-10 sm:-mx-8 sm:-mt-8 sm:px-8">

      <!-- Ambient glow -->
      <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 left-1/2 h-80 w-80 -translate-x-1/2 rounded-full bg-emerald-500/10 blur-3xl" />
        <div class="absolute top-1/3 -left-20 h-60 w-60 rounded-full bg-emerald-600/5 blur-3xl" />
        <div class="absolute top-1/3 -right-20 h-60 w-60 rounded-full bg-emerald-600/5 blur-3xl" />
      </div>

      <div class="relative mx-auto max-w-3xl">

        <!-- Step indicator -->
        <div class="mb-12 flex items-start justify-center gap-0">
          <template v-for="(meta, i) in stepMeta" :key="i">
            <div class="flex flex-col items-center">
              <div
                :class="[
                  'flex h-9 w-9 items-center justify-center rounded-full border-2 text-sm font-bold transition-all duration-300',
                  i + 1 < step  ? 'border-emerald-500 bg-emerald-500 text-white' :
                  i + 1 === step ? 'border-emerald-400 bg-emerald-400/10 text-emerald-400' :
                                   'border-gray-700 bg-gray-900 text-gray-600',
                ]"
              >
                <UIcon v-if="i + 1 < step" name="i-heroicons-check-20-solid" class="h-4 w-4" />
                <span v-else>{{ i + 1 }}</span>
              </div>
              <span
                class="mt-2 hidden text-xs sm:block"
                :class="i + 1 === step ? 'font-semibold text-white' : i + 1 < step ? 'text-emerald-500' : 'text-gray-600'"
              >
                {{ meta.stepLabel }}
              </span>
            </div>
            <div
              v-if="i < stepMeta.length - 1"
              class="mx-1 mt-4 h-px w-16 transition-all duration-500 sm:w-24"
              :class="i + 1 < step ? 'bg-emerald-500' : 'bg-gray-800'"
            />
          </template>
        </div>

        <!-- Step heading -->
        <Transition name="fade" mode="out-in">
          <div :key="`h-${step}`" class="mb-8 text-center">
            <p class="mb-1 text-xs font-semibold uppercase tracking-widest text-emerald-500">
              Step {{ step }} of {{ TOTAL_STEPS }}
            </p>
            <h1 class="text-4xl font-black tracking-tight text-white sm:text-5xl">
              {{ stepMeta[step - 1].title }}
            </h1>
            <p class="mt-3 text-base text-gray-400">{{ stepMeta[step - 1].subtitle }}</p>
          </div>
        </Transition>

        <!-- Step content -->
        <Transition name="slide" mode="out-in">
          <div :key="`c-${step}`">

            <!-- STEP 1 — Background -->
            <div v-if="step === 1" class="space-y-6">
              <div class="rounded-2xl border border-gray-800 bg-gray-900/60 p-6 backdrop-blur-sm">
                <label class="mb-2 block text-sm font-semibold text-gray-300">Current Role <span class="text-emerald-500">*</span></label>
                <UInput
                  v-model="form.profession"
                  placeholder="e.g. Frontend Developer, DevOps Engineer…"
                  size="xl"
                  :ui="{ base: 'bg-gray-800 border-gray-700 text-white placeholder-gray-600 focus:border-emerald-500' }"
                />
              </div>

              <div>
                <label class="mb-3 block text-sm font-semibold text-gray-300">Experience Level <span class="text-emerald-500">*</span></label>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                  <button
                    v-for="opt in levelOptions"
                    :key="opt.value"
                    type="button"
                    :class="[
                      'group rounded-2xl border p-5 text-left transition-all duration-200',
                      form.level === opt.value
                        ? 'border-emerald-500 bg-emerald-950/50 ring-1 ring-emerald-500/30'
                        : 'border-gray-800 bg-gray-900/60 hover:border-gray-600 hover:bg-gray-800/80',
                    ]"
                    @click="form.level = opt.value"
                  >
                    <p class="text-sm font-bold" :class="form.level === opt.value ? 'text-emerald-400' : 'text-white'">{{ opt.label }}</p>
                    <p class="mt-1 text-xs text-gray-500">{{ opt.sub }}</p>
                  </button>
                </div>
              </div>
            </div>

            <!-- STEP 2 — Career Goal -->
            <div v-else-if="step === 2" class="space-y-6">
              <div class="rounded-2xl border border-gray-800 bg-gray-900/60 p-6 backdrop-blur-sm">
                <label class="mb-2 block text-sm font-semibold text-gray-300">What do you want to achieve? <span class="text-emerald-500">*</span></label>
                <UTextarea
                  v-model="form.goal"
                  placeholder="e.g. Land a senior full-stack role at a product company in Ireland within 12 months…"
                  :rows="4"
                  autoresize
                  maxlength="255"
                  :ui="{ base: 'bg-gray-800 border-gray-700 text-white placeholder-gray-600 focus:border-emerald-500' }"
                />
                <p class="mt-2 text-xs text-gray-600">Your consultant will use this to build your personal learning plan.</p>
              </div>

              <div>
                <label class="mb-3 block text-sm font-semibold text-gray-300">Target Timeline <span class="text-emerald-500">*</span></label>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                  <button
                    v-for="opt in timelineOptions"
                    :key="opt.value"
                    type="button"
                    :class="[
                      'rounded-2xl border p-5 text-left transition-all duration-200',
                      form.timeline === opt.value
                        ? 'border-emerald-500 bg-emerald-950/50 ring-1 ring-emerald-500/30'
                        : 'border-gray-800 bg-gray-900/60 hover:border-gray-600 hover:bg-gray-800/80',
                    ]"
                    @click="form.timeline = opt.value"
                  >
                    <p class="text-sm font-bold" :class="form.timeline === opt.value ? 'text-emerald-400' : 'text-white'">{{ opt.label }}</p>
                    <p class="mt-1 text-xs text-gray-500">{{ opt.sub }}</p>
                  </button>
                </div>
              </div>
            </div>

            <!-- STEP 3 — Stack & Availability -->
            <div v-else-if="step === 3" class="space-y-6">
              <div class="rounded-2xl border border-gray-800 bg-gray-900/60 p-6 backdrop-blur-sm">
                <label class="mb-1 block text-sm font-semibold text-gray-300">
                  Your Tech Stack <span class="text-emerald-500">*</span>
                </label>
                <p class="mb-4 text-xs text-gray-600">Select technologies you work with or want to master.</p>

                <div v-if="form.stack.length" class="mb-4 flex flex-wrap gap-2">
                  <span
                    v-for="tag in form.stack"
                    :key="tag"
                    class="flex items-center gap-1.5 rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-400 ring-1 ring-emerald-500/30"
                  >
                    {{ tag }}
                    <button type="button" class="leading-none text-emerald-600 hover:text-emerald-300" @click="removeTag(tag)">×</button>
                  </span>
                </div>

                <div class="flex flex-wrap gap-2">
                  <button
                    v-for="tag in popularTags"
                    :key="tag"
                    type="button"
                    :class="[
                      'rounded-full px-3 py-1.5 text-xs font-medium transition-all duration-150',
                      form.stack.includes(tag)
                        ? 'bg-emerald-500/20 text-emerald-400 ring-1 ring-emerald-500/40'
                        : 'bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-gray-200',
                    ]"
                    @click="toggleTag(tag)"
                  >
                    {{ tag }}
                  </button>
                </div>

                <div class="mt-4 flex gap-2">
                  <UInput
                    v-model="customTag"
                    placeholder="Add another…"
                    size="sm"
                    class="flex-1"
                    :ui="{ base: 'bg-gray-800 border-gray-700 text-white placeholder-gray-600 focus:border-emerald-500' }"
                    @keydown.enter.prevent="addCustomTag"
                  />
                  <UButton size="sm" color="gray" variant="outline" @click="addCustomTag">Add</UButton>
                </div>
              </div>

              <div>
                <label class="mb-3 block text-sm font-semibold text-gray-300">Weekly Availability <span class="text-emerald-500">*</span></label>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                  <button
                    v-for="opt in availabilityOptions"
                    :key="opt.value"
                    type="button"
                    :class="[
                      'rounded-2xl border p-5 text-left transition-all duration-200',
                      form.availability_hours === opt.value
                        ? 'border-emerald-500 bg-emerald-950/50 ring-1 ring-emerald-500/30'
                        : 'border-gray-800 bg-gray-900/60 hover:border-gray-600 hover:bg-gray-800/80',
                    ]"
                    @click="form.availability_hours = opt.value"
                  >
                    <p class="text-sm font-bold" :class="form.availability_hours === opt.value ? 'text-emerald-400' : 'text-white'">{{ opt.label }}</p>
                    <p class="mt-1 text-xs text-gray-500">{{ opt.sub }}</p>
                  </button>
                </div>
              </div>
            </div>

            <!-- STEP 4 — Learning Style (vocational) -->
            <div v-else-if="step === 4" class="space-y-4">
              <p class="text-sm text-gray-500">
                Every career journey is different. Choose the path that matches the way you grow best.
              </p>

              <div class="grid gap-4 sm:grid-cols-3">
                <button
                  v-for="opt in productOptions"
                  :key="opt.value"
                  type="button"
                  :class="[
                    'group relative overflow-hidden rounded-2xl border p-6 text-left transition-all duration-300',
                    form.product_interest === opt.value
                      ? 'border-emerald-500 bg-emerald-950/50 ring-1 ring-emerald-500/30'
                      : 'border-gray-800 bg-gray-900/60 hover:border-gray-600 hover:bg-gray-800/70',
                  ]"
                  @click="form.product_interest = opt.value"
                >
                  <!-- Glow on selected -->
                  <div
                    v-if="form.product_interest === opt.value"
                    class="pointer-events-none absolute -top-8 left-1/2 h-24 w-24 -translate-x-1/2 rounded-full bg-emerald-500/20 blur-2xl"
                  />

                  <div class="relative">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl" :class="opt.iconBg">
                      <UIcon :name="opt.icon" class="h-6 w-6" :class="opt.iconColor" />
                    </div>
                    <p class="font-bold text-white">{{ opt.label }}</p>
                    <p class="mt-2 text-xs leading-relaxed text-gray-500">{{ opt.description }}</p>
                    <div
                      v-if="form.product_interest === opt.value"
                      class="mt-4 flex items-center gap-1 text-xs font-semibold text-emerald-400"
                    >
                      <UIcon name="i-heroicons-check-circle-20-solid" class="h-4 w-4" />
                      Selected
                    </div>
                  </div>
                </button>
              </div>
            </div>

          </div>
        </Transition>

        <!-- Navigation -->
        <div class="mt-8 flex items-center justify-between">
          <UButton
            v-if="step > 1"
            color="gray"
            variant="ghost"
            icon="i-heroicons-arrow-left"
            class="text-gray-400 hover:text-white"
            @click="step--"
          >
            Back
          </UButton>
          <div v-else />

          <UButton
            v-if="step < TOTAL_STEPS"
            size="lg"
            :disabled="!stepValid"
            :class="[
              'rounded-xl px-8 font-semibold transition-all',
              stepValid
                ? 'bg-emerald-500 text-white hover:bg-emerald-400'
                : 'cursor-not-allowed bg-gray-800 text-gray-600',
            ]"
            @click="nextStep"
          >
            Continue
            <template #trailing>
              <UIcon name="i-heroicons-arrow-right" class="h-4 w-4" />
            </template>
          </UButton>

          <UButton
            v-else
            size="lg"
            :loading="loading"
            :disabled="!stepValid"
            :class="[
              'rounded-xl px-8 font-semibold transition-all',
              stepValid
                ? 'bg-emerald-500 text-white hover:bg-emerald-400'
                : 'cursor-not-allowed bg-gray-800 text-gray-600',
            ]"
            @click="submit"
          >
            Complete Assessment
          </UButton>
        </div>

      </div>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
import type { User } from '~/types/models'

definePageMeta({ layout: false, middleware: 'auth' })

const TOTAL_STEPS = 4

const { user } = useAuth()
const { patch } = useApi()

const step = ref(1)
const loading = ref(false)
const customTag = ref('')

const form = reactive({
  profession: '',
  level: '' as '' | 'junior' | 'mid' | 'senior' | 'manager',
  goal: '',
  timeline: '' as '' | '1-3m' | '3-6m' | '6-12m' | 'flexible',
  availability_hours: 0,
  stack: [] as string[],
  product_interest: '' as '' | 'self-serve' | 'bootcamp' | 'mentorship',
})

const stepMeta = [
  { stepLabel: 'Background',   title: 'Where are you today?',          subtitle: 'Tell us about your current role and level of experience.' },
  { stepLabel: 'Goal',         title: 'Where do you want to go?',      subtitle: 'Define your career goal and the timeline you\'re working with.' },
  { stepLabel: 'Stack',        title: 'What do you work with?',        subtitle: 'Select your technologies and how much time you can commit each week.' },
  { stepLabel: 'Style',        title: 'How do you grow best?',         subtitle: 'Choose the type of support that fits your learning style.' },
]

const levelOptions = [
  { value: 'junior',  label: 'Junior',    sub: '< 2 years' },
  { value: 'mid',     label: 'Mid-Level', sub: '2–5 years' },
  { value: 'senior',  label: 'Senior',    sub: '5+ years' },
  { value: 'manager', label: 'Manager',   sub: 'Tech lead / EM' },
]

const timelineOptions = [
  { value: '1-3m',     label: '1–3 months',  sub: 'Moving fast' },
  { value: '3-6m',     label: '3–6 months',  sub: 'Steady pace' },
  { value: '6-12m',    label: '6–12 months', sub: 'Thorough' },
  { value: 'flexible', label: 'Flexible',    sub: 'No rush' },
]

const availabilityOptions = [
  { value: 5,  label: '~5 h/week',  sub: 'Light commitment' },
  { value: 10, label: '~10 h/week', sub: 'Regular practice' },
  { value: 20, label: '~20 h/week', sub: 'Serious effort' },
  { value: 40, label: '40+ h/week', sub: 'Full focus' },
]

const productOptions = [
  {
    value: 'self-serve',
    label: 'Self-Directed',
    description: 'I learn best independently. Give me the tools, resources, and a clear roadmap — I\'ll take it from there.',
    icon: 'i-heroicons-book-open',
    iconBg: 'bg-blue-500/10',
    iconColor: 'text-blue-400',
  },
  {
    value: 'bootcamp',
    label: 'Structured Programme',
    description: 'I thrive with a defined curriculum, milestones, and clear goals to keep me on track.',
    icon: 'i-heroicons-academic-cap',
    iconBg: 'bg-emerald-500/10',
    iconColor: 'text-emerald-400',
  },
  {
    value: 'mentorship',
    label: 'Personal Mentorship',
    description: 'I want direct guidance from an expert who understands my goals and challenges me every step of the way.',
    icon: 'i-heroicons-users',
    iconBg: 'bg-amber-500/10',
    iconColor: 'text-amber-400',
  },
]

const popularTags = [
  'JavaScript', 'TypeScript', 'Python', 'PHP', 'Java', 'Go', 'C#', 'Ruby', 'Rust', 'Swift',
  'React', 'Vue', 'Angular', 'Next.js', 'Nuxt', 'Svelte',
  'Node.js', 'Laravel', 'Django', 'Spring Boot', 'Rails',
  'PostgreSQL', 'MySQL', 'MongoDB', 'Redis',
  'AWS', 'GCP', 'Azure', 'Docker', 'Kubernetes', 'Linux',
]

const stepValid = computed(() => {
  if (step.value === 1) return form.profession.trim().length > 0 && form.level !== ''
  if (step.value === 2) return form.goal.trim().length > 0 && form.timeline !== ''
  if (step.value === 3) return form.stack.length > 0 && form.availability_hours > 0
  if (step.value === 4) return form.product_interest !== ''
  return false
})

function toggleTag(tag: string) {
  const idx = form.stack.indexOf(tag)
  if (idx === -1) form.stack.push(tag)
  else form.stack.splice(idx, 1)
}

function removeTag(tag: string) {
  form.stack.splice(form.stack.indexOf(tag), 1)
}

function addCustomTag() {
  const t = customTag.value.trim()
  if (t && !form.stack.includes(t)) form.stack.push(t)
  customTag.value = ''
}

function nextStep() {
  if (stepValid.value) step.value++
}

async function submit() {
  if (!stepValid.value) return
  loading.value = true
  try {
    const res = await patch<{ message: string; user: User }>('/me/onboarding', {
      profession: form.profession,
      level: form.level,
      goal: form.goal,
      timeline: form.timeline,
      availability_hours: form.availability_hours,
      stack: form.stack,
      product_interest: form.product_interest,
    })
    if (user.value) Object.assign(user.value, res.user)
    await navigateTo('/dashboard')
  } finally {
    loading.value = false
  }
}

useHead({ title: 'Career Assessment — CODECV' })
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-enter-active,
.slide-leave-active {
  transition: all 0.25s ease;
}
.slide-enter-from {
  opacity: 0;
  transform: translateY(12px);
}
.slide-leave-to {
  opacity: 0;
  transform: translateY(-12px);
}
</style>
