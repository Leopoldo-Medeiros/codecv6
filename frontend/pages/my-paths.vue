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
          :class="view === 'timeline' ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800'"
          @click="view = 'timeline'"
        >
          <UIcon name="i-heroicons-bars-3-bottom-left" class="h-3.5 w-3.5" />
          Timeline
        </button>
        <button
          class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium transition-colors"
          :class="view === 'roadmap' ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800'"
          @click="view = 'roadmap'"
        >
          <UIcon name="i-heroicons-map" class="h-3.5 w-3.5" />
          Roadmap
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-20">
      <UIcon name="i-heroicons-arrow-path" class="animate-spin text-3xl text-indigo-500" />
    </div>

    <template v-else>

      <!-- Empty -->
      <div v-if="!paths.length" class="flex flex-col items-center py-20 text-center">
        <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-950">
          <UIcon name="i-heroicons-map" class="h-10 w-10 text-indigo-400" />
        </div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">No learning paths yet</h3>
        <p class="mt-2 max-w-sm text-sm text-gray-500 dark:text-gray-400">
          Your consultant will assign a personalised learning path based on your goals and target role.
        </p>
        <a href="https://wa.me/353894050730?text=Hi%2C+I%27d+like+to+get+a+learning+path+assigned+to+my+profile."
          target="_blank"
          class="mt-5 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5
                 text-sm font-semibold text-white transition-opacity hover:opacity-90">
          <UIcon name="i-heroicons-chat-bubble-left-ellipsis" class="h-4 w-4" />
          Request a Learning Path
        </a>
      </div>

      <!-- Paths -->
      <div v-else class="flex flex-col gap-8">
        <div v-for="path in enrichedPaths" :key="path.id">

          <!-- Path header card -->
          <div class="mb-4 rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div class="flex flex-wrap items-center gap-4 p-5">
              <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-950">
                <UIcon name="i-heroicons-map" class="h-6 w-6 text-indigo-500" />
              </div>
              <div class="min-w-0 flex-1">
                <h2 class="text-base font-bold text-gray-900 dark:text-white">{{ path.name }}</h2>
                <p v-if="path.description" class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                  {{ path.description }}
                </p>
              </div>
              <!-- Overall progress -->
              <div class="flex shrink-0 flex-col items-end gap-1">
                <span class="text-sm font-bold" :class="progressColor(path.progressPct)">
                  {{ path.progressPct }}%
                </span>
                <UProgress :value="path.progressPct" size="sm" color="indigo" class="w-32" />
                <span class="text-xs text-gray-400">
                  {{ path.doneCount }} / {{ path.steps.length }} done
                </span>
              </div>
            </div>
          </div>

          <!-- Roadmap view -->
          <RoadmapFlow
            v-if="view === 'roadmap' && path.steps.length"
            :steps="path.steps"
            @node-click="(step) => toggleExpanded(step.id)"
          />

          <!-- Timeline -->
          <div v-if="view === 'timeline' && path.steps.length" class="flex flex-col pl-2 sm:pl-6">
            <div v-for="(step, i) in path.steps" :key="step.id" class="flex gap-4">

              <!-- Connector -->
              <div class="flex flex-col items-center">
                <!-- Node -->
                <button
                  class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border-2 font-bold text-xs
                         transition-all focus:outline-none"
                  :class="nodeClass(step.user_status)"
                  :title="statusLabel(step.user_status)"
                  @click="cycleStatus(path, step)"
                >
                  <UIcon v-if="step.user_status === 'done'" name="i-heroicons-check" class="h-4 w-4" />
                  <span v-else>{{ i + 1 }}</span>
                </button>
                <!-- Line -->
                <div v-if="i < path.steps.length - 1"
                  class="my-1 w-0.5 flex-1"
                  :class="step.user_status === 'done' ? 'bg-green-300 dark:bg-green-700' : 'bg-gray-200 dark:bg-gray-700'"
                />
              </div>

              <!-- Step card -->
              <div class="mb-3 min-w-0 flex-1 cursor-pointer rounded-xl border transition-all"
                :class="stepCardClass(step.user_status)"
                @click="toggleExpanded(step.id)">
                <div class="flex items-start gap-3 p-4">
                  <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                      <p class="text-sm font-semibold"
                        :class="step.user_status === 'done'
                          ? 'text-gray-400 line-through dark:text-gray-500'
                          : 'text-gray-900 dark:text-white'">
                        {{ step.title }}
                      </p>
                      <UBadge :color="statusBadgeColor(step.user_status)" variant="subtle" size="xs">
                        {{ statusLabel(step.user_status) }}
                      </UBadge>
                    </div>
                    <!-- Expanded content -->
                    <Transition enter-from-class="opacity-0 -translate-y-1" enter-active-class="transition duration-150"
                                leave-to-class="opacity-0 -translate-y-1" leave-active-class="transition duration-100">
                      <div v-if="expanded.has(step.id)" class="mt-3">
                        <p v-if="step.description" class="text-sm text-gray-600 leading-relaxed dark:text-gray-400">
                          {{ step.description }}
                        </p>
                        <!-- Linked course -->
                        <div v-if="step.course" class="mt-3">
                          <UButton size="xs" color="indigo" variant="soft" icon="i-heroicons-book-open"
                            @click.stop="navigateTo(`/courses/${step.course!.id}`)">
                            Open Course: {{ step.course.name }}
                          </UButton>
                        </div>
                        <!-- Resources -->
                        <div v-if="step.resources?.length" class="mt-3 flex flex-wrap gap-2">
                          <a v-for="r in step.resources" :key="r.url"
                            :href="r.url" target="_blank"
                            class="flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1
                                   text-xs font-medium text-gray-600 hover:bg-indigo-50 hover:text-indigo-600
                                   dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-indigo-950 dark:hover:text-indigo-400"
                            @click.stop>
                            <UIcon name="i-heroicons-arrow-top-right-on-square" class="h-3 w-3" />
                            {{ r.label }}
                          </a>
                        </div>
                        <!-- Status controls -->
                        <div class="mt-4 flex flex-wrap gap-2">
                          <button v-for="s in statusOptions" :key="s.value"
                            class="flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-medium transition-colors"
                            :class="step.user_status === s.value
                              ? `border-transparent ${s.activeClass}`
                              : 'border-gray-200 text-gray-500 hover:border-gray-300 dark:border-gray-600'"
                            @click.stop="setStatus(path, step, s.value)">
                            <UIcon :name="s.icon" class="h-3.5 w-3.5" />
                            {{ s.label }}
                          </button>
                        </div>
                      </div>
                    </Transition>
                  </div>
                  <UIcon
                    :name="expanded.has(step.id) ? 'i-heroicons-chevron-up' : 'i-heroicons-chevron-down'"
                    class="h-4 w-4 shrink-0 text-gray-400"
                  />
                </div>
              </div>
            </div>
          </div>

          <div v-else-if="!path.steps.length" class="rounded-xl border border-dashed border-gray-200 px-5 py-8 text-center dark:border-gray-700">
            <p class="text-sm text-gray-400 dark:text-gray-500">Your consultant hasn't added steps to this path yet.</p>
          </div>

        </div>
      </div>

    </template>
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
  if (saving.value.has(step.id)) return
  saving.value.add(step.id)

  const prev = step.user_status
  // optimistic update
  const arr = pathSteps.value[path.id]
  if (arr) {
    const idx = arr.findIndex(s => s.id === step.id)
    if (idx !== -1) arr[idx] = { ...arr[idx], user_status: status }
  }

  try {
    await updateStepProgress(step.id, status)
  } catch {
    // rollback
    if (arr) {
      const idx = arr.findIndex(s => s.id === step.id)
      if (idx !== -1) arr[idx] = { ...arr[idx], user_status: prev }
    }
    toast.add({ title: 'Could not save progress', color: 'red' })
  } finally {
    saving.value.delete(step.id)
  }
}

// ── Styling helpers ─────────────────────────────────────

function nodeClass(status: PathStep['user_status']) {
  if (status === 'done')        return 'border-green-400 bg-green-500 text-white dark:border-green-500'
  if (status === 'in_progress') return 'border-indigo-400 bg-indigo-500 text-white dark:border-indigo-500'
  return 'border-gray-300 bg-white text-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400'
}

function stepCardClass(status: PathStep['user_status']) {
  if (status === 'done')
    return 'border-green-100 bg-green-50/50 dark:border-green-900/30 dark:bg-green-950/10'
  if (status === 'in_progress')
    return 'border-indigo-200 bg-indigo-50/50 dark:border-indigo-800/40 dark:bg-indigo-950/20'
  return 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800'
}

function statusBadgeColor(status: PathStep['user_status']) {
  if (status === 'done') return 'green'
  if (status === 'in_progress') return 'indigo'
  return 'gray'
}

function statusLabel(status: PathStep['user_status']) {
  if (status === 'done') return 'Done'
  if (status === 'in_progress') return 'In Progress'
  return 'Not Started'
}

function progressColor(pct: number) {
  if (pct >= 75) return 'text-green-600 dark:text-green-400'
  if (pct >= 40) return 'text-indigo-600 dark:text-indigo-400'
  return 'text-gray-500 dark:text-gray-400'
}

const statusOptions = [
  { value: 'not_started' as const, label: 'Not Started', icon: 'i-heroicons-minus-circle',
    activeClass: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' },
  { value: 'in_progress' as const, label: 'In Progress', icon: 'i-heroicons-play-circle',
    activeClass: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' },
  { value: 'done' as const, label: 'Done', icon: 'i-heroicons-check-circle',
    activeClass: 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' },
]
</script>
