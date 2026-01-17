interface User {
  id: number
  fullname: string
  email: string
  role: string
  profile?: {
    birth_date?: string
    profession?: string
    profile_image?: string
    profile_image_url?: string
    website?: string
    github?: string
    linkedin?: string
    instagram?: string
    facebook?: string
  }
  created_at: string
  updated_at: string
}

interface UsersResponse {
  data: User[]
  meta?: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

export const useUsers = () => {
  const api = useApi()
  const users = ref<User[]>([])
  const user = ref<User | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchUsers = async (params?: { search?: string; page?: number; per_page?: number }) => {
    loading.value = true
    error.value = null

    try {
      const query = new URLSearchParams()
      if (params?.search) query.append('search', params.search)
      if (params?.page) query.append('page', params.page.toString())
      if (params?.per_page) query.append('per_page', params.per_page.toString())

      const queryString = query.toString()
      const endpoint = queryString ? `/users?${queryString}` : '/users'
      const response = await api.get(endpoint) as UsersResponse
      users.value = response.data
      return response
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch users'
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchUser = async (id: number) => {
    loading.value = true
    error.value = null

    try {
      const response = await api.get(`/users/${id}`) as { data: User }
      user.value = response.data
      return response.data
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch user'
      throw err
    } finally {
      loading.value = false
    }
  }

  const createUser = async (data: Partial<User> & { password?: string; password_confirmation?: string; role?: number }) => {
    loading.value = true
    error.value = null

    try {
      const response = await api.post('/users', data)
      return response
    } catch (err: any) {
      error.value = err.message || 'Failed to create user'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateUser = async (id: number, data: Partial<User>) => {
    loading.value = true
    error.value = null

    try {
      const response = await api.put(`/users/${id}`, data)
      return response
    } catch (err: any) {
      error.value = err.message || 'Failed to update user'
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteUser = async (id: number) => {
    loading.value = true
    error.value = null

    try {
      await api.delete(`/users/${id}`)
    } catch (err: any) {
      error.value = err.message || 'Failed to delete user'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    users: readonly(users),
    user: readonly(user),
    loading: readonly(loading),
    error: readonly(error),
    fetchUsers,
    fetchUser,
    createUser,
    updateUser,
    deleteUser,
  }
}
