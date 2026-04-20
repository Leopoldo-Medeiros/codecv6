<template>
  <nav class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-50">
    <div class="max-w-5xl mx-auto px-6 h-16 flex items-center justify-between">
      <!-- Logo -->
      <NuxtLink to="/" class="flex items-center gap-2">
        <img src="/images/codecv.png" alt="CodeCV" class="h-8 w-auto" onerror="this.style.display='none'" />
        <span class="font-bold text-gray-900 dark:text-white text-lg">CODECV</span>
      </NuxtLink>

      <!-- Desktop nav -->
      <div class="hidden sm:flex items-center gap-6">
        <NuxtLink
          v-for="link in navLinks"
          :key="link.to"
          :to="link.to"
          class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors"
        >
          {{ link.label }}
        </NuxtLink>
      </div>

      <!-- Auth buttons -->
      <div class="hidden sm:flex items-center gap-3">
        <template v-if="isAuthenticated">
          <UButton to="/dashboard" variant="ghost" color="gray" size="sm">Dashboard</UButton>
          <UButton @click="logout" variant="ghost" color="red" size="sm">Logout</UButton>
        </template>
        <template v-else>
          <UButton to="/login" variant="ghost" color="gray" size="sm">Sign In</UButton>
          <UButton to="/pricing" color="primary" size="sm">Get Started</UButton>
        </template>
      </div>

      <!-- Mobile menu button -->
      <UButton
        class="sm:hidden"
        :icon="mobileOpen ? 'i-heroicons-x-mark' : 'i-heroicons-bars-3'"
        variant="ghost"
        color="gray"
        @click="mobileOpen = !mobileOpen"
      />
    </div>

    <!-- Mobile menu -->
    <div v-if="mobileOpen" class="sm:hidden border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-6 py-4 space-y-3">
      <NuxtLink
        v-for="link in navLinks"
        :key="link.to"
        :to="link.to"
        class="block text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white py-1"
        @click="mobileOpen = false"
      >
        {{ link.label }}
      </NuxtLink>
      <div class="pt-3 border-t border-gray-200 dark:border-gray-700 flex flex-col gap-2">
        <template v-if="isAuthenticated">
          <UButton to="/dashboard" variant="outline" size="sm" block @click="mobileOpen = false">Dashboard</UButton>
          <UButton @click="logout" color="red" variant="ghost" size="sm" block>Logout</UButton>
        </template>
        <template v-else>
          <UButton to="/login" variant="outline" size="sm" block @click="mobileOpen = false">Sign In</UButton>
          <UButton to="/pricing" color="primary" size="sm" block @click="mobileOpen = false">Get Started</UButton>
        </template>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
const { isAuthenticated, logout } = useAuth()
const mobileOpen = ref(false)

const navLinks = [
  { label: 'About', to: '/about' },
  { label: 'Pricing', to: '/pricing' },
  { label: 'FAQs', to: '/faqs' },
]
</script>
