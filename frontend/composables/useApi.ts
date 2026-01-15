export const useApi = () => {
    const baseURL = 'http://codecv6.lndo.site' // Your Laravel URL

    const apiFetch = async (endpoint: string, options: any = {}) => {
        try {
            const response = await $fetch(endpoint, {
                baseURL: `${baseURL}/api`,
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    ...options.headers,
                },
                ...options,
            })
            return response
        } catch (error: any) {
            console.error('API Error:', error)
            throw error
        }
    }

    return {
        get: (endpoint: string) => apiFetch(endpoint, { method: 'GET' }),
        post: (endpoint: string, body: any) => apiFetch(endpoint, { method: 'POST', body }),
        put: (endpoint: string, body: any) => apiFetch(endpoint, { method: 'PUT', body }),
        delete: (endpoint: string) => apiFetch(endpoint, { method: 'DELETE' }),
    }
}
