<template>
  <NuxtLayout name="admin">

    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Clients</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
          Track your clients' progress and manage their training plans.
        </p>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center py-20">
      <UIcon name="i-heroicons-arrow-path" class="animate-spin text-3xl text-emerald-500" />
    </div>

    <div v-else-if="!clients.length" class="flex flex-col items-center py-20 text-center">
      <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-emerald-50 dark:bg-emerald-950">
        <UIcon name="i-heroicons-users" class="h-10 w-10 text-emerald-400" />
      </div>
      <h3 class="text-base font-semibold text-gray-900 dark:text-white">No clients yet</h3>
      <p class="mt-2 max-w-sm text-sm text-gray-500 dark:text-gray-400">
        Clients assigned to you by an admin will appear here.
      </p>
    </div>

    <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <NuxtLink
        v-for="client in clients"
        :key="client.id"
        :to="`/my-clients/${client.id}`"
        class="group rounded-xl border border-gray-200 bg-white p-5 transition-all hover:border-emerald-300 hover:shadow-md
               dark:border-gray-700 dark:bg-gray-800 dark:hover:border-emerald-700"
      >
        <!-- Avatar + name -->
        <div class="flex items-center gap-3">
          <UAvatar
            :src="client.profile?.profile_image_url ?? undefined"
            :alt="client.fullname"
            size="md"
            :ui="{ background: 'bg-emerald-100 dark:bg-emerald-900' }"
          />
          <div class="min-w-0 flex-1">
            <p class="truncate font-semibold text-gray-900 dark:text-white">{{ client.fullname }}</p>
            <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ client.email }}</p>
          </div>
        </div>

        <!-- Profession + level badges -->
        <div v-if="client.profile?.profession || client.profile?.level" class="mt-3 flex flex-wrap gap-1.5">
          <UBadge v-if="client.profile?.profession" color="gray" variant="subtle" size="xs">
            {{ client.profile.profession }}
          </UBadge>
          <UBadge v-if="client.profile?.level" :color="levelColor(client.profile.level)" variant="subtle" size="xs">
            {{ client.profile.level }}
          </UBadge>
        </div>

        <!-- Progress -->
        <div class="mt-4">
          <div class="mb-1 flex items-center justify-between text-xs">
            <span class="text-gray-500 dark:text-gray-400">
              {{ client.path_count }} path{{ client.path_count !== 1 ? 's' : '' }} ·
              {{ client.done_steps }}/{{ client.total_steps }} steps done
            </span>
            <span class="font-bold" :class="progressColor(client.progress_pct)">
              {{ client.progress_pct }}%
            </span>
          </div>
          <UProgress :value="client.progress_pct" size="xs" color="emerald" />
        </div>
      </NuxtLink>
    </div>

  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({ layout: false, middleware: 'auth' })
useHead({ title: 'My Clients — CODECV' })

const { clients, loading, fetchMyClients } = useMyClients()

onMounted(fetchMyClients)

function levelColor(level: string) {
  if (level === 'senior' || level === 'manager') return 'emerald'
  if (level === 'mid') return 'blue'
  return 'gray'
}

function progressColor(pct: number) {
  if (pct >= 75) return 'text-emerald-600 dark:text-emerald-400'
  if (pct >= 40) return 'text-blue-600 dark:text-blue-400'
  return 'text-gray-500 dark:text-gray-400'
}
</script>
