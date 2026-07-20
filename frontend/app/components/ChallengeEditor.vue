<template>
  <div class="flex h-full min-h-screen flex-col bg-neutral-950">
    <!-- Top bar -->
    <div class="flex h-12 items-center gap-4 border-b border-neutral-800 px-4">
      <button
        class="flex items-center gap-1.5 text-sm text-neutral-400 transition-colors hover:text-neutral-200"
        @click="emit('back')"
      >
        <ArrowLeft :size="15" />
        Back
      </button>

      <div class="h-4 w-px bg-neutral-700" />

      <span class="text-xs text-neutral-500 uppercase tracking-widest font-medium">PHP</span>
      <span class="text-xs text-neutral-600">/</span>
      <span class="text-sm font-medium text-neutral-300">{{ challenge.title }}</span>

      <div class="ml-auto flex items-center gap-2">
        <span
          v-if="lastResult"
          class="flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[11px] font-semibold tabular-nums"
          :class="lastResult.passed ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400'"
        >
          <span class="h-1.5 w-1.5 rounded-full" :class="lastResult.passed ? 'bg-emerald-400' : 'bg-rose-400'" />
          {{ passedCount }}/{{ lastResult.tests.length }} passing
        </span>
        <span class="rounded px-2 py-0.5 text-xs font-semibold uppercase tracking-wide" :class="difficultyClass">
          {{ challenge.difficulty }}
        </span>
      </div>
    </div>

    <!-- Body: instructions left (reading order), editor right -->
    <div class="flex flex-1 flex-col overflow-hidden lg:flex-row">
      <!-- Left panel -->
      <div class="flex h-1/2 w-full flex-shrink-0 flex-col overflow-hidden border-b border-neutral-800 lg:h-auto lg:w-[26rem] lg:border-b-0 lg:border-r xl:w-[30rem]">
        <!-- Tabs -->
        <div class="flex border-b border-neutral-800">
          <button
            v-for="tab in visibleTabs"
            :key="tab"
            class="relative flex items-center gap-1.5 px-4 py-2.5 text-xs font-medium uppercase tracking-wider transition-colors"
            :class="
              activeTab === tab
                ? 'border-b-2 border-emerald-500 text-emerald-400'
                : 'text-neutral-500 hover:text-neutral-300'
            "
            @click="activeTab = tab"
          >
            {{ tab }}
            <span
              v-if="tab === 'Hints' && hints.length"
              class="rounded-full bg-amber-500/15 px-1.5 text-[10px] font-bold normal-case text-amber-400"
            >{{ hints.length }}</span>
            <span
              v-if="tab === 'Results' && lastResult"
              class="h-1.5 w-1.5 rounded-full"
              :class="lastResult.passed ? 'bg-emerald-400' : 'bg-rose-400'"
            />
            <span
              v-if="tab === 'Iterations' && iterations?.length"
              class="rounded-full bg-neutral-800 px-1.5 text-[10px] font-bold normal-case text-neutral-400"
            >{{ iterations.length }}</span>
          </button>
        </div>

        <!-- Instructions (structured sections, LabEx-style reading panel) -->
        <div v-show="activeTab === 'Instructions'" class="flex-1 overflow-y-auto p-5">
          <InstructionsPanel :markdown="parsed.instructions" />
        </div>

        <!-- Hints (progressive disclosure) -->
        <div v-show="activeTab === 'Hints'" class="flex-1 overflow-y-auto p-5">
          <ProgressiveHints :hints="hints" />
        </div>

        <!-- Iterations (submission history) -->
        <div v-show="activeTab === 'Iterations'" class="flex-1 overflow-y-auto p-4">
          <div v-if="loadingIterations" class="space-y-2 py-2">
            <div v-for="i in 3" :key="i" class="h-9 animate-pulse rounded-lg bg-neutral-900" />
          </div>

          <div v-else-if="!iterations?.length" class="py-12 text-center">
            <History :size="26" class="mx-auto mb-3 text-neutral-700" />
            <p class="text-xs text-neutral-600">No iterations yet.</p>
            <p class="mt-1 text-[11px] text-neutral-700">Every run is saved here — compare attempts, restore old code.</p>
          </div>

          <div v-else class="space-y-2">
            <div
              v-for="(it, i) in iterations"
              :key="it.id"
              class="overflow-hidden rounded-lg border transition-colors"
              :class="it.passed ? 'border-emerald-800/40' : 'border-neutral-800'"
            >
              <button
                class="flex w-full items-center gap-2.5 px-3 py-2.5 text-left hover:bg-neutral-900/60"
                @click="expandedIteration = expandedIteration === it.id ? null : it.id"
              >
                <CheckCircle v-if="it.passed" :size="13" class="shrink-0 text-emerald-500" />
                <XCircle v-else :size="13" class="shrink-0 text-rose-500" />
                <span class="text-xs font-semibold text-neutral-300">Iteration {{ iterations.length - i }}</span>
                <span class="text-[11px] text-neutral-600">
                  {{ it.passed ? 'passed' : `${it.failed_count} failed` }} · {{ it.duration_ms }}ms
                </span>
                <span class="ml-auto text-[11px] text-neutral-600">{{ timeAgo(it.created_at) }}</span>
                <ChevronDown :size="13" class="shrink-0 text-neutral-600 transition-transform" :class="expandedIteration === it.id ? 'rotate-180' : ''" />
              </button>

              <div v-if="expandedIteration === it.id" class="border-t border-neutral-800 p-3">
                <CopyableCode :code="it.code" />
                <button
                  class="mt-1 flex items-center gap-1.5 rounded-md border border-neutral-700 px-3 py-1.5 text-xs text-neutral-300 transition-colors hover:border-emerald-600 hover:text-emerald-400"
                  @click="restoreIteration(it)"
                >
                  <RotateCcw :size="12" />
                  Restore this code into the editor
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Results -->
        <div v-show="activeTab === 'Results'" class="flex-1 overflow-y-auto p-4">
          <!-- Running skeleton -->
          <div v-if="running" class="space-y-2 py-2">
            <div class="flex items-center gap-2 px-1 pb-2 text-xs text-neutral-500">
              <Loader2 :size="13" class="animate-spin text-emerald-500" />
              Running your solution in the sandbox…
            </div>
            <div v-for="i in 3" :key="i" class="h-10 animate-pulse rounded-lg bg-neutral-900" :style="{ animationDelay: `${i * 150}ms` }" />
          </div>

          <div v-else-if="!lastResult" class="py-12 text-center">
            <SquareCode :size="28" class="mx-auto mb-3 text-neutral-700" />
            <p class="text-xs text-neutral-600">Run tests to see results here.</p>
            <p class="mt-1 text-[11px] text-neutral-700">{{ runShortcutLabel }} in the editor works too.</p>
          </div>

          <div v-else>
            <!-- Celebration banner -->
            <div
              v-if="lastResult.passed"
              class="mb-4 rounded-lg border border-emerald-700/40 bg-emerald-950/30 p-4 text-center"
            >
              <PartyPopper :size="20" class="mx-auto mb-1.5 text-emerald-400" />
              <p class="text-sm font-semibold text-emerald-300">All tests passed — solved!</p>
              <p class="mt-0.5 text-[11px] text-emerald-500/80">Your progress and XP are being recorded.</p>
            </div>

            <!-- Summary + progress bar -->
            <div class="mb-1.5 flex items-center gap-2">
              <component :is="lastResult.passed ? CheckCircle : XCircle" :size="15" :class="lastResult.passed ? 'text-emerald-500' : 'text-rose-500'" />
              <span class="text-xs font-medium tabular-nums" :class="lastResult.passed ? 'text-emerald-400' : 'text-rose-400'">
                {{ passedCount }} of {{ lastResult.tests.length }} tests passing
              </span>
              <span class="ml-auto text-xs text-neutral-600 tabular-nums">{{ lastResult.duration }}ms</span>
            </div>
            <div class="mb-4 h-1 overflow-hidden rounded-full bg-neutral-800">
              <div
                class="h-full rounded-full transition-all duration-500"
                :class="lastResult.passed ? 'bg-emerald-500' : 'bg-rose-500'"
                :style="{ width: `${lastResult.tests.length ? (passedCount / lastResult.tests.length) * 100 : 0}%` }"
              />
            </div>

            <TransitionGroup name="test-card" tag="div" appear class="space-y-2">
              <div
                v-for="(test, i) in lastResult.tests"
                :key="`${runId}-${i}`"
                class="rounded-lg border p-3"
                :class="test.passed ? 'border-emerald-800/40 bg-emerald-950/20' : 'border-rose-800/40 bg-rose-950/20'"
                :style="{ transitionDelay: `${i * 60}ms` }"
              >
                <div class="flex items-start gap-2">
                  <CheckCircle v-if="test.passed" :size="13" class="mt-0.5 shrink-0 text-emerald-500" />
                  <XCircle v-else :size="13" class="mt-0.5 shrink-0 text-rose-500" />
                  <span class="text-xs text-neutral-300 leading-snug">{{ test.name }}</span>
                </div>
                <template v-if="!test.passed && test.message">
                  <div v-if="parseDiff(test.message)" class="mt-2 overflow-hidden rounded border border-neutral-800 bg-neutral-950 font-mono text-xs">
                    <div class="flex items-baseline gap-3 border-b border-neutral-800/60 bg-emerald-950/20 px-3 py-1.5">
                      <span class="w-3 shrink-0 select-none text-emerald-500">+</span>
                      <span class="w-16 shrink-0 text-neutral-500">expected</span>
                      <span class="break-all text-emerald-300">{{ parseDiff(test.message)?.expected }}</span>
                    </div>
                    <div class="flex items-baseline gap-3 bg-rose-950/20 px-3 py-1.5">
                      <span class="w-3 shrink-0 select-none text-rose-500">−</span>
                      <span class="w-16 shrink-0 text-neutral-500">received</span>
                      <span class="break-all text-rose-300">{{ parseDiff(test.message)?.actual }}</span>
                    </div>
                  </div>
                  <pre v-else class="mt-2 overflow-x-auto whitespace-pre-wrap rounded border border-neutral-800 bg-neutral-950 px-3 py-2 text-xs leading-relaxed text-neutral-400">{{ test.message }}</pre>
                </template>
              </div>
            </TransitionGroup>
          </div>
        </div>
      </div>

      <!-- Editor -->
      <div class="relative flex flex-1 flex-col overflow-hidden">
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
            <div class="flex flex-1 items-center justify-center text-sm text-neutral-600">
              Loading editor…
            </div>
          </template>
        </ClientOnly>

        <!-- Run bar -->
        <div class="flex h-12 items-center gap-3 border-t border-neutral-800 bg-neutral-900 px-4">
          <button
            class="flex items-center gap-2 rounded-md bg-emerald-600 px-4 py-1.5 text-sm font-semibold text-white transition-colors hover:bg-emerald-500 disabled:opacity-50"
            :disabled="running"
            :title="`Run tests (${runShortcutLabel})`"
            @click="runTests"
          >
            <Play v-if="!running" :size="13" />
            <Loader2 v-else :size="13" class="animate-spin" />
            {{ running ? 'Running…' : 'Run Tests' }}
          </button>
          <kbd class="hidden rounded border border-neutral-700 px-1.5 py-0.5 text-[10px] text-neutral-500 sm:inline">{{ runShortcutLabel }}</kbd>

          <button
            class="rounded-md border border-neutral-700 px-3 py-1.5 text-xs text-neutral-400 transition-colors hover:border-neutral-500 hover:text-neutral-200"
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
    </div>
  </div>
</template>

<script setup lang="ts">
import { ArrowLeft, CheckCircle, XCircle, Play, Loader2, SquareCode, PartyPopper, History, ChevronDown, RotateCcw } from 'lucide-vue-next'
import { VueMonacoEditor } from '@guolao/vue-monaco-editor'
import type { Challenge } from '~/types/models'

const props = defineProps<{
  challenge: Challenge
}>()

const emit = defineEmits<{
  back: []
  // `progress` is the gamification delta from the run response (null on
  // repeat solves) — standalone play uses it for the celebration overlay.
  completed: [challenge: Challenge, progress: TestResult['progress']]
}>()

const { post, get } = useApi()

type WorkspaceTab = 'Instructions' | 'Hints' | 'Results' | 'Iterations'

const code = ref(props.challenge.boilerplate_code)
const activeTab = ref<WorkspaceTab>('Instructions')
const running = ref(false)
const runId = ref(0)

interface TestResult {
  passed: boolean
  failedCount: number
  duration: number
  tests: Array<{ name: string; passed: boolean; message?: string }>
  progress?: {
    xp_awarded: number
    xp_points: number
    current_streak: number
    new_badges: Array<{ key: string; name: string; description: string; icon: string }>
  } | null
}

const lastResult = ref<TestResult | null>(null)

const parsed = computed(() => parseChallengeDescription(props.challenge.description))
const hints = computed(() => parsed.value.hints)

const visibleTabs = computed<WorkspaceTab[]>(() =>
  hints.value.length
    ? ['Instructions', 'Hints', 'Results', 'Iterations']
    : ['Instructions', 'Results', 'Iterations'],
)

// ── Iterations (submission history) ─────────────────────────
interface Iteration {
  id: number
  code: string
  passed: boolean
  failed_count: number
  duration_ms: number
  created_at: string
}

const iterations = ref<Iteration[] | null>(null)
const loadingIterations = ref(false)
const expandedIteration = ref<number | null>(null)

async function fetchIterations() {
  loadingIterations.value = true
  try {
    const res = await get(`/challenges/${props.challenge.slug}/submissions`) as { data: Iteration[] }
    iterations.value = res.data
  } catch {
    iterations.value = iterations.value ?? []
  } finally {
    loadingIterations.value = false
  }
}

// Lazy-load on first open; runs refresh it only if already loaded.
watch(activeTab, (tab) => {
  if (tab === 'Iterations' && iterations.value === null && !loadingIterations.value) fetchIterations()
})

function restoreIteration(it: Iteration) {
  code.value = it.code
  expandedIteration.value = null
}

function timeAgo(iso: string): string {
  const s = Math.max(1, Math.floor((Date.now() - new Date(iso).getTime()) / 1000))
  if (s < 60) return `${s}s ago`
  if (s < 3600) return `${Math.floor(s / 60)}m ago`
  if (s < 86400) return `${Math.floor(s / 3600)}h ago`
  return `${Math.floor(s / 86400)}d ago`
}

const passedCount = computed(() => lastResult.value?.tests.filter(t => t.passed).length ?? 0)

const isMac = import.meta.client && /Mac|iP(hone|ad|od)/.test(navigator.platform)
const runShortcutLabel = isMac ? '⌘↵' : 'Ctrl+↵'

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
  expert: 'bg-violet-500/10 text-violet-400',
}[props.challenge.difficulty]))

async function runTests() {
  if (running.value) return
  running.value = true
  activeTab.value = 'Results'
  lastResult.value = null

  try {
    const result = await post<TestResult>(`/challenges/${props.challenge.slug}/run`, {
      code: code.value,
    })
    runId.value++
    lastResult.value = result
    // Keep the iterations list live if the user has already opened it.
    if (iterations.value !== null) fetchIterations()
    if (result.passed) emit('completed', props.challenge, result.progress ?? null)
  } catch {
    runId.value++
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

function onKeydown(e: KeyboardEvent) {
  if ((e.metaKey || e.ctrlKey) && e.key === 'Enter') {
    e.preventDefault()
    runTests()
  }
}

onMounted(() => window.addEventListener('keydown', onKeydown))
onUnmounted(() => window.removeEventListener('keydown', onKeydown))

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

<style scoped>
.test-card-enter-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}
.test-card-enter-from {
  opacity: 0;
  transform: translateY(8px);
}
</style>
