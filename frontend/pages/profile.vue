<template>
  <NuxtLayout name="admin">

    <!-- Header -->
    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Profile</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your personal information and social links.</p>
      </div>
      <UButton v-if="!editing" icon="i-heroicons-pencil-square" size="sm" @click="startEdit">
        Edit Profile
      </UButton>
      <div v-else class="flex gap-2">
        <UButton size="sm" color="gray" variant="outline" @click="cancelEdit">Cancel</UButton>
        <UButton size="sm" icon="i-heroicons-check" :loading="saving" @click="saveProfile">
          Save Changes
        </UButton>
      </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

      <!-- Left: avatar + social -->
      <div class="flex flex-col gap-5">

        <!-- Avatar card -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 text-center dark:border-gray-700 dark:bg-gray-800">
          <div class="relative mx-auto mb-4 h-24 w-24">
            <UAvatar
              :src="avatarPreview || user?.profile?.profile_image_url || '/images/team-13.jpg'"
              :alt="user?.fullname"
              size="3xl"
              class="ring-4 ring-indigo-100 dark:ring-indigo-900"
            />
            <button
              class="absolute bottom-0 right-0 flex h-7 w-7 items-center justify-center
                     rounded-full border-2 border-white bg-indigo-600 text-white shadow
                     hover:bg-indigo-700 dark:border-gray-800"
              title="Change photo"
              :disabled="uploadingAvatar"
              @click="$refs.avatarInput.click()"
            >
              <UIcon v-if="uploadingAvatar" name="i-heroicons-arrow-path" class="h-3.5 w-3.5 animate-spin" />
              <UIcon v-else name="i-heroicons-camera" class="h-3.5 w-3.5" />
            </button>
            <input
              ref="avatarInput"
              type="file"
              accept="image/jpeg,image/jpg,image/png"
              class="hidden"
              @change="handleAvatarUpload"
            />
          </div>
          <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ user?.fullname }}</h3>
          <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400 capitalize">{{ user?.role }}</p>
          <UBadge color="indigo" variant="subtle" size="xs" class="mt-2">
            {{ user?.email }}
          </UBadge>
        </div>

        <!-- Social links card -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
          <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Social Links</h2>
          </div>
          <div class="flex flex-col divide-y divide-gray-100 dark:divide-gray-700">

            <div v-for="link in socialLinks" :key="link.key"
              class="flex items-center gap-3 px-5 py-3">
              <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg"
                :style="`background:${link.color}18;color:${link.color}`">
                <UIcon :name="link.icon" class="h-4 w-4" />
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ link.label }}</p>
                <template v-if="editing">
                  <input
                    v-model="form[link.key]"
                    type="url"
                    :placeholder="`https://...`"
                    class="mt-0.5 w-full rounded-md border border-gray-200 bg-gray-50 px-2 py-1
                           text-xs text-gray-800 outline-none focus:border-indigo-400
                           dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
                  />
                </template>
                <template v-else>
                  <a v-if="user?.profile?.[link.key]"
                    :href="user.profile[link.key]" target="_blank"
                    class="truncate text-xs text-indigo-600 hover:underline dark:text-indigo-400">
                    {{ user.profile[link.key] }}
                  </a>
                  <p v-else class="text-xs italic text-gray-400 dark:text-gray-500">Not set</p>
                </template>
              </div>
            </div>

          </div>
        </div>

      </div>

      <!-- Right: info + edit form -->
      <div class="lg:col-span-2 flex flex-col gap-5">

        <!-- Personal info -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
          <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Personal Information</h2>
          </div>
          <div class="divide-y divide-gray-100 dark:divide-gray-700">

            <!-- Full name -->
            <div class="flex items-center gap-4 px-5 py-4">
              <UIcon name="i-heroicons-user" class="h-4 w-4 shrink-0 text-gray-400" />
              <div class="min-w-0 flex-1">
                <p class="text-xs text-gray-500 dark:text-gray-400">Full Name</p>
                <input v-if="editing" v-model="form.fullname"
                  type="text"
                  class="mt-1 w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-1.5
                         text-sm text-gray-800 outline-none focus:border-indigo-400
                         dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200" />
                <p v-else class="mt-0.5 text-sm font-medium text-gray-900 dark:text-white">
                  {{ user?.fullname || '—' }}
                </p>
              </div>
            </div>

            <!-- Email -->
            <div class="flex items-center gap-4 px-5 py-4">
              <UIcon name="i-heroicons-envelope" class="h-4 w-4 shrink-0 text-gray-400" />
              <div class="min-w-0 flex-1">
                <p class="text-xs text-gray-500 dark:text-gray-400">Email Address</p>
                <p class="mt-0.5 text-sm font-medium text-gray-900 dark:text-white">
                  {{ user?.email || '—' }}
                </p>
              </div>
              <UBadge color="green" variant="subtle" size="xs">Verified</UBadge>
            </div>

            <!-- Role -->
            <div class="flex items-center gap-4 px-5 py-4">
              <UIcon name="i-heroicons-shield-check" class="h-4 w-4 shrink-0 text-gray-400" />
              <div class="min-w-0 flex-1">
                <p class="text-xs text-gray-500 dark:text-gray-400">Role</p>
                <p class="mt-0.5 text-sm font-medium capitalize text-gray-900 dark:text-white">
                  {{ user?.role || '—' }}
                </p>
              </div>
            </div>

          </div>
        </div>

        <!-- Change password -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
          <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Password</h2>
            <UButton size="xs" color="gray" variant="ghost" icon="i-heroicons-key"
              @click="showPasswordForm = !showPasswordForm">
              Change
            </UButton>
          </div>

          <div v-if="!showPasswordForm" class="px-5 py-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">
              Password last changed: <span class="font-medium text-gray-700 dark:text-gray-300">never</span>
            </p>
          </div>

          <div v-else class="flex flex-col gap-3 px-5 py-4">
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Current Password</label>
              <input v-model="passwordForm.current" type="password"
                class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm
                       outline-none focus:border-indigo-400 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200" />
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">New Password</label>
              <input v-model="passwordForm.new" type="password"
                class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm
                       outline-none focus:border-indigo-400 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200" />
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Confirm New Password</label>
              <input v-model="passwordForm.confirm" type="password"
                class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm
                       outline-none focus:border-indigo-400 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200" />
            </div>
            <div class="flex justify-end gap-2 pt-1">
              <UButton size="sm" color="gray" variant="outline" @click="showPasswordForm = false">Cancel</UButton>
              <UButton size="sm" icon="i-heroicons-lock-closed">Update Password</UButton>
            </div>
          </div>
        </div>

        <!-- Save bar — visible only in edit mode -->
        <div v-if="editing"
          class="flex items-center justify-between rounded-xl border border-indigo-200 bg-indigo-50
                 px-5 py-4 dark:border-indigo-800 dark:bg-indigo-950/40">
          <p class="text-sm text-indigo-700 dark:text-indigo-300">You have unsaved changes.</p>
          <div class="flex gap-2">
            <UButton size="sm" color="gray" variant="outline" @click="cancelEdit">Discard</UButton>
            <UButton size="sm" icon="i-heroicons-check" :loading="saving" @click="saveProfile">
              Save Changes
            </UButton>
          </div>
        </div>

        <!-- Danger zone -->
        <div class="rounded-xl border border-red-100 bg-white dark:border-red-900/30 dark:bg-gray-800">
          <div class="border-b border-red-100 px-5 py-4 dark:border-red-900/30">
            <h2 class="text-sm font-semibold text-red-600 dark:text-red-400">Danger Zone</h2>
          </div>
          <div class="flex items-center justify-between px-5 py-4">
            <div>
              <p class="text-sm font-medium text-gray-900 dark:text-white">Delete Account</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">Permanently delete your account and all data. This cannot be undone.</p>
            </div>
            <UButton size="sm" color="red" variant="outline">Delete</UButton>
          </div>
        </div>

      </div>
    </div>

  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({ layout: false, middleware: 'auth' })

useHead({ title: 'My Profile — CODECV' })

const { user, updateUser } = useAuth()
const toast    = useToast()

const editing          = ref(false)
const saving           = ref(false)
const showPasswordForm = ref(false)
const uploadingAvatar  = ref(false)
const avatarPreview    = ref<string | null>(null)

const { upload, put } = useApi()
const config = useRuntimeConfig()
const storageBase = computed(() => `${config.public.apiBase}/storage`)

const form = reactive({
  fullname:  '',
  github:    '',
  linkedin:  '',
  instagram: '',
  facebook:  '',
  website:   '',
})

const passwordForm = reactive({ current: '', new: '', confirm: '' })

const socialLinks = [
  { key: 'github',    label: 'GitHub',    icon: 'i-heroicons-code-bracket', color: '#1a1a2e' },
  { key: 'linkedin',  label: 'LinkedIn',  icon: 'i-heroicons-briefcase',    color: '#0077b5' },
  { key: 'instagram', label: 'Instagram', icon: 'i-heroicons-camera',       color: '#e1306c' },
  { key: 'facebook',  label: 'Facebook',  icon: 'i-heroicons-user-group',   color: '#1877f2' },
  { key: 'website',   label: 'Website',   icon: 'i-heroicons-globe-alt',    color: '#059669' },
]

function startEdit() {
  form.fullname  = user.value?.fullname  ?? ''
  form.github    = user.value?.profile?.github    ?? ''
  form.linkedin  = user.value?.profile?.linkedin  ?? ''
  form.instagram = user.value?.profile?.instagram ?? ''
  form.facebook  = user.value?.profile?.facebook  ?? ''
  form.website   = user.value?.profile?.website   ?? ''
  editing.value  = true
}

function cancelEdit() {
  editing.value = false
}

async function handleAvatarUpload(event: Event) {
  const file = (event.target as HTMLInputElement).files?.[0]
  if (!file) return

  if (file.size > 2048 * 1024) {
    toast.add({ title: 'Image too large', description: 'Max 2MB allowed.', color: 'red' })
    return
  }

  // Show preview immediately
  avatarPreview.value = URL.createObjectURL(file)

  uploadingAvatar.value = true
  try {
    const formData = new FormData()
    formData.append('avatar', file)
    const res = await upload<{ url: string; path: string }>(`/users/${user.value?.id}/avatar`, formData)

    // Build URL using the working localhost base (lndo.site DNS doesn't resolve locally)
    const imageUrl = `${storageBase.value}/${res.path}`

    // Persist in reactive state + localStorage
    updateUser({
      profile: {
        ...user.value?.profile,
        profile_image_url: imageUrl,
        profile_image:     res.path,
      } as any,
    })

    // Replace blob preview with the real server URL
    avatarPreview.value = imageUrl

    toast.add({ title: 'Photo updated', icon: 'i-heroicons-check-circle', color: 'green' })
  } catch (e: any) {
    avatarPreview.value = null
    const msg = e?.data?.message ?? 'Please try again.'
    toast.add({ title: 'Upload failed', description: msg, color: 'red' })
  } finally {
    uploadingAvatar.value = false
  }
}

async function saveProfile() {
  saving.value = true
  try {
    await put(`/users/${user.value?.id}`, {
      fullname: form.fullname,
      email:    user.value?.email,
      role:     user.value?.role_id ?? user.value?.roles?.[0]?.id ?? 2,
      profile: {
        github:    form.github    || null,
        linkedin:  form.linkedin  || null,
        instagram: form.instagram || null,
        facebook:  form.facebook  || null,
        website:   form.website   || null,
      },
    })
    // Persist in reactive state + localStorage
    updateUser({
      fullname: form.fullname,
      profile: {
        ...user.value?.profile,
        github:    form.github    || null,
        linkedin:  form.linkedin  || null,
        instagram: form.instagram || null,
        facebook:  form.facebook  || null,
        website:   form.website   || null,
      } as any,
    })
    toast.add({ title: 'Profile updated', icon: 'i-heroicons-check-circle', color: 'green' })
    editing.value = false
  } catch (e: any) {
    const msg = e?.data?.message ?? 'Please check your inputs and try again.'
    toast.add({ title: 'Failed to save', description: msg, color: 'red' })
  } finally {
    saving.value = false
  }
}
</script>
