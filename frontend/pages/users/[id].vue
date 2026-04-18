<template>
  <NuxtLayout name="admin">
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
            User Profile
          </h1>
          <p class="text-gray-500 dark:text-gray-400 text-lg">
            Viewing details for {{ selectedUser?.fullname }}
          </p>
        </div>
        <UButton @click="navigateTo('/users')" variant="ghost" color="gray">
          <Icon name="heroicons:arrow-left" class="w-4 h-4 mr-2" />
          Back to Users
        </UButton>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center p-12">
      <UIcon name="i-heroicons-arrow-path" class="w-8 h-8 animate-spin text-primary-500" />
    </div>

    <!-- Error -->
    <UAlert v-else-if="fetchError" color="red" variant="subtle" :description="fetchError" />

    <!-- User Details -->
    <div v-else-if="selectedUser" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Profile Card -->
      <UCard class="lg:col-span-1">
        <div class="text-center">
          <div class="relative mb-4">
            <div class="absolute inset-0 rounded-full bg-primary-500 blur-md opacity-20 animate-pulse"></div>
            <UAvatar
              :src="selectedUser.profile?.profile_image_url || '/images/team-13.jpg'"
              :alt="selectedUser.fullname"
              size="3xl"
              class="ring-2 ring-primary-500 ring-offset-2 dark:ring-offset-gray-900"
            />
          </div>
          <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ selectedUser.fullname }}</h2>
          <p class="text-gray-500 dark:text-gray-400 mb-4">{{ selectedUser.profile?.profession || '—' }}</p>
          <UBadge :color="roleBadgeColor(selectedUser.role)" variant="subtle">
            {{ selectedUser.role }}
          </UBadge>
        </div>

        <UDivider class="my-6" />

        <!-- Social Links -->
        <div class="space-y-3">
          <div class="flex items-center justify-between text-sm">
            <div class="flex items-center gap-2 text-gray-500">
              <Icon name="mdi:github" class="w-5 h-5" />
              <span>GitHub</span>
            </div>
            <a v-if="selectedUser.profile?.github" :href="selectedUser.profile.github" target="_blank" class="text-primary-500 truncate max-w-[150px]">View Profile</a>
            <span v-else class="text-gray-400">Not available</span>
          </div>
          <div class="flex items-center justify-between text-sm">
            <div class="flex items-center gap-2 text-gray-500">
              <Icon name="mdi:linkedin" class="w-5 h-5 text-blue-600" />
              <span>LinkedIn</span>
            </div>
            <a v-if="selectedUser.profile?.linkedin" :href="selectedUser.profile.linkedin" target="_blank" class="text-primary-500 truncate max-w-[150px]">View Profile</a>
            <span v-else class="text-gray-400">Not available</span>
          </div>
        </div>
      </UCard>

      <!-- Details Card -->
      <div class="lg:col-span-2 space-y-6">
        <UCard>
          <template #header>
            <h3 class="font-semibold text-gray-900 dark:text-white">Account Details</h3>
          </template>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Full Name</label>
              <p class="text-gray-900 dark:text-white font-medium border-b dark:border-gray-800 pb-2 mt-1">{{ selectedUser.fullname }}</p>
            </div>
            <div>
              <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Email</label>
              <p class="text-gray-900 dark:text-white font-medium border-b dark:border-gray-800 pb-2 mt-1">{{ selectedUser.email }}</p>
            </div>
            <div>
              <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Birth Date</label>
              <p class="text-gray-900 dark:text-white font-medium border-b dark:border-gray-800 pb-2 mt-1">{{ selectedUser.profile?.birth_date || 'N/A' }}</p>
            </div>
            <div>
              <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Profession</label>
              <p class="text-gray-900 dark:text-white font-medium border-b dark:border-gray-800 pb-2 mt-1">{{ selectedUser.profile?.profession || 'N/A' }}</p>
            </div>
            <div>
              <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Member Since</label>
              <p class="text-gray-900 dark:text-white font-medium border-b dark:border-gray-800 pb-2 mt-1">{{ formatDate(selectedUser.created_at) }}</p>
            </div>
          </div>
        </UCard>
      </div>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({
  layout: false,
  middleware: 'auth'
})

const route = useRoute()
const userId = computed(() => Number(route.params.id))
const { user: selectedUser, loading, error: fetchError, fetchUser } = useUsers()

const roleBadgeColor = (role?: string) => {
  switch (role) {
    case 'admin': return 'red'
    case 'consultant': return 'purple'
    default: return 'blue'
  }
}

const formatDate = (date: string) => new Date(date).toLocaleDateString()

onMounted(() => fetchUser(userId.value))
</script>
