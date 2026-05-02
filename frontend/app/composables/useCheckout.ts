export type CheckoutTier = 'accelerator' | 'bootcamp' | 'mentorship'
export type CheckoutCurrency = 'eur' | 'brl'
export type CheckoutStatus = 'pending' | 'paid' | 'failed' | 'refunded'

export interface CheckoutSessionResponse {
    session_id: string
    url: string
}

export interface CheckoutStatusResponse {
    status: CheckoutStatus
    tier: CheckoutTier
    amount: number
    currency: CheckoutCurrency
    paid_at: string | null
}

export const detectCurrency = (): CheckoutCurrency => {
    if (!import.meta.client) return 'eur'
    const lang = navigator.language || ''
    return lang.toLowerCase().startsWith('pt-br') ? 'brl' : 'eur'
}

export const useCheckout = () => {
    const api = useApi()

    const startCheckout = async (
        tier: CheckoutTier,
        currency: CheckoutCurrency = detectCurrency(),
    ): Promise<CheckoutSessionResponse> => {
        return await api.post<CheckoutSessionResponse>('/checkout/session', { tier, currency })
    }

    const getStatus = async (sessionId: string): Promise<CheckoutStatusResponse> => {
        return await api.get<CheckoutStatusResponse>(`/checkout/${sessionId}/status`)
    }

    return {
        startCheckout,
        getStatus,
    }
}
