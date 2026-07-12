<template>
  <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
    <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
      <h2 class="text-sm font-semibold text-gray-900 dark:text-white">My Practice</h2>
      <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">XP, streak, and badges</p>
    </div>

    <div class="p-5">
      <!-- Loading skeleton -->
      <div v-if="loading" class="space-y-3">
        <div class="h-8 animate-pulse rounded-md bg-gray-200 dark:bg-gray-700" />
        <div class="h-8 animate-pulse rounded-md bg-gray-200 dark:bg-gray-700" />
      </div>

      <template v-else-if="progress">
        <!-- XP + streak -->
        <div class="grid grid-cols-2 gap-3">
          <div class="rounded-lg border border-gray-100 p-3 text-center dark:border-gray-700">
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
              {{ progress.xp_points }}
            </p>
            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">XP earned</p>
          </div>
          <div class="rounded-lg border border-gray-100 p-3 text-center dark:border-gray-700">
            <p class="text-2xl font-bold text-orange-500">
              🔥 {{ progress.current_streak }}
            </p>
            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
              day streak <span v-if="progress.longest_streak > progress.current_streak">(best: {{ progress.longest_streak }})</span>
            </p>
          </div>
        </div>

        <!-- Badges -->
        <div v-if="progress.badges.length" class="mt-4">
          <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Badges</p>
          <div class="flex flex-wrap gap-2">
            <UTooltip v-for="badge in progress.badges" :key="badge.key" :text="`${badge.name} — ${badge.description}`">
              <span
                class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 px-2.5 py-1 text-xs
                       font-medium text-gray-700 dark:border-gray-600 dark:text-gray-300"
              >
                <span>{{ badge.icon }}</span>{{ badge.name }}
              </span>
            </UTooltip>
          </div>
        </div>
        <p v-else class="mt-4 text-xs text-gray-500 dark:text-gray-400">
          Complete your first challenge to earn a badge.
        </p>

        <!-- Public skill profile -->
        <div class="mt-4 border-t border-gray-100 pt-4 dark:border-gray-700">
          <div class="flex items-center justify-between gap-3">
            <div>
              <p class="text-sm font-medium text-gray-900 dark:text-white">Public skill profile</p>
              <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                Share your practice history with recruiters
              </p>
            </div>
            <UToggle :model-value="isPublic" @update:model-value="onToggle" />
          </div>

          <div v-if="isPublic && publicSlug" class="mt-3 flex items-center gap-2">
            <code class="flex-1 truncate rounded-md bg-gray-50 px-2.5 py-1.5 text-xs text-gray-600 dark:bg-gray-900 dark:text-gray-300">
              {{ profileUrl }}
            </code>
            <UButton
              size="xs"
              variant="soft"
              :icon="copied ? 'i-heroicons-check' : 'i-heroicons-clipboard'"
              @click="copyLink"
            >
              {{ copied ? 'Copied' : 'Copy' }}
            </UButton>
          </div>
        </div>
      </template>

      <p v-else-if="error" class="text-xs text-red-500">{{ error }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
const { progress, isPublic, publicSlug, loading, error, fetchProgress, setProfileVisibility } = usePracticeProgress()

const copied = ref(false)

const profileUrl = computed(() =>
  publicSlug.value ? `${window.location.origin}/u/${publicSlug.value}` : '',
)

async function onToggle(visible: boolean) {
  await setProfileVisibility(visible)
}

async function copyLink() {
  if (!profileUrl.value) return
  await navigator.clipboard.writeText(profileUrl.value)
  copied.value = true
  setTimeout(() => { copied.value = false }, 2000)
}

onMounted(fetchProgress)
</script>
