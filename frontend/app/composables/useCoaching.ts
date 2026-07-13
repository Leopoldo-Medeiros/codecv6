import type { CheckoutTier, CheckoutCurrency } from './useCheckout'

export interface CoachingRecommendation {
    tier: CheckoutTier
    name: string
    headline: string
    body: string
    cta: string
    prices: Partial<Record<CheckoutCurrency, number>>
}

/**
 * Practice funnel F6 — the coaching upsell nudge for the current user.
 * The backend (CoachingRecommendationService) decides whether the user has
 * earned a nudge and which tier fits; the frontend just renders it.
 */
export const useCoaching = () => {
    const recommendation = ref<CoachingRecommendation | null>(null)
    const loading = ref(false)
    const error = ref<string | null>(null)

    async function fetchRecommendation() {
        loading.value = true
        error.value = null
        try {
            const res = await useApi().get<{ recommendation: CoachingRecommendation | null }>(
                '/me/coaching-recommendation',
            )
            recommendation.value = res.recommendation
        } catch (e: unknown) {
            error.value = (e as Error).message
        } finally {
            loading.value = false
        }
    }

    return {
        recommendation: readonly(recommendation),
        loading: readonly(loading),
        error: readonly(error),
        fetchRecommendation,
    }
}
