<template>
  <div class="rounded-2xl border border-gray-200/80 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Help shape what's next</h2>
    <p class="mt-0.5 text-xs text-gray-500 dark:text-neutral-400">Which track should we build first? Tap to vote.</p>

    <div class="mt-4 flex flex-col gap-2.5">
      <div
        v-for="t in topics"
        :key="t.topic"
        class="flex items-center justify-between gap-3 rounded-xl border border-gray-100 p-3 dark:border-neutral-800"
      >
        <div class="min-w-0">
          <p class="text-sm font-medium text-gray-900 dark:text-white">{{ t.title }}</p>
          <p class="truncate text-xs text-gray-400 dark:text-neutral-500">{{ t.blurb }}</p>
        </div>
        <UButton
          :color="voted[t.topic] ? 'gray' : 'emerald'"
          :variant="voted[t.topic] ? 'soft' : 'solid'"
          size="xs"
          class="shrink-0"
          :loading="busy[t.topic]"
          :disabled="voted[t.topic] || busy[t.topic]"
          :icon="voted[t.topic] ? 'i-heroicons-check-20-solid' : undefined"
          @click="vote(t.topic)"
        >
          {{ voted[t.topic] ? 'Voted' : 'Vote' }}
        </UButton>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const topics = [
  { topic: 'observability', title: 'Observability', blurb: 'Operate what you build — traces, metrics, logs' },
  { topic: 'ai-for-devs', title: 'AI for Developers', blurb: 'Ship faster with AI coding assistants' },
  { topic: 'ai-for-support', title: 'AI for IT Support', blurb: 'AI troubleshooting for support engineers & TSEs' },
]

const voted = reactive<Record<string, boolean>>({})
const busy = reactive<Record<string, boolean>>({})

async function vote(topic: string) {
  if (voted[topic] || busy[topic]) return
  busy[topic] = true
  try {
    // Authenticated endpoint — links the vote to the logged-in user + their email.
    await useApi().post('/waitlist', { topic, source: 'dashboard' })
    voted[topic] = true
  } catch {
    // Non-critical; leave the button votable so they can retry.
  } finally {
    busy[topic] = false
  }
}
</script>
