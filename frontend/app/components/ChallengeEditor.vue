<template>
  <div class="flex h-full min-h-screen flex-col bg-slate-950">
    <!-- Top bar -->
    <div class="flex h-12 items-center gap-4 border-b border-slate-800 px-4">
      <button
        class="flex items-center gap-1.5 text-sm text-slate-400 transition-colors hover:text-slate-200"
        @click="emit('back')"
      >
        <ArrowLeft :size="15" />
        Back
      </button>

      <div class="h-4 w-px bg-slate-700" />

      <span class="text-xs text-slate-500 uppercase tracking-widest font-medium">PHP</span>
      <span class="text-xs text-slate-600">/</span>
      <span class="text-sm font-medium text-slate-300">{{ challenge.title }}</span>

      <div class="ml-auto flex items-center gap-2">
        <span class="rounded px-2 py-0.5 text-xs font-semibold uppercase tracking-wide" :class="difficultyClass">
          {{ challenge.difficulty }}
        </span>
      </div>
    </div>

    <!-- Body -->
    <div class="flex flex-1 overflow-hidden">
      <!-- Editor -->
      <div class="relative flex flex-1 flex-col overflow-hidden border-r border-slate-800">
        <ClientOnly>
          <VueMonacoEditor
            v-model:value="code"
            language="php"
            theme="vs-dark"
            :options="editorOptions"
            class="flex-1"
            style="min-height: 0; height: 100%"
          />
          <template #fallback>
            <div class="flex flex-1 items-center justify-center text-sm text-slate-600">
              Loading editor…
            </div>
          </template>
        </ClientOnly>

        <!-- Run bar -->
        <div class="flex h-12 items-center gap-3 border-t border-slate-800 bg-slate-900 px-4">
          <button
            class="flex items-center gap-2 rounded-md bg-emerald-600 px-4 py-1.5 text-sm font-semibold text-white transition-colors hover:bg-emerald-500 disabled:opacity-50"
            :disabled="running"
            @click="runTests"
          >
            <Play v-if="!running" :size="13" />
            <Loader2 v-else :size="13" class="animate-spin" />
            {{ running ? 'Running…' : 'Run Tests' }}
          </button>

          <button
            class="rounded-md border border-slate-700 px-3 py-1.5 text-xs text-slate-400 transition-colors hover:border-slate-500 hover:text-slate-200"
            @click="resetCode"
          >
            Reset
          </button>

          <div v-if="lastResult" class="ml-auto flex items-center gap-1.5 text-xs">
            <CheckCircle v-if="lastResult.passed" :size="13" class="text-emerald-500" />
            <XCircle v-else :size="13" class="text-rose-500" />
            <span :class="lastResult.passed ? 'text-emerald-400' : 'text-rose-400'">
              {{ lastResult.passed ? 'All tests passed' : `${lastResult.failedCount} test(s) failed` }}
            </span>
          </div>
        </div>
      </div>

      <!-- Right panel -->
      <div class="flex w-96 flex-shrink-0 flex-col overflow-hidden">
        <!-- Tabs -->
        <div class="flex border-b border-slate-800">
          <button
            v-for="tab in ['Instructions', 'Results']"
            :key="tab"
            class="px-4 py-2.5 text-xs font-medium uppercase tracking-wider transition-colors"
            :class="
              activeTab === tab
                ? 'border-b-2 border-emerald-500 text-emerald-400'
                : 'text-slate-500 hover:text-slate-300'
            "
            @click="activeTab = tab as 'Instructions' | 'Results'"
          >
            {{ tab }}
          </button>
        </div>

        <!-- Instructions -->
        <div v-if="activeTab === 'Instructions'" class="flex-1 overflow-y-auto p-5">
          <div class="prose prose-sm prose-invert prose-slate max-w-none
                      prose-headings:font-semibold prose-headings:text-slate-200
                      prose-p:text-slate-400 prose-p:leading-relaxed
                      prose-code:rounded prose-code:bg-slate-800 prose-code:px-1 prose-code:py-0.5 prose-code:text-emerald-300 prose-code:text-xs
                      prose-pre:border prose-pre:border-slate-700 prose-pre:bg-slate-900
                      prose-strong:text-slate-300
                      prose-a:text-emerald-400">
            <!-- eslint-disable-next-line vue/no-v-html -->
            <div v-html="renderedDescription" />
          </div>
        </div>

        <!-- Results -->
        <div v-if="activeTab === 'Results'" class="flex-1 overflow-y-auto p-4">
          <div v-if="!lastResult" class="py-12 text-center">
            <SquareCode :size="28" class="mx-auto mb-3 text-slate-700" />
            <p class="text-xs text-slate-600">Run tests to see results here.</p>
          </div>

          <div v-else>
            <div class="mb-4 flex items-center gap-2">
              <component :is="lastResult.passed ? CheckCircle : XCircle" :size="16" :class="lastResult.passed ? 'text-emerald-500' : 'text-rose-500'" />
              <span class="text-sm font-medium" :class="lastResult.passed ? 'text-emerald-400' : 'text-rose-400'">
                {{ lastResult.passed ? 'All tests passed' : 'Tests failed' }}
              </span>
              <span class="ml-auto text-xs text-slate-600 tabular-nums">{{ lastResult.duration }}ms</span>
            </div>

            <div class="space-y-2">
              <div
                v-for="(test, i) in lastResult.tests"
                :key="i"
                class="rounded-lg border p-3"
                :class="test.passed ? 'border-emerald-800/40 bg-emerald-950/20' : 'border-rose-800/40 bg-rose-950/20'"
              >
                <div class="flex items-start gap-2">
                  <CheckCircle v-if="test.passed" :size="13" class="mt-0.5 shrink-0 text-emerald-500" />
                  <XCircle v-else :size="13" class="mt-0.5 shrink-0 text-rose-500" />
                  <span class="text-xs text-slate-300 leading-snug">{{ test.name }}</span>
                </div>
                <template v-if="!test.passed && test.message">
                  <div v-if="parseDiff(test.message)" class="mt-2 rounded bg-slate-950 border border-slate-800 overflow-hidden font-mono text-xs">
                    <div class="flex items-baseline gap-3 px-3 py-1.5 border-b border-slate-800/60 bg-emerald-950/20">
                      <span class="text-emerald-500 select-none w-3 shrink-0">+</span>
                      <span class="text-slate-500 w-16 shrink-0">expected</span>
                      <span class="text-emerald-300 break-all">{{ parseDiff(test.message)?.expected }}</span>
                    </div>
                    <div class="flex items-baseline gap-3 px-3 py-1.5 bg-rose-950/20">
                      <span class="text-rose-500 select-none w-3 shrink-0">−</span>
                      <span class="text-slate-500 w-16 shrink-0">received</span>
                      <span class="text-rose-300 break-all">{{ parseDiff(test.message)?.actual }}</span>
                    </div>
                  </div>
                  <pre v-else class="mt-2 overflow-x-auto rounded bg-slate-950 border border-slate-800 px-3 py-2 text-xs text-slate-400 leading-relaxed whitespace-pre-wrap">{{ test.message }}</pre>
                </template>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ArrowLeft, CheckCircle, XCircle, Play, Loader2, SquareCode } from 'lucide-vue-next'
import { VueMonacoEditor } from '@guolao/vue-monaco-editor'
import type { Challenge } from '~/types/models'

const props = defineProps<{
  challenge: Challenge
}>()

const emit = defineEmits<{
  back: []
  completed: [challenge: Challenge]
}>()

const { post } = useApi()

const code = ref(props.challenge.boilerplate_code)
const activeTab = ref<'Instructions' | 'Results'>('Instructions')
const running = ref(false)

interface TestResult {
  passed: boolean
  failedCount: number
  duration: number
  tests: Array<{ name: string; passed: boolean; message?: string }>
}

const lastResult = ref<TestResult | null>(null)

const editorOptions = {
  fontSize: 13,
  fontFamily: "'JetBrains Mono', 'Fira Code', monospace",
  fontLigatures: true,
  lineHeight: 20,
  minimap: { enabled: false },
  scrollBeyondLastLine: false,
  padding: { top: 16 },
  tabSize: 4,
  wordWrap: 'on' as const,
  renderLineHighlight: 'gutter' as const,
  overviewRulerBorder: false,
  hideCursorInOverviewRuler: true,
}

const difficultyClass = computed(() => ({
  beginner: 'bg-emerald-500/10 text-emerald-400',
  intermediate: 'bg-amber-500/10 text-amber-400',
  advanced: 'bg-rose-500/10 text-rose-400',
}[props.challenge.difficulty]))

const renderedDescription = computed(() => {
  return props.challenge.description
    .replace(/^### (.+)$/gm, '<h3>$1</h3>')
    .replace(/^## (.+)$/gm, '<h2>$1</h2>')
    .replace(/^# (.+)$/gm, '<h1>$1</h1>')
    .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
    .replace(/`([^`\n]+)`/g, '<code>$1</code>')
    .replace(/```(\w*)\n([\s\S]*?)```/g, '<pre><code>$2</code></pre>')
    .replace(/^\| (.+) \|$/gm, (_, row) => {
      const cells = row.split(' | ').map((c: string) => `<td class="border border-slate-700 px-2 py-1 text-xs">${c}</td>`).join('')
      return `<tr>${cells}</tr>`
    })
    .replace(/(<tr>[\s\S]*?<\/tr>\n?)+/g, match => `<table class="w-full border-collapse my-3">${match}</table>`)
    .replace(/\n\n/g, '</p><p>')
})

async function runTests() {
  running.value = true
  activeTab.value = 'Results'
  lastResult.value = null

  try {
    const result = await post<TestResult>(`/challenges/${props.challenge.slug}/run`, {
      code: code.value,
    })
    lastResult.value = result
    if (result.passed) emit('completed', props.challenge)
  } catch {
    lastResult.value = {
      passed: false,
      failedCount: 1,
      duration: 0,
      tests: [{ name: 'Error running tests', passed: false, message: 'An unexpected error occurred.' }],
    }
  } finally {
    running.value = false
  }
}

function resetCode() {
  code.value = props.challenge.boilerplate_code
  lastResult.value = null
}

interface AssertionDiff { expected: string; actual: string }

function parseDiff(message: string): AssertionDiff | null {
  let m: RegExpMatchArray | null

  m = message.match(/^Failed asserting that (.+) is identical to (.+)\.$/)
  if (m) return { actual: m[1]!, expected: m[2]! }

  m = message.match(/^Failed asserting that (.+) equals (.+)\.$/)
  if (m) return { actual: m[1]!, expected: m[2]! }

  m = message.match(/^Failed asserting that (.+) is greater than (.+)\.$/)
  if (m) return { actual: m[1]!, expected: `> ${m[2]}` }

  m = message.match(/^Failed asserting that (.+) is less than (.+)\.$/)
  if (m) return { actual: m[1]!, expected: `< ${m[2]}` }

  m = message.match(/^Failed asserting that actual size (\d+) matches expected size (\d+)\.$/)
  if (m) return { actual: `count: ${m[1]}`, expected: `count: ${m[2]}` }

  m = message.match(/^Failed asserting that (.+) is an instance of (.+)\.$/)
  if (m) return { actual: m[1]!, expected: `instanceof ${m[2]}` }

  m = message.match(/^Failed asserting that array contains (.+)\.$/)
  if (m) return { actual: '(not in array)', expected: m[1]! }

  m = message.match(/^Failed asserting that (.+) is null\.$/)
  if (m) return { actual: m[1]!, expected: 'null' }

  return null
}
</script>
