<template>
  <svg :viewBox="`0 0 ${W} ${H}`" class="block h-full w-full" preserveAspectRatio="none" role="img" :aria-label="ariaLabel">
    <defs>
      <linearGradient :id="`ac-${uid}`" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0%" stop-color="#10b981" stop-opacity="0.30" />
        <stop offset="100%" stop-color="#10b981" stop-opacity="0" />
      </linearGradient>
    </defs>
    <path v-if="areaPath" :d="areaPath" :fill="`url(#ac-${uid})`" />
    <path v-if="linePath" :d="linePath" fill="none" stroke="#10b981" stroke-width="2" vector-effect="non-scaling-stroke" stroke-linejoin="round" />
    <circle v-if="last" :cx="last.x" :cy="last.y" r="3.5" fill="#10b981" />
  </svg>
</template>

<script setup lang="ts">
const props = defineProps<{
  values: number[]
  ariaLabel?: string
}>()

const W = 600
const H = 160
const uid = Math.random().toString(36).slice(2, 8)

const pts = computed(() => {
  const v = props.values
  if (!v.length) return [] as Array<{ x: number, y: number }>
  const min = Math.min(...v)
  const max = Math.max(...v)
  const pad = 10
  const span = max - min || 1
  const stepX = v.length > 1 ? W / (v.length - 1) : 0
  return v.map((val, i) => ({
    x: v.length > 1 ? i * stepX : W / 2,
    y: H - pad - ((val - min) / span) * (H - pad * 2),
  }))
})

const linePath = computed(() =>
  pts.value.map((p, i) => `${i ? 'L' : 'M'}${p.x.toFixed(1)},${p.y.toFixed(1)}`).join(' '),
)
const areaPath = computed(() => {
  if (!pts.value.length) return ''
  const first = pts.value[0]!
  const lastP = pts.value[pts.value.length - 1]!
  return `${linePath.value} L${lastP.x.toFixed(1)},${H} L${first.x.toFixed(1)},${H} Z`
})
const last = computed(() => pts.value[pts.value.length - 1] ?? null)
</script>
