<template>
  <NuxtLayout name="admin">

    <!-- Back -->
    <div class="mb-5">
      <NuxtLink to="/my-clients"
        class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
        <UIcon name="i-heroicons-arrow-left" class="h-4 w-4" />
        My Clients
      </NuxtLink>
    </div>

    <div v-if="loading" class="flex justify-center py-20">
      <UIcon name="i-heroicons-arrow-path" class="animate-spin text-3xl text-emerald-500" />
    </div>

    <template v-else-if="detail">

      <!-- Client header -->
      <div class="mb-6 flex flex-wrap items-start gap-5 rounded-xl border border-gray-200 bg-white p-6
                  dark:border-gray-700 dark:bg-gray-800">
        <UAvatar
          :src="detail.user.profile?.profile_image_url ?? undefined"
          :alt="detail.user.fullname"
          size="xl"
          :ui="{ background: 'bg-emerald-100 dark:bg-emerald-900' }"
        />
        <div class="min-w-0 flex-1">
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ detail.user.fullname }}</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400">{{ detail.user.email }}</p>

          <div v-if="detail.user.profile" class="mt-3 flex flex-wrap gap-2 text-xs text-gray-600 dark:text-gray-300">
            <span v-if="detail.user.profile.profession" class="flex items-center gap-1">
              <UIcon name="i-heroicons-briefcase" class="h-3.5 w-3.5" />
              {{ detail.user.profile.profession }}
            </span>
            <span v-if="detail.user.profile.level" class="flex items-center gap-1">
              <UIcon name="i-heroicons-signal" class="h-3.5 w-3.5" />
              {{ detail.user.profile.level }}
            </span>
            <span v-if="detail.user.profile.availability_hours" class="flex items-center gap-1">
              <UIcon name="i-heroicons-clock" class="h-3.5 w-3.5" />
              {{ detail.user.profile.availability_hours }}h/week
            </span>
            <span v-if="detail.user.profile.timeline" class="flex items-center gap-1">
              <UIcon name="i-heroicons-calendar" class="h-3.5 w-3.5" />
              {{ detail.user.profile.timeline }}
            </span>
          </div>

          <p v-if="detail.user.profile?.goal" class="mt-2 max-w-lg text-sm text-gray-500 dark:text-gray-400 italic">
            "{{ detail.user.profile.goal }}"
          </p>
        </div>

        <!-- Overall progress pill -->
        <div class="flex shrink-0 flex-col items-end gap-1.5">
          <span class="text-2xl font-black" :class="progressColor(overallPct)">{{ overallPct }}%</span>
          <UProgress :value="overallPct" size="sm" color="emerald" class="w-32" />
          <span class="text-xs text-gray-400">{{ totalDone }} / {{ totalSteps }} steps done</span>
        </div>
      </div>

      <!-- Assign path button -->
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Training Paths</h2>
        <UButton
          color="emerald"
          variant="solid"
          size="sm"
          icon="i-heroicons-plus"
          @click="showAssignModal = true"
        >
          Assign Path
        </UButton>
      </div>

      <!-- Empty paths -->
      <div v-if="!detail.paths.length"
        class="flex flex-col items-center rounded-xl border border-dashed border-gray-200 py-14 text-center
               dark:border-gray-700">
        <UIcon name="i-heroicons-map" class="mb-3 h-10 w-10 text-gray-300 dark:text-gray-600" />
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No paths assigned yet.</p>
        <UButton class="mt-4" color="emerald" variant="soft" size="sm" @click="showAssignModal = true">
          Assign the first path
        </UButton>
      </div>

      <!-- Path cards -->
      <div v-else class="flex flex-col gap-5">
        <div v-for="clientPath in detail.paths" :key="clientPath.id"
          class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">

          <!-- Path header -->
          <div class="flex flex-wrap items-center gap-3 border-b border-gray-100 p-4 dark:border-gray-700">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-950">
              <UIcon name="i-heroicons-map" class="h-5 w-5 text-emerald-500" />
            </div>
            <div class="min-w-0 flex-1">
              <p class="font-semibold text-gray-900 dark:text-white">{{ clientPath.name }}</p>
              <p v-if="clientPath.description" class="text-xs text-gray-500 dark:text-gray-400">{{ clientPath.description }}</p>
            </div>
            <div class="flex shrink-0 items-center gap-3">
              <div class="text-right">
                <p class="text-sm font-bold" :class="progressColor(pathPct(clientPath))">
                  {{ pathPct(clientPath) }}%
                </p>
                <p class="text-xs text-gray-400">{{ clientPath.done_count }}/{{ clientPath.total_count }}</p>
              </div>
              <UProgress :value="pathPct(clientPath)" size="sm" color="emerald" class="w-24" />
              <UButton
                color="red"
                variant="ghost"
                size="xs"
                icon="i-heroicons-trash"
                :loading="removing.has(clientPath.id)"
                @click="confirmRemove(clientPath)"
              />
            </div>
          </div>

          <!-- Steps list -->
          <div class="divide-y divide-gray-50 dark:divide-gray-700/50">
            <div v-for="step in clientPath.steps" :key="step.id"
              class="flex items-center gap-3 px-4 py-2.5">
              <!-- Status dot -->
              <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold"
                :class="stepDotClass(step.user_status)">
                <UIcon v-if="step.user_status === 'done'" name="i-heroicons-check" class="h-3.5 w-3.5" />
                <span v-else>{{ step.order }}</span>
              </div>
              <p class="flex-1 text-sm" :class="step.user_status === 'done'
                ? 'text-gray-400 line-through dark:text-gray-500'
                : 'text-gray-700 dark:text-gray-200'">
                {{ step.title }}
              </p>
              <UBadge :color="statusColor(step.user_status)" variant="subtle" size="xs">
                {{ statusLabel(step.user_status) }}
              </UBadge>
              <UBadge v-if="step.type && step.type !== 'reading'" color="blue" variant="soft" size="xs">
                {{ step.type }}
              </UBadge>
            </div>
          </div>
        </div>
      </div>

    </template>

    <!-- Assign Path Modal -->
    <UModal v-model="showAssignModal">
      <UCard :ui="{ ring: '', divide: 'divide-y divide-gray-100 dark:divide-gray-700' }">
        <template #header>
          <p class="text-base font-semibold text-gray-900 dark:text-white">Assign a Learning Path</p>
        </template>

        <div class="space-y-3 py-2">
          <UFormGroup label="Search paths">
            <UInput v-model="pathSearch" placeholder="Type to filter…" icon="i-heroicons-magnifying-glass" />
          </UFormGroup>

          <div v-if="loadingPaths" class="flex justify-center py-6">
            <UIcon name="i-heroicons-arrow-path" class="animate-spin text-emerald-500" />
          </div>
          <div v-else-if="!availablePaths.length" class="py-4 text-center text-sm text-gray-400">
            No paths found.
          </div>
          <div v-else class="max-h-64 overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <button
              v-for="p in availablePaths"
              :key="p.id"
              class="flex w-full items-center gap-3 px-4 py-3 text-left transition-colors hover:bg-emerald-50 dark:hover:bg-emerald-950/30"
              :class="selectedPathId === p.id ? 'bg-emerald-50 dark:bg-emerald-950/40' : ''"
              @click="selectedPathId = p.id"
            >
              <UIcon
                :name="selectedPathId === p.id ? 'i-heroicons-check-circle' : 'i-heroicons-circle-stack'"
                class="h-5 w-5 shrink-0"
                :class="selectedPathId === p.id ? 'text-emerald-500' : 'text-gray-400'"
              />
              <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium text-gray-900 dark:text-white">{{ p.name }}</p>
                <p v-if="p.description" class="truncate text-xs text-gray-500 dark:text-gray-400">{{ p.description }}</p>
              </div>
            </button>
          </div>
        </div>

        <template #footer>
          <div class="flex justify-end gap-2">
            <UButton color="gray" variant="ghost" @click="showAssignModal = false">Cancel</UButton>
            <UButton
              color="emerald"
              :disabled="!selectedPathId"
              :loading="assigning"
              @click="doAssignPath"
            >
              Assign Path
            </UButton>
          </div>
        </template>
      </UCard>
    </UModal>

    <!-- Remove confirm modal -->
    <UModal v-model="showRemoveModal">
      <UCard :ui="{ ring: '' }">
        <template #header>
          <p class="text-base font-semibold text-gray-900 dark:text-white">Remove Path</p>
        </template>
        <p class="text-sm text-gray-600 dark:text-gray-300">
          Remove <strong>{{ pathToRemove?.name }}</strong> from {{ detail?.user.fullname }}'s training plan?
          Their step progress will not be deleted.
        </p>
        <template #footer>
          <div class="flex justify-end gap-2">
            <UButton color="gray" variant="ghost" @click="showRemoveModal = false">Cancel</UButton>
            <UButton color="red" :loading="removing.has(pathToRemove?.id ?? 0)" @click="doRemovePath">
              Remove
            </UButton>
          </div>
        </template>
      </UCard>
    </UModal>

  </NuxtLayout>
</template>

<script setup lang="ts">
import type { ClientDetail, ClientPath } from '~/composables/useMyClients'
import type { Path } from '~/composables/usePaths'

definePageMeta({ layout: false, middleware: 'auth' })

const route = useRoute()
const toast = useToast()
const clientId = Number(route.params.id)

const { fetchClientDetail, assignPath, removePath } = useMyClients()
const { fetchPaths } = usePaths()

useHead({ title: 'Client Detail — CODECV' })

const detail      = ref<ClientDetail | null>(null)
const loading     = ref(true)
const assigning   = ref(false)
const removing    = ref(new Set<number>())

const showAssignModal = ref(false)
const showRemoveModal = ref(false)
const pathToRemove    = ref<ClientPath | null>(null)
const selectedPathId  = ref<number | null>(null)
const pathSearch      = ref('')
const allPaths        = ref<Path[]>([])
const loadingPaths    = ref(false)

onMounted(async () => {
  detail.value  = await fetchClientDetail(clientId)
  loading.value = false
})

watch(showAssignModal, async (open) => {
  if (!open) { selectedPathId.value = null; pathSearch.value = ''; return }
  loadingPaths.value = true
  try {
    const res = await fetchPaths({ per_page: 100 })
    allPaths.value = res?.data ?? []
  } finally {
    loadingPaths.value = false
  }
})

const assignedIds   = computed(() => new Set(detail.value?.paths.map(p => p.id) ?? []))
const availablePaths = computed(() => {
  const q = pathSearch.value.toLowerCase()
  return allPaths.value.filter(p =>
    !assignedIds.value.has(p.id) &&
    (!q || p.name.toLowerCase().includes(q))
  )
})

const totalSteps = computed(() => detail.value?.paths.reduce((a, p) => a + p.total_count, 0) ?? 0)
const totalDone  = computed(() => detail.value?.paths.reduce((a, p) => a + p.done_count, 0) ?? 0)
const overallPct = computed(() => totalSteps.value > 0 ? Math.round(totalDone.value / totalSteps.value * 100) : 0)

function pathPct(p: ClientPath) {
  return p.total_count > 0 ? Math.round(p.done_count / p.total_count * 100) : 0
}

async function doAssignPath() {
  if (!selectedPathId.value) return
  assigning.value = true
  try {
    await assignPath(clientId, selectedPathId.value)
    detail.value = await fetchClientDetail(clientId)
    showAssignModal.value = false
    toast.add({ title: 'Path assigned', color: 'green' })
  } catch {
    toast.add({ title: 'Could not assign path', color: 'red' })
  } finally {
    assigning.value = false
  }
}

function confirmRemove(p: ClientPath) {
  pathToRemove.value  = p
  showRemoveModal.value = true
}

async function doRemovePath() {
  if (!pathToRemove.value) return
  const id = pathToRemove.value.id
  removing.value.add(id)
  try {
    await removePath(clientId, id)
    detail.value = await fetchClientDetail(clientId)
    showRemoveModal.value = false
    toast.add({ title: 'Path removed', color: 'green' })
  } catch {
    toast.add({ title: 'Could not remove path', color: 'red' })
  } finally {
    removing.value.delete(id)
    pathToRemove.value = null
  }
}

function progressColor(pct: number) {
  if (pct >= 75) return 'text-emerald-600 dark:text-emerald-400'
  if (pct >= 40) return 'text-blue-600 dark:text-blue-400'
  return 'text-gray-500 dark:text-gray-400'
}

function stepDotClass(status: string) {
  if (status === 'done')        return 'bg-emerald-500 text-white'
  if (status === 'in_progress') return 'bg-blue-500 text-white'
  return 'border-2 border-gray-300 bg-white text-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400'
}

function statusColor(status: string) {
  if (status === 'done')        return 'green'
  if (status === 'in_progress') return 'blue'
  return 'gray'
}

function statusLabel(status: string) {
  if (status === 'done')        return 'Done'
  if (status === 'in_progress') return 'In Progress'
  return 'Not Started'
}
</script>
