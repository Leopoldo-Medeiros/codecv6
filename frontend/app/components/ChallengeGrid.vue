<template>
  <div>
    <!-- Filters -->
    <div class="mb-6 flex items-center gap-2">
      <button
        v-for="f in filters"
        :key="f.value"
        class="rounded-md border px-3 py-1.5 text-sm font-medium transition-colors"
        :class="
          activeFilter === f.value
            ? 'border-emerald-500 bg-emerald-500/10 text-emerald-400'
            : 'border-slate-700 bg-slate-800/50 text-slate-400 hover:border-slate-500 hover:text-slate-200'
        "
        @click="activeFilter = f.value"
      >
        {{ f.label }}
        <span class="ml-1.5 rounded bg-slate-700 px-1.5 py-0.5 text-xs tabular-nums">
          {{ f.value === 'all' ? challenges.length : challenges.filter(c => c.difficulty === f.value).length }}
        </span>
      </button>

      <div class="ml-auto flex items-center gap-1.5 text-sm text-slate-500">
        <CheckCircle :size="14" class="text-emerald-500" />
        <span>{{ completedCount }} / {{ challenges.length }} completed</span>
      </div>
    </div>

    <!-- Grid -->
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
      <button
        v-for="challenge in filtered"
        :key="challenge.id"
        class="group relative flex flex-col rounded-xl border bg-slate-900 p-5 text-left transition-all duration-150"
        :class="[
          statusClass(challenge),
          isLocked(challenge) ? 'cursor-default opacity-70' : 'cursor-pointer hover:shadow-lg hover:shadow-emerald-950/30',
        ]"
        :disabled="isLocked(challenge)"
        @click="!isLocked(challenge) && emit('select', challenge)"
      >
        <!-- Top row: difficulty + status icon -->
        <div class="mb-3 flex items-start justify-between gap-2">
          <span
            class="rounded px-2 py-0.5 text-xs font-semibold uppercase tracking-wide"
            :class="difficultyClass(challenge.difficulty)"
          >
            {{ challenge.difficulty }}
          </span>

          <component
            :is="statusIcon(challenge)"
            :size="18"
            class="shrink-0 transition-colors"
            :class="statusIconClass(challenge)"
          />
        </div>

        <!-- Title -->
        <h3 class="mb-1 text-sm font-semibold text-slate-100 leading-snug group-hover:text-emerald-300 transition-colors" :class="{ 'group-hover:text-slate-100': isLocked(challenge) }">
          {{ challenge.title }}
        </h3>

        <!-- Description preview -->
        <p class="mt-1 line-clamp-2 text-xs text-slate-500 leading-relaxed">
          {{ stripMarkdown(challenge.description) }}
        </p>

        <!-- Footer -->
        <div class="mt-4 flex items-center gap-2">
          <template v-if="challenge.is_premium && !isPurchased(challenge)">
            <Lock :size="11" class="text-amber-500" />
            <span class="text-xs text-amber-500 font-medium">
              {{ challenge.price_eur ? `€${(challenge.price_eur / 100).toFixed(2)}` : 'Premium' }}
            </span>
          </template>
          <template v-else>
            <span class="text-xs" :class="getStatus(challenge) === 'done' ? 'text-emerald-500' : 'text-slate-600'">
              {{ getStatus(challenge) === 'done' ? 'Completed' : 'Free' }}
            </span>
          </template>

          <div class="ml-auto">
            <span
              v-if="!isLocked(challenge)"
              class="text-xs font-medium text-slate-500 group-hover:text-emerald-400 transition-colors"
            >
              {{ getStatus(challenge) === 'done' ? 'Review →' : getStatus(challenge) === 'in_progress' ? 'Continue →' : 'Start →' }}
            </span>
          </div>
        </div>

        <!-- Progress bar for in-progress -->
        <div
          v-if="getStatus(challenge) === 'in_progress'"
          class="absolute bottom-0 left-0 right-0 h-0.5 rounded-b-xl bg-emerald-500/40 overflow-hidden"
        >
          <div class="h-full w-1/2 bg-emerald-500 rounded-full" />
        </div>
      </button>
    </div>

    <!-- Empty state -->
    <div v-if="filtered.length === 0" class="py-16 text-center">
      <SquareCode :size="32" class="mx-auto mb-3 text-slate-700" />
      <p class="text-sm text-slate-500">No challenges in this category yet.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { CheckCircle, Lock, SquareCode, Circle } from 'lucide-vue-next'
import type { Challenge } from '~/types/models'

const props = defineProps<{
  challenges: Challenge[]
  progress?: Record<number, 'not_started' | 'in_progress' | 'done'>
  purchasedIds?: number[]
}>()

const emit = defineEmits<{
  select: [challenge: Challenge]
}>()

const filters = [
  { label: 'All', value: 'all' },
  { label: 'Beginner', value: 'beginner' },
  { label: 'Intermediate', value: 'intermediate' },
  { label: 'Advanced', value: 'advanced' },
] as const

const activeFilter = ref<string>('all')

const filtered = computed(() =>
  activeFilter.value === 'all'
    ? props.challenges
    : props.challenges.filter(c => c.difficulty === activeFilter.value),
)

const completedCount = computed(
  () => props.challenges.filter(c => getStatus(c) === 'done').length,
)

function getStatus(c: Challenge): 'not_started' | 'in_progress' | 'done' {
  return props.progress?.[c.id] ?? 'not_started'
}

function isPurchased(c: Challenge): boolean {
  return !c.is_premium || (props.purchasedIds?.includes(c.id) ?? false)
}

function isLocked(c: Challenge): boolean {
  return c.is_premium && !isPurchased(c)
}

function statusIcon(c: Challenge) {
  if (isLocked(c)) return Lock
  if (getStatus(c) === 'done') return CheckCircle
  if (getStatus(c) === 'in_progress') return SquareCode
  return Circle
}

function statusIconClass(c: Challenge): string {
  if (isLocked(c)) return 'text-amber-500/60'
  if (getStatus(c) === 'done') return 'text-emerald-500'
  if (getStatus(c) === 'in_progress') return 'text-emerald-400'
  return 'text-slate-600'
}

function statusClass(c: Challenge): string {
  if (getStatus(c) === 'done') return 'border-emerald-800/60 bg-emerald-950/20'
  if (getStatus(c) === 'in_progress') return 'border-emerald-700/40'
  return 'border-slate-800 hover:border-slate-700'
}

function difficultyClass(d: Challenge['difficulty']): string {
  return {
    beginner: 'bg-emerald-500/10 text-emerald-400',
    intermediate: 'bg-amber-500/10 text-amber-400',
    advanced: 'bg-rose-500/10 text-rose-400',
  }[d]
}

function stripMarkdown(text: string): string {
  return text
    .replace(/^#+\s+.*/gm, '')
    .replace(/\*\*(.+?)\*\*/g, '$1')
    .replace(/`([^`]+)`/g, '$1')
    .replace(/\[([^\]]+)\]\([^)]+\)/g, '$1')
    .replace(/\n+/g, ' ')
    .trim()
}
</script>
