<template>
  <NuxtLayout name="admin">
    <!-- Page Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
        Settings
      </h1>
      <p class="text-gray-500 dark:text-gray-400 text-lg">
        Manage your account settings and preferences
      </p>
    </div>

    <!-- Settings Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Profile Settings -->
      <UCard>
        <template #header>
          <h3 class="font-semibold text-gray-900 dark:text-white">Profile Settings</h3>
        </template>
        <div class="space-y-4">
          <div>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
            <UInput v-model="profile.fullname" class="mt-1" />
          </div>
          <div>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
            <UInput v-model="profile.email" type="email" class="mt-1" />
          </div>
          <div>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Bio</label>
            <UTextarea v-model="profile.bio" class="mt-1" />
          </div>
        </div>
      </UCard>

      <!-- Notification Settings -->
      <UCard>
        <template #header>
          <h3 class="font-semibold text-gray-900 dark:text-white">Notifications</h3>
        </template>
        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Email Notifications</label>
            <UToggle v-model="notifications.email" />
          </div>
          <div class="flex items-center justify-between">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Push Notifications</label>
            <UToggle v-model="notifications.push" />
          </div>
          <div class="flex items-center justify-between">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">SMS Notifications</label>
            <UToggle v-model="notifications.sms" />
          </div>
        </div>
      </UCard>

      <!-- Appearance Settings -->
      <UCard>
        <template #header>
          <h3 class="font-semibold text-gray-900 dark:text-white">Appearance</h3>
        </template>
        <div class="space-y-4">
          <div>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Theme</label>
            <USelect v-model="appearance.theme" :options="themeOptions" class="mt-1" />
          </div>
          <div>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Language</label>
            <USelect v-model="appearance.language" :options="languageOptions" class="mt-1" />
          </div>
          <div>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Timezone</label>
            <USelect v-model="appearance.timezone" :options="timezoneOptions" class="mt-1" />
          </div>
        </div>
      </UCard>
    </div>

    <!-- Save Button -->
    <div class="mt-8 flex justify-end">
      <UButton color="primary" @click="saveSettings">
        Save Settings
      </UButton>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({
  layout: false,
  middleware: 'auth'
})

const profile = ref({
  fullname: 'John Doe',
  email: 'john@example.com',
  bio: ''
})

const notifications = ref({
  email: true,
  push: false,
  sms: false
})

const appearance = ref({
  theme: 'light',
  language: 'en',
  timezone: 'UTC'
})

const themeOptions = ['light', 'dark', 'system']
const languageOptions = ['en', 'es', 'fr', 'de', 'pt']
const timezoneOptions = ['UTC', 'EST', 'PST', 'CST', 'MST']

const saveSettings = () => {
  // Save settings logic here
  console.log('Settings saved:', {
    profile: profile.value,
    notifications: notifications.value,
    appearance: appearance.value
  })
}
</script>
