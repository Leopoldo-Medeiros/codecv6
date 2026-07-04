<template>
  <ClientOnly>
    <div class="rf-outer">

      <!-- Progress header -->
      <div class="rf-progress" :class="isDark ? 'rf-progress--dark' : 'rf-progress--light'">
        <div class="rf-progress__count">
          <span class="rf-progress__num">{{ doneCount }}</span>
          <span class="rf-progress__slash">/</span>
          <span class="rf-progress__total">{{ steps.length }}</span>
          <span class="rf-progress__label">steps completed</span>
        </div>
        <div class="rf-progress__track">
          <div class="rf-progress__fill" :style="{ width: progressPct + '%' }" />
        </div>
        <span class="rf-progress__pct">{{ progressPct }}%</span>
      </div>

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
          :fit-view-on-init-options="{ padding: 0.15 }"
          :min-zoom="0.2"
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
            v-if="steps.length > 6 && !isMobile"
            :node-color="minimapColor"
            :mask-color="isDark ? 'rgba(0,0,0,0.55)' : 'rgba(255,255,255,0.6)'"
            class="rf-minimap"
          />
          <Controls :show-interactive="false" class="rf-controls" />
        </VueFlow>
      </div>
    </div>

    <template #fallback>
      <div class="rf-fallback">Loading roadmap…</div>
    </template>
  </ClientOnly>
</template>

<script setup lang="ts">
import { VueFlow, Position, MarkerType } from '@vue-flow/core'
import { Background } from '@vue-flow/background'
import { MiniMap } from '@vue-flow/minimap'
import { Controls } from '@vue-flow/controls'
import dagre from '@dagrejs/dagre'
import '@vue-flow/core/dist/style.css'
import '@vue-flow/core/dist/theme-default.css'
import '@vue-flow/minimap/dist/style.css'
import '@vue-flow/controls/dist/style.css'
import RoadmapStepNode from '~/components/RoadmapStepNode.vue'
import RoadmapConceptChip from '~/components/RoadmapConceptChip.vue'
import RoadmapResourceGroup from '~/components/RoadmapResourceGroup.vue'
import type { PathStep } from '~/composables/usePaths'

type Step = PathStep

const props = defineProps<{ steps: Step[] }>()
const emit  = defineEmits<{ (e: 'nodeClick', step: Step): void }>()

// JetBrains Mono accents the step index / progress readout — loaded via
// useHead (not a CSS @import) so it isn't silently dropped when Vite bundles
// this component's <style> block after the vue-flow libraries' own CSS
// imports (an @import must be the first rule in the final stylesheet).
useHead({
  link: [
    { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
    { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: '' },
    {
      rel: 'stylesheet',
      href: 'https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;700&display=swap',
    },
  ],
})

const colorMode = useColorMode()
const isDark    = computed(() => colorMode.value === 'dark')
// vue-flow's NodeTypesObject expects raw component definitions; markRaw'd
// SFC imports satisfy the runtime shape but TS sees a stricter generic.
// eslint-disable-next-line @typescript-eslint/no-explicit-any
const nodeTypes = {
  step:      markRaw(RoadmapStepNode),
  concept:   markRaw(RoadmapConceptChip),
  resources: markRaw(RoadmapResourceGroup),
} as any

// Authenticated routes run SPA-only (ssr: false), so `window` is always
// available here — no hydration mismatch risk.
const isMobile = ref(window.innerWidth < 640)
function handleResize() { isMobile.value = window.innerWidth < 640 }
onMounted(() => window.addEventListener('resize', handleResize))
onUnmounted(() => window.removeEventListener('resize', handleResize))

const doneCount   = computed(() => props.steps.filter(s => s.user_status === 'done').length)
const progressPct = computed(() => props.steps.length ? Math.round((doneCount.value / props.steps.length) * 100) : 0)

const NODE_W = 264, NODE_H = 112
const CHIP_W = 132, CHIP_H = 34, CHIP_GAP = 8
const RES_W  = 176, SATELLITE_GAP = 44

const STATUS_COLOR = { done: '#00AC69', wip: '#00d97e', idle: 'rgba(0,172,105,0.3)' }
const AMBER = '#d9a441'

function buildGraph() {
  const steps = props.steps
  const nodes: any[] = []
  const edges: any[] = []

  if (!steps.length) return { nodes, edges, maxY: 0 }

  const g = new dagre.graphlib.Graph()
  g.setGraph({ rankdir: 'TB', nodesep: 70, ranksep: 130, marginx: 40, marginy: 40 })
  g.setDefaultEdgeLabel(() => ({}))

  steps.forEach(step => g.setNode(String(step.id), { width: NODE_W, height: NODE_H }))
  for (let i = 0; i < steps.length - 1; i++) {
    g.setEdge(String(steps[i]!.id), String(steps[i + 1]!.id))
  }
  dagre.layout(g)

  let maxY = 0

  steps.forEach((step, i) => {
    const dn = g.node(String(step.id))
    const pos = { x: dn.x - NODE_W / 2, y: dn.y - NODE_H / 2 }
    maxY = Math.max(maxY, pos.y + NODE_H)

    nodes.push({
      id:   `step-${step.id}`,
      type: 'step',
      position: pos,
      data: {
        order:            step.order ?? i + 1,
        index:            i,
        title:            step.title,
        course:           step.course?.name ?? null,
        difficulty:       step.difficulty ?? null,
        estimatedMinutes: step.estimated_minutes ?? null,
        status:           step.user_status ?? 'not_started',
        step,
      },
    })

    if (i < steps.length - 1) {
      const next   = steps[i + 1]!
      const isDone = step.user_status === 'done'
      const isWip  = step.user_status === 'in_progress'
      const stroke = isDone ? STATUS_COLOR.done : isWip ? STATUS_COLOR.wip : STATUS_COLOR.idle
      edges.push({
        id:           `e-${step.id}-${next.id}`,
        source:       `step-${step.id}`,
        target:       `step-${next.id}`,
        sourceHandle: 'bottom',
        targetHandle: 'top',
        animated:     isWip,
        type:         'smoothstep',
        style:        { stroke, strokeWidth: isDone || isWip ? 2.5 : 1.5 },
        markerEnd:    { type: MarkerType.ArrowClosed, color: stroke, width: 10, height: 10 },
      })
    }

    if (isMobile.value) return

    // Satellites: concepts on one side, resources on the other — alternating
    // per step so consecutive steps' satellites don't stack on the same side.
    // `sourceHandle`/the chip's own target position must match this side, or
    // the connector line cuts across the card instead of exiting from it.
    const conceptSide: 'left' | 'right' = i % 2 === 0 ? 'right' : 'left'

    const concepts = (step.concepts ?? []).slice(0, 2)
    const conceptOverflow = (step.concepts?.length ?? 0) - concepts.length
    if (concepts.length) {
      const stackH = concepts.length * CHIP_H + (concepts.length - 1) * CHIP_GAP
      const startY = pos.y + NODE_H / 2 - stackH / 2
      const cx = conceptSide === 'right' ? pos.x + NODE_W + SATELLITE_GAP : pos.x - SATELLITE_GAP - CHIP_W
      concepts.forEach((concept, ci) => {
        const chipId = `concept-${step.id}-${ci}`
        const cy = startY + ci * (CHIP_H + CHIP_GAP)
        nodes.push({
          id: chipId,
          type: 'concept',
          position: { x: cx, y: cy },
          data: { label: concept, overflow: ci === concepts.length - 1 ? conceptOverflow : 0, side: conceptSide },
        })
        edges.push({
          id: `ce-${chipId}`,
          source: `step-${step.id}`,
          target: chipId,
          sourceHandle: conceptSide,
          type: 'straight',
          style: { stroke: AMBER, strokeWidth: 1.25, strokeDasharray: '3 4' },
        })
        maxY = Math.max(maxY, cy + CHIP_H)
      })
    }

    const resources = step.resources ?? []
    if (resources.length) {
      const resId = `resources-${step.id}`
      const resSide: 'left' | 'right' = conceptSide === 'right' ? 'left' : 'right'
      const rx = resSide === 'right' ? pos.x + NODE_W + SATELLITE_GAP : pos.x - SATELLITE_GAP - RES_W
      const ry = pos.y + NODE_H / 2 - 20
      nodes.push({
        id: resId,
        type: 'resources',
        position: { x: rx, y: ry },
        data: { resources: resources.slice(0, 3), overflow: Math.max(0, resources.length - 3), side: resSide },
      })
      edges.push({
        id: `re-${resId}`,
        source: `step-${step.id}`,
        target: resId,
        sourceHandle: resSide,
        type: 'straight',
        style: { stroke: AMBER, strokeWidth: 1.5 },
      })
      maxY = Math.max(maxY, ry + 90)
    }
  })

  return { nodes, edges, maxY }
}

const graph = computed(() => buildGraph())
const nodes = computed(() => graph.value.nodes)
const edges = computed(() => graph.value.edges)

const wrapHeight = computed(() => `${Math.max(graph.value.maxY + 80, 320)}px`)

function minimapColor(node: any) {
  const s = node.data?.status
  if (s === 'done')        return '#00AC69'
  if (s === 'in_progress') return '#00d97e'
  if (node.type !== 'step') return AMBER
  return isDark.value ? '#1D252C' : '#b2dfce'
}

function onNodeClick({ node }: any) {
  if (node.type !== 'step') return
  emit('nodeClick', node.data.step)
}
</script>

<style>
.rf-outer { display: flex; flex-direction: column; gap: 14px; }

/* Progress header */
.rf-progress {
  display: flex; align-items: center; gap: 14px;
  padding: 14px 18px; border-radius: 12px;
}
.rf-progress--dark  { background: #1a2220; border: 1px solid rgba(0,172,105,0.2); }
.rf-progress--light { background: #f7fdfa; border: 1px solid #b2dfce; }

.rf-progress__count { display: flex; align-items: baseline; gap: 3px; flex-shrink: 0; }
.rf-progress__num, .rf-progress__total {
  font-family: 'JetBrains Mono', ui-monospace, monospace;
  font-weight: 700; font-size: 1rem;
}
.rf-progress__slash { opacity: 0.4; font-size: 0.9rem; }
.rf-progress__label { font-size: 0.72rem; margin-left: 6px; opacity: 0.65; text-transform: uppercase; letter-spacing: 0.05em; }

.rf-progress--dark  .rf-progress__num  { color: #5dd9a4; }
.rf-progress--dark  .rf-progress__total,
.rf-progress--dark  .rf-progress__label { color: #8f9ab8; }
.rf-progress--light .rf-progress__num  { color: #007D4A; }
.rf-progress--light .rf-progress__total,
.rf-progress--light .rf-progress__label { color: #5a7a6e; }

.rf-progress__track {
  flex: 1; height: 8px; border-radius: 999px; overflow: hidden;
}
.rf-progress--dark  .rf-progress__track { background: rgba(0,172,105,0.12); }
.rf-progress--light .rf-progress__track { background: #e0f5ec; }
.rf-progress__fill {
  height: 100%; border-radius: 999px;
  background: linear-gradient(90deg, #007D4A, #00AC69, #00d97e);
  box-shadow: 0 0 10px rgba(0,172,105,0.5);
  transition: width 0.5s ease;
}
.rf-progress__pct {
  font-family: 'JetBrains Mono', ui-monospace, monospace;
  font-weight: 700; font-size: 0.9rem; flex-shrink: 0; min-width: 2.6em; text-align: right;
}
.rf-progress--dark  .rf-progress__pct { color: #5dd9a4; }
.rf-progress--light .rf-progress__pct { color: #007D4A; }

/* Canvas wrap */
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
