<template>
  <div class="space-y-6">
    <section v-for="(section, i) in sections" :key="i">
      <!-- Section header -->
      <div v-if="section.title" class="mb-3 flex items-center gap-2 border-b border-neutral-800 pb-2">
        <component :is="sectionIcon(section.title)" :size="14" class="shrink-0 text-emerald-500" />
        <h2 class="text-[11px] font-bold uppercase tracking-[0.14em] text-neutral-300">
          {{ section.title }}
        </h2>
      </div>

      <!-- Segments: prose text (justified) interleaved with copyable code -->
      <template v-for="(seg, j) in section.segments" :key="j">
        <CopyableCode v-if="seg.type === 'code'" :code="seg.code" />
        <!-- eslint-disable-next-line vue/no-v-html -->
        <div v-else class="instructions-prose" v-html="renderMarkdown(seg.text)" />
      </template>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ClipboardList, Target, Table2, Globe, Hash } from 'lucide-vue-next'

const props = defineProps<{
  markdown: string
}>()

type Segment = { type: 'text'; text: string } | { type: 'code'; code: string }
interface Section { title: string; segments: Segment[] }

const sections = computed<Section[]>(() =>
  props.markdown
    .split(/^(?=## )/m)
    .map((chunk) => {
      const heading = chunk.match(/^## (.+)\n?/)
      const body = heading ? chunk.slice(heading[0].length) : chunk
      return { title: heading?.[1]?.trim() ?? '', segments: toSegments(body) }
    })
    .filter(s => s.title || s.segments.length),
)

// Pull fenced code out as first-class segments so it renders through
// CopyableCode (copy button, consistent styling) instead of raw <pre>.
function toSegments(body: string): Segment[] {
  const segments: Segment[] = []
  const fence = /```\w*\n([\s\S]*?)```/g
  let cursor = 0
  let m: RegExpExecArray | null
  while ((m = fence.exec(body)) !== null) {
    const before = body.slice(cursor, m.index).trim()
    if (before) segments.push({ type: 'text', text: before })
    segments.push({ type: 'code', code: m[1]!.replace(/\n$/, '') })
    cursor = m.index + m[0].length
  }
  const rest = body.slice(cursor).trim()
  if (rest) segments.push({ type: 'text', text: rest })
  return segments
}

function sectionIcon(title: string) {
  const t = title.toLowerCase()
  if (t.includes('situation')) return ClipboardList
  if (t.includes('task')) return Target
  if (t.includes('example')) return Table2
  if (t.includes('real world')) return Globe
  return Hash
}
</script>

<style scoped>
/* Professional reading column: justified, hyphenated, measured. */
.instructions-prose {
  font-size: 0.8125rem;
  line-height: 1.75;
  color: rgb(163 163 163);
}
.instructions-prose :deep(p) {
  margin: 0 0 0.75rem;
  text-align: justify;
  hyphens: auto;
  -webkit-hyphens: auto;
}
.instructions-prose :deep(p:last-child) {
  margin-bottom: 0;
}
.instructions-prose :deep(strong) {
  color: rgb(212 212 212);
  font-weight: 600;
}
.instructions-prose :deep(em) {
  color: rgb(163 163 163);
}
.instructions-prose :deep(code) {
  border-radius: 0.25rem;
  background: rgb(38 38 38);
  padding: 0.1rem 0.3rem;
  font-size: 0.75rem;
  color: rgb(110 231 183);
}
.instructions-prose :deep(ul) {
  margin: 0 0 0.75rem;
  padding-left: 1.1rem;
  list-style: disc;
}
.instructions-prose :deep(li) {
  margin-bottom: 0.35rem;
  text-align: justify;
  hyphens: auto;
}
.instructions-prose :deep(li)::marker {
  color: rgb(52 211 153);
}
.instructions-prose :deep(table) {
  width: 100%;
  margin: 0.75rem 0;
  border-collapse: collapse;
  font-size: 0.75rem;
}
.instructions-prose :deep(td) {
  border: 1px solid rgb(64 64 64) !important;
  padding: 0.4rem 0.6rem;
  text-align: left;
}
.instructions-prose :deep(tr:first-child td) {
  background: rgb(23 23 23);
}
</style>
