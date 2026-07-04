<template>
  <div class="rr" :class="isDark ? 'rr--dark' : 'rr--light'">
    <div class="rr__head">
      <svg viewBox="0 0 12 12" fill="none" class="rr__icon">
        <path d="M3 6h6M3 3.5h6M3 8.5h4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" />
      </svg>
      Resources
    </div>
    <!-- @click.stop only blocks native click bubbling; Vue Flow's own node-click
         detection runs on mousedown/mouseup, not click, so the real guard against
         triggering step navigation is the `node.type !== 'step'` check in
         RoadmapFlow.vue's onNodeClick. -->
    <a
      v-for="(res, i) in data.resources"
      :key="i"
      :href="res.url"
      target="_blank"
      rel="noopener noreferrer"
      class="rr__link"
      @click.stop
    >
      {{ res.label }}
    </a>
    <span v-if="data.overflow" class="rr__more">+{{ data.overflow }} more</span>
    <Handle type="target" :position="targetPosition" style="opacity:0;pointer-events:none;background:transparent;border:none" />
  </div>
</template>

<script setup lang="ts">
import { Handle, Position } from '@vue-flow/core'

const props = defineProps<{
  data: { resources: Array<{ label: string; url: string }>; overflow?: number; side?: 'left' | 'right' }
}>()

const colorMode = useColorMode()
const isDark = computed(() => colorMode.value === 'dark')

// The box sits to the step's `side` — so it must face back toward the step:
// a box placed on the right faces left, and vice versa.
const targetPosition = computed(() => (props.data.side === 'left' ? Position.Right : Position.Left))
</script>

<style scoped>
.rr {
  display: flex;
  flex-direction: column;
  gap: 3px;
  width: 176px;
  padding: 10px 12px;
  border-radius: 10px;
  font-size: 11px;
  animation: rr-in 0.35s ease both;
}
@keyframes rr-in {
  from { opacity: 0; translate: -4px 0; }
  to   { opacity: 1; translate: 0 0; }
}

.rr__head {
  display: flex;
  align-items: center;
  gap: 5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-size: 9px;
  margin-bottom: 3px;
  opacity: 0.85;
}
.rr__icon { width: 10px; height: 10px; }

.rr__link {
  display: block;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  text-decoration: none;
  font-weight: 600;
  padding: 2px 0;
}
.rr__link:hover { text-decoration: underline; }
.rr__more { opacity: 0.6; font-size: 10px; margin-top: 1px; }

.rr--dark {
  background: #241c10;
  border: 1px solid rgba(217, 155, 49, 0.35);
  color: #e8b969;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
}
.rr--dark .rr__link { color: #f0c988; }

.rr--light {
  background: #fdf6ea;
  border: 1px solid #d9a441;
  color: #7a541a;
  box-shadow: 0 1px 6px rgba(0, 0, 0, 0.06);
}
.rr--light .rr__link { color: #8a5f19; }
</style>
