<template>
  <div class="my-5 rounded-2xl border border-gray-200 bg-gray-50/70 p-5 dark:border-neutral-800 dark:bg-neutral-900/40">
    <!-- Vertical flow (flowchart TD/TB) -->
    <ol v-if="!horizontal" class="flex flex-col items-center">
      <li v-for="(n, i) in nodes" :key="n.id" class="flex w-full max-w-[460px] flex-col items-center">
        <div
          class="flex w-full items-center gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm dark:border-neutral-800 dark:bg-neutral-900"
          :style="`border-left:3px solid ${n.accent}`"
        >
          <span
            class="inline-flex h-[26px] w-[26px] shrink-0 items-center justify-center rounded-lg font-mono text-xs font-bold"
            :style="`color:${n.accent};background:color-mix(in srgb, ${n.accent} 16%, transparent)`"
          >{{ i + 1 }}</span>
          <div class="min-w-0">
            <p class="text-[0.86rem] font-semibold leading-tight text-gray-900 dark:text-white">{{ n.title }}</p>
            <p v-if="n.subtitle" class="mt-0.5 text-[0.74rem] leading-snug text-gray-500 dark:text-neutral-400">{{ n.subtitle }}</p>
          </div>
        </div>
        <div v-if="i < nodes.length - 1" class="relative flex h-6 w-full items-center justify-center text-gray-300 dark:text-neutral-700">
          <span v-if="edgeLabel(i)" class="absolute left-1/2 translate-x-4 text-[0.68rem] text-gray-500 dark:text-neutral-400">{{ edgeLabel(i) }}</span>
          <svg viewBox="0 0 16 22" class="h-[22px] w-4" aria-hidden="true">
            <path d="M8 0 V15" stroke="currentColor" stroke-width="1.5" fill="none" />
            <path d="M3 13 L8 20 L13 13" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </div>
      </li>
    </ol>

    <!-- Horizontal flow (flowchart LR/RL) -->
    <div v-else class="overflow-x-auto pb-1.5">
      <ol class="flex min-w-min items-stretch">
        <li v-for="(n, i) in nodes" :key="n.id" class="flex items-center">
          <div
            class="flex h-full w-[200px] items-center gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm dark:border-neutral-800 dark:bg-neutral-900"
            :style="`border-left:3px solid ${n.accent}`"
          >
            <span
              class="inline-flex h-[26px] w-[26px] shrink-0 items-center justify-center rounded-lg font-mono text-xs font-bold"
              :style="`color:${n.accent};background:color-mix(in srgb, ${n.accent} 16%, transparent)`"
            >{{ i + 1 }}</span>
            <div class="min-w-0">
              <p class="text-[0.82rem] font-semibold leading-tight text-gray-900 dark:text-white">{{ n.title }}</p>
              <p v-if="n.subtitle" class="mt-0.5 text-[0.7rem] leading-snug text-gray-500 dark:text-neutral-400">{{ n.subtitle }}</p>
            </div>
          </div>
          <div v-if="i < nodes.length - 1" class="flex w-10 shrink-0 items-center justify-center text-gray-300 dark:text-neutral-700">
            <svg viewBox="0 0 24 16" class="h-4 w-6" aria-hidden="true">
              <path d="M0 8 H17" stroke="currentColor" stroke-width="1.5" fill="none" />
              <path d="M15 3 L22 8 L15 13" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </div>
        </li>
      </ol>
    </div>
  </div>
</template>

<script setup lang="ts">
interface FlowNode { id: string, title: string, subtitle: string, accent: string }

const props = defineProps<{ code: string }>()

const CLASS_ACCENT: Record<string, string> = {
  emerald: '#059669', slate: '#64748b', blue: '#3b82f6',
  amber: '#d97706', violet: '#7c3aed', red: '#dc2626', rose: '#e11d48',
}
const DEFAULT_ACCENT = '#059669'

const parsed = computed(() => parse(props.code))
const nodes = computed<FlowNode[]>(() => parsed.value.nodes)
const horizontal = computed(() => parsed.value.horizontal)
const edges = computed(() => parsed.value.edges)

function edgeLabel(index: number): string | null {
  const from = nodes.value[index]?.id
  const to = nodes.value[index + 1]?.id
  return edges.value.find(x => x.from === from && x.to === to)?.label ?? null
}

function parse(code: string) {
  const lines = code.split('\n').map(l => l.trim()).filter(Boolean)

  let horizontal = false
  const accents: Record<string, string> = {}
  const defs = new Map<string, { title: string, subtitle: string, cls: string }>()
  const order: string[] = []
  const edges: Array<{ from: string, to: string, label: string | null }> = []

  // Node def: ID<shape>"quoted label" OR unquoted<shape-close> with optional :::class.
  // Quoted labels are preferred so parens inside the label (register() → boot())
  // don't terminate it early.
  const nodeRe = /\b([A-Za-z_]\w*)\s*(?:\(\[|\[\[|\[|\(\(|\(|\{)\s*(?:"([^"]*)"|([^\])}]*?))\s*(?:\]\)|\]\]|\]|\)\)|\)|\})(?:\s*:::\s*([A-Za-z_]\w*))?/g

  const registerNode = (id: string, rawLabel?: string, cls?: string) => {
    if (!defs.has(id)) { defs.set(id, { title: '', subtitle: '', cls: '' }); order.push(id) }
    const d = defs.get(id)!
    if (rawLabel != null && rawLabel !== '') {
      const parts = rawLabel.split(/\\n|\n/).map(s => s.trim()).filter(Boolean)
      d.title = parts.shift() ?? id
      d.subtitle = parts.join(' · ')
    }
    if (cls) d.cls = cls
  }

  for (const line of lines) {
    if (/^(flowchart|graph)\s+(LR|RL)/i.test(line)) horizontal = true
    if (/^(flowchart|graph)\s+(TD|TB)/i.test(line)) horizontal = false

    const cd = line.match(/^classDef\s+(\w+)\s+(.+)$/)
    if (cd) {
      const stroke = cd[2]!.match(/stroke:\s*(#[0-9a-fA-F]{3,8})/)
      if (stroke) accents[cd[1]!] = stroke[1]!
      continue
    }
    if (/^(flowchart|graph|classDef|%%|style\b|linkStyle\b|subgraph\b|end$)/i.test(line)) continue

    let m: RegExpExecArray | null
    nodeRe.lastIndex = 0
    while ((m = nodeRe.exec(line)) !== null) registerNode(m[1]!, m[2] ?? m[3], m[4])

    if (line.includes('-->') || line.includes('---')) {
      const skeleton = line.replace(nodeRe, ' $1 ').replace(/:::\s*\w+/g, ' ')
      const tokens = skeleton.split(/--+>?|==+>?/)
      const ids: string[] = []
      const labels: (string | null)[] = []
      for (let t of tokens) {
        t = t.trim()
        const lbl = t.match(/^\|([^|]*)\|/)
        if (lbl) { labels[ids.length] = lbl[1]!.trim(); t = t.replace(/^\|[^|]*\|/, '').trim() }
        const idm = t.match(/^([A-Za-z_]\w*)/)
        if (idm) ids.push(idm[1]!)
      }
      for (let i = 0; i < ids.length - 1; i++) {
        registerNode(ids[i]!); registerNode(ids[i + 1]!)
        edges.push({ from: ids[i]!, to: ids[i + 1]!, label: labels[i + 1] ?? null })
      }
    }
  }

  const incoming = new Set(edges.map(e => e.to))
  const adj = new Map<string, string[]>()
  for (const e of edges) { (adj.get(e.from) ?? adj.set(e.from, []).get(e.from)!).push(e.to) }

  const ordered: string[] = []
  const seen = new Set<string>()
  const visit = (id: string) => {
    if (seen.has(id)) return
    seen.add(id); ordered.push(id)
    for (const nx of adj.get(id) ?? []) visit(nx)
  }
  for (const id of order) if (!incoming.has(id)) visit(id)
  for (const id of order) visit(id)

  const nodes: FlowNode[] = ordered.map((id) => {
    const d = defs.get(id) ?? { title: id, subtitle: '', cls: '' }
    return { id, title: d.title || id, subtitle: d.subtitle, accent: accents[d.cls] ?? CLASS_ACCENT[d.cls] ?? DEFAULT_ACCENT }
  })

  return { nodes, edges, horizontal }
}
</script>
