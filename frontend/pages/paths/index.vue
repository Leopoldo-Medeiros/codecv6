<template>
  <NuxtLayout name="admin">
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
            Learning Paths
          </h1>
          <p class="text-gray-500 dark:text-gray-400 text-lg">
            Manage learning paths and career tracks
          </p>
        </div>
        <UButton color="primary" @click="navigateTo('/paths/create')">
          <Icon name="heroicons:plus" class="w-4 h-4 mr-2" />
          Create Path
        </UButton>
      </div>
    </div>

    <!-- Search -->
    <UCard class="mb-6">
      <div class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
          <UInput
            v-model="searchQuery"
            placeholder="Search paths..."
            icon="i-heroicons-magnifying-glass"
            size="lg"
            @keyup.enter="handleSearch"
          />
        </div>
        <UButton color="primary" size="lg" class="w-full sm:w-auto" @click="handleSearch">
          <Icon name="heroicons:magnifying-glass" class="w-4 h-4 mr-2" />
          Search
        </UButton>
      </div>
    </UCard>

    <!-- Error -->
    <UAlert v-if="error" color="red" variant="subtle" class="mb-6" :description="error" />

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center p-12">
      <UIcon name="i-heroicons-arrow-path" class="w-8 h-8 animate-spin text-primary-500" />
    </div>

    <!-- Empty -->
    <UCard v-else-if="!paths.length" class="text-center p-12">
      <p class="text-gray-500 dark:text-gray-400">No learning paths found.</p>
    </UCard>

    <!-- Paths Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <UCard v-for="path in paths" :key="path.id" class="hover:shadow-lg transition-shadow">
        <div class="p-4">
          <div class="flex items-start justify-between mb-3">
            <h3 class="font-semibold text-gray-900 dark:text-white">{{ path.name }}</h3>
            <UDropdown :items="getActionItems(path)">
              <UButton icon="i-heroicons-ellipsis-vertical" variant="ghost" color="gray" size="sm" />
            </UDropdown>
          </div>

          <p class="text-gray-500 dark:text-gray-400 text-sm mb-4 line-clamp-2">
            {{ path.description || 'No description.' }}
          </p>

          <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
            <div class="flex items-center gap-2">
              <Icon name="heroicons:map" class="w-4 h-4" />
              <span>{{ path.plans?.length ?? 0 }} plans</span>
            </div>
            <div class="flex items-center gap-2">
              <Icon name="heroicons:calendar" class="w-4 h-4" />
              <span>{{ formatDate(path.created_at) }}</span>
            </div>
          </div>

          <div v-if="path.consultant" class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
            <p class="text-xs text-gray-400 dark:text-gray-500">
              By {{ path.consultant.fullname }}
            </p>
          </div>
        </div>
      </UCard>
    </div>

    <!-- Pagination -->
    <div v-if="totalPages > 1" class="flex items-center justify-between mt-6">
      <p class="text-sm text-gray-500 dark:text-gray-400">Page {{ currentPage }} of {{ totalPages }}</p>
      <div class="flex gap-2">
        <UButton variant="outline" size="sm" :disabled="currentPage === 1" @click="changePage(currentPage - 1)">
          Previous
        </UButton>
        <UButton variant="outline" size="sm" :disabled="currentPage >= totalPages" @click="changePage(currentPage + 1)">
          Next
        </UButton>
      </div>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({
  layout: false,
  middleware: 'auth'
})

const toast = useToast()
const { paths, loading, error, fetchPaths, deletePath } = usePaths()

const searchQuery = ref('')
const currentPage = ref(1)
const totalPages = ref(1)

const load = async () => {
  const res = await fetchPaths({
    search: searchQuery.value || undefined,
    page: currentPage.value,
    per_page: 12,
  })
  if (res?.meta) totalPages.value = res.meta.last_page
}

onMounted(load)

const handleSearch = () => {
  currentPage.value = 1
  load()
}

const changePage = (page: number) => {
  currentPage.value = page
  load()
}

const formatDate = (date: string) => new Date(date).toLocaleDateString()

const getActionItems = (path: any) => [
  [{
    label: 'Manage Steps',
    icon: 'i-heroicons-map',
    click: () => navigateTo(`/paths/${path.id}`)
  }],
  [{
    label: 'Delete',
    icon: 'i-heroicons-trash',
    click: () => handleDeletePath(path)
  }]
]

const handleDeletePath = async (path: any) => {
  if (!confirm(`Delete "${path.name}"?`)) return
  try {
    await deletePath(path.id)
    toast.add({ title: 'Path deleted', color: 'green' })
    load()
  } catch (e: any) {
    toast.add({ title: 'Error', description: e?.data?.message || 'Failed to delete path.', color: 'red' })
  }
}
</script>
