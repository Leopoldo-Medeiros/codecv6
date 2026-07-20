<template>
  <NuxtLayout name="marketing">
    <div class="try">
      <div class="mkt-container">

        <div v-if="pending" class="try__state">Loading challenge…</div>

        <div v-else-if="!challenge" class="try__state">
          <h1 class="try__missing">Challenge not available</h1>
          <p>This challenge isn't part of the free preview.</p>
          <NuxtLink to="/#try" class="mkt-btn mkt-btn--primary">See free challenges</NuxtLink>
        </div>

        <template v-else>
          <NuxtLink to="/#try" class="try__back">← All free challenges</NuxtLink>

          <div class="try__grid">
            <!-- Prompt -->
            <section class="try__prompt">
              <div class="try__prompt-head">
                <span class="try__difficulty" :data-level="challenge.difficulty">{{ challenge.difficulty }}</span>
                <span class="try__free">Free preview · no signup</span>
              </div>
              <h1 class="try__title">{{ challenge.title }}</h1>
              <div class="try__desc" v-html="renderedDescription" />

              <!-- Progressive hints — revealed one at a time, no inline spoilers -->
              <div v-if="hints.length" class="try__hints">
                <p class="try__hints-title">💡 Hints</p>
                <TransitionGroup name="try-fade" tag="div" class="try__hints-list">
                  <div v-for="(hint, i) in hints.slice(0, revealedHints)" :key="i" class="try__hint">
                    <span class="try__hint-num">{{ i + 1 }}</span>
                    <span class="try__hint-body" v-html="renderMarkdown(hint)" />
                  </div>
                </TransitionGroup>
                <button
                  v-if="revealedHints < hints.length"
                  class="try__hint-reveal"
                  @click="revealedHints++"
                >
                  Reveal hint {{ revealedHints + 1 }} of {{ hints.length }}
                </button>
              </div>
            </section>

            <!-- Editor -->
            <section class="try__editor-wrap">
              <div class="try__editor-bar">
                <span class="try__editor-label">solution.php</span>
                <div class="try__editor-actions">
                  <button class="try__btn-ghost" @click="resetCode">Reset</button>
                  <button class="try__btn-run" :disabled="running" @click="run">
                    {{ running ? 'Running…' : 'Run tests ▶' }}
                  </button>
                </div>
              </div>

              <ClientOnly>
                <VueMonacoEditor
                  v-model:value="code"
                  language="php"
                  theme="vs-dark"
                  :options="{ minimap: { enabled: false }, fontSize: 14, scrollBeyondLastLine: false, automaticLayout: true }"
                  class="try__editor"
                />
                <template #fallback>
                  <div class="try__editor try__editor--loading">Loading editor…</div>
                </template>
              </ClientOnly>

              <!-- Result -->
              <Transition name="try-fade">
                <div v-if="result" class="try__result" :class="result.passed ? 'try__result--pass' : 'try__result--fail'">
                  <p class="try__result-head">
                    {{ result.passed ? '✓ All tests passed!' : `✘ ${result.failedCount} test(s) failed` }}
                  </p>
                  <ul class="try__tests">
                    <li v-for="(t, i) in result.tests" :key="i" :class="t.passed ? 'is-pass' : 'is-fail'">
                      <span>{{ t.passed ? '✓' : '✘' }}</span>
                      <span>{{ t.name }}</span>
                      <span v-if="!t.passed && t.message" class="try__test-msg">{{ t.message }}</span>
                    </li>
                  </ul>

                  <div v-if="result.passed" class="try__win">
                    <p>Nice — that's exactly the kind of problem you'll practice on CODECV.</p>
                    <NuxtLink to="/register" class="mkt-btn mkt-btn--primary mkt-btn--lg">
                      Create a free account to unlock 40+ more
                    </NuxtLink>
                  </div>
                </div>
              </Transition>

              <p v-if="error" class="try__error">{{ error }}</p>
            </section>
          </div>
        </template>

      </div>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
import { VueMonacoEditor } from '@guolao/vue-monaco-editor'

definePageMeta({ layout: false })

interface TeaserChallenge {
  title: string
  slug: string
  description: string
  difficulty: string
  boilerplate_code: string
}
interface TestResult {
  passed: boolean
  failedCount: number
  tests: { name: string, passed: boolean, message: string | null }[]
}

const route = useRoute()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase as string

const challenge = ref<TeaserChallenge | null>(null)
const pending = ref(true)
const code = ref('')
const running = ref(false)
const result = ref<TestResult | null>(null)
const error = ref('')

const parsed = computed(() =>
  challenge.value
    ? parseChallengeDescription(challenge.value.description)
    : { instructions: '', hints: [] },
)
const renderedDescription = computed(() => renderMarkdown(parsed.value.instructions))
const hints = computed(() => parsed.value.hints)
const revealedHints = ref(0)

useSeoMeta({
  title: () => challenge.value ? `Try: ${challenge.value.title} — CODECV` : 'Try a challenge — CODECV',
  description: 'Solve a real coding challenge in your browser — no signup required.',
})

function resetCode() {
  if (challenge.value) code.value = challenge.value.boilerplate_code
  result.value = null
}

async function run() {
  if (!challenge.value || running.value) return
  running.value = true
  error.value = ''
  result.value = null
  try {
    result.value = await $fetch<TestResult>(
      `/api/public/challenges/${challenge.value.slug}/run`,
      { baseURL: apiBase, method: 'POST', body: { code: code.value } },
    )
  } catch (e: unknown) {
    const err = e as { response?: { status?: number } }
    error.value = err.response?.status === 429
      ? 'You\'ve hit the free preview limit. Create an account to keep practicing.'
      : 'Something went wrong running your code. Please try again.'
  } finally {
    running.value = false
  }
}

onMounted(async () => {
  try {
    const list = await $fetch<{ data: TeaserChallenge[] }>(
      '/api/public/challenges/teaser',
      { baseURL: apiBase },
    )
    challenge.value = list.data.find(c => c.slug === route.params.slug) ?? null
    if (challenge.value) code.value = challenge.value.boilerplate_code
  } catch {
    challenge.value = null
  } finally {
    pending.value = false
  }
})
</script>

<style scoped>
.try {
  padding: clamp(32px, 5vw, 56px) 0 clamp(48px, 7vw, 80px);
  font-family: var(--ff, 'Poppins', sans-serif);
  color: var(--text, #17212B);
  min-height: 70vh;
}
.try__state, .try__missing { text-align: center; }
.try__state { padding: 80px 0; color: var(--muted, #8B95A1); display: flex; flex-direction: column; gap: 16px; align-items: center; }
.try__missing { font-size: 26px; font-weight: 700; color: var(--text, #17212B); }

.try__back {
  display: inline-block;
  margin-bottom: 20px;
  font-size: 14px;
  font-weight: 600;
  color: var(--accent, #059669);
  text-decoration: none;
}
.try__back:hover { text-decoration: underline; }

.try__grid {
  display: grid;
  grid-template-columns: 1fr 1.2fr;
  gap: 28px;
  align-items: start;
}

.try__prompt-head { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
.try__difficulty {
  font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;
  padding: 3px 9px; border-radius: 3px;
  background: var(--accent-light, rgba(5, 150, 105, 0.08)); color: var(--accent, #059669);
}
.try__difficulty[data-level="advanced"], .try__difficulty[data-level="expert"] {
  background: rgba(168, 113, 10, 0.1); color: #A8710A;
}
.try__free { font-size: 12.5px; color: var(--muted, #8B95A1); }
.try__title { font-size: clamp(22px, 3vw, 30px); font-weight: 700; margin: 0 0 16px; line-height: 1.2; }
.try__desc { font-size: 15px; line-height: 1.7; color: var(--text-body, #45505C); }
.try__desc :deep(h2) { font-size: 18px; margin: 20px 0 8px; }
.try__desc :deep(pre) { background: #0B1215; color: #C9D1D9; padding: 14px; border-radius: 6px; overflow-x: auto; font-size: 13px; }
.try__desc :deep(code) { font-family: ui-monospace, Menlo, monospace; }
.try__desc :deep(table) { width: 100%; border-collapse: collapse; margin: 12px 0; font-size: 14px; }
.try__desc :deep(th), .try__desc :deep(td) { border: 1px solid var(--border, #E9EDF2); padding: 6px 10px; text-align: left; }

/* Editor panel */
.try__editor-wrap { border: 1px solid var(--border, #E9EDF2); border-radius: 8px; overflow: hidden; background: #0B1215; }
.try__editor-bar {
  display: flex; align-items: center; justify-content: space-between;
  padding: 10px 14px; background: #1E2428; border-bottom: 1px solid rgba(0,0,0,0.4);
}
.try__editor-label { font-family: ui-monospace, Menlo, monospace; font-size: 12.5px; color: #9BA3AB; }
.try__editor-actions { display: flex; gap: 8px; }
.try__btn-ghost {
  background: none; border: 1px solid rgba(255,255,255,0.18); color: #9BA3AB;
  padding: 5px 12px; border-radius: 4px; font-size: 12.5px; cursor: pointer;
}
.try__btn-ghost:hover { color: #fff; border-color: rgba(255,255,255,0.4); }
.try__btn-run {
  background: #059669; border: none; color: #fff;
  padding: 5px 16px; border-radius: 4px; font-size: 12.5px; font-weight: 600; cursor: pointer;
}
.try__btn-run:hover:not(:disabled) { background: #0BAB79; }
.try__btn-run:disabled { opacity: 0.5; cursor: not-allowed; }
.try__editor { height: 380px; }
.try__editor--loading { display: flex; align-items: center; justify-content: center; color: #8B949E; font-size: 14px; }

.try__result { padding: 16px; background: #101A1E; border-top: 1px solid rgba(255,255,255,0.08); }
.try__result-head { margin: 0 0 10px; font-weight: 700; font-size: 14px; }
.try__result--pass .try__result-head { color: #34D399; }
.try__result--fail .try__result-head { color: #FF7B72; }
.try__tests { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 6px; }
.try__tests li { display: flex; gap: 8px; font-size: 13px; color: #C9D1D9; font-family: ui-monospace, Menlo, monospace; }
.try__tests li.is-pass span:first-child { color: #34D399; }
.try__tests li.is-fail span:first-child { color: #FF7B72; }
.try__test-msg { color: #8B949E; font-style: italic; }
.try__win { margin-top: 16px; padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.08); }
.try__win p { color: #C9D1D9; font-size: 14px; margin: 0 0 12px; }
.try__error { color: #FF7B72; padding: 12px 16px; font-size: 13.5px; }

.try-fade-enter-active { transition: opacity 0.3s ease; }
.try-fade-enter-from { opacity: 0; }

/* Progressive hints */
.try__hints { margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--border, #E9EDF2); }
.try__hints-title { font-size: 14px; font-weight: 700; margin: 0 0 10px; color: var(--text, #17212B); }
.try__hints-list { display: flex; flex-direction: column; gap: 8px; }
.try__hint {
  display: flex; gap: 10px; align-items: flex-start;
  padding: 10px 12px; border: 1px solid #F0E4C8; border-radius: 6px; background: #FDF9EE;
}
.try__hint-num {
  flex-shrink: 0; width: 20px; height: 20px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  background: rgba(168, 113, 10, 0.12); color: #A8710A; font-size: 11px; font-weight: 700;
}
.try__hint-body { font-size: 13.5px; line-height: 1.6; color: var(--text-body, #45505C); min-width: 0; }
.try__hint-body :deep(code) { background: rgba(5, 150, 105, 0.08); color: #047857; padding: 1px 4px; border-radius: 3px; font-size: 12.5px; }
.try__hint-reveal {
  margin-top: 8px; width: 100%; padding: 9px 0;
  background: none; border: 1px dashed var(--border, #D5DCE3); border-radius: 6px;
  color: var(--muted, #8B95A1); font-size: 12.5px; font-weight: 600; cursor: pointer;
  transition: color 0.15s ease, border-color 0.15s ease;
}
.try__hint-reveal:hover { color: #A8710A; border-color: #E3CD96; }

@media (max-width: 860px) {
  .try__grid { grid-template-columns: 1fr; }
}
</style>
