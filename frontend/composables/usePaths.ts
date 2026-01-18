interface Path {
  id: number
  name: string
  description?: string
  consultant?: {
    id: number
    fullname: string
  }
  plans?: Array<{
    id: number
    name: string
  }>
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

      const queryString = query.toString()
      const endpoint = queryString ? `/paths?${queryString}` : '/paths'
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
      const response = await api.post('/paths', data)
      return response
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
      const response = await api.put(`/paths/${id}`, data)
      return response
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

  return {
    paths: readonly(paths),
    path: readonly(path),
    loading: readonly(loading),
    error: readonly(error),
    fetchPaths,
    fetchPath,
    createPath,
    updatePath,
    deletePath,
  }
}
