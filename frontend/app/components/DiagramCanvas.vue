<template>
  <div
    class="relative overflow-hidden rounded-2xl border border-slate-800/60 shadow-2xl shadow-black/40"
    :style="{ height: `${height}px` }"
  >
    <!-- Dot-grid background -->
    <div
      class="absolute inset-0 bg-slate-950"
      style="background-image: radial-gradient(circle, #1e293b 1px, transparent 1px); background-size: 24px 24px;"
    />
    <!-- Ambient glow -->
    <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-emerald-950/20 via-transparent to-slate-900/0" />

    <svg
      class="absolute inset-0 select-none"
      :width="width"
      :height="height"
      :viewBox="`0 0 ${width} ${height}`"
      @mouseleave="hoveredId = null"
    >
      <defs>
        <marker
          v-for="key in MARKER_KEYS"
          :key="key"
          :id="`mk-${uid}-${key}`"
          markerWidth="10"
          markerHeight="7"
          refX="9"
          refY="3.5"
          orient="auto"
          markerUnits="userSpaceOnUse"
        >
          <polygon points="0 0, 10 3.5, 0 7" :fill="markerFill(key)" />
        </marker>
      </defs>

      <!-- ── Edges ──────────────────────────────────────────── -->
      <g v-for="(edge, i) in computedEdges" :key="`e${i}`">
        <path
          :d="edge.path"
          fill="none"
          :stroke="edge.active ? COLORS[edge.color].border : '#1e293b'"
          :stroke-width="edge.active ? 2.5 : 1.5"
          :stroke-dasharray="edge.dashed ? '7 4' : undefined"
          :marker-end="`url(#mk-${uid}-${edge.active ? edge.color : 'default'})`"
          style="transition: stroke 0.25s, stroke-width 0.25s"
        />

        <!-- Label pill -->
        <g v-if="edge.label" :transform="`translate(${edge.lx}, ${edge.ly})`">
          <rect
            :x="-edge.lw / 2"
            y="-9"
            :width="edge.lw"
            height="18"
            rx="4"
            :fill="edge.active ? COLORS[edge.color].bg : '#080f1a'"
            :stroke="edge.active ? COLORS[edge.color].border : '#1e293b'"
            stroke-width="1"
            style="transition: fill 0.25s, stroke 0.25s"
          />
          <text
            x="0"
            y="5"
            text-anchor="middle"
            font-size="10"
            font-family="Inter, system-ui, sans-serif"
            :fill="edge.active ? COLORS[edge.color].text : '#475569'"
            style="transition: fill 0.25s"
          >
            {{ edge.label }}
          </text>
        </g>
      </g>

      <!-- ── Nodes ──────────────────────────────────────────── -->
      <g
        v-for="n in computedNodes"
        :key="n.id"
        @mouseenter="hoveredId = n.id"
        :style="{ opacity: dimmed(n.id) ? 0.2 : 1, transition: 'opacity 0.25s' }"
      >
        <!-- ─ TERMINAL ─ (pill shape) -->
        <template v-if="n.type === 'terminal'">
          <rect
            v-if="n.active"
            :x="n.x - 6"
            :y="n.y - 6"
            :width="n.w + 12"
            :height="n.h + 12"
            :rx="(n.h + 12) / 2"
            :fill="COLORS[n.color].border"
            opacity="0.15"
          />
          <rect
            :x="n.x"
            :y="n.y"
            :width="n.w"
            :height="n.h"
            :rx="n.h / 2"
            :fill="n.active ? COLORS[n.color].bg : '#0b1420'"
            :stroke="n.active ? COLORS[n.color].border : '#1a2744'"
            stroke-width="1.5"
            style="transition: fill 0.25s, stroke 0.25s"
          />
          <text
            :x="n.cx"
            :y="n.cy + 5"
            text-anchor="middle"
            font-size="12"
            font-weight="700"
            letter-spacing="0.3"
            font-family="Inter, system-ui, sans-serif"
            :fill="n.active ? COLORS[n.color].text : COLORS[n.color].dimText"
            style="transition: fill 0.25s"
          >
            {{ n.label }}
          </text>
        </template>

        <!-- ─ PROCESS ─ (rounded rect + left accent strip) -->
        <template v-else-if="n.type === 'process'">
          <!-- Drop shadow -->
          <rect
            :x="n.x + 2"
            :y="n.y + 4"
            :width="n.w"
            :height="n.h"
            rx="10"
            fill="rgba(0,0,0,0.45)"
          />
          <!-- Glow -->
          <rect
            v-if="n.active"
            :x="n.x - 5"
            :y="n.y - 5"
            :width="n.w + 10"
            :height="n.h + 10"
            rx="14"
            :fill="COLORS[n.color].border"
            opacity="0.12"
          />
          <!-- Body -->
          <rect
            :x="n.x"
            :y="n.y"
            :width="n.w"
            :height="n.h"
            rx="10"
            :fill="n.active ? COLORS[n.color].bg : '#0b1420'"
            :stroke="n.active ? COLORS[n.color].border : '#1a2744'"
            stroke-width="1.5"
            style="transition: fill 0.25s, stroke 0.25s"
          />
          <!-- Left accent strip (rounded on left, flat on right) -->
          <rect
            :x="n.x"
            :y="n.y"
            width="4"
            :height="n.h"
            rx="10"
            :fill="n.active ? COLORS[n.color].border : '#1a2744'"
            style="transition: fill 0.25s"
          />
          <rect
            :x="n.x"
            :y="n.y + 10"
            width="4"
            :height="n.h - 20"
            :fill="n.active ? COLORS[n.color].border : '#1a2744'"
            style="transition: fill 0.25s"
          />
          <!-- Label -->
          <text
            :x="n.cx + 2"
            :y="n.cy + (n.sublabel ? -8 : 5)"
            text-anchor="middle"
            font-size="12.5"
            font-weight="600"
            font-family="Inter, system-ui, sans-serif"
            :fill="n.active ? COLORS[n.color].text : '#cbd5e1'"
            style="transition: fill 0.25s"
          >
            {{ n.label }}
          </text>
          <!-- Sub-label -->
          <text
            v-if="n.sublabel"
            :x="n.cx + 2"
            :y="n.cy + 10"
            text-anchor="middle"
            font-size="10.5"
            font-family="Inter, system-ui, sans-serif"
            :fill="n.active ? COLORS[n.color].dimText : '#334155'"
            style="transition: fill 0.25s"
          >
            {{ n.sublabel }}
          </text>
        </template>

        <!-- ─ DECISION ─ (diamond) -->
        <template v-else-if="n.type === 'decision'">
          <!-- Glow -->
          <polygon
            v-if="n.active"
            :points="`${n.cx},${n.y - 7} ${n.x + n.w + 7},${n.cy} ${n.cx},${n.y + n.h + 7} ${n.x - 7},${n.cy}`"
            :fill="COLORS[n.color].border"
            opacity="0.1"
          />
          <!-- Diamond body -->
          <polygon
            :points="`${n.cx},${n.y} ${n.x + n.w},${n.cy} ${n.cx},${n.y + n.h} ${n.x},${n.cy}`"
            :fill="n.active ? COLORS[n.color].bg : '#0b1420'"
            :stroke="n.active ? COLORS[n.color].border : '#1a2744'"
            stroke-width="1.5"
            style="transition: fill 0.25s, stroke 0.25s"
          />
          <!-- Label lines (split by \n) -->
          <text
            v-for="(line, li) in n.labelLines"
            :key="li"
            :x="n.cx"
            :y="n.cy + (li - (n.labelLines.length - 1) / 2) * 17"
            text-anchor="middle"
            font-size="11.5"
            font-weight="600"
            font-family="Inter, system-ui, sans-serif"
            :fill="n.active ? COLORS[n.color].text : '#64748b'"
            style="transition: fill 0.25s"
          >
            {{ line }}
          </text>
        </template>
      </g>
    </svg>
  </div>
</template>

<script setup lang="ts">
export interface DiagramNode {
  id: string
  x: number
  y: number
  width?: number
  type: 'terminal' | 'process' | 'decision'
  label: string
  sublabel?: string
  color?: keyof typeof COLORS
}

export interface DiagramEdge {
  from: string
  to: string
  fromPort: 'top' | 'right' | 'bottom' | 'left'
  toPort: 'top' | 'right' | 'bottom' | 'left'
  label?: string
  dashed?: boolean
  color?: keyof typeof COLORS
}

const props = withDefaults(defineProps<{
  nodes: DiagramNode[]
  edges: DiagramEdge[]
  width?: number
  height?: number
}>(), {
  width: 760,
  height: 770,
})

// ── Unique ID per instance (avoids marker ID collisions when multiple diagrams) ──
const uid = Math.random().toString(36).slice(2, 7)

// ── Color palette ──────────────────────────────────────────────────────────────
const COLORS = {
  emerald: { bg: '#022c22', border: '#059669', text: '#34d399', dimText: '#065f46' },
  rose:    { bg: '#1c0505', border: '#f43f5e', text: '#fda4af', dimText: '#9f1239' },
  amber:   { bg: '#1c1000', border: '#d97706', text: '#fcd34d', dimText: '#92400e' },
  slate:   { bg: '#0f1a2e', border: '#475569', text: '#94a3b8', dimText: '#334155' },
  blue:    { bg: '#0a1628', border: '#3b82f6', text: '#93c5fd', dimText: '#1e3a5f' },
  violet:  { bg: '#13072e', border: '#7c3aed', text: '#c4b5fd', dimText: '#4c1d95' },
} as const

const MARKER_KEYS = ['emerald', 'rose', 'amber', 'slate', 'blue', 'violet', 'default'] as const

function markerFill(key: typeof MARKER_KEYS[number]): string {
  return key === 'default' ? '#334155' : COLORS[key as keyof typeof COLORS].border
}

// ── State ──────────────────────────────────────────────────────────────────────
const hoveredId = ref<string | null>(null)

// ── Node geometry ──────────────────────────────────────────────────────────────
function nodeH(n: DiagramNode): number {
  const w = n.width ?? 180
  if (n.type === 'terminal') return 44
  if (n.type === 'decision') return Math.round(w * 0.55)
  return n.sublabel ? 70 : 56
}

function anchor(n: DiagramNode, port: 'top' | 'right' | 'bottom' | 'left') {
  const w = n.width ?? 180
  const h = nodeH(n)
  const cx = n.x + w / 2
  const cy = n.y + h / 2
  if (n.type === 'decision') {
    if (port === 'top')    return { x: cx, y: n.y }
    if (port === 'bottom') return { x: cx, y: n.y + h }
    if (port === 'right')  return { x: n.x + w, y: cy }
    return { x: n.x, y: cy }
  }
  if (port === 'top')    return { x: cx, y: n.y }
  if (port === 'bottom') return { x: cx, y: n.y + h }
  if (port === 'right')  return { x: n.x + w, y: cy }
  return { x: n.x, y: cy }
}

// ── Connectivity ───────────────────────────────────────────────────────────────
const nodeMap = computed(() =>
  Object.fromEntries(props.nodes.map(n => [n.id, n])),
)

const connectedIds = computed<Set<string>>(() => {
  if (!hoveredId.value) return new Set()
  const set = new Set<string>()
  for (const e of props.edges) {
    if (e.from === hoveredId.value) set.add(e.to)
    if (e.to === hoveredId.value) set.add(e.from)
  }
  return set
})

const dimmed = (id: string) =>
  hoveredId.value !== null && id !== hoveredId.value && !connectedIds.value.has(id)

// ── Computed nodes ─────────────────────────────────────────────────────────────
const computedNodes = computed(() =>
  props.nodes.map(n => {
    const w = n.width ?? 180
    const h = nodeH(n)
    return {
      ...n,
      w,
      h,
      cx: n.x + w / 2,
      cy: n.y + h / 2,
      color: (n.color ?? 'slate') as keyof typeof COLORS,
      labelLines: n.label.split('\n'),
      active: hoveredId.value === n.id || connectedIds.value.has(n.id),
    }
  }),
)

// ── Edge paths ─────────────────────────────────────────────────────────────────
function edgePath(
  p1: { x: number; y: number },
  p2: { x: number; y: number },
  port1: DiagramEdge['fromPort'],
  port2: DiagramEdge['toPort'],
): string {
  const dx = Math.abs(p2.x - p1.x)
  const dy = Math.abs(p2.y - p1.y)

  // Straight lines for same-axis connections
  if (dx < 2 || dy < 2) return `M ${p1.x} ${p1.y} L ${p2.x} ${p2.y}`

  // Cubic Bezier for off-axis connections
  const tension = Math.min(Math.sqrt(dx * dx + dy * dy) * 0.38, 110)
  let [c1x, c1y] = [p1.x, p1.y]
  let [c2x, c2y] = [p2.x, p2.y]

  if (port1 === 'bottom') c1y += tension
  else if (port1 === 'top') c1y -= tension
  else if (port1 === 'right') c1x += tension
  else if (port1 === 'left') c1x -= tension

  if (port2 === 'top') c2y -= tension
  else if (port2 === 'bottom') c2y += tension
  else if (port2 === 'left') c2x -= tension
  else if (port2 === 'right') c2x += tension

  return `M ${p1.x} ${p1.y} C ${c1x} ${c1y}, ${c2x} ${c2y}, ${p2.x} ${p2.y}`
}

// ── Computed edges ─────────────────────────────────────────────────────────────
const computedEdges = computed(() =>
  props.edges.flatMap((e) => {
    const src = nodeMap.value[e.from]
    const dst = nodeMap.value[e.to]
    if (!src || !dst) return []

    const p1 = anchor(src, e.fromPort)
    const p2 = anchor(dst, e.toPort)
    const path = edgePath(p1, p2, e.fromPort, e.toPort)

    const isVert  = Math.abs(p2.x - p1.x) < 5
    const isHoriz = Math.abs(p2.y - p1.y) < 5
    const lx = (p1.x + p2.x) / 2 + (isVert ? -42 : 0)
    const ly = (p1.y + p2.y) / 2 + (isHoriz ? -14 : 0)
    const lw = e.label ? e.label.length * 6.8 + 18 : 0

    const color = (e.color ?? 'emerald') as keyof typeof COLORS
    const active = hoveredId.value === e.from || hoveredId.value === e.to

    return [{ path, label: e.label, lx, ly, lw, dashed: e.dashed ?? false, color, active }]
  }),
)
</script>
