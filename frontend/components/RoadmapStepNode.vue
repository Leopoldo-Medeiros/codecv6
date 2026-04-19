<template>
  <div
    class="rn"
    :class="[`rn--${status}`, entered && 'rn--entered']"
    @mouseenter="onEnter"
    @mouseleave="onLeave"
    @mousemove="onMove"
    :style="tiltStyle"
  >
    <!-- Animated gradient border for in_progress -->
    <div v-if="status === 'in_progress'" class="rn__border-anim" />

    <!-- Glass surface -->
    <div class="rn__glass" />

    <!-- Inner shine highlight -->
    <div class="rn__shine" :style="shineStyle" />

    <!-- Content -->
    <div class="rn__content">
      <!-- Badge + title row -->
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

      <!-- Meta row -->
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

    <!-- Glow orb -->
    <div class="rn__orb" :class="`rn__orb--${status}`" />

    <Handle type="target" :position="Position.Top"    style="opacity:0;pointer-events:none;background:transparent;border:none" />
    <Handle type="source" :position="Position.Bottom" style="opacity:0;pointer-events:none;background:transparent;border:none" />
  </div>
</template>

<script setup lang="ts">
import { Handle, Position } from '@vue-flow/core'

const props = defineProps<{
  data: {
    order: number
    title: string
    course?: string | null
    resourceCount?: number
    status?: 'not_started' | 'in_progress' | 'done'
    step: any
  }
}>()

defineEmits(['click'])

const status = computed(() => props.data.status ?? 'not_started')

const statusLabel = computed(() => ({
  done:        'Done',
  in_progress: 'In Progress',
  not_started: 'Not Started',
}[status.value]))

// ── 3D tilt ──────────────────────────────────────────────
const tiltX    = ref(0)
const tiltY    = ref(0)
const shineX   = ref(50)
const shineY   = ref(50)
const entered  = ref(false)
let   raf      = 0

function onEnter() { entered.value = true }
function onLeave() {
  cancelAnimationFrame(raf)
  raf = requestAnimationFrame(() => {
    tiltX.value  = 0
    tiltY.value  = 0
    shineX.value = 50
    shineY.value = 50
  })
}

function onMove(e: MouseEvent) {
  const el   = e.currentTarget as HTMLElement
  const rect = el.getBoundingClientRect()
  const x    = (e.clientX - rect.left) / rect.width   // 0-1
  const y    = (e.clientY - rect.top)  / rect.height  // 0-1
  raf = requestAnimationFrame(() => {
    tiltY.value  =  (x - 0.5) * 16
    tiltX.value  = -(y - 0.5) * 10
    shineX.value = x * 100
    shineY.value = y * 100
  })
}

const tiltStyle = computed(() => ({
  transform: `perspective(900px) rotateX(${tiltX.value}deg) rotateY(${tiltY.value}deg) translateZ(0)`,
}))

const shineStyle = computed(() => ({
  background: `radial-gradient(circle at ${shineX.value}% ${shineY.value}%, rgba(255,255,255,0.18) 0%, transparent 65%)`,
}))
</script>

<style scoped>
/* ── Root ─────────────────────────────────────────────── */
.rn {
  position: relative;
  width: 290px;
  border-radius: 16px;
  padding: 14px 16px;
  cursor: pointer;
  isolation: isolate;
  overflow: hidden;
  transition: transform 0.08s linear, box-shadow 0.25s ease;
  will-change: transform;
  opacity: 0;
  translate: 0 12px;
}
.rn--entered {
  opacity: 1;
  translate: 0 0;
  transition: opacity 0.4s ease, translate 0.4s ease, transform 0.08s linear, box-shadow 0.25s ease;
}

/* Status variants */
.rn--not_started {
  background: rgba(15, 23, 42, 0.72);
  border: 1px solid rgba(99, 102, 241, 0.2);
  box-shadow: 0 4px 24px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.06);
}
.rn--in_progress {
  background: rgba(15, 23, 42, 0.80);
  border: 1px solid transparent;
  box-shadow: 0 4px 32px rgba(99,102,241,0.25), 0 0 60px rgba(99,102,241,0.08);
}
.rn--done {
  background: rgba(5, 46, 22, 0.65);
  border: 1px solid rgba(34, 197, 94, 0.3);
  box-shadow: 0 4px 24px rgba(34,197,94,0.18), inset 0 1px 0 rgba(255,255,255,0.05);
}

.rn--not_started:hover { box-shadow: 0 16px 40px rgba(0,0,0,0.6), 0 0 30px rgba(99,102,241,0.2); }
.rn--in_progress:hover { box-shadow: 0 16px 48px rgba(99,102,241,0.4), 0 0 60px rgba(99,102,241,0.15); }
.rn--done:hover        { box-shadow: 0 16px 40px rgba(34,197,94,0.3), 0 0 40px rgba(34,197,94,0.12); }

/* ── Animated gradient border (in_progress) ──────────── */
.rn__border-anim {
  position: absolute;
  inset: -2px;
  border-radius: 18px;
  z-index: -1;
  background: conic-gradient(from var(--angle, 0deg), #6366f1, #06b6d4, #8b5cf6, #ec4899, #6366f1);
  animation: spin-border 3s linear infinite;
}
@property --angle { syntax: '<angle>'; initial-value: 0deg; inherits: false; }
@keyframes spin-border { to { --angle: 360deg; } }

/* ── Glass surface ───────────────────────────────────── */
.rn__glass {
  position: absolute;
  inset: 0;
  border-radius: inherit;
  backdrop-filter: blur(16px) saturate(180%);
  -webkit-backdrop-filter: blur(16px) saturate(180%);
  pointer-events: none;
  z-index: 0;
}

/* ── Mouse-follow shine ──────────────────────────────── */
.rn__shine {
  position: absolute;
  inset: 0;
  border-radius: inherit;
  pointer-events: none;
  z-index: 1;
  transition: background 0.05s;
}

/* ── Content ─────────────────────────────────────────── */
.rn__content {
  position: relative;
  z-index: 2;
}

.rn__header {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin-bottom: 8px;
}

/* Badge */
.rn__badge {
  flex-shrink: 0;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 800;
  margin-top: 1px;
}
.rn__badge--not_started {
  background: rgba(99,102,241,0.15);
  border: 1.5px solid rgba(99,102,241,0.4);
  color: #a5b4fc;
}
.rn__badge--in_progress {
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  border: none;
  color: #fff;
  box-shadow: 0 0 12px rgba(99,102,241,0.6);
}
.rn__badge--done {
  background: linear-gradient(135deg, #22c55e, #16a34a);
  border: none;
  color: #fff;
  box-shadow: 0 0 12px rgba(34,197,94,0.5);
}
.rn__check { width: 13px; height: 13px; }
.rn__num { line-height: 1; }

/* Title */
.rn__title {
  font-size: 0.8125rem;
  font-weight: 600;
  color: #e2e8f0;
  line-height: 1.4;
  flex: 1;
  min-width: 0;
  font-family: 'Inter', system-ui, sans-serif;
}
.rn__title--done {
  color: #4b7a5e;
  text-decoration: line-through;
}

/* Meta tags */
.rn__meta {
  display: flex;
  flex-wrap: wrap;
  gap: 5px;
  margin-top: 4px;
}
.rn__tag {
  display: inline-flex;
  align-items: center;
  gap: 3px;
  padding: 2px 7px;
  border-radius: 99px;
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  font-family: 'Inter', system-ui, sans-serif;
}
.rn__tag svg { width: 9px; height: 9px; }
.rn__tag--course      { background: rgba(99,102,241,0.15); color: #a5b4fc; border: 1px solid rgba(99,102,241,0.25); }
.rn__tag--res         { background: rgba(14,165,233,0.12); color: #7dd3fc; border: 1px solid rgba(14,165,233,0.2); }
.rn__tag--not_started { background: rgba(99,102,241,0.1);  color: #818cf8; border: 1px solid rgba(99,102,241,0.2); }
.rn__tag--in_progress { background: rgba(139,92,246,0.15); color: #c4b5fd; border: 1px solid rgba(139,92,246,0.3); }
.rn__tag--done        { background: rgba(34,197,94,0.12);  color: #86efac; border: 1px solid rgba(34,197,94,0.25); }

/* ── Glow orb (decorative) ───────────────────────────── */
.rn__orb {
  position: absolute;
  width: 120px;
  height: 120px;
  border-radius: 50%;
  right: -40px;
  bottom: -50px;
  pointer-events: none;
  z-index: 0;
  filter: blur(40px);
  opacity: 0.4;
}
.rn__orb--not_started { background: rgba(99,102,241,0.4); }
.rn__orb--in_progress { background: rgba(139,92,246,0.6); animation: pulse-orb 2s ease-in-out infinite; }
.rn__orb--done        { background: rgba(34,197,94,0.4); }

@keyframes pulse-orb {
  0%, 100% { opacity: 0.35; transform: scale(1); }
  50%       { opacity: 0.6;  transform: scale(1.15); }
}
</style>
