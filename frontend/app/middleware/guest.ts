export default defineNuxtRouteMiddleware((to, from) => {
    const auth = useAuth();

    if (auth.isAuthenticated.value) {
        return navigateTo('/dashboard');
    }
});
