<template>
  <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
    <!-- Header: root + total + legend -->
    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 border-b border-gray-100 px-4 py-3 dark:border-gray-800">
      <div class="min-w-0">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Trace</p>
        <p class="truncate font-mono text-sm font-semibold text-gray-900 dark:text-white">{{ trace.root ?? 'request' }}</p>
      </div>
      <div class="ml-auto flex items-center gap-3">
        <span class="font-mono text-sm font-bold tabular-nums text-gray-900 dark:text-white">{{ fmt(totalDur) }}</span>
      </div>
      <div class="flex w-full flex-wrap items-center gap-3 pt-1">
        <span v-for="s in usedServices" :key="s" class="flex items-center gap-1.5 text-[11px] text-gray-500 dark:text-gray-400">
          <span class="h-2.5 w-2.5 rounded-sm" :style="`background:${serviceColor(s)}`" />
          {{ s }}
        </span>
      </div>
    </div>

    <!-- Rows -->
    <div class="divide-y divide-gray-50 dark:divide-gray-800/60">
      <button
        v-for="row in rows"
        :key="row.id"
        type="button"
        class="grid w-full grid-cols-[minmax(0,1fr)_2fr] items-center gap-3 px-4 py-1.5 text-left transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50"
        :class="selectedId === row.id ? 'bg-emerald-50/70 dark:bg-emerald-950/30' : ''"
        @click="selectedId = selectedId === row.id ? null : row.id"
      >
        <!-- Label -->
        <span class="flex min-w-0 items-center gap-1.5" :style="`padding-left:${row.depth * 14}px`">
          <span class="h-2 w-2 shrink-0 rounded-sm" :style="`background:${serviceColor(row.service)}`" />
          <span class="truncate font-mono text-xs text-gray-700 dark:text-gray-300">{{ row.name }}</span>
          <span v-if="row.repeat && row.repeat > 1" class="shrink-0 rounded bg-gray-100 px-1 font-mono text-[10px] font-bold text-gray-500 dark:bg-gray-800 dark:text-gray-400">
            ×{{ row.repeat }}
          </span>
        </span>
        <!-- Bar track -->
        <span class="relative h-4">
          <span
            class="absolute top-1/2 h-3 -translate-y-1/2 rounded-sm"
            :style="`left:${row.leftPct}%; width:${row.widthPct}%; background:${serviceColor(row.service)}`"
          />
          <span
            class="absolute top-1/2 -translate-y-1/2 font-mono text-[10px] tabular-nums text-gray-400 dark:text-gray-500"
            :style="row.leftPct + row.widthPct > 82 ? `right:${100 - row.leftPct}%; padding-right:6px` : `left:${row.leftPct + row.widthPct}%; padding-left:6px`"
          >{{ fmt(row.effDur) }}</span>
        </span>
      </button>
    </div>

    <!-- Selected span detail -->
    <div v-if="selected" class="border-t border-gray-100 bg-gray-50/70 px-4 py-3 dark:border-gray-800 dark:bg-gray-800/40">
      <div class="grid grid-cols-2 gap-x-6 gap-y-1.5 sm:grid-cols-4">
        <div><p class="text-[10px] uppercase tracking-wider text-gray-400">Span</p><p class="truncate font-mono text-xs text-gray-800 dark:text-gray-200">{{ selected.name }}</p></div>
        <div><p class="text-[10px] uppercase tracking-wider text-gray-400">Service</p><p class="font-mono text-xs text-gray-800 dark:text-gray-200">{{ selected.service ?? '—' }}</p></div>
        <div><p class="text-[10px] uppercase tracking-wider text-gray-400">Kind</p><p class="font-mono text-xs text-gray-800 dark:text-gray-200">{{ selected.kind ?? '—' }}</p></div>
        <div>
          <p class="text-[10px] uppercase tracking-wider text-gray-400">Duration</p>
          <p class="font-mono text-xs text-gray-800 dark:text-gray-200">
            <template v-if="selected.repeat && selected.repeat > 1">
              {{ selected.repeat }} × {{ fmt(selected.dur ?? 0) }} = <strong>{{ fmt((selected.dur ?? 0) * selected.repeat) }}</strong>
            </template>
            <template v-else>{{ fmt(selected.dur ?? 0) }}</template>
          </p>
        </div>
      </div>
    </div>
    <p v-else class="px-4 py-2.5 text-[11px] text-gray-400 dark:text-gray-500">Click a span to inspect it.</p>
  </div>
</template>

<script setup lang="ts">
import type { TraceSpan } from '~/composables/usePaths'

const props = defineProps<{
  trace: { root?: string; spans: TraceSpan[] }
}>()

const selectedId = ref<string | null>(null)

const SERVICE_COLORS: Record<string, string> = {
  web: '#10b981', // emerald
  db: '#3b82f6', // blue
  ext: '#f59e0b', // amber
  cache: '#8b5cf6', // violet
  queue: '#ec4899', // pink
}
function serviceColor(service?: string): string {
  return (service && SERVICE_COLORS[service]) || '#94a3b8' // slate fallback
}

// Effective duration accounts for repeated spans (an N+1 shows its true cost).
function effDur(s: TraceSpan): number {
  return (s.dur ?? 0) * (s.repeat && s.repeat > 1 ? s.repeat : 1)
}

const totalDur = computed(() => {
  const root = props.trace.spans.find(s => s.parent === null)
  if (root) return effDur(root) || (root.dur ?? 0)
  // Fallback: max span end.
  return Math.max(1, ...props.trace.spans.map(s => (s.start ?? 0) + effDur(s)))
})

const usedServices = computed(() =>
  [...new Set(props.trace.spans.map(s => s.service).filter(Boolean))] as string[],
)

const selected = computed(() => props.trace.spans.find(s => s.id === selectedId.value) ?? null)

// Depth from the parent chain (capped to avoid runaway on malformed data).
function depthOf(span: TraceSpan): number {
  const byId = new Map(props.trace.spans.map(s => [s.id, s]))
  let depth = 0
  let cur = span
  while (cur.parent && byId.has(cur.parent) && depth < 12) {
    cur = byId.get(cur.parent)!
    depth++
  }
  return depth
}

const rows = computed(() => {
  const total = totalDur.value || 1
  return props.trace.spans.map((s) => {
    const d = effDur(s)
    const leftPct = Math.min(98, ((s.start ?? 0) / total) * 100)
    const widthPct = Math.max(1.5, Math.min(100 - leftPct, (d / total) * 100))
    return { ...s, depth: depthOf(s), effDur: d, leftPct, widthPct }
  })
})

function fmt(ms: number): string {
  if (ms >= 1000) return `${(ms / 1000).toFixed(ms % 1000 === 0 ? 0 : 2)}s`
  return `${Math.round(ms)}ms`
}
</script>
