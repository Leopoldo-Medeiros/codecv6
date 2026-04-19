<template>
  <NuxtLayout name="admin">

    <div v-if="loading" class="flex justify-center py-20">
      <UIcon name="i-heroicons-arrow-path" class="animate-spin text-3xl text-indigo-500" />
    </div>

    <template v-else-if="plan">

      <!-- Header -->
      <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
        <div class="flex items-center gap-3">
          <UButton icon="i-heroicons-arrow-left" size="sm" color="gray" variant="ghost" @click="navigateTo('/plans')" />
          <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ plan.name }}</h1>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
              {{ plan.paths?.length ?? 0 }} paths · {{ plan.clients?.length ?? 0 }} clients
            </p>
          </div>
        </div>
        <div class="flex gap-2">
          <UButton icon="i-heroicons-pencil-square" size="sm" color="gray" variant="outline"
            @click="editMode = !editMode">
            {{ editMode ? 'Cancel' : 'Edit' }}
          </UButton>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <!-- Left: edit form or details -->
        <div class="lg:col-span-2 flex flex-col gap-5">

          <!-- Edit name/description -->
          <div v-if="editMode" class="rounded-xl border border-indigo-200 bg-white p-6 dark:border-indigo-800 dark:bg-gray-800">
            <h2 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">Edit Details</h2>
            <div class="flex flex-col gap-4">
              <UInput v-model="editForm.name" label="Name" placeholder="Plan name" size="lg" />
              <UTextarea v-model="editForm.description" label="Description" :rows="3" />
            </div>
            <div class="mt-4 flex gap-2">
              <UButton color="indigo" :loading="saving" @click="handleSave">Save Changes</UButton>
              <UButton color="gray" variant="outline" @click="editMode = false">Cancel</UButton>
            </div>
          </div>

          <!-- Paths section -->
          <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-gray-700">
              <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Learning Paths</h2>
              <UButton size="xs" variant="ghost" icon="i-heroicons-plus" @click="showAddPath = true">
                Add Path
              </UButton>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-700">
              <div v-if="!plan.paths?.length" class="px-5 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                No paths assigned yet.
              </div>
              <div v-for="path in plan.paths" :key="path.id"
                class="flex items-center gap-3 px-5 py-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-50 dark:bg-indigo-950">
                  <UIcon name="i-heroicons-map" class="h-4 w-4 text-indigo-500" />
                </div>
                <span class="flex-1 text-sm font-medium text-gray-800 dark:text-gray-200">{{ path.name }}</span>
                <UButton icon="i-heroicons-arrow-top-right-on-square" size="xs" color="gray" variant="ghost"
                  @click="navigateTo(`/paths/${path.id}`)" />
                <UButton icon="i-heroicons-x-mark" size="xs" color="red" variant="ghost"
                  @click="handleRemovePath(path)" />
              </div>
            </div>
          </div>

          <!-- Clients section -->
          <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-gray-700">
              <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Clients</h2>
              <UButton size="xs" variant="ghost" icon="i-heroicons-user-plus" @click="showAddClient = true">
                Add Client
              </UButton>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-700">
              <div v-if="!plan.clients?.length" class="px-5 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                No clients assigned yet.
              </div>
              <div v-for="client in plan.clients" :key="client.id"
                class="flex items-center gap-3 px-5 py-3">
                <UAvatar :alt="client.fullname" size="xs" />
                <div class="min-w-0 flex-1">
                  <p class="truncate text-sm font-medium text-gray-800 dark:text-gray-200">{{ client.fullname }}</p>
                </div>
                <UButton icon="i-heroicons-user" size="xs" color="gray" variant="ghost"
                  @click="navigateTo(`/users/${client.id}`)" />
                <UButton icon="i-heroicons-x-mark" size="xs" color="red" variant="ghost"
                  @click="handleRemoveClient(client)" />
              </div>
            </div>
          </div>

        </div>

        <!-- Right sidebar -->
        <div class="flex flex-col gap-5">
          <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
            <h3 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">Overview</h3>
            <dl class="flex flex-col gap-3">
              <div class="flex justify-between text-sm">
                <dt class="text-gray-500 dark:text-gray-400">Paths</dt>
                <dd class="font-semibold text-gray-900 dark:text-white">{{ plan.paths?.length ?? 0 }}</dd>
              </div>
              <div class="flex justify-between text-sm">
                <dt class="text-gray-500 dark:text-gray-400">Clients</dt>
                <dd class="font-semibold text-gray-900 dark:text-white">{{ plan.clients?.length ?? 0 }}</dd>
              </div>
              <div class="flex justify-between text-sm">
                <dt class="text-gray-500 dark:text-gray-400">Created</dt>
                <dd class="font-semibold text-gray-900 dark:text-white">
                  {{ new Date(plan.created_at).toLocaleDateString('en-IE') }}
                </dd>
              </div>
            </dl>
            <p v-if="plan.description" class="mt-4 border-t border-gray-100 pt-4 text-xs text-gray-500 leading-relaxed dark:border-gray-700 dark:text-gray-400">
              {{ plan.description }}
            </p>
          </div>
        </div>

      </div>

    </template>

    <!-- Add Path modal -->
    <UModal v-model="showAddPath">
      <div class="p-6">
        <h3 class="mb-4 text-base font-bold text-gray-900 dark:text-white">Add Path to Plan</h3>
        <div class="flex flex-col gap-2 max-h-64 overflow-y-auto">
          <label
            v-for="path in availablePaths"
            :key="path.id"
            class="flex cursor-pointer items-center gap-3 rounded-lg border p-3 transition-colors"
            :class="selectedPathIds.includes(path.id)
              ? 'border-indigo-200 bg-indigo-50 dark:border-indigo-800 dark:bg-indigo-950/30'
              : 'border-gray-100 hover:bg-gray-50 dark:border-gray-700'"
          >
            <input type="checkbox" class="hidden" :value="path.id" v-model="selectedPathIds" />
            <UIcon name="i-heroicons-map" class="h-4 w-4 text-indigo-400" />
            <span class="flex-1 text-sm text-gray-800 dark:text-gray-200">{{ path.name }}</span>
            <UIcon v-if="selectedPathIds.includes(path.id)" name="i-heroicons-check-circle" class="h-5 w-5 text-indigo-500" />
          </label>
          <p v-if="!availablePaths.length" class="text-sm text-gray-400 py-4 text-center">All paths already added.</p>
        </div>
        <div class="mt-5 flex justify-end gap-2">
          <UButton color="gray" variant="outline" @click="showAddPath = false">Cancel</UButton>
          <UButton color="indigo" :loading="saving" :disabled="!selectedPathIds.length" @click="handleAddPaths">
            Add {{ selectedPathIds.length || '' }} Path{{ selectedPathIds.length !== 1 ? 's' : '' }}
          </UButton>
        </div>
      </div>
    </UModal>

    <!-- Add Client modal -->
    <UModal v-model="showAddClient">
      <div class="p-6">
        <h3 class="mb-4 text-base font-bold text-gray-900 dark:text-white">Add Client to Plan</h3>
        <UInput v-model="clientSearch" placeholder="Search clients…" icon="i-heroicons-magnifying-glass" size="sm" class="mb-3" />
        <div class="flex flex-col gap-2 max-h-64 overflow-y-auto">
          <label
            v-for="client in filteredAvailableClients"
            :key="client.id"
            class="flex cursor-pointer items-center gap-3 rounded-lg border p-2.5 transition-colors"
            :class="selectedClientIds.includes(client.id)
              ? 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-950/30'
              : 'border-gray-100 hover:bg-gray-50 dark:border-gray-700'"
          >
            <input type="checkbox" class="hidden" :value="client.id" v-model="selectedClientIds" />
            <UAvatar :alt="client.fullname" size="xs" />
            <div class="min-w-0 flex-1">
              <p class="truncate text-sm font-medium text-gray-800 dark:text-gray-200">{{ client.fullname }}</p>
              <p class="truncate text-xs text-gray-400">{{ client.email }}</p>
            </div>
            <UIcon v-if="selectedClientIds.includes(client.id)" name="i-heroicons-check-circle" class="h-5 w-5 text-green-500" />
          </label>
          <p v-if="!filteredAvailableClients.length" class="text-sm text-gray-400 py-4 text-center">No clients available.</p>
        </div>
        <div class="mt-5 flex justify-end gap-2">
          <UButton color="gray" variant="outline" @click="showAddClient = false">Cancel</UButton>
          <UButton color="green" :loading="saving" :disabled="!selectedClientIds.length" @click="handleAddClients">
            Add {{ selectedClientIds.length || '' }} Client{{ selectedClientIds.length !== 1 ? 's' : '' }}
          </UButton>
        </div>
      </div>
    </UModal>

  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({ layout: false, middleware: 'auth' })

const route  = useRoute()
const toast  = useToast()
const planId = computed(() => Number(route.params.id))

const { fetchPlan, updatePlan, attachClient, detachClient } = usePlans()
const { fetchPaths }  = usePaths()
const { fetchUsers }  = useUsers()

const plan    = ref<any>(null)
const loading = ref(true)
const saving  = ref(false)
const editMode = ref(false)
const editForm = reactive({ name: '', description: '' })

const showAddPath    = ref(false)
const showAddClient  = ref(false)
const allPaths       = ref<any[]>([])
const allClients     = ref<any[]>([])
const selectedPathIds   = ref<number[]>([])
const selectedClientIds = ref<number[]>([])
const clientSearch   = ref('')

const assignedPathIds   = computed(() => plan.value?.paths?.map((p: any) => p.id) ?? [])
const assignedClientIds = computed(() => plan.value?.clients?.map((c: any) => c.id) ?? [])

const availablePaths = computed(() =>
  allPaths.value.filter(p => !assignedPathIds.value.includes(p.id))
)
const filteredAvailableClients = computed(() => {
  const available = allClients.value.filter(c => !assignedClientIds.value.includes(c.id))
  const q = clientSearch.value.toLowerCase()
  return q ? available.filter(c => c.fullname.toLowerCase().includes(q) || c.email.toLowerCase().includes(q)) : available
})

onMounted(async () => {
  try {
    const [p, pathsRes, usersRes] = await Promise.all([
      fetchPlan(planId.value),
      fetchPaths({ per_page: 100 }),
      fetchUsers({ per_page: 100 }),
    ])
    plan.value       = p
    allPaths.value   = (pathsRes as any)?.data ?? []
    allClients.value = ((usersRes as any)?.data ?? []).filter((u: any) => u.role === 'client')
    editForm.name        = p.name
    editForm.description = p.description ?? ''
  } finally {
    loading.value = false
  }
})

useHead(() => ({ title: plan.value ? `${plan.value.name} — Plans` : 'Plan — CODECV' }))

async function handleSave() {
  saving.value = true
  try {
    await updatePlan(planId.value, { name: editForm.name, description: editForm.description })
    plan.value = { ...plan.value, name: editForm.name, description: editForm.description }
    editMode.value = false
    toast.add({ title: 'Plan updated', color: 'green' })
  } catch {
    toast.add({ title: 'Error', description: 'Could not update plan.', color: 'red' })
  } finally {
    saving.value = false
  }
}

async function handleAddPaths() {
  saving.value = true
  try {
    const currentIds = assignedPathIds.value
    await updatePlan(planId.value, { path_ids: [...currentIds, ...selectedPathIds.value] })
    plan.value = await fetchPlan(planId.value)
    showAddPath.value = false
    selectedPathIds.value = []
    toast.add({ title: 'Paths added', color: 'green' })
  } catch {
    toast.add({ title: 'Error', description: 'Could not add paths.', color: 'red' })
  } finally {
    saving.value = false
  }
}

async function handleRemovePath(path: any) {
  if (!confirm(`Remove "${path.name}" from this plan?`)) return
  try {
    const remaining = assignedPathIds.value.filter((id: number) => id !== path.id)
    await updatePlan(planId.value, { path_ids: remaining })
    plan.value = await fetchPlan(planId.value)
    toast.add({ title: 'Path removed', color: 'green' })
  } catch {
    toast.add({ title: 'Error', description: 'Could not remove path.', color: 'red' })
  }
}

async function handleAddClients() {
  saving.value = true
  try {
    await Promise.all(selectedClientIds.value.map(id => attachClient(planId.value, id)))
    plan.value = await fetchPlan(planId.value)
    showAddClient.value = false
    selectedClientIds.value = []
    clientSearch.value = ''
    toast.add({ title: 'Clients added', color: 'green' })
  } catch {
    toast.add({ title: 'Error', description: 'Could not add clients.', color: 'red' })
  } finally {
    saving.value = false
  }
}

async function handleRemoveClient(client: any) {
  if (!confirm(`Remove ${client.fullname} from this plan?`)) return
  try {
    await detachClient(planId.value, client.id)
    plan.value = await fetchPlan(planId.value)
    toast.add({ title: 'Client removed', color: 'green' })
  } catch {
    toast.add({ title: 'Error', description: 'Could not remove client.', color: 'red' })
  }
}
</script>
