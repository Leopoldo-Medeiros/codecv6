<template>
  <NuxtLayout name="admin">
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
            Jobs
          </h1>
          <p class="text-gray-500 dark:text-gray-400 text-lg">
            Browse and manage job listings
          </p>
        </div>
        <UButton color="primary">
          <Icon name="heroicons:plus" class="w-4 h-4 mr-2" />
          Add Job
        </UButton>
      </div>
    </div>

    <!-- Error -->
    <UAlert v-if="error" color="red" variant="subtle" class="mb-6" :description="error" />

    <!-- Table -->
    <UCard>
      <template #header>
        <div class="flex items-center justify-between">
          <p class="font-semibold text-gray-900 dark:text-white">All Listings</p>
          <span class="text-sm text-gray-500 dark:text-gray-400">{{ totalJobs }} total</span>
        </div>
      </template>

      <div v-if="loading" class="flex justify-center p-8">
        <UIcon name="i-heroicons-arrow-path" class="w-8 h-8 animate-spin text-primary-500" />
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-200 dark:border-gray-700">
              <th class="text-left p-4 font-semibold text-gray-900 dark:text-white">Title</th>
              <th class="text-left p-4 font-semibold text-gray-900 dark:text-white">Company</th>
              <th class="text-left p-4 font-semibold text-gray-900 dark:text-white">Location</th>
              <th class="text-left p-4 font-semibold text-gray-900 dark:text-white">Salary</th>
              <th class="text-left p-4 font-semibold text-gray-900 dark:text-white">Posted</th>
              <th class="text-left p-4 font-semibold text-gray-900 dark:text-white">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!jobs.length">
              <td colspan="6" class="p-8 text-center text-gray-500 dark:text-gray-400">
                No jobs found.
              </td>
            </tr>
            <tr
              v-for="job in jobs"
              :key="job.id"
              class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
            >
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-primary-100 dark:bg-primary-900 flex items-center justify-center flex-shrink-0">
                    <UIcon name="i-heroicons-briefcase" class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                  </div>
                  <div>
                    <p class="font-medium text-gray-900 dark:text-white">{{ job.title }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-1">{{ job.description }}</p>
                  </div>
                </div>
              </td>
              <td class="p-4">
                <UBadge color="gray" variant="subtle">{{ job.company || '—' }}</UBadge>
              </td>
              <td class="p-4">
                <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                  <UIcon name="i-heroicons-map-pin" class="w-4 h-4 text-gray-400" />
                  {{ job.location || '—' }}
                </div>
              </td>
              <td class="p-4">
                <p class="text-sm text-gray-900 dark:text-white font-medium">
                  {{ job.salary ? `R$ ${job.salary}` : '—' }}
                </p>
              </td>
              <td class="p-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ formatDate(job.created_at) }}</p>
              </td>
              <td class="p-4">
                <UDropdown :items="getActionItems(job)" :popper="{ placement: 'bottom-end' }">
                  <UButton icon="i-heroicons-ellipsis-horizontal" color="gray" variant="ghost" size="sm" />
                </UDropdown>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <template #footer v-if="totalPages > 1">
        <div class="flex items-center justify-between">
          <p class="text-sm text-gray-500 dark:text-gray-400">
            Page {{ currentPage }} of {{ totalPages }}
          </p>
          <div class="flex gap-2">
            <UButton variant="outline" size="sm" :disabled="currentPage === 1" @click="changePage(currentPage - 1)">
              Previous
            </UButton>
            <UButton variant="outline" size="sm" :disabled="currentPage >= totalPages" @click="changePage(currentPage + 1)">
              Next
            </UButton>
          </div>
        </div>
      </template>
    </UCard>
  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({
  layout: false,
  middleware: 'auth'
})

const toast = useToast()
const { jobs, loading, error, fetchJobs, deleteJob } = useJobs()

const currentPage = ref(1)
const totalJobs = ref(0)
const totalPages = computed(() => Math.max(1, Math.ceil(totalJobs.value / 10)))

const load = async () => {
  const res = await fetchJobs({ page: currentPage.value, per_page: 10 })
  if (res?.meta) totalJobs.value = res.meta.total
}

onMounted(load)

const changePage = (page: number) => {
  currentPage.value = page
  load()
}

const formatDate = (date: string) => new Date(date).toLocaleDateString()

const getActionItems = (job: any) => [[
  {
    label: 'Delete',
    icon: 'i-heroicons-trash',
    click: () => handleDeleteJob(job)
  }
]]

const handleDeleteJob = async (job: any) => {
  if (!confirm(`Delete "${job.title}"?`)) return
  try {
    await deleteJob(job.id)
    toast.add({ title: 'Job deleted', color: 'green' })
    load()
  } catch (e: any) {
    toast.add({ title: 'Error', description: e?.data?.message || 'Failed to delete.', color: 'red' })
  }
}
</script>
