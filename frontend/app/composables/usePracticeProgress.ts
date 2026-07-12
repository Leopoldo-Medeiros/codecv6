export interface EarnedBadge {
    key: string
    name: string
    description: string
    icon: string
    earned_at: string
}

export interface PracticeProgress {
    xp_points: number
    current_streak: number
    longest_streak: number
    last_practiced_at: string | null
    badges: EarnedBadge[]
}

/**
 * Gamification snapshot (practice funnel F1) + public skill profile
 * visibility (F3) for the authenticated user.
 */
export const usePracticeProgress = () => {
    const progress = ref<PracticeProgress | null>(null)
    const isPublic = ref(false)
    const publicSlug = ref<string | null>(null)
    const loading = ref(false)
    const error = ref<string | null>(null)

    async function fetchProgress() {
        loading.value = true
        error.value = null
        try {
            const [progressData, me] = await Promise.all([
                useApi().get<PracticeProgress>('/me/progress'),
                useApi().get<{ user: { profile: { is_public?: boolean, public_slug?: string | null } | null } }>('/me'),
            ])
            progress.value = progressData
            isPublic.value = me.user.profile?.is_public ?? false
            publicSlug.value = me.user.profile?.public_slug ?? null
        } catch (e: unknown) {
            error.value = (e as Error).message
        } finally {
            loading.value = false
        }
    }

    async function setProfileVisibility(visible: boolean) {
        error.value = null
        try {
            const response = await useApi().patch<{ is_public: boolean, public_slug: string | null }>(
                '/me/public-profile',
                { is_public: visible },
            )
            isPublic.value = response.is_public
            publicSlug.value = response.public_slug
        } catch (e: unknown) {
            error.value = (e as Error).message
        }
    }

    return {
        progress: readonly(progress),
        isPublic: readonly(isPublic),
        publicSlug: readonly(publicSlug),
        loading: readonly(loading),
        error: readonly(error),
        fetchProgress,
        setProfileVisibility,
    }
}
