import type { User, LoginResponse } from '~/types/models'

const STORAGE_KEYS = {
    TOKEN: 'auth_token',
    USER: 'auth_user'
} as const

const loadUserFromStorage = (): User | null => {
    if (import.meta.client) {
        const userData = localStorage.getItem(STORAGE_KEYS.USER)
        return userData ? JSON.parse(userData) : null
    }
    return null
}

export const useAuth = () => {
    const config = useRuntimeConfig()
    const baseURL = config.public.apiBase as string
    const router = useRouter()

    const user = useState<User | null>('user', () => loadUserFromStorage())

    const isAuthenticated = computed(() => Boolean(user.value))
    const isAdmin = computed(() => user.value?.role === 'admin')
    const isClient = computed(() => user.value?.role === 'client')
    const isConsultant = computed(() => user.value?.role === 'consultant')

    onMounted(() => {
        if (import.meta.client) {
            const storedUser = loadUserFromStorage()
            if (storedUser) {
                user.value = storedUser
            }
        }
    })

    const fetchCsrfToken = async (): Promise<string | undefined> => {
        await $fetch('/sanctum/csrf-cookie', {
            baseURL,
            credentials: 'include',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        })

        if (import.meta.client) {
            return document.cookie
                .split('; ')
                .find(row => row.startsWith('XSRF-TOKEN='))
                ?.split('=')[1]
        }
        return undefined
    }

    const login = async (email: string, password: string): Promise<{ success: true; user: User } | { success: false; error: string }> => {
        try {
            const token = await fetchCsrfToken()

            const response = await $fetch<LoginResponse>('/api/login', {
                baseURL,
                method: 'POST',
                body: { email, password },
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(token ? { 'X-XSRF-TOKEN': decodeURIComponent(token) } : {}),
                },
            })

            if (response.user) {
                user.value = response.user
                if (import.meta.client) {
                    localStorage.setItem(STORAGE_KEYS.USER, JSON.stringify(response.user))
                    if (response.access_token) {
                        localStorage.setItem(STORAGE_KEYS.TOKEN, response.access_token)
                    }
                }
            }

            return { success: true, user: response.user }
        } catch (error: unknown) {
            const err = error as { response?: { _data?: { message?: string } }; message?: string }
            return {
                success: false,
                error: err.response?._data?.message || err.message || 'Login failed',
            }
        }
    }

    const logout = async (): Promise<void> => {
        try {
            const token = await fetchCsrfToken()

            await $fetch('/api/logout', {
                baseURL,
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(token ? { 'X-XSRF-TOKEN': decodeURIComponent(token) } : {}),
                },
            })
        } catch (error) {
            console.error('Error during logout:', error)
        } finally {
            user.value = null
            if (import.meta.client) {
                localStorage.removeItem(STORAGE_KEYS.USER)
                localStorage.removeItem(STORAGE_KEYS.TOKEN)
            }
            await router.push('/login')
        }
    }

    return {
        user: readonly(user),
        isAuthenticated,
        isAdmin,
        isClient,
        isConsultant,
        login,
        logout,
    }
}
