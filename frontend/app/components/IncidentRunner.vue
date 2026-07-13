<template>
  <div class="mx-auto max-w-3xl space-y-5">
    <!-- Scenario -->
    <div
      v-if="evidence.scenario"
      class="rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/60 dark:bg-amber-950/30"
    >
      <p class="mb-1 flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400">
        <UIcon name="i-heroicons-exclamation-triangle" class="h-4 w-4" />
        Incident
      </p>
      <p class="text-sm leading-relaxed text-gray-800 dark:text-gray-200">{{ evidence.scenario }}</p>
    </div>

    <!-- Evidence: trace -->
    <section v-if="evidence.trace?.spans?.length">
      <TraceWaterfall :trace="evidence.trace" />
    </section>

    <!-- Evidence: metrics -->
    <section v-if="evidence.metrics?.length" class="grid gap-4" :class="evidence.metrics.length > 1 ? 'sm:grid-cols-2' : ''">
      <MetricChart v-for="(m, i) in evidence.metrics" :key="i" :metric="m" />
    </section>

    <!-- Evidence: logs -->
    <section v-if="evidence.logs?.length">
      <LogStream :logs="evidence.logs" />
    </section>

    <!-- Diagnose -->
    <section v-if="questions.length" class="pt-1">
      <div class="mb-3 flex items-center gap-2">
        <div class="h-px flex-1 bg-gray-200 dark:bg-gray-700" />
        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Diagnose it</p>
        <div class="h-px flex-1 bg-gray-200 dark:bg-gray-700" />
      </div>
      <QuizRunner :step-id="stepId" :questions="questions" @passed="emit('passed')" />
    </section>
  </div>
</template>

<script setup lang="ts">
import type { IncidentEvidence } from '~/composables/usePaths'

defineProps<{
  stepId: number
  evidence: IncidentEvidence
  questions: Array<{ id: number; question: string; options: string[] }>
}>()

const emit = defineEmits<{ passed: [] }>()
</script>
