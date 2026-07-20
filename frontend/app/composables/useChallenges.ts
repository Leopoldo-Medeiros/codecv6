import type { Challenge } from '~/types/models'

export const useChallenges = () => {
  const challenges = ref<Challenge[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const api = useApi()

  const fetchChallenges = async (): Promise<Challenge[]> => {
    loading.value = true
    error.value = null
    try {
      const res = await api.get('/challenges') as { data: Challenge[] }
      challenges.value = res.data
      return res.data
    } catch (e: unknown) {
      error.value = (e as Error).message || 'Failed to fetch challenges'
      throw e
    } finally {
      loading.value = false
    }
  }

  const fetchChallenge = async (slug: string): Promise<Challenge> => {
    const res = await api.get(`/challenges/${slug}`) as { data: Challenge }
    return res.data
  }

  return {
    challenges: readonly(challenges),
    loading: readonly(loading),
    error: readonly(error),
    fetchChallenges,
    fetchChallenge,
  }
}
