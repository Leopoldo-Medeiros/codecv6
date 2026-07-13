<template>
  <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
    <div class="mb-2 flex items-baseline justify-between gap-3">
      <p class="font-mono text-xs font-semibold text-gray-700 dark:text-gray-300">{{ metric.title }}</p>
      <p class="font-mono text-sm font-bold tabular-nums text-gray-900 dark:text-white">
        {{ lastValue }}<span class="text-xs font-normal text-gray-400">{{ metric.unit ? ' ' + metric.unit : '' }}</span>
      </p>
    </div>
    <svg :viewBox="`0 0 ${W} ${H}`" class="w-full" :style="`height:${H}px`" preserveAspectRatio="none" role="img" :aria-label="metric.title">
      <!-- area -->
      <path :d="areaPath" :fill="`url(#grad-${uid})`" />
      <!-- line -->
      <path :d="linePath" fill="none" stroke="#10b981" stroke-width="2" vector-effect="non-scaling-stroke" />
      <!-- threshold -->
      <line
        v-if="thresholdY !== null"
        :x1="0" :x2="W" :y1="thresholdY" :y2="thresholdY"
        stroke="#ef4444" stroke-width="1" stroke-dasharray="4 3" vector-effect="non-scaling-stroke"
      />
      <!-- endpoint dot -->
      <circle :cx="lastPoint.x" :cy="lastPoint.y" r="3" fill="#10b981" />
      <defs>
        <linearGradient :id="`grad-${uid}`" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" stop-color="#10b981" stop-opacity="0.28" />
          <stop offset="100%" stop-color="#10b981" stop-opacity="0" />
        </linearGradient>
      </defs>
    </svg>
    <div class="mt-1 flex justify-between font-mono text-[10px] text-gray-400 dark:text-gray-500">
      <span>t={{ series[0]?.[0] }}</span>
      <span v-if="metric.threshold != null" class="text-red-500">threshold {{ metric.threshold }}{{ metric.unit ? ' ' + metric.unit : '' }}</span>
      <span>t={{ series[series.length - 1]?.[0] }}</span>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  metric: { title: string; unit?: string; series: Array<[number, number]>; threshold?: number }
}>()

const W = 400
const H = 90
const uid = Math.random().toString(36).slice(2, 8)

const series = computed(() => props.metric.series ?? [])

const bounds = computed(() => {
  const ys = series.value.map(p => p[1])
  const xs = series.value.map(p => p[0])
  const yValues = props.metric.threshold != null ? [...ys, props.metric.threshold] : ys
  const minY = Math.min(...yValues)
  const maxY = Math.max(...yValues)
  return {
    minX: Math.min(...xs),
    maxX: Math.max(...xs),
    minY: minY === maxY ? minY - 1 : minY,
    maxY: minY === maxY ? maxY + 1 : maxY,
  }
})

function px(x: number): number {
  const { minX, maxX } = bounds.value
  return maxX === minX ? 0 : ((x - minX) / (maxX - minX)) * W
}
function py(y: number): number {
  const { minY, maxY } = bounds.value
  const pad = 6
  return H - pad - ((y - minY) / (maxY - minY)) * (H - pad * 2)
}

const points = computed(() => series.value.map(([x, y]) => ({ x: px(x), y: py(y) })))
const linePath = computed(() => points.value.map((p, i) => `${i ? 'L' : 'M'}${p.x.toFixed(1)},${p.y.toFixed(1)}`).join(' '))
const areaPath = computed(() => {
  if (!points.value.length) return ''
  const first = points.value[0]!
  const last = points.value[points.value.length - 1]!
  return `${linePath.value} L${last.x.toFixed(1)},${H} L${first.x.toFixed(1)},${H} Z`
})
const lastPoint = computed(() => points.value[points.value.length - 1] ?? { x: 0, y: 0 })
const lastValue = computed(() => series.value[series.value.length - 1]?.[1] ?? '—')
const thresholdY = computed(() => (props.metric.threshold != null ? py(props.metric.threshold) : null))
</script>
