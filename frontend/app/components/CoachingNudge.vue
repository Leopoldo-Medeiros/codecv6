<template>
  <div
    class="overflow-hidden rounded-xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white
           dark:border-emerald-900/60 dark:from-emerald-950/40 dark:to-gray-800"
  >
    <div class="p-5">
      <div class="flex items-start gap-3">
        <div
          class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600
                 dark:bg-emerald-900/60 dark:text-emerald-400"
        >
          <UIcon :name="tierIcon" class="h-5 w-5" />
        </div>
        <div class="min-w-0">
          <p class="text-[11px] font-semibold uppercase tracking-wide text-emerald-600 dark:text-emerald-400">
            Next step · {{ recommendation.name }}
          </p>
          <h3 class="mt-0.5 text-sm font-bold text-gray-900 dark:text-white">
            {{ recommendation.headline }}
          </h3>
        </div>
      </div>

      <p class="mt-3 text-xs leading-relaxed text-gray-600 dark:text-gray-300">
        {{ recommendation.body }}
      </p>

      <div class="mt-4 flex items-center justify-between gap-3">
        <UButton
          color="emerald"
          size="sm"
          trailing-icon="i-heroicons-arrow-right"
          @click="goToPricing"
        >
          {{ recommendation.cta }}
        </UButton>
        <p v-if="priceLabel" class="text-xs font-medium text-gray-500 dark:text-gray-400">
          {{ priceLabel }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { CoachingRecommendation } from '~/composables/useCoaching'
import { detectCurrency } from '~/composables/useCheckout'

const props = defineProps<{
  recommendation: CoachingRecommendation
}>()

const tierIcon = computed(() => {
  switch (props.recommendation.tier) {
    case 'mentorship': return 'i-heroicons-academic-cap'
    case 'bootcamp': return 'i-heroicons-user-group'
    case 'accelerator': return 'i-heroicons-rocket-launch'
    default: return 'i-heroicons-sparkles'
  }
})

// Show the price in the viewer's currency, formatted from minor units.
const priceLabel = computed(() => {
  const currency = detectCurrency()
  const minor = props.recommendation.prices[currency]
  if (minor == null) return null

  const amount = minor / 100
  const symbol = currency === 'brl' ? 'R$' : '€'
  const formatted = Number.isInteger(amount) ? amount.toString() : amount.toFixed(2)
  const isRecurring = props.recommendation.tier === 'mentorship'
  return `${symbol}${formatted}${isRecurring ? '/mo' : ''}`
})

function goToPricing() {
  navigateTo('/pricing#plans')
}
</script>
