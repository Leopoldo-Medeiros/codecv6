export default defineNuxtRouteMiddleware((to, from) => {
    const { user } = useAuth()

    if (!user.value) {
        return navigateTo('/login')
    }

    if (user.value.needs_onboarding && to.path !== '/onboarding') {
        return navigateTo('/onboarding')
    }
})
