<template>
  <div class="mx-auto max-w-2xl">
    <UCard>
      <div class="space-y-6">
        <div
          v-for="(q, qi) in questions"
          :key="q.id"
          class="border-b border-gray-100 pb-6 last:border-b-0 last:pb-0 dark:border-gray-700/60"
        >
          <p class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">
            <span class="text-gray-400 dark:text-gray-500">{{ qi + 1 }}.</span> {{ q.question }}
          </p>

          <div class="flex flex-col gap-2">
            <button
              v-for="(opt, oi) in q.options"
              :key="oi"
              type="button"
              :disabled="submitted"
              class="flex items-center gap-3 rounded-lg border px-3.5 py-2.5 text-left text-sm transition-colors"
              :class="optionClass(q.id, oi)"
              @click="select(q.id, oi)"
            >
              <span
                class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border text-[11px] font-bold"
                :class="answers[q.id] === oi
                  ? 'border-emerald-500 bg-emerald-500 text-white'
                  : 'border-gray-300 text-transparent dark:border-gray-600'"
              >
                {{ String.fromCharCode(65 + oi) }}
              </span>
              <span class="flex-1">{{ opt }}</span>
              <UIcon
                v-if="submitted && resultFor(q.id)"
                :name="oi === resultFor(q.id)!.correct_index ? 'i-heroicons-check-circle' : (answers[q.id] === oi ? 'i-heroicons-x-circle' : '')"
                class="h-4 w-4 shrink-0"
                :class="oi === resultFor(q.id)!.correct_index ? 'text-emerald-500' : 'text-red-500'"
              />
            </button>
          </div>

          <!-- Explanation after grading -->
          <p
            v-if="submitted && resultFor(q.id)?.explanation"
            class="mt-2.5 rounded-lg bg-gray-50 px-3 py-2 text-xs leading-relaxed text-gray-600 dark:bg-gray-800/60 dark:text-gray-400"
          >
            {{ resultFor(q.id)!.explanation }}
          </p>
        </div>
      </div>

      <template #footer>
        <!-- Result banner -->
        <div v-if="submitted" class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <p class="text-sm font-bold" :class="allCorrect ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-900 dark:text-white'">
              {{ score }} / {{ total }} correct
              <span v-if="allCorrect"> — perfect! 🎉</span>
            </p>
            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
              {{ allCorrect ? 'Mark this step done to earn XP.' : 'Review the explanations and try again.' }}
            </p>
          </div>
          <UButton v-if="!allCorrect" color="gray" variant="soft" size="sm" @click="reset">Try again</UButton>
        </div>

        <!-- Submit -->
        <UButton
          v-else
          color="emerald"
          :loading="loading"
          :disabled="!allAnswered"
          icon="i-heroicons-check"
          @click="submit"
        >
          {{ allAnswered ? 'Submit answers' : `Answer all ${total} questions` }}
        </UButton>
        <p v-if="error" class="mt-2 text-xs text-red-500">{{ error }}</p>
      </template>
    </UCard>
  </div>
</template>

<script setup lang="ts">
interface QuizQuestion { id: number; question: string; options: string[] }
interface QuizResult { id: number; correct: boolean; correct_index: number; explanation: string | null }

const props = defineProps<{
  stepId: number
  questions: QuizQuestion[]
}>()

const emit = defineEmits<{ passed: [] }>()

const answers = reactive<Record<number, number>>({})
const results = ref<QuizResult[]>([])
const submitted = ref(false)
const loading = ref(false)
const error = ref('')
const score = ref(0)

const total = computed(() => props.questions.length)
const allAnswered = computed(() => props.questions.every(q => answers[q.id] !== undefined))
const allCorrect = computed(() => submitted.value && score.value === total.value)

function select(questionId: number, optionIndex: number) {
  if (submitted.value) return
  answers[questionId] = optionIndex
}

function resultFor(questionId: number): QuizResult | undefined {
  return results.value.find(r => r.id === questionId)
}

function optionClass(questionId: number, optionIndex: number): string {
  if (!submitted.value) {
    return answers[questionId] === optionIndex
      ? 'border-emerald-400 bg-emerald-50 dark:border-emerald-600 dark:bg-emerald-950/30'
      : 'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600'
  }
  const result = resultFor(questionId)
  if (result && optionIndex === result.correct_index) {
    return 'border-emerald-400 bg-emerald-50 dark:border-emerald-600 dark:bg-emerald-950/30'
  }
  if (answers[questionId] === optionIndex) {
    return 'border-red-300 bg-red-50 dark:border-red-800 dark:bg-red-950/30'
  }
  return 'border-gray-200 opacity-60 dark:border-gray-700'
}

async function submit() {
  if (!allAnswered.value || loading.value) return
  loading.value = true
  error.value = ''
  try {
    const res = await useApi().post<{ score: number; total: number; passed: boolean; results: QuizResult[] }>(
      `/path-steps/${props.stepId}/quiz`,
      { answers },
    )
    results.value = res.results
    score.value = res.score
    submitted.value = true
    if (res.passed) emit('passed')
  } catch (e: unknown) {
    error.value = (e as Error).message || 'Could not grade your answers. Please try again.'
  } finally {
    loading.value = false
  }
}

function reset() {
  submitted.value = false
  results.value = []
  score.value = 0
  for (const key of Object.keys(answers)) delete answers[Number(key)]
}
</script>
