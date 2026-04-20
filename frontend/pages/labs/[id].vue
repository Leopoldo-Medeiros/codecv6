<template>
  <NuxtLayout name="admin">

    <!-- Loading -->
    <div v-if="loading" class="flex h-[70vh] items-center justify-center">
      <UIcon name="i-heroicons-arrow-path" class="animate-spin text-3xl text-emerald-500" />
    </div>

    <template v-else-if="step">
      <!-- Header -->
      <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
          <UButton icon="i-heroicons-arrow-left" size="sm" color="gray" variant="ghost" @click="router.back()" />
          <div class="min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
              <UBadge :color="typeBadgeColor" variant="subtle" size="xs" :icon="typeIcon">
                {{ typeLabel }}
              </UBadge>
              <h1 class="text-lg font-bold text-gray-900 dark:text-white truncate">{{ step.title }}</h1>
            </div>
            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
              Step {{ step.order }} · {{ step.course?.name ?? 'No linked course' }}
            </p>
          </div>
        </div>

        <!-- Progress + status -->
        <div class="flex items-center gap-3 shrink-0">
          <div v-if="step.instructions?.length" class="text-right">
            <p class="text-xs font-semibold" :class="allDone ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500'">
              {{ checkedCount }} / {{ step.instructions.length }} completed
            </p>
            <UProgress :value="progressPct" size="xs" color="emerald" class="w-28 mt-1" />
          </div>
          <UBadge :color="statusColor" variant="subtle">{{ statusLabel }}</UBadge>
        </div>
      </div>

      <!-- Main split layout -->
      <div class="flex gap-4 h-[calc(100vh-180px)] min-h-[500px]">

        <!-- ── Left panel: instructions ─────────────────── -->
        <div class="w-80 shrink-0 flex flex-col gap-4 overflow-y-auto pr-1">

          <!-- Description -->
          <div v-if="step.description"
            class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 p-4">
            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ step.description }}</p>
          </div>

          <!-- Challenge prompt -->
          <div v-if="step.challenge_prompt"
            class="rounded-xl border border-emerald-200 bg-emerald-50 dark:border-emerald-800/40 dark:bg-emerald-950/20 p-4">
            <div class="flex items-center gap-2 mb-2">
              <UIcon name="i-heroicons-bolt" class="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
              <p class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wide">Challenge</p>
            </div>
            <p class="text-sm text-emerald-800 dark:text-emerald-300 leading-relaxed">{{ step.challenge_prompt }}</p>
          </div>

          <!-- Instructions checklist -->
          <div v-if="step.instructions?.length"
            class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 p-4">
            <p class="mb-3 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
              Instructions
            </p>
            <div class="flex flex-col gap-2">
              <label
                v-for="inst in step.instructions"
                :key="inst.id"
                class="flex items-start gap-3 cursor-pointer group"
              >
                <div class="mt-0.5 shrink-0">
                  <div
                    class="h-5 w-5 rounded-full border-2 flex items-center justify-center transition-all"
                    :class="checked.has(inst.id)
                      ? 'border-emerald-500 bg-emerald-500'
                      : 'border-gray-300 dark:border-gray-600 group-hover:border-emerald-400'"
                    @click="toggleCheck(inst.id)"
                  >
                    <UIcon v-if="checked.has(inst.id)" name="i-heroicons-check" class="h-3 w-3 text-white" />
                  </div>
                </div>
                <span
                  class="text-sm leading-relaxed transition-colors"
                  :class="checked.has(inst.id)
                    ? 'text-gray-400 line-through dark:text-gray-500'
                    : 'text-gray-700 dark:text-gray-300'"
                  @click="toggleCheck(inst.id)"
                >
                  {{ inst.text }}
                </span>
              </label>
            </div>
          </div>

          <!-- External resources -->
          <div v-if="step.resources?.length"
            class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 p-4">
            <p class="mb-3 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Resources</p>
            <div class="flex flex-col gap-2">
              <a v-for="r in step.resources" :key="r.url" :href="r.url" target="_blank"
                class="flex items-center gap-2 text-sm text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 hover:underline">
                <UIcon name="i-heroicons-arrow-top-right-on-square" class="h-3.5 w-3.5 shrink-0" />
                {{ r.label }}
              </a>
            </div>
          </div>

          <!-- Mark complete / in-progress actions -->
          <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 p-4 mt-auto">
            <p class="mb-3 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Your Progress</p>
            <div class="flex flex-col gap-2">
              <UButton
                block
                color="emerald"
                variant="solid"
                icon="i-heroicons-check-circle"
                :loading="saving"
                :disabled="hasInstructions && !allDone"
                @click="markStatus('done')"
              >
                {{ step.user_status === 'done' ? 'Completed ✓' : 'Mark as Done' }}
              </UButton>
              <UButton
                v-if="step.user_status !== 'in_progress'"
                block
                color="gray"
                variant="outline"
                icon="i-heroicons-play-circle"
                :loading="saving"
                @click="markStatus('in_progress')"
              >
                Mark In Progress
              </UButton>
              <p v-if="hasInstructions && !allDone" class="text-xs text-center text-gray-400 dark:text-gray-500">
                Complete all {{ step.instructions!.length }} instructions first
              </p>
            </div>
          </div>
        </div>

        <!-- ── Right panel: embedded environment ─────────── -->
        <div class="flex-1 flex flex-col rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">

          <!-- Toolbar -->
          <div class="flex items-center justify-between px-4 py-2 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 shrink-0">
            <div class="flex items-center gap-2">
              <div class="h-3 w-3 rounded-full bg-red-400" />
              <div class="h-3 w-3 rounded-full bg-yellow-400" />
              <div class="h-3 w-3 rounded-full bg-green-400" />
              <span class="ml-2 text-xs text-gray-500 dark:text-gray-400 font-mono truncate max-w-xs">
                {{ step.lab_url ?? 'No environment configured' }}
              </span>
            </div>
            <a v-if="step.lab_url" :href="step.lab_url" target="_blank"
              class="flex items-center gap-1 text-xs text-gray-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
              <UIcon name="i-heroicons-arrow-top-right-on-square" class="h-3.5 w-3.5" />
              Open in new tab
            </a>
          </div>

          <!-- iframe or empty state -->
          <div class="flex-1 bg-gray-100 dark:bg-gray-900">
            <iframe
              v-if="step.lab_url"
              :src="step.lab_url"
              class="w-full h-full border-0"
              allow="cross-origin-isolated"
              sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-modals allow-downloads"
            />
            <div v-else class="flex h-full flex-col items-center justify-center text-center p-8">
              <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-200 dark:bg-gray-700">
                <UIcon name="i-heroicons-command-line" class="h-8 w-8 text-gray-400" />
              </div>
              <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">No lab environment linked</p>
              <p class="mt-1 text-xs text-gray-400 dark:text-gray-500 max-w-xs">
                Ask your consultant to link a StackBlitz, Killercoda, or GitHub Codespace to this step.
              </p>
            </div>
          </div>
        </div>

      </div>
    </template>

  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({ layout: false, middleware: 'auth' })

const route  = useRoute()
const router = useRouter()
const toast  = useToast()

const { fetchStep, updateStepProgress } = usePaths()

const step   = ref<any>(null)
const loading = ref(true)
const saving  = ref(false)

// ── Instruction checkboxes (persisted in localStorage) ──
const checked = ref(new Set<number>())
const storageKey = computed(() => `lab-progress-${route.params.id}`)

onMounted(async () => {
  try {
    step.value = await fetchStep(Number(route.params.id))
    // Restore checked state from localStorage
    const saved = localStorage.getItem(storageKey.value)
    if (saved) {
      const ids: number[] = JSON.parse(saved)
      checked.value = new Set(ids)
    }
  } finally {
    loading.value = false
  }
})

useHead(() => ({ title: step.value ? `${step.value.title} — Lab` : 'Lab — CODECV' }))

function toggleCheck(id: number) {
  if (checked.value.has(id)) {
    checked.value.delete(id)
  } else {
    checked.value.add(id)
  }
  // Persist
  localStorage.setItem(storageKey.value, JSON.stringify([...checked.value]))
}

const hasInstructions = computed(() => (step.value?.instructions?.length ?? 0) > 0)
const checkedCount    = computed(() => checked.value.size)
const allDone         = computed(() =>
  !hasInstructions.value || checked.value.size >= (step.value?.instructions?.length ?? 0)
)
const progressPct = computed(() => {
  const total = step.value?.instructions?.length ?? 0
  return total ? Math.round((checked.value.size / total) * 100) : 0
})

async function markStatus(status: 'in_progress' | 'done') {
  if (saving.value) return
  saving.value = true
  try {
    await updateStepProgress(step.value.id, status)
    step.value = { ...step.value, user_status: status }
    toast.add({ title: status === 'done' ? 'Step completed!' : 'Marked as in progress', color: 'emerald' })
  } catch {
    toast.add({ title: 'Could not save progress', color: 'red' })
  } finally {
    saving.value = false
  }
}

// ── Presentation helpers ────────────────────────────────
const typeLabel = computed(() => ({
  lab:       'Hands-on Lab',
  challenge: 'Challenge',
  quiz:      'Quiz',
  reading:   'Reading',
}[step.value?.type ?? 'reading']))

const typeIcon = computed(() => ({
  lab:       'i-heroicons-command-line',
  challenge: 'i-heroicons-bolt',
  quiz:      'i-heroicons-question-mark-circle',
  reading:   'i-heroicons-book-open',
}[step.value?.type ?? 'reading']))

const typeBadgeColor = computed(() => ({
  lab:       'emerald',
  challenge: 'amber',
  quiz:      'violet',
  reading:   'gray',
}[step.value?.type ?? 'reading']))

const statusLabel = computed(() => ({
  done:        'Done',
  in_progress: 'In Progress',
  not_started: 'Not Started',
}[step.value?.user_status ?? 'not_started']))

const statusColor = computed(() => ({
  done:        'green',
  in_progress: 'emerald',
  not_started: 'gray',
}[step.value?.user_status ?? 'not_started']))
</script>
