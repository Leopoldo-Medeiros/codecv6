<template>
  <div class="callback-page">
    <div class="callback-card">
      <svg v-if="!error" class="spinner" viewBox="0 0 24 24" fill="none">
        <circle cx="12" cy="12" r="10" stroke="#0284c7" stroke-width="3"
          stroke-dasharray="40 20" stroke-linecap="round"
          style="transform-origin:center;animation:spin .8s linear infinite"/>
      </svg>
      <p v-if="!error">Signing you in…</p>
      <p v-else class="error-msg">{{ error }} <NuxtLink to="/login">Back to sign in</NuxtLink></p>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: false })

const route  = useRoute()
const { setAuth } = useAuth()

onMounted(() => {
  const token = route.query.token as string | undefined
  const raw   = route.query.user  as string | undefined
  const err   = route.query.error as string | undefined

  if (err || !token || !raw) {
    error.value = 'Google sign-in failed. Please try again.'
    return
  }

  try {
    const user = JSON.parse(decodeURIComponent(raw))
    setAuth(token, user)
    const dest = user?.profile?.profession ? '/dashboard' : '/onboarding'
    navigateTo(dest)
  } catch {
    error.value = 'Could not complete sign-in. Please try again.'
  }
})

const error = ref('')
</script>

<style scoped>
@keyframes spin { to { transform: rotate(360deg); } }
.callback-page {
  min-height: 100dvh; display: flex; align-items: center; justify-content: center;
  background: #f1f5f9; font-family: 'Inter', system-ui, sans-serif;
}
.callback-card {
  text-align: center; background: #fff; border-radius: 16px;
  padding: 3rem 4rem;
  box-shadow: 0 10px 40px rgba(0,0,0,0.08);
  display: flex; flex-direction: column; align-items: center; gap: 1rem;
}
.spinner { width: 40px; height: 40px; }
p { font-size: 0.9375rem; color: #64748b; }
.error-msg { color: #ef4444; }
.error-msg a { color: #0284c7; font-weight: 600; text-decoration: none; margin-left: 0.5rem; }
</style>
