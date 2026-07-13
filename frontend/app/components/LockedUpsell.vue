<template>
  <div
    class="rounded-xl border border-amber-200 bg-amber-50 p-5 text-center
           dark:border-amber-900/40 dark:bg-amber-950/20"
  >
    <div
      class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-full
             bg-amber-100 dark:bg-amber-900/40"
    >
      <UIcon name="i-heroicons-lock-closed" class="h-5 w-5 text-amber-600 dark:text-amber-400" />
    </div>
    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ title }}</p>
    <p class="mx-auto mt-1 max-w-sm text-xs leading-relaxed text-gray-500 dark:text-gray-400">
      {{ subtitle }}
    </p>
    <UButton
      class="mt-4"
      color="amber"
      :size="compact ? 'xs' : 'sm'"
      icon="i-heroicons-sparkles"
      @click="navigateTo('/pricing#plans')"
    >
      Unlock with Practice Pro
    </UButton>
  </div>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
  title?: string
  subtitle?: string
  compact?: boolean
}>(), {
  title: 'This is a Practice Pro exercise',
  subtitle: 'Upgrade to unlock the full challenge and learning-path library — €12/month, cancel anytime.',
  compact: false,
})
</script>
