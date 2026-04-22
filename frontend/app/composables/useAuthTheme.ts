export function useAuthTheme() {
  const isDark = useState<boolean>('auth-theme-dark', () => {
    if (import.meta.client) {
      const stored = localStorage.getItem('auth-theme')
      if (stored !== null) return stored === 'dark'
      return window.matchMedia?.('(prefers-color-scheme: dark)').matches ?? true
    }
    return true
  })

  function toggle() {
    isDark.value = !isDark.value
    if (import.meta.client) {
      localStorage.setItem('auth-theme', isDark.value ? 'dark' : 'light')
    }
  }

  return { isDark: readonly(isDark), toggle }
}
