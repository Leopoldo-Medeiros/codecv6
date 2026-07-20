<template>
  <Teleport to="body">
    <Transition name="celebrate" appear>
      <div class="fixed inset-0 z-[70] flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="emit('close')" />

        <!-- Card -->
        <div class="celebrate-card relative w-full max-w-sm overflow-hidden rounded-2xl border border-neutral-800 bg-neutral-900 p-6 text-center shadow-2xl">
          <!-- Confetti burst (CSS only) -->
          <div class="pointer-events-none absolute inset-x-0 top-0 h-40 overflow-hidden" aria-hidden="true">
            <span
              v-for="(c, i) in confetti"
              :key="i"
              class="confetti absolute block h-1.5 w-1.5 rounded-sm"
              :style="{ left: c.left, backgroundColor: c.color, animationDelay: c.delay, animationDuration: c.duration }"
            />
          </div>

          <button
            class="absolute right-3 top-3 rounded-full p-1 text-neutral-500 transition-colors hover:bg-neutral-800 hover:text-neutral-300"
            aria-label="Close"
            @click="emit('close')"
          >
            <X :size="16" />
          </button>

          <!-- XP -->
          <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-500/10 ring-1 ring-emerald-500/30">
            <Check :size="26" class="text-emerald-400" />
          </div>
          <p class="text-sm font-medium text-neutral-300">{{ heading ?? 'Step complete!' }}</p>
          <p class="mt-1 text-4xl font-bold tabular-nums text-emerald-400">+{{ animatedXp }} XP</p>
          <p class="mt-1 text-[11px] text-neutral-500 tabular-nums">{{ progress.xp_points }} XP total</p>

          <!-- Streak -->
          <div v-if="progress.current_streak > 0" class="mt-4 flex items-center justify-center gap-1.5 text-sm">
            <Flame :size="15" class="text-amber-400" />
            <span class="font-semibold tabular-nums text-amber-300">{{ progress.current_streak }}</span>
            <span class="text-neutral-400">day streak{{ progress.current_streak > 1 ? ' — keep it alive' : ' started' }}</span>
          </div>

          <!-- Fresh badges -->
          <div v-if="progress.new_badges.length" class="mt-4 space-y-2">
            <div
              v-for="badge in progress.new_badges"
              :key="badge.key"
              class="flex items-center gap-3 rounded-xl border border-amber-700/30 bg-amber-950/20 p-3 text-left"
            >
              <span class="text-2xl leading-none">{{ badge.icon }}</span>
              <div class="min-w-0">
                <p class="text-sm font-semibold text-amber-300">{{ badge.name }}</p>
                <p class="truncate text-[11px] text-neutral-500">{{ badge.description }}</p>
              </div>
            </div>
          </div>

          <!-- CTAs -->
          <div class="mt-6 space-y-2">
            <button
              v-if="nextStep"
              class="flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-emerald-500"
              @click="emit('next')"
            >
              <span class="truncate">Next up: {{ nextStep.title }}</span>
              <ArrowRight :size="14" class="shrink-0" />
            </button>
            <button
              class="w-full rounded-lg border border-neutral-700 px-4 py-2 text-xs text-neutral-400 transition-colors hover:border-neutral-500 hover:text-neutral-200"
              @click="nextStep ? emit('close') : emit('exit')"
            >
              {{ nextStep ? 'Stay here' : (exitLabel ?? 'Back to my paths') }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { X, Check, Flame, ArrowRight } from 'lucide-vue-next'
import type { PathStep } from '~/composables/usePaths'

const props = defineProps<{
  progress: {
    xp_awarded: number
    xp_points: number
    current_streak: number
    new_badges: Array<{ key: string; name: string; description: string; icon: string }>
  }
  nextStep?: PathStep | null
  heading?: string
  exitLabel?: string
}>()

const emit = defineEmits<{
  close: []
  next: []
  exit: []
}>()

// Deterministic confetti layout — no Math.random so SSR/CSR always agree.
const palette = ['#34d399', '#fbbf24', '#f472b6', '#60a5fa', '#a78bfa']
const confetti = Array.from({ length: 14 }, (_, i) => ({
  left: `${(i * 7.3 + 4) % 96}%`,
  color: palette[i % palette.length]!,
  delay: `${(i % 5) * 0.12}s`,
  duration: `${1.4 + (i % 4) * 0.3}s`,
}))

// Count the awarded XP up from zero — small, cheap dopamine.
const animatedXp = ref(0)
onMounted(() => {
  const target = props.progress.xp_awarded
  const started = performance.now()
  const duration = 700
  const tick = (now: number) => {
    const t = Math.min((now - started) / duration, 1)
    animatedXp.value = Math.round(target * (1 - Math.pow(1 - t, 3)))
    if (t < 1) requestAnimationFrame(tick)
  }
  requestAnimationFrame(tick)
})
</script>

<style scoped>
.celebrate-enter-active {
  transition: opacity 0.2s ease;
}
.celebrate-enter-active .celebrate-card {
  transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.2s ease;
}
.celebrate-enter-from {
  opacity: 0;
}
.celebrate-enter-from .celebrate-card {
  transform: scale(0.92) translateY(10px);
  opacity: 0;
}

.confetti {
  top: -8px;
  animation-name: confetti-fall;
  animation-timing-function: ease-in;
  animation-iteration-count: 1;
  animation-fill-mode: forwards;
}
@keyframes confetti-fall {
  0% {
    transform: translateY(0) rotate(0deg);
    opacity: 1;
  }
  100% {
    transform: translateY(160px) rotate(540deg);
    opacity: 0;
  }
}
</style>
