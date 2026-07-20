<template>
  <NuxtLayout name="admin">

    <!-- Full-screen workspace; the layout stays mounted underneath -->
    <div v-if="!pending && challenge" class="fixed inset-0 z-50">
      <ChallengeEditor
        :challenge="challenge"
        @back="navigateTo('/challenges')"
        @completed="onCompleted"
      />
    </div>

    <div v-if="pending" class="flex justify-center py-20">
      <UIcon name="i-heroicons-arrow-path" class="animate-spin text-3xl text-emerald-500" />
    </div>

    <!-- F4 gate: 403 → upsell instead of a generic error -->
    <div v-else-if="locked" class="mx-auto max-w-md py-20">
      <LockedUpsell subtitle="This challenge is part of the Practice Pro library. Upgrade to unlock the full catalog — €29/month, cancel anytime." />
      <div class="mt-4 text-center">
        <UButton color="gray" variant="ghost" size="sm" @click="navigateTo('/challenges')">Back to Challenges</UButton>
      </div>
    </div>

    <div v-else-if="!challenge" class="py-20 text-center">
      <p class="text-sm text-gray-500 dark:text-gray-400">Challenge not found.</p>
      <UButton class="mt-4" color="emerald" variant="ghost" @click="navigateTo('/challenges')">Back to Challenges</UButton>
    </div>

    <!-- Solved celebration (standalone play — XP comes from the run itself) -->
    <CompletionCelebration
      v-if="celebration"
      :progress="celebration"
      heading="Challenge solved!"
      exit-label="Back to challenges"
      @close="celebration = null"
      @exit="celebration = null; navigateTo('/challenges')"
    />

  </NuxtLayout>
</template>

<script setup lang="ts">
import type { Challenge } from '~/types/models'

definePageMeta({ layout: false, middleware: 'auth', key: route => route.fullPath })

const route = useRoute()
const { fetchChallenge } = useChallenges()
const focus = useFocusMode()

const challenge = ref<Challenge | null>(null)
const pending = ref(true)
const locked = ref(false)
const celebration = ref<{
  xp_awarded: number
  xp_points: number
  current_streak: number
  new_badges: Array<{ key: string; name: string; description: string; icon: string }>
} | null>(null)

onBeforeUnmount(() => { focus.value = false })

onMounted(async () => {
  try {
    challenge.value = await fetchChallenge(String(route.params.slug))
    focus.value = true
    useHead({ title: `${challenge.value.title} — CODECV` })
  } catch (err: unknown) {
    if ((err as { response?: { status?: number } })?.response?.status === 403) {
      locked.value = true
    }
  } finally {
    pending.value = false
  }
})

function onCompleted(_challenge: Challenge, progress: typeof celebration.value) {
  // progress is null on repeat solves (XP is first-pass only) — no fanfare.
  if (progress) celebration.value = progress
}
</script>
