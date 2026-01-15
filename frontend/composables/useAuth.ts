// ====================================
// Types & Interfaces
// ====================================

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
    profile_image?: string;
    // Add other user properties as needed
}

interface LoginResponse {
    user: User;
    access_token: string;
    token_type: string;
}

interface ApiError {
    response?: {
        _data?: {
            message?: string;
        };
    };
    message?: string;
}

type LoginResult =
    | { success: true; user: User }
    | { success: false; error: string; timestamp: string };

// ====================================
// Constants
// ====================================

const STORAGE_KEYS = {
    TOKEN: 'auth_token',
    USER: 'auth_user'
};

const DEFAULT_HEADERS = {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
};

// ====================================
// Storage Helpers
// ====================================

const setInStorage = (key: string, value: string): void => {
    if (process.client) {
        localStorage.setItem(key, value);
    }
};

const getFromStorage = (key: string): string | null => {
    if (process.client) {
        return localStorage.getItem(key);
    }
    return null;
};

const removeFromStorage = (key: string): void => {
    if (process.client) {
        localStorage.removeItem(key);
    }
};

// ====================================
// Auth Helpers
// ====================================

const loadUserFromStorage = (): User | null => {
    if (process.client) {
        const userData = localStorage.getItem(STORAGE_KEYS.USER);
        return userData ? JSON.parse(userData) : null;
    }
    return null;
};

const logAuthState = (message: string, user: User | null): void => {
    if (process.server || process.env.NODE_ENV === 'production') return;
    console.log(`[Auth] ${message}`, { user });
};

// ====================================
// API Client
// ====================================

type CustomHeaders = HeadersInit & {
    'X-XSRF-TOKEN'?: string;
    'X-Requested-With'?: string;
    'Accept'?: string;
    'Content-Type'?: string;
};

const createAuthApiClient = () => {
    return $fetch.create({
        baseURL: 'http://codecv6.lndo.site',
        credentials: 'include',
        headers: { ...DEFAULT_HEADERS },
        async onRequest({ options }) {
            // For non-GET requests, ensure we have a CSRF token
            if (options.method?.toUpperCase() !== 'GET') {
                try {
                    await $fetch('/sanctum/csrf-cookie', {
                        baseURL: 'http://codecv6.lndo.site',
                        credentials: 'include',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });
                    
                    // Get the CSRF token from cookies
                    const token = document.cookie
                        .split('; ')
                        .find(row => row.startsWith('XSRF-TOKEN='))
                        ?.split('=')[1];

                    // Add CSRF token to headers
                    if (token) {
                        // Create a new Headers object
                        const headers = new Headers(options.headers);
                        
                        // Set the required headers
                        headers.set('X-XSRF-TOKEN', decodeURIComponent(token));
                        headers.set('X-Requested-With', 'XMLHttpRequest');
                        headers.set('Accept', 'application/json');
                        
                        // Assign the Headers object back to options
                        options.headers = headers;
                    }
                } catch (error) {
                    console.error('Failed to get CSRF token:', error);
                }
            }
        },
        onResponseError({ response }) {
            if (response.status === 401) {
                const auth = useAuth();
                auth.logout();
            }
        }
    });
};

const apiClient = createAuthApiClient();

// ====================================
// Auth Composable
// ====================================

export const useAuth = () => {
    // State
    const user = useState<User | null>('user', () => {
        const loadedUser = loadUserFromStorage();
        logAuthState('Initial user state loaded', loadedUser);
        return loadedUser;
    });

    // Dependencies
    const router = useRouter();

    // Computed
    const isAuthenticated = computed(() => Boolean(user.value));
    const isAdmin = computed(() => user.value?.role === 'admin');
    const isClient = computed(() => user.value?.role === 'client');

    // Lifecycle
    onMounted(() => {
        if (process.client) {
            const storedUser = loadUserFromStorage();
            logAuthState('Mounted - loading user from storage', storedUser);
            if (storedUser) {
                user.value = storedUser;
            }
        }
    });

    // Methods
    const login = async (email: string, password: string): Promise<LoginResult> => {
        try {
            // Make login request - the apiClient will handle CSRF token automatically
            const response = await apiClient<LoginResponse>('/api/login', {
                method: 'POST',
                body: { email, password },
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            // Store user data in state and localStorage
            if (response.user) {
                user.value = response.user;
                if (process.client) {
                    localStorage.setItem(STORAGE_KEYS.USER, JSON.stringify(response.user));
                    if (response.access_token) {
                        localStorage.setItem(STORAGE_KEYS.TOKEN, response.access_token);
                    }
                }
            }

            // Handle successful login
            return {
                success: true,
                user: response.user
            } as const;
        } catch (error: any) {
            return {
                success: false,
                error: error.response?._data?.message || 'Login failed',
                timestamp: new Date().toISOString()
            };
        }
    };
    const logout = async (): Promise<void> => {
        try {
            await apiClient('/api/logout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            // Clear user data from state and storage
            user.value = null;
            if (process.client) {
                localStorage.removeItem(STORAGE_KEYS.USER);
                localStorage.removeItem(STORAGE_KEYS.TOKEN);
            }
        } catch (error) {
            console.error('Error during logout:', error);
        } finally {
            clearAuthState();
            await router.push('/login');
        }
    };

    const updateAuthState = (userData: User, token: string): void => {
        user.value = userData;
        setInStorage(STORAGE_KEYS.USER, JSON.stringify(userData));
        setInStorage(STORAGE_KEYS.TOKEN, token);
    };

    const clearAuthState = (): void => {
        user.value = null;
        removeFromStorage(STORAGE_KEYS.USER);
        removeFromStorage(STORAGE_KEYS.TOKEN);
    };

    const handleAuthError = (error: ApiError, context: string): LoginResult => {
        const errorMessage = error.response?._data?.message || error.message || `${context}. Please try again.`;
        console.error(`[Auth] ${context}:`, error);
        return {
            success: false,
            error: errorMessage,
            timestamp: new Date().toISOString()
        };
    };

    return {
        // State
        user: readonly(user),

        // Computed
        isAuthenticated,
        isAdmin,
        isClient,

        // Methods
        login,
        logout
    };
};
