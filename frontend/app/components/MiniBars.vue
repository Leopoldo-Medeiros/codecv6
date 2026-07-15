<template>
  <div class="flex items-end gap-[2px]" :style="`height:${height}px`">
    <span
      v-for="(v, i) in values"
      :key="i"
      class="flex-1 rounded-[2px]"
      :style="barStyle(v, i)"
    />
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  values: number[]
  color?: string
  height?: number
}>()

const color = props.color ?? '#10b981'
const height = props.height ?? 40

const max = computed(() => Math.max(1, ...props.values))

function barStyle(v: number, i: number): string {
  const pct = Math.max(8, (v / max.value) * 100)
  // The most recent bar (last) is full-opacity; older bars fade slightly.
  const isLast = i === props.values.length - 1
  const opacity = v === 0 ? 0.18 : (isLast ? 1 : 0.55)
  return `height:${pct}%;background:${color};opacity:${opacity}`
}
</script>
