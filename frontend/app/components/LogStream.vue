<template>
  <div class="overflow-hidden rounded-xl border border-gray-200 bg-gray-950 dark:border-gray-700">
    <div class="flex items-center justify-between border-b border-white/10 px-4 py-2">
      <p class="font-mono text-[11px] font-semibold uppercase tracking-wider text-gray-400">Logs</p>
      <button
        v-if="correlationIds.length > 1"
        type="button"
        class="font-mono text-[11px] text-gray-400 transition-colors hover:text-emerald-400"
        @click="filterId = null"
      >
        {{ filterId ? 'clear filter' : '' }}
      </button>
    </div>
    <div class="max-h-72 overflow-y-auto p-3 font-mono text-xs leading-relaxed">
      <div
        v-for="(line, i) in visibleLogs"
        :key="i"
        class="flex flex-wrap items-baseline gap-x-2 gap-y-0.5 rounded px-1.5 py-0.5 hover:bg-white/5"
      >
        <span class="text-gray-500">{{ line.t }}</span>
        <span class="font-bold" :class="levelColor(line.level)">{{ (line.level ?? 'INFO').padEnd(5) }}</span>
        <button
          v-if="line.request_id"
          type="button"
          class="rounded bg-white/10 px-1 text-[11px] text-sky-300 transition-colors hover:bg-white/20"
          :class="filterId === line.request_id ? 'ring-1 ring-sky-400' : ''"
          @click="filterId = filterId === line.request_id ? null : line.request_id!"
        >{{ line.request_id }}</button>
        <span class="text-gray-200">{{ line.msg }}</span>
      </div>
      <p v-if="!visibleLogs.length" class="px-1.5 text-gray-500">No log lines.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  logs: Array<{ t?: string; level?: string; request_id?: string; msg: string }>
}>()

const filterId = ref<string | null>(null)

const correlationIds = computed(() =>
  [...new Set(props.logs.map(l => l.request_id).filter(Boolean))] as string[],
)

const visibleLogs = computed(() =>
  filterId.value ? props.logs.filter(l => l.request_id === filterId.value) : props.logs,
)

function levelColor(level?: string): string {
  switch ((level ?? '').toUpperCase()) {
    case 'ERROR': return 'text-red-400'
    case 'WARN': return 'text-amber-400'
    case 'DEBUG': return 'text-gray-500'
    default: return 'text-emerald-400'
  }
}
</script>
