<template>
  <NuxtLayout name="admin">

    <!-- Header -->
    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Learning Paths</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
          Structured roadmaps to guide your IT career in Ireland.
        </p>
      </div>
      <div class="flex rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <button
          class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium transition-colors"
          :class="view === 'timeline' ? 'bg-teal-600 text-white' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800'"
          @click="view = 'timeline'"
        >
          <UIcon name="i-heroicons-bars-3-bottom-left" class="h-3.5 w-3.5" />
          Timeline
        </button>
        <button
          class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium transition-colors"
          :class="view === 'roadmap' ? 'bg-teal-600 text-white' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800'"
          @click="view = 'roadmap'"
        >
          <UIcon name="i-heroicons-map" class="h-3.5 w-3.5" />
          Roadmap
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-20">
      <UIcon name="i-heroicons-arrow-path" class="animate-spin text-3xl text-teal-500" />
    </div>

    <template v-else>

      <!-- Empty -->
      <div v-if="!paths.length" class="flex flex-col items-center py-20 text-center">
        <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-teal-50 dark:bg-teal-950">
          <UIcon name="i-heroicons-map" class="h-10 w-10 text-teal-400" />
        </div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">No learning paths yet</h3>
        <p class="mt-2 max-w-sm text-sm text-gray-500 dark:text-gray-400">
          Your consultant will assign a personalised learning path based on your goals and target role.
        </p>
        <a href="https://wa.me/353894050730?text=Hi%2C+I%27d+like+to+get+a+learning+path+assigned+to+my+profile."
          target="_blank"
          class="mt-5 inline-flex items-center gap-2 rounded-lg bg-teal-600 px-4 py-2.5
                 text-sm font-semibold text-white transition-opacity hover:opacity-90">
          <UIcon name="i-heroicons-chat-bubble-left-ellipsis" class="h-4 w-4" />
          Request a Learning Path
        </a>
      </div>

      <!-- Paths -->
      <div v-else class="flex flex-col gap-8">
        <div v-for="path in enrichedPaths" :key="path.id">

          <!-- Path header card -->
          <div class="mb-4 overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div class="flex flex-wrap items-start gap-4 p-5">
              <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-950">
                <UIcon name="i-heroicons-map" class="h-6 w-6 text-emerald-500" />
              </div>
              <div class="min-w-0 flex-1">
                <h2 class="text-base font-bold text-gray-900 dark:text-white">{{ path.name }}</h2>
                <p v-if="path.description" class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                  {{ path.description }}
                </p>
                <!-- This path includes -->
                <div class="mt-3 grid grid-cols-2 gap-x-6 gap-y-1.5 sm:grid-cols-3">
                  <div v-for="item in pathIncludes(path)" :key="item.label"
                    class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                    <UIcon :name="item.icon" class="h-3.5 w-3.5 shrink-0 text-gray-400 dark:text-gray-500" />
                    {{ item.label }}
                  </div>
                </div>
              </div>
              <!-- Progress -->
              <div class="flex shrink-0 flex-col items-end gap-1.5">
                <span class="text-lg font-black" :class="progressColor(path.progressPct)">
                  {{ path.progressPct }}%
                </span>
                <UProgress :value="path.progressPct" size="sm" color="emerald" class="w-28" />
                <span class="text-xs text-gray-400">{{ path.doneCount }} / {{ path.steps.length }} done</span>
              </div>
            </div>
          </div>

          <!-- Roadmap view -->
          <RoadmapFlow
            v-if="view === 'roadmap' && path.steps.length"
            :steps="path.steps"
            @node-click="(step) => openStepModal(step, path)"
          />

          <!-- Course content (Udemy-style accordion) -->
          <div v-if="view === 'timeline' && path.steps.length">

            <!-- Summary bar -->
            <p class="mb-3 text-xs text-gray-500 dark:text-gray-400">
              {{ path.steps.length }} steps
              <span class="mx-1">·</span>
              {{ path.doneCount }} completed
              <span v-if="resourceTotal(path) > 0">
                <span class="mx-1">·</span>{{ resourceTotal(path) }} resources
              </span>
            </p>

            <!-- Accordion -->
            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
              <div v-for="(step, i) in path.steps" :key="step.id"
                class="border-b border-gray-100 last:border-b-0 dark:border-gray-700/60">

                <!-- Row header -->
                <button
                  class="flex w-full items-center gap-3 px-4 py-3.5 text-left transition-colors
                         hover:bg-gray-50 dark:hover:bg-gray-800/50"
                  :class="expanded.has(step.id) ? 'bg-gray-50 dark:bg-gray-800/40' : 'bg-white dark:bg-gray-900'"
                  @click="toggleExpanded(step.id)"
                >
                  <!-- Done/number indicator -->
                  <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[11px] font-bold"
                    :class="step.user_status === 'done'
                      ? 'bg-emerald-500 text-white'
                      : step.user_status === 'in_progress'
                        ? 'bg-blue-500 text-white'
                        : 'border-2 border-gray-300 text-gray-400 dark:border-gray-600'">
                    <UIcon v-if="step.user_status === 'done'" name="i-heroicons-check" class="h-3 w-3" />
                    <span v-else>{{ i + 1 }}</span>
                  </div>

                  <!-- Type icon -->
                  <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-gray-100 dark:bg-gray-800">
                    <UIcon :name="stepTypeIcon(step.type)" class="h-3.5 w-3.5 text-gray-500 dark:text-gray-400" />
                  </div>

                  <!-- Title + sub-label -->
                  <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium leading-snug"
                      :class="step.user_status === 'done'
                        ? 'text-gray-400 line-through dark:text-gray-500'
                        : 'text-gray-900 dark:text-white'">
                      {{ step.title }}
                    </p>
                    <p class="mt-0.5 text-[11px] text-gray-400 dark:text-gray-500">
                      {{ stepTypeLabel(step.type) }}
                      <span v-if="step.resources?.length"> · {{ step.resources.length }} resource{{ step.resources.length !== 1 ? 's' : '' }}</span>
                    </p>
                  </div>

                  <!-- Status badge -->
                  <UBadge :color="statusBadgeColor(step.user_status)" variant="subtle" size="xs" class="shrink-0">
                    {{ statusLabel(step.user_status) }}
                  </UBadge>

                  <UIcon :name="expanded.has(step.id) ? 'i-heroicons-chevron-up' : 'i-heroicons-chevron-down'"
                    class="h-4 w-4 shrink-0 text-gray-400" />
                </button>

                <!-- Expanded drawer -->
                <Transition
                  enter-from-class="opacity-0 -translate-y-1" enter-active-class="transition duration-150"
                  leave-to-class="opacity-0 -translate-y-1"   leave-active-class="transition duration-100">
                  <div v-if="expanded.has(step.id)"
                    class="border-t border-gray-100 bg-gray-50/60 px-5 py-4 dark:border-gray-700/60 dark:bg-gray-800/30">

                    <div class="space-y-4">

                      <!-- Overview -->
                      <div v-if="step.description">
                        <p class="mb-1 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Overview</p>
                        <p class="text-sm leading-relaxed text-gray-700 dark:text-gray-300">{{ step.description }}</p>
                      </div>

                      <!-- What you'll learn -->
                      <div v-if="step.instructions?.length">
                        <p class="mb-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">What you'll learn</p>
                        <ul class="space-y-1.5">
                          <li v-for="ins in step.instructions" :key="ins.id"
                            class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <UIcon name="i-heroicons-check-circle" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-500" />
                            {{ ins.text }}
                          </li>
                        </ul>
                      </div>

                      <!-- Challenge prompt -->
                      <div v-if="step.challenge_prompt">
                        <p class="mb-1 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Challenge</p>
                        <blockquote class="border-l-2 border-emerald-400 pl-3 text-sm italic leading-relaxed text-gray-600 dark:text-gray-400">
                          {{ step.challenge_prompt }}
                        </blockquote>
                      </div>

                      <!-- Linked course -->
                      <div v-if="step.course">
                        <button
                          class="flex w-full items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-left
                                 transition-colors hover:bg-emerald-100 dark:border-emerald-900/40 dark:bg-emerald-950/30 dark:hover:bg-emerald-950/50"
                          @click.stop="navigateTo(`/courses/${step.course!.id}`)">
                          <UIcon name="i-heroicons-book-open" class="h-5 w-5 shrink-0 text-emerald-600 dark:text-emerald-400" />
                          <div>
                            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">{{ step.course.name }}</p>
                            <p class="text-xs text-emerald-600/70 dark:text-emerald-400/60">Open course material →</p>
                          </div>
                        </button>
                      </div>

                      <!-- Lab / Challenge CTA -->
                      <div v-if="step.type === 'lab' || step.type === 'challenge'">
                        <UButton size="sm" color="emerald" variant="solid" :icon="stepTypeIcon(step.type)"
                          @click.stop="navigateTo(`/labs/${step.id}`)">
                          {{ step.type === 'lab' ? 'Open Lab Environment' : 'Start Challenge' }}
                        </UButton>
                      </div>

                      <!-- Resources -->
                      <div v-if="step.resources?.length">
                        <p class="mb-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Resources</p>
                        <div class="flex flex-wrap gap-2">
                          <a v-for="r in step.resources" :key="r.url"
                            :href="r.url" target="_blank"
                            class="flex items-center gap-1.5 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium
                                   text-gray-600 transition-colors hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700
                                   dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-emerald-700 dark:hover:text-emerald-400"
                            @click.stop>
                            <UIcon name="i-heroicons-arrow-top-right-on-square" class="h-3 w-3" />
                            {{ r.label }}
                          </a>
                        </div>
                      </div>

                      <!-- Status controls -->
                      <div class="flex flex-wrap gap-2 border-t border-gray-100 pt-3 dark:border-gray-700/60">
                        <button v-for="s in statusOptions" :key="s.value"
                          class="flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-medium transition-colors"
                          :class="step.user_status === s.value
                            ? `border-transparent ${s.activeClass}`
                            : 'border-gray-200 text-gray-500 hover:border-gray-300 dark:border-gray-600 dark:text-gray-400'"
                          @click.stop="setStatus(path, step, s.value)">
                          <UIcon :name="s.icon" class="h-3.5 w-3.5" />
                          {{ s.label }}
                        </button>
                      </div>

                    </div>
                  </div>
                </Transition>

              </div>
            </div>
          </div>

          <div v-else-if="!path.steps.length" class="rounded-xl border border-dashed border-gray-200 px-5 py-8 text-center dark:border-gray-700">
            <p class="text-sm text-gray-400 dark:text-gray-500">Your consultant hasn't added steps to this path yet.</p>
          </div>

        </div>
      </div>

    </template>
  <!-- Blocking modal: step order enforcement -->
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
        <strong class="text-gray-900 dark:text-white">"{{ blockedByStep?.title }}"</strong>.
        Mark it as <strong>Done</strong> before starting a new step — this keeps your focus sharp and your roadmap clean.
      </p>

      <template #footer>
        <div class="flex justify-end">
          <UButton color="emerald" @click="showBlockModal = false">Got it</UButton>
        </div>
      </template>
    </UCard>
  </UModal>

  <!-- Step detail modal (roadmap click) -->
  <UModal v-model="showStepModal" :ui="{ width: 'sm:max-w-xl' }">
    <UCard v-if="modalStep" :ui="{ ring: '', divide: 'divide-y divide-gray-100 dark:divide-gray-700', body: { padding: 'p-0' } }">

      <!-- Coloured header band -->
      <template #header>
        <div class="flex flex-wrap items-start gap-3">
          <!-- Step number / status dot -->
          <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold"
            :class="modalStep.user_status === 'done'
              ? 'bg-emerald-600 text-white'
              : modalStep.user_status === 'in_progress'
                ? 'bg-blue-500 text-white'
                : 'border-2 border-gray-300 bg-white text-gray-500 dark:border-gray-600 dark:bg-gray-800'">
            <UIcon v-if="modalStep.user_status === 'done'" name="i-heroicons-check" class="h-4 w-4" />
            <span v-else>{{ modalStep.order }}</span>
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-base font-bold text-gray-900 dark:text-white leading-tight">{{ modalStep.title }}</p>
            <!-- type pill -->
            <div class="mt-1 flex items-center gap-1.5">
              <UIcon :name="stepTypeIcon(modalStep.type)" class="h-3.5 w-3.5 text-emerald-500" />
              <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400 capitalize">{{ stepTypeLabel(modalStep.type) }}</span>
            </div>
          </div>
          <UBadge :color="statusBadgeColor(modalStep.user_status)" variant="subtle" size="sm">
            {{ statusLabel(modalStep.user_status) }}
          </UBadge>
        </div>
      </template>

      <!-- Body -->
      <div class="space-y-5 px-5 py-4">

        <!-- Description -->
        <div v-if="modalStep.description">
          <p class="mb-1.5 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Overview</p>
          <p class="text-sm leading-relaxed text-gray-700 dark:text-gray-300">{{ modalStep.description }}</p>
        </div>

        <!-- What you'll learn (instructions) -->
        <div v-if="modalStep.instructions?.length">
          <p class="mb-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">What you'll learn</p>
          <ul class="space-y-2">
            <li v-for="ins in modalStep.instructions" :key="ins.id"
              class="flex items-start gap-2.5 text-sm text-gray-700 dark:text-gray-300">
              <UIcon name="i-heroicons-check-circle" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-500" />
              {{ ins.text }}
            </li>
          </ul>
        </div>

        <!-- Challenge prompt -->
        <div v-if="modalStep.challenge_prompt">
          <p class="mb-1.5 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Challenge</p>
          <blockquote class="border-l-2 border-emerald-400 pl-3 text-sm italic leading-relaxed text-gray-600 dark:text-gray-400">
            {{ modalStep.challenge_prompt }}
          </blockquote>
        </div>

        <!-- Linked course -->
        <div v-if="modalStep.course">
          <p class="mb-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Linked Course</p>
          <button
            class="flex w-full items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3
                   text-left transition-colors hover:bg-emerald-100 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:hover:bg-emerald-950/50"
            @click="navigateTo(`/courses/${modalStep.course!.id}`)">
            <UIcon name="i-heroicons-book-open" class="h-5 w-5 shrink-0 text-emerald-600 dark:text-emerald-400" />
            <div class="min-w-0 flex-1">
              <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">{{ modalStep.course.name }}</p>
              <p class="text-xs text-emerald-600/70 dark:text-emerald-400/70">Open course material →</p>
            </div>
          </button>
        </div>

        <!-- Lab / Challenge CTA -->
        <div v-if="modalStep.type === 'lab' || modalStep.type === 'challenge'">
          <UButton size="sm" color="emerald" variant="solid" :icon="stepTypeIcon(modalStep.type)"
            @click="navigateTo(`/labs/${modalStep.id}`)">
            {{ modalStep.type === 'lab' ? 'Open Lab Environment' : 'Start Challenge' }}
          </UButton>
        </div>

        <!-- Resources -->
        <div v-if="modalStep.resources?.length">
          <p class="mb-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Resources</p>
          <div class="flex flex-wrap gap-2">
            <a v-for="r in modalStep.resources" :key="r.url"
              :href="r.url" target="_blank"
              class="flex items-center gap-1.5 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium
                     text-gray-600 transition-colors hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700
                     dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-emerald-700 dark:hover:text-emerald-400">
              <UIcon name="i-heroicons-arrow-top-right-on-square" class="h-3 w-3" />
              {{ r.label }}
            </a>
          </div>
        </div>

      </div>

      <template #footer>
        <!-- Status controls -->
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div class="flex flex-wrap gap-2">
            <button v-for="s in statusOptions" :key="s.value"
              class="flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-medium transition-colors"
              :class="modalStep.user_status === s.value
                ? `border-transparent ${s.activeClass}`
                : 'border-gray-200 text-gray-500 hover:border-gray-300 dark:border-gray-600 dark:text-gray-400'"
              @click="setStatusModal(s.value)">
              <UIcon :name="s.icon" class="h-3.5 w-3.5" />
              {{ s.label }}
            </button>
          </div>
          <UButton color="gray" variant="ghost" size="sm" @click="showStepModal = false">Close</UButton>
        </div>
      </template>
    </UCard>
  </UModal>

  </NuxtLayout>
</template>

<script setup lang="ts">
import type { PathStep } from '~/composables/usePaths'

definePageMeta({ layout: false, middleware: 'auth' })
useHead({ title: 'My Learning Paths — CODECV' })

const toast = useToast()
const { paths, loading, fetchMyPaths, fetchSteps, updateStepProgress } = usePaths()
const view = ref<'timeline' | 'roadmap'>('timeline')

// local step state per path: pathId → steps with user_status
const pathSteps = ref<Record<number, PathStep[]>>({})
const expanded  = ref(new Set<number>())
const saving    = ref(new Set<number>())

// Step order blocking modal
const showBlockModal  = ref(false)
const blockedByStep   = ref<PathStep | null>(null)

// Step detail modal (roadmap view)
const showStepModal = ref(false)
const modalStep     = ref<PathStep | null>(null)
const modalPath     = ref<any | null>(null)

function openStepModal(step: any, path: any) {
  modalStep.value = step
  modalPath.value = path
  showStepModal.value = true
}

async function setStatusModal(status: PathStep['user_status']) {
  if (!modalPath.value || !modalStep.value) return
  const step = modalStep.value
  await setStatus(modalPath.value, step, status)
  // sync modal display
  const updated = pathSteps.value[modalPath.value.id]?.find(s => s.id === step.id)
  if (updated) modalStep.value = updated
}

onMounted(async () => {
  const all = await fetchMyPaths()
  await Promise.all(all.map(async (p) => {
    const steps = await fetchSteps(p.id)
    pathSteps.value[p.id] = steps
  }))
})

const enrichedPaths = computed(() =>
  paths.value.map(p => {
    const steps = pathSteps.value[p.id] ?? []
    const doneCount = steps.filter(s => s.user_status === 'done').length
    const progressPct = steps.length ? Math.round((doneCount / steps.length) * 100) : 0
    return { ...p, steps, doneCount, progressPct }
  })
)

function toggleExpanded(id: number) {
  if (expanded.value.has(id)) {
    expanded.value.delete(id)
  } else {
    expanded.value.add(id)
  }
}

// Click node = toggle done / not_started
const cycle: Record<string, PathStep['user_status']> = {
  not_started: 'done',
  in_progress: 'done',
  done: 'not_started',
}

async function cycleStatus(path: any, step: PathStep) {
  const next = cycle[step.user_status ?? 'not_started'] ?? 'done'
  await setStatus(path, step, next)
}

async function setStatus(path: any, step: PathStep, status: PathStep['user_status']) {
  if (status === 'in_progress') {
    const arr = pathSteps.value[path.id] ?? []
    const blocker = arr.find(s => s.order < step.order && s.user_status === 'in_progress')
    if (blocker) {
      blockedByStep.value = blocker
      showBlockModal.value = true
      return
    }
  }

  if (saving.value.has(step.id)) return
  saving.value.add(step.id)

  const arr = pathSteps.value[path.id]

  // snapshot for rollback
  const snapshot = arr ? arr.map(s => ({ ...s })) : []

  // optimistic: demote any other in_progress sibling
  if (status === 'in_progress' && arr) {
    arr.forEach((s, i) => {
      if (s.id !== step.id && s.user_status === 'in_progress') arr[i] = { ...s, user_status: 'not_started' }
    })
  }
  // optimistic: update this step
  if (arr) {
    const idx = arr.findIndex(s => s.id === step.id)
    if (idx !== -1) arr[idx] = { ...arr[idx], user_status: status }
  }

  try {
    await updateStepProgress(step.id, status)
  } catch {
    // rollback all changes
    if (arr) pathSteps.value[path.id] = snapshot
    toast.add({ title: 'Could not save progress', color: 'red' })
  } finally {
    saving.value.delete(step.id)
  }
}

// ── Styling helpers ─────────────────────────────────────

function statusBadgeColor(status: PathStep['user_status']) {
  if (status === 'done') return 'green'
  if (status === 'in_progress') return 'teal'
  return 'gray'
}

function statusLabel(status: PathStep['user_status']) {
  if (status === 'done') return 'Done'
  if (status === 'in_progress') return 'In Progress'
  return 'Not Started'
}

function progressColor(pct: number) {
  if (pct >= 75) return 'text-green-600 dark:text-green-400'
  if (pct >= 40) return 'text-teal-600 dark:text-teal-400'
  return 'text-gray-500 dark:text-gray-400'
}

function pathIncludes(path: any) {
  const steps: any[] = path.steps ?? []
  const items = []
  const reading   = steps.filter(s => !s.type || s.type === 'reading').length
  const labs      = steps.filter(s => s.type === 'lab').length
  const challenges = steps.filter(s => s.type === 'challenge').length
  const quizzes   = steps.filter(s => s.type === 'quiz').length
  const resources = steps.reduce((n: number, s: any) => n + (s.resources?.length ?? 0), 0)
  if (reading)    items.push({ icon: 'i-heroicons-book-open',      label: `${reading} reading step${reading !== 1 ? 's' : ''}` })
  if (labs)       items.push({ icon: 'i-heroicons-command-line',   label: `${labs} hands-on lab${labs !== 1 ? 's' : ''}` })
  if (challenges) items.push({ icon: 'i-heroicons-trophy',         label: `${challenges} challenge${challenges !== 1 ? 's' : ''}` })
  if (quizzes)    items.push({ icon: 'i-heroicons-question-mark-circle', label: `${quizzes} quiz${quizzes !== 1 ? 'zes' : ''}` })
  if (resources)  items.push({ icon: 'i-heroicons-link',           label: `${resources} resource${resources !== 1 ? 's' : ''}` })
  return items
}

function resourceTotal(path: any) {
  return (path.steps ?? []).reduce((n: number, s: any) => n + (s.resources?.length ?? 0), 0)
}

function stepTypeIcon(type?: string) {
  if (type === 'lab')       return 'i-heroicons-command-line'
  if (type === 'challenge') return 'i-heroicons-trophy'
  if (type === 'quiz')      return 'i-heroicons-question-mark-circle'
  return 'i-heroicons-book-open'
}

function stepTypeLabel(type?: string) {
  if (type === 'lab')       return 'Hands-on Lab'
  if (type === 'challenge') return 'Challenge'
  if (type === 'quiz')      return 'Quiz'
  return 'Reading'
}

const statusOptions = [
  { value: 'not_started' as const, label: 'Not Started', icon: 'i-heroicons-minus-circle',
    activeClass: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' },
  { value: 'in_progress' as const, label: 'In Progress', icon: 'i-heroicons-play-circle',
    activeClass: 'bg-teal-100 text-teal-700 dark:bg-teal-900/40 dark:text-teal-300' },
  { value: 'done' as const, label: 'Done', icon: 'i-heroicons-check-circle',
    activeClass: 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' },
]
</script>
