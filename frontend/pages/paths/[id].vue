<template>
  <NuxtLayout name="admin">

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-24">
      <UIcon name="i-heroicons-arrow-path" class="animate-spin text-3xl text-indigo-500" />
    </div>

    <template v-else-if="currentPath">

      <!-- Header -->
      <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
        <div class="flex items-center gap-3">
          <UButton icon="i-heroicons-arrow-left" size="sm" color="gray" variant="ghost" @click="router.back()" />
          <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ currentPath.name }}</h1>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
              {{ steps.length }} step{{ steps.length !== 1 ? 's' : '' }} ·
              by {{ currentPath.consultant?.fullname || 'CODECV' }}
            </p>
          </div>
        </div>
        <UButton icon="i-heroicons-plus" size="sm" color="indigo" @click="openAddStep">
          Add Step
        </UButton>
      </div>

      <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <!-- ── Timeline ─────────────────────────────── -->
        <div class="lg:col-span-2">

          <!-- Empty -->
          <div v-if="!steps.length"
            class="flex flex-col items-center rounded-xl border border-dashed border-gray-200
                   bg-white py-16 text-center dark:border-gray-700 dark:bg-gray-800">
            <UIcon name="i-heroicons-map" class="mb-3 h-10 w-10 text-gray-300 dark:text-gray-600" />
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No steps yet</p>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Add the first step to build this roadmap.</p>
            <UButton class="mt-4" size="sm" icon="i-heroicons-plus" @click="openAddStep">Add Step</UButton>
          </div>

          <!-- Steps timeline -->
          <div v-else class="flex flex-col">
            <div v-for="(step, i) in steps" :key="step.id" class="flex gap-4">

              <!-- Line + node -->
              <div class="flex flex-col items-center">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border-2 font-bold text-sm"
                  :class="nodeClass(step)">
                  {{ i + 1 }}
                </div>
                <div v-if="i < steps.length - 1" class="my-1 w-0.5 flex-1 bg-gray-200 dark:bg-gray-700" />
              </div>

              <!-- Card -->
              <div class="mb-4 flex-1 rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800"
                :class="{ 'ring-2 ring-indigo-200 dark:ring-indigo-800': editingStep?.id === step.id }">
                <div class="flex items-start justify-between gap-3 p-4">
                  <div class="min-w-0 flex-1">
                    <p class="font-semibold text-gray-900 dark:text-white">{{ step.title }}</p>
                    <p v-if="step.description" class="mt-1 text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                      {{ step.description }}
                    </p>
                    <!-- Linked course -->
                    <div v-if="step.course" class="mt-2 flex items-center gap-2">
                      <UBadge color="indigo" variant="subtle" size="xs" icon="i-heroicons-book-open">
                        {{ step.course.name }}
                      </UBadge>
                    </div>
                    <!-- Resources -->
                    <div v-if="step.resources?.length" class="mt-2 flex flex-wrap gap-2">
                      <a v-for="r in step.resources" :key="r.url"
                        :href="r.url" target="_blank"
                        class="flex items-center gap-1 text-xs text-indigo-600 hover:underline dark:text-indigo-400">
                        <UIcon name="i-heroicons-link" class="h-3 w-3" />
                        {{ r.label }}
                      </a>
                    </div>
                  </div>

                  <!-- Admin actions -->
                  <div class="flex shrink-0 gap-1">
                    <!-- Reorder -->
                    <UButton icon="i-heroicons-chevron-up" size="xs" color="gray" variant="ghost"
                      :disabled="i === 0" @click="moveStep(i, -1)" />
                    <UButton icon="i-heroicons-chevron-down" size="xs" color="gray" variant="ghost"
                      :disabled="i === steps.length - 1" @click="moveStep(i, 1)" />
                    <UButton icon="i-heroicons-pencil-square" size="xs" color="gray" variant="ghost"
                      @click="openEditStep(step)" />
                    <UButton icon="i-heroicons-trash" size="xs" color="red" variant="ghost"
                      @click="handleDeleteStep(step)" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ── Sidebar info ──────────────────────────── -->
        <div class="flex flex-col gap-5">

          <!-- Path info -->
          <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
              <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Path Details</h2>
            </div>
            <div class="p-5">
              <p v-if="currentPath.description" class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                {{ currentPath.description }}
              </p>
              <p v-else class="text-sm text-gray-400 dark:text-gray-500 italic">No description.</p>
              <dl class="mt-4 flex flex-col gap-2">
                <div class="flex items-center justify-between">
                  <dt class="text-xs text-gray-500 dark:text-gray-400">Steps</dt>
                  <dd class="text-xs font-semibold text-gray-900 dark:text-white">{{ steps.length }}</dd>
                </div>
                <div class="flex items-center justify-between">
                  <dt class="text-xs text-gray-500 dark:text-gray-400">Created</dt>
                  <dd class="text-xs font-semibold text-gray-900 dark:text-white">
                    {{ new Date(currentPath.created_at).toLocaleDateString('en-IE') }}
                  </dd>
                </div>
              </dl>
            </div>
          </div>

          <!-- Tip -->
          <div class="rounded-xl bg-indigo-50 p-4 dark:bg-indigo-950/30">
            <p class="text-xs font-semibold text-indigo-700 dark:text-indigo-400">Tip</p>
            <p class="mt-1 text-xs text-indigo-600 dark:text-indigo-500 leading-relaxed">
              Use the ↑ ↓ arrows to reorder steps. Link a course to each step so clients can access content directly from their roadmap.
            </p>
          </div>
        </div>
      </div>

    </template>

    <!-- ── Step modal ──────────────────────────────────── -->
    <UModal v-model="showModal" :ui="{ width: 'sm:max-w-xl' }">
      <div class="p-6">
        <h3 class="mb-5 text-base font-bold text-gray-900 dark:text-white">
          {{ editingStep ? 'Edit Step' : 'Add Step' }}
        </h3>

        <!-- API error -->
        <UAlert
          v-if="stepError"
          color="red"
          variant="subtle"
          class="mb-2"
          title="Error"
          :description="stepError"
        />

        <div class="flex flex-col gap-4">
          <!-- Title -->
          <div>
            <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">Title *</label>
            <UInput v-model="form.title" placeholder="e.g. Learn Git & GitHub" />
          </div>

          <!-- Description -->
          <div>
            <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">Description</label>
            <UTextarea v-model="form.description" :rows="3"
              placeholder="What will the student learn or do in this step?" />
          </div>

          <!-- Linked course -->
          <div>
            <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">Linked Course (optional)</label>
            <USelect v-model="form.course_id"
              :options="[{ label: 'None', value: null }, ...courseOptions]"
              value-attribute="value"
              option-attribute="label"
              placeholder="Select a course…" />
          </div>

          <!-- Resources -->
          <div>
            <div class="mb-1.5 flex items-center justify-between">
              <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">External Resources</label>
              <UButton size="xs" variant="ghost" icon="i-heroicons-plus" @click="addResource">Add</UButton>
            </div>
            <div v-for="(r, ri) in form.resources" :key="ri" class="mb-2 flex gap-2">
              <UInput v-model="r.label" placeholder="Label" class="w-32 shrink-0" />
              <UInput v-model="r.url" placeholder="https://…" class="flex-1" />
              <UButton icon="i-heroicons-x-mark" size="xs" color="red" variant="ghost"
                @click="form.resources.splice(ri, 1)" />
            </div>
          </div>
        </div>

        <div class="mt-6 flex justify-end gap-2">
          <UButton color="gray" variant="outline" @click="showModal = false; stepError = null">Cancel</UButton>
          <UButton color="indigo" :loading="saving" @click="saveStep">
            {{ editingStep ? 'Save Changes' : 'Add Step' }}
          </UButton>
        </div>
      </div>
    </UModal>

  </NuxtLayout>
</template>

<script setup lang="ts">
import type { PathStep } from '~/composables/usePaths'

definePageMeta({ layout: false, middleware: 'auth' })

const route  = useRoute()
const router = useRouter()
const toast  = useToast()

const { fetchPath, fetchSteps, createStep, updateStep, deleteStep, reorderSteps } = usePaths()
const { courses, fetchCourses } = useCourses()

const currentPath = ref<any>(null)
const steps       = ref<PathStep[]>([])
const loading     = ref(true)

const showModal   = ref(false)
const saving      = ref(false)
const stepError   = ref<string | null>(null)
const editingStep = ref<PathStep | null>(null)

const emptyForm = () => ({ title: '', description: '', course_id: null as number | null, resources: [] as { label: string; url: string }[] })
const form = reactive(emptyForm())

const pathId = computed(() => Number(route.params.id))

const courseOptions = computed(() =>
  (courses.value ?? []).map((c: any) => ({ label: c.name, value: c.id }))
)

onMounted(async () => {
  try {
    const [p, s] = await Promise.all([
      fetchPath(pathId.value),
      fetchSteps(pathId.value),
      fetchCourses({ per_page: 100 }),
    ])
    currentPath.value = p
    steps.value = s
  } finally {
    loading.value = false
  }
})

useHead(() => ({ title: currentPath.value ? `${currentPath.value.name} — Paths` : 'Path — CODECV' }))

function nodeClass(step: PathStep) {
  return 'border-gray-300 bg-white text-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400'
}

function openAddStep() {
  editingStep.value = null
  stepError.value = null
  Object.assign(form, emptyForm())
  showModal.value = true
}

function openEditStep(step: PathStep) {
  editingStep.value = step
  stepError.value = null
  Object.assign(form, {
    title: step.title,
    description: step.description ?? '',
    course_id: step.course?.id ?? null,
    resources: step.resources ? JSON.parse(JSON.stringify(step.resources)) : [],
  })
  showModal.value = true
}

function addResource() {
  form.resources.push({ label: '', url: '' })
}

async function saveStep() {
  if (!form.title.trim()) return
  saving.value = true
  try {
    const payload = {
      title: form.title,
      description: form.description || null,
      course_id: form.course_id || null,
      resources: form.resources.filter(r => r.label && r.url),
    }
    if (editingStep.value) {
      const res = await updateStep(pathId.value, editingStep.value.id, payload)
      const idx = steps.value.findIndex(s => s.id === editingStep.value!.id)
      if (idx !== -1) steps.value[idx] = res.data
      toast.add({ title: 'Step updated', color: 'green' })
    } else {
      const res = await createStep(pathId.value, payload)
      steps.value.push(res.data)
      toast.add({ title: 'Step added', color: 'green' })
    }
    showModal.value = false
  } catch (err: any) {
    const data = err?.data ?? err?.response?._data
    if (data?.errors) {
      const messages = Object.values(data.errors as Record<string, string[]>).flat()
      stepError.value = messages[0] ?? data.message ?? 'Validation failed.'
    } else {
      stepError.value = data?.message ?? 'Could not save step. Please try again.'
    }
  } finally {
    saving.value = false
  }
}

async function handleDeleteStep(step: PathStep) {
  if (!confirm(`Delete "${step.title}"?`)) return
  try {
    await deleteStep(pathId.value, step.id)
    steps.value = steps.value.filter(s => s.id !== step.id)
    toast.add({ title: 'Step deleted', color: 'green' })
  } catch {
    toast.add({ title: 'Error', description: 'Could not delete step.', color: 'red' })
  }
}

async function moveStep(index: number, dir: -1 | 1) {
  const newIndex = index + dir
  if (newIndex < 0 || newIndex >= steps.value.length) return
  const arr = [...steps.value]
  ;[arr[index], arr[newIndex]] = [arr[newIndex], arr[index]]
  steps.value = arr
  try {
    await reorderSteps(pathId.value, arr.map(s => s.id))
  } catch {
    toast.add({ title: 'Reorder failed', color: 'red' })
  }
}
</script>
