interface Plan {
  id: number
  name: string
  description?: string
  price?: number
  consultant?: {
    id: number
    fullname: string
  }
  clients?: Array<{
    id: number
    fullname: string
  }>
  paths?: Array<{
    id: number
    name: string
  }>
  created_at: string
  updated_at: string
}

interface PlansResponse {
  data: Plan[]
  meta?: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

export const usePlans = () => {
  const api = useApi()
  const plans = ref<Plan[]>([])
  const plan = ref<Plan | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchPlans = async (params?: { search?: string; page?: number; per_page?: number }) => {
    loading.value = true
    error.value = null

    try {
      const query = new URLSearchParams()
      if (params?.search) query.append('search', params.search)
      if (params?.page) query.append('page', params.page.toString())
      if (params?.per_page) query.append('per_page', params.per_page.toString())

      const queryString = query.toString()
      const endpoint = queryString ? `/plans?${queryString}` : '/plans'
      const response = await api.get(endpoint) as PlansResponse
      plans.value = response.data
      return response
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch plans'
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchPlan = async (id: number) => {
    loading.value = true
    error.value = null

    try {
      const response = await api.get(`/plans/${id}`) as { data: Plan }
      plan.value = response.data
      return response.data
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch plan'
      throw err
    } finally {
      loading.value = false
    }
  }

  const createPlan = async (data: Partial<Plan> & { client_ids?: number[]; path_ids?: number[] }) => {
    loading.value = true
    error.value = null

    try {
      const response = await api.post('/plans', data)
      return response
    } catch (err: any) {
      error.value = err.message || 'Failed to create plan'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updatePlan = async (id: number, data: Partial<Plan> & { client_ids?: number[]; path_ids?: number[] }) => {
    loading.value = true
    error.value = null

    try {
      const response = await api.put(`/plans/${id}`, data)
      return response
    } catch (err: any) {
      error.value = err.message || 'Failed to update plan'
      throw err
    } finally {
      loading.value = false
    }
  }

  const deletePlan = async (id: number) => {
    loading.value = true
    error.value = null

    try {
      await api.delete(`/plans/${id}`)
    } catch (err: any) {
      error.value = err.message || 'Failed to delete plan'
      throw err
    } finally {
      loading.value = false
    }
  }

  const attachClient = async (planId: number, clientId: number) => {
    loading.value = true
    error.value = null

    try {
      await api.post(`/plans/${planId}/clients`, { client_id: clientId })
    } catch (err: any) {
      error.value = err.message || 'Failed to attach client'
      throw err
    } finally {
      loading.value = false
    }
  }

  const detachClient = async (planId: number, clientId: number) => {
    loading.value = true
    error.value = null

    try {
      await api.delete(`/plans/${planId}/clients?client_id=${clientId}`)
    } catch (err: any) {
      error.value = err.message || 'Failed to detach client'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    plans: readonly(plans),
    plan: readonly(plan),
    loading: readonly(loading),
    error: readonly(error),
    fetchPlans,
    fetchPlan,
    createPlan,
    updatePlan,
    deletePlan,
    attachClient,
    detachClient,
  }
}
