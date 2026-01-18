export const useApi = () => {
    const config = useRuntimeConfig()
    const baseURL = config.public.apiBase as string

    const apiFetch = async <T>(endpoint: string, options: RequestInit & { body?: unknown } = {}): Promise<T> => {
        const fetchOptions: RequestInit = {
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                ...options.headers,
            },
            ...options,
        }

        if (options.body && typeof options.body === 'object') {
            fetchOptions.body = JSON.stringify(options.body)
        }

        try {
            const response = await $fetch<T>(endpoint, {
                baseURL: `${baseURL}/api`,
                ...fetchOptions,
            })
            return response
        } catch (error: unknown) {
            console.error('API Error:', error)
            throw error
        }
    }

    return {
        get: <T>(endpoint: string) => apiFetch<T>(endpoint, { method: 'GET' }),
        post: <T>(endpoint: string, body: unknown) => apiFetch<T>(endpoint, { method: 'POST', body }),
        put: <T>(endpoint: string, body: unknown) => apiFetch<T>(endpoint, { method: 'PUT', body }),
        delete: <T>(endpoint: string) => apiFetch<T>(endpoint, { method: 'DELETE' }),
    }
}
