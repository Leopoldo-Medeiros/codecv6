export const useApi = () => {
    const config = useRuntimeConfig()
    const baseURL = `${config.public.apiBase as string}/api`

    const getCsrfToken = (): string | undefined => {
        if (!import.meta.client) return undefined
        const match = document.cookie
            .split('; ')
            .find(row => row.startsWith('XSRF-TOKEN='))
        return match ? decodeURIComponent(match.split('=')[1]) : undefined
    }

    const getAuthToken = (): string | undefined => {
        if (!import.meta.client) return undefined
        return localStorage.getItem('auth_token') || undefined
    }

    const apiFetch = async <T>(
        endpoint: string,
        method: string,
        body?: unknown,
    ): Promise<T> => {
        const isFormData = body instanceof FormData

        const headers: Record<string, string> = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        }

        // Only set JSON content-type for non-FormData bodies
        // (browser sets multipart/form-data + boundary automatically for FormData)
        if (!isFormData) {
            headers['Content-Type'] = 'application/json'
        }

        // Add CSRF token
        const csrfToken = getCsrfToken()
        if (csrfToken) {
            headers['X-XSRF-TOKEN'] = csrfToken
        }

        // Add authentication token
        const authToken = getAuthToken()
        if (authToken) {
            headers['Authorization'] = `Bearer ${authToken}`
        }

        const controller = new AbortController()
        const timer = setTimeout(() => controller.abort(), 90000)

        try {
            return await $fetch<T>(endpoint, {
                baseURL,
                // eslint-disable-next-line @typescript-eslint/no-explicit-any
                method: method as any,
                body: body !== undefined ? body : undefined,
                credentials: 'include',
                headers,
                signal: controller.signal,
            })
        } finally {
            clearTimeout(timer)
        }
    }

    return {
        get: <T>(endpoint: string) => apiFetch<T>(endpoint, 'GET'),
        post: <T>(endpoint: string, body: unknown) => apiFetch<T>(endpoint, 'POST', body),
        put: <T>(endpoint: string, body: unknown) => apiFetch<T>(endpoint, 'PUT', body),
        patch: <T>(endpoint: string, body: unknown) => apiFetch<T>(endpoint, 'PATCH', body),
        delete: <T>(endpoint: string) => apiFetch<T>(endpoint, 'DELETE'),
        upload: <T>(endpoint: string, formData: FormData) => apiFetch<T>(endpoint, 'POST', formData),
    }
}
