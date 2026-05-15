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

          <!-- TOC -->
          <UCard v-if="toc.length" :ui="{ body: { padding: 'p-4' } }">
            <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
              On this step
            </p>
            <nav class="space-y-1">
              <a
                v-for="t in toc"
                :key="t.id"
                :href="`#${t.id}`"
                class="block rounded-md px-2 py-1 text-xs transition-colors"
                :class="activeTocId === t.id
                  ? 'bg-emerald-50 font-semibold text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300'
                  : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-slate-800/60 dark:hover:text-gray-200'"
                @click.prevent="scrollTo(t.id)"
              >
                {{ t.text }}
              </a>
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
          <article ref="contentRef" class="step-concept">
            <!-- eslint-disable-next-line vue/no-v-html -->
            <div v-html="renderedConcept" />
          </article>
        </UCard>

        <!-- Code playground (read-only mock for now; execution comes in a follow-up PR) -->
        <UCard v-if="step.has_playground && step.playground_starter_code" :ui="{ body: { padding: 'p-0' } }">
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
            <pre><code><span
              v-for="(line, idx) in playgroundLines"
              :key="idx"
              class="playground-line"
            ><span class="playground-line__num">{{ idx + 1 }}</span><!-- eslint-disable-next-line vue/no-v-html
            --><span class="playground-line__src" v-html="line || '&nbsp;'" /></span></code></pre>
          </div>

          <div class="border-t border-gray-200 bg-gray-50 px-4 py-3 dark:border-slate-800 dark:bg-slate-900">
            <p class="mb-1.5 text-[10px] font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">
              Output
            </p>
            <pre v-if="playgroundOutput" class="playground-output">{{ playgroundOutput }}</pre>
            <p v-else class="font-mono text-xs italic text-gray-400 dark:text-gray-600">
              Press Run to see the output here.
            </p>
          </div>
        </UCard>

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
              class="group flex items-start gap-3 rounded-lg border border-gray-100 px-3 py-2.5 transition-colors hover:border-gray-200 dark:border-gray-700 dark:hover:border-gray-600"
              :class="checked.has(ins.id) && 'bg-emerald-50/60 dark:bg-emerald-950/20'"
            >
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
              <div class="min-w-0">
                <p
                  class="text-sm leading-relaxed text-gray-800 dark:text-gray-200"
                  :class="checked.has(ins.id) && 'text-gray-400 line-through dark:text-gray-500'"
                >
                  <span class="mr-2 text-xs font-semibold text-gray-400">{{ String(i + 1).padStart(2, '0') }}.</span>
                  {{ ins.text }}
                </p>
              </div>
            </li>
          </ul>
        </UCard>

        <!-- Linked challenge card -->
        <div
          v-if="step.challenge"
          class="overflow-hidden rounded-xl border border-emerald-200 bg-gradient-to-br from-emerald-50 via-white to-emerald-50 p-5 shadow-sm dark:border-emerald-900/40 dark:from-emerald-950/40 dark:via-slate-900 dark:to-emerald-950/30"
        >
          <div class="flex flex-wrap items-center gap-5">
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-lg shadow-emerald-500/30">
              <UIcon name="i-heroicons-trophy" class="h-7 w-7" />
            </div>
            <div class="min-w-0 flex-1">
              <p class="mb-1 text-[11px] font-bold uppercase tracking-widest text-emerald-700 dark:text-emerald-400">
                Graded challenge
              </p>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ step.challenge.title }}</h3>
              <p v-if="step.challenge.description" class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ step.challenge.description }}
              </p>
              <div class="mt-2.5 flex flex-wrap items-center gap-2">
                <UBadge color="emerald" variant="subtle" size="xs" icon="i-heroicons-bolt">
                  {{ step.challenge.difficulty }}
                </UBadge>
              </div>
            </div>
            <div class="flex shrink-0 items-center gap-2 self-stretch sm:self-center">
              <UButton
                color="emerald"
                size="md"
                trailing-icon="i-heroicons-arrow-right"
                @click="$emit('open-challenge')"
              >
                Open challenge
              </UButton>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { PathStep, StepDifficulty } from '~/composables/usePaths'

const props = defineProps<{
  step: PathStep
  saving?: boolean
}>()

defineEmits<{
  (e: 'status', value: NonNullable<PathStep['user_status']>): void
  (e: 'open-challenge'): void
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
  switch (props.step.difficulty as StepDifficulty | null | undefined) {
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

const renderedConcept = computed(() => renderMarkdown(props.step.concept_content ?? ''))

const toc = computed(() => {
  const matches = [...(props.step.concept_content ?? '').matchAll(/^## (.+)$/gm)]
  return matches.map(m => ({ id: slug(m[1]!), text: m[1]! }))
})

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
    contentRef.value.querySelectorAll('h2[id]').forEach(h => observer.observe(h))
  })

  onUnmounted(() => observer.disconnect())
})

// ─── Playground (mocked; execution will land in a follow-up PR) ───────
const playgroundCode = ref(props.step.playground_starter_code ?? '')
const playgroundOutput = ref('')
const playgroundRunning = ref(false)
const playgroundLines = computed(() => playgroundCode.value.split('\n').map(highlightPhp))
const toast = useToast()

function resetPlayground() {
  playgroundCode.value = props.step.playground_starter_code ?? ''
  playgroundOutput.value = ''
}

async function runPlayground() {
  playgroundRunning.value = true
  playgroundOutput.value = ''
  // TODO: replace with a real call to a playground endpoint (Judge0 backed)
  // that executes arbitrary PHP without comparing against tests_code.
  await new Promise(r => setTimeout(r, 500))
  playgroundRunning.value = false
  toast.add({
    title: 'Playground execution coming soon',
    description: 'Wire-up to Judge0 lands in a follow-up PR.',
    color: 'amber',
  })
}
</script>

<style scoped>
.step-concept :deep(h2) {
  scroll-margin-top: 80px;
  margin: 32px 0 12px;
  font-size: 1.25rem;
  font-weight: 700;
  letter-spacing: -0.015em;
  color: rgb(17 24 39);
}
:global(.dark) .step-concept :deep(h2) { color: rgb(243 244 246); }
.step-concept :deep(h2:first-child) { margin-top: 0; }

.step-concept :deep(h3) {
  scroll-margin-top: 80px;
  margin: 24px 0 8px;
  font-size: 1rem;
  font-weight: 600;
  color: rgb(31 41 55);
}
:global(.dark) .step-concept :deep(h3) { color: rgb(229 231 235); }

.step-concept :deep(p) {
  margin: 0 0 14px;
  font-size: 0.9rem;
  line-height: 1.7;
  color: rgb(55 65 81);
}
:global(.dark) .step-concept :deep(p) { color: rgb(209 213 219); }

.step-concept :deep(strong) {
  font-weight: 600;
  color: rgb(17 24 39);
}
:global(.dark) .step-concept :deep(strong) { color: rgb(243 244 246); }

.step-concept :deep(:not(pre) > code) {
  padding: 1px 6px;
  border-radius: 4px;
  background: rgb(241 245 249);
  color: rgb(4 120 87);
  font-family: 'JetBrains Mono', 'SF Mono', Menlo, monospace;
  font-size: 0.82em;
  font-weight: 500;
}
:global(.dark) .step-concept :deep(:not(pre) > code) {
  background: rgb(30 41 59);
  color: rgb(110 231 183);
}

.step-concept :deep(pre) {
  position: relative;
  margin: 16px 0;
  padding: 16px 18px;
  border: 1px solid rgb(226 232 240);
  border-radius: 10px;
  background: rgb(248 250 252);
  overflow-x: auto;
  font-size: 0.82rem;
  line-height: 1.6;
}
:global(.dark) .step-concept :deep(pre) {
  border-color: rgb(30 41 59);
  background: rgb(15 23 42);
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
  color: rgb(148 163 184);
}
.step-concept :deep(pre code) {
  font-family: 'JetBrains Mono', 'SF Mono', Menlo, monospace;
  color: rgb(15 23 42);
}
:global(.dark) .step-concept :deep(pre code) { color: rgb(226 232 240); }

.step-concept :deep(ul) {
  margin: 0 0 14px;
  padding-left: 22px;
  list-style: disc;
}
.step-concept :deep(li) {
  margin-bottom: 6px;
  font-size: 0.9rem;
  line-height: 1.65;
  color: rgb(55 65 81);
}
:global(.dark) .step-concept :deep(li) { color: rgb(209 213 219); }

.step-concept :deep(table) {
  width: 100%;
  margin: 16px 0;
  border-collapse: collapse;
  font-size: 0.85rem;
}
.step-concept :deep(td) {
  padding: 8px 12px;
  border: 1px solid rgb(226 232 240);
  color: rgb(55 65 81);
}
:global(.dark) .step-concept :deep(td) {
  border-color: rgb(51 65 85);
  color: rgb(203 213 225);
}
.step-concept :deep(tr:first-child td) {
  background: rgb(248 250 252);
  font-weight: 600;
  color: rgb(15 23 42);
}
:global(.dark) .step-concept :deep(tr:first-child td) {
  background: rgb(30 41 59);
  color: rgb(241 245 249);
}

/* Syntax highlighting — light */
.step-concept :deep(.t-kw) { color: #a626a4; }
.step-concept :deep(.t-ty) { color: #c18401; font-weight: 500; }
.step-concept :deep(.t-st) { color: #50a14f; }
.step-concept :deep(.t-co) { color: #a0a1a7; font-style: italic; }
.step-concept :deep(.t-vr) { color: #e45649; }
.step-concept :deep(.t-fn) { color: #4078f2; }
.step-concept :deep(.t-cl) { color: #c18401; }
.step-concept :deep(.t-nu) { color: #986801; }
.step-concept :deep(.t-op) { color: #383a42; }
.step-concept :deep(.t-tg) { color: #a626a4; font-weight: 600; }

/* Syntax highlighting — dark (VS Code Dark+) */
:global(.dark) .step-concept :deep(.t-kw) { color: #c586c0; }
:global(.dark) .step-concept :deep(.t-ty) { color: #4ec9b0; font-weight: 500; }
:global(.dark) .step-concept :deep(.t-st) { color: #ce9178; }
:global(.dark) .step-concept :deep(.t-co) { color: #6a9955; }
:global(.dark) .step-concept :deep(.t-vr) { color: #9cdcfe; }
:global(.dark) .step-concept :deep(.t-fn) { color: #dcdcaa; }
:global(.dark) .step-concept :deep(.t-cl) { color: #4ec9b0; }
:global(.dark) .step-concept :deep(.t-nu) { color: #b5cea8; }
:global(.dark) .step-concept :deep(.t-op) { color: #d4d4d4; }
:global(.dark) .step-concept :deep(.t-tg) { color: #569cd6; font-weight: 600; }

/* Code playground (read-only mock — Monaco lands in a follow-up) */
.playground-editor {
  background: rgb(15 23 42);
  color: rgb(226 232 240);
  font-family: 'JetBrains Mono', 'SF Mono', Menlo, monospace;
  font-size: 0.82rem;
  line-height: 1.65;
  overflow-x: auto;
}
.playground-editor pre {
  margin: 0;
  padding: 12px 0;
}
.playground-editor code { display: block; }
.playground-line {
  display: flex;
  align-items: baseline;
  padding-right: 16px;
}
.playground-line:hover { background: rgba(255, 255, 255, 0.03); }
.playground-line__num {
  display: inline-block;
  width: 44px;
  flex-shrink: 0;
  padding-right: 14px;
  text-align: right;
  color: rgb(100 116 139);
  user-select: none;
}
.playground-line__src {
  white-space: pre;
  flex: 1;
  min-width: 0;
}
.playground-output {
  margin: 0;
  font-family: 'JetBrains Mono', 'SF Mono', Menlo, monospace;
  font-size: 0.78rem;
  line-height: 1.6;
  color: rgb(55 65 81);
  white-space: pre-wrap;
}
:global(.dark) .playground-output { color: rgb(203 213 225); }
</style>
