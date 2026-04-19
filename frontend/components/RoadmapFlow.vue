<template>
  <ClientOnly>
    <div class="rf-wrap" :style="{ height: wrapHeight }">
      <VueFlow
        :nodes="nodes"
        :edges="edges"
        :node-types="nodeTypes"
        fit-view-on-init
        :min-zoom="0.3"
        :max-zoom="1.8"
        :nodes-draggable="false"
        :nodes-connectable="false"
        :elements-selectable="false"
        class="rf-flow"
        @node-click="onNodeClick"
      >
        <!-- Dark dot grid -->
        <Background
          variant="dots"
          :gap="28"
          :size="1.2"
          pattern-color="rgba(99,102,241,0.2)"
        />

        <!-- Minimap for long paths -->
        <MiniMap
          v-if="steps.length > 5"
          :node-color="minimapColor"
          :mask-color="'rgba(0,0,0,0.6)'"
          class="rf-minimap"
        />

        <Controls :show-interactive="false" class="rf-controls" />
      </VueFlow>
    </div>

    <template #fallback>
      <div class="rf-fallback">Loading roadmap…</div>
    </template>
  </ClientOnly>
</template>

<script setup lang="ts">
import { VueFlow } from '@vue-flow/core'
import { Background } from '@vue-flow/background'
import { MiniMap } from '@vue-flow/minimap'
import { Controls } from '@vue-flow/controls'
import '@vue-flow/core/dist/style.css'
import '@vue-flow/core/dist/theme-default.css'
import '@vue-flow/minimap/dist/style.css'
import '@vue-flow/controls/dist/style.css'
import RoadmapStepNode from '~/components/RoadmapStepNode.vue'

interface Step {
  id: number
  title: string
  order: number
  course?: { id: number; name: string } | null
  resources?: any[]
  user_status?: 'not_started' | 'in_progress' | 'done'
}

const props = defineProps<{ steps: Step[] }>()
const emit  = defineEmits<{ (e: 'nodeClick', step: Step): void }>()

const nodeTypes = { step: markRaw(RoadmapStepNode) }

const NODE_H = 100
const GAP    = 52

const nodes = computed(() =>
  props.steps.map((step, i) => ({
    id:       String(step.id),
    type:     'step',
    position: { x: 0, y: i * (NODE_H + GAP) },
    data: {
      order:         step.order ?? i + 1,
      title:         step.title,
      course:        step.course?.name ?? null,
      resourceCount: (step.resources ?? []).length,
      status:        step.user_status,
      step,
    },
  }))
)

const edges = computed(() =>
  props.steps.slice(0, -1).map((step, i) => {
    const next   = props.steps[i + 1]
    const isDone = step.user_status === 'done'
    const isWip  = step.user_status === 'in_progress'
    return {
      id:        `e${step.id}-${next.id}`,
      source:    String(step.id),
      target:    String(next.id),
      animated:  isWip,
      type:      'smoothstep',
      style: {
        stroke:      isDone ? '#22c55e' : isWip ? '#818cf8' : 'rgba(99,102,241,0.35)',
        strokeWidth: isDone || isWip ? 2.5 : 1.5,
        filter:      isDone ? 'drop-shadow(0 0 4px #22c55e)' : isWip ? 'drop-shadow(0 0 6px #818cf8)' : 'none',
      },
      markerEnd: {
        type:   'arrowclosed',
        color:  isDone ? '#22c55e' : isWip ? '#818cf8' : 'rgba(99,102,241,0.4)',
        width:  12,
        height: 12,
      },
    }
  })
)

const wrapHeight = computed(() => {
  const h = props.steps.length * (NODE_H + GAP) + 120
  return `${Math.max(h, 340)}px`
})

function minimapColor(node: any) {
  const s = node.data?.status
  if (s === 'done')        return '#22c55e'
  if (s === 'in_progress') return '#6366f1'
  return '#334155'
}

function onNodeClick({ node }: any) {
  emit('nodeClick', node.data.step)
}
</script>

<style>
/* ── Canvas ──────────────────────────────────────────── */
.rf-wrap {
  width: 100%;
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid rgba(99,102,241,0.2);
  background:
    radial-gradient(ellipse at 20% 10%,  rgba(99,102,241,0.12) 0%, transparent 45%),
    radial-gradient(ellipse at 80% 80%,  rgba(6,182,212,0.08)  0%, transparent 45%),
    radial-gradient(ellipse at 60% 40%,  rgba(139,92,246,0.07) 0%, transparent 40%),
    #060d1a;
}

.rf-flow { width: 100%; height: 100%; }

/* Override VueFlow internals to fit dark theme */
.rf-flow .vue-flow__background { background: transparent !important; }
.rf-flow .vue-flow__pane       { cursor: grab; }
.rf-flow .vue-flow__pane:active { cursor: grabbing; }

/* Minimap */
.rf-minimap {
  border-radius: 10px !important;
  overflow: hidden;
  border: 1px solid rgba(99,102,241,0.25) !important;
  background: rgba(6,13,26,0.9) !important;
}

/* Controls */
.rf-controls {
  border-radius: 10px !important;
  overflow: hidden;
  border: 1px solid rgba(99,102,241,0.25) !important;
  background: rgba(15,23,42,0.9) !important;
  box-shadow: none !important;
}
.rf-controls button {
  background: transparent !important;
  border-bottom: 1px solid rgba(99,102,241,0.15) !important;
  color: #94a3b8 !important;
}
.rf-controls button:hover { background: rgba(99,102,241,0.15) !important; color: #a5b4fc !important; }
.rf-controls button:last-child { border-bottom: none !important; }

/* Fallback */
.rf-fallback {
  display: flex; align-items: center; justify-content: center;
  height: 240px; font-size: 0.875rem; color: #64748b;
}
</style>
