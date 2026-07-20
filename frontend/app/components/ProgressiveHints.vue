<template>
  <div>
    <div class="mb-4 flex items-start gap-2.5 rounded-lg border border-neutral-800 bg-neutral-900/60 p-3">
      <Lightbulb :size="15" class="mt-0.5 shrink-0 text-amber-400" />
      <p class="text-xs leading-relaxed text-neutral-400">
        Hints go from a gentle nudge to nearly the answer. Wrestle with the
        problem first — then reveal them one at a time.
      </p>
    </div>

    <TransitionGroup name="hint" tag="div" class="space-y-2.5">
      <div
        v-for="(hint, i) in revealedHints"
        :key="i"
        class="flex items-start gap-3 rounded-lg border border-amber-800/30 bg-amber-950/15 p-3.5"
      >
        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-500/15 text-[11px] font-bold text-amber-400">
          {{ i + 1 }}
        </span>
        <!-- eslint-disable-next-line vue/no-v-html -->
        <div class="hint-body min-w-0 text-[13px] leading-relaxed text-neutral-300" v-html="renderMarkdown(hint)" />
      </div>
    </TransitionGroup>

    <button
      v-if="revealed < hints.length"
      class="mt-3 flex w-full items-center justify-center gap-2 rounded-lg border border-dashed border-neutral-700 py-2.5 text-xs font-medium text-neutral-400 transition-colors hover:border-amber-600/50 hover:text-amber-400"
      @click="revealed++"
    >
      <Lightbulb :size="13" />
      Reveal hint {{ revealed + 1 }} of {{ hints.length }}
    </button>
    <p v-else class="mt-4 text-center text-[11px] text-neutral-600">
      All hints revealed — you've got everything you need.
    </p>
  </div>
</template>

<script setup lang="ts">
import { Lightbulb } from 'lucide-vue-next'

const props = defineProps<{
  hints: string[]
}>()

const revealed = ref(0)

const revealedHints = computed(() => props.hints.slice(0, revealed.value))

// A new challenge (or a reset) starts hidden again.
watch(() => props.hints, () => { revealed.value = 0 })
</script>

<style scoped>
.hint-enter-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}
.hint-enter-from {
  opacity: 0;
  transform: translateY(6px);
}
.hint-body :deep(code) {
  border-radius: 0.25rem;
  background: rgb(38 38 38);
  padding: 0.1rem 0.3rem;
  font-size: 0.75rem;
  color: rgb(110 231 183);
}
.hint-body :deep(strong) {
  color: rgb(212 212 212);
}
</style>
