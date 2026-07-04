<template>
  <div class="rc" :class="isDark ? 'rc--dark' : 'rc--light'">
    <span class="rc__dot" />
    <span class="rc__label">{{ data.label }}</span>
    <span v-if="data.overflow" class="rc__overflow">+{{ data.overflow }}</span>
    <Handle type="target" :position="targetPosition" style="opacity:0;pointer-events:none;background:transparent;border:none" />
  </div>
</template>

<script setup lang="ts">
import { Handle, Position } from '@vue-flow/core'

const props = defineProps<{
  data: { label: string; overflow?: number; side?: 'left' | 'right' }
}>()

const colorMode = useColorMode()
const isDark = computed(() => colorMode.value === 'dark')

// The chip sits to the step's `side` — so it must face back toward the step:
// a chip placed on the right faces left, and vice versa.
const targetPosition = computed(() => (props.data.side === 'left' ? Position.Right : Position.Left))
</script>

<style scoped>
.rc {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  width: 132px;
  padding: 7px 10px;
  border-radius: 999px;
  font-size: 10.5px;
  font-weight: 600;
  letter-spacing: 0.01em;
  cursor: default;
  animation: rc-in 0.35s ease both;
}
@keyframes rc-in {
  from { opacity: 0; translate: -4px 0; }
  to   { opacity: 1; translate: 0 0; }
}

.rc__dot { width: 5px; height: 5px; border-radius: 50%; background: currentColor; flex-shrink: 0; }
.rc__label { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1; min-width: 0; }
.rc__overflow { opacity: 0.65; font-size: 9.5px; flex-shrink: 0; }

.rc--dark {
  background: rgba(217, 155, 49, 0.1);
  border: 1px dashed rgba(217, 155, 49, 0.45);
  color: #e8b969;
}
.rc--light {
  background: #fdf3e3;
  border: 1px dashed #d9a441;
  color: #8a5f19;
}
</style>
