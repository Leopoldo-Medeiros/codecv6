<template>
  <div class="step-concept-view">
    <!-- ── Breadcrumb + actions strip ─────────────────────────────── -->
    <div class="mb-6 flex flex-wrap items-center gap-3">
      <slot name="breadcrumb" />
      <div class="ml-auto flex shrink-0 items-center gap-2">
        <UBadge :color="statusBadge.color" variant="subtle" size="sm">{{ statusBadge.label }}</UBadge>
        <UBadge v-if="step.estimated_minutes" color="gray" variant="subtle" size="sm" icon="i-heroicons-clock">
          {{ step.estimated_minutes }} min
        </UBadge>
      </div>
    </div>

    <!-- ── Two-column body ────────────────────────────────────────── -->
    <div class="flex flex-col gap-6 lg:flex-row">
      <!-- Sidebar -->
      <aside class="w-full shrink-0 lg:w-64">
        <div class="sticky top-6 space-y-4">
          <!-- About: difficulty + prerequisites + concepts -->
          <UCard
            v-if="step.difficulty || step.prerequisites?.length || step.concepts?.length"
            :ui="{ body: { padding: 'p-4' } }"
          >
            <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
              About this step
            </p>

            <div class="space-y-3 text-xs">
              <div v-if="step.difficulty" class="flex items-center justify-between">
                <span class="text-gray-500 dark:text-gray-400">Difficulty</span>
                <UBadge :color="difficultyColor" variant="subtle" size="xs">{{ difficultyLabel }}</UBadge>
              </div>

              <div v-if="step.prerequisites?.length">
                <p class="mb-1.5 text-gray-500 dark:text-gray-400">Prerequisites</p>
                <ul class="space-y-1">
                  <li
                    v-for="p in step.prerequisites"
                    :key="p.id"
                    class="flex items-center gap-1.5 text-gray-700 dark:text-gray-300"
                  >
                    <UIcon
                      name="i-heroicons-minus-circle"
                      class="h-3.5 w-3.5 shrink-0 text-gray-400"
                    />
                    <span class="truncate">{{ p.title }}</span>
                  </li>
                </ul>
              </div>

              <div v-if="step.concepts?.length">
                <p class="mb-1.5 text-gray-500 dark:text-gray-400">Concepts covered</p>
                <div class="flex flex-wrap gap-1">
                  <span
                    v-for="c in step.concepts"
                    :key="c"
                    class="rounded bg-gray-100 px-1.5 py-0.5 font-mono text-[10px] text-gray-700 dark:bg-slate-800 dark:text-gray-300"
                  >
                    {{ c }}
                  </span>
                </div>
              </div>
            </div>
          </UCard>

          <!-- TOC (tier → subsections) -->
          <UCard v-if="toc.length" :ui="{ body: { padding: 'p-4' } }">
            <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
              On this step
            </p>
            <nav class="space-y-1">
              <template v-for="t in toc" :key="t.id">
                <a
                  v-show="isTierVisible(t.tier)"
                  :href="`#${t.id}`"
                  class="block rounded-md px-2 py-1 transition-colors"
                  :class="[
                    t.level === 2
                      ? 'text-[11px] font-bold uppercase tracking-widest text-gray-700 dark:text-gray-300'
                      : 'pl-4 text-xs',
                    activeTocId === t.id
                      ? 'bg-emerald-50 font-semibold text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300'
                      : t.level === 2
                        ? 'mt-2 hover:text-gray-900 dark:hover:text-gray-100'
                        : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-slate-800/60 dark:hover:text-gray-200',
                  ]"
                  @click.prevent="scrollTo(t.id)"
                >
                  {{ t.text }}
                </a>
              </template>
            </nav>
          </UCard>

          <!-- Progress -->
          <UCard :ui="{ body: { padding: 'p-4' } }">
            <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
              Progress
            </p>
            <div class="flex flex-col gap-2">
              <button
                v-for="s in statusOptions"
                :key="s.value"
                class="flex items-center gap-2 rounded-lg border px-3 py-2 text-xs font-medium transition-colors"
                :class="step.user_status === s.value
                  ? `border-transparent ${s.activeClass}`
                  : 'border-gray-200 text-gray-500 hover:border-gray-300 dark:border-gray-600 dark:text-gray-400 dark:hover:border-gray-500'"
                :disabled="saving"
                @click="$emit('status', s.value)"
              >
                <UIcon :name="s.icon" class="h-3.5 w-3.5" />
                {{ s.label }}
              </button>
            </div>
            <div v-if="step.instructions?.length" class="mt-4 border-t border-gray-100 pt-3 dark:border-gray-700">
              <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ checkedCount }} / {{ step.instructions.length }} tasks done
              </p>
              <UProgress :value="practicePct" size="xs" color="emerald" class="mt-2" />
            </div>
          </UCard>

          <!-- Resources -->
          <UCard v-if="step.resources?.length" :ui="{ body: { padding: 'p-4' } }">
            <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
              Further reading
            </p>
            <ul class="space-y-2">
              <li v-for="r in step.resources" :key="r.url">
                <a
                  :href="r.url"
                  target="_blank"
                  rel="noopener"
                  class="flex items-start gap-2 text-xs text-gray-600 hover:text-emerald-600 dark:text-gray-400 dark:hover:text-emerald-400"
                >
                  <UIcon name="i-heroicons-arrow-top-right-on-square" class="mt-0.5 h-3 w-3 shrink-0" />
                  <span>{{ r.label }}</span>
                </a>
              </li>
            </ul>
          </UCard>
        </div>
      </aside>

      <!-- Main content -->
      <div class="min-w-0 flex-1 space-y-6">
        <!-- TL;DR callout -->
        <div
          v-if="step.tldr"
          class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-4 dark:border-emerald-900/50 dark:bg-emerald-950/30"
        >
          <div class="flex items-start gap-3">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/50">
              <UIcon name="i-heroicons-light-bulb" class="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
            </div>
            <div>
              <p class="mb-1 text-xs font-bold uppercase tracking-widest text-emerald-700 dark:text-emerald-400">TL;DR</p>
              <p class="text-sm leading-relaxed text-gray-700 dark:text-gray-300">{{ step.tldr }}</p>
            </div>
          </div>
        </div>

        <!-- Concept content -->
        <UCard v-if="step.concept_content">
          <!-- Seniority filter pill bar (only renders if step has tiered content) -->
          <template v-if="hasTiers" #header>
            <div class="flex flex-wrap items-center justify-between gap-3">
              <h2 class="flex items-center gap-2 text-base font-semibold text-gray-900 dark:text-white">
                <UIcon name="i-heroicons-book-open" class="h-5 w-5 text-emerald-500" />
                Concept
              </h2>
              <div class="flex items-center gap-1.5">
                <span class="mr-1 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
                  Showing for
                </span>
                <button
                  v-for="opt in tierFilterOptions"
                  :key="opt.value"
                  class="rounded-full border px-2.5 py-1 text-[11px] font-semibold transition-colors"
                  :class="activeFilter === opt.value
                    ? 'border-emerald-500 bg-emerald-500 text-white'
                    : 'border-gray-200 text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:border-gray-700 dark:text-gray-400 dark:hover:border-gray-500 dark:hover:text-gray-200'"
                  @click="activeFilter = opt.value"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>
          </template>

          <article ref="contentRef" class="step-concept">
            <!-- Tiered render: one <section> per tier so we can hide them via CSS. -->
            <template v-if="hasTiers">
              <section
                v-for="(t, i) in tierSections"
                :key="i"
                v-show="isTierVisible(t.tier)"
                :data-tier="t.tier"
                class="step-concept__tier"
              >
                <template v-for="(seg, si) in t.segments" :key="si">
                  <FlowDiagram v-if="seg.type === 'flow'" :code="seg.content" />
                  <!-- eslint-disable-next-line vue/no-v-html -->
                  <div v-else v-html="seg.content" />
                </template>
              </section>
            </template>
            <!-- Untiered fallback (legacy steps without tier markers). -->
            <template v-else>
              <template v-for="(seg, si) in renderedSegments" :key="si">
                <FlowDiagram v-if="seg.type === 'flow'" :code="seg.content" />
                <!-- eslint-disable-next-line vue/no-v-html -->
                <div v-else v-html="seg.content" />
              </template>
            </template>
          </article>
        </UCard>

        <!-- Code playground — Monaco editor + Judge0-backed Run. -->
        <div v-if="step.has_playground && step.playground_starter_code" ref="playgroundAnchor">
        <UCard :ui="{ body: { padding: 'p-0' } }">
          <template #header>
            <div class="flex items-center justify-between">
              <h2 class="flex items-center gap-2 text-base font-semibold text-gray-900 dark:text-white">
                <UIcon name="i-heroicons-command-line" class="h-5 w-5 text-emerald-500" />
                Try it yourself
              </h2>
              <span class="text-xs text-gray-400 dark:text-gray-500">
                Scratchpad — experiment freely
              </span>
            </div>
          </template>

          <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-4 py-2 dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-2">
              <span class="rounded bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-widest text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400">
                PHP
              </span>
              <span class="font-mono text-xs text-gray-500 dark:text-gray-400">scratch.php</span>
            </div>
            <div class="flex items-center gap-2">
              <UButton
                size="xs"
                color="gray"
                variant="ghost"
                icon="i-heroicons-arrow-path"
                @click="resetPlayground"
              >
                Reset
              </UButton>
              <UButton
                size="xs"
                color="emerald"
                variant="solid"
                icon="i-heroicons-play"
                :loading="playgroundRunning"
                @click="runPlayground"
              >
                Run
              </UButton>
            </div>
          </div>

          <div class="playground-editor">
            <ClientOnly>
              <VueMonacoEditor
                v-model:value="playgroundCode"
                language="php"
                theme="vs-dark"
                :options="monacoOptions"
                class="h-full"
              />
              <template #fallback>
                <div class="flex items-center justify-center px-6 py-10 text-xs text-slate-500">
                  Loading editor…
                </div>
              </template>
            </ClientOnly>
          </div>

          <div class="border-t border-gray-200 bg-gray-50 px-4 py-3 dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-1.5 flex items-center justify-between gap-3">
              <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                Output
              </p>
              <p v-if="playgroundDurationMs !== null" class="text-[10px] text-gray-400 dark:text-gray-600">
                {{ playgroundDurationMs }} ms
              </p>
            </div>
            <pre v-if="playgroundError" class="playground-output playground-output--err">{{ playgroundError }}</pre>
            <pre v-else-if="playgroundOutput" class="playground-output">{{ playgroundOutput }}</pre>
            <p v-else class="font-mono text-xs italic text-gray-400 dark:text-gray-600">
              Press Run to see the output here.
            </p>
          </div>
        </UCard>
        </div>

        <!-- Practice tasks -->
        <UCard v-if="step.instructions?.length">
          <template #header>
            <div class="flex items-center justify-between">
              <h2 class="flex items-center gap-2 text-base font-semibold text-gray-900 dark:text-white">
                <UIcon name="i-heroicons-check-circle" class="h-5 w-5 text-emerald-500" />
                Practice tasks
              </h2>
              <span class="text-xs text-gray-400">{{ checkedCount }} / {{ step.instructions.length }} completed</span>
            </div>
          </template>

          <ul class="space-y-2">
            <li
              v-for="(ins, i) in step.instructions"
              :key="ins.id"
              class="group rounded-lg border border-gray-100 px-3 py-2.5 transition-colors hover:border-gray-200 dark:border-gray-700 dark:hover:border-gray-600"
              :class="checked.has(ins.id) && 'bg-emerald-50/60 dark:bg-emerald-950/20'"
            >
              <div class="flex items-start gap-3">
                <button
                  type="button"
                  class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-md border-2 transition-colors"
                  :class="checked.has(ins.id)
                    ? 'border-emerald-500 bg-emerald-500 text-white'
                    : 'border-gray-300 dark:border-gray-600'"
                  @click="toggleCheck(ins.id)"
                >
                  <UIcon v-if="checked.has(ins.id)" name="i-heroicons-check" class="h-3.5 w-3.5" />
                </button>
                <div class="min-w-0 flex-1">
                  <p
                    class="text-sm leading-relaxed text-gray-800 dark:text-gray-200"
                    :class="checked.has(ins.id) && 'text-gray-400 line-through dark:text-gray-500'"
                  >
                    <span class="mr-2 text-xs font-semibold text-gray-400">{{ String(i + 1).padStart(2, '0') }}.</span>
                    {{ ins.text }}
                  </p>
                  <!-- "Try this" button — only when the task ships with starter_code -->
                  <div v-if="ins.starter_code" class="mt-2 flex items-center gap-2">
                    <UButton
                      size="xs"
                      color="emerald"
                      variant="soft"
                      icon="i-heroicons-command-line"
                      @click="loadTaskIntoPlayground(ins)"
                    >
                      Try this in the playground
                    </UButton>
                    <span v-if="activeTaskId === ins.id" class="text-[10px] font-medium uppercase tracking-widest text-emerald-600 dark:text-emerald-400">
                      Loaded ↑
                    </span>
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </UCard>

      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { VueMonacoEditor } from '@guolao/vue-monaco-editor'
import type { PathStep } from '~/composables/usePaths'

const props = defineProps<{
  step: PathStep
  saving?: boolean
}>()

defineEmits<{
  (e: 'status', value: NonNullable<PathStep['user_status']>): void
}>()

// ─── Status sidebar buttons ───────────────────────────────────────────
const statusOptions = [
  { value: 'not_started' as const, label: 'Not started', icon: 'i-heroicons-pause',
    activeClass: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200' },
  { value: 'in_progress' as const, label: 'In progress', icon: 'i-heroicons-play',
    activeClass: 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' },
  { value: 'done'        as const, label: 'Done',        icon: 'i-heroicons-check-circle',
    activeClass: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' },
]

const statusBadge = computed(() => {
  switch (props.step.user_status) {
    case 'done':        return { color: 'emerald' as const, label: 'Completed' }
    case 'in_progress': return { color: 'amber'   as const, label: 'In progress' }
    default:            return { color: 'gray'    as const, label: 'Not started' }
  }
})

// ─── Difficulty badge ─────────────────────────────────────────────────
const difficultyColor = computed((): 'emerald' | 'amber' | 'red' | 'gray' => {
  switch (props.step.difficulty) {
    case 'beginner':     return 'emerald'
    case 'intermediate': return 'amber'
    case 'advanced':     return 'red'
    default:             return 'gray'
  }
})
const difficultyLabel = computed(() => {
  const d = props.step.difficulty
  return d ? d.charAt(0).toUpperCase() + d.slice(1) : ''
})

// ─── Instructions check-list (state is per-session; persistence later) ─
const checked = ref(new Set<number>())
function toggleCheck(id: number) {
  if (checked.value.has(id)) checked.value.delete(id)
  else checked.value.add(id)
}
const checkedCount = computed(() => checked.value.size)
const practicePct = computed(() => {
  const total = props.step.instructions?.length ?? 0
  return total ? Math.round((checked.value.size / total) * 100) : 0
})

// ─── PHP syntax highlighter (inline, no deps) ─────────────────────────
function highlightPhp(code: string): string {
  const escaped = code
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')

  const pattern = new RegExp(
    [
      "(\\/\\*[\\s\\S]*?\\*\\/)",
      "(\\/\\/[^\\n]*)",
      "('(?:\\\\.|[^'\\\\])*'|\"(?:\\\\.|[^\"\\\\])*\")",
      "(&lt;\\?php|\\?&gt;)",
      "(\\$[A-Za-z_][A-Za-z0-9_]*)",
      "\\b(class|function|return|if|else|elseif|while|for|foreach|switch|case|break|continue|new|use|namespace|public|private|protected|static|abstract|final|extends|implements|interface|trait|throw|try|catch|finally|declare|echo|print|as|true|false|null|enum|match|readonly)\\b",
      "\\b(int|integer|string|bool|boolean|float|double|array|object|void|mixed|self|parent|callable|iterable|never)\\b",
      "(\\b[A-Z][A-Za-z0-9_]*)",
      "([A-Za-z_][A-Za-z0-9_]*)(?=\\s*\\()",
      "(\\b\\d+\\.?\\d*\\b)",
      "(=&gt;|-&gt;|::|==|===|!=|!==|&lt;=|&gt;=)",
    ].join('|'),
    'g',
  )

  return escaped.replace(pattern, (...args: unknown[]) => {
    if (args[1])  return `<span class="t-co">${args[1]}</span>`
    if (args[2])  return `<span class="t-co">${args[2]}</span>`
    if (args[3])  return `<span class="t-st">${args[3]}</span>`
    if (args[4])  return `<span class="t-tg">${args[4]}</span>`
    if (args[5])  return `<span class="t-vr">${args[5]}</span>`
    if (args[6])  return `<span class="t-kw">${args[6]}</span>`
    if (args[7])  return `<span class="t-ty">${args[7]}</span>`
    if (args[8])  return `<span class="t-cl">${args[8]}</span>`
    if (args[9])  return `<span class="t-fn">${args[9]}</span>`
    if (args[10]) return `<span class="t-nu">${args[10]}</span>`
    if (args[11]) return `<span class="t-op">${args[11]}</span>`
    return String(args[0])
  })
}

// ─── Markdown rendering with anchored headings + PHP highlighting ────
function slug(text: string): string {
  return text
    .toLowerCase()
    .replace(/[^\w\s-]/g, '')
    .trim()
    .replace(/\s+/g, '-')
}

function renderMarkdown(md: string): string {
  return md
    .replace(/```(\w*)\n([\s\S]*?)```/g, (_, lang: string, code: string) => {
      const highlighted = lang === 'php' ? highlightPhp(code) : code
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
      return `<pre data-lang="${lang || 'plain'}"><code>${highlighted}</code></pre>`
    })
    .replace(/^### (.+)$/gm, (_, t: string) => `<h3 id="${slug(t)}">${t}</h3>`)
    .replace(/^## (.+)$/gm, (_, t: string) => `<h2 id="${slug(t)}">${t}</h2>`)
    .replace(/^\| (.+) \|$/gm, (_, row: string) => {
      const cells = row.split(' | ').map(c => `<td>${c.trim()}</td>`).join('')
      return `<tr>${cells}</tr>`
    })
    .replace(/(<tr>[\s\S]*?<\/tr>\n?)+/g, m => `<table>${m}</table>`)
    .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
    .replace(/`([^`\n]+)`/g, '<code>$1</code>')
    .replace(/^[\*\-] (.+)$/gm, '<li>$1</li>')
    .replace(/(<li>[\s\S]*?<\/li>\n?)+/g, m => `<ul>${m}</ul>`)
    .replace(/(^|\n)([^<\n][^\n]+)(?=\n|$)/g, (_, prefix: string, line: string) => `${prefix}<p>${line}</p>`)
}

// Split content so ```mermaid``` flow blocks render as the modern FlowDiagram
// component (not a raw code box), while everything else stays HTML.
interface ContentSegment { type: 'html' | 'flow', content: string }

function toSegments(md: string): ContentSegment[] {
  const segs: ContentSegment[] = []
  const re = /```mermaid\n([\s\S]*?)```/g
  let last = 0
  let m: RegExpExecArray | null
  while ((m = re.exec(md)) !== null) {
    if (m.index > last) segs.push({ type: 'html', content: renderMarkdown(md.slice(last, m.index)) })
    segs.push({ type: 'flow', content: (m[1] ?? '').trim() })
    last = m.index + m[0].length
  }
  if (last < md.length) segs.push({ type: 'html', content: renderMarkdown(md.slice(last)) })
  return segs
}

// ─── Seniority tier parsing ───────────────────────────────────────────
// Convention: a step's concept_content is structured as up to three
// top-level H2 sections whose heading text begins with "Core",
// "Deeper dive" or "Senior". Steps that don't follow the convention
// render untouched via the fallback below.
type Tier = 'core' | 'mid' | 'senior'

function classifyHeading(text: string): Tier {
  const lower = text.toLowerCase().trim()
  if (lower.startsWith('deeper dive')) return 'mid'
  if (lower.startsWith('senior')) return 'senior'
  return 'core'
}

interface TocEntry {
  id: string
  text: string
  level: 2 | 3
  tier: Tier
}

interface TierSection {
  tier: Tier
  markdown: string
  segments: ContentSegment[]
  headings: TocEntry[]
}

const tierSections = computed<TierSection[]>(() => {
  const md = props.step.concept_content ?? ''
  // Split right BEFORE every "## " (lookahead). Drop the leading chunk if
  // it doesn't start with a tier H2 — that's intro prose, prepended to
  // the first real tier so we don't lose it.
  const chunks = md.split(/(?=^## )/m).filter(c => c.trim())
  if (!chunks.length) return []

  const sections: TierSection[] = []
  let prelude = ''
  if (!chunks[0]!.startsWith('## ')) {
    prelude = chunks.shift()!
  }

  for (const chunk of chunks) {
    const lines = chunk.split('\n')
    const h2Line = lines[0]!.match(/^## (.+)$/)
    if (!h2Line) continue
    const h2Text = h2Line[1]!
    const tier = classifyHeading(h2Text)

    const headings: TocEntry[] = [{ id: slug(h2Text), text: h2Text, level: 2, tier }]
    for (const line of lines.slice(1)) {
      const h3 = line.match(/^### (.+)$/)
      if (h3) headings.push({ id: slug(h3[1]!), text: h3[1]!, level: 3, tier })
    }

    // The first section absorbs any prelude so untiered intro prose
    // stays visible.
    const markdown = prelude && sections.length === 0 ? prelude + chunk : chunk
    sections.push({ tier, markdown, segments: toSegments(markdown), headings })
  }

  return sections
})

const hasTiers = computed(() => {
  // Only treat content as tiered if at least one section is non-core OR
  // an explicit "Core" header is present. Otherwise fall back to flat
  // rendering for legacy steps.
  const tiers = new Set(tierSections.value.map(s => s.tier))
  if (tiers.size > 1) return true
  const firstH2 = (props.step.concept_content ?? '').match(/^## (.+)$/m)
  return firstH2 ? /^(core|deeper dive|senior)/i.test(firstH2[1]!) : false
})

// Untiered fallback render.
const renderedSegments = computed(() => toSegments(props.step.concept_content ?? ''))

const toc = computed<TocEntry[]>(() => {
  if (hasTiers.value) {
    return tierSections.value.flatMap(s => s.headings)
  }
  // Untiered: only show H2 entries as before.
  const matches = [...(props.step.concept_content ?? '').matchAll(/^## (.+)$/gm)]
  return matches.map(m => ({ id: slug(m[1]!), text: m[1]!, level: 2 as const, tier: 'core' as const }))
})

// ─── Filter pill bar ──────────────────────────────────────────────────
type Filter = 'all' | 'junior' | 'mid' | 'senior'
const activeFilter = ref<Filter>('all')
const tierFilterOptions: Array<{ value: Filter; label: string }> = [
  { value: 'all',    label: 'All' },
  { value: 'junior', label: 'Junior' },
  { value: 'mid',    label: 'Mid' },
  { value: 'senior', label: 'Senior' },
]

// Map filter intent → which tiers are visible. Each filter focuses on a
// single tier; "All" shows everything.
function isTierVisible(tier: Tier): boolean {
  if (activeFilter.value === 'all') return true
  if (activeFilter.value === 'junior') return tier === 'core'
  if (activeFilter.value === 'mid')    return tier === 'mid'
  if (activeFilter.value === 'senior') return tier === 'senior'
  return true
}

// ─── Scroll-spy ────────────────────────────────────────────────────────
const contentRef = ref<HTMLElement | null>(null)
const activeTocId = ref<string>('')

function scrollTo(id: string) {
  const el = document.getElementById(id)
  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

onMounted(() => {
  if (!import.meta.client) return
  activeTocId.value = toc.value[0]?.id ?? ''

  const observer = new IntersectionObserver(
    (entries) => {
      for (const entry of entries) {
        if (entry.isIntersecting) activeTocId.value = entry.target.id
      }
    },
    { rootMargin: '-20% 0px -70% 0px', threshold: 0 },
  )

  nextTick(() => {
    if (!contentRef.value) return
    contentRef.value.querySelectorAll('h2[id], h3[id]').forEach(h => observer.observe(h))
  })

  onUnmounted(() => observer.disconnect())
})

// ─── Playground (Monaco-backed editor, Judge0-backed Run) ─────────────
const playgroundCode = ref(props.step.playground_starter_code ?? '')
const playgroundOutput = ref('')
const playgroundError = ref('')
const playgroundRunning = ref(false)
const playgroundDurationMs = ref<number | null>(null)
const playgroundAnchor = ref<HTMLElement | null>(null)
const activeTaskId = ref<number | null>(null)
const toast = useToast()
const api = useApi()

const monacoOptions = {
  fontSize: 13,
  fontFamily: "'JetBrains Mono', 'Fira Code', monospace",
  fontLigatures: true,
  lineHeight: 20,
  minimap: { enabled: false },
  scrollBeyondLastLine: false,
  padding: { top: 16, bottom: 16 },
  tabSize: 4,
  wordWrap: 'on' as const,
  renderLineHighlight: 'gutter' as const,
  overviewRulerBorder: false,
  hideCursorInOverviewRuler: true,
}

interface PlaygroundResult {
  ok: boolean
  stdout: string
  stderr: string
  exit_code: number
  duration_ms: number
  status: string
}

function resetPlayground() {
  playgroundCode.value = props.step.playground_starter_code ?? ''
  playgroundOutput.value = ''
  playgroundError.value = ''
  playgroundDurationMs.value = null
  activeTaskId.value = null
}

async function runPlayground() {
  if (playgroundRunning.value) return
  playgroundRunning.value = true
  playgroundOutput.value = ''
  playgroundError.value = ''
  playgroundDurationMs.value = null
  try {
    const result = await api.post<PlaygroundResult>('/playground/run', {
      code: playgroundCode.value,
      language: 'php',
    })
    playgroundDurationMs.value = result.duration_ms
    // Compile errors and timeouts already arrive as stderr in our shape.
    if (result.stderr && !result.ok) {
      playgroundError.value = result.stderr
    } else {
      playgroundOutput.value = result.stdout || '(no output)'
      // Surface stderr separately if both stdout and stderr came through.
      if (result.stderr) {
        playgroundError.value = result.stderr
      }
    }
  } catch (err: unknown) {
    const data = (err as { data?: { message?: string }, status?: number })
    if (data?.status === 429) {
      toast.add({
        title: 'Slow down',
        description: 'Too many runs in a short window. Try again in a minute.',
        color: 'amber',
      })
    } else {
      playgroundError.value = data?.data?.message ?? 'Sandbox request failed. Please try again.'
    }
  } finally {
    playgroundRunning.value = false
  }
}

// Load a task's starter into the playground and scroll the editor into
// view. Always overwrites — users can click Reset on the playground to
// go back to the step's primary starter.
function loadTaskIntoPlayground(task: { id: number; starter_code?: string | null }) {
  if (!task.starter_code) return
  activeTaskId.value = task.id
  playgroundCode.value = task.starter_code
  playgroundOutput.value = ''
  playgroundError.value = ''
  nextTick(() => {
    playgroundAnchor.value?.scrollIntoView({ behavior: 'smooth', block: 'start' })
  })
}
</script>

<style scoped>
/* Content colours are driven by CSS variables so a single high-specificity
   selector (:global(.dark) .step-concept) flips the whole theme. This avoids
   the Vue scoped-style gotcha where per-element ":global(.dark) …:deep()"
   overrides tie on specificity with their light counterparts and lose on
   source order (which left dark text unreadable and code blocks white). */
.step-concept {
  --sc-h2: rgb(17 24 39);
  --sc-h3: rgb(31 41 55);
  --sc-p: rgb(55 65 81);
  --sc-strong: rgb(17 24 39);
  --sc-code-bg: rgb(241 245 249);
  --sc-code-fg: rgb(4 120 87);
  --sc-pre-bg: rgb(248 250 252);
  --sc-pre-border: rgb(226 232 240);
  --sc-pre-fg: rgb(15 23 42);
  --sc-td-fg: rgb(55 65 81);
  --sc-td-border: rgb(226 232 240);
  --sc-th-bg: rgb(248 250 252);
  --sc-th-fg: rgb(15 23 42);
  --sc-divider: rgb(226 232 240);
  --sc-lang: rgb(148 163 184);
  --sc-tier-core: rgb(6 95 70);
  --sc-tier-mid: rgb(146 64 14);
  --sc-tier-senior: rgb(91 33 182);
  --t-kw: #a626a4; --t-ty: #c18401; --t-st: #50a14f; --t-co: #a0a1a7; --t-vr: #e45649;
  --t-fn: #4078f2; --t-cl: #c18401; --t-nu: #986801; --t-op: #383a42; --t-tg: #a626a4;
}
/* Dark overrides live in a NON-scoped <style> block below (html.dark …) so
   they out-specify the scoped light defaults — a scoped :global(.dark) rule
   ties on specificity and loses on source order. */

.step-concept :deep(h2) {
  scroll-margin-top: 80px;
  margin: 32px 0 12px;
  font-size: 1.25rem;
  font-weight: 700;
  letter-spacing: -0.015em;
  color: var(--sc-h2);
}
.step-concept :deep(h2:first-child) { margin-top: 0; }

/* ── Tier sections ─────────────────────────────────────────────── */
/* Each tier is rendered inside its own <section data-tier="...">.
   The section's own H2 (the tier name) gets a stronger visual treatment
   with a colored eyebrow tag so the reader feels the level change. */
.step-concept__tier {
  position: relative;
  padding-top: 8px;
}
.step-concept__tier + .step-concept__tier {
  margin-top: 48px;
  padding-top: 32px;
  border-top: 1px dashed var(--sc-divider);
}

.step-concept__tier :deep(h2) {
  position: relative;
  margin-top: 12px;
  margin-bottom: 18px;
  font-size: 1.5rem;
  letter-spacing: -0.02em;
}
.step-concept__tier :deep(h2)::before {
  content: attr(data-tier-label);
  display: block;
  margin-bottom: 8px;
  font-size: 0.65rem;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
}
.step-concept__tier[data-tier="core"] :deep(h2)::before {
  content: 'Core — foundations';
  color: rgb(5 150 105);
}
.step-concept__tier[data-tier="mid"] :deep(h2)::before {
  content: 'Deeper dive — for mid-level engineers';
  color: rgb(217 119 6);
}
.step-concept__tier[data-tier="senior"] :deep(h2)::before {
  content: 'Senior insights — interview & architecture';
  color: rgb(124 58 237);
}
.step-concept__tier[data-tier="core"]   :deep(h2) { color: var(--sc-tier-core); }
.step-concept__tier[data-tier="mid"]    :deep(h2) { color: var(--sc-tier-mid); }
.step-concept__tier[data-tier="senior"] :deep(h2) { color: var(--sc-tier-senior); }

.step-concept :deep(h3) {
  scroll-margin-top: 80px;
  margin: 24px 0 8px;
  font-size: 1rem;
  font-weight: 600;
  color: var(--sc-h3);
}

.step-concept :deep(p) {
  margin: 0 0 14px;
  font-size: 0.9rem;
  line-height: 1.7;
  color: var(--sc-p);
}

.step-concept :deep(strong) {
  font-weight: 600;
  color: var(--sc-strong);
}

.step-concept :deep(:not(pre) > code) {
  padding: 1px 6px;
  border-radius: 4px;
  background: var(--sc-code-bg);
  color: var(--sc-code-fg);
  font-family: 'JetBrains Mono', 'SF Mono', Menlo, monospace;
  font-size: 0.82em;
  font-weight: 500;
}

.step-concept :deep(pre) {
  position: relative;
  margin: 16px 0;
  padding: 16px 18px;
  border: 1px solid var(--sc-pre-border);
  border-radius: 10px;
  background: var(--sc-pre-bg);
  overflow-x: auto;
  font-size: 0.82rem;
  line-height: 1.6;
}
.step-concept :deep(pre)::before {
  content: attr(data-lang);
  position: absolute;
  top: 8px;
  right: 12px;
  font-size: 0.65rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--sc-lang);
}
.step-concept :deep(pre code) {
  font-family: 'JetBrains Mono', 'SF Mono', Menlo, monospace;
  color: var(--sc-pre-fg);
}

.step-concept :deep(ul) {
  margin: 0 0 14px;
  padding-left: 22px;
  list-style: disc;
}
.step-concept :deep(li) {
  margin-bottom: 6px;
  font-size: 0.9rem;
  line-height: 1.65;
  color: var(--sc-p);
}

.step-concept :deep(table) {
  width: 100%;
  margin: 16px 0;
  border-collapse: collapse;
  font-size: 0.85rem;
}
.step-concept :deep(td) {
  padding: 8px 12px;
  border: 1px solid var(--sc-td-border);
  color: var(--sc-td-fg);
}
.step-concept :deep(tr:first-child td) {
  background: var(--sc-th-bg);
  font-weight: 600;
  color: var(--sc-th-fg);
}

/* Syntax highlighting — colours flip via the CSS vars above (light / dark) */
.step-concept :deep(.t-kw) { color: var(--t-kw); font-weight: 600; }
.step-concept :deep(.t-ty) { color: var(--t-ty); font-weight: 500; }
.step-concept :deep(.t-st) { color: var(--t-st); }
.step-concept :deep(.t-co) { color: var(--t-co); font-style: italic; }
.step-concept :deep(.t-vr) { color: var(--t-vr); }
.step-concept :deep(.t-fn) { color: var(--t-fn); }
.step-concept :deep(.t-cl) { color: var(--t-cl); }
.step-concept :deep(.t-nu) { color: var(--t-nu); }
.step-concept :deep(.t-op) { color: var(--t-op); }
.step-concept :deep(.t-tg) { color: var(--t-tg); font-weight: 600; }

/* Code playground (Monaco-backed editor) */
.playground-editor {
  background: rgb(30 30 30); /* matches vs-dark theme */
  height: 320px;
  min-height: 320px;
  overflow: hidden;
}
.playground-editor :deep(.monaco-editor) {
  /* Let Monaco fill the container without inheriting any prose font-size. */
  font-size: 13px;
}
.playground-output {
  margin: 0;
  font-family: 'JetBrains Mono', 'SF Mono', Menlo, monospace;
  font-size: 0.78rem;
  line-height: 1.6;
  color: rgb(55 65 81);
  white-space: pre-wrap;
  word-break: break-word;
}
:global(.dark) .playground-output { color: rgb(203 213 225); }
.playground-output--err {
  color: rgb(220 38 38);
}
:global(.dark) .playground-output--err {
  color: rgb(252 165 165);
}
</style>

<!-- Non-scoped: dark theme flips the step-content CSS variables. `html.dark`
     out-specifies the scoped light defaults regardless of source order. -->
<style>
html.dark .step-concept {
  --sc-h2: rgb(244 244 245);
  --sc-h3: rgb(228 228 231);
  --sc-p: rgb(212 212 216);
  --sc-strong: rgb(250 250 250);
  --sc-code-bg: rgb(38 38 38);
  --sc-code-fg: rgb(110 231 183);
  --sc-pre-bg: rgb(23 23 23);
  --sc-pre-border: rgb(45 45 45);
  --sc-pre-fg: rgb(228 228 231);
  --sc-td-fg: rgb(212 212 216);
  --sc-td-border: rgb(64 64 64);
  --sc-th-bg: rgb(38 38 38);
  --sc-th-fg: rgb(244 244 245);
  --sc-divider: rgb(64 64 64);
  --sc-lang: rgb(115 115 115);
  --sc-tier-core: rgb(110 231 183);
  --sc-tier-mid: rgb(252 211 77);
  --sc-tier-senior: rgb(196 181 253);
  --t-kw: #c586c0; --t-ty: #4ec9b0; --t-st: #ce9178; --t-co: #6a9955; --t-vr: #9cdcfe;
  --t-fn: #dcdcaa; --t-cl: #4ec9b0; --t-nu: #b5cea8; --t-op: #d4d4d4; --t-tg: #569cd6;
}
</style>
