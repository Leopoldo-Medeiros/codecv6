export type StepType = 'reading' | 'lab' | 'challenge' | 'quiz'

export interface PathStep {
  id: number
  path_id: number
  title: string
  description?: string
  type: StepType
  lab_url?: string | null
  instructions?: Array<{ id: number; text: string }>
  challenge_prompt?: string | null
  resources?: Array<{ label: string; url: string }>
  order: number
  course?: { id: number; name: string; slug: string } | null
  user_status?: 'not_started' | 'in_progress' | 'done'
  created_at: string
  updated_at: string
}

export interface Path {
  id: number
  name: string
  description?: string
  consultant?: { id: number; fullname: string }
  plans?: Array<{ id: number; name: string }>
  steps?: PathStep[]
  steps_count?: number
  created_at: string
  updated_at: string
}

interface PathsResponse {
  data: Path[]
  meta?: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

export const usePaths = () => {
  const api = useApi()
  const paths = ref<Path[]>([])
  const path = ref<Path | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchPaths = async (params?: { search?: string; page?: number; per_page?: number }) => {
    loading.value = true
    error.value = null
    try {
      const query = new URLSearchParams()
      if (params?.search) query.append('search', params.search)
      if (params?.page) query.append('page', params.page.toString())
      if (params?.per_page) query.append('per_page', params.per_page.toString())
      const endpoint = query.toString() ? `/paths?${query}` : '/paths'
      const response = await api.get(endpoint) as PathsResponse
      paths.value = response.data
      return response
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch paths'
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchPath = async (id: number) => {
    loading.value = true
    error.value = null
    try {
      const response = await api.get(`/paths/${id}`) as { data: Path }
      path.value = response.data
      return response.data
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch path'
      throw err
    } finally {
      loading.value = false
    }
  }

  const createPath = async (data: Partial<Path> & { plan_ids?: number[] }) => {
    loading.value = true
    error.value = null
    try {
      return await api.post('/paths', data)
    } catch (err: any) {
      error.value = err.message || 'Failed to create path'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updatePath = async (id: number, data: Partial<Path> & { plan_ids?: number[] }) => {
    loading.value = true
    error.value = null
    try {
      return await api.put(`/paths/${id}`, data)
    } catch (err: any) {
      error.value = err.message || 'Failed to update path'
      throw err
    } finally {
      loading.value = false
    }
  }

  const deletePath = async (id: number) => {
    loading.value = true
    error.value = null
    try {
      await api.delete(`/paths/${id}`)
    } catch (err: any) {
      error.value = err.message || 'Failed to delete path'
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchMyPaths = async (): Promise<Path[]> => {
    loading.value = true
    error.value = null
    try {
      const response = await api.get('/my-paths') as { data: Path[] }
      paths.value = response.data
      return response.data
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch your paths'
      throw err
    } finally {
      loading.value = false
    }
  }

  // ── Step methods ───────────────────────────────────────────

  const fetchSteps = async (pathId: number): Promise<PathStep[]> => {
    const res = await api.get(`/paths/${pathId}/steps`) as { data: PathStep[] }
    return res.data
  }

  const fetchStep = async (stepId: number): Promise<PathStep & { user_status: string }> => {
    const res = await api.get(`/path-steps/${stepId}`) as { data: PathStep & { user_status: string } }
    return res.data
  }

  const createStep = async (pathId: number, data: Partial<PathStep>) => {
    return api.post(`/paths/${pathId}/steps`, data) as Promise<{ data: PathStep }>
  }

  const updateStep = async (pathId: number, stepId: number, data: Partial<PathStep>) => {
    return api.put(`/paths/${pathId}/steps/${stepId}`, data) as Promise<{ data: PathStep }>
  }

  const deleteStep = async (pathId: number, stepId: number) => {
    return api.delete(`/paths/${pathId}/steps/${stepId}`)
  }

  const reorderSteps = async (pathId: number, ids: number[]) => {
    return api.post(`/paths/${pathId}/steps/reorder`, { ids })
  }

  const updateStepProgress = async (stepId: number, status: PathStep['user_status']) => {
    return api.put(`/path-steps/${stepId}/progress`, { status })
  }

  return {
    paths: readonly(paths),
    path: readonly(path),
    loading: readonly(loading),
    error: readonly(error),
    fetchPaths,
    fetchMyPaths,
    fetchPath,
    createPath,
    updatePath,
    deletePath,
    fetchSteps,
    fetchStep,
    createStep,
    updateStep,
    deleteStep,
    reorderSteps,
    updateStepProgress,
  }
}
