<template>
  <div class="rounded-2xl border border-gray-200/80 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
    <div class="mb-4 flex items-start justify-between">
      <div>
        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">What to build next</h2>
        <p class="mt-0.5 text-xs text-gray-500 dark:text-neutral-400">Waitlist votes per track</p>
      </div>
      <span class="text-2xl font-bold leading-none tracking-tight text-gray-900 dark:text-white">{{ total }}</span>
    </div>

    <div v-if="loading" class="flex justify-center py-6 text-emerald-500">
      <UIcon name="i-heroicons-arrow-path" class="animate-spin text-lg" />
    </div>

    <div v-else class="flex flex-col gap-3.5">
      <div v-for="t in topics" :key="t.topic">
        <div class="mb-1 flex items-center justify-between text-xs">
          <span class="font-medium text-gray-700 dark:text-neutral-300">{{ t.title }}</span>
          <span class="font-semibold text-gray-900 dark:text-white">{{ t.signups }}</span>
        </div>
        <div class="h-2 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-neutral-800">
          <div class="h-full rounded-full bg-emerald-500 transition-all" :style="`width:${pct(t.signups)}%`" />
        </div>
      </div>
      <p v-if="total === 0" class="pt-1 text-xs text-gray-400 dark:text-neutral-500">
        No votes yet — share the site to start measuring demand.
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
interface TopicCount { topic: string, title: string, signups: number }

const topics = ref<TopicCount[]>([])
const total = ref(0)
const loading = ref(true)

const maxSignups = computed(() => Math.max(1, ...topics.value.map(t => t.signups)))
function pct(n: number): number {
  return Math.round((n / maxSignups.value) * 100)
}

onMounted(async () => {
  try {
    const res = await useApi().get<{ topics: TopicCount[], total: number }>('/admin/waitlist')
    topics.value = res.topics
    total.value = res.total
  } catch {
    // A failed readout shouldn't break the dashboard; just show zero state.
  } finally {
    loading.value = false
  }
})
</script>
