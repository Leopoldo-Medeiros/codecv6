<template>
  <NuxtLayout name="admin">

    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Plans</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Assign paths and clients to structured coaching plans</p>
      </div>
      <UButton icon="i-heroicons-plus" size="sm" color="teal" @click="navigateTo('/plans/create')">
        New Plan
      </UButton>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-20">
      <UIcon name="i-heroicons-arrow-path" class="animate-spin text-3xl text-teal-500" />
    </div>

    <!-- Empty -->
    <div v-else-if="!plans.length" class="flex flex-col items-center rounded-xl border border-dashed border-gray-200 bg-white py-20 text-center dark:border-gray-700 dark:bg-gray-800">
      <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-teal-50 dark:bg-teal-950">
        <UIcon name="i-heroicons-clipboard-document-list" class="h-8 w-8 text-teal-400" />
      </div>
      <p class="text-sm font-medium text-gray-700 dark:text-gray-300">No plans yet</p>
      <p class="mt-1 max-w-xs text-xs text-gray-400 dark:text-gray-500">Create a plan to bundle paths and assign clients.</p>
      <UButton size="sm" color="teal" class="mt-5" icon="i-heroicons-plus" @click="navigateTo('/plans/create')">
        Create First Plan
      </UButton>
    </div>

    <!-- Plans grid -->
    <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
      <div
        v-for="plan in plans"
        :key="plan.id"
        class="flex flex-col rounded-xl border border-gray-200 bg-white transition-shadow hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
      >
        <div class="flex-1 p-5">
          <div class="mb-3 flex items-start justify-between gap-2">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-teal-50 dark:bg-teal-950">
              <UIcon name="i-heroicons-clipboard-document-list" class="h-5 w-5 text-teal-500" />
            </div>
            <UDropdown :items="planActions(plan)" :popper="{ placement: 'bottom-end' }">
              <UButton icon="i-heroicons-ellipsis-horizontal" color="gray" variant="ghost" size="xs" />
            </UDropdown>
          </div>

          <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ plan.name }}</h3>
          <p v-if="plan.description" class="mt-1 text-xs text-gray-500 dark:text-gray-400 line-clamp-2 leading-relaxed">
            {{ plan.description }}
          </p>

          <div class="mt-4 flex flex-wrap gap-3">
            <div class="flex items-center gap-1.5">
              <UIcon name="i-heroicons-map" class="h-3.5 w-3.5 text-teal-400" />
              <span class="text-xs text-gray-500 dark:text-gray-400">
                {{ plan.paths?.length ?? 0 }} path{{ (plan.paths?.length ?? 0) !== 1 ? 's' : '' }}
              </span>
            </div>
            <div class="flex items-center gap-1.5">
              <UIcon name="i-heroicons-users" class="h-3.5 w-3.5 text-green-400" />
              <span class="text-xs text-gray-500 dark:text-gray-400">
                {{ plan.clients?.length ?? 0 }} client{{ (plan.clients?.length ?? 0) !== 1 ? 's' : '' }}
              </span>
            </div>
          </div>
        </div>

        <div class="border-t border-gray-100 px-5 py-3 dark:border-gray-700">
          <UButton size="xs" color="gray" variant="ghost" trailing-icon="i-heroicons-arrow-right"
            @click="navigateTo(`/plans/${plan.id}`)">
            Manage plan
          </UButton>
        </div>
      </div>
    </div>

  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({ layout: false, middleware: 'auth' })
useHead({ title: 'Plans — CODECV' })

const toast = useToast()
const { plans, loading, fetchPlans, deletePlan } = usePlans()

onMounted(() => fetchPlans({ per_page: 50 }))

const planActions = (plan: any) => [[
  { label: 'Manage',  icon: 'i-heroicons-pencil-square', click: () => navigateTo(`/plans/${plan.id}`) },
  { label: 'Delete',  icon: 'i-heroicons-trash',         click: () => handleDelete(plan) },
]]

async function handleDelete(plan: any) {
  if (!confirm(`Delete "${plan.name}"? This will unassign all clients and paths.`)) return
  try {
    await deletePlan(plan.id)
    await fetchPlans({ per_page: 50 })
    toast.add({ title: 'Plan deleted', color: 'green' })
  } catch {
    toast.add({ title: 'Error', description: 'Could not delete plan.', color: 'red' })
  }
}
</script>
