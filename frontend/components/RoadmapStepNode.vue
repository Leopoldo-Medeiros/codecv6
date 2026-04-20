<template>
  <div
    class="rn"
    :class="[`rn--${status}`, isDark ? 'rn--dark' : 'rn--light']"
    :style="nodeStyle"
    @mouseleave="onLeave"
    @mousemove="onMove"
  >
    <div v-if="status === 'in_progress'" class="rn__border-anim" />
    <div class="rn__shine" :style="shineStyle" />

    <div class="rn__content">
      <div class="rn__header">
        <div class="rn__badge" :class="`rn__badge--${status}`">
          <svg v-if="status === 'done'" viewBox="0 0 14 14" fill="none" class="rn__check">
            <path d="M2 7l4 4 6-6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <svg v-else-if="status === 'in_progress'" viewBox="0 0 14 14" fill="none" class="rn__check">
            <circle cx="7" cy="7" r="3" fill="currentColor"/>
          </svg>
          <span v-else class="rn__num">{{ data.order }}</span>
        </div>
        <p class="rn__title" :class="status === 'done' && 'rn__title--done'">
          {{ data.title }}
        </p>
      </div>

      <div class="rn__meta">
        <span v-if="data.course" class="rn__tag rn__tag--course">
          <svg viewBox="0 0 12 12" fill="none"><path d="M1 3l5 2.5L11 3M1 3v6l5 2.5M1 3l5-2.5L11 3v6L6 11.5" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/></svg>
          {{ data.course }}
        </span>
        <span v-if="data.resourceCount" class="rn__tag rn__tag--res">
          <svg viewBox="0 0 12 12" fill="none"><path d="M3 6h6M3 3.5h6M3 8.5h4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
          {{ data.resourceCount }}
        </span>
        <span class="rn__tag" :class="`rn__tag--${status}`">{{ statusLabel }}</span>
      </div>
    </div>

    <Handle type="target" :position="(props.targetPosition as Position) ?? Position.Top"    style="opacity:0;pointer-events:none;background:transparent;border:none" />
    <Handle type="source" :position="(props.sourcePosition as Position) ?? Position.Bottom" style="opacity:0;pointer-events:none;background:transparent;border:none" />
  </div>
</template>

<script setup lang="ts">
import { Handle, Position } from '@vue-flow/core'

const props = defineProps<{
  data: {
    order: number
    index: number
    title: string
    course?: string | null
    resourceCount?: number
    status?: 'not_started' | 'in_progress' | 'done'
    step: any
  }
  sourcePosition?: string
  targetPosition?: string
}>()

defineEmits(['click'])

const colorMode = useColorMode()
const isDark    = computed(() => colorMode.value === 'dark')
const status    = computed(() => props.data.status ?? 'not_started')
const statusLabel = computed(() => ({ done: 'Done', in_progress: 'In Progress', not_started: 'Not Started' }[status.value]))

const tiltX = ref(0), tiltY = ref(0), shineX = ref(50), shineY = ref(50)
let raf = 0

function onLeave() {
  cancelAnimationFrame(raf)
  raf = requestAnimationFrame(() => { tiltX.value = 0; tiltY.value = 0; shineX.value = 50; shineY.value = 50 })
}
function onMove(e: MouseEvent) {
  const r = (e.currentTarget as HTMLElement).getBoundingClientRect()
  const x = (e.clientX - r.left) / r.width
  const y = (e.clientY - r.top) / r.height
  raf = requestAnimationFrame(() => {
    tiltY.value = (x - 0.5) * 12; tiltX.value = -(y - 0.5) * 8
    shineX.value = x * 100; shineY.value = y * 100
  })
}

const nodeStyle = computed(() => ({
  transform: `perspective(900px) rotateX(${tiltX.value}deg) rotateY(${tiltY.value}deg) translateZ(0)`,
  animationDelay: `${(props.data.index ?? 0) * 55}ms`,
}))
const shineStyle = computed(() => ({
  background: `radial-gradient(circle at ${shineX.value}% ${shineY.value}%, rgba(255,255,255,0.08) 0%, transparent 65%)`,
}))
</script>

<style scoped>
/* NR colour tokens
   --nr-green:       #00AC69  (brand primary)
   --nr-green-dark:  #007D4A  (hover / border)
   --nr-green-dim:   rgba(0,172,105,x) */

.rn {
  position: relative;
  width: 240px;
  border-radius: 10px;
  padding: 12px 14px;
  cursor: pointer;
  isolation: isolate;
  overflow: hidden;
  will-change: transform;
  transition: transform 0.08s linear, box-shadow 0.2s ease;
  animation: node-in 0.3s ease both;
}
@keyframes node-in {
  from { opacity: 0; translate: 0 8px; }
  to   { opacity: 1; translate: 0 0; }
}

/* ── Dark (NR product dark theme: charcoal #1D252C) ───── */
.rn--dark.rn--not_started {
  background: #1d252c;
  border: 1px solid rgba(0,172,105,0.3);
  box-shadow: 0 2px 10px rgba(0,0,0,0.45);
}
.rn--dark.rn--in_progress {
  background: #1f2a2e;
  border: 1px solid transparent;
  box-shadow: 0 2px 14px rgba(0,172,105,0.18);
}
.rn--dark.rn--done {
  background: #1a2820;
  border: 1px solid rgba(0,172,105,0.35);
  box-shadow: 0 2px 10px rgba(0,0,0,0.4);
}
.rn--dark.rn--not_started:hover { box-shadow: 0 4px 18px rgba(0,0,0,0.55), 0 0 12px rgba(0,172,105,0.12); }
.rn--dark.rn--in_progress:hover { box-shadow: 0 4px 20px rgba(0,172,105,0.22); }
.rn--dark.rn--done:hover        { box-shadow: 0 4px 18px rgba(0,172,105,0.2); }

/* Title dark */
.rn--dark .rn__title      { color: #e8edf0; }
.rn--dark .rn__title--done { color: #6e7a87; text-decoration: line-through; }

/* ── Light ──────────────────────────────────────────────── */
.rn--light.rn--not_started {
  background: #ffffff;
  border: 1px solid #b2dfce;
  box-shadow: 0 1px 6px rgba(0,0,0,0.07);
}
.rn--light.rn--in_progress {
  background: #f0faf5;
  border: 1px solid transparent;
  box-shadow: 0 2px 10px rgba(0,172,105,0.14), 0 0 0 1px #7ecfb0;
}
.rn--light.rn--done {
  background: #e8f7f0;
  border: 1px solid #7ecfb0;
  box-shadow: 0 1px 6px rgba(0,0,0,0.05);
}
.rn--light.rn--not_started:hover { box-shadow: 0 4px 14px rgba(0,0,0,0.1), 0 0 0 1px #7ecfb0; }
.rn--light.rn--in_progress:hover { box-shadow: 0 4px 16px rgba(0,172,105,0.18), 0 0 0 1px #00AC69; }
.rn--light.rn--done:hover        { box-shadow: 0 4px 14px rgba(0,172,105,0.16), 0 0 0 1px #00AC69; }

.rn--light .rn__title      { color: #1a2e25; }
.rn--light .rn__title--done { color: #8f9ab8; text-decoration: line-through; }

/* ── Animated border (in_progress) ─────────────────────── */
.rn__border-anim {
  position: absolute; inset: -2px; border-radius: 12px; z-index: -1;
  background: conic-gradient(from var(--angle, 0deg), #00AC69, #00d97e, #007D4A, #00AC69);
  animation: spin-border 4s linear infinite;
}
@property --angle { syntax: '<angle>'; initial-value: 0deg; inherits: false; }
@keyframes spin-border { to { --angle: 360deg; } }

/* Shine */
.rn__shine { position: absolute; inset: 0; border-radius: inherit; pointer-events: none; z-index: 1; transition: background 0.05s; }

/* Content */
.rn__content { position: relative; z-index: 2; }
.rn__header  { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 8px; }

/* Badge */
.rn__badge {
  flex-shrink: 0; width: 24px; height: 24px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 10px; font-weight: 800; margin-top: 1px;
}
.rn__badge--not_started { background: rgba(0,172,105,0.15); border: 1.5px solid rgba(0,172,105,0.5); color: #00AC69; }
.rn__badge--in_progress { background: #00AC69; border: none; color: #fff; }
.rn__badge--done        { background: #007D4A; border: none; color: #fff; }
.rn__check { width: 12px; height: 12px; }
.rn__num   { line-height: 1; }

/* Title base */
.rn__title { font-size: 0.8rem; font-weight: 600; line-height: 1.4; flex: 1; min-width: 0; font-family: 'Inter', system-ui, sans-serif; }

/* Meta */
.rn__meta { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 6px; }
.rn__tag {
  display: inline-flex; align-items: center; gap: 3px; padding: 2px 6px;
  border-radius: 4px; font-size: 9px; font-weight: 600; letter-spacing: 0.03em;
  text-transform: uppercase; font-family: 'Inter', system-ui, sans-serif;
}
.rn__tag svg { width: 9px; height: 9px; }

/* Light tags */
.rn__tag--course      { background: #e0f5ec; color: #007D4A; border: 1px solid #b2dfce; }
.rn__tag--res         { background: #e0f5ec; color: #007D4A; border: 1px solid #b2dfce; }
.rn__tag--not_started { background: #f0f5f3; color: #5a7a6e; border: 1px solid #c8ddd6; }
.rn__tag--in_progress { background: #e0f5ec; color: #007D4A; border: 1px solid #7ecfb0; }
.rn__tag--done        { background: #d0eedf; color: #005c34; border: 1px solid #7ecfb0; }

/* Dark tag overrides */
.rn--dark .rn__tag--course      { background: rgba(0,172,105,0.14); color: #5dd9a4; border-color: rgba(0,172,105,0.28); }
.rn--dark .rn__tag--res         { background: rgba(0,172,105,0.12); color: #5dd9a4; border-color: rgba(0,172,105,0.24); }
.rn--dark .rn__tag--not_started { background: rgba(110,122,135,0.2); color: #8f9ab8; border-color: rgba(110,122,135,0.3); }
.rn--dark .rn__tag--in_progress { background: rgba(0,172,105,0.18); color: #5dd9a4; border-color: rgba(0,172,105,0.32); }
.rn--dark .rn__tag--done        { background: rgba(0,172,105,0.22); color: #3dcc8f; border-color: rgba(0,172,105,0.38); }
</style>
