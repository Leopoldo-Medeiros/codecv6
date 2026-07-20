<template>
  <NuxtLayout name="admin">

    <!-- Header -->
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Challenges</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
          Real-world coding problems, run in a sandbox. Solve them to earn XP and keep your streak alive.
        </p>
      </div>
      <p v-if="!loading && challenges.length" class="text-xs text-gray-400 dark:text-gray-500 tabular-nums">
        {{ solvedCount }} / {{ challenges.length }} solved
      </p>
    </div>

    <!-- Controls -->
    <div class="mb-5 flex flex-wrap items-center gap-3">
      <UInput
        v-model="search"
        icon="i-heroicons-magnifying-glass"
        placeholder="Search challenges…"
        size="sm"
        class="w-full sm:w-64"
      />

      <div class="flex flex-wrap gap-1.5">
        <button
          v-for="d in difficultyFilters"
          :key="d.value"
          class="rounded-full border px-3 py-1 text-xs font-medium capitalize transition-colors"
          :class="difficulty === d.value
            ? 'border-transparent bg-emerald-600 text-white'
            : 'border-gray-200 text-gray-500 hover:border-gray-300 dark:border-gray-700 dark:text-gray-400 dark:hover:border-gray-500'"
          @click="difficulty = d.value"
        >
          {{ d.label }}
        </button>
      </div>

      <div class="flex flex-wrap gap-1.5">
        <button
          v-for="s in statusFilters"
          :key="s.value"
          class="rounded-full border px-3 py-1 text-xs font-medium transition-colors"
          :class="status === s.value
            ? 'border-transparent bg-emerald-600 text-white'
            : 'border-gray-200 text-gray-500 hover:border-gray-300 dark:border-gray-700 dark:text-gray-400 dark:hover:border-gray-500'"
          @click="status = s.value"
        >
          {{ s.label }}
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-20">
      <UIcon name="i-heroicons-arrow-path" class="animate-spin text-3xl text-emerald-500" />
    </div>

    <div v-else-if="error" class="flex flex-col items-center py-20 text-center">
      <UIcon name="i-heroicons-exclamation-triangle" class="mb-4 h-10 w-10 text-red-400" />
      <p class="text-sm text-red-500 dark:text-red-400">{{ error }}</p>
    </div>

    <!-- Empty filter result -->
    <div v-else-if="!filtered.length" class="flex flex-col items-center py-20 text-center">
      <UIcon name="i-heroicons-funnel" class="mb-3 h-8 w-8 text-gray-300 dark:text-gray-600" />
      <p class="text-sm text-gray-500 dark:text-gray-400">No challenges match your filters.</p>
    </div>

    <!-- Grid -->
    <div v-else class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
      <button
        v-for="c in filtered"
        :key="c.id"
        class="group flex flex-col rounded-xl border bg-white p-5 text-left transition-all hover:-translate-y-0.5 hover:shadow-md
               dark:bg-gray-900"
        :class="c.solved
          ? 'border-emerald-200 dark:border-emerald-900/60'
          : 'border-gray-200 hover:border-emerald-300 dark:border-gray-700 dark:hover:border-emerald-800'"
        @click="navigateTo(`/challenges/${c.slug}`)"
      >
        <div class="mb-2 flex items-center gap-2">
          <span class="rounded px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide" :class="difficultyClass(c.difficulty)">
            {{ c.difficulty }}
          </span>
          <UBadge v-if="c.locked" color="amber" variant="subtle" size="xs">
            <UIcon name="i-heroicons-lock-closed" class="mr-0.5 h-3 w-3" /> Pro
          </UBadge>
          <span v-if="c.solved" class="ml-auto flex items-center gap-1 text-[11px] font-semibold text-emerald-500">
            <UIcon name="i-heroicons-check-circle-solid" class="h-4 w-4" /> Solved
          </span>
        </div>

        <h3 class="text-sm font-bold leading-snug text-gray-900 group-hover:text-emerald-600 dark:text-white dark:group-hover:text-emerald-400">
          {{ c.title }}
        </h3>
        <p class="mt-1.5 line-clamp-3 flex-1 text-xs leading-relaxed text-gray-500 dark:text-gray-400">
          {{ excerpt(c.description) }}
        </p>

        <span class="mt-3 flex items-center gap-1 text-xs font-semibold text-emerald-600 opacity-0 transition-opacity group-hover:opacity-100 dark:text-emerald-400">
          {{ c.solved ? 'Solve again' : c.locked ? 'See details' : 'Start solving' }}
          <UIcon name="i-heroicons-arrow-right" class="h-3.5 w-3.5" />
        </span>
      </button>
    </div>

  </NuxtLayout>
</template>

<script setup lang="ts">
import type { Challenge } from '~/types/models'

definePageMeta({ layout: false, middleware: 'auth' })
useHead({ title: 'Challenges — CODECV' })

const { challenges, loading, error, fetchChallenges } = useChallenges()

const search = ref('')
const difficulty = ref<'all' | Challenge['difficulty']>('all')
const status = ref<'all' | 'solved' | 'unsolved'>('all')

const difficultyFilters = [
  { value: 'all' as const, label: 'All levels' },
  { value: 'beginner' as const, label: 'Beginner' },
  { value: 'intermediate' as const, label: 'Intermediate' },
  { value: 'advanced' as const, label: 'Advanced' },
  { value: 'expert' as const, label: 'Expert' },
]

const statusFilters = [
  { value: 'all' as const, label: 'All' },
  { value: 'unsolved' as const, label: 'To solve' },
  { value: 'solved' as const, label: 'Solved ✓' },
]

onMounted(() => { fetchChallenges().catch(() => {}) })

const solvedCount = computed(() => challenges.value.filter(c => c.solved).length)

const filtered = computed(() => {
  const q = search.value.trim().toLowerCase()
  return challenges.value.filter((c) => {
    if (difficulty.value !== 'all' && c.difficulty !== difficulty.value) return false
    if (status.value === 'solved' && !c.solved) return false
    if (status.value === 'unsolved' && c.solved) return false
    if (q && !c.title.toLowerCase().includes(q) && !c.description.toLowerCase().includes(q)) return false
    return true
  })
})

// First real paragraph of the description, markdown tokens stripped — the
// card teaser. Descriptions open with "## The situation", so skip headings.
function excerpt(description: string): string {
  const paragraph = description
    .split('\n')
    .map(l => l.trim())
    .filter(l => l && !l.startsWith('#') && !l.startsWith('|') && !l.startsWith('```'))
    .slice(0, 3)
    .join(' ')
  return paragraph
    .replace(/\*\*(.+?)\*\*/g, '$1')
    .replace(/\*(.+?)\*/g, '$1')
    .replace(/`([^`]+)`/g, '$1')
    .slice(0, 220)
}

function difficultyClass(d: Challenge['difficulty']) {
  return {
    beginner: 'bg-emerald-500/10 text-emerald-500',
    intermediate: 'bg-amber-500/10 text-amber-500',
    advanced: 'bg-rose-500/10 text-rose-500',
    expert: 'bg-violet-500/10 text-violet-500',
  }[d]
}
</script>
