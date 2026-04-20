<template>
  <NuxtLayout name="admin">

    <!-- Header -->
    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My CV Analyser</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
          Upload your CV and provide a job posting — AI will score the match and suggest improvements.
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

        <!-- CV Upload -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
          <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Your CV</h2>
            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Upload a PDF file (max 5 MB)</p>
          </div>
          <div class="p-5">
            <label
              for="cv-upload"
              class="flex cursor-pointer flex-col items-center justify-center gap-3 rounded-lg border-2 border-dashed
                     px-6 py-10 transition-colors"
              :class="dragOver
                ? 'border-emerald-400 bg-emerald-50 dark:border-emerald-500 dark:bg-emerald-950/30'
                : cvFile
                  ? 'border-green-300 bg-green-50 dark:border-green-700 dark:bg-green-950/20'
                  : 'border-gray-200 hover:border-emerald-300 dark:border-gray-600 dark:hover:border-emerald-600'"
              @dragover.prevent="dragOver = true"
              @dragleave.prevent="dragOver = false"
              @drop.prevent="handleDrop"
            >
              <div class="flex h-14 w-14 items-center justify-center rounded-xl"
                :class="cvFile ? 'bg-green-100 dark:bg-green-900/40' : 'bg-emerald-50 dark:bg-emerald-950'">
                <UIcon
                  :name="cvFile ? 'i-heroicons-document-check' : 'i-heroicons-document-arrow-up'"
                  class="h-7 w-7"
                  :class="cvFile ? 'text-green-500' : 'text-emerald-400'"
                />
              </div>
              <div class="text-center">
                <p v-if="cvFile" class="text-sm font-semibold text-green-700 dark:text-green-400">
                  {{ cvFile.name }}
                </p>
                <p v-else class="text-sm font-medium text-gray-700 dark:text-gray-300">
                  Drop your CV here or <span class="text-emerald-600 dark:text-emerald-400">browse</span>
                </p>
                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">PDF only · max 5 MB</p>
              </div>
              <input id="cv-upload" type="file" accept=".pdf" class="hidden" @change="handleFileChange" />
            </label>

            <button v-if="cvFile"
              class="mt-3 flex w-full items-center justify-center gap-1.5 rounded-lg border border-gray-200
                     py-2 text-xs font-medium text-gray-500 hover:text-red-600 dark:border-gray-700 dark:hover:text-red-400"
              @click="cvFile = null">
              <UIcon name="i-heroicons-x-mark" class="h-3.5 w-3.5" />
              Remove
            </button>
          </div>
        </div>

        <!-- Job Description -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
          <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Job Posting</h2>
            <!-- Tabs -->
            <div class="mt-3 flex gap-1 rounded-lg bg-gray-100 p-1 dark:bg-gray-700/50">
              <button
                v-for="t in inputTabs" :key="t.value"
                class="flex flex-1 items-center justify-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-medium transition-all"
                :class="jobInputMode === t.value
                  ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-700 dark:text-white'
                  : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'"
                @click="jobInputMode = t.value"
              >
                <UIcon :name="t.icon" class="h-3.5 w-3.5" />
                {{ t.label }}
              </button>
            </div>
          </div>

          <div class="p-5">
            <!-- URL mode -->
            <template v-if="jobInputMode === 'url'">
              <div class="flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3
                          focus-within:border-emerald-400 focus-within:bg-white focus-within:ring-2
                          focus-within:ring-emerald-200 dark:border-gray-700 dark:bg-gray-900
                          dark:focus-within:border-emerald-500 dark:focus-within:bg-gray-800">
                <UIcon name="i-heroicons-link" class="h-4 w-4 shrink-0 text-gray-400" />
                <input
                  v-model="jobUrl"
                  type="url"
                  placeholder="https://www.irishjobs.ie/jobs/..."
                  class="w-full bg-transparent py-3 text-sm text-gray-700 placeholder-gray-400
                         focus:outline-none dark:text-gray-300 dark:placeholder-gray-600"
                />
                <button v-if="jobUrl" @click="jobUrl = ''" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                  <UIcon name="i-heroicons-x-mark" class="h-4 w-4" />
                </button>
              </div>
              <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">
                Paste the job listing URL — works with most job boards (IrishJobs, Indeed, LinkedIn, etc.)
              </p>
            </template>

            <!-- Text mode -->
            <template v-else>
              <textarea
                v-model="jobDescription"
                rows="10"
                placeholder="Paste the job description here…&#10;&#10;E.g. &quot;We are looking for a Senior Software Engineer with 5+ years of experience in Laravel, Vue.js, and AWS…&quot;"
                class="w-full resize-none rounded-lg border border-gray-200 bg-gray-50 px-4 py-3
                       text-sm text-gray-700 placeholder-gray-400 transition-colors
                       focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-200
                       dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:placeholder-gray-600
                       dark:focus:border-emerald-500 dark:focus:bg-gray-800"
              />
              <p class="mt-1.5 text-right text-xs text-gray-400">
                {{ jobDescription.length }} / 5000 chars
              </p>
            </template>
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
          {{ analysing ? 'Analysing your CV…' : 'Analyse My CV' }}
        </UButton>

        <p v-if="!canAnalyse" class="text-center text-xs text-gray-400 dark:text-gray-600">
          {{ !cvFile ? 'Upload a PDF to continue.' : inputHint }}
        </p>

      </div>

      <!-- ── Results panel ───────────────────────────────── -->
      <div class="flex flex-col gap-5">

        <!-- Placeholder / empty -->
        <div v-if="!result && !analysing"
          class="flex flex-col items-center justify-center rounded-xl border border-dashed border-gray-200
                 bg-white py-20 text-center dark:border-gray-700 dark:bg-gray-800">
          <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 dark:bg-emerald-950">
            <UIcon name="i-heroicons-chart-bar-square" class="h-8 w-8 text-emerald-300" />
          </div>
          <p class="text-sm font-medium text-gray-400 dark:text-gray-500">Results will appear here</p>
          <p class="mt-1 text-xs text-gray-300 dark:text-gray-600">Upload your CV and add a job posting</p>
        </div>

        <!-- Skeleton loader -->
        <div v-else-if="analysing" class="flex flex-col gap-5">
          <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 p-6">
            <div class="flex flex-col items-center gap-4 py-6">
              <UIcon name="i-heroicons-sparkles" class="h-10 w-10 animate-pulse text-emerald-400" />
              <div class="text-center">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Analysing your CV…</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">CODECV is reading your CV and comparing it to the job. This takes ~15 seconds.</p>
              </div>
            </div>
            <div class="mt-2 flex flex-col gap-3">
              <div v-for="i in 4" :key="i" class="h-4 animate-pulse rounded-full bg-gray-100 dark:bg-gray-700"
                :style="`width: ${60 + i * 8}%`" />
            </div>
          </div>
        </div>

        <!-- Actual results -->
        <template v-else-if="result">

          <!-- Score card -->
          <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 p-6">
            <div class="flex items-center gap-6">
              <div class="relative flex h-24 w-24 shrink-0 items-center justify-center">
                <svg class="absolute inset-0 h-full w-full -rotate-90" viewBox="0 0 100 100">
                  <circle cx="50" cy="50" r="40" fill="none" stroke="currentColor"
                    class="text-gray-100 dark:text-gray-700" stroke-width="10" />
                  <circle cx="50" cy="50" r="40" fill="none"
                    :stroke="scoreColor"
                    stroke-width="10"
                    stroke-linecap="round"
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
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Match Score</p>
                <p class="mt-1 text-lg font-bold" :style="`color: ${scoreColor}`">{{ scoreLabel }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 leading-relaxed max-w-xs">
                  {{ result.summary }}
                </p>
              </div>
            </div>
          </div>

          <!-- Keywords -->
          <div class="grid grid-cols-2 gap-4">
            <div class="rounded-xl border border-green-100 bg-green-50 p-4 dark:border-green-900/40 dark:bg-green-950/20">
              <p class="mb-3 flex items-center gap-1.5 text-xs font-semibold text-green-700 dark:text-green-400">
                <UIcon name="i-heroicons-check-circle" class="h-4 w-4" />
                Matched ({{ result.matched_keywords?.length ?? 0 }})
              </p>
              <div class="flex flex-wrap gap-1.5">
                <span v-for="kw in result.matched_keywords" :key="kw"
                  class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700
                         dark:bg-green-900/40 dark:text-green-300">
                  {{ kw }}
                </span>
                <span v-if="!result.matched_keywords?.length" class="text-xs text-gray-400">None found</span>
              </div>
            </div>

            <div class="rounded-xl border border-red-100 bg-red-50 p-4 dark:border-red-900/40 dark:bg-red-950/20">
              <p class="mb-3 flex items-center gap-1.5 text-xs font-semibold text-red-700 dark:text-red-400">
                <UIcon name="i-heroicons-x-circle" class="h-4 w-4" />
                Missing ({{ result.missing_keywords?.length ?? 0 }})
              </p>
              <div class="flex flex-wrap gap-1.5">
                <span v-for="kw in result.missing_keywords" :key="kw"
                  class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700
                         dark:bg-red-900/40 dark:text-red-300">
                  {{ kw }}
                </span>
                <span v-if="!result.missing_keywords?.length" class="text-xs text-gray-400">None missing</span>
              </div>
            </div>
          </div>

          <!-- Strengths -->
          <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
              <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white">
                <UIcon name="i-heroicons-trophy" class="h-4 w-4 text-amber-500" />
                Your Strengths
              </h3>
            </div>
            <ul class="divide-y divide-gray-100 dark:divide-gray-700">
              <li v-for="s in result.strengths" :key="s"
                class="flex items-start gap-3 px-5 py-3 text-sm text-gray-700 dark:text-gray-300">
                <UIcon name="i-heroicons-check-circle" class="mt-0.5 h-4 w-4 shrink-0 text-green-500" />
                {{ s }}
              </li>
            </ul>
          </div>

          <!-- Improvements -->
          <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
              <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white">
                <UIcon name="i-heroicons-light-bulb" class="h-4 w-4 text-emerald-500" />
                How to Improve Your CV
              </h3>
            </div>
            <ul class="divide-y divide-gray-100 dark:divide-gray-700">
              <li v-for="(tip, i) in result.improvements" :key="tip"
                class="flex items-start gap-3 px-5 py-3 text-sm text-gray-700 dark:text-gray-300">
                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-50
                             text-[10px] font-bold text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400">
                  {{ i + 1 }}
                </span>
                {{ tip }}
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
useHead({ title: 'My CV — CODECV' })

const toast = useToast()
const { upload } = useApi()

const cvFile         = ref<File | null>(null)
const jobInputMode   = ref<'url' | 'text'>('url')
const jobUrl         = ref('')
const jobDescription = ref('')
const dragOver       = ref(false)
const analysing      = ref(false)

const inputTabs = [
  { value: 'url' as const,  label: 'Job URL',  icon: 'i-heroicons-link' },
  { value: 'text' as const, label: 'Paste Text', icon: 'i-heroicons-document-text' },
]

interface CvResult {
  score: number
  matched_keywords: string[]
  missing_keywords: string[]
  strengths: string[]
  improvements: string[]
  summary: string
}
const result = ref<CvResult | null>(null)

const jobReady = computed(() =>
  jobInputMode.value === 'url'
    ? jobUrl.value.startsWith('http')
    : jobDescription.value.length >= 50
)

const canAnalyse = computed(() => !!cvFile.value && jobReady.value)

const inputHint = computed(() =>
  jobInputMode.value === 'url'
    ? 'Paste a valid job URL to continue.'
    : 'Add at least 50 characters of job description to continue.'
)

function handleFileChange(e: Event) {
  const input = e.target as HTMLInputElement
  const file = input.files?.[0]
  if (file) setFile(file)
}

function handleDrop(e: DragEvent) {
  dragOver.value = false
  const file = e.dataTransfer?.files?.[0]
  if (file?.type === 'application/pdf') {
    setFile(file)
  } else {
    toast.add({ title: 'PDF only', description: 'Please drop a PDF file.', color: 'red' })
  }
}

function setFile(file: File) {
  if (file.size > 5 * 1024 * 1024) {
    toast.add({ title: 'File too large', description: 'Max file size is 5 MB.', color: 'red' })
    return
  }
  cvFile.value = file
}

async function runAnalysis() {
  if (!canAnalyse.value) return

  analysing.value = true
  result.value    = null

  try {
    const fd = new FormData()
    fd.append('cv', cvFile.value!)

    if (jobInputMode.value === 'url') {
      fd.append('job_url', jobUrl.value)
    } else {
      fd.append('job_description', jobDescription.value)
    }

    const data = await upload<CvResult>('/cv/analyze', fd)
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
  if (s >= 85) return 'Excellent Match'
  if (s >= 70) return 'Good Match'
  if (s >= 50) return 'Partial Match'
  return 'Low Match'
})
</script>
