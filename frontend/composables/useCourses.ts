interface Course {
  id: number
  name: string
  slug: string
  description?: string
  user?: {
    id: number
    fullname: string
  }
  created_at: string
  updated_at: string
}

interface CoursesResponse {
  data: Course[]
  meta?: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

export const useCourses = () => {
  const api = useApi()
  const courses = ref<Course[]>([])
  const course = ref<Course | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchCourses = async (params?: { search?: string; page?: number; per_page?: number }) => {
    loading.value = true
    error.value = null

    try {
      const query = new URLSearchParams()
      if (params?.search) query.append('search', params.search)
      if (params?.page) query.append('page', params.page.toString())
      if (params?.per_page) query.append('per_page', params.per_page.toString())

      const queryString = query.toString()
      const endpoint = queryString ? `/courses?${queryString}` : '/courses'
      const response = await api.get(endpoint) as CoursesResponse
      courses.value = response.data
      return response
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch courses'
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchCourse = async (id: number) => {
    loading.value = true
    error.value = null

    try {
      const response = await api.get(`/courses/${id}`) as { data: Course }
      course.value = response.data
      return response.data
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch course'
      throw err
    } finally {
      loading.value = false
    }
  }

  const createCourse = async (data: Partial<Course>) => {
    loading.value = true
    error.value = null

    try {
      const response = await api.post('/courses', data)
      return response
    } catch (err: any) {
      error.value = err.message || 'Failed to create course'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateCourse = async (id: number, data: Partial<Course>) => {
    loading.value = true
    error.value = null

    try {
      const response = await api.put(`/courses/${id}`, data)
      return response
    } catch (err: any) {
      error.value = err.message || 'Failed to update course'
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteCourse = async (id: number) => {
    loading.value = true
    error.value = null

    try {
      await api.delete(`/courses/${id}`)
    } catch (err: any) {
      error.value = err.message || 'Failed to delete course'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    courses: readonly(courses),
    course: readonly(course),
    loading: readonly(loading),
    error: readonly(error),
    fetchCourses,
    fetchCourse,
    createCourse,
    updateCourse,
    deleteCourse,
  }
}
