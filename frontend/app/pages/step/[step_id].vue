<template>
  <NuxtLayout name="admin">

    <!-- Challenge: fixed full-screen overlay keeps NuxtLayout always mounted -->
    <div
      v-if="!pending && !error && step?.challenge"
      class="fixed inset-0 z-50"
    >
      <ChallengeEditor
        :challenge="(step.challenge as Challenge)"
        @back="navigateTo('/my-paths')"
        @completed="onChallengeCompleted"
      />
    </div>

    <div v-if="pending" class="flex justify-center py-20">
      <UIcon name="i-heroicons-arrow-path" class="animate-spin text-3xl text-emerald-500" />
    </div>

    <div v-else-if="locked" class="mx-auto max-w-md py-20">
      <LockedUpsell subtitle="This step is part of the Practice Pro library. Upgrade to continue this path — €29/month, cancel anytime." />
      <div class="mt-4 text-center">
        <UButton color="gray" variant="ghost" size="sm" @click="navigateTo('/my-paths')">Back to My Paths</UButton>
      </div>
    </div>

    <div v-else-if="error || !step" class="py-20 text-center">
      <p class="text-sm text-gray-500 dark:text-gray-400">Step not found.</p>
      <UButton class="mt-4" color="emerald" variant="ghost" @click="navigateTo('/my-paths')">Back to My Paths</UButton>
    </div>

    <template v-else-if="!step.challenge">
      <!-- ── Rich-content layout (concept_content present) ────────── -->
      <StepConceptView
        v-if="step.concept_content"
        :step="step"
        :saving="saving"
        @status="setStatus"
      >
        <template #breadcrumb>
          <UButton icon="i-heroicons-arrow-left" color="gray" variant="ghost" size="sm" @click="navigateTo('/my-paths')">
            My Paths
          </UButton>
          <span class="text-gray-300 dark:text-gray-600">/</span>
          <UIcon :name="stepTypeIcon(step.type)" class="h-4 w-4 text-gray-400" />
          <h1 class="truncate text-base font-semibold text-gray-900 dark:text-white">{{ step.title }}</h1>
        </template>
      </StepConceptView>

      <!-- ── Legacy layout (no concept_content yet) ────────────────── -->
      <template v-else>
        <div class="mb-6 flex items-center gap-3">
          <UButton icon="i-heroicons-arrow-left" color="gray" variant="ghost" size="sm" @click="navigateTo('/my-paths')">
            My Paths
          </UButton>
          <span class="text-gray-300 dark:text-gray-600">/</span>
          <UIcon :name="stepTypeIcon(step.type)" class="h-4 w-4 text-gray-400" />
          <h1 class="truncate text-base font-semibold text-gray-900 dark:text-white">{{ step.title }}</h1>
          <div class="ml-auto shrink-0">
            <UBadge :color="statusBadgeColor(step.user_status)" variant="subtle" size="sm">
              {{ statusLabel(step.user_status) }}
            </UBadge>
          </div>
        </div>

        <div v-if="step.type === 'challenge' || step.type === 'lab'" class="mx-auto max-w-3xl">
          <UCard>
            <div class="py-8 text-center">
              <UIcon name="i-heroicons-code-bracket" class="mx-auto mb-3 h-10 w-10 text-gray-300 dark:text-gray-600" />
              <p class="text-sm font-medium text-gray-700 dark:text-gray-300">No exercise linked to this step yet.</p>
              <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">A consultant will link a coding challenge soon.</p>
            </div>
          </UCard>
        </div>

        <div v-else-if="step.type === 'reading' || !step.type" class="flex gap-6">
          <div class="min-w-0 flex-1">
            <UCard>
              <MarkdownContent v-if="step.description" :content="step.description" />
              <p v-else class="text-sm italic text-gray-400 dark:text-gray-500">No content yet.</p>
            </UCard>
          </div>

          <div class="w-72 shrink-0 space-y-4">
            <UCard>
              <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Progress</p>
              <div class="flex flex-col gap-2">
                <button
                  v-for="s in statusOptions"
                  :key="s.value"
                  class="flex items-center gap-2 rounded-lg border px-3 py-2 text-xs font-medium transition-colors"
                  :class="step.user_status === s.value
                    ? `border-transparent ${s.activeClass}`
                    : 'border-gray-200 text-gray-500 hover:border-gray-300 dark:border-gray-600 dark:text-gray-400'"
                  :disabled="saving"
                  @click="setStatus(s.value)"
                >
                  <UIcon :name="s.icon" class="h-3.5 w-3.5" />
                  {{ s.label }}
                </button>
              </div>
            </UCard>

            <UCard v-if="step.resources?.length">
              <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Resources</p>
              <div class="flex flex-col gap-2">
                <a
                  v-for="r in step.resources"
                  :key="r.url"
                  :href="r.url"
                  target="_blank"
                  class="flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-medium
                         text-gray-600 transition-colors hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700
                         dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-emerald-700 dark:hover:text-emerald-400"
                >
                  <UIcon name="i-heroicons-arrow-top-right-on-square" class="h-3 w-3 shrink-0" />
                  {{ r.label }}
                </a>
              </div>
            </UCard>
          </div>
        </div>

        <div v-else-if="step.type === 'incident'" class="space-y-4">
          <IncidentRunner
            v-if="step.evidence"
            :step-id="step.id"
            :evidence="step.evidence"
            :questions="step.quiz ?? []"
            @passed="setStatus('done')"
          />
          <div v-else class="mx-auto max-w-2xl">
            <UCard>
              <div class="py-8 text-center">
                <UIcon name="i-heroicons-signal" class="mx-auto mb-3 h-10 w-10 text-gray-300 dark:text-gray-600" />
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">No incident telemetry attached yet.</p>
              </div>
            </UCard>
          </div>
        </div>

        <div v-else-if="step.type === 'quiz'" class="space-y-4">
          <div v-if="step.description" class="mx-auto max-w-2xl">
            <p class="text-sm leading-relaxed text-gray-700 dark:text-gray-300">{{ step.description }}</p>
          </div>
          <QuizRunner
            v-if="step.quiz?.length"
            :step-id="step.id"
            :questions="step.quiz"
            @passed="setStatus('done')"
          />
          <div v-else class="mx-auto max-w-2xl">
            <UCard>
              <div class="py-8 text-center">
                <UIcon name="i-heroicons-question-mark-circle" class="mx-auto mb-3 h-10 w-10 text-gray-300 dark:text-gray-600" />
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">No quiz questions added yet.</p>
              </div>
            </UCard>
          </div>
        </div>

        <div v-else class="mx-auto max-w-3xl">
          <UCard>
            <p v-if="step.description" class="text-sm leading-relaxed text-gray-700 dark:text-gray-300">{{ step.description }}</p>
            <p v-else class="text-sm italic text-gray-400 dark:text-gray-500">No content yet.</p>
          </UCard>
        </div>

        <div v-if="step.type !== 'reading' && step.type" class="mx-auto mt-6 max-w-3xl">
          <div class="flex flex-wrap gap-2 rounded-xl border border-gray-200 bg-white px-5 py-4 dark:border-gray-700 dark:bg-gray-900">
            <p class="mb-2 w-full text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Mark as</p>
            <button
              v-for="s in statusOptions"
              :key="s.value"
              class="flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-medium transition-colors"
              :class="step.user_status === s.value
                ? `border-transparent ${s.activeClass}`
                : 'border-gray-200 text-gray-500 hover:border-gray-300 dark:border-gray-600 dark:text-gray-400'"
              :disabled="saving"
              @click="setStatus(s.value)"
            >
              <UIcon :name="s.icon" class="h-3.5 w-3.5" />
              {{ s.label }}
            </button>
          </div>
        </div>
      </template>
    </template>

    <!-- Blocking modal -->
    <UModal v-model="showBlockModal">
      <UCard :ui="{ ring: '', divide: 'divide-y divide-gray-100 dark:divide-gray-700' }">
        <template #header>
          <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-950/60">
              <UIcon name="i-heroicons-lock-closed" class="h-5 w-5 text-amber-500" />
            </div>
            <p class="font-semibold text-gray-900 dark:text-white">Finish your current step first</p>
          </div>
        </template>

        <p class="py-1 text-sm leading-relaxed text-gray-600 dark:text-gray-400">
          You're still working on
          <strong class="text-gray-900 dark:text-white">"{{ blockingStepTitle }}"</strong>.
          Mark it as <strong>Done</strong> before starting a new step.
        </p>

        <template #footer>
          <div class="flex justify-end">
            <UButton color="emerald" @click="showBlockModal = false">Got it</UButton>
          </div>
        </template>
      </UCard>
    </UModal>

    <!-- Step-completion celebration: XP, streak, fresh badges, next-up CTA -->
    <CompletionCelebration
      v-if="celebration"
      :progress="celebration.progress"
      :next-step="celebration.nextStep"
      @close="celebration = null"
      @next="goToNextStep"
      @exit="celebration = null; navigateTo('/my-paths')"
    />

    <!-- Path-completed celebration + coaching nudge (F6) -->
    <UModal v-model="showCoachingModal">
      <UCard :ui="{ ring: '', divide: 'divide-y divide-gray-100 dark:divide-gray-700' }">
        <template #header>
          <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-950/60">
              <UIcon name="i-heroicons-trophy" class="h-5 w-5 text-emerald-500" />
            </div>
            <div>
              <p class="font-semibold text-gray-900 dark:text-white">Path complete! 🎉</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">You've earned the Path Completed badge.</p>
            </div>
          </div>
        </template>

        <CoachingNudge v-if="coaching" :recommendation="coaching" />

        <template #footer>
          <div class="flex justify-end">
            <UButton color="gray" variant="ghost" @click="showCoachingModal = false">Maybe later</UButton>
          </div>
        </template>
      </UCard>
    </UModal>

  </NuxtLayout>
</template>

<script setup lang="ts">
import type { Challenge } from '~/types/models'
import type { PathStep, StepProgressResponse } from '~/composables/usePaths'

// key: navigating celebration → next step remounts the page with fresh state.
definePageMeta({ layout: false, middleware: 'auth', key: route => route.fullPath })

const route = useRoute()
const toast = useToast()
const { fetchStep, updateStepProgress, fetchSteps } = usePaths()
const { recommendation: coaching, fetchRecommendation } = useCoaching()
const focus = useFocusMode()

const stepId = Number(route.params.step_id)

// Always restore the layout chrome when leaving the step page.
onBeforeUnmount(() => { focus.value = false })

const step = ref<PathStep | null>(null)
const pending = ref(true)
const error = ref(false)
const locked = ref(false)
const saving = ref(false)
const showBlockModal = ref(false)
const blockingStepTitle = ref('')
const showCoachingModal = ref(false)
const celebration = ref<{
  progress: NonNullable<StepProgressResponse['progress']>
  nextStep: PathStep | null
} | null>(null)

onMounted(async () => {
  try {
    step.value = await fetchStep(stepId)
    // Challenge steps use the full-screen editor overlay — hide layout chrome.
    focus.value = !!step.value?.challenge
  } catch (err: unknown) {
    // 403 from the F4 content gate → show an upsell, not a generic error
    if ((err as { response?: { status?: number } })?.response?.status === 403) {
      locked.value = true
    } else {
      error.value = true
    }
  } finally {
    pending.value = false
  }
})

async function setStatus(status: NonNullable<PathStep['user_status']>) {
  if (!step.value || saving.value) return
  saving.value = true
  const prev = step.value.user_status
  step.value = { ...step.value, user_status: status }
  try {
    const res = await updateStepProgress(stepId, status)
    // Highest-intent coaching moment: the user just finished the whole path.
    if (res?.path_completed) {
      await fetchRecommendation()
      if (coaching.value) showCoachingModal.value = true
    } else if (res?.progress && status === 'done') {
      // Fresh completion (progress is null on repeats) — celebrate and offer
      // the next step so the loop continues.
      celebration.value = { progress: res.progress, nextStep: await findNextStep() }
    }
  } catch (err: unknown) {
    step.value = { ...step.value!, user_status: prev }
    const data = (err as { data?: { blocking_step?: string } })?.data
    if (data?.blocking_step) {
      blockingStepTitle.value = data.blocking_step
      showBlockModal.value = true
    } else {
      toast.add({ title: 'Could not save progress', color: 'red' })
    }
  } finally {
    saving.value = false
  }
}

async function onChallengeCompleted() {
  await setStatus('done')
}

async function findNextStep(): Promise<PathStep | null> {
  if (!step.value) return null
  try {
    const steps = await fetchSteps(step.value.path_id)
    return steps
      .filter(s => s.order > step.value!.order && !s.locked)
      .sort((a, b) => a.order - b.order)[0] ?? null
  } catch {
    return null
  }
}

function goToNextStep() {
  const id = celebration.value?.nextStep?.id
  celebration.value = null
  if (id) navigateTo(`/step/${id}`)
}

function stepTypeIcon(type?: string) {
  if (type === 'lab') return 'i-heroicons-command-line'
  if (type === 'challenge') return 'i-heroicons-trophy'
  if (type === 'quiz') return 'i-heroicons-question-mark-circle'
  if (type === 'incident') return 'i-heroicons-signal'
  return 'i-heroicons-book-open'
}

function statusBadgeColor(status?: string) {
  if (status === 'done') return 'green'
  if (status === 'in_progress') return 'emerald'
  return 'gray'
}

function statusLabel(status?: string) {
  if (status === 'done') return 'Done'
  if (status === 'in_progress') return 'In Progress'
  return 'Not Started'
}

const statusOptions = [
  {
    value: 'not_started' as const,
    label: 'Not Started',
    icon: 'i-heroicons-minus-circle',
    activeClass: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
  },
  {
    value: 'in_progress' as const,
    label: 'In Progress',
    icon: 'i-heroicons-play-circle',
    activeClass: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
  },
  {
    value: 'done' as const,
    label: 'Done',
    icon: 'i-heroicons-check-circle',
    activeClass: 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
  },
]
</script>
