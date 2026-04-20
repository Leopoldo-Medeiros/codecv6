<template>
  <ClientOnly>
    <div
      class="rf-wrap"
      :class="isDark ? 'rf-wrap--dark' : 'rf-wrap--light'"
      :style="{ height: wrapHeight }"
    >
      <VueFlow
        :nodes="nodes"
        :edges="edges"
        :node-types="nodeTypes"
        fit-view-on-init
        :fit-view-on-init-options="{ padding: 0.12 }"
        :min-zoom="0.25"
        :max-zoom="2"
        :nodes-draggable="false"
        :nodes-connectable="false"
        :elements-selectable="false"
        class="rf-flow"
        @node-click="onNodeClick"
      >
        <Background
          variant="dots"
          :gap="28"
          :size="1.2"
          :pattern-color="isDark ? 'rgba(0,172,105,0.18)' : 'rgba(0,172,105,0.1)'"
        />
        <MiniMap
          v-if="steps.length > 6"
          :node-color="minimapColor"
          :mask-color="isDark ? 'rgba(0,0,0,0.55)' : 'rgba(255,255,255,0.6)'"
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
import { VueFlow, Position } from '@vue-flow/core'
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

const colorMode = useColorMode()
const isDark    = computed(() => colorMode.value === 'dark')
const nodeTypes = { step: markRaw(RoadmapStepNode) }

// Snake layout constants
const NODE_W = 240, NODE_H = 90, V_GAP = 60, H_GAP = 100
const SRC = [Position.Right,  Position.Bottom, Position.Left,  Position.Bottom]
const TGT = [Position.Top,    Position.Left,   Position.Top,   Position.Right]

const nodes = computed(() =>
  props.steps.map((step, i) => {
    const row = Math.floor(i / 2)
    const col = (row % 2 === 0) ? (i % 2) : (1 - (i % 2))
    return {
      id:             String(step.id),
      type:           'step',
      position:       { x: col * (NODE_W + H_GAP), y: row * (NODE_H + V_GAP) },
      sourcePosition: SRC[i % 4],
      targetPosition: TGT[i % 4],
      data: {
        order:         step.order ?? i + 1,
        index:         i,
        title:         step.title,
        course:        step.course?.name ?? null,
        resourceCount: (step.resources ?? []).length,
        status:        step.user_status,
        step,
      },
    }
  })
)

const edges = computed(() =>
  props.steps.slice(0, -1).map((step, i) => {
    const next   = props.steps[i + 1]
    const isDone = step.user_status === 'done'
    const isWip  = step.user_status === 'in_progress'
    const stroke = isDone ? '#00AC69' : isWip ? '#00d97e' : 'rgba(0,172,105,0.3)'
    return {
      id:        `e${step.id}-${next.id}`,
      source:    String(step.id),
      target:    String(next.id),
      animated:  isWip,
      type:      'smoothstep',
      style:     { stroke, strokeWidth: isDone || isWip ? 2 : 1.5 },
      markerEnd: { type: 'arrowclosed', color: stroke, width: 11, height: 11 },
    }
  })
)

const wrapHeight = computed(() => {
  const rows = Math.ceil(props.steps.length / 2)
  return `${Math.max(rows * (NODE_H + V_GAP) + 120, 300)}px`
})

function minimapColor(node: any) {
  const s = node.data?.status
  if (s === 'done')        return '#00AC69'
  if (s === 'in_progress') return '#00d97e'
  return isDark.value ? '#1D252C' : '#b2dfce'
}

function onNodeClick({ node }: any) {
  emit('nodeClick', node.data.step)
}
</script>

<style>
.rf-wrap { width: 100%; border-radius: 12px; overflow: hidden; }

.rf-wrap--dark {
  border: 1px solid rgba(0,172,105,0.2);
  background:
    radial-gradient(ellipse at 20% 15%, rgba(0,172,105,0.07) 0%, transparent 50%),
    radial-gradient(ellipse at 80% 80%, rgba(0,125,74,0.05)  0%, transparent 50%),
    #1D252C;
}
.rf-wrap--light {
  border: 1px solid #b2dfce;
  background: #f0faf5;
}

.rf-flow { width: 100%; height: 100%; }
.rf-flow .vue-flow__background { background: transparent !important; }
.rf-flow .vue-flow__pane       { cursor: default; }

/* Minimap */
.rf-minimap { border-radius: 8px !important; overflow: hidden; }
.rf-wrap--dark  .rf-minimap { border: 1px solid rgba(0,172,105,0.2) !important; background: rgba(29,37,44,0.95) !important; }
.rf-wrap--light .rf-minimap { border: 1px solid #b2dfce !important; background: rgba(240,250,245,0.95) !important; }

/* Controls */
.rf-controls { border-radius: 8px !important; overflow: hidden; box-shadow: none !important; }
.rf-wrap--dark  .rf-controls { border: 1px solid rgba(0,172,105,0.2) !important; background: #1a2220 !important; }
.rf-wrap--light .rf-controls { border: 1px solid #b2dfce !important; background: #ffffff !important; }

.rf-wrap--dark  .rf-controls button { background: transparent !important; border-bottom: 1px solid rgba(0,172,105,0.15) !important; color: #5dd9a4 !important; }
.rf-wrap--dark  .rf-controls button:hover { background: rgba(0,172,105,0.12) !important; color: #00AC69 !important; }
.rf-wrap--light .rf-controls button { background: transparent !important; border-bottom: 1px solid #b2dfce !important; color: #007D4A !important; }
.rf-wrap--light .rf-controls button:hover { background: #e0f5ec !important; color: #005c34 !important; }
.rf-controls button:last-child { border-bottom: none !important; }

.rf-fallback { display: flex; align-items: center; justify-content: center; height: 240px; font-size: 0.875rem; color: #6b7280; }
</style>
