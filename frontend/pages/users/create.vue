<template>
  <NuxtLayout name="admin">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
        <NuxtLink to="/users" class="hover:text-primary">Users</NuxtLink>
        <Icon name="heroicons:chevron-right" class="w-4 h-4" />
        <span class="text-gray-900 dark:text-white">Create User</span>
      </div>
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Create New User</h1>
      <p class="text-gray-500 dark:text-gray-400">Add a new user to the platform</p>
    </div>

    <!-- Form -->
    <UCard>
      <template #header>
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">User Information</h3>
          <UButton variant="ghost" color="gray" @click="navigateTo('/users')">
            <Icon name="heroicons:x-mark" class="w-4 h-4 mr-1" /> Cancel
          </UButton>
        </div>
      </template>

      <!-- API error banner -->
      <UAlert
        v-if="apiError"
        color="red"
        variant="subtle"
        class="mb-6"
        title="Error"
        :description="apiError"
      />

      <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Basic info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Full Name <span class="text-red-500">*</span>
            </label>
            <UInput v-model="form.fullname" placeholder="Enter full name" />
            <p v-if="errors.fullname" class="text-red-500 text-sm mt-1">{{ errors.fullname }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Email Address <span class="text-red-500">*</span>
            </label>
            <UInput v-model="form.email" type="email" placeholder="Enter email" />
            <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Role <span class="text-red-500">*</span>
            </label>
            <USelectMenu
              v-model="selectedRole"
              :options="roleOptions"
              option-attribute="label"
              placeholder="Select role"
            />
            <p v-if="errors.role" class="text-red-500 text-sm mt-1">{{ errors.role }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Profession
            </label>
            <UInput v-model="form.profession" placeholder="e.g. Software Engineer" />
          </div>
        </div>

        <!-- Password -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
          <h4 class="font-medium text-gray-900 dark:text-white mb-4">Password</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Password <span class="text-red-500">*</span>
              </label>
              <UInput v-model="form.password" type="password" placeholder="Min 8 chars, upper+lower+number" />
              <p v-if="errors.password" class="text-red-500 text-sm mt-1">{{ errors.password }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Confirm Password <span class="text-red-500">*</span>
              </label>
              <UInput v-model="form.password_confirmation" type="password" placeholder="Repeat password" />
              <p v-if="errors.password_confirmation" class="text-red-500 text-sm mt-1">{{ errors.password_confirmation }}</p>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
          <UButton variant="ghost" color="gray" @click="navigateTo('/users')">Cancel</UButton>
          <UButton type="submit" color="primary" :loading="loading">
            <Icon name="heroicons:user-plus" class="w-4 h-4 mr-2" />
            Create User
          </UButton>
        </div>
      </form>
    </UCard>
  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({ layout: false, middleware: 'auth' })

const api = useApi()
const toast = useToast()

const loading = ref(false)
const apiError = ref<string | null>(null)
const errors = ref<Record<string, string>>({})

interface RoleOption { label: string; value: number }
const roleOptions = ref<RoleOption[]>([])
const selectedRole = ref<RoleOption | null>(null)

const form = ref({
  fullname: '',
  email: '',
  password: '',
  password_confirmation: '',
  profession: '',
})

// Load roles from API on mount
onMounted(async () => {
  try {
    const res = await api.get<{ roles: { id: number; name: string }[] }>('/roles')
    roleOptions.value = res.roles.map(r => ({
      label: r.name.charAt(0).toUpperCase() + r.name.slice(1),
      value: r.id,
    }))
  } catch {
    toast.add({ title: 'Warning', description: 'Could not load roles', color: 'yellow' })
  }
})

const handleSubmit = async () => {
  apiError.value = null
  errors.value = {}

  // Client-side validation
  if (!form.value.fullname) errors.value.fullname = 'Full name is required'
  if (!form.value.email) errors.value.email = 'Email is required'
  if (!selectedRole.value) errors.value.role = 'Role is required'
  if (!form.value.password) errors.value.password = 'Password is required'
  if (!form.value.password_confirmation) errors.value.password_confirmation = 'Please confirm the password'
  if (form.value.password && form.value.password_confirmation && form.value.password !== form.value.password_confirmation) {
    errors.value.password_confirmation = 'Passwords do not match'
  }

  if (Object.keys(errors.value).length) return

  loading.value = true

  try {
    await api.post('/users', {
      fullname: form.value.fullname,
      email: form.value.email,
      password: form.value.password,
      password_confirmation: form.value.password_confirmation,
      role: selectedRole.value!.value,
      profile: { profession: form.value.profession },
    })

    toast.add({ title: 'Success', description: 'User created successfully', color: 'green' })
    navigateTo('/users')
  } catch (err: any) {
    // Surface Laravel validation errors field by field
    const data = err?.data ?? err?.response?._data
    if (data?.errors) {
      Object.entries(data.errors as Record<string, string[]>).forEach(([field, messages]) => {
        const key = field.replace('profile.', '')
        errors.value[key] = (messages as string[])[0]
      })
      apiError.value = data.message ?? 'Please fix the errors below.'
    } else {
      apiError.value = data?.message ?? 'Failed to create user. Please try again.'
    }
  } finally {
    loading.value = false
  }
}

useHead({ title: 'Create User' })
</script>
