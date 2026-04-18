<template>
  <NuxtLayout name="admin">

    <div class="mb-6 flex items-center gap-3">
      <UButton icon="i-heroicons-arrow-left" size="sm" color="gray" variant="ghost" @click="router.back()" />
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">New Plan</h1>
        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Bundle paths and assign clients in one step</p>
      </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

      <!-- Form -->
      <div class="lg:col-span-2 flex flex-col gap-5">

        <!-- Details card -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
          <div class="border-b border-gray-100 px-6 py-4 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Plan Details</h2>
          </div>
          <div class="flex flex-col gap-4 p-6">
            <div>
              <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">
                Name <span class="text-red-400">*</span>
              </label>
              <UInput v-model="form.name" placeholder="e.g. Backend Bootcamp — April 2026" size="lg" :disabled="saving" />
            </div>
            <div>
              <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">Description</label>
              <UTextarea v-model="form.description" :rows="3"
                placeholder="What does this plan cover and who is it for?" :disabled="saving" />
            </div>
          </div>
        </div>

        <!-- Paths card -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
          <div class="border-b border-gray-100 px-6 py-4 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Learning Paths</h2>
            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Select which paths are included in this plan</p>
          </div>
          <div class="p-6">
            <div v-if="pathsLoading" class="flex justify-center py-6">
              <UIcon name="i-heroicons-arrow-path" class="animate-spin text-indigo-400" />
            </div>
            <div v-else-if="!allPaths.length" class="text-sm text-gray-400 dark:text-gray-500">
              No paths available.
              <NuxtLink to="/paths/create" class="text-indigo-500 hover:underline">Create one first.</NuxtLink>
            </div>
            <div v-else class="flex flex-col gap-2">
              <label
                v-for="path in allPaths"
                :key="path.id"
                class="flex cursor-pointer items-center gap-3 rounded-lg border p-3 transition-colors"
                :class="form.path_ids.includes(path.id)
                  ? 'border-indigo-200 bg-indigo-50 dark:border-indigo-800 dark:bg-indigo-950/30'
                  : 'border-gray-100 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/30'"
              >
                <input type="checkbox" class="hidden" :value="path.id" v-model="form.path_ids" />
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg"
                  :class="form.path_ids.includes(path.id) ? 'bg-indigo-100 dark:bg-indigo-900/50' : 'bg-gray-100 dark:bg-gray-700'">
                  <UIcon name="i-heroicons-map" class="h-4 w-4"
                    :class="form.path_ids.includes(path.id) ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400'" />
                </div>
                <span class="flex-1 text-sm font-medium text-gray-800 dark:text-gray-200">{{ path.name }}</span>
                <UIcon v-if="form.path_ids.includes(path.id)"
                  name="i-heroicons-check-circle" class="h-5 w-5 text-indigo-500" />
              </label>
            </div>
          </div>
        </div>

        <!-- Clients card -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
          <div class="border-b border-gray-100 px-6 py-4 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Assign Clients</h2>
            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Select which clients will have access to this plan</p>
          </div>
          <div class="p-6">
            <UInput v-model="clientSearch" placeholder="Search clients…" icon="i-heroicons-magnifying-glass"
              size="sm" class="mb-3" />
            <div v-if="usersLoading" class="flex justify-center py-6">
              <UIcon name="i-heroicons-arrow-path" class="animate-spin text-indigo-400" />
            </div>
            <div v-else-if="!filteredClients.length" class="text-sm text-gray-400 dark:text-gray-500">
              No clients found.
            </div>
            <div v-else class="flex max-h-56 flex-col gap-1.5 overflow-y-auto">
              <label
                v-for="client in filteredClients"
                :key="client.id"
                class="flex cursor-pointer items-center gap-3 rounded-lg border p-2.5 transition-colors"
                :class="form.client_ids.includes(client.id)
                  ? 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-950/30'
                  : 'border-gray-100 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/30'"
              >
                <input type="checkbox" class="hidden" :value="client.id" v-model="form.client_ids" />
                <UAvatar :src="client.profile?.profile_image_url" :alt="client.fullname" size="xs" />
                <div class="min-w-0 flex-1">
                  <p class="truncate text-sm font-medium text-gray-800 dark:text-gray-200">{{ client.fullname }}</p>
                  <p class="truncate text-xs text-gray-400 dark:text-gray-500">{{ client.email }}</p>
                </div>
                <UIcon v-if="form.client_ids.includes(client.id)"
                  name="i-heroicons-check-circle" class="h-5 w-5 shrink-0 text-green-500" />
              </label>
            </div>
          </div>
        </div>

      </div>

      <!-- Sidebar summary -->
      <div class="flex flex-col gap-5">
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 p-5">
          <h3 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">Summary</h3>
          <dl class="flex flex-col gap-3 text-sm">
            <div class="flex justify-between">
              <dt class="text-gray-500 dark:text-gray-400">Paths</dt>
              <dd class="font-semibold text-gray-900 dark:text-white">{{ form.path_ids.length }}</dd>
            </div>
            <div class="flex justify-between">
              <dt class="text-gray-500 dark:text-gray-400">Clients</dt>
              <dd class="font-semibold text-gray-900 dark:text-white">{{ form.client_ids.length }}</dd>
            </div>
          </dl>

          <div v-if="error" class="mt-4 flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2.5 text-xs text-red-700 dark:border-red-800/40 dark:bg-red-950/20 dark:text-red-400">
            <UIcon name="i-heroicons-exclamation-circle" class="h-4 w-4 shrink-0" />
            {{ error }}
          </div>

          <UButton type="button" block color="indigo" size="md" class="mt-5" :loading="saving"
            :disabled="!form.name.trim() || saving" @click="handleSubmit">
            Create Plan
          </UButton>
          <UButton type="button" block color="gray" variant="outline" size="md" class="mt-2"
            @click="router.back()">
            Cancel
          </UButton>
        </div>
      </div>

    </div>

  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({ layout: false, middleware: 'auth' })
useHead({ title: 'New Plan — CODECV' })

const router = useRouter()
const toast  = useToast()
const { createPlan }           = usePlans()
const { fetchPaths }           = usePaths()
const { fetchUsers }           = useUsers()

const form = reactive({
  name: '',
  description: '',
  path_ids: [] as number[],
  client_ids: [] as number[],
})

const saving       = ref(false)
const error        = ref('')
const pathsLoading = ref(false)
const usersLoading = ref(false)
const allPaths     = ref<any[]>([])
const allClients   = ref<any[]>([])
const clientSearch = ref('')

const filteredClients = computed(() => {
  const q = clientSearch.value.toLowerCase()
  return q
    ? allClients.value.filter(c => c.fullname.toLowerCase().includes(q) || c.email.toLowerCase().includes(q))
    : allClients.value
})

onMounted(async () => {
  pathsLoading.value = true
  usersLoading.value = true
  try {
    const [pathsRes, usersRes] = await Promise.all([
      fetchPaths({ per_page: 100 }),
      fetchUsers({ per_page: 100 }),
    ])
    allPaths.value   = (pathsRes as any)?.data ?? []
    allClients.value = ((usersRes as any)?.data ?? []).filter((u: any) => u.role === 'client')
  } finally {
    pathsLoading.value = false
    usersLoading.value = false
  }
})

async function handleSubmit() {
  if (!form.name.trim()) return
  saving.value = true
  error.value  = ''
  try {
    await createPlan({
      name: form.name,
      description: form.description || undefined,
      path_ids: form.path_ids,
      client_ids: form.client_ids,
    })
    toast.add({ title: 'Plan created', color: 'green' })
    navigateTo('/plans')
  } catch (err: any) {
    error.value = err?.data?.message || err?.message || 'Failed to create plan.'
  } finally {
    saving.value = false
  }
}
</script>
