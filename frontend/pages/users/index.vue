<template>
  <NuxtLayout name="admin">
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
            Users Management
          </h1>
          <p class="text-gray-500 dark:text-gray-400 text-lg">
            Manage user accounts and permissions
          </p>
        </div>
        <UButton @click="goToCreateUser" color="primary">
          <Icon name="heroicons:plus" class="w-4 h-4 mr-2" />
          Create User
        </UButton>
      </div>
    </div>

    <!-- Search -->
    <UCard class="mb-6">
      <div class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
          <UInput
            v-model="searchQuery"
            placeholder="Search users..."
            icon="i-heroicons-magnifying-glass"
            size="lg"
            @keyup.enter="handleSearch"
          />
        </div>
        <UButton @click="handleSearch" color="primary" size="lg" class="w-full sm:w-auto">
          <Icon name="heroicons:magnifying-glass" class="w-4 h-4 mr-2" />
          Search
        </UButton>
      </div>
    </UCard>

    <!-- Error -->
    <UAlert v-if="error" color="red" variant="subtle" class="mb-6" :description="error" />

    <!-- Table -->
    <UCard>
      <div v-if="loading" class="flex justify-center p-8">
        <UIcon name="i-heroicons-arrow-path" class="w-8 h-8 animate-spin text-primary-500" />
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-200 dark:border-gray-700">
              <th class="text-left p-4 font-semibold text-gray-900 dark:text-white">User</th>
              <th class="text-left p-4 font-semibold text-gray-900 dark:text-white">Email</th>
              <th class="text-left p-4 font-semibold text-gray-900 dark:text-white">Role</th>
              <th class="text-left p-4 font-semibold text-gray-900 dark:text-white">Created</th>
              <th class="text-left p-4 font-semibold text-gray-900 dark:text-white">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="u in users"
              :key="u.id"
              class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
            >
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <UAvatar
                    :src="u.profile?.profile_image_url || '/images/team-13.jpg'"
                    :alt="u.fullname"
                    size="sm"
                  />
                  <div>
                    <p class="font-medium text-gray-900 dark:text-white">{{ u.fullname }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ u.profile?.profession || 'N/A' }}</p>
                  </div>
                </div>
              </td>
              <td class="p-4">
                <p class="text-gray-900 dark:text-white">{{ u.email }}</p>
              </td>
              <td class="p-4">
                <UBadge :color="roleBadgeColor(u.role)" variant="subtle">
                  {{ u.role }}
                </UBadge>
              </td>
              <td class="p-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ formatDate(u.created_at) }}</p>
              </td>
              <td class="p-4">
                <div class="flex gap-2">
                  <UButton @click="viewUser(u.id)" variant="ghost" size="sm" color="blue">
                    <Icon name="heroicons:eye" class="w-4 h-4" />
                  </UButton>
                  <UButton @click="editUser(u.id)" variant="ghost" size="sm" color="primary">
                    <Icon name="heroicons:pencil-square" class="w-4 h-4" />
                  </UButton>
                  <UButton @click="handleDeleteUser(u)" variant="ghost" size="sm" color="red">
                    <Icon name="heroicons:trash" class="w-4 h-4" />
                  </UButton>
                </div>
              </td>
            </tr>
            <tr v-if="!users.length && !loading">
              <td colspan="5" class="p-8 text-center text-gray-500 dark:text-gray-400">No users found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex items-center justify-between p-4 border-t border-gray-200 dark:border-gray-700">
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
    </UCard>
  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({
  layout: false,
  middleware: 'auth'
})

const router = useRouter()
const toast = useToast()
const { users, loading, error, fetchUsers, deleteUser } = useUsers()

const searchQuery = ref('')
const currentPage = ref(1)
const totalPages = ref(1)

const load = async () => {
  const res = await fetchUsers({
    search: searchQuery.value || undefined,
    page: currentPage.value,
    per_page: 10,
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

const roleBadgeColor = (role?: string) => {
  switch (role) {
    case 'admin': return 'red'
    case 'consultant': return 'purple'
    default: return 'blue'
  }
}

const formatDate = (date: string) => new Date(date).toLocaleDateString()

const goToCreateUser = () => router.push('/users/create')
const viewUser = (id: number) => router.push(`/users/${id}`)
const editUser = (id: number) => router.push(`/users/${id}-edit`)

const handleDeleteUser = async (u: any) => {
  if (!confirm(`Delete ${u.fullname}?`)) return
  try {
    await deleteUser(u.id)
    toast.add({ title: 'User deleted', description: `${u.fullname} was deleted.`, color: 'green' })
    load()
  } catch (e: any) {
    toast.add({ title: 'Error', description: e?.data?.message || 'Failed to delete user.', color: 'red' })
  }
}

useHead({ title: 'Users Management' })
</script>
