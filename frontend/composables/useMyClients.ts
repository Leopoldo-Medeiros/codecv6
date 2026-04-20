export interface ClientSummary {
  id: number
  fullname: string
  email: string
  profile: {
    profession?: string
    level?: string
    profile_image_url?: string | null
  } | null
  path_count: number
  progress_pct: number
  done_steps: number
  total_steps: number
}

export interface ClientPath {
  id: number
  name: string
  description?: string
  done_count: number
  total_count: number
  steps: Array<{
    id: number
    order: number
    title: string
    type: string
    course: { id: number; name: string } | null
    user_status: 'not_started' | 'in_progress' | 'done'
  }>
}

export interface ClientDetail {
  user: {
    id: number
    fullname: string
    email: string
    profile: {
      profession?: string
      level?: string
      goal?: string
      stack?: string[]
      availability_hours?: number
      timeline?: string
      product_interest?: string
      profile_image_url?: string | null
      linkedin?: string
      github?: string
    } | null
  }
  paths: ClientPath[]
}

export function useMyClients() {
  const api     = useApi()
  const clients = ref<ClientSummary[]>([])
  const loading = ref(false)
  const error   = ref<string | null>(null)

  async function fetchMyClients() {
    loading.value = true
    error.value   = null
    try {
      const res = await api.get<{ data: ClientSummary[] }>('/my-clients')
      clients.value = res.data
      return res.data
    } catch (e: any) {
      error.value = e?.data?.message ?? 'Failed to load clients'
      return []
    } finally {
      loading.value = false
    }
  }

  async function fetchClientDetail(clientId: number): Promise<ClientDetail | null> {
    try {
      return await api.get<ClientDetail>(`/my-clients/${clientId}`)
    } catch (e: any) {
      error.value = e?.data?.message ?? 'Failed to load client'
      return null
    }
  }

  async function assignPath(clientId: number, pathId: number) {
    await api.post(`/my-clients/${clientId}/paths`, { path_id: pathId })
  }

  async function removePath(clientId: number, pathId: number) {
    await api.delete(`/my-clients/${clientId}/paths/${pathId}`)
  }

  return {
    clients: readonly(clients),
    loading: readonly(loading),
    error:   readonly(error),
    fetchMyClients,
    fetchClientDetail,
    assignPath,
    removePath,
  }
}
