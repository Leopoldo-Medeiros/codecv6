<template>
  <NuxtLayout name="admin">
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
        <NuxtLink to="/dashboard" class="hover:text-primary">Dashboard</NuxtLink>
        <Icon name="heroicons:chevron-right" class="w-4 h-4" />
        <NuxtLink to="/users" class="hover:text-primary">Users</NuxtLink>
        <Icon name="heroicons:chevron-right" class="w-4 h-4" />
        <span class="text-gray-900 dark:text-white">Edit User</span>
      </div>

      <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
        Edit User
      </h1>
      <p class="text-gray-500 dark:text-gray-400 text-lg">
        Update user information and permissions
      </p>
    </div>

    <!-- Edit Form -->
    <UCard class="hover:shadow-lg transition-shadow duration-200">
      <template #header>
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">User Information</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
              Edit the user's details below
            </p>
          </div>
          <UButton variant="ghost" color="gray" @click="navigateTo('/users')">
            <Icon name="heroicons:x-mark" class="w-4 h-4 mr-2" />
            Cancel
          </UButton>
        </div>
      </template>

      <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Profile Picture Section -->
        <div class="flex items-center gap-6 p-6 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
          <UAvatar
            :src="form.profile_image || '/images/team-13.jpg'"
            :alt="form.fullname"
            size="xl"
          />
          <div>
            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Profile Picture</h4>
            <UButton type="button" variant="soft" size="sm">
              <Icon name="heroicons:camera" class="w-4 h-4 mr-2" />
              Change Photo
            </UButton>
          </div>
        </div>

        <!-- Form Fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Full Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Full Name
            </label>
            <UInput
              v-model="form.fullname"
              placeholder="Enter full name"
              required
            />
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Email Address
            </label>
            <UInput
              v-model="form.email"
              type="email"
              placeholder="Enter email address"
              required
            />
          </div>

          <!-- Role -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Role
            </label>
            <USelectMenu
              v-model="form.role"
              :options="roleOptions"
              placeholder="Select role"
            />
          </div>

          <!-- Status -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Status
            </label>
            <USelectMenu
              v-model="form.status"
              :options="statusOptions"
              placeholder="Select status"
            />
          </div>
        </div>

        <!-- Password Section -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
          <h4 class="font-medium text-gray-900 dark:text-white mb-4">Change Password</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                New Password (optional)
              </label>
              <UInput
                v-model="form.password"
                type="password"
                placeholder="Leave blank to keep current password"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Confirm Password
              </label>
              <UInput
                v-model="form.password_confirmation"
                type="password"
                placeholder="Confirm new password"
              />
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
          <UButton variant="ghost" color="gray" @click="navigateTo('/users')">
            Cancel
          </UButton>
          <UButton type="submit" color="primary" :loading="loading">
            <Icon name="heroicons:check" class="w-4 h-4 mr-2" />
            Save Changes
          </UButton>
        </div>
      </form>
    </UCard>
  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({
  layout: false,
  middleware: 'auth'
})

// Define user data type
interface User {
  id: number
  fullname: string
  email: string
  role: string
  status: string
  profile_image?: string
}

// Define API response type
interface UserResponse {
  data: User
}

const route = useRoute()
const router = useRouter()
const toast = useToast()

const loading = ref(false)

const form = ref({
  fullname: '',
  email: '',
  role: '',
  status: '',
  profile_image: '',
  password: '',
  password_confirmation: ''
})

const roleOptions = [
  { label: 'Admin', value: 'Admin' },
  { label: 'Client', value: 'Client' }
]

const statusOptions = [
  { label: 'Active', value: 'Active' },
  { label: 'Inactive', value: 'Inactive' },
  { label: 'Pending', value: 'Pending' }
]

const loadUser = async () => {
  loading.value = true
  try {
    const api = useApi()
    // Get user ID from route params
    const userId = route.params.id as string

    const response = await api.get<UserResponse>(`/users/${userId}`)

    if (response.data) {
      form.value = {
        ...response.data,
        profile_image: response.data.profile_image || '',
        password: '',
        password_confirmation: ''
      }
    }
  } catch (error) {
    toast.add({
      title: 'Error',
      description: 'Failed to load user data',
      color: 'red'
    })
  } finally {
    loading.value = false
  }
}

const handleSubmit = async () => {
  if (form.value.password && form.value.password !== form.value.password_confirmation) {
    toast.add({
      title: 'Error',
      description: 'Passwords do not match',
      color: 'red'
    })
    return
  }

  loading.value = true

  try {
    const api = useApi()
    // Get user ID from route params
    const userId = route.params.id as string

    // Prepare update data
    const updateData: {
      fullname: string
      email: string
      role: string
      status: string
      profile_image: string
      password?: string
    } = {
      fullname: form.value.fullname,
      email: form.value.email,
      role: form.value.role,
      status: form.value.status,
      profile_image: form.value.profile_image
    }

    // Only include password if it's provided
    if (form.value.password) {
      updateData.password = form.value.password
    }

    await api.put(`/users/${userId}`, updateData)

    toast.add({
      title: 'Success',
      description: 'User updated successfully',
      color: 'green'
    })

    navigateTo('/users')
  } catch (error) {
    toast.add({
      title: 'Error',
      description: 'Failed to update user',
      color: 'red'
    })
  } finally {
    loading.value = false
  }
}

// Load user data when component mounts
onMounted(() => {
  loadUser()
})
</script>
