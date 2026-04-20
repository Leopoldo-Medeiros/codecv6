interface Job {
  id: number
  title: string
  description?: string
  company?: string
  location?: string
  salary?: string
  consultant?: {
    id: number
    fullname: string
  }
  created_at: string
  updated_at: string
}

interface JobsResponse {
  data: Job[]
  meta?: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

export const useJobs = () => {
  const api = useApi()
  const jobs = ref<Job[]>([])
  const job = ref<Job | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchJobs = async (params?: { search?: string; page?: number; per_page?: number }) => {
    loading.value = true
    error.value = null

    try {
      const query = new URLSearchParams()
      if (params?.search) query.append('search', params.search)
      if (params?.page) query.append('page', params.page.toString())
      if (params?.per_page) query.append('per_page', params.per_page.toString())

      const queryString = query.toString()
      const endpoint = queryString ? `/jobs?${queryString}` : '/jobs'
      const response = await api.get(endpoint) as JobsResponse
      jobs.value = response.data
      return response
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch jobs'
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchJob = async (id: number) => {
    loading.value = true
    error.value = null

    try {
      const response = await api.get(`/jobs/${id}`) as { data: Job }
      job.value = response.data
      return response.data
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch job'
      throw err
    } finally {
      loading.value = false
    }
  }

  const createJob = async (data: Partial<Job>) => {
    loading.value = true
    error.value = null

    try {
      const response = await api.post('/jobs', data)
      return response
    } catch (err: any) {
      error.value = err.message || 'Failed to create job'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateJob = async (id: number, data: Partial<Job>) => {
    loading.value = true
    error.value = null

    try {
      const response = await api.put(`/jobs/${id}`, data)
      return response
    } catch (err: any) {
      error.value = err.message || 'Failed to update job'
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteJob = async (id: number) => {
    loading.value = true
    error.value = null

    try {
      await api.delete(`/jobs/${id}`)
    } catch (err: any) {
      error.value = err.message || 'Failed to delete job'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    jobs: readonly(jobs),
    job: readonly(job),
    loading: readonly(loading),
    error: readonly(error),
    fetchJobs,
    fetchJob,
    createJob,
    updateJob,
    deleteJob,
  }
}
