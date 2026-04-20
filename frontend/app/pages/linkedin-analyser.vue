<template>
  <NuxtLayout name="admin">

    <!-- Header -->
    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">LinkedIn Analyser</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
          Share your LinkedIn profile and career goal — AI will tell you exactly what to improve.
        </p>
      </div>
      <UBadge color="emerald" variant="subtle" size="sm" class="items-center gap-1.5">
        <UIcon name="i-heroicons-sparkles" class="h-3.5 w-3.5" />
        Powered by CODECV
      </UBadge>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

      <!-- ── Input panel ─────────────────────────────────── -->
      <div class="flex flex-col gap-5">

        <!-- LinkedIn Profile PDF -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
          <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Your LinkedIn Profile</h2>
            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
              On LinkedIn: <strong>Me → View Profile → More → Save to PDF</strong>
            </p>
          </div>
          <div class="p-5">
              <label
                for="li-upload"
                class="flex cursor-pointer flex-col items-center justify-center gap-3 rounded-lg border-2 border-dashed px-6 py-8 transition-colors"
                :class="linkedinPdf
                  ? 'border-green-300 bg-green-50 dark:border-green-700 dark:bg-green-950/20'
                  : 'border-gray-200 hover:border-emerald-300 dark:border-gray-600 dark:hover:border-emerald-600'"
              >
                <UIcon
                  :name="linkedinPdf ? 'i-heroicons-document-check' : 'i-heroicons-document-arrow-up'"
                  class="h-7 w-7"
                  :class="linkedinPdf ? 'text-green-500' : 'text-emerald-400'"
                />
                <div class="text-center">
                  <p v-if="linkedinPdf" class="text-sm font-semibold text-green-700 dark:text-green-400">
                    {{ linkedinPdf.name }}
                  </p>
                  <p v-else class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Drop PDF here or <span class="text-emerald-600 dark:text-emerald-400">browse</span>
                  </p>
                  <p class="mt-1 text-xs text-gray-400">PDF only · max 5 MB</p>
                </div>
                <input id="li-upload" type="file" accept=".pdf" class="hidden" @change="handlePdfChange" />
              </label>
              <button v-if="linkedinPdf"
                class="mt-3 flex w-full items-center justify-center gap-1.5 rounded-lg border border-gray-200
                       py-2 text-xs font-medium text-gray-500 hover:text-red-600 dark:border-gray-700"
                @click="linkedinPdf = null">
                <UIcon name="i-heroicons-x-mark" class="h-3.5 w-3.5" /> Remove
              </button>
          </div>
        </div>

        <!-- Career Goal -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
          <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Career Goal</h2>
            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Tell us what you're aiming for</p>
          </div>
          <div class="flex flex-col gap-4 p-5">

            <!-- Target role -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">
                Target role <span class="text-red-500">*</span>
              </label>
              <input
                v-model="targetRole"
                type="text"
                placeholder="e.g. Senior Laravel Engineer"
                maxlength="200"
                class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm
                       text-gray-700 placeholder-gray-400 transition-colors
                       focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-200
                       dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:placeholder-gray-600
                       dark:focus:border-emerald-500 dark:focus:bg-gray-800"
              />
            </div>

            <!-- Industry / location -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">
                Industry / location <span class="text-gray-400">(optional)</span>
              </label>
              <input
                v-model="industry"
                type="text"
                placeholder="e.g. Fintech in Dublin, Ireland"
                maxlength="100"
                class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm
                       text-gray-700 placeholder-gray-400 transition-colors
                       focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-200
                       dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:placeholder-gray-600
                       dark:focus:border-emerald-500 dark:focus:bg-gray-800"
              />
            </div>

            <!-- Years of experience -->
            <div>
              <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">
                Years of experience <span class="text-gray-400">(optional)</span>
              </label>
              <input
                v-model.number="yearsExp"
                type="number"
                min="0"
                max="50"
                placeholder="e.g. 5"
                class="w-32 rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm
                       text-gray-700 placeholder-gray-400 transition-colors
                       focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-200
                       dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:placeholder-gray-600
                       dark:focus:border-emerald-500 dark:focus:bg-gray-800"
              />
            </div>
          </div>
        </div>

        <!-- Analyse button -->
        <UButton
          size="lg"
          color="emerald"
          block
          :loading="analysing"
          :disabled="!canAnalyse || analysing"
          icon="i-heroicons-sparkles"
          @click="runAnalysis"
        >
          {{ analysing ? 'Analysing your profile…' : 'Analyse My LinkedIn' }}
        </UButton>

        <p v-if="!canAnalyse" class="text-center text-xs text-gray-400 dark:text-gray-600">
          Enter your LinkedIn URL and target role to continue.
        </p>

      </div>

      <!-- ── Results panel ───────────────────────────────── -->
      <div class="flex flex-col gap-5">

        <!-- Placeholder -->
        <div v-if="!result && !analysing"
          class="flex flex-col items-center justify-center rounded-xl border border-dashed border-gray-200
                 bg-white py-20 text-center dark:border-gray-700 dark:bg-gray-800">
          <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 dark:bg-emerald-950">
            <UIcon name="i-heroicons-identification" class="h-8 w-8 text-emerald-300" />
          </div>
          <p class="text-sm font-medium text-gray-400 dark:text-gray-500">Results will appear here</p>
          <p class="mt-1 text-xs text-gray-300 dark:text-gray-600">Fill in the form to get your analysis</p>
        </div>

        <!-- Skeleton loader -->
        <div v-else-if="analysing" class="flex flex-col gap-5">
          <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 p-6">
            <div class="flex flex-col items-center gap-4 py-6">
              <UIcon name="i-heroicons-sparkles" class="h-10 w-10 animate-pulse text-emerald-400" />
              <div class="text-center">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Analysing your profile…</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">CODECV is reading your LinkedIn and building your personalised report.</p>
              </div>
            </div>
            <div class="mt-2 flex flex-col gap-3">
              <div v-for="i in 5" :key="i" class="h-4 animate-pulse rounded-full bg-gray-100 dark:bg-gray-700"
                :style="`width: ${55 + i * 7}%`" />
            </div>
          </div>
        </div>

        <!-- Results -->
        <template v-else-if="result">

          <!-- Score card -->
          <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 p-6">
            <div class="flex items-center gap-6">
              <div class="relative flex h-24 w-24 shrink-0 items-center justify-center">
                <svg class="absolute inset-0 h-full w-full -rotate-90" viewBox="0 0 100 100">
                  <circle cx="50" cy="50" r="40" fill="none" stroke="currentColor"
                    class="text-gray-100 dark:text-gray-700" stroke-width="10" />
                  <circle cx="50" cy="50" r="40" fill="none"
                    :stroke="scoreColor" stroke-width="10" stroke-linecap="round"
                    :stroke-dasharray="`${2 * Math.PI * 40}`"
                    :stroke-dashoffset="`${2 * Math.PI * 40 * (1 - result.score / 100)}`"
                    style="transition: stroke-dashoffset 1s ease"
                  />
                </svg>
                <span class="text-2xl font-extrabold" :style="`color: ${scoreColor}`">
                  {{ result.score }}%
                </span>
              </div>
              <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Profile Score</p>
                <p class="mt-1 text-lg font-bold" :style="`color: ${scoreColor}`">{{ scoreLabel }}</p>
                <p class="mt-1 text-xs leading-relaxed text-gray-500 dark:text-gray-400 max-w-xs">
                  {{ result.overall }}
                </p>
              </div>
            </div>
          </div>

          <!-- Headline & Summary suggestions -->
          <div class="rounded-xl border border-emerald-100 bg-emerald-50 dark:border-emerald-900/40 dark:bg-emerald-950/20">
            <div class="border-b border-emerald-100 px-5 py-4 dark:border-emerald-900/40">
              <h3 class="flex items-center gap-2 text-sm font-semibold text-emerald-800 dark:text-emerald-300">
                <UIcon name="i-heroicons-pencil-square" class="h-4 w-4" />
                Suggested Headline
              </h3>
            </div>
            <p class="px-5 py-4 text-sm text-emerald-700 dark:text-emerald-300 italic">
              "{{ result.headline_suggestion }}"
            </p>
          </div>

          <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
              <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white">
                <UIcon name="i-heroicons-document-text" class="h-4 w-4 text-emerald-500" />
                Suggested About Section
              </h3>
            </div>
            <p class="px-5 py-4 text-sm leading-relaxed text-gray-600 dark:text-gray-400">
              {{ result.summary_suggestion }}
            </p>
          </div>

          <!-- Strengths & Gaps -->
          <div class="grid grid-cols-2 gap-4">
            <div class="rounded-xl border border-green-100 bg-green-50 p-4 dark:border-green-900/40 dark:bg-green-950/20">
              <p class="mb-3 flex items-center gap-1.5 text-xs font-semibold text-green-700 dark:text-green-400">
                <UIcon name="i-heroicons-check-circle" class="h-4 w-4" />
                Strengths
              </p>
              <ul class="flex flex-col gap-1.5">
                <li v-for="s in result.strengths" :key="s"
                  class="text-xs text-green-700 dark:text-green-300">
                  · {{ s }}
                </li>
              </ul>
            </div>
            <div class="rounded-xl border border-red-100 bg-red-50 p-4 dark:border-red-900/40 dark:bg-red-950/20">
              <p class="mb-3 flex items-center gap-1.5 text-xs font-semibold text-red-700 dark:text-red-400">
                <UIcon name="i-heroicons-exclamation-circle" class="h-4 w-4" />
                Gaps
              </p>
              <ul class="flex flex-col gap-1.5">
                <li v-for="g in result.gaps" :key="g"
                  class="text-xs text-red-700 dark:text-red-300">
                  · {{ g }}
                </li>
              </ul>
            </div>
          </div>

          <!-- Skills to add -->
          <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 p-5">
            <p class="mb-3 flex items-center gap-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300">
              <UIcon name="i-heroicons-plus-circle" class="h-4 w-4 text-emerald-500" />
              Skills / Certifications to Add
            </p>
            <div class="flex flex-wrap gap-1.5">
              <span v-for="skill in result.skills_to_add" :key="skill"
                class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700
                       dark:bg-emerald-900/40 dark:text-emerald-300">
                {{ skill }}
              </span>
            </div>
          </div>

          <!-- Recommendations -->
          <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
              <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white">
                <UIcon name="i-heroicons-light-bulb" class="h-4 w-4 text-emerald-500" />
                Action Plan
              </h3>
            </div>
            <ul class="divide-y divide-gray-100 dark:divide-gray-700">
              <li v-for="(rec, i) in result.recommendations" :key="rec"
                class="flex items-start gap-3 px-5 py-3 text-sm text-gray-700 dark:text-gray-300">
                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-50
                             text-[10px] font-bold text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400">
                  {{ i + 1 }}
                </span>
                {{ rec }}
              </li>
            </ul>
          </div>

          <!-- Re-analyse -->
          <UButton size="sm" color="gray" variant="outline" icon="i-heroicons-arrow-path" @click="result = null">
            Analyse Again
          </UButton>

        </template>
      </div>
    </div>

  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({ layout: false, middleware: 'auth' })
useHead({ title: 'LinkedIn Analyser — CODECV' })

const toast = useToast()
const { upload } = useApi()

const linkedinPdf = ref<File | null>(null)
const targetRole  = ref('')
const industry    = ref('')
const yearsExp    = ref<number | null>(null)
const analysing   = ref(false)

interface LinkedInResult {
  score: number
  headline_suggestion: string
  summary_suggestion: string
  strengths: string[]
  gaps: string[]
  skills_to_add: string[]
  recommendations: string[]
  overall: string
}
const result = ref<LinkedInResult | null>(null)

const canAnalyse = computed(() => !!linkedinPdf.value && targetRole.value.trim().length >= 5)

function handlePdfChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (!file) return
  if (file.size > 5 * 1024 * 1024) {
    toast.add({ title: 'File too large', description: 'Max 5 MB.', color: 'red' })
    return
  }
  linkedinPdf.value = file
}

async function runAnalysis() {
  if (!canAnalyse.value) return

  analysing.value = true
  result.value    = null

  try {
    let data: LinkedInResult

    const yearsNum = Number(yearsExp.value)
    const fd = new FormData()
    fd.append('linkedin_pdf', linkedinPdf.value!)
    fd.append('target_role', targetRole.value)
    if (industry.value) fd.append('industry', industry.value)
    if (Number.isFinite(yearsNum)) fd.append('years_exp', String(yearsNum))
    data = await upload<LinkedInResult>('/linkedin/analyze', fd)

    result.value = data
  } catch (err: any) {
    const msg = err?.data?.message || err?.message || 'Analysis failed. Please try again.'
    toast.add({ title: 'Error', description: msg, color: 'red' })
  } finally {
    analysing.value = false
  }
}

const scoreColor = computed(() => {
  const s = result.value?.score ?? 0
  if (s >= 75) return '#10b981'
  if (s >= 50) return '#f59e0b'
  return '#ef4444'
})

const scoreLabel = computed(() => {
  const s = result.value?.score ?? 0
  if (s >= 85) return 'Strong Profile'
  if (s >= 70) return 'Good Profile'
  if (s >= 50) return 'Needs Work'
  return 'Weak Alignment'
})
</script>
